<?php
/*
rex_sql_table::get(rex::getTable('media'))
    ->removeColumn('ycom_auth_type')
    ->removeColumn('ycom_group_type')
    ->removeColumn('ycom_groups')
    ->alter();
*/

$tables = [
    'warehouse_countries',
    'warehouse_shipping'
];


$sql = rex_sql::factory();

foreach ($tables as $table) {
    $sql->setQuery('DELETE FROM `'.rex::getTable('yform_table').'` WHERE table_name = "'.rex::getTable($table).'"');
    $sql->setQuery('DELETE FROM `'.rex::getTable('yform_field').'` WHERE table_name = "'.rex::getTable($table).'"');
    $sql->setQuery('DELETE FROM `'.rex::getTable('yform_history').'` WHERE table_name = "'.rex::getTable($table).'"');
    rex_sql_table::get(rex::getTable($table))
        ->drop();
}

rex_yform_manager_table::deleteCache();
