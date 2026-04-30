<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Betöltések
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

//Aktuális oldal meghatározása a navigáció kiemeléséhez
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
//echo "Teszt sidebar:" . $currentPage;  //index.php

//Segédfüggvény a sidebar linkek aktív állapotának meghatározásához
function isSidebarActive(array $pages, string $currentPage): string
{
    return in_array($currentPage, $pages, true) ? 'active' : '';
}

//Jogosultságok lekérése a megjelenítéshez
$canViewProducts = function_exists('has_permission') ? has_permission('TERMEK_OLVAS') : false;
$canManageProducts = function_exists('has_permission') ? has_permission('TERMEK_KEZEL') : false;
$canViewStock = function_exists('has_permission') ? has_permission('RAKTARKESZLET_OLVAS') : false;
$canManageStock = function_exists('has_permission') ? has_permission('RAKTARKESZLET_KEZEL') : false;
$canManageUsers = function_exists('has_permission') ? has_permission('FELHASZNALO_KEZEL') : false;

// Kategóriák lekérése az adatbázisból
$kategoriak = [];
if ($canViewProducts) {
    $kategoriak = $pdo->query("
        SELECT kategoria
        FROM termek_kategoria
        ORDER BY kategoria ASC
    ")->fetchAll();
}
?>

<div class="sidebar">
    <ul>

        <li class="<?= isSidebarActive(['index.php'], $currentPage) ?>">
            <a href="index.php" style="display:flex;align-items:center;gap:10px;width:100%;text-decoration:none;color:inherit;">
                <span class="nav-icon">🏠</span>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>

        <?php if ($canViewProducts): ?>
            <li class="<?= isSidebarActive(['termekek.php', 'termek_reszletek.php', 'termek_szerkesztes.php'], $currentPage) ?>">
                <a href="termekek.php" style="display:flex;align-items:center;gap:10px;width:100%;text-decoration:none;color:inherit;">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Termékek</span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($canManageProducts): ?>
            <li class="<?= isSidebarActive(['uj_termek.php'], $currentPage) ?>">
                <a href="uj_termek.php" style="display:flex;align-items:center;gap:10px;width:100%;text-decoration:none;color:inherit;">
                    <span class="nav-icon">➕</span>
                    <span class="nav-text">Új termék</span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($canManageStock): ?>
            <li class="<?= isSidebarActive(['uj_keszlet.php', 'keszlet_szerkesztes.php'], $currentPage) ?>">
                <a href="uj_keszlet.php" style="display:flex;align-items:center;gap:10px;width:100%;text-decoration:none;color:inherit;">
                    <span class="nav-icon">🏷️</span>
                    <span class="nav-text">Új készlet</span>
                   
                </a>
            </li>
        <?php endif; ?>

        <?php if ($canManageStock): ?>
            <li class="<?= isSidebarActive(['termekek.php', 'termek_reszletek.php', 'termek_szerkesztes.php'], $currentPage) ?>">
                <a href="termekek.php?mode=out" style="display:flex;align-items:center;gap:10px;width:100%;text-decoration:none;color:inherit;">
                    <span class="nav-icon">🏷️</span>
                    <span class="nav-text">Készletkezelés</span>            
                   
                </a>
            </li>
        <?php endif; ?>



        <?php if ($canManageUsers): ?>
            <li class="<?= isSidebarActive(['felhasznalok.php', 'uj_felhasznalo.php', 'felhasznalo_szerkesztes.php'], $currentPage) ?>">
                <a href="felhasznalok.php" style="display:flex;align-items:center;gap:10px;width:100%;text-decoration:none;color:inherit;">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">Felhasználók</span>
                </a>
            </li>
        <?php endif; ?>
       <!--későbbi fejlesztéshez (egyelőre nem meghatározott a leltározás módja)
        <?php if (function_exists('has_permission') && has_permission('FELHASZNALO_KEZEL')): ?>
            <li class="<?= isSidebarActive(['leltarak.php', 'uj_leltar.php', 'leltar_szerkesztes.php'], $currentPage) ?>">
                <a href="leltarak.php" style="display:flex;align-items:center;gap:10px;width:100%;text-decoration:none;color:inherit;">
                    <span class="nav-icon">📋</span>
                    <span class="nav-text">Leltár</span>
                </a>
            </li>
        <?php endif; ?>
        -->
            <li class="<?= isSidebarActive(['profil.php', 'jelszocsere.php'], $currentPage) ?>">
                <a href="profil.php" style="display:flex;align-items:center;gap:10px;width:100%;text-decoration:none;color:inherit;">
                    <span class="nav-icon">🙍</span>
                    <span class="nav-text">Profil</span>
                </a>
            </li>

        <?php if ($canViewProducts && $currentPage === 'termekek.php'): ?>
            <hr style="margin: 12px 0; opacity: 0.25;">

            <li data-filter="all" class="<?= ($currentPage === 'termekek.php') ? 'active' : '' ?>">
                <span class="nav-icon">☰</span>
                <span class="nav-text">Összes</span>
            </li>

            <?php foreach ($kategoriak as $kat): ?>
                <?php
                $nev = (string)$kat['kategoria'];
                $class = categoryToClass($nev); //helpers.php 3. sor
                ?>
                <li
                    data-filter="<?= htmlspecialchars($nev, ENT_QUOTES, 'UTF-8') ?>"
                    class="<?= htmlspecialchars($class, ENT_QUOTES, 'UTF-8') ?>"
                >
                    <span class="nav-icon">📁</span>
                    <span class="nav-text"><?= htmlspecialchars($nev, ENT_QUOTES, 'UTF-8') ?></span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>

    </ul>
</div>