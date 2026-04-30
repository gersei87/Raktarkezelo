<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/logger.php';

//Felhasználó betöltés, ellenőrzés
load_user_auth($pdo);

if (!has_permission('FELHASZNALO_KEZEL')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod leltár módosításához.'];
    header('Location: ../index.php');
    exit;
}

//Csak POST kérés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../leltarak.php');
    exit;
}

//Beküldött adatok kiolvasása
$leltar_id = (int)($_POST['leltar_id'] ?? 0);
$tenyleges = $_POST['tenyleges'] ?? [];
$muvelet = (string)($_POST['muvelet'] ?? 'mentes');

//Leltár ID ellenőrzése
if ($leltar_id <= 0) {
    $_SESSION['form_errors'] = ['Érvénytelen leltárazonosító.'];
    header('Location: ../leltarak.php');
    exit;
}

//Leltár állapotának lekérése
$stmtL = $pdo->prepare("
    SELECT allapot
    FROM leltar
    WHERE leltar_id = :id
    LIMIT 1
");
$stmtL->execute([':id' => $leltar_id]);
$leltar = $stmtL->fetch();

//Leltár létezésének és állapotának ellenőrzése
if (!$leltar) {
    $_SESSION['form_errors'] = ['A leltár nem található.'];
    header('Location: ../leltarak.php');
    exit;
}

if ($leltar['allapot'] !== 'nyitott') {
    $_SESSION['form_errors'] = ['A leltár már le van zárva.'];
    header('Location: ../leltar_szerkesztes.php?id=' . $leltar_id);
    exit;
}

//Tranzakció indítása
try {
    $pdo->beginTransaction();

    //Leltár tételek lekérése
    $stmtGet = $pdo->prepare("
        SELECT leltar_tetel_id, vart_mennyiseg
        FROM leltar_tetel
        WHERE leltar_id = :leltar_id
    ");
    $stmtGet->execute([':leltar_id' => $leltar_id]);
    $tetelSorok = $stmtGet->fetchAll();

    //Leltár tételek frissítése
    $stmtUpdate = $pdo->prepare("
        UPDATE leltar_tetel
        SET tenyleges_mennyiseg = :tenyleges_mennyiseg,
            elteres = :elteres
        WHERE leltar_tetel_id = :leltar_tetel_id
    ");

    //Tényleges mennyiségek feldolgozása
    foreach ($tetelSorok as $sor) {
        $tetelId = (int)$sor['leltar_tetel_id'];
        $vart = (int)$sor['vart_mennyiseg'];
        $teny = isset($tenyleges[$tetelId]) && $tenyleges[$tetelId] !== ''
            ? (int)$tenyleges[$tetelId]
            : null;

        $elteres = ($teny !== null) ? ($teny - $vart) : null;

        $stmtUpdate->execute([
            ':tenyleges_mennyiseg' => $teny,
            ':elteres' => $elteres,
            ':leltar_tetel_id' => $tetelId,
        ]);
    }

    if ($muvelet === 'lezaras') {
        //Leltár lezárása
        $stmtClose = $pdo->prepare("
            UPDATE leltar
            SET allapot = 'lezart',
                lezarva = NOW()
            WHERE leltar_id = :leltar_id
        ");
        $stmtClose->execute([':leltar_id' => $leltar_id]);

        //Naplózás
        log_action(
            $pdo,
            'leltar_lezaras',
            'Leltár lezárva. leltar_id=' . $leltar_id
        );

        //Sikeres művelet üzenet
        $_SESSION['success_message'] = 'A leltár lezárása sikeres.';
        //Ha nem lezárás, akkor sima mentés
    } else {
        log_action(
            $pdo,
            'leltar_mentes',
            'Leltár mentve. leltar_id=' . $leltar_id
        );

        $_SESSION['success_message'] = 'A leltár mentése sikeres.';
    }

    $pdo->commit();

    //Visszairányítás
    header('Location: ../leltar_szerkesztes.php?id=' . $leltar_id);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    //Hibakezelés
    $_SESSION['form_errors'] = ['Mentési hiba történt.'];
    header('Location: ../leltar_szerkesztes.php?id=' . $leltar_id);
    exit;
}