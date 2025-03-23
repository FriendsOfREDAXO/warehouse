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
$field->setLabel('Name des Shops');
$field->setNotice('z.B. <code>Martin Muster GmbH</code>, wird u.a. bei Zahlung an PayPal übermittelt.');

$field = $form->addSelectField('store_country_code');
$field->setLabel('Ländercode des Shops');
$select = $field->getSelect();
$select->addOptions(
    PayPal::COUNTRY_CODES
);
$field->setNotice('z.B. <code>de-DE</code> - wird u.a. bei Zahlung an PayPal übermittelt.');

$form->addFieldset('Paypal Einstellungen');

$field = $form->addTextField('paypal_client_id');
$field->setLabel('Paypal Client Id');

$field = $form->addTextField('paypal_secret');
$field->setLabel('Paypal Secret');

$field = $form->addCheckboxField('sandboxmode');
$field->setLabel('Paypal Sandbox ein');
$field->addOption('Paypal Sandbox ein', "1");


$field = $form->addTextField('paypal_sandbox_client_id');
$field->setLabel('Paypal Sandbox Client Id');

$field = $form->addTextField('paypal_sandbox_secret');
$field->setLabel('Paypal Sandbox Secret');

$field = $form->addTextField('paypal_getparams');
$field->setLabel('Paypal Zusätzliche Get-Parameter für Paypal');
$field->setNotice('z.B. um Maintenance bei der Entwicklung zu verwenden. Als JSON in der Form <code>{"key1":"value1","key2":"value2"}</code> angeben.');

$field = $form->addLinkmapField('paypal_page_start');
$field->setLabel('Paypal Startseite');
$field->setNotice('<code>rex_config::get("warehouse","paypal_page_start")</code>');

$field = $form->addLinkmapField('paypal_page_success');
$field->setLabel('Paypal Zahlung erfolgt');
$field->setNotice('<code>rex_config::get("warehouse","paypal_page_success")</code>');

$field = $form->addLinkmapField('paypal_page_error');
$field->setLabel('Paypal Fehler');
$field->setNotice('<code>rex_config::get("warehouse","paypal_page_error")</code>');

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

?>
<div class="row">
    <div class="col-12 col-md-8">
        <?php echo $content; ?>
    </div>
    <div class="col-12 col-md-4">
        <?= rex_view::info('Derzeit wird nur PayPal als Zahlungsanbieter unterstützt. Beteilige dich an der Weiterentwicklung auf GitHub unter <a href="https://github.com/friendsofredaxo/warehouse/">https://github.com/friendsofredaxo/warehouse/</a>.'); ?>
    </div>
</div>
