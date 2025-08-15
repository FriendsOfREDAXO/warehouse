<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Domain;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$func = rex_request('func', 'string');
$csrf = rex_csrf_token::factory('warehouse_setup');

// Handle setup actions
if ('' !== $func) {
    if (!$csrf->isValid()) {
        echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    } else {
        switch ($func) {
            case 'repair_tables':
                try {
                    // Execute update scheme
                    $this->includeFile(__DIR__ . '/../install/update_scheme.php');
                    
                    // Import tablesets if YForm is available
                    if (rex_addon::get('yform')->isAvailable()) {
                        $tableset1 = rex_file::get(__DIR__ . '/../install/tablesets/warehouse_settings_domain.json');
                        if ($tableset1 !== null) {
                            rex_yform_manager_table_api::importTablesets($tableset1);
                        }
                        
                        $tableset2 = rex_file::get(__DIR__ . '/../install/tablesets/warehouse_article.json');
                        if ($tableset2 !== null) {
                            rex_yform_manager_table_api::importTablesets($tableset2);
                        }
                        
                        $tableset3 = rex_file::get(__DIR__ . '/../install/tablesets/warehouse_article_variant.json');
                        if ($tableset3 !== null) {
                            rex_yform_manager_table_api::importTablesets($tableset3);
                        }
                        
                        $tableset4 = rex_file::get(__DIR__ . '/../install/tablesets/warehouse_category.json');
                        if ($tableset4 !== null) {
                            rex_yform_manager_table_api::importTablesets($tableset4);
                        }
                        
                        $tableset5 = rex_file::get(__DIR__ . '/../install/tablesets/warehouse_order.json');
                        if ($tableset5 !== null) {
                            rex_yform_manager_table_api::importTablesets($tableset5);
                        }
                        
                        $tableset6 = rex_file::get(__DIR__ . '/../install/tablesets/warehouse_customer_address.json');
                        if ($tableset6 !== null) {
                            rex_yform_manager_table_api::importTablesets($tableset6);
                        }
                        
                        rex_yform_manager_table::deleteCache();
                    }
                    echo rex_view::success($addon->i18n('warehouse.setup.success_tables_repaired'));
                } catch (Exception $e) {
                    echo rex_view::error($addon->i18n('warehouse.setup.error_repair_tables', $e->getMessage()));
                }
                break;

            case 'install_structure':
                try {
                    // Check if domain profile already exists
                    rex_yform_manager_dataset::setModelClass('rex_warehouse_settings_domain', Domain::class);
                    if (Domain::query()->findOne() !== null) {
                        echo rex_view::error($addon->i18n('warehouse.setup.error_domain_exists'));
                    } else {
                        $this->includeFile(__DIR__ . '/../install/structure.php');
                        echo rex_view::success($addon->i18n('warehouse.setup.success_structure_installed'));
                    }
                } catch (Exception $e) {
                    echo rex_view::error($addon->i18n('warehouse.setup.error_install_structure', $e->getMessage()));
                }
                break;

            case 'install_url_profiles':
                try {
                    if (rex_addon::get('url')->isAvailable()) {
                        $this->includeFile(__DIR__ . '/../install/url/url_profile_article.php');
                        $this->includeFile(__DIR__ . '/../install/url/url_profile_category.php');
                        $this->includeFile(__DIR__ . '/../install/url/url_profile_order.php');
                        echo rex_view::success($addon->i18n('warehouse.setup.success_url_profiles_installed'));
                    } else {
                        echo rex_view::error($addon->i18n('warehouse.setup.error_url_addon_unavailable'));
                    }
                } catch (Exception $e) {
                    echo rex_view::error($addon->i18n('warehouse.setup.error_install_url_profiles', $e->getMessage()));
                }
                break;

            case 'import_demo':
                try {
                    include_once __DIR__ . '/../install/demo/demo.php';
                    echo rex_view::success($addon->i18n('warehouse.setup.success_demo_imported'));
                } catch (Exception $e) {
                    echo rex_view::error($addon->i18n('warehouse.setup.error_import_demo', $e->getMessage()));
                }
                break;

            case 'reset_shop':
                try {
                    // Clear warehouse tables (keep structure but remove data)
                    $tables_to_clear = [
                        'warehouse_article',
                        'warehouse_article_variant', 
                        'warehouse_category',
                        'warehouse_order',
                        'warehouse_customer_address'
                    ];
                    
                    $sql = rex_sql::factory();
                    foreach ($tables_to_clear as $table) {
                        $sql->setQuery('TRUNCATE TABLE ' . rex::getTable($table));
                    }
                    
                    // Also clear domain profile
                    $sql = rex_sql::factory();
                    $sql->setQuery('TRUNCATE TABLE ' . rex::getTable('warehouse_settings_domain'));
                    
                    echo rex_view::success($addon->i18n('warehouse.setup.success_shop_reset'));
                } catch (Exception $e) {
                    echo rex_view::error($addon->i18n('warehouse.setup.error_reset_shop', $e->getMessage()));
                }
                break;
        }
    }
}

