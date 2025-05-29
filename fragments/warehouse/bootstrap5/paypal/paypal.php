<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Logger;

$domain = Domain::getCurrent();

if(rex::isFrontend()) {

    if(rex_request('warehouse', 'string') === 'paypal_create_order') {
        Logger::log('paypal_create_order', 'Creating PayPal order');
        FriendsOfRedaxo\Warehouse\PayPal::createOrder();
        exit;
    }

    if(rex_request('warehouse', 'string') === 'paypal_execute_payment') {
        Logger::log('paypal_execute_payment', 'Execute Payment');
        FriendsOfRedaxo\Warehouse\PayPal::executePayment();
        FriendsOfRedaxo\Warehouse\Warehouse::PaypalPaymentApprovedViaResponse($response);
        rex_response::sendRedirect(rex_getUrl($domain->getThankyouArtUrl(), '', $params ?? [] , '&'));    
        exit;
    }

    
}
?>
