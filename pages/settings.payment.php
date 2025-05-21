<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\PayPal;

echo rex_view::title($this->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

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
        <?= rex_view::info(rex_i18n::msg('warehouse.settings.payment.info')); ?>
    </div>
</div>
