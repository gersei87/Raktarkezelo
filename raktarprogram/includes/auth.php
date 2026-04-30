<?php
declare(strict_types=1); //Szigorú típusellenőrzés engedélyezése

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/*session_status()
Ez egy PHP függvény, ami megmondja a session állapotát:
PHP_SESSION_NONE → nincs még elindítva session
PHP_SESSION_ACTIVE → már fut a session
PHP_SESSION_DISABLED → session tiltva van
*/



//Be van-e jelentkezve
if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;
    }
}

//Biztosítja hogy legyen CSRF token
if (!function_exists('ensure_csrf_token')) {
    function ensure_csrf_token(): string
    {
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

//Generál egy kész HTML hidden inputot CSRF tokennel
if (!function_exists('csrf_input')) {
    function csrf_input(): string
    {
        $token = ensure_csrf_token();//saját fv.

        return '<input type="hidden" name="csrf_token" value="' .
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8') .
            '">';
    }
}

//Ellenőrzi a CSRF token érvényességét
if (!function_exists('validate_csrf_token')) {
    function validate_csrf_token($token): bool
    {
        if (!is_string($token) || $token === '') {
            return false;
        }

        $sessionToken = $_SESSION['csrf_token'] ?? null;

        if (!is_string($sessionToken) || $sessionToken === '') {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}

//Betölti a felhasználó adatait, jogosultságait és szerepkörét a sessionbe
if (!function_exists('load_user_auth')) {
    function load_user_auth(PDO $pdo): void
    {
        //Ha nincs bejelentkezve, akkor töröljük a jogosultságokat és szerepkört a sessionből
        if (!is_logged_in()) {
            $_SESSION['permissions'] = [];
            $_SESSION['user_role'] = null;

            if (!defined('SKIP_AUTH_CHECK')) {
                header('Location: login.php');
                exit;
            }

            return;
        }

        //Felhasználó ID kiolvasása a sessionből
        $userId = (int)$_SESSION['user_id'];

        //Felhasználó adatainak lekérése az adatbázisból
        $stmtUser = $pdo->prepare(
            'SELECT felhasznalo_id, felhasznalonev, email, aktiv
             FROM felhasznalo
             WHERE felhasznalo_id = :felhasznalo_id
             LIMIT 1'
        );
        $stmtUser->execute([':felhasznalo_id' => $userId]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        //Ha nem létezik vagy inaktív
        if (!$user || (int)$user['aktiv'] !== 1) {
            session_unset();
            session_destroy();

            if (!defined('SKIP_AUTH_CHECK')) {
                header('Location: login.php');
                exit;
            }

            return;
        }

        //Felhasználó adatainak tárolása a sessionben
        $_SESSION['username'] = $user['felhasznalonev'];
        $_SESSION['email'] = $user['email'];

        //Szerepkör és jogosultságok lekérése //szerepkör: admin, raktáros, olvasó
        $stmtRole = $pdo->prepare(
            'SELECT sz.szerepkor_id, sz.nev
             FROM felhasznalo_szerepkor fsz
             INNER JOIN szerepkor sz ON fsz.szerepkor_id = sz.szerepkor_id
             WHERE fsz.felhasznalo_id = :felhasznalo_id
             LIMIT 1'
        );
        $stmtRole->execute([':felhasznalo_id' => $userId]);
        $role = $stmtRole->fetch(PDO::FETCH_ASSOC);

        //Szerepkör és jogosultságok tárolása a sessionben
        $_SESSION['user_role'] = $role['nev'] ?? null;
        $_SESSION['role_id'] = isset($role['szerepkor_id']) ? (int)$role['szerepkor_id'] : null;
        //echo "Teszt:" . $_SESSION['role_id'];///kitörölni teszt
        //echo "<br>Teszt:" . $_SESSION['user_role'];///kitörölni teszt

        //Jogosultságok lekérése a szerepkör alapján
        $permissions = [];


        if (!empty($role['szerepkor_id'])) {
            $stmtPerm = $pdo->prepare(
                'SELECT j.kod  
                 FROM szerepkor_jogosultsag szj
                 INNER JOIN jogosultsag j ON szj.jogosultsag_id = j.jogosultsag_id
                 WHERE szj.szerepkor_id = :szerepkor_id'
            );
            //j.kod: TERMEK_OLVAS, TERMEK_KEZEL, stb...
            $stmtPerm->execute([':szerepkor_id' => (int)$role['szerepkor_id']]);

            $permissions = $stmtPerm->fetchAll(PDO::FETCH_COLUMN);
        }

        //Jogosultságok tárolása a sessionben
        $_SESSION['permissions'] = is_array($permissions) ? $permissions : [];
        
    }
}

//Ellenőrzi, hogy a felhasználó rendelkezik-e egy adott jogosultsággal
if (!function_exists('has_permission')) {
    function has_permission(string $permissionCode): bool
    {
        $permissions = $_SESSION['permissions'] ?? [];
        
        return is_array($permissions) && in_array($permissionCode, $permissions, true);
        //true paraméter: szigorú egyezés tipus és érték is
    }
}
if (!function_exists('debugPermissions')) {
    function debugPermissions() {  //csak a teszteléshez kell
    /* if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
            */

        echo "<pre>";

        if (!isset($_SESSION['permissions'])) {
            echo "Nincs beállítva a \$_SESSION['permissions']";
        } else {
            //print_r($_SESSION['permissions']); //Teszteléshez
            //echo $_SESSION['permissions']; //rossz
        }

        echo "</pre>";
    }
}

function show_Permissions(){

    foreach ($_SESSION['permissions'] as $perm) {  //csak a teszteléshez kell
    echo $perm . "<br>";
    }
}


