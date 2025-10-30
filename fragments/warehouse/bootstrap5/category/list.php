<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Category;
use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\ArticleVariant;
use FriendsOfRedaxo\Warehouse\Media;

$categories = Category::findRootCategories('active');
?>

<section>
	<?php

foreach ($categories as $category) {
    /** @var Category $category */
    $articles = $category?->getArticles('active', 8);
    if ($articles && count($articles) > 0) {
        ?>
	<div class="row">
		<div class="col-12">
			<h2 class="mt-5 mb-2 fw-bold">
				<a
					href="<?= $category?->getUrl() ?? '' ?>">
					<?= $category?->getName() ?? '' ?>
				</a>
			</h2>
		</div>
	</div>
	<div class="row">
		<?php foreach ($articles as $article) {
		    /** @var Article $article */
		    ?>
		<div class="col-sm-6 col-md-4 col-lg-3">
			<div class="card">
				<a href="<?= $article->getUrl() ?>">
					<?php if ($article->getImage() && $media = Media::get($article->getImage())): ?>
					<?= $media->setAlt($article->getName())
						->setClass('img-fluid card-img-top')
						->getImg('warehouse-category-list') ?>
					<?php endif; ?>
				</a>
				<div class="card-body">
					<a href="<?= $article->getUrl(); ?>"
						class="card-title"><?= $article->getName() ?></a>
					<br />
					<?php foreach ($article?->getVariants() ?? [] as $variant) {
					    /** @var ArticleVariant $variant */ ?>
					<a href="<?= $article->getUrl() ?>?variant=<?= $variant->getId() ?>"
						class="card-link"><?= $variant->getName() ?></a>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php
    }
}
?>
</section>
