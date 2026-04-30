<?php
declare(strict_types=1); //Szigorú típusellenőrzés engedélyezése

//Kapcsolati adatok
$host = 'localhost';
$dbname = 'vrdb';
$username = 'root';
$password = '';

//Adatbázis kapcsolat létrehozása
try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    //Hibakezelés
} catch (PDOException $e) {
    http_response_code(500);
    exit('Adatbázis kapcsolódási hiba.');
}
