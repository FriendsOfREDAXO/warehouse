<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Checkout;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Warehouse;

$domain = Domain::getCurrent();
?>
<div class="row">
    <section class="col-12 text-center">
        <h1><?= Warehouse::getLabel('checkout'); ?></h1>
        <p><?= Warehouse::getLabel('checkout_choose'); ?></p>
    </section>
    <section class="col-12">
        <div class="row g-3">
            <div class="col-12 col-md-6 d-flex">
                <div class="card flex-fill h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= Warehouse::getLabel('checkout_guest'); ?></h5>
                        <p class="card-text"><?= Warehouse::getLabel('checkout_guest_text'); ?></p>
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
                        <h5 class="card-title"><?= Warehouse::getLabel('checkout_login'); ?></h5>
                        <p class="card-text"><?= Warehouse::getLabel('checkout_login_text'); ?> <a href="#"><?= Warehouse::getLabel('checkout_register_text'); ?></a></p>
                        <div class="mt-auto">
                            <?= Checkout::getLoginForm()->getForm(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>
