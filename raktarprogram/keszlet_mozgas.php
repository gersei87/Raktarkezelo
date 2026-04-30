<?php
session_start();

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/product_images.php';
require_once 'includes/helpers.php';

include 'includes/header.php';
include 'includes/navbar.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// raktárak
$raktarak = $pdo->query("SELECT raktar_id, nev FROM raktar")->fetchAll(PDO::FETCH_ASSOC);

// termékek
$termekek = $pdo->query("SELECT termek_id, termeknev FROM termekek")->fetchAll(PDO::FETCH_ASSOC);

$message = "";
$siker = true;

// POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        die("Érvénytelen kérés!");
    }

    $termek = $_POST["termek_id"];
    $raktar = $_POST["raktar_id"];
    $tarolo = $_POST["tarolohely_id"];
    $mennyiseg = $_POST["mennyiseg"];
    $tipus = $_POST["tipus"];
    $user = $_SESSION["user_id"];

    $_SESSION['tipus'] = $tipus;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            SELECT mennyiseg
            FROM raktarkeszlet
            WHERE termek_id = ?
            AND raktar_id = ?
            AND tarolohely_id = ?
        ");
        $stmt->execute([$termek, $raktar, $tarolo]);

        $keszlet = $stmt->fetchColumn();

        if ($tipus === "bevetel") {

            if ($keszlet === false) {
                $stmt = $pdo->prepare("
                    INSERT INTO raktarkeszlet
                    (termek_id, raktar_id, tarolohely_id, felhasznalo_id, mennyiseg)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$termek, $raktar, $tarolo, $user, $mennyiseg]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE raktarkeszlet
                    SET mennyiseg = ?
                    WHERE termek_id = ? AND raktar_id = ? AND tarolohely_id = ?
                ");
                $stmt->execute([(int)$keszlet + $mennyiseg, $termek, $raktar, $tarolo]);
            }

            $message = "Bevétel sikeres!";
        }

        elseif ($tipus === "kiadas") {

            if ($keszlet === false || $mennyiseg > $keszlet) {
                $pdo->rollBack();
                $siker = false;
                $message = "Nincs elég készlet!";
            } else {
                $stmt = $pdo->prepare("
                    UPDATE raktarkeszlet
                    SET mennyiseg = ?
                    WHERE termek_id = ? AND raktar_id = ? AND tarolohely_id = ?
                ");
                $stmt->execute([(int)$keszlet - $mennyiseg, $termek, $raktar, $tarolo]);

                $message = "Kiadás sikeres!";
            }
        }

        if ($siker) {
            $stmt = $pdo->prepare("
                INSERT INTO keszlet_mozgas
                (termek_id, raktar_id, tarolohely_id, felhasznalo_id, mennyiseg, tipus)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$termek, $raktar, $tarolo, $user, $mennyiseg, $tipus]);

            $pdo->commit();
        }

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = "Hiba: " . $e->getMessage();
    }
}

$termekId = (int)($_GET['termek_Id'] ?? 0);
?>

<h2>Készletkezelés</h2>


<div class="form-page">
    <div class="form-card">
      
        <form   method="POST" style="display:inline-block"> 
             <!-- CSRF token -->
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            

           
                <div class="succes-box"
                   <!-- Üzenet kiírás -->
                    
                    <?php if ($message): ?>
                        <p class="error-msg"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>






        <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

        <label>Típus:</label>
        <select name="tipus" id="tipus">
            <option value="bevetel">Bevétel</option>
            <option value="kiadas">Kiadás</option>
        </select>

        <br><br>

        <label>Termék:</label>
        <select name="termek_id" id="termek">
            <?php foreach ($termekek as $t): ?>
                <option value="<?= $t['termek_id'] ?>"
                    <?= $t['termek_id'] == $termekId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['termeknev']) ?>
                </option>
            <?php endforeach; ?> 
        </select>

        <br><br>

        <label>Raktár:</label>
        <select id="raktar" name="raktar_id">
            <option value="">-- válassz --</option>
            <?php foreach ($raktarak as $r): ?>
                <option value="<?= $r['raktar_id'] ?>">
                    <?= htmlspecialchars($r['nev']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label>Tárolóhely:</label>
        <select id="tarolohely" name="tarolohely_id"></select>

        <br><br>
        <label>Mennyiség:</label>
        <input type="number" name="mennyiseg" min="1">

        
        <div class="btn-row">
            <a href="index.php" class="btn cancel">Mégse</a>
            <button type="submit" class="btn">Mentés</button>
        </div>
        </form>
        <br>

    </div>
</div>
<script>
function loadTarolohely() {

    let raktarId = document.getElementById("raktar").value;
    let termekId = document.getElementById("termek").value;
    let tipus = document.getElementById("tipus").value;

    let select = document.getElementById("tarolohely");

    if (!raktarId) return;

    select.innerHTML = "<option>Betöltés...</option>";

    fetch("get_tarolohely.php?raktar_id=" + raktarId +
          "&termek_id=" + termekId +
          "&tipus=" + tipus)
    .then(r => r.json())
    .then(data => {

        select.innerHTML = "";

        if (!data.length) {
            select.innerHTML = "<option>Nincs</option>";
            return;
        }

        data.forEach(x => {
            let o = document.createElement("option");
            o.value = x.tarolohely_id;
            o.textContent = x.kod;
            select.appendChild(o);
        });
    });
}

// EVENTEK
document.getElementById("raktar").addEventListener("change", loadTarolohely);
document.getElementById("tipus").addEventListener("change", loadTarolohely);
document.getElementById("termek").addEventListener("change", loadTarolohely);

// INIT (EZ HIÁNYZOTT!)
window.addEventListener("load", loadTarolohely);
</script>

<?php include 'includes/footer.php'; ?>