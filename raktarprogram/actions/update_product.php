<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/product_images.php';
require_once __DIR__ . '/../includes/logger.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('TERMEK_KEZEL')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod termék módosításához.'];
    header('Location: ../termekek.php');
    exit;
}

// Csak POST kérést fogadunk el
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../termekek.php');
    exit;
}

//Beküldött adatok feldolgozása
$termek_id = (int)($_POST['termek_id'] ?? 0);
$termeknev = trim((string)($_POST['termeknev'] ?? ''));
$cikkszam = trim((string)($_POST['cikkszam'] ?? ''));
$leiras = trim((string)($_POST['leiras'] ?? ''));
$mertekegyseg = trim((string)($_POST['mertekegyseg'] ?? ''));
$egysegarRaw = trim((string)($_POST['egysegar'] ?? ''));
$kategoria_id = (int)($_POST['kategoria_id'] ?? 0);
$beszallitok_id = (int)($_POST['beszallitok_id'] ?? 0);

$errors = [];

// Termékazonosító ellenőrzése
if ($termek_id <= 0) {
    $errors[] = 'Érvénytelen termékazonosító.';
}

// Kötelező mezők ellenőrzése
if ($termeknev === '') {
    $errors[] = 'A terméknév kötelező.';
}

if ($cikkszam === '') {
    $errors[] = 'A cikkszám kötelező.';
}

if ($mertekegyseg === '') {
    $errors[] = 'A mértékegység kötelező.';
}

if ($egysegarRaw === '' || !is_numeric($egysegarRaw) || (int)$egysegarRaw < 0) {
    $errors[] = 'Az egységár hibás.';
}

if ($kategoria_id <= 0) {
    $errors[] = 'A kategória kiválasztása kötelező.';
}

if ($beszallitok_id <= 0) {
    $errors[] = 'A beszállító kiválasztása kötelező.';
}

//Hibák kezelése
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: ../termek_szerkesztes.php?id=' . $termek_id);
    exit;
}

// Egységár konvertálása
$egysegar = (int)$egysegarRaw;

// Termék létezésének ellenőrzése
$stmtExists = $pdo->prepare(
    'SELECT COUNT(*)
     FROM termekek
     WHERE termek_id = :termek_id'
);
$stmtExists->execute([':termek_id' => $termek_id]);

//Ha nincs ilyen termék
if ((int)$stmtExists->fetchColumn() === 0) {
    $_SESSION['form_errors'] = ['A termék nem található.'];
    header('Location: ../termekek.php');
    exit;
}

// Cikkszám egyediségének ellenőrzése
$stmtCheck = $pdo->prepare(
    'SELECT COUNT(*)
     FROM termekek
     WHERE cikkszam = :cikkszam
       AND termek_id <> :termek_id'
);
$stmtCheck->execute([
    ':cikkszam' => $cikkszam,
    ':termek_id' => $termek_id,
]);

// Ha van már ilyen cikkszámú termék, ami nem ez a termék
if ((int)$stmtCheck->fetchColumn() > 0) {
    $_SESSION['form_errors'] = ['Ez a cikkszám már másik termékhez tartozik.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../termek_szerkesztes.php?id=' . $termek_id);
    exit;
}

// Termék adatainak frissítése
try {
    $stmt = $pdo->prepare(
        'UPDATE termekek
         SET
            termeknev = :termeknev,
            cikkszam = :cikkszam,
            leiras = :leiras,
            mertekegyseg = :mertekegyseg,
            egysegar = :egysegar,
            kategoria_id = :kategoria_id,
            beszallitok_id = :beszallitok_id
         WHERE termek_id = :termek_id'
    );

    //Végrehajtás
    $stmt->execute([
        ':termeknev' => $termeknev,
        ':cikkszam' => $cikkszam,
        ':leiras' => ($leiras !== '') ? $leiras : null,
        ':mertekegyseg' => $mertekegyseg,
        ':egysegar' => $egysegar,
        ':kategoria_id' => $kategoria_id,
        ':beszallitok_id' => $beszallitok_id,
        ':termek_id' => $termek_id,
    ]);

    // Kép feltöltése, ha van
    if (isset($_FILES['product_image'])) {
        save_uploaded_product_image($_FILES['product_image'], $termek_id);
    }

    //Naplózás
    log_action(
        $pdo,
        'termek_modositas',
        'Termék módosítva: ID=' . $termek_id . ', név=' . $termeknev . ', cikkszám=' . $cikkszam
    );

    // Sikeres módosítás
    $_SESSION['success_message'] = 'A termék módosítása sikeres.';
    unset($_SESSION['old_input'], $_SESSION['form_errors']);

    header('Location: ../termek_reszletek.php?id=' . $termek_id);
    exit;
    // Hibakezelés
} catch (RuntimeException $e) {
    $_SESSION['form_errors'] = [$e->getMessage()];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../termek_szerkesztes.php?id=' . $termek_id);
    exit;
} catch (PDOException $e) {
    $_SESSION['form_errors'] = ['Mentési hiba történt.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../termek_szerkesztes.php?id=' . $termek_id);
    exit;
}