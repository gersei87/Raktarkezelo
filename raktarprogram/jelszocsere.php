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

//Session üzenetek kiolvasása
$errors = $_SESSION['form_errors'] ?? [];
$success = $_SESSION['success_message'] ?? null;

//Törlése
unset($_SESSION['form_errors'], $_SESSION['success_message']);

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="main-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="content">
        <div class="form-card">
            <form class="product-form" method="POST" action="actions/change_password.php">
                <div class="form-right" style="width: 100%;">

                    <h2>Jelszócsere</h2>

                    <!-- Hibák és sikerüzenetek kezelése -->
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

                    <input type="password" name="regi_jelszo" placeholder="Régi jelszó" required>
                    <input type="password" name="uj_jelszo" placeholder="Új jelszó" required>
                    <input type="password" name="uj_jelszo_megerosites" placeholder="Új jelszó megerősítése" required>

                    <div class="btn-row">
                        <a href="profil.php" class="btn cancel">Mégse</a>
                        <button type="submit" class="btn">Mentés</button>
                    </div>

                </div>
            </form>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>