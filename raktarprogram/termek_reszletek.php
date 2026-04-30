<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/product_images.php';
require_once 'includes/helpers.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('TERMEK_OLVAS')) {
    $_SESSION['form_errors'] = ['Nincs jogosultságod a termék megtekintéséhez.'];
    header('Location: termekek.php');
    exit;
}


$mode = $_GET['mode'] ?? 'in';
//Termékazonosító lekérése és ellenőrzése
$termekId = (int)($_GET['id'] ?? 0);

if ($termekId <= 0) {
    $_SESSION['form_errors'] = ['Érvénytelen termékazonosító.'];
    header('Location: termekek.php');
    exit;
}

//Termék adatainak lekérése
$stmt = $pdo->prepare(
    'SELECT
        t.termek_id,
        t.termeknev,
        t.cikkszam,
        t.leiras,
        t.mertekegyseg,
        t.egysegar,
        t.kategoria_id,
        t.beszallitok_id,
        k.kategoria,
        b.beszallito,
        b.email AS beszallito_email,
        COALESCE(SUM(rk.mennyiseg), 0) AS osszes_mennyiseg,
        COUNT(DISTINCT rk.raktar_id) AS raktar_db
    FROM termekek t
    LEFT JOIN termek_kategoria k ON t.kategoria_id = k.kategoria_id
    LEFT JOIN beszallitok b ON t.beszallitok_id = b.beszallitok_id
    LEFT JOIN raktarkeszlet rk ON t.termek_id = rk.termek_id
    WHERE t.termek_id = :id
    GROUP BY
        t.termek_id,
        t.termeknev,
        t.cikkszam,
        t.leiras,
        t.mertekegyseg,
        t.egysegar,
        t.kategoria_id,
        t.beszallitok_id,
        k.kategoria,
        b.beszallito,
        b.email
    LIMIT 1'
);
$stmt->execute([':id' => $termekId]);
$product = $stmt->fetch();

//Ha nem található
if (!$product) {
    $_SESSION['form_errors'] = ['A termék nem található.'];
    header('Location: termekek.php');
    exit;
}

//Jogosultság előkészítése
$canManageProduct = has_permission('TERMEK_KEZEL');
$canViewStock = has_permission('RAKTARKESZLET_OLVAS');
$canManageStock = has_permission('RAKTARKESZLET_KEZEL');
$canDelete = has_permission('TORLES');

//Session üzenetek kiolvasása
$success = $_SESSION['success_message'] ?? null;
$error = $_SESSION['form_errors'] ?? [];

//Session üzenetek törlése
unset($_SESSION['success_message'], $_SESSION['form_errors']);

//Készletadatok lekérése
$stockRows = [];

//Ha van jogosultság a készlet megtekintéséhez
if ($canViewStock) {
    $stmtStock = $pdo->prepare(
        'SELECT
            rk.raktarkeszlet_id,
            rk.termek_id,
            rk.raktar_id,
            rk.tarolohely_id,
            rk.mennyiseg,
            rk.allapot,
            rk.beszerzes_datuma,
            rk.megjegyzes,
            r.nev AS raktar_nev,
            th.kod AS tarolohely_kod,
            f.felhasznalonev
        FROM raktarkeszlet rk
        INNER JOIN raktar r ON rk.raktar_id = r.raktar_id
        INNER JOIN tarolohely th ON rk.tarolohely_id = th.tarolohely_id
        INNER JOIN felhasznalo f ON rk.felhasznalo_id = f.felhasznalo_id
        WHERE rk.termek_id = :termek_id
        ORDER BY r.nev ASC, th.kod ASC, rk.raktarkeszlet_id DESC'
    );
    $stmtStock->execute([':termek_id' => $termekId]);
    $stockRows = $stmtStock->fetchAll();
}

