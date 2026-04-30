<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/logger.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('RAKTARKESZLET_KEZEL')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod készlet módosításához.'];
    header('Location: ../termekek.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../termekek.php');
    exit;
}

//Beküldött adatok feldolgozása
$raktarkeszlet_id = (int)($_POST['raktarkeszlet_id'] ?? 0);
$termek_id = (int)($_POST['termek_id'] ?? 0);
$raktar_id = (int)($_POST['raktar_id'] ?? 0);
$tarolohely_id = (int)($_POST['tarolohely_id'] ?? 0);
$mennyiseg = (int)($_POST['mennyiseg'] ?? 0);
$allapot = trim((string)($_POST['allapot'] ?? ''));
$beszerzes_datuma = trim((string)($_POST['beszerzes_datuma'] ?? ''));
$megjegyzes = trim((string)($_POST['megjegyzes'] ?? ''));

$errors = [];

//Kötelező mezők és formátum ellenőrzése
if ($raktarkeszlet_id <= 0) $errors[] = 'Érvénytelen készletazonosító.';
if ($termek_id <= 0) $errors[] = 'A termék kiválasztása kötelező.';
if ($raktar_id <= 0) $errors[] = 'A raktár kiválasztása kötelező.';
if ($tarolohely_id <= 0) $errors[] = 'A tárolóhely kiválasztása kötelező.';
if ($mennyiseg <= 0) $errors[] = 'A mennyiség csak 1 vagy annál nagyobb lehet.';

// Engedélyezett állapotok ellenőrzése
$engedelyezettAllapotok = ['uj', 'hasznalt', 'hibas', 'selejt'];
if (!in_array($allapot, $engedelyezettAllapotok, true)) {
    $errors[] = 'Az állapot kiválasztása kötelező.';
}

//Dátum formátum ellenőrzése
if ($beszerzes_datuma !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $beszerzes_datuma)) {
    $errors[] = 'A beszerzés dátuma hibás.';
}

//Validációs hibák kezelése
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: ../keszlet_szerkesztes.php?id=' . $raktarkeszlet_id);
    exit;
}

// Tárolóhely és raktár ellenőrzése
$stmtCheck = $pdo->prepare(
    'SELECT COUNT(*)
     FROM tarolohely
     WHERE tarolohely_id = :tarolohely_id AND raktar_id = :raktar_id'
);
$stmtCheck->execute([
    ':tarolohely_id' => $tarolohely_id,
    ':raktar_id' => $raktar_id,
]);

// Ha a tárolóhely nem tartozik a raktárhoz, hiba
if ((int)$stmtCheck->fetchColumn() === 0) {
    $_SESSION['form_errors'] = ['A kiválasztott tárolóhely nem ehhez a raktárhoz tartozik.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../keszlet_szerkesztes.php?id=' . $raktarkeszlet_id);
    exit;
}

//Készlet adatainak frissítése
try {
    $stmt = $pdo->prepare(
        'UPDATE raktarkeszlet
         SET
            termek_id = :termek_id,
            raktar_id = :raktar_id,
            tarolohely_id = :tarolohely_id,
            mennyiseg = :mennyiseg,
            allapot = :allapot,
            beszerzes_datuma = :beszerzes_datuma,
            megjegyzes = :megjegyzes
         WHERE raktarkeszlet_id = :raktarkeszlet_id'
    );

    //Végrehajtás
    $stmt->execute([
        ':termek_id' => $termek_id,
        ':raktar_id' => $raktar_id,
        ':tarolohely_id' => $tarolohely_id,
        ':mennyiseg' => $mennyiseg,
        ':allapot' => $allapot,
        ':beszerzes_datuma' => ($beszerzes_datuma !== '') ? $beszerzes_datuma : null,
        ':megjegyzes' => ($megjegyzes !== '') ? $megjegyzes : null,
        ':raktarkeszlet_id' => $raktarkeszlet_id,
    ]);

    //Naplózás
    log_action(
        $pdo,
        'keszlet_modositas',
        'Készlet módosítva: raktarkeszlet_id=' . $raktarkeszlet_id . ', termek_id=' . $termek_id . ', mennyiseg=' . $mennyiseg . ', allapot=' . $allapot
    );

    // Sikeres módosítás
    $_SESSION['success_message'] = 'A készlet módosítása sikeres.';
    unset($_SESSION['old_input'], $_SESSION['form_errors']);

    header('Location: ../termek_reszletek.php?id=' . $termek_id);
    exit;
    // Hibakezelés
} catch (PDOException $e) {
    $_SESSION['form_errors'] = ['Mentési hiba történt.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../keszlet_szerkesztes.php?id=' . $raktarkeszlet_id);
    exit;
}