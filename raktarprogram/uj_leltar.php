<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

//Session üzenetek és régi input adatok kiolvasása
$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];

//Törlése
unset($_SESSION['form_errors'], $_SESSION['old_input']);

//Raktárak lekérése
$raktarak = $pdo->query("
    SELECT raktar_id, nev
    FROM raktar
    ORDER BY nev ASC
")->fetchAll();
?>

<div class="main-layout">
    <?php include 'includes/sidebar.php'; ?>

    <main class="content">
        <div class="form-card">
            <form class="product-form" method="POST" action="actions/create_leltar.php">
                <div class="form-right" style="width:100%;">

                    <?php if (!empty($errors)): ?>
                        <div class="error-box">
                            <?php foreach ($errors as $e): ?>
                                <p class="error-msg"><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <select name="raktar_id" required>
                        <option value="">Raktár kiválasztása</option>
                        <?php foreach ($raktarak as $r): ?>
                            <option
                                value="<?= (int)$r['raktar_id'] ?>"
                                <?= (($old['raktar_id'] ?? '') == $r['raktar_id']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($r['nev'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <textarea name="megjegyzes" placeholder="Megjegyzés"><?= htmlspecialchars($old['megjegyzes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                    <div class="btn-row">
                        <a href="leltarak.php" class="btn cancel">Mégse</a>
                        <button type="submit" class="btn">Leltár létrehozása</button>
                    </div>

                </div>
            </form>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>