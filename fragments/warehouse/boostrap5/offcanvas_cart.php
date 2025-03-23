<?php

/** @var rex_fragment $this */

?>
<div class="offcanvas offcanvas-flip" tabindex="-1" id="cart-offcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">{{ Cart }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php if ($this->cart) :  // ==== Warenkorb ====  ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($this->cart as $k => $item) : ?>
                    <?php $base_id = explode('__', $item['art_id'])[0] ?>
                    <li class="list-group-item">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <div class="ratio ratio-4x3">
                                    <?php if ($item['image']) : ?>
                                        <a href="<?= rex_getUrl('', '', ['warehouse_art_id' => $base_id]) ?>">
                                            <img src="/images/cartthumb/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="img-fluid">
                                        </a>
                                    <?php endif ?>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-muted small"><?= $item['cat_name'] ?></div>
                                <a class="link-heading stretched-link text-decoration-none small" href="<?= rex_getUrl('', '', ['warehouse_art_id' => $base_id]) ?>"><?= trim($item['name'], '- ') ?>
                                    <?php $attr_text = []; ?>
                                    <?php foreach ($item['attributes'] as $attr) : ?>
                                        <?php $attr_text[] = $attr['value'] ?>
                                    <?php endforeach ?>
                                    <?= implode(' - ', $attr_text) ?>
                                </a>
                                <div class="mt-1 row g-2 align-items-center">
                                    <div class="col-auto fw-bolder small"><?= rex_config::get('warehouse', 'currency_symbol') ?>&nbsp;<?= number_format($item['total'], 2) ?></div>
                                    <div class="col-auto text-muted small"><?= $item['count'] ?> &times; <?= rex_config::get('warehouse', 'currency_symbol') ?>&nbsp;<?= number_format($item['price'], 2) ?></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a class="text-danger" href="/?showcart=1&action=modify_cart&art_uid=<?= $k ?>&mod=del" title="Remove">
                                    <i class="bi bi-x-circle-fill"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endforeach ?>
            </ul>
        
            <div class="mt-3">
                <div class="row g-2">
                    <div class="col text-muted h4">Subtotal</div>
                    <div class="col-auto h4 fw-bolder"><?= rex_config::get('warehouse', 'currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getSubTotal(), 2) ?></div>
                </div>
                <div class="row g-2">
                    <div class="col text-muted">{{ Shipping }}</div>
                    <div class="col-auto"><?= rex_config::get('warehouse', 'currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Shipping::getCost(), 2) ?></div>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col text-muted">{{ Total }}</div>
                    <div class="col-auto h5 fw-bolder"><?= rex_config::get('warehouse', 'currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getCartTotal(), 2) ?></div>
                </div>
            </div>
        
            <div class="d-grid gap-2 mt-3">
                <a class="btn btn-outline-secondary" href="<?= rex_getUrl(rex_config::get('warehouse', 'cart_page')) ?>">{{ view cart }}</a>
                <a class="btn btn-primary" href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>">{{ checkout }}</a>
            </div>
        <?php else : ?>
            <div class="alert alert-info">{{ Der Warenkorb ist leer }}</div>
        <?php endif ?>
    </div>
</div>
