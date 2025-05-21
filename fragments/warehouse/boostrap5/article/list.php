<!-- BEGIN article_list -->
<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Article;

?>
<div class="row row-cols-1 row-cols-md-2 g-4">
    <?php if (empty($this->articles)) : ?>
        <div class="alert alert-info" role="alert">
            <?= rex_i18n::msg('warehouse_no_articles') ?>
        </div>
    <?php endif; ?>
    <?php foreach ($this->articles as $item) : ?>
        <?php
        $link = rex_getUrl('', '', ['warehouse_art_id' => $item->getId()]);
        $image = $item->getImage();
        $teaser = $item->getTeaser();
        $imageUrl = $image ? '/media/' . $image : 'https://via.placeholder.com/300x200?text=No+Image';
        $category = $item->getCategory();
        $categoryName = $category ? $category->getValue('name') : '';
        ?>
        <div class="col">
            <div class="card h-100">
                <a href="<?= $link; ?>">
                    <img src="<?= $imageUrl ?>" class="card-img-top" alt="<?= htmlspecialchars($item->getName()) ?>">
                </a>
                <div class="card-body">
                    <h5 class="card-title"><a href="<?= $link ?>"><?= htmlspecialchars($item->getName()) ?></a></h5>
                    <p class="card-text"><?= htmlspecialchars($item->getShortText(true)) ?></p>
                    <p class="card-text"><small class="text-muted"><?= htmlspecialchars($categoryName) ?></small></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group">
                            <a href="<?= rex_getUrl('', '', ['art_id' => $item->getId(), 'action' => 'add_to_cart', 'order_count' => 1]) ?>" class="btn btn-sm btn-outline-secondary">In den Warenkorb</a>
                        </div>
                        <small class="text-muted"><?= $item->getPriceFormatted() ?></small>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>
<!-- END article_list -->
