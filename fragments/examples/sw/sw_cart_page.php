<?php
$cart = $this->cart;
//dump($cart);
$showcart = $this->mode == 'template' && rex_request('showcart', 'int') ? 1 : 0;
$rex_article_id = rex_article::getCurrentId();
?>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main">
                    <div class="cart">
                        <div class="page-content page-order">
                            <div class="page-title mt30">
                                <h2>Ihr Warenkorb</h2>
                            </div><?php if (!$cart) : ?>

                            <p>{{ Der Warenkorb ist leer }}</p><?php else : ?>

                            <div class="order-detail-content">
                                <div class="table-responsive">
                                    <table class="table table-bordered  cart_summary">
                                        <thead>
                                            <tr>
                                                <th class="cart_product">Artikel</th>

                                                <th>Beschreibung</th>

                                                <th class="text-center">Einzelpreis <?= rex_config::get('warehouse', 'currency_symbol') ?>
                                                </th>

                                                <th class="text-center">Anzahl</th>

                                                <th class="text-center">Gesamt <?= rex_config::get('warehouse', 'currency_symbol') ?>
                                                </th>

                                                <th class="action"><i class="fa fa-trash-o"></i></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($cart as $k => $item) : ?>
                                            
                                            <?php //dump($item); ?>

                                            <tr>
                                                <td class="cart_product"><img src="/index.php?rex_media_type=rex_product_main&rex_media_file=<?= $item['image'] ?>" alt="<?= html_entity_decode($item['name']) ?>"></td>

                                                <td class="cart_description">
                                                    <p class="product-name"><?= html_entity_decode($item['name']) ?></p>
                                                    <small>Artikelnr.: <?= $item['var_whvarid'] ?></small> 
                                                </td>

                                                <td class="price"><span><?= number_format($item['price'], 2) ?>
                                                </span></td>

                                                <td class="qty"><a href="/index.php?current_article=<?= $rex_article_id ?>&showcart=<?= $showcart ?>&action=modify_cart&art_uid=<?= $k ?>&mod=-1" class="button tiny">-</a> <span class="countnum"><?= $item['count'] ?>
                                                </span> <a href="/index.php?current_article=<?= $rex_article_id ?>&showcart=<?= $showcart ?>&action=modify_cart&art_uid=<?= $k ?>&mod=+1" class="button tiny">+</a></td>

                                                <td class="price"><span><?= number_format($item['total'], 2) ?>
                                                </span></td>

                                                <td class="action"><a href="/index.php?current_article=<?= $rex_article_id ?>&showcart=<?= $showcart ?>&action=modify_cart&art_uid=<?= $k ?>&mod=del" class="button"><i class="icon-close"></i></a></td>
                                            </tr><?php endforeach; ?>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td colspan="2"></td>

                                                <td colspan="3"><span class="fontbold">Summe netto</span></td>

                                                <td colspan="2"><span class="fontbold"><?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getSubTotalNetto(), 2) ?>
                                                <?= rex_config::get('warehouse', 'currency_symbol') ?></span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"></td>

                                                <td colspan="3" class="lighter"><i>MwSt 19%</i>
                                                </td>

                                                <td colspan="2" class="lighter"><i><?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getTaxTotal(), 2) ?></i>
                                                <?= rex_config::get('warehouse', 'currency_symbol') ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"></td>

                                                <td colspan="3"><span class="fontbold">Zwischensumme</span></td>

                                                <td colspan="2"><span class="fontbold"><?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getSubTotal(), 2) ?>
                                                <?= rex_config::get('warehouse', 'currency_symbol') ?></span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"></td>

                                                <td colspan="3" class="lighter"><i>{{ Versandkosten }}</i></td>

                                                <td colspan="2" class="lighter"><i><?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getShippingCost(), 2) ?>
                                                <?= rex_config::get('warehouse', 'currency_symbol') ?></i>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"></td>

                                                <td colspan="3"><span class="fontbold fontred">{{ Total }}</span></td>

                                                <td colspan="2">
	                                                <span class="fontbold fontred"><?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getCartTotal(), 2) ?>
                                                 <?= rex_config::get('warehouse', 'currency_symbol') ?>
												 	</span>
												</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="cart_navigation">
                                    <?php if (rex_session('current_page')) : ?> 
                                    <a href="<?= FriendsOfRedaxo\Warehouse\Warehouse::clean_url(rex_session('current_page')) ?>" class="continue-btn"><i class="fa fa-arrow-left"></i> Zurück</a>
                                    <?php endif ?> 
                                    <a href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>" class="checkout-btn"><i class="fa fa-arrow-right"></i> Weiter zur Kasse </a>
                                </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
