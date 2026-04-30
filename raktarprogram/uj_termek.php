<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/product_images.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('TERMEK_KEZEL')) {
    header('Location: termekek.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

// Kategóriák lekérése
$kategoriak = $pdo->query(
    "SELECT kategoria_id, kategoria
     FROM termek_kategoria
     ORDER BY kategoria ASC"
)->fetchAll();

// Beszállítók lekérése
$beszallitok = $pdo->query(
    "SELECT beszallitok_id, beszallito
     FROM beszallitok
     ORDER BY beszallito ASC"
)->fetchAll();

//Session üzenetek és régi input adatok kiolvasása
$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];

//Törlése
unset($_SESSION['form_errors'], $_SESSION['old_input']);
?>

<div class="form-page">
    <div class="form-card">
        <form class="product-form" action="actions/create_product.php" method="POST" enctype="multipart/form-data">

            <div class="form-left">
                <label for="imageInput" class="file-btn">Kép kiválasztása</label>
                <input type="file" id="imageInput" name="product_image" accept="image/*">
                <img id="preview" alt="">
            </div>

            <div class="form-right">

                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <?php foreach ($errors as $e): ?>
                            <p class="error-msg"><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <input
                    type="text"
                    name="termeknev"
                    placeholder="Terméknév"
                    required
                    value="<?= htmlspecialchars($old['termeknev'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <input
                    type="text"
                    name="cikkszam"
                    placeholder="Cikkszám"
                    required
                    value="<?= htmlspecialchars($old['cikkszam'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <input
                    type="text"
                    name="mertekegyseg"
                    placeholder="Mértékegység"
                    required
                    value="<?= htmlspecialchars($old['mertekegyseg'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <input
                    type="number"
                    name="egysegar"
                    min="0"
                    step="1"
                    placeholder="Egységár"
                    required
                    value="<?= htmlspecialchars($old['egysegar'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <select name="kategoria_id" required>
                    <option value="">Kategória</option>
                    <?php foreach ($kategoriak as $k): ?>
                        <option
                            value="<?= (int)$k['kategoria_id'] ?>"
                            <?= (($old['kategoria_id'] ?? '') == $k['kategoria_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($k['kategoria'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="beszallitok_id">
                    <option value="">Meglévő beszállító kiválasztása</option>
                    <?php foreach ($beszallitok as $b): ?>
                        <option
                            value="<?= (int)$b['beszallitok_id'] ?>"
                            <?= (($old['beszallitok_id'] ?? '') == $b['beszallitok_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($b['beszallito'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="product-card-note">
                    <strong>vagy új beszállító hozzáadása</strong>
                </div>

                <input
                    type="text"
                    name="uj_beszallito"
                    placeholder="Új beszállító neve"
                    value="<?= htmlspecialchars($old['uj_beszallito'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <input
                    type="email"
                    name="uj_beszallito_email"
                    placeholder="Új beszállító email címe"
                    value="<?= htmlspecialchars($old['uj_beszallito_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <textarea
                    name="leiras"
                    placeholder="Leírás"
                ><?= htmlspecialchars($old['leiras'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                <div class="btn-row">
                    <a href="termekek.php" class="btn cancel">Mégse</a>
                    <button type="submit" class="btn">Mentés</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>