$all_modules = rex_sql::factory()->getArray('SELECT * FROM ' . rex::getTable('module'));
$all_etpls = rex_sql::factory()->getArray('SELECT * FROM ' . rex::getTable('yform_email_template'));

$modules_dir = scandir(rex_path::addon($this->getName(), 'install/module/'));
foreach ($modules_dir as $k => $v) {
    if (in_array($v, ['.', '..'])) {
        unset($modules_dir[$k]);
    }
}

$etpl_dir = scandir(rex_path::addon($this->getName(), 'install/yform_email/'));
foreach ($etpl_dir as $k => $v) {
    if (in_array($v, ['.', '..'])) {
        unset($etpl_dir[$k]);
    }
}


if (rex_request('install_module')) {
    $mod_to_install = '';
    foreach ($modules_dir as $mod_name) {
        if (md5($mod_name) == rex_request('install_module')) {
            $mod_to_install = $mod_name;
            break;
        }
    }

    if ($mod_to_install) {
        $input = rex_file::get(rex_path::addon($this->getName(), 'install/module/' . $mod_to_install . '/input.php'));
        $output = rex_file::get(rex_path::addon($this->getName(), 'install/module/' . $mod_to_install . '/output.php'));

        $is_installed = false;
        foreach ($all_modules as $mod) {
            if (rex_string::normalize($mod_to_install) == $mod['key']) {
                $is_installed = true;
            }
        }

        $mi = rex_sql::factory();
        $mi->setTable(rex::getTable("module"));
        $mi->setValue('input', $input);
        $mi->setValue('output', $output);
        if ($is_installed) {
            $mi->setWhere('`key`=:key', ['key' => rex_string::normalize($mod_to_install)]);
            $mi->update();
            echo rex_view::success($addon->i18n('warehouse.setup.module_updated', $mod_to_install));
        } else {
            $mi->setValue('name', $mod_to_install);
            $mi->setValue('key', rex_string::normalize($mod_to_install));
            $mi->insert();
            echo rex_view::success($addon->i18n('warehouse.setup.module_created', $mod_to_install));
        }
        $all_modules = rex_sql::factory()->getArray('SELECT * FROM ' . rex::getTable('module'));
    }
}

if (rex_request('install_yform_email')) {
    $tpl_key = rex_request('install_yform_email');
    $body = rex_file::get(rex_path::addon($this->getName(), 'install/yform_email/' . $tpl_key . '/body.php'));
    $body_html = rex_file::get(rex_path::addon($this->getName(), 'install/yform_email/' . $tpl_key . '/body_html.php'));
    $meta = rex_string::yamlDecode(rex_file::get(rex_path::addon($this->getName(), 'install/yform_email/' . $tpl_key . '/metadata.yml')));
    $meta['name'] = $tpl_key;

    $mi = rex_sql::factory();
    $mi->setTable(rex::getTable("yform_email_template"));
    $mi->setValue('body', $body);
    $mi->setValue('body_html', $body_html);
    $mi->setValues($meta);

    $is_installed = false;
    foreach ($all_etpls as $tpl) {
        if ($tpl_key == $tpl['name']) {
            $is_installed = true;
        }
    }

    if ($is_installed) {
        $mi->setWhere('`name`=:name', ['name' => $tpl_key]);
        $mi->update();
        echo rex_view::success($addon->i18n('warehouse.setup.email_template_updated', $tpl_key));
    } else {
        $mi->insert();
        echo rex_view::success($addon->i18n('warehouse.setup.email_template_created', $tpl_key));
    }
    $all_etpls = rex_sql::factory()->getArray('SELECT * FROM ' . rex::getTable('yform_email_template'));
}

