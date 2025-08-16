<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Shipping;

$cart = Cart::create();
$cart_items = $cart->getItems();

?>
<div class="container">
	<div class="row">
		<div class="col-12 col-md-8">
			<div class="card">

				<?php if ($cart->isEmpty()) { ?>
				<div class="card-body">
					<p class="text-center">
						<?= rex_i18n::msg('warehouse.cart_empty') ?>
					</p>
				</div>
				<?php } ?>
				<?php if (!$cart->isEmpty()) : ?>
				<div class="card-header text-uppercase text-muted text-center text-small d-none d-md-block">
					<div class="row row-cols-1 row-cols-md-2">
						<div class="col">
							<?= Warehouse::getLabel('article') ?>
						</div>
						<div class="col">
							<div class="row row-cols-auto">
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
				<div class="card-body">
					<div class="row row-cols-1 row-cols-md-2 align-items-center">

						<!-- Product cell-->
						<div class="col">
							<div class="row">
								<div class="col-12 col-md-4">
									<?php if (isset($item['image']) && $item['image']) : ?>
									<a class=""
										href="<?= rex_getUrl('', '', ['warehouse-article-id'=>$item['article_id']]) ?>">
										<figure class=""><img
												src="/images/products/<?= $item['image'] ?>"
												alt="<?= $item['name'] ?>"
												class="img-fluid"></figure>
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
										<small class="text-muted d-block">Variante</small>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<!-- Other cells-->
						<div class="col">
							<div class="row row-cols-1 row-cols-sm-auto text-center">
								<div class="col">
									<div class="text-muted d-md-none">
										<?= Warehouse::getLabel('price') ?>
									</div>
									<div>
										<?= Warehouse::formatCurrency($item['price']) ?>
									</div>
								</div>
								<div class="col">
									<a href="?rex_api_call=warehouse_cart_api&action=modify&article_id=<?= $item['article_id'] ?>&variant_id=<?= $item['variant_id'] ?>&amount=1&mode=-"
										class="btn btn-sm"><i class="bi bi-dash"></i></a>
									<input class="form-control wh-qty-input" id="product-1" type="text" maxlength="3"
										value="<?= $item['amount'] ?>"
										disabled>
									<a href="?rex_api_call=warehouse_cart_api&action=modify&article_id=<?= $item['article_id'] ?>&variant_id=<?= $item['variant_id'] ?>&amount=1&mode=+"
										class="btn btn-sm"><i class="bi bi-plus"></i></a>
								</div>
								<div class="col">
									<div class="text-muted d-md-none">
										<?= Warehouse::getLabel('total') ?>
									</div>
									<div>
										<?= Warehouse::formatCurrency($item['total']) ?>
									</div>
								</div>
								<div class="col"><a
										href="?rex_api_call=warehouse_cart_api&action=delete&article_id=<?= $item['article_id'] ?>&variant_id=<?= $item['variant_id'] ?>"
										class="text-danger" data-bs-toggle="tooltip" data-bs-title="Remove"><i
											class="bi bi-x-circle"></i></a></div>
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
						<div class="col">
							<?= Warehouse::formatCurrency($cart::getSubTotalByMode(Warehouse::getPriceInputMode())) ?>
						</div>
					</div>
					<div class="row">
						<div class="col text-muted">MwSt.</div>
						<div class="col">
							<?= Warehouse::formatCurrency($cart::getTaxTotalByMode()) ?>
						</div>
					</div>
					<div class="row">
						<div class="col text-muted">Versand</div>
						<div class="col text">
							<?= Warehouse::formatCurrency((float) Shipping::getCost()) ?>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col text-muted">Total
							(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
						</div>
						<div class="col text-lead fw-bolder">
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
				class="btn btn-primary">Weiter</a>
		</div>
		<?php endif ?>

	</div>
</div>
<script nonce="<?= rex_response::getNonce() ?>">
	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
	const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
