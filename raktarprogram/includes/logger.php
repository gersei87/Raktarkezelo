<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function log_action(PDO $pdo, string $tipus, string $leiras, ?int $felhasznaloId = null): void
{
    $userId = $felhasznaloId ?? (int)($_SESSION['user_id'] ?? 0);

    $stmt = $pdo->prepare(
        'INSERT INTO naplo (felhasznalo_id, tipus, leiras)
         VALUES (:felhasznalo_id, :tipus, :leiras)'
    );

    $stmt->execute([
        ':felhasznalo_id' => $userId > 0 ? $userId : null,
        ':tipus' => $tipus,
        ':leiras' => $leiras,
    ]);
}
//Esemény naplózás az adatbázisba a felhasználó azonosítójával, eseménytípusával és leírásával.