<?php
session_start();
require_once 'includes/db.php';

//Bejelentkezett felhasználó azonosítója
if (isset($_SESSION['user_id'])) {
    //Esemény naplózása a kijelentkezésről
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO naplo (felhasznalo_id, tipus, leiras) VALUES (:felhasznalo_id, :tipus, :leiras)'
        );
        //Naplórekord beszúrása
        $stmt->execute([
            ':felhasznalo_id' => (int)$_SESSION['user_id'],
            ':tipus' => 'felhasznalo_kijelentkezett',
            ':leiras' => ($_SESSION['username'] ?? 'Ismeretlen felhasználó') . ', Email: ' . ($_SESSION['email'] ?? '') . ' kijelentkezett.',
        ]);
    } catch (Throwable $e) {
    }
}

session_unset();
session_destroy();

header('Location: login.php');
exit;
