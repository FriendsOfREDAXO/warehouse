<?php

/** @var rex_fragment $this */

?>
<div>
    <div class="uk-grid-medium" uk-grid>

        <div class="uk-width-expand">

            
            <div class="uk-grid-medium uk-child-width-1-1" uk-grid>
                <div>
                    <div class="uk-card uk-card-default uk-card-small tm-ignore-container">
                        <div class="uk-grid-collapse uk-child-width-1-1" id="products" uk-grid>
                            <div>
                                <div class="uk-grid-collapse uk-child-width-1-3 tm-products-grid js-products-grid" uk-grid>
                                    <?php foreach ($this->articles as $item) : ?>
                                        <?php $link = isset($item->var_id) ? rex_getUrl('', '', ['warehouse_art_id' => $item->id, 'var_id' => $item->var_id]) : rex_getUrl('', '', ['warehouse_art_id' => $item->id]); ?>
                                        <article class="tm-product-card">
                                            <div class="tm-product-card-media">
                                                <div class="tm-ratio tm-ratio-4-3">
                                                    <a class="tm-media-box" href="<?= $link; ?>">
                                                        <div class="tm-product-card-labels"></div>
                                                        <?php if ($item->get_image()) : ?>
                                                            <figure class="tm-media-box-wrap"><img src="/images/whlist/<?= $item->get_image() ?>" alt="<?= $item->get_name() ?>" /></figure>
                                                        <?php else : ?>
                                                            <figure class="tm-media-box-wrap"><span class="uk-text-muted uk-icon" uk-icon="icon: image; ratio: 3;"><svg width="60" height="60" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"> <circle cx="16.1" cy="6.1" r="1.1"></circle> <rect fill="none" stroke="#000" x="0.5" y="2.5" width="19" height="15"></rect> <polyline fill="none" stroke="#000" stroke-width="1.01" points="4,13 8,9 13,14"></polyline> <polyline fill="none" stroke="#000" stroke-width="1.01" points="11,12 12.5,10.5 16,14"></polyline></svg></span></figure>
                                                        <?php endif ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="tm-product-card-body">
                                                <div class="tm-product-card-info">
                                                    <div class="uk-text-meta uk-margin-xsmall-bottom"><?= $item->cat_name ?></div>
                                                    <h3 class="tm-product-card-title"><a class="uk-link-heading" href="<?= $link ?>"><?= $item->get_name() ?></a></h3>
                                                    <p><?= $item->art_description ?></p>
                                                </div>
                                                <div class="tm-product-card-shop">
                                                    <div class="tm-product-card-prices">
                                                        <div class="tm-product-card-price"><?= $item->get_price(true) ?></div>
                                                    </div>
                                                    <div class="tm-product-card-add">
                                                        <div class="uk-text-meta tm-product-card-actions">

                                                        </div><a class="uk-button uk-button-primary tm-product-card-add-button tm-shine js-add-to-cart" href="<?= rex_getUrl('', '', ['art_id' => $item->get_art_id(), 'action' => 'add_to_cart', 'order_count' => 1]) ?>"><span class="tm-product-card-add-button-icon" uk-icon="cart"></span><span class="tm-product-card-add-button-text">add to cart</span></a></div>
                                                </div>
                                            </div>
                                        </article>

                                    <?php endforeach ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
