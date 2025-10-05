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
                <div id="warehouse_art_price" data-price="<?= $article->getPrice() ?>">
                    <span class="fs-3"><?= $article->getPriceFormatted() ?></span>
                    <p class="text-small mb-0"><?= Warehouse::getLabel('tax') ?> <a href="#shipping_modal" data-bs-toggle="modal"><?= Warehouse::getLabel('shipping_costs') ?></a></p>
                </div>
                <!-- / Preis -->

                    <div class="col-12">
                        <form id="warehouse_form_detail">
                            <input type="hidden" name="article_id" value="<?= $article->getId() ?>">
                            <div class="input-group mb-3">
                                <button class="btn btn-outline-primary switch_count" type="button" data-value="-1">[-]</button>
                                <input name="order_count" type="number" min="1" step="1" class="form-control" id="warehouse_count_<?= $article->getId() ?>" value="1">
                                <button class="btn btn-outline-primary switch_count" type="button" data-value="+1">[+]</button>
                            </div>
                            <button type="submit" name="submit" value="cart" class="btn btn-secondary"><?= Warehouse::getLabel('add_to_cart') ?></button>
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
        const priceElement = document.getElementById('warehouse_art_price');
        const basePrice = parseFloat(priceElement.dataset.price);
        const bulkPrices = <?= json_encode($bulkPrices) ?>;
        
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
                    
                    // Update price based on tier pricing
                    updatePriceDisplay(currentValue);
                }
            });
        });
        
        // Also update price when input value changes directly
        const input = document.getElementById('warehouse_count_<?= $article->getId() ?>');
        input.addEventListener('input', function() {
            const quantity = parseInt(this.value, 10) || 1;
            updatePriceDisplay(quantity);
        });
        
        function updatePriceDisplay(quantity) {
            let pricePerUnit = basePrice;
            let totalPrice = basePrice * quantity;
            
            // Check if bulk pricing applies
            if (bulkPrices && bulkPrices.length > 0) {
                for (const bulkPrice of bulkPrices) {
                    if (quantity >= bulkPrice.min && (bulkPrice.max === null || quantity <= bulkPrice.max)) {
                        pricePerUnit = parseFloat(bulkPrice.price);
                        totalPrice = pricePerUnit * quantity;
                        break;
                    }
                }
            }
            
            // Format price
            const formatter = new Intl.NumberFormat('de-DE', {
                style: 'currency',
                currency: 'EUR'
            });
            
            // Update the price display
            const priceSpan = priceElement.querySelector('span.fs-3');
            if (priceSpan) {
                if (quantity === 1) {
                    priceSpan.textContent = formatter.format(pricePerUnit);
                } else {
                    priceSpan.innerHTML = `${formatter.format(pricePerUnit)} × ${quantity} = <strong>${formatter.format(totalPrice)}</strong>`;
                }
            }
        }
    });
</script>
<script nonce="<?= rex_response::getNonce() ?>">
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('warehouse_form_detail');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const articleId = formData.get('article_id');
                const orderCount = formData.get('order_count');
                // Optional: Variantenauswahl berücksichtigen
                let variantId = null;
                const activeVariant = document.querySelector('.nav-link.active[data-art_id]');
                if (activeVariant) {
                    variantId = activeVariant.getAttribute('data-art_id');
                }
                // Ziel-Action bestimmen
                let action = 'add';
                if (formData.get('submit') === 'checkout') {
                    action = 'add'; // oder ggf. andere Logik
                }
                // API-URL zusammenbauen
                let url = `index.php?rex-api-call=warehouse_cart_api&action=${action}`;
                url += `&article_id=${encodeURIComponent(articleId)}`;
                if (variantId) {
                    url += `&variant_id=${encodeURIComponent(variantId)}`;
                }
                url += `&amount=${encodeURIComponent(orderCount)}`;
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Optional: UI-Feedback, z.B. Button-Text ändern
                    form.querySelector('button[type="submit"][name="submit"]:focus')?.blur();
                })
                .catch(() => {
                    console.error('Fehler beim Hinzufügen zum Warenkorb.');
                    alert('Fehler beim Hinzufügen zum Warenkorb.');
                });
            });
        }
    });
</script>
