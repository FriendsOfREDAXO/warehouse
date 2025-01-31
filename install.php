<?php

$addon = rex_addon::get('warehouse');
if (rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_article.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_article_variant.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_category.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_order.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_shipping.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_country.json'));
    rex_yform_manager_table::deleteCache();
}
