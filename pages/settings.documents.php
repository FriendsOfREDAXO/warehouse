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
	</div>
</div>
