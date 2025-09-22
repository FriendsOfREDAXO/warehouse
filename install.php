<?php

use Alexplusde\Tracks\Editor;
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
    if (null !== $tableset1) {
        rex_yform_manager_table_api::importTablesets($tableset1);
    }

    $tableset2 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_article.json');
    if (null !== $tableset2) {
        rex_yform_manager_table_api::importTablesets($tableset2);
    }

    $tableset3 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_article_variant.json');
    if (null !== $tableset3) {
        rex_yform_manager_table_api::importTablesets($tableset3);
    }

    $tableset4 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_category.json');
    if (null !== $tableset4) {
        rex_yform_manager_table_api::importTablesets($tableset4);
    }

    $tableset5 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_order.json');
    if (null !== $tableset5) {
        rex_yform_manager_table_api::importTablesets($tableset5);
    }

    $tableset6 = rex_file::get(__DIR__ . '/install/tablesets/warehouse_customer_address.json');
    if (null !== $tableset6) {
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
        if (null !== $tableset) {
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

// Media Manager Profile creation for warehouse
if (rex_addon::get('media_manager')->isAvailable()) {
    $profiles = [
        'warehouse_category' => [
            'description' => 'Warehouse - Kategorie-Bilder',
            'effect_parameters' => '{"rex_effect_resize":{"rex_effect_resize_width":"300","rex_effect_resize_height":"300","rex_effect_resize_style":"maximum","rex_effect_resize_allow_enlarge":"not_enlarge"}}',
        ],
        'warehouse_article' => [
            'description' => 'Warehouse - Artikel-Bilder',
            'effect_parameters' => '{"rex_effect_resize":{"rex_effect_resize_width":"800","rex_effect_resize_height":"600","rex_effect_resize_style":"maximum","rex_effect_resize_allow_enlarge":"not_enlarge"}}',
        ],
        'warehouse_article_preview' => [
            'description' => 'Warehouse - Artikel-Vorschaubilder',
            'effect_parameters' => '{"rex_effect_resize":{"rex_effect_resize_width":"400","rex_effect_resize_height":"300","rex_effect_resize_style":"maximum","rex_effect_resize_allow_enlarge":"not_enlarge"}}',
        ],
    ];

    foreach ($profiles as $profile_name => $profile_config) {
        // Check if profile already exists
        $media_manager_type = rex_sql::factory()->getArray('SELECT `name` FROM ' . rex::getTable('media_manager_type') . ' WHERE `name` = :name', [':name' => $profile_name]);
        $profile_exists = false;
        foreach ($media_manager_type as $profile) {
            if ($profile['name'] === $profile_name) {
                $profile_exists = true;
                break;
            }
    // Fetch all existing profile names in one query
    $existing_profiles = rex_sql::factory()->getArray('SELECT `name` FROM ' . rex::getTable('media_manager_type'));
    $existing_profile_names = array_column($existing_profiles, 'name');

    foreach ($profiles as $profile_name => $profile_config) {
        // Check if profile already exists
        $profile_exists = in_array($profile_name, $existing_profile_names, true);

        if (!$profile_exists) {
            // Create media manager type (profile)
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable('media_manager_type'));
            $sql->setValue('name', $profile_name);
            $sql->setValue('status', 1);
            $sql->setValue('description', $profile_config['description']);
            $sql->setValue('createdate', date('Y-m-d H:i:s'));
            $sql->setValue('createuser', 'warehouse');
            $sql->setValue('updatedate', date('Y-m-d H:i:s'));
            $sql->setValue('updateuser', 'warehouse');
            $sql->insert();

            // Get the created profile ID
            $media_manager_type_id = rex_sql::factory()->getArray('SELECT `id` FROM ' . rex::getTable('media_manager_type') . ' WHERE `name` = :name', [':name' => $profile_name]);
            $profile_id = $media_manager_type_id[0]['id'];

            // Add resize effect to the profile
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable('media_manager_type_effect'));
            $sql->setValue('type_id', $profile_id);
            $sql->setValue('effect', 'resize');
            $sql->setValue('parameters', $profile_config['effect_parameters']);
            $sql->setValue('priority', 1);
            $sql->setValue('createdate', date('Y-m-d H:i:s'));
            $sql->setValue('createuser', 'warehouse');
            $sql->setValue('updatedate', date('Y-m-d H:i:s'));
            $sql->setValue('updateuser', 'warehouse');
            $sql->insert();
        }
    }
}

if (rex_addon::get('ycom')->isAvailable() && '' == rex_config::get('warehouse', 'ycom_mode')) {
    rex_config::set('warehouse', 'ycom_mode', 'choose');
}

if ('' == rex_config::get('warehouse', 'store_name')) {
    rex_config::set('store_name', rex::getServerName());
}

if ('' == rex_config::get('warehouse', 'order_email')) {
    rex_config::set('warehouse', 'order_email', rex::getErrorEmail());
}

if ('' == rex_config::get('warehouse', 'editor')) {
    $tracksAddon = rex_addon::get('tracks');
    if ($tracksAddon->isAvailable()) {
        $class = Editor::getFirstEditorProfile();
        rex_config::set('warehouse', 'editor', $class);
    }
}

/* Initialisiere Struktur: Artikel, Kategorien, Domain-Profil */
$addon->includeFile(__DIR__ . '/install/structure.php');

// Wenn Warehouse 1.x installiert ist, dann abbrechen - es ist keine Migration vorgesehen.
if (rex_addon::get('warehouse')->isAvailable() && rex_version::compare(rex_addon::get('warehouse')->getVersion(), '2.0.0', '<')) {
    rex_view::error('Warehouse ' . rex_addon::get('warehouse')->getVersion() . ' ist bereits installiert. Ein Upgrade ist nicht vorgesehen und kann zu unvorhersehbaren Fehlern führen. Bitte deinstalliere Warehouse und installiere die aktuelle Version.');
    return;
}
