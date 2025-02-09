<?php

$content = '';

$all_modules = rex_sql::factory()->getArray('SELECT * FROM ' . rex::getTable('module'));
$all_etpls = rex_sql::factory()->getArray('SELECT * FROM ' . rex::getTable('yform_email_template'));

$modules_dir = scandir(rex_path::addon($this->getName(), 'install/modules/'));
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
        $input = rex_file::get(rex_path::addon($this->getName(), 'install/modules/' . $mod_to_install . '/input.php'));
        $output = rex_file::get(rex_path::addon($this->getName(), 'install/modules/' . $mod_to_install . '/output.php'));

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
            echo rex_view::success('Modul "' . $mod_to_install . '" wurde aktualisiert.');
        } else {
            $mi->setValue('name', $mod_to_install);
            $mi->setValue('key', rex_string::normalize($mod_to_install));
            $mi->insert();
            echo rex_view::success('Modul wurde angelegt unter "' . $mod_to_install . '"');
        }
        $all_modules = rex_sql::factory()->getArray('SELECT * FROM ' . rex::getTable('module'));
    }
}

if (rex_request('install_etpl')) {
    $tpl_key = rex_request('install_etpl');
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
        echo rex_view::success('E-Mail Template "' . $tpl_key . '" wurde aktualisiert.');
    } else {
        $mi->insert();
        echo rex_view::success('E-Mail Template "' . $tpl_key . '" wurde angelegt');
    }
    $all_etpls = rex_sql::factory()->getArray('SELECT * FROM ' . rex::getTable('yform_email_template'));
}

$content .= '<h2>Module intallieren oder aktualisieren</h2>';

foreach ($modules_dir as $mod_name) {
    $is_installed = false;
    foreach ($all_modules as $mod) {
        if (rex_string::normalize($mod_name) == $mod['key']) {
            $is_installed = true;
        }
    }
    $content .= '<p><a class="btn btn-primary ' . ($is_installed ? '' : 'btn-save') . '" href="index.php?page=' . $this->getName() . '/setup&amp;install_module=' . md5($mod_name) . '" class="rex-button">Modul ' . $mod_name . ' ' . ($is_installed ? 'aktualisieren' : 'installieren') . '</a></p>';
}

$content .= '<h2>E-Mail Templates intallieren</h2>';

foreach ($etpl_dir as $etpl_name) {
    $is_installed = false;
    foreach ($all_etpls as $tpl) {
        if ($etpl_name == $tpl['name']) {
            $is_installed = true;
        }
    }
    $content .= '<p><a class="btn btn-primary ' . ($is_installed ? '' : 'btn-save') . '" href="index.php?page=' . $this->getName() . '/setup&amp;install_etpl=' . $etpl_name . '" class="rex-button">Template ' . $etpl_name . ' ' . ($is_installed ? 'aktualisieren' : 'installieren') . '</a></p>';
}

$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('warehouse_setup'), false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
