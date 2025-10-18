<?php

use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\ArticleVariant;
use FriendsOfRedaxo\Warehouse\Category;
use FriendsOfRedaxo\Warehouse\Warehouse;

/** @var rex_fragment $this */
$article = $this->getVar('article');
if (!$article instanceof Article) {
    return;
}
/** @var Category $category */
$category = $article->getCategory();
$variants = [];
if (Warehouse::isVariantsEnabled()) {
    $variants = $article->getVariants();
}

$bulkPrices = [];

if (Warehouse::isBulkPricesEnabled()) {
    $bulkPrices = $article->getBulkPrices();
}


?>
<div class="row" data-warehouse-article-detail>
    <div class="col-12">
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="card-body p-0">
                    <?php if ($article->getImageAsMedia()) : ?>
                        <img src="<?= $article->getImageAsMedia()->getUrl() ?>" class="img-fluid" alt="<?= htmlspecialchars($article->getName() ?? '') ?>">
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-12 col-md-8">

                <p>
                    <a href="<?= $category->getUrl() ?>" class="text-decoration-none">
                        <span class="badge bg-secondary"><?= htmlspecialchars($category->getName() ?? '') ?></span>
                    </a>
                </p>
                <h3><?= $article->getName() ?></h3>
                <p><?= htmlspecialchars($article->getShortText(true) ?? '') ?></p>

                <!-- Varianten -->
                <?php if (count($variants) > 1) :
                    ?>
                    <div class="mb-3">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <?php foreach ($variants as $variant) :
                                /** @var ArticleVariant $variant */ ?>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?= $k == 0 ? 'active' : '' ?>" 
                                            id="pills-<?= $variant->getId() ?>-tab" 
                                            data-bs-toggle="pill" 
                                            data-bs-target="#pills-<?= $variant->getId() ?>" 
                                            type="button" role="tab" 
                                            aria-controls="pills-<?= $variant->getId() ?>" 
                                            aria-selected="<?= $k == 0 ? 'true' : 'false' ?>" 
                                            data-warehouse-variant
                                            data-warehouse-variant-id="<?= $variant->getId() ?>"
                                            data-warehouse-variant-price="<?= $variant->getPrice() ?>"><?= $variant->getName() ?></button>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>
                <!-- / Varianten -->

                <!-- Staffelpreise -->
                <?php if (count($bulkPrices)) : ?>
                    <div class="mb-3">
                        <h4><?= Warehouse::getLabel('bulk_prices'); ?></h4>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><?= Warehouse::getLabel('amount'); ?></th>
                                    <th scope="col"><?= Warehouse::getLabel('price'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bulkPrices as $bulkPrice) : ?>
                                    <tr>
                                        <td><?= $bulkPrice['min'] ?> - <?= $bulkPrice['max'] ?></td>
                                        <td><?= $bulkPrice['price'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- / Staffelpreise -->


                <div class="row g-3">
                    <div class="col-12">
                        <?= html_entity_decode($article->getText() ?? '') ?>

                    </div>
                    
                <!-- Preis -->
                <div data-warehouse-price-display 
                     data-warehouse-base-price="<?= $article->getPrice() ?>"
                     data-warehouse-bulk-prices='<?= json_encode($bulkPrices) ?>'>
                    <span data-warehouse-price-value class="fs-3"><?= $article->getPriceFormatted() ?></span>
                    <p class="text-small mb-0"><?= Warehouse::getLabel('tax') ?> <a href="#shipping_modal" data-bs-toggle="modal"><?= Warehouse::getLabel('shipping_costs') ?></a></p>
                </div>
                <!-- / Preis -->

                    <div class="col-12">
                        <form data-warehouse-add-form data-warehouse-checkout-url="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>">
                            <input type="hidden" name="article_id" value="<?= $article->getId() ?>">
                            <div class="input-group mb-3">
                                <button class="btn btn-outline-primary" type="button" 
                                        data-warehouse-quantity-switch="-1"
                                        data-warehouse-quantity-input="warehouse_count_<?= $article->getId() ?>">[-]</button>
                                <input name="order_count" type="number" min="1" step="1" 
                                       class="form-control" 
                                       id="warehouse_count_<?= $article->getId() ?>" 
                                       data-warehouse-quantity-input
                                       value="1">
                                <button class="btn btn-outline-primary" type="button" 
                                        data-warehouse-quantity-switch="+1"
                                        data-warehouse-quantity-input="warehouse_count_<?= $article->getId() ?>">[+]</button>
                            </div>
                            <button type="submit" name="submit" value="cart" class="btn btn-secondary"><?= Warehouse::getLabel('add_to_cart') ?></button>
                            <?php if (Warehouse::getConfig('instant_checkout_enabled', 1)) : ?>
                            <button type="submit" name="submit" value="checkout" class="btn btn-primary"><?= Warehouse::getLabel('checkout_instant') ?></button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" nonce="<?= rex_response::getNonce() ?>"></script>
