<?php

use Alexplusde\Tracks\Media;

$files = [
    'warehouse_placeholder_article.jpeg' => 'Warehouse - Produktbild-Fallback',
    'warehouse_placeholder_category.jpeg' => 'Warehouse - Produktkategorie-Fallback',
];

foreach ($files as $filename => $title) {
    $path = __DIR__ . '/install/media/' . $filename;
    if (file_exists($path)) {
        Media::addImage($filename, $path, $title);
    }
}
