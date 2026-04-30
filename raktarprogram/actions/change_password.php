// Indítás és fájlok betöltése
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/logger.php';

// Felhasználói jogosultság ellenőrzése
load_user_auth($pdo);

//Bejelentkezés ellenörzése
if (!is_logged_in()) {
    header('Location: ../login.php');
    exit;
}

// Kérés tipusa, csak POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../jelszocsere.php');
    exit;
}

//Adatok kiolvasása és validálása
$userId = (int)($_SESSION['user_id'] ?? 0);
$regi_jelszo = (string)($_POST['regi_jelszo'] ?? '');
$uj_jelszo = (string)($_POST['uj_jelszo'] ?? '');
$uj_jelszo_megerosites = (string)($_POST['uj_jelszo_megerosites'] ?? '');

$errors = [];

if ($regi_jelszo === '') {
    $errors[] = 'A régi jelszó kötelező.';
}

if ($uj_jelszo === '') {
    $errors[] = 'Az új jelszó kötelező.';
}

if (mb_strlen($uj_jelszo) < 6) {
    $errors[] = 'Az új jelszó legalább 6 karakter legyen.';
}

if ($uj_jelszo !== $uj_jelszo_megerosites) {
    $errors[] = 'Az új jelszavak nem egyeznek.';
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    header('Location: ../jelszocsere.php');
    exit;
}

//Jelszó lekérése
$stmt = $pdo->prepare("
    SELECT jelszo_hash
    FROM felhasznalo
    WHERE felhasznalo_id = :felhasznalo_id
    LIMIT 1
");
$stmt->execute([':felhasznalo_id' => $userId]);
$currentHash = $stmt->fetchColumn();

if (!$currentHash || !password_verify($regi_jelszo, $currentHash)) {
    $_SESSION['form_errors'] = ['A régi jelszó hibás.'];
    header('Location: ../jelszocsere.php');
    exit;
}

// Jelszó frissítése
try {
    $stmtUpdate = $pdo->prepare("
        UPDATE felhasznalo
        SET jelszo_hash = :jelszo_hash
        WHERE felhasznalo_id = :felhasznalo_id
    ");

    $stmtUpdate->execute([
        ':jelszo_hash' => password_hash($uj_jelszo, PASSWORD_DEFAULT),
        ':felhasznalo_id' => $userId,
    ]);

    // Naplózás
        log_action(
            $pdo,
            'jelszocsere',
            'Jelszó módosítva.'
        );

        //Sikeres művelet üzenet
    $_SESSION['success_message'] = 'A jelszó módosítása sikeres.';
    header('Location: ../jelszocsere.php');
    exit;
    // Hibakezelés
} catch (PDOException $e) {
    $_SESSION['form_errors'] = ['Mentési hiba történt.'];
    header('Location: ../jelszocsere.php');
    exit;
}