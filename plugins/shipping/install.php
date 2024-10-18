<?php

$tablesets = [
    'warehouse_countries.json',
    'warehouse_shipping.json'
];

rex_yform_manager_table::deleteCache();

/** @var rex_addon $this */

foreach ($tablesets as $tableset) {
    $content = rex_file::get(rex_path::plugin('warehouse', 'shipping', 'install/tablesets/'.$tableset));
    rex_yform_manager_table_api::importTablesets($content);
}

rex_delete_cache();
rex_yform_manager_table::deleteCache();

        