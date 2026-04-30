<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/auth.php';
require_once 'includes/db.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('RAKTARKESZLET_KEZEL')) {
    header('Location: termekek.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

//Session üzenetek és régi input adatok kiolvasása
$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];

//Törlése
unset($_SESSION['form_errors'], $_SESSION['old_input']);

//Előzetes termékadatok lekérése
$termekId = (int)($old['termek_id'] ?? ($_GET['termek_id'] ?? 0));

//Termékek lekérése
$termekek = $pdo->query(
    "SELECT termek_id, termeknev, cikkszam
     FROM termekek
     ORDER BY termeknev ASC"
)->fetchAll();

//Raktárak lekérése
$raktarak = $pdo->query(
    "SELECT raktar_id, nev
     FROM raktar
     ORDER BY nev ASC"
)->fetchAll();

//Tárolóhelyek előkészítése
$tarolohelyek = [];

//Ha van előzetes raktárválasztás, akkor a tárolóhelyeket is lekérjük
if (!empty($old['raktar_id'])) {
    $stmt = $pdo->prepare(
        "SELECT tarolohely_id, kod
         FROM tarolohely
         WHERE raktar_id = :raktar_id
         ORDER BY kod ASC"
    );
    $stmt->execute([':raktar_id' => (int)$old['raktar_id']]);
    $tarolohelyek = $stmt->fetchAll();
}
?>

<div class="form-page">
    <div class="form-card">
        <form method="POST" action="actions/create_stock.php">

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <?php foreach ($errors as $e): ?>
                        <p class="error-msg"><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Termék -->
            <select name="termek_id" required>
                <option value="">Termék kiválasztása</option>
                <?php foreach ($termekek as $t): ?>
                    <option value="<?= $t['termek_id'] ?>"
                        <?= ($termekId == $t['termek_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['termeknev'] . ' (' . $t['cikkszam'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Raktár -->
            <select name="raktar_id" id="raktarSelect" required>
                <option value="">Raktár kiválasztása</option>
                <?php foreach ($raktarak as $r): ?>
                    <option value="<?= $r['raktar_id'] ?>"
                        <?= (($old['raktar_id'] ?? '') == $r['raktar_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nev']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Tárolóhely -->
            <select name="tarolohely_id" id="tarolohelySelect" required>
                <option value="">Tárolóhely kiválasztása</option>
                <?php foreach ($tarolohelyek as $th): ?>
                    <option value="<?= $th['tarolohely_id'] ?>">
                        <?= htmlspecialchars($th['kod']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Állapot -->
            <select name="allapot" required>
                <option value="">Állapot</option>
                <option value="uj">Új</option>
                <option value="hasznalt">Használt</option>
                <option value="hibas">Hibás</option>
                <option value="selejt">Selejt</option>
            </select>

            <input type="number" name="mennyiseg" placeholder="Mennyiség" required min="1"
                value="<?= htmlspecialchars($old['mennyiseg'] ?? '') ?>">

            <input type="date" name="beszerzes_datuma"
                value="<?= htmlspecialchars($old['beszerzes_datuma'] ?? '') ?>">

            <textarea name="megjegyzes" placeholder="Megjegyzés"><?= htmlspecialchars($old['megjegyzes'] ?? '') ?></textarea>

            <div class="btn-row">
                <a href="<?= $termekId > 0 ? 'termek_reszletek.php?id=' . $termekId : 'termekek.php' ?>" class="btn cancel">Mégse</a>
                <button type="submit" class="btn">Mentés</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/ajax_tarolohely.js"></script>

<?php include 'includes/footer.php'; ?>