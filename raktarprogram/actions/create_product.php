//Indítás, betöltés
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/product_images.php';
require_once __DIR__ . '/../includes/logger.php';
// Felhasználó betöltés, ellenőrzés
load_user_auth($pdo);

if (!has_permission('TERMEK_KEZEL')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod termék létrehozásához.'];
    header('Location: ../termekek.php');
    exit;
}
//Csak POST kérés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../uj_termek.php');
    exit;
}

//Ürlap adatok beolvasása
$termeknev = trim((string)($_POST['termeknev'] ?? ''));
$cikkszam = trim((string)($_POST['cikkszam'] ?? ''));
$leiras = trim((string)($_POST['leiras'] ?? ''));
$mertekegyseg = trim((string)($_POST['mertekegyseg'] ?? ''));
$egysegarRaw = trim((string)($_POST['egysegar'] ?? ''));
$kategoria_id = (int)($_POST['kategoria_id'] ?? 0);
$beszallitok_id = (int)($_POST['beszallitok_id'] ?? 0);

$uj_beszallito = trim((string)($_POST['uj_beszallito'] ?? ''));
$uj_beszallito_email = trim((string)($_POST['uj_beszallito_email'] ?? ''));

//Validáció, kötelező mezők ellenőrzése
$errors = [];

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

/* beszállító ellenőrzés:
   - vagy meglévő beszállító
   - vagy új beszállító név */
if ($beszallitok_id <= 0 && $uj_beszallito === '') {
    $errors[] = 'Válassz meglévő beszállítót vagy adj meg újat.';
}

if ($uj_beszallito_email !== '' && !filter_var($uj_beszallito_email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Az új beszállító email címe hibás.';
}

//Ha van hiba, vissza az űrlapra
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_termek.php');
    exit;
}

//Egységár konvertálása
$egysegar = (int)$egysegarRaw;

//Új termék létrehozása
$stmtCheck = $pdo->prepare(
    'SELECT COUNT(*)
     FROM termekek
     WHERE cikkszam = :cikkszam'
);
$stmtCheck->execute([':cikkszam' => $cikkszam]);

//Cikkszám ellenörzése, nem lehet duplikált
if ((int)$stmtCheck->fetchColumn() > 0) {
    $_SESSION['form_errors'] = ['Ez a cikkszám már létezik.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_termek.php');
    exit;
}

try {
    //Tranzakció indítása
    $pdo->beginTransaction();

    /* ha új beszállító lett megadva, létrehozzuk */
    if ($uj_beszallito !== '') {
        $stmtExistingSupplier = $pdo->prepare(
            'SELECT beszallitok_id
             FROM beszallitok
             WHERE beszallito = :beszallito
             LIMIT 1'
        );
        $stmtExistingSupplier->execute([
            ':beszallito' => $uj_beszallito,
        ]);

        $existingSupplierId = $stmtExistingSupplier->fetchColumn();

        if ($existingSupplierId) {
            $beszallitok_id = (int)$existingSupplierId;
        } else {
            $stmtNewSupplier = $pdo->prepare(
                'INSERT INTO beszallitok (beszallito, email)
                 VALUES (:beszallito, :email)'
            );
            $stmtNewSupplier->execute([
                ':beszallito' => $uj_beszallito,
                ':email' => ($uj_beszallito_email !== '') ? $uj_beszallito_email : null,
            ]);

            $beszallitok_id = (int)$pdo->lastInsertId();

            // Naplózás új beszállító létrehozásáról
            log_action(
                $pdo,
                'beszallito_hozzaadas',
                'Új beszállító létrehozva: ' . $uj_beszallito
            );
        }
    }

    // Beszállító ID ellenőrzése, ha új beszállító lett megadva, de valamiért nem jött létre
    if ($beszallitok_id <= 0) {
        throw new RuntimeException('A beszállító mentése sikertelen.');
    }

    // Termék létrehozása
    $stmt = $pdo->prepare(
        'INSERT INTO termekek (
            termeknev,
            cikkszam,
            leiras,
            mertekegyseg,
            egysegar,
            kategoria_id,
            beszallitok_id
        ) VALUES (
            :termeknev,
            :cikkszam,
            :leiras,
            :mertekegyseg,
            :egysegar,
            :kategoria_id,
            :beszallitok_id
        )'
    );

    //Végrehajtás, új termék ID lekérése
    $stmt->execute([
        ':termeknev' => $termeknev,
        ':cikkszam' => $cikkszam,
        ':leiras' => ($leiras !== '') ? $leiras : null,
        ':mertekegyseg' => $mertekegyseg,
        ':egysegar' => $egysegar,
        ':kategoria_id' => $kategoria_id,
        ':beszallitok_id' => $beszallitok_id,
    ]);

    $newId = (int)$pdo->lastInsertId();

    // Termék kép mentése, ha feltöltve van
    if (isset($_FILES['product_image'])) {
        save_uploaded_product_image($_FILES['product_image'], $newId);
    }

    // Naplózás termék létrehozásáról
    log_action(
        $pdo,
        'termek_letrehozas',
        'Új termék létrehozva: ' . $termeknev . ' (' . $cikkszam . '), beszállító ID: ' . $beszallitok_id
    );

    $pdo->commit();

    //Sikeres művelet üzenet
    $_SESSION['success_message'] = 'A termék létrehozása sikeres.';
    unset($_SESSION['old_input'], $_SESSION['form_errors']);

    header('Location: ../termekek.php');
    exit;
    // Hibakezelés
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['form_errors'] = [$e->getMessage() ?: 'Mentési hiba történt.'];
    $_SESSION['old_input'] = $_POST;
    header('Location: ../uj_termek.php');
    exit;
}