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
<div class="row">
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
                                    <button class="nav-link <?= $k == 0 ? 'active' : '' ?>" id="pills-<?= $variant->getId() ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-<?= $variant->getId() ?>" type="button" role="tab" aria-controls="pills-<?= $variant->getId() ?>" aria-selected="<?= $k == 0 ? 'true' : 'false' ?>" data-price="<?= $variant->getPrice() ?>" data-art_id="<?= $variant->getId() ?>"><?= $variant->getName() ?></button>
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
                                <?php foreach ($bulkPrices as $price) : ?>
                                    <tr>
                                        <td><?= $price->getMinCount() ?> - <?= $price->getMaxCount() ?></td>
                                        <td><?= $price->getPriceFormatted() ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- / Staffelpreise -->

                <!-- Preis -->
                <div id="warehouse_art_price" data-price="<?= $article->getPrice() ?>">
                    <?= $article->getPriceFormatted() ?>
                </div>
                <!-- / Preis -->

                <div class="row g-3">
                    <div class="col-12">
                        <?= html_entity_decode($article->getText() ?? '') ?>

                    </div>
                    <div class="col-12">
                        <form action="" method="post" id="warehouse_form_detail">
                            <input type="hidden" name="article_id" value="<?= $article->getId() ?>">
                            <input type="hidden" name="action" value="add_to_cart">
                            <p class="text-small mb-0">inkl. MwSt. zzgl. <a href="#shipping_modal" data-bs-toggle="modal"><?= Warehouse::getLabel('shipping_costs') ?></a></p>
                            <div class="input-group mb-3">
                                <button class="btn btn-outline-primary switch_count" type="button" data-value="-1">[-]</button>
                                <input name="order_count" type="number" min="1" , step="1" class="form-control" id="warehouse_count_<?= $article->getId() ?>" value="1">
                                <button class="btn btn-outline-primary switch_count" type="button" data-value="+1">[+]</button>
                            </div>
                            <button type="submit" name="submit" value="cart" class="btn btn-secondary"><?= Warehouse::getLabel('cart') ?></button>
                            <button type="submit" name="submit" value="checkout" class="btn btn-primary"><?= Warehouse::getLabel('checkout_instant') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Füge Javascript hinzu, das die +/- Steuerung der Anzahl im Formular ermöglicht -->
<script nonce="<?= rex_response::getNonce() ?>">
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.switch_count');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const input = document.getElementById('warehouse_count_<?= $article->getId() ?>');
                let currentValue = parseInt(input.value, 10);
                const changeValue = parseInt(this.getAttribute('data-value'), 10);
                if (!isNaN(currentValue)) {
                    currentValue += changeValue;
                    if (currentValue < 1) {
                        currentValue = 1; // Mindestwert auf 1 setzen
                    }
                    input.value = currentValue;
                }
            });
        });
    });
</script>
