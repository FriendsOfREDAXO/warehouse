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
<table class="table table-striped table-sm w-100" id="table_order_summary">
    <thead>
        <tr>
            <th></th>
            <th class="text-right"><?= Warehouse::getCurrency() ?></th>
        </tr>
    </thead>
    <?php foreach ($this->cart as $item) : ?>
        <tr>
            <td>
                <?= trim(html_entity_decode($item['name']), ' -') ?><br>
                <?php $attr_text = []; ?>
                <?php foreach ($item['attributes'] as $attr) : ?>
                    <?php $attr_text[] = $attr['value'] ?>
                <?php endforeach ?>
                <?= implode(' - ', $attr_text). ($attr_text ? '<br>' : '') ?>
                
                
                
                <?= $item['amount'] ?> x à <?= number_format($item['price'], 2) ?>
            </td>
            <td class="uk-text-right"><?= number_format($item['total'], 2) ?></td>
        </tr>
    <?php endforeach ?>
    <tr>
        <td><?= Warehouse::getLabel('shipping_costs') ?> <?= $billing_data['country'] ?? $user_data['country'] ?? '' ?></td>
        <td class="uk-text-right"><?= number_format((float) FriendsOfRedaxo\Warehouse\Shipping::getCost(), 2) ?></td>
    </tr>
    <tr>
        <td><?= Warehouse::getLabel('total') ?></td>
        <td class="uk-text-right"><?= Cart::getCartTotalFormatted() ?></td>
    </tr>
</table>

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
