<?php

$lang = 'de_de';

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$content = rex_file::get(rex_path::addon('warehouse', 'doc/'.$lang.'/readme.md'));
if ($content == '') {
    $content = '<p class="alert alert-warning">'.rex_i18n::rawMsg('yform_docs_filenotfound').'</p>';
}

if (class_exists('rex_markdown')) {
    $md = rex_markdown::factory();
    $content = $md->parse($content);
}


$fragment = new rex_fragment();
$fragment->setVar('title', 'Warehouse - Dokumentation', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
