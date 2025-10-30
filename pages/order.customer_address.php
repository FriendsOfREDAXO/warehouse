<?php
use FriendsOfRedaxo\Warehouse\Search;

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$table_name = 'rex_warehouse_customer_address';

rex_extension::register(
    'YFORM_MANAGER_DATA_PAGE_HEADER',
    static function (rex_extension_point $ep) {
        /** @var rex_yform_manager_dataset $yform */
        $yform = $ep->getParam('yform');
        if ($yform->table->getTableName() === $ep->getParam('table_name')) {
            return '';
        }
    },
    rex_extension::EARLY,
    ['table_name' => $table_name],
);

// @phpstan-ignore-next-line
$_REQUEST['table_name'] = $table_name;
?>

<div class="rex-page-section">
	<?= Search::getForm() ?>
</div>
<?php
if (rex_addon::get('yform')->isAvailable() && rex_version::compare(rex_addon::get('yform')->getVersion(), '5.0.0', '>=')) {
    include rex_path::addon('yform', 'pages/manager.data_edit.php');
} else {
    include rex_path::plugin('yform', 'manager', 'pages/data_edit.php');
}
?>
