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

if (!is_logged_in()) {
    header('Location: ../login.php');
    exit;
}

// Csak POST kérést fogadunk el
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../profil.php');
    exit;
}

//Beküldött adatok feldolgozása
$userId = (int)($_SESSION['user_id'] ?? 0);
$felhasznalonev = trim((string)($_POST['felhasznalonev'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));

$errors = [];

// Kötelező mezők ellenőrzése
if ($felhasznalonev === '') {
    $errors[] = 'A felhasználónév kötelező.';
}

// Email formátum ellenőrzése
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Az email hibás.';
}

//Validációs hibák kezelése
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: ../profil.php');
    exit;
}

//Egyediség ellenőrzése
$stmtCheck = $pdo->prepare("
    SELECT COUNT(*)
    FROM felhasznalo
    WHERE (felhasznalonev = :felhasznalonev OR email = :email)
      AND felhasznalo_id <> :felhasznalo_id
");
//Végrehajtás
$stmtCheck->execute([
    ':felhasznalonev' => $felhasznalonev,
    ':email' => $email,
    ':felhasznalo_id' => $userId,
]);

//Ha talál ütközést
if ((int)$stmtCheck->fetchColumn() > 0) {
    $_SESSION['form_errors'] = ['A felhasználónév vagy email már foglalt.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../profil.php');
    exit;
}

// Profil adatainak frissítése
try {
    $stmt = $pdo->prepare("
        UPDATE felhasznalo
        SET felhasznalonev = :felhasznalonev,
            email = :email
        WHERE felhasznalo_id = :felhasznalo_id
    ");

    //Végrehajtás
    $stmt->execute([
        ':felhasznalonev' => $felhasznalonev,
        ':email' => $email,
        ':felhasznalo_id' => $userId,
    ]);

    //Naplózás
    log_action(
        $pdo,
        'profil_modositas',
        'Profil módosítva: ' . $felhasznalonev . ', email: ' . $email
    );

    // Sikeres módosítás
    $_SESSION['username'] = $felhasznalonev;
    $_SESSION['success_message'] = 'A profil módosítása sikeres.';
    unset($_SESSION['old_input'], $_SESSION['form_errors']);

    header('Location: ../profil.php');
    exit;
    // Hibakezelés
} catch (PDOException $e) {
    $_SESSION['form_errors'] = ['Mentési hiba történt.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../profil.php');
    exit;
}