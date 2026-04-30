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

if (!has_permission('TORLES')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod készlet törléséhez.'];
    header('Location: ../termekek.php');
    exit;
}

//Csak POST kérés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../termekek.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$termek_id = (int)($_POST['termek_id'] ?? 0);

//Készlet ID ellenőrzése
if ($id <= 0) {
    $_SESSION['form_errors'] = ['Érvénytelen készletazonosító.'];
    header('Location: ../termekek.php');
    exit;
}

// Készletrekord betöltése
$stmtLoad = $pdo->prepare(
    'SELECT
        raktarkeszlet_id,
        termek_id,
        mennyiseg,
        allapot
     FROM raktarkeszlet
     WHERE raktarkeszlet_id = :id
     LIMIT 1'
);
$stmtLoad->execute([':id' => $id]);
$stock = $stmtLoad->fetch();

// Nem létező készletrekord kezelése
if (!$stock) {
    $_SESSION['form_errors'] = ['A készletrekord nem található.'];
    header('Location: ../termekek.php');
    exit;
}

//Termék ID pótlása, ha nincs megadva
if ($termek_id <= 0) {
    $termek_id = (int)$stock['termek_id'];
}

//Törlés az adatbázisból
try {
    $stmt = $pdo->prepare(
        'DELETE FROM raktarkeszlet
         WHERE raktarkeszlet_id = :id'
    );
    $stmt->execute([':id' => $id]);

    //Naplózás
    log_action(
        $pdo,
        'keszlet_torles',
        'Készlet törölve: raktarkeszlet_id=' . $id .
        ', termek_id=' . ($stock['termek_id'] ?? 'ismeretlen') .
        ', mennyiseg=' . ($stock['mennyiseg'] ?? 'ismeretlen') .
        ', allapot=' . ($stock['allapot'] ?? 'ismeretlen')
    );

    //Sikeres művelet üzenet
    $_SESSION['success_message'] = 'A készlet törlése sikeres.';

    //Visszairányítás
    if ($termek_id > 0) {
        header('Location: ../termek_reszletek.php?id=' . $termek_id);
    } else {
        header('Location: ../termekek.php');
    }
    exit;
    // Hibakezelés
} catch (PDOException $e) {
    $_SESSION['form_errors'] = ['Törlési hiba történt.'];

    if ($termek_id > 0) {
        header('Location: ../termek_reszletek.php?id=' . $termek_id);
    } else {
        header('Location: ../termekek.php');
    }
    exit;
}