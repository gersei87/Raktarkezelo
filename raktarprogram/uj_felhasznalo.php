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

//Session üzenetek és régi input adatok kiolvasása
$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];

//Törlése
unset($_SESSION['form_errors'], $_SESSION['old_input']);

//Szerepkörök lekérése
$szerepkorok = $pdo->query("
    SELECT szerepkor_id, nev
    FROM szerepkor
    ORDER BY nev ASC
")->fetchAll();
?>

<div class="form-page">
    <div class="form-card">
        <form class="product-form" method="POST" action="actions/create_user.php">
            <div class="form-right" style="width: 100%;">

                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <?php foreach ($errors as $e): ?>
                            <p class="error-msg"><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <input
                    type="text"
                    name="felhasznalonev"
                    placeholder="Felhasználónév"
                    required
                    value="<?= htmlspecialchars($old['felhasznalonev'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                    value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <input
                    type="password"
                    name="jelszo"
                    placeholder="Jelszó"
                    required
                >

                <input
                    type="password"
                    name="jelszo_megerosites"
                    placeholder="Jelszó megerősítése"
                    required
                >

                <select name="szerepkor_id" required>
                    <option value="">Szerepkör kiválasztása</option>
                    <?php foreach ($szerepkorok as $sz): ?>
                        <option
                            value="<?= (int)$sz['szerepkor_id'] ?>"
                            <?= (($old['szerepkor_id'] ?? '') == $sz['szerepkor_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($sz['nev'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="aktiv" required>
                    <option value="1" <?= (($old['aktiv'] ?? '1') === '1') ? 'selected' : '' ?>>Aktív</option>
                    <option value="0" <?= (($old['aktiv'] ?? '') === '0') ? 'selected' : '' ?>>Inaktív</option>
                </select>

                <div class="btn-row">
                    <a href="felhasznalok.php" class="btn cancel">Mégse</a>
                    <button type="submit" class="btn">Mentés</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>