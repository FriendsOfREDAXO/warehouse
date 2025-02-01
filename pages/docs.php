<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$mdFiles = [];
foreach (glob(rex_addon::get('warehouse')->getPath('docs') . '/*.md') ?: [] as $file) {
    $mdFiles[mb_substr(basename($file), 0, -3)] = $file;
}

$currentMDFile = rex_request('mdfile', 'string', '01_intro');
if (!array_key_exists($currentMDFile, $mdFiles)) {
    $currentMDFile = '01_intro';
}

$page = rex_be_controller::getPageObject('warehouse/docs');

if (null !== $page) {
    foreach ($mdFiles as $key => $mdFile) {
        $keyWithoudPrio = mb_substr($key, 3);
        $currentMDFileWithoudPrio = mb_substr($currentMDFile, 3);
        $page->addSubpage(
            (new rex_be_page($key, rex_i18n::msg('warehouse_docs_' . $keyWithoudPrio)))
            ->setSubPath($mdFile)
            ->setHref('index.php?page=warehouse/docs&mdfile=' . $key)
            ->setIsActive($key == $currentMDFile),
        );
    }
}

echo rex_view::title($this->i18n('warehouse_title'));

[$Toc, $Content] = rex_markdown::factory()->parseWithToc(rex_file::require($mdFiles[$currentMDFile]), 2, 3, [
    rex_markdown::SOFT_LINE_BREAKS => false,
    rex_markdown::HIGHLIGHT_PHP => true,
]);

$fragment = new rex_fragment();
$fragment->setVar('content', $Content, false);
$fragment->setVar('toc', $Toc, false);
$content = $fragment->parse('core/page/docs.php');

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('package_help') . ' ', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
