<?php

use Alexplusde\Tracks\Editor;

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
    rex_yform_manager_table::deleteCache();
}

if (rex_addon::get('url')->isAvailable()) {
    $this->includeFile(__DIR__ . '/install/url_profile.php');
}

if (rex_addon::get('mediapool')->isAvailable()) {
    $this->includeFile(__DIR__ . '/install/media.php');
}

if (rex_config::get('warehouse', 'store_name') == '') {
    rex_config::set('store_name', rex::getServerName());
}

if (rex_config::get('warehouse', 'order_email') == '') {
    rex_config::set('order_email', rex::getErrorEmail());
}

if (rex_config::get('warehouse', 'editor') == '') {
    $class = Editor::getFirstEditorProfile();
    rex_config::set('warehouse', 'editor', $class);
}

/* Initialisiere Struktur: Artikel, Kategorien, Domain-Profil */
$this->includeFile(__DIR__ . '/install/structure.php');


// Patch Addon YForm - kopiere uuid.php in die YForm Addon - wenn Version <= 5.0.0
// Sonst funktioniert das Klonen mit UUID nicht
// https://github.com/yakamara/yform/commit/df79eff090ad0460c655c2f852b17e6aec53987a
// https://github.com/yakamara/yform/pull/1517
if (rex_version::compare(rex_addon::get('yform')->getVersion(), '5.0.0', '<=')) {
    $yform = rex_addon::get('yform');
    $source = __DIR__ . '/install/patch/uuid.php';
    $target = $yform->getPath('lib/yForm/value/uuid.php');
    rex_file::copy($source, $target);
}
