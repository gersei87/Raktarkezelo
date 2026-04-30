<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Nincs jogosultság ellenőrzés
define('SKIP_AUTH_CHECK', true);

require_once 'includes/db.php'; 
require_once 'includes/auth.php';

//Ha már be van jelentkezve, átirányítás
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

//Hiba paraméter beolvasása
$error = $_GET['error'] ?? '';
include 'includes/header.php';
?>

<div class="login-page">
    <div class="login-card">
        <h2>Bejelentkezés</h2>

        <?php if ($error === 'invalid'): ?>
            <div class="error-box" style="margin-bottom: 15px;">
                <p class="error-msg">Hibás felhasználónév/email vagy jelszó.</p>
            </div>
        <?php elseif ($error === 'csrf'): ?>
            <div class="error-box" style="margin-bottom: 15px;">
                <p class="error-msg">Érvénytelen kérés.</p>
            </div>
        <?php endif; ?>

        <form method="POST" action="actions/login_action.php">
            <?= csrf_input() ?>   <!-- auth.php-ban definiálva 38.sor-->

            <div class="form-group">
                <label for="login">Felhasználónév vagy email</label>
                <input
                    type="text"
                    id="login"
                    name="login"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password">Jelszó</label>
                <!--A for attribútum a <label>-ben azt mondja meg, 
                    hogy melyik input mezőhöz tartozik a címke.
                    id="password"-->
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn">Belépés</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>