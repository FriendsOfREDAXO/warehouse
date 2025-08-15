<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;

$customer = Customer::getCurrent();
$customer_shipping_address = null;
if ($customer !== null) {
    $customer_shipping_address = $customer->getShippingAddress();
}

$allowedPaymentOptions = Payment::getAllowedPaymentOptions();
$domain = Domain::getCurrent();

$yform = new rex_yform();

$yform->setObjectparams('form_action', $domain?->getCheckoutUrl(["continue_as" => "guest"]) ?? '');
$yform->setObjectparams('form_wrap_class', 'warehouse_checkout_form');
$yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
$yform->setObjectparams('form_class', 'rex-yform warehouse_checkout');
$yform->setObjectparams('form_anchor', 'form-checkout');
$yform->setObjectparams('real_field_names', true);

$yform->setValueField('html', ['', '<section><div class="row">']);
$yform->setValueField('html', ['', '<div class="col-md-6">']);

$yform->setValueField('text', ['firstname', 'Vorname*', $customer?->getFirstname(), '', ['required' => 'required']]);
$yform->setValidateField('empty', ['firstname', 'Bitte füllen Sie alle markierten Felder aus']);

$yform->setValueField('text', ['lastname', 'Nachname*', $customer?->getLastname(), '', ['required' => 'required']]);
$yform->setValidateField('empty', ['lastname', 'Bitte füllen Sie alle markierten Felder aus']);

$yform->setValueField('text', ['company', 'Firma', $customer?->getCompany(), '']);

$yform->setValueField('text', ['department', 'Abteilung', $customer?->getDepartment(), '']);

$yform->setValueField('text', ['address', 'Adresse*', $customer?->getAddress(), '', ['required' => 'required']]);
$yform->setValidateField('empty', ['address', 'Bitte füllen Sie alle markierten Felder aus']);

$yform->setValueField('text', ['zip', 'PLZ*', $customer?->getZip(), '', ['required' => 'required']]);
$yform->setValidateField('empty', ['zip', 'Bitte füllen Sie alle markierten Felder aus']);

$yform->setValueField('text', ['city', 'Ort*', $customer?->getCity(), '', ['required' => 'required']]);

$yform->setValidateField('empty', ['city', 'Bitte füllen Sie alle markierten Felder aus']);

$yform->setValueField('text', ['email', 'E-Mail*', $customer?->getEmail(), '', ['required' => 'required']]);
$yform->setValidateField('empty', ['email', 'Bitte füllen Sie alle markierten Felder aus']);
$yform->setValidateField('type', ['email', 'email', 'Bitte geben Sie eine gültige E-Mail Adresse ein']);

$yform->setValueField('text', ['phone', 'Telefon', $customer?->getPhone(), '']);

$yform->setValueField('html', ['', '</div>']);
$yform->setValueField('html', ['', '<div class="col-md-6">']);

$yform->setValueField('text', ['to_name', 'Name', $customer_shipping_address?->getName(), '']);
$yform->setValueField('text', ['to_company', 'Firma', $customer_shipping_address?->getCompany(), '']);
$yform->setValueField('text', ['to_address', 'Lieferadresse', $customer_shipping_address?->getStreet(), '']);
$yform->setValueField('text', ['to_zip', 'PLZ', $customer_shipping_address?->getZip(), '']);
$yform->setValueField('text', ['to_city', 'Ort', $customer_shipping_address?->getCity(), '']);

$yform->setValueField('textarea', ['note', 'Bemerkung', '', '']);

$yform->setValueField('html', ['', '</div>']);
$yform->setValueField('html', ['', '</div>']); // close row

# Payment options

$yform->setValueField('html', ['', '<div class="row"><div class="col-12">']);
if (count($allowedPaymentOptions) > 1) {
    $yform->setValueField('warehouse_payment_options', ["payment_type", Warehouse::getLabel('payment_type')]);
    $yform->setValidateField('empty', ['payment_type', 'Bitte füllen Sie alle markierten Felder aus']);
} else {
    $yform->setValueField('warehouse_payment_options', ["payment_type", Warehouse::getLabel('payment_type')]);
    $yform->setValueField('html', ['', Warehouse::getLabel('payment_type')]);
    // $yform->setValueField('text', ['payment_type', array_values($allowedPaymentOptions)]);
}

if (count($allowedPaymentOptions) > 1) {
    if (in_array('direct_debit', $allowedPaymentOptions)) {
        $yform->setValueField('html', ['', Warehouse::getLabel('direct_debit')]);
        $yform->setValueField('text', ['direct_debit_name', 'Ggf. abweichender Kontoinhaber', '', '']);
        $yform->setValueField('text', ['iban', 'IBAN*', '', '']);
        $yform->setValueField('text', ['bic', 'BIC*', '', '']);
    }
}

$yform->setValueField('html', ['', '</div></div>']); // close row and col
$yform->setValueField('submit_once', ['send',Warehouse::getLabel('next'),'','','','button']);
$yform->setValueField('html', ['','</section>']);

$yform->setActionField('callback', ['FriendsOfRedaxo\Warehouse\Checkout::saveCustomerInSession']);

$yform->setActionField('redirect', [$domain?->getCheckoutUrl(['continue_with' => 'payment']) ?? '']);

$form = $yform->getForm();

$fragment = new rex_fragment();
$fragment->setVar('form', $form);
echo $fragment->parse('warehouse/bootstrap5/checkout/checkout_page.php');
