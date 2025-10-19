<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\CustomerAddress;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Order;
use FriendsOfRedaxo\Warehouse\Warehouse;
use rex_ycom_auth;

// Get the logged-in user
$ycom_user = rex_ycom_auth::getUser();

if (!$ycom_user) {
    echo '<div class="alert alert-warning">' . Warehouse::getLabel('account_login_required') . '</div>';
    return;
}

// Get current domain for links
$domain = Domain::getCurrent();

// Get user data
$customer = Customer::getCurrent();
$firstname = $ycom_user->getValue('firstname') ?? '';
$lastname = $ycom_user->getValue('lastname') ?? '';
$email = $ycom_user->getValue('email') ?? '';
$fullname = trim($firstname . ' ' . $lastname);

// Get latest order
$orders = Order::findByYComUserId();
$latestOrder = $orders && $orders->count() > 0 ? $orders->first() : null;

// Get billing and shipping addresses
$billingAddress = CustomerAddress::query()
    ->where(CustomerAddress::YCOM_USER_ID, $ycom_user->getId())
    ->where(CustomerAddress::TYPE, 'billing')
    ->findOne();

$shippingAddress = CustomerAddress::query()
    ->where(CustomerAddress::YCOM_USER_ID, $ycom_user->getId())
    ->where(CustomerAddress::TYPE, 'shipping')
    ->findOne();

?>
<!-- BEGIN account -->
<div class="container my-5">
    <!-- Greeting -->
    <div class="row mb-4">
        <div class="col-12">
            <h1><?= Warehouse::getLabel('account_welcome') ?>, <?= htmlspecialchars($fullname ?: $email, ENT_QUOTES, 'UTF-8') ?>!</h1>
        </div>
    </div>

    <!-- Account Cards -->
    <div class="row g-4">
        <!-- Master Data Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-person-circle"></i>
                        <?= Warehouse::getLabel('account_master_data') ?>
                    </h5>
                    <div class="card-text">
                        <p class="mb-1">
                            <strong><?= Warehouse::getLabel('customer_firstname') ?>:</strong>
                            <?= htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="mb-1">
                            <strong><?= Warehouse::getLabel('customer_lastname') ?>:</strong>
                            <?= htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="mb-1">
                            <strong><?= Warehouse::getLabel('customer_email') ?>:</strong>
                            <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Orders Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-cart-check"></i>
                        <?= Warehouse::getLabel('account_my_orders') ?>
                    </h5>
                    <div class="card-text">
                        <?php if ($latestOrder): ?>
                            <p class="mb-2">
                                <strong><?= Warehouse::getLabel('account_latest_order') ?>:</strong><br>
                                <?= Warehouse::getLabel('order_number') ?>: <?= htmlspecialchars($latestOrder->getOrderNo() ?: $latestOrder->getId(), ENT_QUOTES, 'UTF-8') ?><br>
                                <small class="text-muted"><?= htmlspecialchars($latestOrder->getCreatedateFormatted(), ENT_QUOTES, 'UTF-8') ?></small>
                            </p>
                        <?php else: ?>
                            <p class="text-muted"><?= Warehouse::getLabel('account_no_orders') ?></p>
                        <?php endif; ?>
                        <?php if ($domain && $domain->getOrderArt()): ?>
                            <a href="<?= $domain->getOrderArtUrl() ?>" class="btn btn-sm btn-outline-primary mt-2">
                                <?= Warehouse::getLabel('account_view_all_orders') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Address Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-receipt"></i>
                        <?= Warehouse::getLabel('address_billing') ?>
                    </h5>
                    <div class="card-text">
                        <?php if ($billingAddress): ?>
                            <p class="mb-0">
                                <?php if ($billingAddress->getCompany()): ?>
                                    <?= htmlspecialchars($billingAddress->getCompany(), ENT_QUOTES, 'UTF-8') ?><br>
                                <?php endif; ?>
                                <?= htmlspecialchars($billingAddress->getName(), ENT_QUOTES, 'UTF-8') ?><br>
                                <?= htmlspecialchars($billingAddress->getStreet(), ENT_QUOTES, 'UTF-8') ?><br>
                                <?= htmlspecialchars($billingAddress->getZip(), ENT_QUOTES, 'UTF-8') ?>
                                <?= htmlspecialchars($billingAddress->getCity(), ENT_QUOTES, 'UTF-8') ?><br>
                                <?php if ($billingAddress->getCountry()): ?>
                                    <?= htmlspecialchars($billingAddress->getCountry(), ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted"><?= Warehouse::getLabel('account_no_billing_address') ?></p>
                        <?php endif; ?>
                        <?php if ($domain && $domain->getAddressArt()): ?>
                            <a href="<?= $domain->getAddressArtUrl() ?>" class="btn btn-sm btn-outline-primary mt-2">
                                <?= Warehouse::getLabel('account_edit_address') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Address Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-truck"></i>
                        <?= Warehouse::getLabel('address_shipping') ?>
                    </h5>
                    <div class="card-text">
                        <?php if ($shippingAddress): ?>
                            <p class="mb-0">
                                <?php if ($shippingAddress->getCompany()): ?>
                                    <?= htmlspecialchars($shippingAddress->getCompany(), ENT_QUOTES, 'UTF-8') ?><br>
                                <?php endif; ?>
                                <?= htmlspecialchars($shippingAddress->getName(), ENT_QUOTES, 'UTF-8') ?><br>
                                <?= htmlspecialchars($shippingAddress->getStreet(), ENT_QUOTES, 'UTF-8') ?><br>
                                <?= htmlspecialchars($shippingAddress->getZip(), ENT_QUOTES, 'UTF-8') ?>
                                <?= htmlspecialchars($shippingAddress->getCity(), ENT_QUOTES, 'UTF-8') ?><br>
                                <?php if ($shippingAddress->getCountry()): ?>
                                    <?= htmlspecialchars($shippingAddress->getCountry(), ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </p>
                        <?php elseif ($billingAddress): ?>
                            <p class="text-muted"><?= Warehouse::getLabel('address_same_as_billing') ?></p>
                        <?php else: ?>
                            <p class="text-muted"><?= Warehouse::getLabel('account_no_shipping_address') ?></p>
                        <?php endif; ?>
                        <?php if ($domain && $domain->getAddressArt()): ?>
                            <a href="<?= $domain->getAddressArtUrl() ?>" class="btn btn-sm btn-outline-primary mt-2">
                                <?= Warehouse::getLabel('account_edit_address') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END account -->
