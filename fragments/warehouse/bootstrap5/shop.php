<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Warehouse;

if (rex::isBackend()) {
    echo '<h2>Warehouse Kategorie- und Detailansicht</h2>';
    return;
} 

if(!rex_addon::get('warehouse')->isAvailable() || !rex_addon::get('url')->isAvailable()) {
    // Addon nicht installiert oder nicht aktiviert
    echo rex_view::error(rex_i18n::msg('warehouse.addon.misssing'));
    return;
}

$manager = Url\Url::resolveCurrent();
        
if ($manager) {
    $profile = $manager->getProfile();
    $dataset = $manager->getDataset();
    $dataset_id = (int) $manager->getDatasetId();

    if ($profile?->getTableName() == rex::getTable('warehouse_article')) {
        echo Warehouse::parse('article/details.php', [
            'article' => $dataset,
            'article_id' => $dataset_id
        ]);
    } elseif ($profile?->getTableName() == rex::getTable('warehouse_category')) {

        echo Warehouse::parse('article/list.php', [
            'category' => $dataset,
            'category_id' => $dataset_id
        ]);
    }
} else {
    echo Warehouse::parse('category/list.php');
}


echo Warehouse::parse('cart/offcanvas_cart.php');
