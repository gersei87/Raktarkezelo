<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/product_images.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('TERMEK_KEZEL')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod termék módosításához.'];
    header('Location: termekek.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

//Termék ID beolvasása URL-ből
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: termekek.php');
    exit;
}

//Termék adatainak lekérése
$stmt = $pdo->prepare(
    'SELECT termek_id, termeknev, cikkszam, leiras, mertekegyseg, egysegar, kategoria_id, beszallitok_id
     FROM termekek
     WHERE termek_id = :id
     LIMIT 1'
);
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

//Ha nem található
if (!$product) {
    header('Location: termekek.php');
    exit;
}

//Kategóriák és beszállítók lekérése
$kategoriak = $pdo->query('SELECT kategoria_id, kategoria FROM termek_kategoria ORDER BY kategoria ASC')->fetchAll();
$beszallitok = $pdo->query('SELECT beszallitok_id, beszallito FROM beszallitok ORDER BY beszallito ASC')->fetchAll();

//Session üzenetek és régi input adatok kiolvasása
$errors = $_SESSION['form_errors'] ?? [];
$success = $_SESSION['success_message'] ?? null;
$old = $_SESSION['old_input'] ?? [];

//Törlése
unset($_SESSION['form_errors'], $_SESSION['success_message'], $_SESSION['old_input']);

//Megjelenítendő adatok előkészítése
$data = [
    'termeknev' => $old['termeknev'] ?? $product['termeknev'],
    'cikkszam' => $old['cikkszam'] ?? $product['cikkszam'],
    'leiras' => $old['leiras'] ?? $product['leiras'],
    'mertekegyseg' => $old['mertekegyseg'] ?? $product['mertekegyseg'],
    'egysegar' => $old['egysegar'] ?? $product['egysegar'],
    'kategoria_id' => $old['kategoria_id'] ?? $product['kategoria_id'],
    'beszallitok_id' => $old['beszallitok_id'] ?? $product['beszallitok_id'],
];
?>

<div class="form-page">
    <div class="form-card">

        <form class="product-form" method="POST" action="actions/update_product.php" enctype="multipart/form-data">
            <input type="hidden" name="termek_id" value="<?= (int)$product['termek_id'] ?>">

            <div class="form-left">
                <label for="imageInput" class="file-btn">Kép feltöltése</label>
                <input type="file" id="imageInput" name="product_image" accept="image/*">
                <img id="preview" alt="">
            </div>

            <div class="form-right">

                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <?php foreach ($errors as $error): ?>
                            <p class="error-msg"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-box">
                        <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php endif; ?>

                <input type="text" name="termeknev" placeholder="Terméknév" required value="<?= htmlspecialchars((string)$data['termeknev'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" name="cikkszam" placeholder="Cikkszám" required value="<?= htmlspecialchars((string)$data['cikkszam'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" name="mertekegyseg" placeholder="Mértékegység" required value="<?= htmlspecialchars((string)$data['mertekegyseg'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="number" name="egysegar" min="0" step="1" placeholder="Egységár" required value="<?= htmlspecialchars((string)$data['egysegar'], ENT_QUOTES, 'UTF-8') ?>">

                <select name="kategoria_id" required>
                    <option value="">Kategória</option>
                    <?php foreach ($kategoriak as $kategoria): ?>
                        <option value="<?= (int)$kategoria['kategoria_id'] ?>" <?= ((string)$data['kategoria_id'] === (string)$kategoria['kategoria_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kategoria['kategoria'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="beszallitok_id" required>
                    <option value="">Beszállító</option>
                    <?php foreach ($beszallitok as $beszallito): ?>
                        <option value="<?= (int)$beszallito['beszallitok_id'] ?>" <?= ((string)$data['beszallitok_id'] === (string)$beszallito['beszallitok_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($beszallito['beszallito'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <textarea name="leiras" placeholder="Leírás"><?= htmlspecialchars((string)$data['leiras'], ENT_QUOTES, 'UTF-8') ?></textarea>

                <div class="btn-row">
                    <a href="termekek.php" class="btn cancel">Mégse</a>
                    <button type="submit" class="btn">Mentés</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>