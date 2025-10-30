<?php

$table_names = [
    'warehouse_article',
    'warehouse_article_variant',
    'warehouse_category',
    'warehouse_order',
    'warehouse_country',
    'warehouse_shipping'
];

if (rex_addon::get('yform')->isAvailable()) {
    foreach ($table_names as $table_name) {
        rex_yform_manager_table_api::removeTable($table_name);
    }
    rex_yform_manager_table::deleteCache();
}
