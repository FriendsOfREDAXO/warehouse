<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Shipping;
use FriendsOfRedaxo\Warehouse\Media;

$cart = Cart::create();
$cart_items = $cart->getItems();

$containerClass = Warehouse::getConfig('container_class', 'container');
$containerClass = ($containerClass === null || $containerClass === false) ? 'container' : htmlspecialchars($containerClass);

?>
<div class="<?= $containerClass ?>" data-warehouse-cart-page>
	<div class="row">
		<div class="col-12 col-md-8">
			<div class="card">

				<?php if ($cart->isEmpty()) { ?>
				<div class="card-body">
					<p class="text-center">
						<?= Warehouse::getLabel('cart_empty') ?>
					</p>
				</div>
				<?php } ?>
				<?php if (!$cart->isEmpty()) : ?>
				<div class="card-header text-uppercase text-muted text-center text-small d-none d-md-block">
					<div class="row row-cols-1 row-cols-md-2 g-0">
						<div class="col">
							<?= Warehouse::getLabel('article') ?>
						</div>
						<div class="col">
							<div class="row row-cols-auto g-0">
								<div class="col">
									<?= Warehouse::getLabel('price') ?>
								</div>
								<div class="col tm-quantity-column">
									<?= Warehouse::getLabel('quantity') ?>
								</div>
								<div class="col">
									<?= Warehouse::getLabel('total') ?>
								</div>
								<div class="col" style="width: 20px;"></div>
							</div>
						</div>
					</div>
				</div>

				<?php foreach ($cart_items as $item_key => $item) : ?>
				<!-- Item -->
				<div class="card-body"
					data-warehouse-item-key="<?= $item_key ?>">
					<div class="row row-cols-1 row-cols-md-2 align-items-center">

						<!-- Product cell-->
						<div class="col">
							<div class="row g-0">
								<div class="col-12 col-md-4">
									<?php if (isset($item['image']) && $item['image']) : ?>
									<a class=""
										href="<?= rex_getUrl('', '', ['warehouse-article-id'=>$item['article_id']]) ?>">
										<figure class="">
											<?php
											$media = new Media('/images/warehouse-cart-article/' . $item['image']);
											$media->setProfile('warehouse-cart')
												->setAlt($item['name'])
												->setAttribute(['class' => 'img-fluid']);
											echo $media->getImg();
											?>
										</figure>
									</a>
									<?php endif ?>
								</div>
								<div class="col">
									<div class="text-meta">
										<?= isset($item['cat_name']) ? $item['cat_name'] : '' ?>
									</div>
									<a class="link-heading"
										href="<?= rex_getUrl('', '', ['warehouse-article-id'=>$item['article_id']]) ?>"><?= html_entity_decode($item['name']) ?>
									</a>
									<?php if ($item['type'] === 'variant'): ?>
									<small
										class="text-muted d-block"><?= Warehouse::getLabel('product_variant') ?></small>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<!-- Other cells-->
						<div class="col">
							<div class="row row-cols-1 row-cols-sm-auto text-center g-0">
								<div class="col">
									<div class="text-muted d-md-none">
										<?= Warehouse::getLabel('price') ?>
									</div>
									<div>
										<?= Warehouse::formatCurrency($item['price']) ?>
									</div>
								</div>
								<div class="col">
									<button type="button" class="btn btn-sm" data-warehouse-cart-quantity="modify"
										data-warehouse-mode="-"
										data-warehouse-article-id="<?= $item['article_id'] ?>"
										data-warehouse-variant-id="<?= $item['variant_id'] ?>"
										data-warehouse-amount="1">
										<i class="bi bi-dash"></i>
									</button>
									<input class="form-control"
										id="product-<?= $item_key ?>"
										type="text" maxlength="3"
										value="<?= $item['amount'] ?>"
										data-warehouse-cart-input
										data-warehouse-article-id="<?= $item['article_id'] ?>"
										data-warehouse-variant-id="<?= $item['variant_id'] ?>"
										data-warehouse-item-key="<?= $item_key ?>">
									<button type="button" class="btn btn-sm" data-warehouse-cart-quantity="modify"
										data-warehouse-mode="+"
										data-warehouse-article-id="<?= $item['article_id'] ?>"
										data-warehouse-variant-id="<?= $item['variant_id'] ?>"
										data-warehouse-amount="1">
										<i class="bi bi-plus"></i>
									</button>
								</div>
								<div class="col">
									<div class="text-muted d-md-none">
										<?= Warehouse::getLabel('total') ?>
									</div>
									<div
										data-warehouse-item-total="<?= $item_key ?>">
										<?= Warehouse::formatCurrency($item['total']) ?>
									</div>
								</div>
								<div class="col">
									<button type="button" class="btn btn-link text-danger" data-warehouse-cart-delete
										data-warehouse-article-id="<?= $item['article_id'] ?>"
										data-warehouse-variant-id="<?= $item['variant_id'] ?>"
										data-warehouse-confirm="<?= rex_i18n::msg('warehouse.cart_remove_confirm', '') ?>"
										data-bs-toggle="tooltip" data-bs-title="Remove">
										<i class="bi bi-x-circle"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- // Item -->
				<?php endforeach ?>
				<?php endif ?>

			</div>
		</div>
		<?php if (!$cart->isEmpty()) : ?>
		<div class="col-12 col-md-4">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col text-muted">Zwischensumme
							(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
						</div>
						<div class="col" data-warehouse-cart-subtotal-by-mode>
							<?= Warehouse::formatCurrency($cart::getSubTotalByMode(Warehouse::getPriceInputMode())) ?>
						</div>
					</div>
					<div class="row">
						<div class="col text-muted">MwSt.</div>
						<div class="col" data-warehouse-cart-tax>
							<?= Warehouse::formatCurrency($cart::getTaxTotalByMode()) ?>
						</div>
					</div>
					<div class="row">
						<div class="col text-muted">
							<?= Warehouse::getLabel('shipping_costs') ?>
						</div>
						<div class="col text" data-warehouse-cart-shipping>
							<?= Warehouse::formatCurrency((float) Shipping::getCost()) ?>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col text-muted">
							<?= Warehouse::getLabel('total') ?>
							(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
						</div>
						<div class="col text-lead fw-bolder" data-warehouse-cart-total>
							<?= Warehouse::formatCurrency($cart::getCartTotalByMode(Warehouse::getPriceInputMode())) ?>
						</div>
					</div>
					<a class="btn btn-primary mt-3 w-100"
						href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>">checkout</a>
				</div>
			</div>
		</div>
		<div class="col-12">
			<a href="<?= rex_getUrl(rex_config::get('warehouse', 'address_page')) ?>"
				class="btn btn-primary"><?= Warehouse::getLabel('next') ?></a>
		</div>
		<?php endif ?>

	</div>
</div>
<script
	src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>"
	nonce="<?= rex_response::getNonce() ?>"></script>
