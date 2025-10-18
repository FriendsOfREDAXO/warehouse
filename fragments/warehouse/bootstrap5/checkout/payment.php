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
$back_to_address_url = $domain?->getCheckoutUrl(['continue_as' => $ycom_mode === 'guest_only' ? 'guest' : rex_get('continue_as', 'string', 'guest')]) ?? '';
$continue_to_summary_url = $domain?->getCheckoutUrl(['continue_with' => 'summary']) ?? '';
?>
<div class="row">
    <section class="col-12 my-3">
        <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-outline-secondary"
                href="<?= htmlspecialchars($back_to_address_url, ENT_QUOTES, 'UTF-8') ?>">
                <i class="bi bi-arrow-left"></i>
                <?= htmlspecialchars(Warehouse::getLabel('back_to_address'), ENT_QUOTES, 'UTF-8') ?>
            </a>
            <a class="btn btn-primary"
                href="<?= htmlspecialchars($continue_to_summary_url, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars(Warehouse::getLabel('continue_to_summary'), ENT_QUOTES, 'UTF-8') ?>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>
    <section class="col-12">
        <h2><?= htmlspecialchars(Warehouse::getLabel('checkout_payment'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p>Zahlungsinformationen wurden gespeichert.</p>
    </section>
    <section class="col-12 my-3">
        <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-outline-secondary"
                href="<?= htmlspecialchars($back_to_address_url, ENT_QUOTES, 'UTF-8') ?>">
                <i class="bi bi-arrow-left"></i>
                <?= htmlspecialchars(Warehouse::getLabel('back_to_address'), ENT_QUOTES, 'UTF-8') ?>
            </a>
            <a class="btn btn-primary"
                href="<?= htmlspecialchars($continue_to_summary_url, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars(Warehouse::getLabel('continue_to_summary'), ENT_QUOTES, 'UTF-8') ?>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>
</div>
