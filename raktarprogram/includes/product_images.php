<?php
declare(strict_types=1); //Szigorú típusellenőrzés engedélyezése

//Szerver oldali fájlkezelés a termékek képeihez
function product_image_upload_dir(): string
{
    return __DIR__ . '/../uploads/termekek';
}

// Biztosítja, hogy a termék képeinek könyvtára létező legyen
function ensure_product_image_dir_exists(): void
{
    $dir = product_image_upload_dir();

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

//Webes elérési útvonal
function product_image_web_path(int $termekId): string
{
    $dir = product_image_upload_dir();
    $base = $dir . '/' . $termekId;

    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        $file = $base . '.' . $ext;
        if (file_exists($file)) {
            return 'uploads/termekek/' . $termekId . '.' . $ext;
        }
    }

    return 'assets/images/no-image.png';
}

// Termék képének mentése feltöltött fájlból
function save_uploaded_product_image(array $file, int $termekId): void
{
    if (
        !isset($file['error'], $file['tmp_name']) ||
        (int)$file['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return;
    }

    if ((int)$file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('A képfeltöltés sikertelen.');
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Érvénytelen feltöltött fájl.');
    }

    $mime = mime_content_type($file['tmp_name']);

    // Engedélyezett MIME típusok és kiterjesztések
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    // MIME típus ellenőrzése
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Csak JPG, PNG vagy WEBP kép tölthető fel.');
    }

    ensure_product_image_dir_exists();

    delete_product_image_if_exists($termekId);

    $ext = $allowed[$mime];
    $target = product_image_upload_dir() . '/' . $termekId . '.' . $ext;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('A kép mentése sikertelen.');
    }
}

// Létező termék képének törlése
function delete_product_image_if_exists(int $termekId): void
{
    $basePath = product_image_upload_dir() . '/' . $termekId;

    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        $file = $basePath . '.' . $ext;

        if (file_exists($file)) {
            @unlink($file);
        }
    }
}