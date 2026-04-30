<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';
require_once 'includes/product_images.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('TERMEK_OLVAS')) {
    header('Location: index.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';


$mode = $_GET['mode'] ?? 'in';
//Session üzenetek beolvasása
$success = $_SESSION['success_message'] ?? null;
$error = $_SESSION['form_errors'] ?? [];

//Törlése
unset($_SESSION['success_message'], $_SESSION['form_errors']);

//Termékek lekérése
$stmt = $pdo->query("
    SELECT
        t.termek_id,
        t.termeknev,
        t.cikkszam,
        t.leiras,
        t.mertekegyseg,
        t.egysegar,
        k.kategoria,
        b.beszallito,
        COALESCE(SUM(rk.mennyiseg), 0) AS mennyiseg,
        COUNT(DISTINCT rk.raktar_id) AS raktar_db
    FROM termekek t
    LEFT JOIN termek_kategoria k ON t.kategoria_id = k.kategoria_id
    LEFT JOIN beszallitok b ON t.beszallitok_id = b.beszallitok_id
    LEFT JOIN raktarkeszlet rk ON t.termek_id = rk.termek_id
    GROUP BY
        t.termek_id,
        t.termeknev,
        t.cikkszam,
        t.leiras,
        t.mertekegyseg,
        t.egysegar,
        k.kategoria,
        b.beszallito
    ORDER BY t.termeknev ASC
");

$termekek = $stmt->fetchAll();

//Jogosultság előkészítése
$canManageProducts = has_permission('TERMEK_KEZEL');
$canDeleteProducts = has_permission('TORLES');
$canViewStock = has_permission('RAKTARKESZLET_OLVAS');
?>

<div class="main-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="content">

        <?php if ($success): ?>
            <div class="success-box" style="margin-bottom: 15px;">
                <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error-box" style="margin-bottom: 15px;">
                <?php foreach ($error as $item): ?>
                    <p class="error-msg"><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mobile-product-list" id="productList">
            <?php if (empty($termekek)): ?>
                <div class="product-card">
                    <div class="product-card-body">
                        <h3 class="product-card-title">Nincs még rögzített termék</h3>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($termekek as $t): ?>
                    <?php $catClass = categoryToClass($t['kategoria'] ?? 'egyeb'); ?>

                    <div
                        class="product-card"
                        data-category="<?= htmlspecialchars($t['kategoria'] ?? 'Egyéb', ENT_QUOTES, 'UTF-8') ?>"
                    >
                        <div class="product-card-top">
                            <span class="badge <?= htmlspecialchars($catClass, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($t['kategoria'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <img
                            src="<?= htmlspecialchars(product_image_web_path((int)$t['termek_id']), ENT_QUOTES, 'UTF-8') ?>"
                            class="product-card-image"
                            alt="Termékkép"
                        >

                        <div class="product-card-body">
                            <h3 class="product-card-title">
                                <?= htmlspecialchars($t['termeknev'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <div class="product-card-grid">
                                <p><strong>Cikkszám:</strong> <?= htmlspecialchars($t['cikkszam'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Beszállító:</strong> <?= htmlspecialchars($t['beszallito'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Mértékegység:</strong> <?= htmlspecialchars($t['mertekegyseg'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Mennyiség:</strong> <?= (int)$t['mennyiseg'] ?></p>
                                <p><strong>Raktárak:</strong> <?= (int)$t['raktar_db'] ?></p>
                                <p><strong>Egységár:</strong> <?= number_format((int)$t['egysegar'], 0, ',', ' ') ?> Ft</p>
                            </div>

                            <?php if (!empty($t['leiras'])): ?>
                                <div class="product-card-note">
                                    <strong>Leírás:</strong>
                                    <span><?= htmlspecialchars($t['leiras'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="card-actions">
                                <?php if ($canViewStock): ?>
                                   
                                    <?php if ($mode == 'out'): ?>
                                        <a href="termek_reszletek.php?id=<?= (int)$t['termek_id'] ?>&mode=out" class="btn" style="height:auto; padding:6px 10px;">
                                        Készletkezelés
                                    <?php else: ?>
                                        <a href="termek_reszletek.php?id=<?= (int)$t['termek_id'] ?>" class="btn" style="height:auto; padding:6px 10px;">
                                        Részletek
                                    <?php endif; ?>
                                                                        
                                    
                                    </a>
                                <?php endif; ?>

                                <?php if ($canManageProducts): ?>
                                    <a href="termek_szerkesztes.php?id=<?= (int)$t['termek_id'] ?>" class="edit-btn" aria-label="Szerkesztés">
                                        ✏️
                                    </a>
                                <?php endif; ?>

                                <?php if ($canDeleteProducts): ?>
                                    <form method="POST" action="actions/delete_product.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= (int)$t['termek_id'] ?>">
                                        <button
                                            type="submit"
                                            class="delete-btn"
                                            onclick="return confirm('Biztosan törölni szeretnéd ezt a terméket?')"
                                            aria-label="Törlés"
                                        >🗑️</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php include 'includes/footer.php'; ?>