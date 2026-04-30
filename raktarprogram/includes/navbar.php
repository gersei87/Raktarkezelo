<?php
$currentPage = basename($_SERVER['PHP_SELF']);
//megállapítja az aktuális futó PHP fájl nevét (útvonal nélkül).
//echo "Teszt navbar:" . $currentPage  //index.php
?>

<div class="navbar">

    <h1>Raktárprogram</h1>

    <div class="nav-right">

        <!-- Keresés -->
        <?php if ($currentPage === 'termekek.php'): ?>
            <input
                type="text"
                id="searchInput"
                placeholder="Keresés..."
            >
        <?php endif; ?>

        <!-- Új termék -->
        <?php if (has_permission('TERMEK_KEZEL') && $currentPage !== 'uj_termek.php'): ?>
            <a href="uj_termek.php" class="btn">+ Új termék</a>
        <?php endif; ?>

        <!-- Új készlet -->
        <?php if (has_permission('RAKTARKESZLET_KEZEL') && $currentPage !== 'uj_keszlet.php'): ?>
            <a href="uj_keszlet.php" class="btn">+ Új készlet</a>
        <?php endif; ?>




        <!-- Új felhasználó -->
        <?php if (has_permission('FELHASZNALO_KEZEL') && $currentPage !== 'uj_felhasznalo.php'): ?>
            <a href="uj_felhasznalo.php" class="btn">+ Új felhasználó</a>
        <?php endif; ?>

        <!-- Sötét mód -->
        <button type="button" id="darkToggle" class="btn">🌙</button>

        <!-- Kijelentkezés -->
        <a href="logout.php" class="btn cancel">Kijelentkezés</a>

    </div>
</div>