<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Shipping;
use FriendsOfRedaxo\Warehouse\Warehouse;

$cart = Cart::get();
$cart_items = $cart->getItems();
?>
<!-- cart/offcanvas_cart.php -->
<div class="offcanvas offcanvas-end show" tabindex="-1" id="cart-offcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title"><?= Warehouse::getLabel('cart') ?></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php if ($cart) { ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($cart_items as $uuid => $article) { ?>
                    <li class="list-group-item">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <div class="ratio ratio-4x3">
                                    <?php if ($article['image']) { ?>
                                        <a href="<?= rex_getUrl('', '', ['warehouse-article-id' => $article['id']]) ?>">
                                            <img src="<?= $article['image'] ?>" alt="<?= $article['name'] ?>" class="img-fluid">
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col">
                                <a class="link-heading stretched-link text-decoration-none small" href="<?= rex_getUrl('', '', ['warehouse-article-id' => $article['id']]) ?>"><?= trim($article['name'], '- ') ?>
                                    <span class="text-muted small"> (<?= $article['id'] ?>)</span>
                                </a>
                                <div class="mt-1 row g-2 align-items-center">
                                    <div class="col-auto fw-bolder small"><?= Warehouse::getCurrencySign() ?>&nbsp;<?= number_format($article['total'], 2) ?></div>
                                    <div class="col-auto text-muted small"><?= $article['amount'] ?> &times; <?= Warehouse::getCurrencySign() ?>&nbsp;<?= number_format($article['price'], 2) ?></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a class="text-danger" href="/?showcart=1&action=modify_cart&art_uid=<?= $uuid ?>&mod=del" title="Remove">
                                    <i class="bi bi-x-circle-fill"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>

            <div class="mt-3">
                <div class="row g-2">
                    <div class="col text-muted h4"><?= Warehouse::getLabel('cart_subtotal') ?></div>
                    <div class="col-auto h4 fw-bolder"><?= Warehouse::getCurrencySign() ?>&nbsp;<?= number_format(Cart::getSubTotal(), 2) ?></div>
                </div>
                <div class="row g-2">
                    <div class="col text-muted"><?= Warehouse::getLabel('shipping_costs') ?></div>
                    <div class="col-auto">
                        <?= Warehouse::getCurrencySign() ?>&nbsp;<?= number_format(Shipping::getCost(), 2) ?></div>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col text-muted"><?= Warehouse::getLabel('cart_total') ?></div>
                    <div class="col-auto h5 fw-bolder">
                        <?= Warehouse::getCurrencySign() ?>&nbsp;<?= number_format($cart->getTotal(), 2) ?></div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-3">
                <a class="btn btn-primary" href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>"><?= Warehouse::getLabel('next') ?></a>
            </div>
        <?php } else { ?>
            <div class="alert alert-info"><?= Warehouse::getLabel('cart_is_empty'); ?></div>
        <?php } ?>

        <!-- Button, um den Warenkorb zu leeren -->
        <div class="d-grid gap-2 mt-3">
            <a class="btn btn-secondary" 
               href="/?action=empty_cart" 
               onclick="return confirm('Sind Sie sicher, dass Sie den Warenkorb leeren möchten?');">
            <?= Warehouse::getLabel('cart_empty') ?>
            </a>
        </div>
    </div>
</div>
<!-- / cart/offcanvas_cart.php -->
