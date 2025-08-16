<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Address;
use FriendsOfRedaxo\Warehouse\Session;

$customer = Customer::getCurrent();
$customer_shipping_address = null;
if ($customer !== null) {
    /** @var Address $customer_shipping_address */
    $customer_shipping_address = $customer->getShippingAddress();
}

// Get existing billing and shipping address data from session
$billing_data = Session::getBillingAddressData();
$shipping_data = Session::getShippingAddressData();

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

// Billing Address Section
$yform->setValueField('html', ['', '<div class="col-md-6">']);
$yform->setValueField('html', ['', '<h4>' . Warehouse::getLabel('address_billing') . '</h4>']);

$yform->setValueField('text', ['billing_address_firstname', Warehouse::getLabel('customer_firstname') . '*', $billing_data['firstname'] ?? $customer?->getFirstname() ?? '', '', ['required' => 'required']]);
$yform->setValidateField('empty', ['billing_address_firstname', Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', ['billing_address_lastname', Warehouse::getLabel('customer_lastname') . '*', $billing_data['lastname'] ?? $customer?->getLastname() ?? '', '', ['required' => 'required']]);
$yform->setValidateField('empty', ['billing_address_lastname', Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', ['billing_address_company', Warehouse::getLabel('customer_company'), $billing_data['company'] ?? $customer?->getCompany() ?? '', '']);

$yform->setValueField('text', ['billing_address_department', Warehouse::getLabel('customer_department'), $billing_data['department'] ?? $customer?->getDepartment() ?? '', '']);

$yform->setValueField('text', ['billing_address_address', Warehouse::getLabel('address_address') . '*', $billing_data['address'] ?? $customer?->getAddress() ?? '', '', ['required' => 'required']]);
$yform->setValidateField('empty', ['billing_address_address', Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', ['billing_address_zip', Warehouse::getLabel('address_zip') . '*', $billing_data['zip'] ?? $customer?->getZip() ?? '', '', ['required' => 'required']]);
$yform->setValidateField('empty', ['billing_address_zip', Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', ['billing_address_city', Warehouse::getLabel('address_city') . '*', $billing_data['city'] ?? $customer?->getCity() ?? '', '', ['required' => 'required']]);
$yform->setValidateField('empty', ['billing_address_city', Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', ['billing_address_email', Warehouse::getLabel('customer_email') . '*', $billing_data['email'] ?? $customer?->getEmail() ?? '', '', ['required' => 'required']]);
$yform->setValidateField('empty', ['billing_address_email', Warehouse::getLabel('validation_required_fields')]);
$yform->setValidateField('type', ['billing_address_email', 'email', Warehouse::getLabel('validation_email_invalid')]);

$yform->setValueField('text', ['billing_address_phone', Warehouse::getLabel('customer_phone'), $billing_data['phone'] ?? $customer?->getPhone() ?? '', '']);

$yform->setValueField('html', ['', '</div>']);

// Shipping Address Section
$yform->setValueField('html', ['', '<div class="col-md-6">']);
$yform->setValueField('html', ['', '<div class="mb-3">']);
$yform->setValueField('checkbox', ['different_shipping_address', 'Abweichende Lieferadresse', '1', '', ['class' => 'form-check-input', 'id' => 'different_shipping_address']]);
$yform->setValueField('html', ['', '</div>']);

$yform->setValueField('html', ['', '<div id="shipping-address-fields" style="display: none;">']);
$yform->setValueField('html', ['', '<h4>' . Warehouse::getLabel('address_shipping') . '</h4>']);

$yform->setValueField('text', ['shipping_address_firstname', Warehouse::getLabel('customer_firstname'), $shipping_data['firstname'] ?? $customer_shipping_address?->getName() ?? '', '']);

$yform->setValueField('text', ['shipping_address_lastname', Warehouse::getLabel('customer_lastname'), $shipping_data['lastname'] ?? '', '']);

$yform->setValueField('text', ['shipping_address_company', Warehouse::getLabel('customer_company'), $shipping_data['company'] ?? $customer_shipping_address?->getCompany() ?? '', '']);

$yform->setValueField('text', ['shipping_address_address', Warehouse::getLabel('address_address'), $shipping_data['address'] ?? $customer_shipping_address?->getStreet() ?? '', '']);

$yform->setValueField('text', ['shipping_address_zip', Warehouse::getLabel('address_zip'), $shipping_data['zip'] ?? $customer_shipping_address?->getZip() ?? '', '']);

$yform->setValueField('text', ['shipping_address_city', Warehouse::getLabel('address_city'), $shipping_data['city'] ?? $customer_shipping_address?->getCity() ?? '', '']);

$yform->setValueField('html', ['', '</div>']); // close shipping fields

$yform->setValueField('textarea', ['note', 'Bemerkung', '', '']);

$yform->setValueField('html', ['', '</div>']);
$yform->setValueField('html', ['', '</div>']); // close row

# Payment options

$yform->setValueField('html', ['', '<div class="row"><div class="col-12">']);
if (count($allowedPaymentOptions) > 1) {
    $yform->setValueField('warehouse_payment_options', ["payment_type", Warehouse::getLabel('payment_type')]);
    $yform->setValidateField('empty', ['payment_type', Warehouse::getLabel('validation_required_fields')]);
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

// Add JavaScript for dynamic shipping address fields
$yform->setValueField('html', ['', '
<script>
document.addEventListener("DOMContentLoaded", function() {
    const checkbox = document.getElementById("different_shipping_address");
    const shippingFields = document.getElementById("shipping-address-fields");
    
    if (checkbox && shippingFields) {
        checkbox.addEventListener("change", function() {
            if (this.checked) {
                shippingFields.style.display = "block";
            } else {
                shippingFields.style.display = "none";
                // Clear shipping address fields when hidden
                const shippingInputs = shippingFields.querySelectorAll("input, textarea");
                shippingInputs.forEach(input => {
                    if (input.type !== "hidden") {
                        input.value = "";
                    }
                });
            }
        });
        
        // Check if shipping data exists and show fields
        const hasShippingData = ' . (empty($shipping_data) ? 'false' : 'true') . ';
        if (hasShippingData) {
            checkbox.checked = true;
            shippingFields.style.display = "block";
        }
    }
});
</script>
']);

$yform->setValueField('html', ['','</section>']);

$yform->setActionField('callback', ['FriendsOfRedaxo\Warehouse\Checkout::saveCustomerInSession']);

$yform->setActionField('redirect', [$domain?->getCheckoutUrl(['continue_with' => 'payment']) ?? '']);

$form = $yform->getForm();

$fragment = new rex_fragment();
$fragment->setVar('form', $form);
echo $fragment->parse('warehouse/bootstrap5/checkout/checkout_page.php');