function stockStateLabel(string $allapot): string
{
    switch ($allapot) {
        case 'uj':
            return 'Új';
        case 'hasznalt':
            return 'Használt';
        case 'hibas':
            return 'Hibás';
        case 'selejt':
            return 'Selejt';
        default:
            return ucfirst($allapot);
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
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

        <div class="form-card" style="margin-bottom: 20px;">
            <div class="product-form">
                <div class="form-left">
                    <img
                        src="<?= htmlspecialchars(product_image_web_path((int)$product['termek_id']), ENT_QUOTES, 'UTF-8') ?>"
                        alt="Termékkép"
                        id="preview"
                    >
                </div>

                <div class="form-right">
                    <h2><?= htmlspecialchars($product['termeknev'], ENT_QUOTES, 'UTF-8') ?></h2>

                    <p><strong>Cikkszám:</strong> <?= htmlspecialchars($product['cikkszam'], ENT_QUOTES, 'UTF-8') ?></p>

                    <p>
                        <strong>Kategória:</strong>
                        <span class="badge <?= htmlspecialchars(categoryToClass($product['kategoria'] ?? null), ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($product['kategoria'] ?? 'Nincs kategória', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </p>

                    <p><strong>Beszállító:</strong> <?= htmlspecialchars($product['beszallito'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Mértékegység:</strong> <?= htmlspecialchars($product['mertekegyseg'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Egységár:</strong> <?= number_format((int)$product['egysegar'], 0, ',', ' ') ?> Ft</p>
                    <p><strong>Összes készlet:</strong> <?= (int)$product['osszes_mennyiseg'] ?></p>
                    <p><strong>Raktárak száma:</strong> <?= (int)$product['raktar_db'] ?></p>

                    <?php if (!empty($product['leiras'])): ?>
                        <div class="product-card-note">
                            <strong>Leírás:</strong>
                            <span><?= nl2br(htmlspecialchars((string)$product['leiras'], ENT_QUOTES, 'UTF-8')) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="btn-row" style="margin-top: 10px;">
                        <a href="termekek.php" class="btn cancel">Vissza</a>

                        <?php if ($canManageProduct): ?>
                            <a href="termek_szerkesztes.php?id=<?= (int)$product['termek_id'] ?>" class="btn">Termék szerkesztése</a>
                        <?php endif; ?>

                        <?php if ($canManageStock): ?>
                            <a href="uj_keszlet.php?termek_id=<?= (int)$product['termek_id'] ?>" class="btn">Új készlet</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($canViewStock): ?>
            <div class="mobile-product-list stock-card-list">
                <?php if (empty($stockRows)): ?>
                    <div class="product-card">
                        <div class="product-card-body">
                            <h3 class="product-card-title">Ehhez a termékhez még nincs készlet rögzítve.</h3>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($stockRows as $stock): ?>
                        <div class="product-card stock-card">
                            <div class="product-card-body">
                                <h3 class="product-card-title">
                                    <?= htmlspecialchars($stock['raktar_nev'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>

                                <div class="product-card-grid">
                                    <p><strong>Tárolóhely:</strong> <?= htmlspecialchars($stock['tarolohely_kod'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Mennyiség:</strong> <?= (int)$stock['mennyiseg'] ?></p>
                                    <p><strong>Állapot:</strong> <?= htmlspecialchars(stockStateLabel($stock['allapot']), ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Beszerzés dátuma:</strong> <?= htmlspecialchars((string)($stock['beszerzes_datuma'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Rögzítette:</strong> <?= htmlspecialchars($stock['felhasznalonev'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>

                                <?php if (!empty($stock['megjegyzes'])): ?>
                                    <div class="product-card-note">
                                        <strong>Megjegyzés:</strong>
                                        <span><?= htmlspecialchars((string)$stock['megjegyzes'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($canManageStock || $canDelete): ?>
                                    <div class="card-actions">
                                        <?php if ($canManageStock): ?>
                                            <?php if ($mode == "out"): ?>
                                             <a href="keszlet_mozgas.php?id=<?= (int)$stock['raktarkeszlet_id'] ?>&termek_Id=<?= (int)$termekId ?>"
                                                class="edit-btn"
                                                aria-label="Szerkesztés"
                                            >Készletkezelés</a>
                                            <?php else: ?>
                                                <a href="keszlet_szerkesztes.php?id=<?= (int)$stock['raktarkeszlet_id'] ?>"
                                                class="edit-btn"
                                                aria-label="Szerkesztés"
                                            >✏️</a>
                                            <?php endif; ?>

                                        <?php endif; ?>

                                        <?php if ($canDelete): ?>
                                            <form action="actions/delete_stock.php" method="POST" style="display:inline-block;">
                                                <input type="hidden" name="id" value="<?= (int)$stock['raktarkeszlet_id'] ?>">
                                                <input type="hidden" name="termek_id" value="<?= (int)$product['termek_id'] ?>">
                                                <button
                                                    type="submit"
                                                    class="delete-btn"
                                                    onclick="return confirm('Biztosan törölni szeretnéd ezt a készletrekordot?');"
                                                    aria-label="Törlés"
                                                >🗑️</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php include 'includes/footer.php'; ?>