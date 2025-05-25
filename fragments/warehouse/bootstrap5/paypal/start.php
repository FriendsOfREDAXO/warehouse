<?php
/* -- Warehouse Paypal Start -- */
if (rex::isBackend()) {
    echo '<h2>Paypal Start</h2>';
    return;
} else {
    FriendsOfRedaxo\Warehouse\PayPal::createOrder();
}
?>
