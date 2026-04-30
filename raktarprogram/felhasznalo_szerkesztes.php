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

//A szerkesztendő felhasználó adatai
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: felhasznalok.php');
    exit;
}

//A szerkesztendő felhasználó adatainak lekérése
$stmt = $pdo->prepare("
    SELECT
        f.felhasznalo_id,
        f.felhasznalonev,
        f.email,
        f.aktiv,
        fsz.szerepkor_id
    FROM felhasznalo f
    LEFT JOIN felhasznalo_szerepkor fsz ON f.felhasznalo_id = fsz.felhasznalo_id
    WHERE f.felhasznalo_id = :id
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

//Ha nem található
if (!$user) {
    header('Location: felhasznalok.php');
    exit;
}

//Szerepkörök kezelése
$szerepkorok = $pdo->query("
    SELECT szerepkor_id, nev
    FROM szerepkor
    ORDER BY nev ASC
")->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';

// Hibák, sikerüzenetek és előzőleg megadott adatok kezelése
$errors = $_SESSION['form_errors'] ?? [];
$success = $_SESSION['success_message'] ?? null;
$old = $_SESSION['old_input'] ?? [];

//Törlése
unset($_SESSION['form_errors'], $_SESSION['success_message'], $_SESSION['old_input']);

//Megjelenítendő adatok előkészítése
$data = [
    'felhasznalonev' => $old['felhasznalonev'] ?? $user['felhasznalonev'],
    'email' => $old['email'] ?? $user['email'],
    'aktiv' => $old['aktiv'] ?? $user['aktiv'],
    'szerepkor_id' => $old['szerepkor_id'] ?? $user['szerepkor_id'],
];
?>

<div class="form-page">
    <div class="form-card">
        <form class="product-form" method="POST" action="actions/update_user_role.php">
            <input type="hidden" name="felhasznalo_id" value="<?= (int)$user['felhasznalo_id'] ?>">

            <div class="form-right" style="width: 100%;">

                <!--Hibaüzenet-->
                <?php if (!empty($errors)): ?>
                    <div class="error-box">
                        <?php foreach ($errors as $e): ?>
                            <p class="error-msg"><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!--Sikerüzenet-->
                <?php if ($success): ?>
                    <div class="success-box">
                        <p><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php endif; ?>

                <input
                    type="text"
                    name="felhasznalonev"
                    placeholder="Felhasználónév"
                    required
                    value="<?= htmlspecialchars((string)$data['felhasznalonev'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                    value="<?= htmlspecialchars((string)$data['email'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <select name="szerepkor_id" required>
                    <option value="">Szerepkör kiválasztása</option>
                    <?php foreach ($szerepkorok as $sz): ?>
                        <option
                            value="<?= (int)$sz['szerepkor_id'] ?>"
                            <?= ((string)$data['szerepkor_id'] === (string)$sz['szerepkor_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($sz['nev'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="aktiv" required>
                    <option value="1" <?= ((string)$data['aktiv'] === '1') ? 'selected' : '' ?>>Aktív</option>
                    <option value="0" <?= ((string)$data['aktiv'] === '0') ? 'selected' : '' ?>>Inaktív</option>
                </select>

                <input
                    type="password"
                    name="uj_jelszo"
                    placeholder="Új jelszó (nem kötelező)"
                >

                <div class="btn-row">
                    <a href="felhasznalok.php" class="btn cancel">Mégse</a>
                    <button type="submit" class="btn">Mentés</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>