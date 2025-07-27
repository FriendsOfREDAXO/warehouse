<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Checkout;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;

$customer = Customer::getCurrent() ?? Checkout::loadCustomerFromSession();
$customerAddress = $customer?->getAddress();
$customer_shipping_address = $customer?->getShippingAddress();
$allowedPaymentOptions = Payment::getAllowedPaymentOptions();

$cart = Cart::loadCartFromSession();
$payment = Payment::loadPaymentFromSession();
$domain = Domain::getCurrent();

$ycom_mode = Warehouse::getConfig('ycom_mode', 'guest_only');


if (rex::isFrontend()) {

    // Voraussetzungen für Schritt 5 - Summary
    if ('summary' === rex_get('continue_with', 'string', '')) {
        $fragment = new rex_fragment();
        $fragment->setVar('cart', $cart);
        $fragment->setVar('customer', $customer);
        $fragment->setVar('payment', $payment);
        echo $fragment->parse('warehouse/bootstrap5/checkout/summary.php');
        return;
    }

    // Voraussetzungen für Schritt 5 - Summary
    if ('paypal' === rex_get('continue_with', 'string', '')) {
        $fragment = new rex_fragment();
        $fragment->setVar('cart', $cart);
        $fragment->setVar('customer', $customer);
        $fragment->setVar('payment', $payment);
        echo $fragment->parse('warehouse/bootstrap5/paypal/paypal.php');
        return;
    }

    // Voraussetzungen für Schritt 4 - Zahlung
    if ('payment' === rex_get('continue_with', 'string', '')) {
        $fragment = new rex_fragment();
        $fragment->setVar('cart', $cart);
        $fragment->setVar('customer', $customer);
        $fragment->setVar('payment', $payment);
        echo $fragment->parse('warehouse/bootstrap5/checkout/payment.php');
        return;
    }

    // Voraussetzungen für Schritt 3 - Auswahl noch nicht getroffen
    if ($ycom_mode === 'choose' && '' === rex_get('continue_as', 'string', '')) {
        $fragment = new rex_fragment();
        echo $fragment->parse('warehouse/bootstrap5/checkout/ycom_choose.php');
        return;
    }

    // Voraussetzungen für Schritt 3 - YCom-Account erforderlich
    if ($ycom_mode === 'enforce_account' && Customer::getCurrent() === null) {
        $fragment = new rex_fragment();
        echo $fragment->parse('warehouse/bootstrap5/checkout/ycom_choose.php');
        return;
    }

    // Voraussetzungen für Schritt 3 - direkt zur Gast-Checkout-Seite
    if ($ycom_mode === 'guest_only' || 'guest' === rex_get('continue_as', 'string', '')) {
        $fragment = new rex_fragment();
        echo $fragment->parse('warehouse/bootstrap5/checkout/form-guest.php');
        return;
    }

}

if(rex::isBackend()) {
	rex_view::info(
		rex_i18n::msg('warehouse_checkout_not_available_in_backend')
	);
}
