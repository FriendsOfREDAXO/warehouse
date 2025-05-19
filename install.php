<?php

use FriendsOfRedaxo\Warehouse\Warehouse;

// Überprüfe aktuell installierte Version von Warehouse
/*
if (rex_version::compare($this->getVersion(), '2.0.0', '<')) {
    rex_view::error('Warehouse ' . $this->getVersion() .' ist bereits installiert. Ein Upgrade ist nicht vorgesehen und kann zu unvorhersehbaren Fehlern führen. Bitte deinstalliere Warehouse und installiere die aktuelle Version.');
    return;
}
*/

$this->includeFile(__DIR__ . '/install/update_scheme.php');

$addon = rex_addon::get('warehouse');
if (rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_settings_domain.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_article.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_article_variant.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_category.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_order.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_shipping.json'));
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tablesets/warehouse_country.json'));
    rex_yform_manager_table::deleteCache();
}
// $this->includeFile(__DIR__ . '/install/url_profile.php');

if(rex_config::get('store_name') == '') {
    rex_config::set('store_name', rex::getServerName());
}

if(rex_config::get('order_email') == '') {
    rex_config::set('order_email', rex::getErrorEmail());
}
