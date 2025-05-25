<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Warehouse;

$cart = Cart::get();
$cart_items = $cart->getItems();

$showcart = Warehouse::getConfig('show_cart') ? 1 : 0;

if (!$cart) {

    echo '<p class="text-center">' . rex_i18n::msg('warehouse.cart_empty') . '</p>';
    return;
}

?>
<table class="table table-striped table-hover table-bordered">
    <?php foreach ($cart as $k => $item) : ?>
        <tr>
            <td class="align-left"><?= html_entity_decode($item['name']) ?></td>
            <td class="align-right"><?= Warehouse::getCurrencySign() ?> <?= number_format($item['price'], 2) ?></td>
            <td class="no-wrap td_warehouse_count">
                <a href="/?current_article=<?= $rex_article_id ?>&showcart=<?= $showcart ?>&action=modify_cart&art_uid=<?= $k ?>&mod=-1" class="circle minus white">-</a>
                <span class="countnum"><?= $item['amount'] ?></span>
                <a href="/?current_article=<?= $rex_article_id ?>&showcart=<?= $showcart ?>&action=modify_cart&art_uid=<?= $k ?>&mod=+1" class="circle plus white">+</a>
            </td>
            <td class="align-right"><?= Warehouse::getCurrencySign() ?> <?= number_format($item['total'], 2) ?></td>
            <td>
                <a href="/?current_article=<?= $rex_article_id ?>&showcart=<?= $showcart ?>&action=modify_cart&art_uid=<?= $k ?>&mod=del" class="circle plus white cross">{{ delete }}</a>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td class="align-left"><?= Warehouse::getLabel('shipping_costs'); ?></td>
        <td></td>
        <td></td>
        <td class="align-right"><?= Warehouse::getCurrencySign() ?> <?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getShippingCost(), 2) ?></td>
        <td></td>
    </tr>
    <tr class="bigtext">
        <td class="align-left">{{ Total }}</td>
        <td></td>
        <td></td>
        <td class="align-right"><?= Warehouse::getCurrencySign() ?> <?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getCartTotal(), 2) ?></td>
        <td></td>
    </tr>
</table>

<p><a href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>" class="white_big_circle">Weiter</a></p>
