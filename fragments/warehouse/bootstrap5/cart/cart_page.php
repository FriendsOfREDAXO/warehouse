<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Shipping;

$cart = Cart::get();
$cart_items = $cart->getItems();

?>
<div class="container">
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="card">
                
                <?php if($cart->isEmpty()) { ?>
                    <div class="card-body">
                        <p class="text-center"><?= rex_i18n::msg('warehouse.cart_empty') ?></p>
                    </div>
                <?php } ?>
                <?php if (!$cart->isEmpty()) : ?>
                    <div class="card-header text-uppercase text-muted text-center text-small d-none d-md-block">
                        <div class="row row-cols-1 row-cols-md-2">
                            <div class="col"><?= Warehouse::getLabel('article') ?></div>
                            <div class="col">
                                <div class="row row-cols-auto">
                                    <div class="col"><?= Warehouse::getLabel('price') ?></div>
                                    <div class="col tm-quantity-column"><?= Warehouse::getLabel('quantity') ?></div>
                                    <div class="col"><?= Warehouse::getLabel('total') ?></div>
                                    <div class="col" style="width: 20px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php foreach ($cart_items as $uuid => $item) : ?>
                        <!-- Item -->
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 align-items-center">

                            <!-- Product cell-->
                                <div class="col">
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <?php if ($item['image']) : ?>
                                                <a class="" href="<?= rex_getUrl('','',['warehouse-article-id'=>$item['id']]) ?>">
                                                    <figure class=""><img src="/images/products/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="img-fluid"></figure>
                                                </a>
                                            <?php endif ?>
                                        </div>
                                        <div class="col">
                                            <div class="text-meta"><?= $item['cat_name'] ?></div>
                                            <a class="link-heading" href="<?= rex_getUrl('','',['warehouse-article-id'=>$item['id']]) ?>"><?= html_entity_decode($item['name']) ?>                               
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Other cells-->
                                <div class="col">
                                    <div class="row row-cols-1 row-cols-sm-auto text-center">
                                        <div class="col">
                                            <div class="text-muted d-md-none"><?= Warehouse::getLabel('price') ?></div>
                                            <div><?= rex_config::get('warehouse','currency_symbol').'&nbsp;'.number_format($item['price'],2) ?></div>                        
                                        </div>
                                        <div class="col">
                                            <a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uuid ?>&mod=-1" class="btn btn-sm"><i class="bi bi-dash"></i></a>
                                            <input class="form-control wh-qty-input" id="product-1" type="text" maxlength="3" value="<?= $item['amount'] ?>" disabled>
                                            <a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uuid ?>&mod=+1"  class="btn btn-sm"><i class="bi bi-plus"></i></a>
                                        </div>
                                        <div class="col">
                                            <div class="text-muted d-md-none"><?= Warehouse::getLabel('total') ?></div>
                                            <div><?= rex_config::get('warehouse','currency_symbol').'&nbsp;'.number_format($item['total'],2) ?></div>                        
                                        </div>
                                        <div class="col"><a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uuid ?>&mod=del" class="text-danger" data-bs-toggle="tooltip" data-bs-title="Remove"><i class="bi bi-x-circle"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- // Item -->
                    <?php endforeach ?>
                <?php endif ?>
                
            </div>
        </div>
        <?php if (!$cart->isEmpty()) : ?>
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col text-muted">Zwischensumme</div>
                            <div class="col"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format($cart->getSubTotal(),2) ?></div>
                        </div>
                        <div class="row">
                            <div class="col text-muted">Versand</div>
                            <div class="col text"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format((float) Shipping::getCost(),2) ?></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-muted">Total</div>
                            <div class="col text-lead fw-bolder"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= $cart->getCartTotalFormatted() ?></div>
                        </div>
                        <a class="btn btn-primary mt-3 w-100" href="<?= rex_getUrl(rex_config::get('warehouse','address_page')) ?>">checkout</a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <a href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>" class="btn btn-primary">Weiter</a>
            </div>
        <?php endif ?>

    </div>
</div>
<script nonce="<?= rex_response::getNonce() ?>">
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
  const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
