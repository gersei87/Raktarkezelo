//Indítás, betöltés
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/logger.php';

// Felhasználó betöltés, ellenőrzés
load_user_auth($pdo);

if (!has_permission('FELHASZNALO_KEZEL')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod leltár létrehozásához.'];
    header('Location: ../index.php');
    exit;
}

//Csak POST kérés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../uj_leltar.php');
    exit;
}

//Beküldött adatok kiolvasása és validálása
$raktar_id = (int)($_POST['raktar_id'] ?? 0);
$megjegyzes = trim((string)($_POST['megjegyzes'] ?? ''));
$felhasznalo_id = (int)($_SESSION['user_id'] ?? 0);

$errors = [];

if ($raktar_id <= 0) {
    $errors[] = 'A raktár kiválasztása kötelező.';
}

//Ha van hiba, vissza az űrlapra
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_leltar.php');
    exit;
}

//Tranzakció indítása
try {
    $pdo->beginTransaction();

    //Leltár létrehozása
    $stmt = $pdo->prepare("
        INSERT INTO leltar (
            raktar_id,
            felhasznalo_id,
            allapot,
            megjegyzes
        ) VALUES (
            :raktar_id,
            :felhasznalo_id,
            'nyitott',
            :megjegyzes
        )
    ");
    //Megjegyzés: ha nincs megjegyzés, akkor NULL érték kerül az adatbázisba
    $stmt->execute([
        ':raktar_id' => $raktar_id,
        ':felhasznalo_id' => $felhasznalo_id,
        ':megjegyzes' => ($megjegyzes !== '') ? $megjegyzes : null,
    ]);

    //Új leltár ID lekérése
    $leltarId = (int)$pdo->lastInsertId();

    // Leltár tételek létrehozása a raktár aktuális készlete alapján
    $stmtStock = $pdo->prepare("
        SELECT raktarkeszlet_id, mennyiseg
        FROM raktarkeszlet
        WHERE raktar_id = :raktar_id
        ORDER BY raktarkeszlet_id ASC
    ");
    $stmtStock->execute([':raktar_id' => $raktar_id]);
    $stocks = $stmtStock->fetchAll();

    // Leltár tételek beszúrása
    $stmtTetel = $pdo->prepare("
        INSERT INTO leltar_tetel (
            leltar_id,
            raktarkeszlet_id,
            vart_mennyiseg,
            tenyleges_mennyiseg,
            elteres
        ) VALUES (
            :leltar_id,
            :raktarkeszlet_id,
            :vart_mennyiseg,
            NULL,
            NULL
        )
    ");

    // Minden készlethez létrehozunk egy leltár tételt
    foreach ($stocks as $s) {
        $stmtTetel->execute([
            ':leltar_id' => $leltarId,
            ':raktarkeszlet_id' => (int)$s['raktarkeszlet_id'],
            ':vart_mennyiseg' => (int)$s['mennyiseg'],
        ]);
    }

    // Naplózás
    log_action(
        $pdo,
        'leltar_letrehozas',
        'Új leltár létrehozva. leltar_id=' . $leltarId . ', raktar_id=' . $raktar_id
    );

    //Véglegesítés
    $pdo->commit();

    //Sikeres művelet üzenet
    $_SESSION['success_message'] = 'A leltár létrehozása sikeres.';
    header('Location: ../leltar_szerkesztes.php?id=' . $leltarId);
    exit;
    // Hibakezelés
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['form_errors'] = ['Mentési hiba történt.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_leltar.php');
    exit;
}