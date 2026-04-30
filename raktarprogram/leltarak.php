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

include 'includes/header.php';
include 'includes/navbar.php';

//Session üzenetek beolvasása
$success = $_SESSION['success_message'] ?? null;
$error = $_SESSION['form_errors'] ?? [];

//Törlése
unset($_SESSION['success_message'], $_SESSION['form_errors']);

//Leltárak lekérése
$stmt = $pdo->query("
    SELECT
        l.leltar_id,
        l.allapot,
        l.megjegyzes,
        l.letrehozva,
        l.lezarva,
        r.nev AS raktar_nev,
        f.felhasznalonev
    FROM leltar l
    INNER JOIN raktar r ON l.raktar_id = r.raktar_id
    INNER JOIN felhasznalo f ON l.felhasznalo_id = f.felhasznalo_id
    ORDER BY l.leltar_id DESC
");
$leltarak = $stmt->fetchAll();
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

        <!--Hibaüzenet-->
        <?php if (!empty($error)): ?>
            <div class="error-box" style="margin-bottom: 15px;">
                <?php foreach ($error as $item): ?>
                    <p class="error-msg"><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="margin-bottom:15px;">
            <a href="uj_leltar.php" class="btn">+ Új leltár</a>
        </div>

        <div class="mobile-product-list">
            <?php if (empty($leltarak)): ?>
                <div class="product-card">
                    <div class="product-card-body">
                        <h3 class="product-card-title">Nincs még leltár</h3>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($leltarak as $l): ?>
                    <div class="product-card">
                        <div class="product-card-body">
                            <h3 class="product-card-title">
                                <?= htmlspecialchars($l['raktar_nev'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <div class="product-card-grid">
                                <p><strong>Állapot:</strong> <?= htmlspecialchars($l['allapot'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Létrehozta:</strong> <?= htmlspecialchars($l['felhasznalonev'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Létrehozva:</strong> <?= htmlspecialchars($l['letrehozva'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Lezárva:</strong> <?= htmlspecialchars((string)($l['lezarva'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>

                            <?php if (!empty($l['megjegyzes'])): ?>
                                <div class="product-card-note">
                                    <strong>Megjegyzés:</strong>
                                    <span><?= htmlspecialchars($l['megjegyzes'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="card-actions">
                                <a href="leltar_szerkesztes.php?id=<?= (int)$l['leltar_id'] ?>" class="btn" style="height:auto;padding:6px 10px;">Megnyitás</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php include 'includes/footer.php'; ?>