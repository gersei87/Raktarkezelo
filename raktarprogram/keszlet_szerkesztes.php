<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Jogosultság ellenőrzése
load_user_auth($pdo);

if (!has_permission('RAKTARKESZLET_KEZEL')) {
    header('Location: termekek.php');
    exit;
}

//Azonosító beolvasása url-ből
$id = (int)($_GET['id'] ?? 0);

//Készletrekord lekérése
$stmt = $pdo->prepare("SELECT * FROM raktarkeszlet WHERE raktarkeszlet_id = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch();

//Ha nem található
if (!$data) {
    header('Location: termekek.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

//Raktárak lekérdezése
$raktarak = $pdo->query("SELECT raktar_id, nev FROM raktar")->fetchAll();

//Tárolóhelyek lekérdezése a kiválasztott raktár alapján
$stmt2 = $pdo->prepare("SELECT tarolohely_id, kod FROM tarolohely WHERE raktar_id = :raktar_id");
$stmt2->execute([':raktar_id' => $data['raktar_id']]);
$tarolohelyek = $stmt2->fetchAll();
?>

<div class="form-page">
    <div class="form-card">
        <form method="POST" action="actions/update_stock.php">

            <input type="hidden" name="raktarkeszlet_id" value="<?= $data['raktarkeszlet_id'] ?>">
            <input type="hidden" name="termek_id" value="<?= $data['termek_id'] ?>">

            <select name="raktar_id" id="raktarSelect">
                <?php foreach ($raktarak as $r): ?>
                    <option value="<?= $r['raktar_id'] ?>"
                        <?= ($data['raktar_id'] == $r['raktar_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nev']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="tarolohely_id" id="tarolohelySelect">
                <?php foreach ($tarolohelyek as $th): ?>
                    <option value="<?= $th['tarolohely_id'] ?>"
                        <?= ($data['tarolohely_id'] == $th['tarolohely_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($th['kod']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="allapot">
                <option value="uj" <?= $data['allapot']=='uj'?'selected':'' ?>>Új</option>
                <option value="hasznalt" <?= $data['allapot']=='hasznalt'?'selected':'' ?>>Használt</option>
                <option value="hibas" <?= $data['allapot']=='hibas'?'selected':'' ?>>Hibás</option>
                <option value="selejt" <?= $data['allapot']=='selejt'?'selected':'' ?>>Selejt</option>
            </select>

            <input type="number" name="mennyiseg" value="<?= $data['mennyiseg'] ?>" min="1">

            <input type="date" name="beszerzes_datuma" value="<?= $data['beszerzes_datuma'] ?>">

            <textarea name="megjegyzes"><?= htmlspecialchars($data['megjegyzes']) ?></textarea>

            <div class="btn-row">
                <a href="termek_reszletek.php?id=<?= $data['termek_id'] ?>" class="btn cancel">Mégse</a>
                <button type="submit" class="btn">Mentés</button>
            </div>
        </form>
    </div>
</div>

<!--AJAX tárolóhely frissítése js-->
<script src="assets/js/ajax_tarolohely.js"></script>

<?php include 'includes/footer.php'; ?>