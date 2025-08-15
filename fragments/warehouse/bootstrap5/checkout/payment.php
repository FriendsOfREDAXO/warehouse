<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Checkout;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;

$customer = Checkout::loadCustomerFromSession();
$cart = Cart::loadCartFromSession();
$domain = Domain::getCurrent();

// Das Formular zur Bestellung wurde bereits ausgefüllt und der Nutzer möchte nun mit der Zahlung fortfahren
// Verschiedene Optionen:
// 1. Zahlung per Vorkasse - Weiterleiten zur Bestellübersicht
// 2. Zahlung per PayPal - Weiterleiten zur PayPal-Zahlung
// 3. Zahlung per Nachnahme - Formular zur Nachnahme-Zahlung anzeigen
// 4. Zahlung per Lastschrift - Formular zur Lastschrift-Zahlung anzeigen
?>
<?php dump(Checkout::loadCustomerFromSession()); ?>

<?php

rex_response::sendRedirect($domain?->getCheckoutUrl(['continue_with' => 'summary']) ?? '');
