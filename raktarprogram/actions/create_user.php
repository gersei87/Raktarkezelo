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
    $_SESSION['form_errors'] = ['Nincs jogosultságod felhasználó létrehozásához.'];
    header('Location: ../index.php');
    exit;
}

//Csak POST kérés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../uj_felhasznalo.php');
    exit;
}

//Beküldött adatok kiolvasása és validálása
$felhasznalonev = trim((string)($_POST['felhasznalonev'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$jelszo = (string)($_POST['jelszo'] ?? '');
$jelszo_megerosites = (string)($_POST['jelszo_megerosites'] ?? '');
$szerepkor_id = (int)($_POST['szerepkor_id'] ?? 0);
$aktiv = (int)($_POST['aktiv'] ?? 1);

$errors = [];

if ($felhasznalonev === '') $errors[] = 'A felhasználónév kötelező.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Az email hibás.';
if ($jelszo === '') $errors[] = 'A jelszó kötelező.';
if (mb_strlen($jelszo) < 6) $errors[] = 'A jelszó legalább 6 karakter legyen.';
if ($jelszo !== $jelszo_megerosites) $errors[] = 'A két jelszó nem egyezik.';
if ($szerepkor_id <= 0) $errors[] = 'A szerepkör kiválasztása kötelező.';

//Ha van hiba, vissza az űrlapra
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_felhasznalo.php');
    exit;
}

//Felhasználónév és email egyediségének ellenőrzése
$stmtCheck = $pdo->prepare("
    SELECT COUNT(*)
    FROM felhasznalo
    WHERE felhasznalonev = :felhasznalonev OR email = :email
");
$stmtCheck->execute([
    ':felhasznalonev' => $felhasznalonev,
    ':email' => $email,
]);

//Ha már létezik ilyen felhasználónév vagy email, vissza az űrlapra
if ((int)$stmtCheck->fetchColumn() > 0) {
    $_SESSION['form_errors'] = ['A felhasználónév vagy email már létezik.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_felhasznalo.php');
    exit;
}

//Új felhasználó létrehozása
try {
    $pdo->beginTransaction();

    //Új felhasználó beszúrása
    $stmt = $pdo->prepare("
        INSERT INTO felhasznalo (
            felhasznalonev,
            email,
            jelszo_hash,
            aktiv
        ) VALUES (
            :felhasznalonev,
            :email,
            :jelszo_hash,
            :aktiv
        )
    ");

    //Végrehajtás
    $stmt->execute([
        ':felhasznalonev' => $felhasznalonev,
        ':email' => $email,
        ':jelszo_hash' => password_hash($jelszo, PASSWORD_DEFAULT),
        ':aktiv' => $aktiv,
    ]);

    //Új felhasználó ID lekérése
    $ujFelhasznaloId = (int)$pdo->lastInsertId();

    // Szerepkör hozzárendelése
    $stmtRole = $pdo->prepare("
        INSERT INTO felhasznalo_szerepkor (
            felhasznalo_id,
            szerepkor_id
        ) VALUES (
            :felhasznalo_id,
            :szerepkor_id
        )
    ");

    //Végrehajtás
    $stmtRole->execute([
        ':felhasznalo_id' => $ujFelhasznaloId,
        ':szerepkor_id' => $szerepkor_id,
    ]);

    // Művelet naplózása
    log_action(
        $pdo,
        'felhasznalo_hozzaadas',
        'Új felhasználó létrehozva: ' . $felhasznalonev . ', email: ' . $email
    );

    // Tranzakció véglegesítése
    $pdo->commit();

    //Sikeres művelet üzenet
    $_SESSION['success_message'] = 'A felhasználó létrehozása sikeres.';
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
    header('Location: ../uj_felhasznalo.php');
    exit;
}