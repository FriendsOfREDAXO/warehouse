<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Warehouse;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$field = $form->addInputField('number', 'invoice_number', null, ['class' => 'form-control', 'min' => '1', 'step' => '1']);
$field->setLabel(rex_i18n::msg('warehouse.settings.invoice_number'));
$field->setNotice(rex_i18n::msg('warehouse.settings.invoice_number.notice'));

$field = $form->addInputField('number', 'delivery_note_number', null, ['class' => 'form-control', 'min' => '1', 'step' => '1']);
$field->setLabel(rex_i18n::msg('warehouse.settings.delivery_note_number'));
$field->setNotice(rex_i18n::msg('warehouse.settings.delivery_note_number.notice'));

$field = $form->addInputField('number', 'order_number', null, ['class' => 'form-control', 'min' => '1', 'step' => '1']);
$field->setLabel(rex_i18n::msg('warehouse.settings.order_number'));
$field->setNotice(rex_i18n::msg('warehouse.settings.order_number.notice'));

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse.settings.payment'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

?>
<div class="row">
	<div class="col-12 col-md-8">
		<?php echo $content; ?>
	</div>
	<div class="col-12 col-md-4">
		<?= rex_view::error(rex_i18n::rawMsg('warehouse.settings.documents.info')); ?>
		<?= rex_view::info('Tipp: Die Nummernkreise können über Extension Points angepasst werden: <code>WAREHOUSE_ORDER_NUMBER</code>, <code>WAREHOUSE_DELIVERY_NOTE_NUMBER</code> und <code>WAREHOUSE_INVOICE_NUMBER</code>. Weitere Informationen findest du in der Hilfe unter "Warehouse erweitern".'); ?>
		<?php

		
// Überprüfe, ob PDFout in Version 10.x vorliegt - wenn <=9 oder >=11, dann zeige Hinweis an
if (rex_addon::get('pdfout')->isAvailable() && rex_version::compare(rex_addon::get('pdfout')->getVersion(), '10.0.0', '<') || rex_version::compare(rex_addon::get('pdfout')->getVersion(), '11.0.0', '>=') ) {
	echo rex_view::error(rex_i18n::msg('warehouse.settings.documents.pdfout_version'));
}
?>
	</div>
</div>
