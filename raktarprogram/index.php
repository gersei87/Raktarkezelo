<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    /*
    Érték	            Jelentés
    PHP_SESSION_NONE	nincs session
    PHP_SESSION_ACTIVE	fut
    */

    //Ne ellenőrizzen jogosultságot
    define('SKIP_AUTH_CHECK', true);

    /*define() Beépített PHP függvény Konstanst hoz létre.*/
    /*SKIP_AUTH_CHECK    Saját konstans */

    require_once 'includes/auth.php';
    require_once 'includes/db.php';
    /*require_once  PHP nyelvi elem Betölt egy fájlt egyszer. Ha nem találja → hiba*/



    //Bejelentkezés és jogosultság ellenőrzése
    load_user_auth($pdo); //saját fv.  auth.php 67. sor

    if (!is_logged_in()) { //saját fv.  auth.php 18. sor
        header('Location: login.php');
        exit;
    }

    //Jogosultság előkészítése
    $canViewProducts = has_permission('TERMEK_OLVAS');  //has_permission saját fv.  includes/auth.php 155. sor
    $canManageProducts = has_permission('TERMEK_KEZEL'); //true vagy false
    $canViewStock = has_permission('RAKTARKESZLET_OLVAS');
    $canManageStock = has_permission('RAKTARKESZLET_KEZEL');
    //debugPermissions();//tesztelés
    //show_Permissions();//tesztelés

    include 'includes/header.php';
    include 'includes/navbar.php';

    $totalProducts = 0;  
    $totalStock = 0;
    $totalWarehouses = 0;
    $totalSuppliers = 0;
    $lowStockProducts = [];   //Kevés készletű termékek
    $latestStocks = [];

    if ($canViewProducts) {
        //Összes termék
        $totalProducts = (int)$pdo->query(
            "SELECT COUNT(*) FROM termekek"
        )->fetchColumn();

        //Összes beszállító
        $totalSuppliers = (int)$pdo->query(
            "SELECT COUNT(*) FROM beszallitok"
        )->fetchColumn();

        //Kevés készletű termékek
        $lowStmt = $pdo->query(
            "SELECT
                t.termek_id,
                t.termeknev,
                t.cikkszam,
                COALESCE(SUM(rk.mennyiseg), 0) AS osszes_mennyiseg
            FROM termekek t
            LEFT JOIN raktarkeszlet rk ON t.termek_id = rk.termek_id
            GROUP BY t.termek_id, t.termeknev, t.cikkszam
            HAVING COALESCE(SUM(rk.mennyiseg), 0) <= 5
            ORDER BY osszes_mennyiseg ASC, t.termeknev ASC
            LIMIT 6"
        );
        //
        $lowStockProducts = $lowStmt->fetchAll();
    }
    //jegyzet: COALESCE(SUM(rk.mennyiseg), 0) AS osszes_mennyiseg (ha NULL, akkor 0)

    if ($canViewStock) { //ha van jogosultság, akkor
        $totalStock = (int)$pdo->query(
            "SELECT COALESCE(SUM(mennyiseg), 0) FROM raktarkeszlet"
        )->fetchColumn();

        $totalWarehouses = (int)$pdo->query( 
            "SELECT COUNT(*) FROM raktar"
        )->fetchColumn();

        //Legutóbbi készletbejegyzések
        $latestStmt = $pdo->query(
            "SELECT
                rk.raktarkeszlet_id,
                rk.mennyiseg,
                rk.allapot,
                rk.beszerzes_datuma,
                t.termeknev,
                r.nev AS raktar_nev,
                th.kod AS tarolohely_kod
            FROM raktarkeszlet rk
            INNER JOIN termekek t ON rk.termek_id = t.termek_id
            INNER JOIN raktar r ON rk.raktar_id = r.raktar_id
            INNER JOIN tarolohely th ON rk.tarolohely_id = th.tarolohely_id
            ORDER BY rk.raktarkeszlet_id DESC
            LIMIT 6"
        );
        $latestStocks = $latestStmt->fetchAll();
    }

    function dashboardStateLabel(string $allapot): string
    {
        switch ($allapot) {
            case 'uj':
                return 'Új';
            case 'hasznalt':
                return 'Használt';
            case 'hibas':
                return 'Hibás';
            case 'selejt':
                return 'Selejt';
            default:
                return $allapot;
        }
    }
