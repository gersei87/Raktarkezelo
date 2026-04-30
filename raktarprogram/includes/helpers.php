<!-- kategória szöveg tisztítás → CSS kompatibilis formára -->
<?php
function categoryToClass(?string $category): string
{
    if (!$category) return 'cat-egyeb';

    $cat = mb_strtolower($category, 'UTF-8');

    $cat = str_replace(
        ['á','é','í','ó','ö','ő','ú','ü','ű'],
        ['a','e','i','o','o','o','u','u','u'],
        $cat
    );

    $cat = preg_replace('/[^a-z0-9]+/', '-', $cat);

    return 'cat-' . trim($cat, '-');
}
