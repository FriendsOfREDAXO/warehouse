<!-- BEGIN article_with_variants -->
<?php
/** @var rex_fragment $this */
use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\ArticleVariant;

/** @var Article $article */
$article = $this->getVar('article');

?>
<div class="row">
	<div class="col-12">
		<h2><?= htmlspecialchars($article->getCategory()->getName() ?? '') ?>
		</h2>
		<?php if ($article->getCategory()?->getImage()) : ?>
		<img src="<?= rex_url::media($article->getCategory()->getImage()) ?>"
			class="img-fluid"
			alt="<?= htmlspecialchars($article->getCategory()->getName() ?? '') ?>">
		<?php endif ?>
		<?= $article->getCategory()?->getText() ?>
	</div>
</div>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
		<?php foreach ($article->getVariants() ?? [] as $item) : ?>
		<div class="col">
			<div class="mt-3">
				<div class="card-title">
					<h3><a
							href="<?= $item->getUrl() ?>"><?= htmlspecialchars($item->getName()) ?></a>
					</h3>
				</div>
				<div>
					<?php if ($item->getImage()) : ?>
					<a href="<?= rex_url::media($item->getImage()) ?>"
						data-caption="<?= htmlspecialchars($item->getName()) ?>"
						class="lightboxlink">
						<img src="<?= rex_url::media($item->getImage()) ?>"
							alt="<?= htmlspecialchars($item->getName()) ?>"
							class="img-fluid warehouse_prod_image">
					</a>
					<?php endif ?>
				</div>
				<div class="mt-2">
					<?= $item->getShortText() ?>
					<?= $item->getText() ?>
					<?php
                        $specifications_json = $item->getValue(ArticleVariant::SPECIFICATIONS);
		    if ($specifications_json) {
		        $specifications = json_decode($specifications_json, true);
		        ?>
					<?php if (is_array($specifications)) : ?>
					<dl class="row">
						<?php foreach ($specifications as $spec) : ?>
						<dt class="col-sm-4">
							<?= htmlspecialchars($spec['name'] ?? '') ?>
						</dt>
						<dd class="col-sm-8">
							<?= htmlspecialchars($spec['value'] ?? '') ?>
						</dd>
						<?php endforeach ?>
					</dl>
					<?php endif ?>
					<?php } ?>
				</div>
				<p class="priceline mb-0">
					<?= $item->getPriceFormatted() ?></p>
				<p class="text-small mt-0">inkl. MwSt. zzgl. <a href="#" data-bs-toggle="modal"
						data-bs-target="#shipping_modal"><?= Warehouse::getLabel('shipping_costs') ?></a></p>
				<form action="/" method="post">
					<input type="hidden" name="art_id"
						value="<?= $item->getId() ?>">
					<input type="hidden" name="action" value="add_to_cart">
					<div class="input-group">
						<button class="btn btn-outline-primary switch_count" type="button" data-value="-1"><i
								class="bi bi-dash"></i></button>
						<input name="order_count" type="text" class="form-control order_count text-center"
							id="warehouse_count_<?= $item->getId() ?>"
							value="1">
						<button class="btn btn-outline-primary switch_count" type="button" data-value="+1"><i
								class="bi bi-plus"></i></button>
						<button type="submit" name="submit" value="1" class="btn btn-primary ms-2"><?= Warehouse::getLabel('add_to_order') ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php endforeach ?>
</div>
<!-- END article_with_variants -->