?>

<div class="main-layout">
   <?php include 'includes/sidebar.php'; ?><!--oldalsáv-->

    <main class="content">
        <div class="dashboard-grid">

            <?php if ($canViewProducts): ?>
                <div class="dashboard-card stat-card">
                    <h3>Összes termék</h3>
                    <p class="stat-number"><?= $totalProducts ?></p>
                </div>
            <?php endif; ?>

            <?php if ($canViewStock): ?>
                <div class="dashboard-card stat-card">
                    <h3>Összes készlet</h3>
                    <p class="stat-number"><?= $totalStock ?></p>
                </div>
            <?php endif; ?>

            <?php if ($canViewStock): ?>
                <div class="dashboard-card stat-card">
                    <h3>Raktárak</h3>
                    <p class="stat-number"><?= $totalWarehouses ?></p>
                </div>
            <?php endif; ?>

            <?php if ($canViewProducts): ?>
                <div class="dashboard-card stat-card">
                    <h3>Beszállítók</h3>
                    <p class="stat-number"><?= $totalSuppliers ?></p>
                </div>
            <?php endif; ?>

            <div class="dashboard-card quick-card">
                <h3>Gyors műveletek</h3>
                <div class="btn-row">
                    <?php if ($canManageProducts): ?>
                        <a href="uj_termek.php" class="btn">Új termék</a>
                    <?php endif; ?>

                    <?php if ($canManageStock): ?>
                        <a href="uj_keszlet.php" class="btn">Új készlet</a>
                    <?php endif; ?>

                    <?php if ($canManageStock): ?>
                        <a href="uj_keszlet.php" class="btn">Árukiadás</a>
                    <?php endif; ?>

                    <?php if ($canViewProducts): ?>
                        <a href="termekek.php" class="btn cancel">Termékek</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($canViewProducts): ?>
                <div class="dashboard-card list-card">
                    <h3>Kevés készletű termékek</h3>

                    <?php if (empty($lowStockProducts)): ?>
                        <p>Nincs alacsony készletű termék.</p>
                    <?php else: ?>
                        <div class="dashboard-list">
                            <?php foreach ($lowStockProducts as $item): ?>
                                <a class="dashboard-list-item" href="termek_reszletek.php?id=<?= (int)$item['termek_id'] ?>">
                                    <div>
                                        <strong><?= htmlspecialchars($item['termeknev'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                                        <span><?= htmlspecialchars($item['cikkszam'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <span class="dashboard-badge"><?= (int)$item['osszes_mennyiseg'] ?> db</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($canViewStock): ?>
                <div class="dashboard-card list-card">
                    <h3>Legutóbbi készletbejegyzések</h3>

                    <?php if (empty($latestStocks)): ?>
                        <p>Nincs még készletbejegyzés.</p>
                    <?php else: ?>
                        <div class="dashboard-list">
                            <?php foreach ($latestStocks as $stock): ?>
                                <div class="dashboard-list-item static">
                                    <div>
                                        <strong><?= htmlspecialchars($stock['termeknev'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                                        <span>
                                            <?= htmlspecialchars($stock['raktar_nev'], ENT_QUOTES, 'UTF-8') ?>
                                            /
                                            <?= htmlspecialchars($stock['tarolohely_kod'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                    <span class="dashboard-badge">
                                        <?= (int)$stock['mennyiseg'] ?> db · <?= htmlspecialchars(dashboardStateLabel($stock['allapot']), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>