<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Shipping;
use FriendsOfRedaxo\Warehouse\Warehouse;

$cart = Cart::get();
$cart_items = $cart->getItems();
$domain = Domain::getCurrent();
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
                <div class="d-flex justify-content-between">
                    <a class="btn btn-link text-danger"
                        href="/?action=empty_cart"
                        onclick="return confirm('<?= Warehouse::getLabel('cart_empty_confirm') ?>');">
                        <?= Warehouse::getLabel('cart_empty') ?>
                    </a>
                    <a class="btn btn-primary ms-auto" href="<?= $domain->getCheckoutUrl() ?>">
                        <?= Warehouse::getLabel('next') ?>
                    </a>
                </div>
            </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-info"><?= Warehouse::getLabel('cart_is_empty'); ?></div>
        <?php } ?>
    </div>
</div>
<!-- / cart/offcanvas_cart.php -->
