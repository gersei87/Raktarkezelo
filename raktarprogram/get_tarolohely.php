<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$raktar_id = $_GET['raktar_id'] ?? null;
$termek_id = $_GET['termek_id'] ?? null;
$tipus = $_GET['tipus'] ?? 'bevetel';

if (!$raktar_id) {
    echo json_encode([]);
    exit;
}

try {

    // KIADÁS → SZŰRT
    if ($tipus === "kiadas" && $termek_id) {

        $stmt = $pdo->prepare("
            SELECT DISTINCT th.tarolohely_id, th.kod
            FROM raktarkeszlet rk
            INNER JOIN tarolohely th ON rk.tarolohely_id = th.tarolohely_id
            WHERE rk.raktar_id = ?
              AND rk.termek_id = ?
              AND rk.mennyiseg > 0
        ");

        $stmt->execute([$raktar_id, $termek_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // BEVÉTEL → MINDEN
    $stmt = $pdo->prepare("
        SELECT tarolohely_id, kod
        FROM tarolohely
        WHERE raktar_id = ?
    ");

    $stmt->execute([$raktar_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}