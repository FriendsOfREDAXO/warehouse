<div id="cart-offcanvas"  uk-offcanvas="flip: true">
    <button class="uk-offcanvas-close" type="button" uk-close></button>
    <header class="uk-card-header uk-flex uk-flex-middle">
        <div class="uk-grid-small uk-flex-1" uk-grid>
            <div class="uk-width-expand">
                <div class="uk-h3">{{ Cart }}</div>
            </div>                
        </div>
    </header>
    
    <?php if ($this->cart) :  // ==== Warenkorb ====  ?>
        <div class="uk-card-body uk-overflow-auto">
            <ul class="uk-list uk-list-divider">
                <?php foreach ($this->cart as $k => $item) : ?>
                    <?php $base_id = explode('__', $item['art_id'])[0] ?>
                    <li class="uk-visible-toggle">
                        <article>
                            <div class="uk-grid-small" uk-grid>
                                <div class="uk-width-1-4">
                                    <div class="tm-ratio tm-ratio-4-3">
                                        <?php if ($item['image']) : ?> 
                                            <a class="tm-media-box" href="<?= rex_getUrl('', '', ['warehouse_art_id' => $base_id]) ?>">
                                                <figure class="tm-media-box-wrap"><img src="/images/cartthumb/<?= $item['image'] ?>" alt="<?= $item['name'] ?>"></figure>
                                            </a>
                                        <?php endif ?>
                                    </div>
                                </div>
                                <div class="uk-width-expand">
                                    <div class="uk-text-meta uk-text-xsmall"><?= $item['cat_name'] ?></div>
                                    <a class="uk-link-heading uk-text-small" href="<?= rex_getUrl('', '', ['warehouse_art_id' => $base_id]) ?>"><?= trim($item['name'],'- ') ?>
                                    <?php $attr_text = []; ?>
                                    <?php foreach ($item['attributes'] as $attr) : ?>
                                        <?php $attr_text[] = $attr['value'] ?>
                                    <?php endforeach ?>
                                    <?= implode(' - ',$attr_text) ?> 
                                    </a>
                                    <div class="uk-margin-xsmall uk-grid-small uk-flex-middle" uk-grid>
                                        <div class="uk-text-bolder uk-text-small"><?= rex_config::get('warehouse', 'currency_symbol') ?>&nbsp;<?= number_format($item['total'], 2) ?></div>
                                        <div class="uk-text-meta uk-text-xsmall"><?= $item['count'] ?> &times; <?= rex_config::get('warehouse', 'currency_symbol') ?>&nbsp;<?= number_format($item['price'], 2) ?></div>
                                    </div>
                                </div>
                                <div><a class="uk-icon-link uk-text-danger uk-invisible-hover" href="/?showcart=1&action=modify_cart&art_uid=<?= $k ?>&mod=del" uk-icon="icon: close; ratio: .75" uk-tooltip="Remove"></a></div>
                            </div>
                        </article>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
        <footer class="uk-card-footer">
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-expand uk-text-muted uk-h4">Subtotal</div>
                <div class="uk-h4 uk-text-bolder"><?= rex_config::get('warehouse', 'currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::get_sub_total(), 2) ?></div>
            </div>
            <div class="uk-grid-small uk-grid" uk-grid="">
                <div class="uk-width-expand uk-text-muted">{{ Shipping }}</div>
                <div class="uk-text"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::get_shipping_cost(),2) ?></div>
            </div>
            <div class="uk-grid-small uk-flex-middle uk-grid" uk-grid="">
                <div class="uk-width-expand uk-text-muted">{{ Total }}</div>
                <div class="uk-text-lead uk-text-bolder"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::GetCartTotal(),2) ?></div>
            </div>           
            
            
            <div class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m uk-margin-small" uk-grid>
                <div><a class="uk-button uk-button-default uk-margin-small uk-width-1-1" href="<?= rex_getUrl(rex_config::get('warehouse', 'cart_page')) ?>">{{ view cart }}</a></div>
                <div><a class="uk-button uk-button-primary uk-margin-small uk-width-1-1" href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>">{{ checkout }}</a></div>
            </div>
        </footer>
    <?php else : ?>
        <div class="uk-card-body uk-overflow-auto">
            <div class="uk-h3 alert-info">{{ Der Warenkorb ist leer }}</div>
        </div>
    <?php endif ?>
</div>
