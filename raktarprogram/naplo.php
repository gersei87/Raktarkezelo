<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('NAPLO_OLVAS')) {
    header('Location: index.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

//Naplóbejegyzések lekérése
$stmt = $pdo->query(
    'SELECT
        n.naplo_id,
        n.tipus,
        n.leiras,
        n.datum,
        f.felhasznalonev
     FROM naplo n
     LEFT JOIN felhasznalo f ON n.felhasznalo_id = f.felhasznalo_id
     ORDER BY n.naplo_id DESC
     LIMIT 200'
);

//Beolvasása
$naplok = $stmt->fetchAll();
?>

<div class="main-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="content">
        <div class="mobile-product-list">
            <?php if (empty($naplok)): ?>
                <div class="product-card">
                    <div class="product-card-body">
                        <h3 class="product-card-title">Nincs még naplóbejegyzés</h3>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($naplok as $n): ?>
                    <div class="product-card">
                        <div class="product-card-body">
                            <h3 class="product-card-title">
                                <?= htmlspecialchars($n['tipus'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <div class="product-card-grid">
                                <p><strong>Felhasználó:</strong> <?= htmlspecialchars($n['felhasznalonev'] ?? 'ismeretlen', ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Dátum:</strong> <?= htmlspecialchars($n['datum'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>

                            <div class="product-card-note">
                                <strong>Leírás:</strong>
                                <span><?= htmlspecialchars($n['leiras'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>