<?php

/** @var rex_addon $this */

// Überprüfe aktuell installierte Version von Warehouse
/*
if (rex_version::compare($this->getVersion(), '2.0.0', '<')) {
    rex_view::error('Warehouse ' . $this->getVersion() .' ist bereits installiert. Ein Upgrade ist nicht vorgesehen und kann zu unvorhersehbaren Fehlern führen. Bitte deinstalliere Warehouse und installiere die aktuelle Version.');
    return;
}
*/

$addon = rex_addon::get('warehouse');
$addon->includeFile(__DIR__ . '/install/update_scheme.php');

if (rex_addon::get('yform')->isAvailable()) {
    $tableset1 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_settings_domain.json');
    if ($tableset1 !== null) {
        rex_yform_manager_table_api::importTablesets($tableset1);
    }
    
    $tableset2 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_article.json');
    if ($tableset2 !== null) {
        rex_yform_manager_table_api::importTablesets($tableset2);
    }
    
    $tableset3 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_article_variant.json');
    if ($tableset3 !== null) {
        rex_yform_manager_table_api::importTablesets($tableset3);
    }
    
    $tableset4 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_category.json');
    if ($tableset4 !== null) {
        rex_yform_manager_table_api::importTablesets($tableset4);
    }
    
    $tableset5 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_order.json');
    if ($tableset5 !== null) {
        rex_yform_manager_table_api::importTablesets($tableset5);
    }
    
    $tableset6 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_customer_address.json');
    if ($tableset6 !== null) {
        rex_yform_manager_table_api::importTablesets($tableset6);
    }
    /**
     * Helper function to load and import a tableset file.
     *
     * @param string $filename
     * @return void
     */
    function warehouse_import_tableset($filename)
    {
        $tableset = rex_file::get($filename);
        if ($tableset !== null) {
            rex_yform_manager_table_api::importTablesets($tableset);
        }
    }

    warehouse_import_tableset(__DIR__ . '/install/tablesets/warehouse_settings_domain.json');
    warehouse_import_tableset(__DIR__ . '/install/tablesets/warehouse_article.json');
    warehouse_import_tableset(__DIR__ . '/install/tablesets/warehouse_article_variant.json');
    warehouse_import_tableset(__DIR__ . '/install/tablesets/warehouse_category.json');
    warehouse_import_tableset(__DIR__ . '/install/tablesets/warehouse_order.json');
    warehouse_import_tableset(__DIR__ . '/install/tablesets/warehouse_customer_address.json');
    //    warehouse_import_tableset(__DIR__ . '/install/tablesets/warehouse_ycom_user.json');
    rex_yform_manager_table::deleteCache();
}

if (rex_addon::get('url')->isAvailable()) {
    // $addon->includeFile(__DIR__ . '/install/url/url_profile_article.php');
    // $addon->includeFile(__DIR__ . '/install/url/url_profile_category.php');
}

if (rex_addon::get('mediapool')->isAvailable()) {
    $addon->includeFile(__DIR__ . '/install/media.php');
}

if (rex_addon::get('ycom')->isAvailable() && rex_config::get('warehouse', 'ycom_mode') == '') {
    rex_config::set('warehouse', 'ycom_mode', 'choose');
}

if (rex_config::get('warehouse', 'store_name') == '') {
    rex_config::set('store_name', rex::getServerName());
}

if (rex_config::get('warehouse', 'order_email') == '') {
    rex_config::set('warehouse', 'order_email', rex::getErrorEmail());
}

if (rex_config::get('warehouse', 'editor') == '') {
    $tracksAddon = rex_addon::get('tracks');
    if ($tracksAddon->isAvailable()) {
        $class = Alexplusde\Tracks\Editor::getFirstEditorProfile();
        rex_config::set('warehouse', 'editor', $class);
    }
}

/* Initialisiere Struktur: Artikel, Kategorien, Domain-Profil */
$addon->includeFile(__DIR__ . '/install/structure.php');


// Wenn Warehouse 1.x installiert ist, dann abbrechen - es ist keine Migration vorgesehen.
if (rex_addon::get('warehouse')->isAvailable() && rex_version::compare(rex_addon::get('warehouse')->getVersion(), '2.0.0', '<')) {
    rex_view::error('Warehouse ' . rex_addon::get('warehouse')->getVersion() .' ist bereits installiert. Ein Upgrade ist nicht vorgesehen und kann zu unvorhersehbaren Fehlern führen. Bitte deinstalliere Warehouse und installiere die aktuelle Version.');
    return;
}
