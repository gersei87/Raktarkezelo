<?php
//Tárolóhelyek lekérése AJAX híváshoz
require_once '../includes/db.php';

// Raktár ID beolvasása
$raktar_id = (int)($_GET['raktar_id'] ?? 0);

//SQL lekérdezés előkészítése
$stmt = $pdo->prepare(
    'SELECT tarolohely_id, kod
     FROM tarolohely
     WHERE raktar_id = ?
     ORDER BY kod ASC'
);

//Végrehajtás
$stmt->execute([$raktar_id]);

// Eredmény visszaküldése JSON formátumban
header('Content-Type: application/json; charset=utf-8');
echo json_encode($stmt->fetchAll());