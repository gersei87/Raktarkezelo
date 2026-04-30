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

if (!has_permission('FELHASZNALO_KEZEL')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod felhasználó módosításához.'];
    header('Location: ../index.php');
    exit;
}

// Csak POST kérést fogadunk el
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../felhasznalok.php');
    exit;
}

//Beküldött adatok feldolgozása
$felhasznalo_id = (int)($_POST['felhasznalo_id'] ?? 0);
$felhasznalonev = trim((string)($_POST['felhasznalonev'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$szerepkor_id = (int)($_POST['szerepkor_id'] ?? 0);
$aktiv = (int)($_POST['aktiv'] ?? 1);
$uj_jelszo = (string)($_POST['uj_jelszo'] ?? '');

$errors = [];

//Alapellenőrzések
if ($felhasznalo_id <= 0) $errors[] = 'Érvénytelen felhasználóazonosító.';
if ($felhasznalonev === '') $errors[] = 'A felhasználónév kötelező.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Az email hibás.';
if ($szerepkor_id <= 0) $errors[] = 'A szerepkör kiválasztása kötelező.';
if ($uj_jelszo !== '' && mb_strlen($uj_jelszo) < 6) $errors[] = 'Az új jelszó legalább 6 karakter legyen.';

//Validációs hibák kezelése
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: ../felhasznalo_szerkesztes.php?id=' . $felhasznalo_id);
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
    ':felhasznalo_id' => $felhasznalo_id,
]);
//Ha már létezik
if ((int)$stmtCheck->fetchColumn() > 0) {
    $_SESSION['form_errors'] = ['A felhasználónév vagy email már másik felhasználóhoz tartozik.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../felhasznalo_szerkesztes.php?id=' . $felhasznalo_id);
    exit;
}

try {
    $pdo->beginTransaction();

    //Új jelszó
    if ($uj_jelszo !== '') {
        $stmt = $pdo->prepare("
            UPDATE felhasznalo
            SET felhasznalonev = :felhasznalonev,
                email = :email,
                aktiv = :aktiv,
                jelszo_hash = :jelszo_hash
            WHERE felhasznalo_id = :felhasznalo_id
        ");

        $stmt->execute([
            ':felhasznalonev' => $felhasznalonev,
            ':email' => $email,
            ':aktiv' => $aktiv,
            ':jelszo_hash' => password_hash($uj_jelszo, PASSWORD_DEFAULT),
            ':felhasznalo_id' => $felhasznalo_id,
        ]);
        //Ha nincs új jelszó
    } else {
        $stmt = $pdo->prepare("
            UPDATE felhasznalo
            SET felhasznalonev = :felhasznalonev,
                email = :email,
                aktiv = :aktiv
            WHERE felhasznalo_id = :felhasznalo_id
        ");

        $stmt->execute([
            ':felhasznalonev' => $felhasznalonev,
            ':email' => $email,
            ':aktiv' => $aktiv,
            ':felhasznalo_id' => $felhasznalo_id,
        ]);
    }

    // Szerepkör törlése 
    $stmtDeleteRole = $pdo->prepare("
        DELETE FROM felhasznalo_szerepkor
        WHERE felhasznalo_id = :felhasznalo_id
    ");
    $stmtDeleteRole->execute([
        ':felhasznalo_id' => $felhasznalo_id,
    ]);

    // Új szerepkör hozzárendelése
    $stmtInsertRole = $pdo->prepare("
        INSERT INTO felhasznalo_szerepkor (
            felhasznalo_id,
            szerepkor_id
        ) VALUES (
            :felhasznalo_id,
            :szerepkor_id
        )
    ");
    $stmtInsertRole->execute([
        ':felhasznalo_id' => $felhasznalo_id,
        ':szerepkor_id' => $szerepkor_id,
    ]);

    $pdo->commit();

    //Naplózás
    log_action(
        $pdo,
        'felhasznalo_modositas',
        'Felhasználó módosítva: ID=' . $felhasznalo_id . ', név=' . $felhasznalonev . ', email=' . $email . ', szerepkor_id=' . $szerepkor_id . ', aktiv=' . $aktiv
    );

    // Sikeres módosítás
    $_SESSION['success_message'] = 'A felhasználó módosítása sikeres.';
    unset($_SESSION['old_input'], $_SESSION['form_errors']);

    header('Location: ../felhasznalok.php');
    exit;
    // Hibakezelés
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['form_errors'] = ['Mentési hiba történt.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../felhasznalo_szerkesztes.php?id=' . $felhasznalo_id);
    exit;
}