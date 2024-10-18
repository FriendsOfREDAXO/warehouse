<?php

$tables = [
    'warehouse_articles',
    'warehouse_article_variants',
    'warehouse_categories',
    'warehouse_attributes',
    'warehouse_attribute_values',
    'warehouse_attributegroups',
    'warehouse_orders'    
];

$sql = rex_sql::factory();

foreach ($tables as $table) {
    $sql->setQuery('DELETE FROM `'.rex::getTable('yform_table').'` WHERE table_name = "'.rex::getTable($table).'"');
    $sql->setQuery('DELETE FROM `'.rex::getTable('yform_field').'` WHERE table_name = "'.rex::getTable($table).'"');
    $sql->setQuery('DELETE FROM `'.rex::getTable('yform_history').'` WHERE table_name = "'.rex::getTable($table).'"');
    rex_sql_table::get(rex::getTable($table))->drop();
}
