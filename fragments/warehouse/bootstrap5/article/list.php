<!-- BEGIN article_list -->
<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\Category;

/** @var Category|null $category */
$category = $this->getVar('category');

if (null === $category) {
    return;
}
$articles = $category->getArticles('active', 48, 0);

?>
<div class="row row-cols-1 row-cols-md-2 g-4">
	<?php if (!$articles || 0 === count($articles)) : ?>
	<div class="alert alert-info" role="alert">
		<?= rex_i18n::msg('warehouse_no_articles') ?>
	</div>
	<?php endif ?>
	<?php foreach ($articles as $article) : ?>
	<?php
        /** @var Article $article */
        $link = rex_getUrl('', '', ['warehouse-article-id' => $article->getId()]);
        $image = $article->getImage();
        $teaser = $article->getShortText();
        $imageUrl = $article->getImageAsMedia()->getUrl() ?? '';
        ?>
	<div class="col">
		<div class="card h-100">
			<div class="row g-0 align-items-center">
				<!-- Image column -->
				<div class="col-12 col-md-4">
					<a href="<?= $link ?>">
						<img src="<?= $imageUrl ?>"
							class="img-fluid rounded-start w-100"
							alt="<?= htmlspecialchars($article->getName() ?? '') ?>">
					</a>
				</div>
				<!-- Content column -->
				<div class="col-12 col-md-8">
					<div class="card-body">
						<p class="card-text mb-0">
							<span
								class="badge bg-secondary"><?= htmlspecialchars($category->getName()) ?></span>
						</p>
						<h5 class="card-title mb-2">
							<a
								href="<?= $link ?>"><?= htmlspecialchars($article->getName() ?? '') ?></a>
						</h5>
						<p class="card-text mb-2">
							<?= htmlspecialchars($article->getShortText(true) ?? '') ?>
						</p>
						<div class="mt-2">
							<span
								class="fw-bold"><?= $article->getPriceFormatted() ?></span>
						</div>
						<a href="<?= rex_getUrl('', '', ['art_id' => $article->getId(), 'action' => 'add_to_cart', 'order_count' => 1]) ?>"
							class="btn btn-sm btn-outline-secondary mb-md-2">In den Warenkorb</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach ?>
</div>
<!-- END article_list -->
