<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Checkout;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Session;
use FriendsOfRedaxo\Warehouse\Warehouse;

$customer = Checkout::loadCustomerFromSession();

$cart = Session::getCartData();
$domain = Domain::getCurrent();
$ycom_mode = Warehouse::getConfig('ycom_mode', 'guest_only');

// Das Formular zur Bestellung wurde bereits ausgefüllt und der Nutzer möchte nun mit der Zahlung fortfahren
// Verschiedene Optionen:
// 1. Zahlung per Vorkasse - Weiterleiten zur Bestellübersicht
// 2. Zahlung per PayPal - Weiterleiten zur PayPal-Zahlung
// 3. Zahlung per Nachnahme - Formular zur Nachnahme-Zahlung anzeigen
// 4. Zahlung per Lastschrift - Formular zur Lastschrift-Zahlung anzeigen

// For now, we automatically redirect to summary, but first show a page with navigation
?>
<div class="row">
    <section class="col-12 my-3">
        <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-outline-secondary"
                href="<?= $domain?->getCheckoutUrl(['continue_as' => $ycom_mode === 'guest_only' ? 'guest' : rex_get('continue_as', 'string', 'guest')]) ?? '' ?>">
                <i class="bi bi-arrow-left"></i>
                <?= Warehouse::getLabel('back_to_address') ?>
            </a>
            <a class="btn btn-primary"
                href="<?= $domain?->getCheckoutUrl(['continue_with' => 'summary']) ?? '' ?>">
                <?= Warehouse::getLabel('continue_to_summary') ?>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>
    <section class="col-12">
        <h2><?= Warehouse::getLabel('checkout_payment') ?></h2>
        <p>Zahlungsinformationen wurden gespeichert.</p>
    </section>
    <section class="col-12 my-3">
        <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-outline-secondary"
                href="<?= $domain?->getCheckoutUrl(['continue_as' => $ycom_mode === 'guest_only' ? 'guest' : rex_get('continue_as', 'string', 'guest')]) ?? '' ?>">
                <i class="bi bi-arrow-left"></i>
                <?= Warehouse::getLabel('back_to_address') ?>
            </a>
            <a class="btn btn-primary"
                href="<?= $domain?->getCheckoutUrl(['continue_with' => 'summary']) ?? '' ?>">
                <?= Warehouse::getLabel('continue_to_summary') ?>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>
</div>
