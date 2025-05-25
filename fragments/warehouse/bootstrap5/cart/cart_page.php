<?php

/** @var rex_fragment $this */

?>
<div class="container">
    <div class="row">
        <div class="col-12 col-md-8">
            <div class="card">
                <?php if ($this->cart) : ?>
                    <div class="card-header text-uppercase text-muted text-center text-small d-none d-md-block">
                        <div class="row row-cols-1 row-cols-md-2">
                            <div class="col">Artikel</div>
                            <div class="col">
                                <div class="row row-cols-auto">
                                    <div class="col">Preis</div>
                                    <div class="col tm-quantity-column">Menge</div>
                                    <div class="col">Summe</div>
                                    <div class="col" style="width: 20px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php foreach ($this->cart as $uid=>$item) : ?>
                        <?php $base_id = explode('__',$item['art_id'])[0] ?>
                        <!-- Item -->
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 align-items-center">
                                <!-- Product cell-->
                                <div class="col">
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                            <?php if ($item['image']) : ?>
                                                <a class="tm-media-box" href="<?= rex_getUrl('','',['warehouse_art_id'=>$base_id]) ?>">
                                                    <figure class="tm-media-box-wrap"><img src="/images/products/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="img-fluid"></figure>
                                                </a>
                                            <?php endif ?>
                                        </div>
                                        <div class="col">
                                            <div class="text-meta"><?= $item['cat_name'] ?></div>
                                            <a class="link-heading" href="<?= rex_getUrl('','',['warehouse_art_id'=>$base_id]) ?>"><?= html_entity_decode($item['name']) ?>
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
                                <div class="col">
                                    <div class="row row-cols-1 row-cols-sm-auto text-center">
                                        <div class="col">
                                            <div class="text-muted d-md-none">Preis</div>
                                            <div><?= rex_config::get('warehouse','currency_symbol').'&nbsp;'.number_format($item['price'],2) ?></div>                        
                                        </div>
                                        <div class="col wh-cart-quantity-column">
                                            <a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uid ?>&mod=-1" class="btn btn-sm"><i class="bi bi-dash"></i></a>
                                            <input class="form-control wh-qty-input" id="product-1" type="text" maxlength="3" value="<?= $item['amount'] ?>" disabled>
                                            <a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uid ?>&mod=+1"  class="btn btn-sm"><i class="bi bi-plus"></i></a>
                                        </div>
                                        <div class="col">
                                            <div class="text-muted d-md-none">Summe</div>
                                            <div><?= rex_config::get('warehouse','currency_symbol').'&nbsp;'.number_format($item['total'],2) ?></div>                        
                                        </div>
                                        <div class="col"><a href="/?current_article=<?= rex_article::getCurrentId() ?>&action=modify_cart&art_uid=<?= $uid ?>&mod=del" class="text-danger" data-bs-toggle="tooltip" data-bs-title="Remove"><i class="bi bi-x-circle"></i></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- // Item -->
                    <?php endforeach ?>
                <?php else : ?>
                    <div class="card-body">
                        <h3>Der Warenkorb ist leer.</h3>
                    </div>
                <?php endif ?>
                
            </div>
        </div>
        <?php if ($this->cart) : ?>
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col text-muted">Zwischensumme</div>
                            <div class="col"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getSubTotal(),2) ?></div>
                        </div>
                        <div class="row">
                            <div class="col text-muted">Versand</div>
                            <div class="col text"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format((float) FriendsOfRedaxo\Warehouse\Warehouse::getShippingCost(),2) ?></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-muted">Total</div>
                            <div class="col text-lead fw-bolder"><?= rex_config::get('warehouse','currency_symbol') ?>&nbsp;<?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getCartTotal(),2) ?></div>
                        </div>
                        <a class="btn btn-primary mt-3 w-100" href="<?= rex_getUrl(rex_config::get('warehouse','address_page')) ?>">checkout</a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <?php if (rex_session('current_page')) : ?>
                    <a href="<?= FriendsOfRedaxo\Warehouse\Warehouse::clean_url(rex_session('current_page')) ?>" class="btn btn-primary">Zurück</a>&nbsp;&nbsp;
                <?php endif ?>
                <a href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>" class="btn btn-primary">Weiter</a>
            </div>
        <?php else : ?>
            <div class="col-12">
                <?php if (rex_session('current_page')) : ?>
                    <a href="<?= FriendsOfRedaxo\Warehouse\Warehouse::clean_url(rex_session('current_page')) ?>" class="btn btn-primary">Zurück</a>&nbsp;&nbsp;
                <?php endif ?>
            </div>
        <?php endif ?>

    </div>
</div>
<script>
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
  const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
