<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

//Bejelentkezett felhasználó azonosítója
$userId = (int)($_SESSION['user_id'] ?? 0);

//Saját profil adatainak lekérése
$stmt = $pdo->prepare("
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
    WHERE f.felhasznalo_id = :id
    LIMIT 1
");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

//Ha nem található
if (!$user) {
    header('Location: logout.php');
    exit;
}

//Session üzenetek és régi input adatok kiolvasása
$errors = $_SESSION['form_errors'] ?? [];
$success = $_SESSION['success_message'] ?? null;
$old = $_SESSION['old_input'] ?? [];

//Törlése
unset($_SESSION['form_errors'], $_SESSION['success_message'], $_SESSION['old_input']);

//Megjelenítendő adatok előkészítése
$data = [
    'felhasznalonev' => $old['felhasznalonev'] ?? $user['felhasznalonev'],
    'email' => $old['email'] ?? $user['email'],
];

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="content">
        <div class="form-card">
            <form class="product-form" method="POST" action="actions/update_profile.php">
                <div class="form-right" style="width: 100%;">

                    <h2>Saját profil</h2>

                    <?php if (!empty($errors)): ?>
                        <div class="error-box">
                            <?php foreach ($errors as $e): ?>
                                <p class="error-msg"><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="success-box">
                            <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    <?php endif; ?>

                    <input type="text" name="felhasznalonev" required value="<?= htmlspecialchars($data['felhasznalonev'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Felhasználónév">
                    <input type="email" name="email" required value="<?= htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Email">

                    <div class="product-card-grid">
                        <p><strong>Szerepkör:</strong> <?= htmlspecialchars($user['szerepkor'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Aktív:</strong> <?= ((int)$user['aktiv'] === 1) ? 'Igen' : 'Nem' ?></p>
                        <p><strong>Létrehozva:</strong> <?= htmlspecialchars((string)($user['letrehozva'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>

                    <div class="btn-row">
                        <a href="jelszocsere.php" class="btn">Jelszócsere</a>
                        <button type="submit" class="btn">Mentés</button>
                    </div>

                </div>
            </form>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>