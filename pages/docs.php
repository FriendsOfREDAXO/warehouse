<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$mdFiles = ['00_README' => rex_addon::get('warehouse')->getPath('README.md')];
foreach (glob(rex_addon::get('warehouse')->getPath('docs') . '/*.md') ?: [] as $file) {
    $mdFiles[mb_substr(basename($file), 0, -3)] = $file;
}

$currentMDFile = rex_request('mdfile', 'string', '00_README');
if (!array_key_exists($currentMDFile, $mdFiles)) {
    $currentMDFile = '00_README';
}

$page = rex_be_controller::getPageObject('warehouse/docs');

if (null !== $page) {
    foreach ($mdFiles as $key => $mdFile) {
        $keyWithoudPrio = mb_substr($key, 3);
        $currentMDFileWithoutPrio = mb_substr($currentMDFile, 3);
        $page->addSubpage(
            (new rex_be_page($key, rex_i18n::msg('warehouse_docs.' . $keyWithoudPrio)))
            ->setSubPath($mdFile)
            ->setHref('index.php?page=warehouse/docs&mdfile=' . $key)
            ->setIsActive($key == $currentMDFile),
        );
    }
}

echo rex_view::title($this->i18n('warehouse.title'));

[$Toc, $Content] = rex_markdown::factory()->parseWithToc(rex_file::require($mdFiles[$currentMDFile]), 2, 3, [
    rex_markdown::SOFT_LINE_BREAKS => false,
    rex_markdown::HIGHLIGHT_PHP => true,
]);

$fragment = new rex_fragment();
$fragment->setVar('content', $Content, false);
$fragment->setVar('toc', $Toc, false);
$content = $fragment->parse('core/page/docs.php');

$fragment = new rex_fragment();
$fragment->setVar('title', '<div><span>'.rex_i18n::msg('package_help') . ' Warehouse </span><a href="https://github.com/FriendsOfREDAXO/warehouse/tree/main/docs" class="btn btn-primary btn-sm"><i class="fa fa-icon rex-icon fa-github"></i> Auf GitHub bearbeiten</a></div>', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
