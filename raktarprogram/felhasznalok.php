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

//Session üzenetek kiolvasása
$success = $_SESSION['success_message'] ?? null;
$error = $_SESSION['form_errors'] ?? [];

//Törlése
unset($_SESSION['success_message'], $_SESSION['form_errors']);

//Felhasználók lekérése
$stmt = $pdo->query("
    SELECT
        f.felhasznalo_id,
        f.felhasznalonev,
        f.email,
        f.aktiv,
        f.letrehozva,
        sz.nev AS szerepkor
    FROM felhasznalo f
    LEFT JOIN felhasznalo_szerepkor fsz ON f.felhasznalo_id = fsz.felhasznalo_id
    LEFT JOIN szerepkor sz ON fsz.szerepkor_id = sz.szerepkor_id
    ORDER BY f.felhasznalonev ASC
");

//Beolvasása
$felhasznalok = $stmt->fetchAll();
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

        <div class="mobile-product-list">
            <?php if (empty($felhasznalok)): ?>
                <div class="product-card">
                    <div class="product-card-body">
                        <h3 class="product-card-title">Nincs még felhasználó</h3>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($felhasznalok as $f): ?>
                    <div class="product-card">
                        <div class="product-card-body">
                            <h3 class="product-card-title">
                                <?= htmlspecialchars($f['felhasznalonev'], ENT_QUOTES, 'UTF-8') ?>
                            </h3>

                            <div class="product-card-grid">
                                <p><strong>Email:</strong> <?= htmlspecialchars($f['email'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Szerepkör:</strong> <?= htmlspecialchars($f['szerepkor'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Aktív:</strong> <?= ((int)$f['aktiv'] === 1) ? 'Igen' : 'Nem' ?></p>
                                <p><strong>Létrehozva:</strong> <?= htmlspecialchars((string)($f['letrehozva'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>

                            <div class="card-actions">
                                <a href="felhasznalo_szerkesztes.php?id=<?= (int)$f['felhasznalo_id'] ?>" class="edit-btn" aria-label="Szerkesztés">✏️</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php include 'includes/footer.php'; ?>