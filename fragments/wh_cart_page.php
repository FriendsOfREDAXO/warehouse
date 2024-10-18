<?php
/*
  https://chekromul.github.io/uikit-ecommerce-template
 *   */
?>
<div class="uk-grid-margin">
    <div class="uk-grid-medium uk-grid" uk-grid="">
        <div class="uk-width-1-1 uk-width-expand@m">
            <div class="uk-card uk-card-default uk-card-small tm-ignore-container">
                <?php if ($this->cart) : ?>
                    <header class="uk-card-header uk-text-uppercase uk-text-muted uk-text-center uk-text-small uk-visible@m">
                        <div class="uk-grid-small uk-child-width-1-2 uk-grid" uk-grid="">
                            <div>Artikel</div>
                            <div>
                                <div class="uk-grid-small uk-child-width-expand uk-grid" uk-grid="">
                                    <div>Preis</div>
                                    <div class="tm-quantity-column">Menge</div>
                                    <div>Summe</div>
                                    <div class="uk-width-auto">
                                        <div style="width: 20px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    <?php foreach ($this->cart as $uid=>$item) : ?>
                        <?php $base_id = explode('__',$item['art_id'])[0] ?>
                        <!-- Item -->
                        <div class="uk-card-body">
                            <div class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m uk-flex-middle uk-grid" uk-grid="">
                                <!-- Product cell-->
                                <div>
                                    <div class="uk-grid-small uk-grid" uk-grid="">
                                        <div class="uk-width-1-1 uk-width-1-3@s">
                                            <?php if ($item['image']) : ?>
                                                <a class="tm-media-box" href="<?= rex_getUrl('','',['warehouse_art_id'=>$base_id]) ?>">
                                                    <figure class="tm-media-box-wrap"><img src="/images/products/<?= $item['image'] ?>" alt="<?= $item['name'] ?>"></figure>
                                                </a>
                                            <?php endif ?>
                                        </div>
                                        <div class="uk-width-expand">
                                            <div class="uk-text-meta"><?= $item['cat_name'] ?></div>
                                            <a class="uk-link-heading" href="<?= rex_getUrl('','',['warehouse_art_id'=>$base_id]) ?>"><?= html_entity_decode($item['name']) ?>
                                            <?php $attr_text = []; ?>
                                            <?php foreach ($item['attributes'] as $attr) : ?>
                                                <?php $attr_text[] = $attr['value'] ?>
                                            <?php endforeach ?>
                                            <?= implode(' - ',$attr_text) ?>                                    
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Other cells-->
                                <div>
                                    <div class="uk-grid-small uk-child-width-1-1 uk-child-width-expand@s uk-text-center uk-grid" uk-grid="">
                                        <div>
                                            <div class="uk-text-muted uk-hidden@m">{{ Price }}</div>
                                            <div><?= rex_config::get('warehouse','currency_symbol').'&nbsp;'.number_format($item['price'],2) ?></div>                        
                                        </div>
                                        <div class="wh-cart-quantity-column">
                                            <a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uid ?>&mod=-1" uk-icon="icon: minus; ratio: .75" class="uk-icon"></a>
                                            <input class="uk-input wh-qty-input" id="product-1" type="text" maxlength="3" value="<?= $item['count'] ?>" disabled>
                                            <a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uid ?>&mod=+1" uk-icon="icon: plus; ratio: .75" class="uk-icon"></a>
                                        </div>
                                        <div>
                                            <div class="uk-text-muted uk-hidden@m">Sum</div>
                                            <div><?= rex_config::get('warehouse','currency_symbol').'&nbsp;'.number_format($item['total'],2) ?></div>                        
                                        </div>
                                        <div class="uk-width-auto@s"><a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uid ?>&mod=del" class="uk-text-danger" uk-tooltip="Remove" title="" aria-expanded="false"><span uk-icon="close" class="uk-icon"></span></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- // Item -->
                    <?php endforeach ?>
                <?php else : ?>
                    <div class="uk-card-body">
                        <h3>Der Warenkorb ist leer.</h3>
                    </div>
                <?php endif ?>
                
            </div>
        </div>
        <?php if ($this->cart) : ?>
            <div class="uk-width-1-1 tm-aside-column uk-width-1-4@m">
                <div class="uk-card uk-card-default uk-card-small tm-ignore-container uk-sticky" uk-sticky="offset: 30; bottom: true; media: @m;">
                    <div class="uk-card-body">
                        <div class="uk-grid-small uk-grid" uk-grid="">
                            <div class="uk-width-expand uk-text-muted">{{ Subtotal }}</div>
                            <div><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::get_sub_total(),2) ?></div>
                        </div>
                        <div class="uk-grid-small uk-grid" uk-grid="">
                            <div class="uk-width-expand uk-text-muted">{{ Shipping }}</div>
                            <div class="uk-text"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format((float) FriendsOfRedaxo\Warehouse\Warehouse::get_shipping_cost(),2) ?></div>
                        </div>
                    </div>
                    <div class="uk-card-body">
                        <div class="uk-grid-small uk-flex-middle uk-grid" uk-grid="">
                            <div class="uk-width-expand uk-text-muted">{{ Total }}</div>
                            <div class="uk-text-lead uk-text-bolder"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::GetCartTotal(),2) ?></div>
                        </div>
                        <a class="uk-button uk-button-primary uk-margin-small uk-width-1-1" href="<?= rex_getUrl(rex_config::get('warehouse','address_page')) ?>">checkout</a>
                    </div>
                </div>
                <div class="uk-sticky-placeholder" hidden="" style="height: 230px; margin: 0px;"></div>
            </div>
            <div class="uk-width-1-1">
                <?php if (rex_session('current_page')) : ?>
                    <a href="<?= FriendsOfRedaxo\Warehouse\Warehouse::clean_url(rex_session('current_page')) ?>" class="uk-button uk-button-primary">Zurück</a>&nbsp;&nbsp;
                <?php endif ?>
                <a href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>" class="uk-button uk-button-primary">Weiter</a>
            </div>
        <?php else : ?>
            <div class="uk-width-1-1">
                <?php if (rex_session('current_page')) : ?>
                    <a href="<?= FriendsOfRedaxo\Warehouse\Warehouse::clean_url(rex_session('current_page')) ?>" class="uk-button uk-button-primary">Zurück</a>&nbsp;&nbsp;
                <?php endif ?>
            </div>
        <?php endif ?>

    </div>
</div>
