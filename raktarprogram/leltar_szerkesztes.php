<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//Betöltések
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('FELHASZNALO_KEZEL')) {
    header('Location: index.php');
    exit;
}
//Leltár Id beolvasása
$leltarId = (int)($_GET['id'] ?? 0);
//Érvényesség ellenőrzése
if ($leltarId <= 0) {
    header('Location: leltarak.php');
    exit;
}

//Leltár adatainak lekérése
$stmtL = $pdo->prepare("
    SELECT
        l.leltar_id,
        l.allapot,
        l.megjegyzes,
        l.letrehozva,
        l.lezarva,
        r.nev AS raktar_nev
    FROM leltar l
    INNER JOIN raktar r ON l.raktar_id = r.raktar_id
    WHERE l.leltar_id = :id
    LIMIT 1
");
$stmtL->execute([':id' => $leltarId]);
$leltar = $stmtL->fetch();

//Ha nem található
if (!$leltar) {
    header('Location: leltarak.php');
    exit;
}

//Leltár tételek lekérése
$stmtT = $pdo->prepare("
    SELECT
        lt.leltar_tetel_id,
        lt.vart_mennyiseg,
        lt.tenyleges_mennyiseg,
        lt.elteres,
        rk.raktarkeszlet_id,
        rk.allapot,
        rk.megjegyzes,
        t.termeknev,
        t.cikkszam,
        th.kod AS tarolohely_kod
    FROM leltar_tetel lt
    INNER JOIN raktarkeszlet rk ON lt.raktarkeszlet_id = rk.raktarkeszlet_id
    INNER JOIN termekek t ON rk.termek_id = t.termek_id
    INNER JOIN tarolohely th ON rk.tarolohely_id = th.tarolohely_id
    WHERE lt.leltar_id = :leltar_id
    ORDER BY t.termeknev ASC, th.kod ASC
");
$stmtT->execute([':leltar_id' => $leltarId]);
$tetelek = $stmtT->fetchAll();

//Session üzenetek beolvasása
$success = $_SESSION['success_message'] ?? null;
$error = $_SESSION['form_errors'] ?? [];

//Törlése
unset($_SESSION['success_message'], $_SESSION['form_errors']);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="content">

        <!--Sikerüzenet-->
        <?php if ($success): ?>
            <div class="success-box" style="margin-bottom: 15px;">
                <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <!--Hibaüzenetek-->
        <?php if (!empty($error)): ?>
            <div class="error-box" style="margin-bottom: 15px;">
                <?php foreach ($error as $item): ?>
                    <p class="error-msg"><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!--Fejlécinformáció-->
        <div class="form-card" style="margin-bottom: 20px;">
            <div class="product-form">
                <div class="form-right" style="width: 100%;">
                    <h2>Leltár: <?= htmlspecialchars($leltar['raktar_nev'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><strong>Állapot:</strong> <?= htmlspecialchars($leltar['allapot'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Létrehozva:</strong> <?= htmlspecialchars($leltar['letrehozva'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Lezárva:</strong> <?= htmlspecialchars((string)($leltar['lezarva'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>

                    <?php if (!empty($leltar['megjegyzes'])): ?>
                        <div class="product-card-note">
                            <strong>Megjegyzés:</strong>
                            <span><?= htmlspecialchars((string)$leltar['megjegyzes'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!--Mentés-->
        <form method="POST" action="actions/save_leltar.php">
            <input type="hidden" name="leltar_id" value="<?= (int)$leltar['leltar_id'] ?>">

            <!--Ha nincs tétel-->
            <div class="mobile-product-list stock-card-list">
                <?php if (empty($tetelek)): ?>
                    <div class="product-card">
                        <div class="product-card-body">
                            <h3 class="product-card-title">Ehhez a leltárhoz nincs tétel.</h3>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($tetelek as $t): ?>
                        <div class="product-card stock-card">
                            <div class="product-card-body">
                                <h3 class="product-card-title">
                                    <?= htmlspecialchars($t['termeknev'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>

                                <div class="product-card-grid">
                                    <p><strong>Cikkszám:</strong> <?= htmlspecialchars($t['cikkszam'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Tárolóhely:</strong> <?= htmlspecialchars($t['tarolohely_kod'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Állapot:</strong> <?= htmlspecialchars($t['allapot'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <p><strong>Várt mennyiség:</strong> <?= (int)$t['vart_mennyiseg'] ?></p>

                                    <p>
                                        <strong>Tényleges mennyiség:</strong><br>
                                        <?php if ($leltar['allapot'] === 'nyitott'): ?>
                                            <input
                                                type="number"
                                                min="0"
                                                name="tenyleges[<?= (int)$t['leltar_tetel_id'] ?>]"
                                                value="<?= htmlspecialchars((string)($t['tenyleges_mennyiseg'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                            >
                                        <?php else: ?>
                                            <?= htmlspecialchars((string)($t['tenyleges_mennyiseg'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                        <?php endif; ?>
                                    </p>

                                    <p><strong>Eltérés:</strong> <?= htmlspecialchars((string)($t['elteres'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                                </div>

                                <?php if (!empty($t['megjegyzes'])): ?>
                                    <div class="product-card-note">
                                        <strong>Készlet megjegyzés:</strong>
                                        <span><?= htmlspecialchars((string)$t['megjegyzes'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($leltar['allapot'] === 'nyitott'): ?>
                <div class="btn-row" style="margin-top: 15px;">
                    <button type="submit" name="muvelet" value="mentes" class="btn">Mentés</button>
                    <button type="submit" name="muvelet" value="lezaras" class="btn cancel">Leltár lezárása</button>
                </div>
            <?php endif; ?>
        </form>

    </main>
</div>

<?php include 'includes/footer.php'; ?>