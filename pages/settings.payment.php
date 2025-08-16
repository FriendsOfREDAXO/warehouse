<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Warehouse;

echo rex_view::title($this->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$field = $form->addCheckboxField('allowed_payment_options');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.allowed_payment_options'));
$field->setAttribute('multiple', 'multiple');

$translatedOptions = [];
foreach (Payment::getPaymentOptions() as $value => $label) {
    $field->addOption(rex_i18n::msg($label), $value);
}

// ==== Paypal
$field = $form->addTextField('store_name');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.store_name'));
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.store_name.notice'));

$field = $form->addSelectField('store_country_code');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.store_country_code'));
$select = $field->getSelect();
$select->addOptions(
    PayPal::COUNTRY_CODES
);
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.store_country_code.notice'));

$form->addFieldset('Paypal Einstellungen');

$field = $form->addTextField('paypal_client_id');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_client_id'));

$field = $form->addTextField('paypal_secret');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_secret'));

$field = $form->addCheckboxField('sandboxmode');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.sandboxmode'));
$field->addOption(rex_i18n::msg('warehouse.settings.payment.sandboxmode.option'), "1");

$field = $form->addTextField('paypal_sandbox_client_id');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_sandbox_client_id'));

$field = $form->addTextField('paypal_sandbox_secret');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_sandbox_secret'));

$field = $form->addTextField('paypal_getparams');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_getparams'));
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_getparams.notice'));


$form->addFieldset(rex_i18n::msg('warehouse.settings.payment.paypal_style'));

$field = $form->addSelectField('paypal_button_shape');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_button_shape'));
$select = $field->getSelect();
$select->addOptions([
    'rect' => rex_i18n::msg('warehouse.settings.payment.paypal_button_shape.rect'),
    'pill' => rex_i18n::msg('warehouse.settings.payment.paypal_button_shape.pill'),
]);
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_button_shape.notice'));

$field = $form->addSelectField('paypal_button_size');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_button_size'));
$select = $field->getSelect();
$select->addOptions([
    'small' => rex_i18n::msg('warehouse.settings.payment.paypal_button_size.small'),
    'medium' => rex_i18n::msg('warehouse.settings.payment.paypal_button_size.medium'),
    'large' => rex_i18n::msg('warehouse.settings.payment.paypal_button_size.large'),
    'responsive' => rex_i18n::msg('warehouse.settings.payment.paypal_button_size.responsive'),
]);
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_button_size.notice'));

$field = $form->addSelectField('paypal_button_color');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_button_color'));
$select = $field->getSelect();
$select->addOptions([
    'gold' => rex_i18n::msg('warehouse.settings.payment.paypal_button_color.gold'),
    'blue' => rex_i18n::msg('warehouse.settings.payment.paypal_button_color.blue'),
    'silver' => rex_i18n::msg('warehouse.settings.payment.paypal_button_color.silver'),
    'black' => rex_i18n::msg('warehouse.settings.payment.paypal_button_color.black'),
    'white' => rex_i18n::msg('warehouse.settings.payment.paypal_button_color.white'),
]);
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_button_color.notice'));

$field = $form->addSelectField('paypal_button_label');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_button_label'));
$select = $field->getSelect();
$select->addOptions([
    'paypal' => rex_i18n::msg('warehouse.settings.payment.paypal_button_label.paypal'),
    'checkout' => rex_i18n::msg('warehouse.settings.payment.paypal_button_label.checkout'),
    'pay' => rex_i18n::msg('warehouse.settings.payment.paypal_button_label.pay'),
    'buynow' => rex_i18n::msg('warehouse.settings.payment.paypal_button_label.buynow'),
    'installment' => rex_i18n::msg('warehouse.settings.payment.paypal_button_label.installment'),
    'donate' => rex_i18n::msg('warehouse.settings.payment.paypal_button_label.donate'),
]);
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_button_label.notice'));

$field = $form->addSelectField('paypal_button_layout');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_button_layout'));
$select = $field->getSelect();
$select->addOptions([
    'horizontal' => rex_i18n::msg('warehouse.settings.payment.paypal_button_layout.horizontal'),
    'vertical' => rex_i18n::msg('warehouse.settings.payment.paypal_button_layout.vertical'),
]);
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_button_layout.notice'));

$field = $form->addTextField('paypal_button_height');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_button_height'));
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_button_height.notice'));
$field = $form->addSelectField('paypal_button_funding_source');

$select = $field->getSelect();
$select->addOptions([
    'paypal' => rex_i18n::msg('warehouse.settings.payment.paypal_button_funding_source.paypal'),
    'credit' => rex_i18n::msg('warehouse.settings.payment.paypal_button_funding_source.credit'),
]);
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_button_funding_source'));
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_button_funding_source.notice'));

$field = $form->addCheckboxField('paypal_include_images');
$field->setLabel(rex_i18n::msg('warehouse.settings.payment.paypal_include_images'));
$field->addOption(rex_i18n::msg('warehouse.settings.payment.paypal_include_images.option'), "1");
$field->setNotice(rex_i18n::msg('warehouse.settings.payment.paypal_include_images.notice'));


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
		<?= rex_view::info(rex_i18n::rawMsg('warehouse.settings.payment.info')); ?>
	</div>
</div>