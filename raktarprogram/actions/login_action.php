<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Ezen az oldalon még nincs szükség hielesítésre
define('SKIP_AUTH_CHECK', true);

//Betöltések
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/logger.php';

//Csak POST kérés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

//CSRF token ellenőrzése
if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
    header('Location: ../login.php?error=csrf');
    exit;
}

//Adatok kiolvasása és validálása
$login = trim((string)($_POST['login'] ?? ''));
$password = (string)($_POST['password'] ?? '');

//Üres mező ellenőrzése
if ($login === '' || $password === '') {
    header('Location: ../login.php?error=invalid');
    exit;
}

//Felhasználó lekérése az adatbázisból
$stmt = $pdo->prepare("
    SELECT
        felhasznalo_id,
        felhasznalonev,
        email,
        jelszo_hash,
        aktiv
    FROM felhasznalo
    WHERE (felhasznalonev = :login_name OR email = :login_email)
    LIMIT 1
");
//Végrehajtás
$stmt->execute([
    ':login_name' => $login,
    ':login_email' => $login,
]);

//Találat betöltése
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//Bejelentkezési feltételek ellenőrzése
if (
    !$user ||
    (int)$user['aktiv'] !== 1 ||
    !password_verify($password, $user['jelszo_hash'])
) {
    header('Location: ../login.php?error=invalid');
    exit;
}

// Sikeres bejelentkezés, session létrehozása és mentése
session_regenerate_id(true);

$_SESSION['user_id'] = (int)$user['felhasznalo_id'];
$_SESSION['username'] = $user['felhasznalonev'];
$_SESSION['email'] = $user['email'];

//Auth adatok betöltése a sessionbe
load_user_auth($pdo);  //auth.php 68. sor

//Naplózás    //logger.php 8. sor
log_action(        
    $pdo,
    'felhasznalo_bejelentkezett',
    'Sikeres bejelentkezés: ' . $user['felhasznalonev'],
    (int)$user['felhasznalo_id']
);

//Sikeres átirányítás
header('Location: ../index.php');
exit;