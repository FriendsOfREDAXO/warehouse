<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Shipping;
use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Media;

$cart = Cart::create();
$cart_items = $cart->getItems();
$domain = Domain::getCurrent();
?>
<!-- cart/offcanvas_cart.php -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cart-offcanvas" data-warehouse-offcanvas-cart data-warehouse-empty-message="<?= Warehouse::getLabel('cart_is_empty'); ?>">
	<div class="offcanvas-header">
		<h5 class="offcanvas-title">
			<?= Warehouse::getLabel('cart') ?>
		</h5>
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body" data-warehouse-offcanvas-body>
		<?php if ($cart_items && count($cart_items) > 0) { ?>
		<ul class="list-group list-group-flush">
			<?php foreach ($cart_items as $item_key => $item) { ?>
			<li class="list-group-item" data-warehouse-item-key="<?= $item_key ?>">
				<div class="row g-3 align-items-center">
					<div class="col-auto">
						<div class="ratio ratio-4x3">
							<?php if (isset($item['image']) && $item['image']) { ?>
							<a
								href="<?= rex_getUrl('', '', ['warehouse-article-id' => $item['article_id']]) ?>">
								<?php
								$media = new Media($item['image']);
								$media->setProfile('warehouse-cart')
									->setAlt($item['name'])
									->setAttribute(['class' => 'img-fluid']);
								echo $media->getImg();
								?>
							</a>
							<?php } ?>
						</div>
					</div>
					<div class="col">
						<a class="link-heading stretched-link text-decoration-none small"
							href="<?= rex_getUrl('', '', ['warehouse-article-id' => $item['article_id']]) ?>"><?= trim($item['name'], '- ') ?>
							<span class="text-muted small">
								(<?= $item['article_id'] ?><?= $item['type'] === 'variant' ? '-' . $item['variant_id'] : '' ?>)</span>
						</a>
						<?php if ($item['type'] === 'variant'): ?>
							<small class="text-muted d-block"><?= Warehouse::getLabel('product_variant') ?></small>
						<?php endif; ?>
						<div class="mt-1 row g-0 align-items-center">
							<div class="col-auto fw-bolder small" data-warehouse-item-total="<?= $item_key ?>">
								<?= Warehouse::formatCurrency($item['total']) ?>
							</div>
							<div class="col-auto text-muted small">
								<span data-warehouse-item-amount="<?= $item_key ?>"><?= $item['amount'] ?></span>
								&times;
								<span data-warehouse-item-price="<?= $item_key ?>"><?= Warehouse::formatCurrency($item['price']) ?></span>
							</div>
						</div>
					</div>
					<div class="col-auto">
						<button type="button" class="btn btn-link text-danger p-0" 
							data-warehouse-cart-delete
							data-warehouse-article-id="<?= $item['article_id'] ?>" 
							data-warehouse-variant-id="<?= $item['variant_id'] ?>"
							data-warehouse-confirm="<?= rex_i18n::msg('warehouse.cart_remove_confirm', '') ?>" 
							title="Remove">
							<i class="bi bi-x-circle-fill"></i>
						</button>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>

		<div class="mt-3">
			<div class="row g-2">
				<div class="col text-muted h4">
					<?= Warehouse::getLabel('cart_subtotal') ?>
				</div>
				<div class="col-auto h4 fw-bolder" data-warehouse-offcanvas-subtotal>
					<?= Warehouse::formatCurrency(Cart::getSubTotal()) ?>
				</div>
			</div>
			<div class="row g-2">
				<div class="col text-muted">
					<?= Warehouse::getLabel('shipping_costs') ?>
				</div>
				<div class="col-auto">
					<?= Warehouse::formatCurrency(Shipping::getCost()) ?>
				</div>
			</div>
			<div class="row g-2 align-items-center">
				<div class="col text-muted">
					<?= Warehouse::getLabel('cart_total') ?>
				</div>
				<div class="col-auto h5 fw-bolder">
					<?= Warehouse::formatCurrency($cart->getTotal()) ?>
				</div>
			</div>
		</div>

		<!-- Netto/Brutto-Ausgabe im Offcanvas-Cart -->
		<div class="row g-2">
			<div class="col text-muted h4">Zwischensumme
				(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
			</div>
			<div class="col-auto h4 fw-bolder" data-warehouse-offcanvas-subtotal-by-mode>
				<?= Warehouse::formatCurrency(Cart::getSubTotalByMode(Warehouse::getPriceInputMode())) ?>
			</div>
		</div>
		<div class="row g-2">
			<div class="col text-muted">MwSt.</div>
			<div class="col-auto" data-warehouse-offcanvas-tax>
				<?= Warehouse::formatCurrency(Cart::getTaxTotalByMode()) ?>
			</div>
		</div>
		<div class="row g-2">
			<div class="col text-muted"><?= Warehouse::getLabel('shipping_costs') ?></div>
			<div class="col-auto" data-warehouse-offcanvas-shipping>
				<?= Warehouse::formatCurrency(Shipping::getCost()) ?>
			</div>
		</div>
		<div class="row g-2 align-items-center">
			<div class="col text-muted"><?= Warehouse::getLabel('total') ?>
				(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
			</div>
			<div class="col-auto h5 fw-bolder" data-warehouse-offcanvas-total>
				<?= Warehouse::formatCurrency(Cart::getCartTotalByMode(Warehouse::getPriceInputMode())) ?>
			</div>
		</div>

		<div class="d-grid gap-2 mt-3">
			<div class="d-flex justify-content-between">
				<button type="button" class="btn btn-link text-danger" 
					data-warehouse-cart-empty
					data-warehouse-confirm="<?= rex_escape(Warehouse::getLabel('cart_empty_confirm')) ?>">
					<?= Warehouse::getLabel('cart_empty') ?>
				</button>
				<a class="btn btn-primary ms-auto"
					href="<?= $domain?->getCheckoutUrl() ?? '' ?>">
					<?= Warehouse::getLabel('next') ?>
				</a>
			</div>
		</div>
	</div>
	<?php } else { ?>
	<div class="alert alert-info">
		<?= Warehouse::getLabel('cart_is_empty'); ?>
	</div>
	<?php } ?>
</div>
</div>
<!-- / cart/offcanvas_cart.php -->

<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" nonce="<?= rex_response::getNonce() ?>"></script>
