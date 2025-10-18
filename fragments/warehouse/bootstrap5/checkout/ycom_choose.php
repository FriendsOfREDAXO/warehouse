<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Checkout;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Warehouse;

$domain = Domain::getCurrent();
$cart_url = $domain?->getCartArtUrl() ?? '';
?>
<div class="row">
    <section class="col-12 my-3">
        <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-outline-secondary"
                href="<?= htmlspecialchars($cart_url, ENT_QUOTES, 'UTF-8') ?>">
                <i class="bi bi-arrow-left"></i>
                <?= htmlspecialchars(rex_i18n::msg('warehouse.settings.label_back_to_cart'), ENT_QUOTES, 'UTF-8') ?>
            </a>
        </div>
    </section>
    <section class="col-12 text-center">
        <h1><?= htmlspecialchars(Warehouse::getLabel('checkout'), ENT_QUOTES, 'UTF-8'); ?></h1>
        <p><?= htmlspecialchars(Warehouse::getLabel('checkout_choose'), ENT_QUOTES, 'UTF-8'); ?></p>
    </section>
    <section class="col-12">
        <div class="row g-3">
            <div class="col-12 col-md-6 d-flex">
                <div class="card flex-fill h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars(Warehouse::getLabel('checkout_guest'), ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="card-text"><?= htmlspecialchars(Warehouse::getLabel('checkout_guest_text'), ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="mt-auto">
                            <?=
                            Checkout::getContinueAsGuestForm()->getForm();
?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 d-flex">
                <div class="card flex-fill h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars(Warehouse::getLabel('checkout_login'), ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="card-text"><?= htmlspecialchars(Warehouse::getLabel('checkout_login_text'), ENT_QUOTES, 'UTF-8'); ?> <a href="#"><?= htmlspecialchars(Warehouse::getLabel('checkout_register_text'), ENT_QUOTES, 'UTF-8'); ?></a></p>
                        <div class="mt-auto">
                            <?= Checkout::getLoginForm()->getForm(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="col-12 my-3">
        <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-outline-secondary"
                href="<?= htmlspecialchars($cart_url, ENT_QUOTES, 'UTF-8') ?>">
                <i class="bi bi-arrow-left"></i>
                <?= htmlspecialchars(rex_i18n::msg('warehouse.settings.label_back_to_cart'), ENT_QUOTES, 'UTF-8') ?>
            </a>
        </div>
    </section>
</div>
