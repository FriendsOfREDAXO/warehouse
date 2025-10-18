<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Session;

?>
<?php
$user_data = $this->warehouse_userdata;
$billing_data = Session::getBillingAddressData();
$shipping_data = Session::getShippingAddressData();
?>

<h2><?= Warehouse::getLabel('order_summary') ?></h2>
<div class="table-responsive">
<table class="table" id="table_order_summary">
    <thead class="table-light">
        <tr>
            <th scope="col"><?= Warehouse::getLabel('article') ?></th>
            <th scope="col" class="text-end"><?= Warehouse::getLabel('price') ?></th>
            <th scope="col" class="text-end"><?= Warehouse::getLabel('quantity') ?></th>
            <th scope="col" class="text-end"><?= Warehouse::getLabel('total') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($this->cart as $item) : ?>
        <tr>
            <td>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <?php if (isset($item['image']) && $item['image']) : ?>
                        <img src="<?= rex_url::media($item['image']) ?>" alt="<?= htmlspecialchars(html_entity_decode($item['name']), ENT_QUOTES, 'UTF-8') ?>" class="img-fluid" style="max-width: 80px;">
                        <?php endif ?>
                    </div>
                    <div class="col">
                        <div class="text-muted small">
                            <?= htmlspecialchars($item['article_id'] . ($item['variant_id'] ? '-' . $item['variant_id'] : ''), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div><?= html_entity_decode($item['name']) ?></div>
                        <?php if ($item['type'] === 'variant'): ?>
                        <small class="text-muted d-block"><?= Warehouse::getLabel('product_variant') ?></small>
                        <?php endif; ?>
                        <?php if (isset($item['attributes']) && count($item['attributes']) > 0): ?>
                            <?php $attr_text = []; ?>
                            <?php foreach ($item['attributes'] as $attr) : ?>
                                <?php $attr_text[] = htmlspecialchars($attr['value'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endforeach ?>
                            <small class="text-muted d-block"><?= implode(' - ', $attr_text) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td class="text-end"><?= Warehouse::formatCurrency($item['price']) ?></td>
            <td class="text-end"><?= $item['amount'] ?></td>
            <td class="text-end"><?= Warehouse::formatCurrency($item['total']) ?></td>
        </tr>
    <?php endforeach ?>
    
    <!-- Zwischensumme -->
    <tr>
        <td colspan="3" class="border-top">
            <strong><?= Warehouse::getLabel('cart_subtotal') ?></strong>
        </td>
        <td class="text-end border-top">
            <?= Warehouse::formatCurrency(Cart::getSubTotalByMode(Warehouse::getPriceInputMode())) ?>
        </td>
    </tr>
    
    <!-- Versandkosten -->
    <tr>
        <td colspan="3">
            <strong><?= Warehouse::getLabel('shipping_costs') ?> <?= $billing_data['country'] ?? $user_data['country'] ?? '' ?></strong>
        </td>
        <td class="text-end"><?= Warehouse::formatCurrency((float) FriendsOfRedaxo\Warehouse\Shipping::getCost()) ?></td>
    </tr>
    
    <!-- Gesamtsumme -->
    <tr class="table-info">
        <td colspan="3" class="fw-bold"><?= Warehouse::getLabel('cart_total') ?></td>
        <td class="text-end fw-bold"><?= Cart::getCartTotalFormatted() ?></td>
    </tr>
    </tbody>
</table>
</div>

<div class="row">
    <div class="col-md-6">
        <h4><?= Warehouse::getLabel('address_billing') ?>:</h4>
        <p>
            <?php if (!empty($billing_data)): ?>
                <?= ($billing_data[Customer::FIRSTNAME] ?? '') . ' ' . ($billing_data[Customer::LASTNAME] ?? '') ?><br>
                <?php if (!empty($billing_data[Customer::COMPANY])): ?>
                    <?= $billing_data[Customer::COMPANY] ?><br>
                <?php endif ?>
                <?= $billing_data[Customer::ADDRESS] ?? '' ?><br>
                <?= ($billing_data[Customer::ZIP] ?? '') . ' ' . ($billing_data[Customer::CITY] ?? '') ?><br>
                <?= $billing_data['country'] ?? '' ?>
            <?php else: ?>
                <?= ($user_data['firstname'] ?? '') . ' ' . ($user_data['lastname'] ?? '') ?><br>
                <?php if (!empty($user_data['company'])): ?>
                    <?= $user_data['company'] ?><br>
                <?php endif ?>
                <?= $user_data['address'] ?? '' ?><br>
                <?= ($user_data['zip'] ?? '') . ' ' . ($user_data['city'] ?? '') ?><br>
                <?= $user_data['country'] ?? '' ?>
            <?php endif ?>
        </p>
    </div>
    
    <div class="col-md-6">
        <h4><?= Warehouse::getLabel('address_shipping') ?>:</h4>
        <?php if (!empty($shipping_data)): ?>
            <p>
                <?= ($shipping_data[Customer::FIRSTNAME] ?? '') . ' ' . ($shipping_data[Customer::LASTNAME] ?? '') ?><br>
                <?php if (!empty($shipping_data[Customer::COMPANY])): ?>
                    <?= $shipping_data[Customer::COMPANY] ?><br>
                <?php endif ?>
                <?= $shipping_data[Customer::ADDRESS] ?? '' ?><br>
                <?= ($shipping_data[Customer::ZIP] ?? '') . ' ' . ($shipping_data[Customer::CITY] ?? '') ?><br>
                <?= $shipping_data['country'] ?? '' ?>
            </p>
        <?php else: ?>
            <p>
                <em><?= Warehouse::getLabel('address_same_as_billing') ?></em>
            </p>
        <?php endif ?>
    </div>
</div>

<p><?= Warehouse::getLabel('payment_type'); ?>: <?= Warehouse::getLabel('paymentoptions_' . ($user_data['payment_type'] ?? '')) ?></p>
