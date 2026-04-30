<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//Betöltések
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/logger.php';
require_once __DIR__ . '/../includes/product_images.php';

//Felhasználó betöltés, ellenőrzés
load_user_auth($pdo);

if (!has_permission('TORLES')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod termék törléséhez.'];
    header('Location: ../termekek.php');
    exit;
}

//Csak POST kérés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../termekek.php');
    exit;
}

//CSRF token ellenőrzése
if (!function_exists('validate_csrf_token') || !validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['form_errors'] = ['Érvénytelen kérés.'];
    header('Location: ../termekek.php');
    exit;
}

//Törlendő termék ID-jének kiolvasása és ellenőrzése
$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['form_errors'] = ['Érvénytelen termékazonosító.'];
    header('Location: ../termekek.php');
    exit;
}

//Törlendő termék adatainak lekérése
$stmtLoad = $pdo->prepare(
    'SELECT termek_id, termeknev, cikkszam
     FROM termekek
     WHERE termek_id = :id
     LIMIT 1'
);
$stmtLoad->execute([':id' => $id]);
$termek = $stmtLoad->fetch();

//Nem létező termék kezelése
if (!$termek) {
    $_SESSION['form_errors'] = ['A termék nem található.'];
    header('Location: ../termekek.php');
    exit;
}

//Tranzakció indítása
try {
    $pdo->beginTransaction();

    //Termék törlése az adatbázisból
    $stmt = $pdo->prepare(
        'DELETE FROM termekek
         WHERE termek_id = :id'
    );
    $stmt->execute([':id' => $id]);

    //Kép törlése, ha van
    delete_product_image_if_exists($id);

    //Naplózás
    log_action(
        $pdo,
        'termek_torles',
        'Termék törölve: ID=' . $id .
        ', név=' . ($termek['termeknev'] ?? 'ismeretlen') .
        ', cikkszám=' . ($termek['cikkszam'] ?? 'ismeretlen')
    );

    $pdo->commit();

    //Sikeres művelet üzenet
    $_SESSION['success_message'] = 'A termék törlése sikeres.';
    header('Location: ../termekek.php');
    exit;
    // Hibakezelés
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['form_errors'] = ['A termék nem törölhető. Lehet, hogy még tartozik hozzá készletrekord.'];
    header('Location: ../termekek.php');
    exit;
}