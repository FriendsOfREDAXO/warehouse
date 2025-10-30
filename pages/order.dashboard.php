<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Dashboard;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

// Dashboard-Klasse instanziieren und Layout generieren
$dashboard = new Dashboard();
$layout = $dashboard->getDashboardLayout();

// Dashboard-Layout ausgeben
foreach ($layout as $row_key => $columns): ?>
<div class="row">
	<?php foreach ($columns as $col_key => $column): ?>
	<div class="col-md-<?= $column['col'] ?>">
		<?= $column['content'] ?>
	</div>
	<?php endforeach; ?>
</div>
<?php endforeach;
?>