// Section 1: Repair Tables
$content = '';
$content .= '<p>' . $addon->i18n('warehouse.setup.repair_tables_info') . '</p>';
$content .= '<p><a class="btn btn-danger" href="' . rex_url::currentBackendPage(['func' => 'repair_tables'] + $csrf->getUrlParams()) . '" data-confirm="' . $addon->i18n('warehouse.setup.repair_tables_confirm') . '">' . $addon->i18n('warehouse.setup.repair_tables_button') . '</a></p>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'danger', false);
$fragment->setVar('title', $addon->i18n('warehouse.setup.repair_tables'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Section 2: Install Structure
$content = '';
$content .= '<p>' . $addon->i18n('warehouse.setup.install_structure_info') . '</p>';
$content .= '<p><a class="btn btn-warning" href="' . rex_url::currentBackendPage(['func' => 'install_structure'] + $csrf->getUrlParams()) . '" data-confirm="' . $addon->i18n('warehouse.setup.install_structure_confirm') . '">' . $addon->i18n('warehouse.setup.install_structure_button') . '</a></p>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'warning', false);
$fragment->setVar('title', $addon->i18n('warehouse.setup.install_structure'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Section 3: Install URL Profiles
if (rex_addon::get('url')->isAvailable()) {
    $content = '';
    $content .= '<p>' . $addon->i18n('warehouse.setup.install_url_profiles_info') . '</p>';
    $content .= '<p><a class="btn btn-primary" href="' . rex_url::currentBackendPage(['func' => 'install_url_profiles'] + $csrf->getUrlParams()) . '" data-confirm="' . $addon->i18n('warehouse.setup.install_url_profiles_confirm') . '">' . $addon->i18n('warehouse.setup.install_url_profiles_button') . '</a></p>';

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'info', false);
    $fragment->setVar('title', $addon->i18n('warehouse.setup.install_url_profiles'), false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
}

// Section 4: Import Demo Data
$content = '';
$content .= '<p>' . $addon->i18n('warehouse.setup.import_demo_info') . '</p>';
$content .= '<p><a class="btn btn-success" href="' . rex_url::currentBackendPage(['func' => 'import_demo'] + $csrf->getUrlParams()) . '" data-confirm="' . $addon->i18n('warehouse.setup.import_demo_confirm') . '">' . $addon->i18n('warehouse.setup.import_demo_button') . '</a></p>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'success', false);
$fragment->setVar('title', $addon->i18n('warehouse.setup.import_demo'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Section 5: Reset Shop
$content = '';
$content .= '<p>' . $addon->i18n('warehouse.setup.reset_shop_info') . '</p>';
$content .= '<p><a class="btn btn-danger" href="' . rex_url::currentBackendPage(['func' => 'reset_shop'] + $csrf->getUrlParams()) . '" data-confirm="' . $addon->i18n('warehouse.setup.reset_shop_confirm') . '">' . $addon->i18n('warehouse.setup.reset_shop_button') . '</a></p>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'danger', false);
$fragment->setVar('title', $addon->i18n('warehouse.setup.reset_shop'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Section 6: Module Installation (existing functionality)
$content = '';
$content .= '<p>' . $addon->i18n('warehouse.setup.install_module_info') . '</p>';

foreach ($modules_dir as $mod_name) {
    $is_installed = false;
    foreach ($all_modules as $mod) {
        if (rex_string::normalize($mod_name) == $mod['key']) {
            $is_installed = true;
        }
    }
    $content .= '<p><a class="btn btn-primary ' . ($is_installed ? '' : 'btn-save') . '" href="index.php?page=' . $this->getName() . '/setup&amp;install_module=' . md5($mod_name) . '" class="rex-button">' . $addon->i18n('warehouse.setup.module_button', $mod_name, $addon->i18n($is_installed ? 'warehouse.setup.module_update' : 'warehouse.setup.module_install')) . '</a></p>';
}

$fragment = new rex_fragment();
$fragment->setVar('class', 'default', false);
$fragment->setVar('title', $addon->i18n('warehouse.setup.install_module'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Section 7: E-Mail Templates (existing functionality)
$content = '';
$content .= '<h4>' . $addon->i18n('warehouse.setup.install_email_templates') . '</h4>';

foreach ($etpl_dir as $etpl_name) {
    $is_installed = false;
    foreach ($all_etpls as $tpl) {
        if ($etpl_name == $tpl['name']) {
            $is_installed = true;
        }
    }
    $content .= '<p><a class="btn btn-primary ' . ($is_installed ? '' : 'btn-save') . '" href="index.php?page=' . $this->getName() . '/setup&amp;install_yform_email=' . $etpl_name . '" class="rex-button">' . $addon->i18n('warehouse.setup.email_template_button', $etpl_name, $addon->i18n($is_installed ? 'warehouse.setup.module_update' : 'warehouse.setup.module_install')) . '</a></p>';
}

$fragment = new rex_fragment();
$fragment->setVar('class', 'default', false);
$fragment->setVar('title', $addon->i18n('warehouse.setup.install_email_templates'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
