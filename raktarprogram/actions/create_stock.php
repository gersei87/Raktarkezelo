//Indítás, betöltés
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Felhasználó betöltés, ellenőrzés
load_user_auth($pdo);

if (!has_permission('RAKTARKESZLET_KEZEL')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod készlet rögzítéséhez.'];
    header('Location: ../termekek.php');
    exit;
}

//Csak POST kérés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../termekek.php');
    exit;
}

//Beküldött adatok kiolvasása és validálása
$termek_id = (int)($_POST['termek_id'] ?? 0);
$raktar_id = (int)($_POST['raktar_id'] ?? 0);
$tarolohely_id = (int)($_POST['tarolohely_id'] ?? 0);
$mennyiseg = (int)($_POST['mennyiseg'] ?? 0);
$allapot = trim((string)($_POST['allapot'] ?? ''));
$beszerzes_datuma = trim((string)($_POST['beszerzes_datuma'] ?? ''));
$megjegyzes = trim((string)($_POST['megjegyzes'] ?? ''));

$errors = [];

if ($termek_id <= 0) $errors[] = 'A termék kiválasztása kötelező.';
if ($raktar_id <= 0) $errors[] = 'A raktár kiválasztása kötelező.';
if ($tarolohely_id <= 0) $errors[] = 'A tárolóhely kiválasztása kötelező.';
if ($mennyiseg <= 0) $errors[] = 'A mennyiség csak 1 vagy annál nagyobb lehet.';

// Engedélyezett állapotok ellenőrzése
$engedelyezettAllapotok = ['uj', 'hasznalt', 'hibas', 'selejt'];
if (!in_array($allapot, $engedelyezettAllapotok, true)) {
    $errors[] = 'Az állapot kiválasztása kötelező.';
}

//Dátum formátum ellenőrzése, ha meg van adva
if ($beszerzes_datuma !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $beszerzes_datuma)) {
    $errors[] = 'A beszerzés dátuma hibás.';
}

//Ha van hiba, vissza az űrlapra
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_keszlet.php' . ($termek_id > 0 ? '?termek_id=' . $termek_id : ''));
    exit;
}

//Tárolóhely ellenőrzése, hogy a megadott raktárhoz tartozik-e
$stmtCheck = $pdo->prepare(
    'SELECT COUNT(*)
     FROM tarolohely
     WHERE tarolohely_id = :tarolohely_id AND raktar_id = :raktar_id'
);
$stmtCheck->execute([
    ':tarolohely_id' => $tarolohely_id,
    ':raktar_id' => $raktar_id,
]);

if ((int)$stmtCheck->fetchColumn() === 0) {
    $_SESSION['form_errors'] = ['A kiválasztott tárolóhely nem ehhez a raktárhoz tartozik.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_keszlet.php' . ($termek_id > 0 ? '?termek_id=' . $termek_id : ''));
    exit;
}

//Új készlet létrehozása
try {
    $stmt = $pdo->prepare(
        'INSERT INTO raktarkeszlet (
            termek_id,
            raktar_id,
            tarolohely_id,
            mennyiseg,
            allapot,
            beszerzes_datuma,
            megjegyzes,
            felhasznalo_id
        ) VALUES (
            :termek_id,
            :raktar_id,
            :tarolohely_id,
            :mennyiseg,
            :allapot,
            :beszerzes_datuma,
            :megjegyzes,
            :felhasznalo_id
        )'
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
        ':felhasznalo_id' => (int)($_SESSION['user_id'] ?? 0),
    ]);

    //Sikeres mentés után
    $_SESSION['success_message'] = 'A készlet rögzítése sikeres.';
    unset($_SESSION['old_input'], $_SESSION['form_errors']);

    header('Location: ../termek_reszletek.php?id=' . $termek_id);
    exit;
    // Hibakezelés
} catch (PDOException $e) {
    $_SESSION['form_errors'] = ['Mentési hiba történt.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_keszlet.php' . ($termek_id > 0 ? '?termek_id=' . $termek_id : ''));
    exit;
}