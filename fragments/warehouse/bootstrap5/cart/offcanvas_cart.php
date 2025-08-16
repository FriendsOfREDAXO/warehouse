<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Shipping;
use FriendsOfRedaxo\Warehouse\Warehouse;

$cart = Cart::create();
$cart_items = $cart->getItems();
$domain = Domain::getCurrent();
?>
<!-- cart/offcanvas_cart.php -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cart-offcanvas">
	<div class="offcanvas-header">
		<h5 class="offcanvas-title">
			<?= Warehouse::getLabel('cart') ?>
		</h5>
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body">
		<?php if ($cart_items && count($cart_items) > 0) { ?>
		<ul class="list-group list-group-flush">
			<?php foreach ($cart_items as $item_key => $item) { ?>
			<li class="list-group-item">
				<div class="row g-3 align-items-center">
					<div class="col-auto">
						<div class="ratio ratio-4x3">
							<?php if (isset($item['image']) && $item['image']) { ?>
							<a
								href="<?= rex_getUrl('', '', ['warehouse-article-id' => $item['article_id']]) ?>">
								<img src="<?= $item['image'] ?>"
									alt="<?= $item['name'] ?>"
									class="img-fluid">
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
						<div class="mt-1 row g-2 align-items-center">
							<div class="col-auto fw-bolder small item-total" data-item-key="<?= $item_key ?>">
								<?= Warehouse::formatCurrency($item['total']) ?>
							</div>
							<div class="col-auto text-muted small">
								<span class="item-amount" data-item-key="<?= $item_key ?>"><?= $item['amount'] ?></span>
								&times;
								<span class="item-price" data-item-key="<?= $item_key ?>"><?= Warehouse::formatCurrency($item['price']) ?></span>
							</div>
						</div>
					</div>
					<div class="col-auto">
						<button type="button" class="btn btn-link text-danger cart-delete-btn p-0" 
							data-action="delete" 
							data-article-id="<?= $item['article_id'] ?>" 
							data-variant-id="<?= $item['variant_id'] ?>" 
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
				<div class="col-auto h4 fw-bolder" id="offcanvas-cart-subtotal">
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
			<div class="col-auto h4 fw-bolder">
				<?= Warehouse::formatCurrency(Cart::getSubTotalByMode(Warehouse::getPriceInputMode())) ?>
			</div>
		</div>
		<div class="row g-2">
			<div class="col text-muted">MwSt.</div>
			<div class="col-auto">
				<?= Warehouse::formatCurrency(Cart::getTaxTotalByMode()) ?>
			</div>
		</div>
		<div class="row g-2">
			<div class="col text-muted"><?= Warehouse::getLabel('shipping_costs') ?></div>
			<div class="col-auto">
				<?= Warehouse::formatCurrency(Shipping::getCost()) ?>
			</div>
		</div>
		<div class="row g-2 align-items-center">
			<div class="col text-muted"><?= Warehouse::getLabel('total') ?>
				(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
			</div>
			<div class="col-auto h5 fw-bolder">
				<?= Warehouse::formatCurrency(Cart::getCartTotalByMode(Warehouse::getPriceInputMode())) ?>
			</div>
		</div>

		<div class="d-grid gap-2 mt-3">
			<div class="d-flex justify-content-between">
				<button type="button" class="btn btn-link text-danger" id="empty-cart-btn">
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

<script nonce="<?= rex_response::getNonce() ?>">
	// Handle offcanvas cart interactions
	document.addEventListener('DOMContentLoaded', function() {
		// Handle empty cart button
		const emptyCartBtn = document.getElementById('empty-cart-btn');
		if (emptyCartBtn) {
			emptyCartBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (confirm('<?= rex_escape(Warehouse::getLabel('cart_empty_confirm')) ?>')) {
					updateOffcanvasCartItem('empty');
				}
			});
		}
		
		// Handle delete button clicks in offcanvas
		document.querySelectorAll('#cart-offcanvas .cart-delete-btn').forEach(function(button) {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				const articleId = this.dataset.articleId;
				const variantId = this.dataset.variantId;

				if (confirm('<?= rex_i18n::msg('warehouse.cart_remove_confirm', '') ?>')) {
					updateOffcanvasCartItem('delete', articleId, variantId);
				}
			});
		});
	});

	function updateOffcanvasCartItem(action, articleId = null, variantId = null, amount = 1, mode = null) {
		// Build API URL
		let url = `index.php?rex_api_call=warehouse_cart_api&action=${action}`;
		if (articleId) {
			url += `&article_id=${encodeURIComponent(articleId)}`;
		}
		if (variantId && variantId !== 'null' && variantId !== '') {
			url += `&variant_id=${encodeURIComponent(variantId)}`;
		}
		if (amount && action !== 'empty') {
			url += `&amount=${encodeURIComponent(amount)}`;
		}
		if (mode) {
			url += `&mode=${encodeURIComponent(mode)}`;
		}

		fetch(url, {
			method: 'POST',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				updateOffcanvasCartDisplay(data);
			} else {
				console.error('Offcanvas cart update failed:', data);
				alert('Fehler beim Aktualisieren des Warenkorbs.');
			}
		})
		.catch(error => {
			console.error('Offcanvas cart update error:', error);
			alert('Fehler beim Aktualisieren des Warenkorbs.');
		});
	}

	function updateOffcanvasCartDisplay(cartData) {
		// Update item quantities and totals in offcanvas
		Object.entries(cartData.items).forEach(([itemKey, item]) => {
			// Update item amount
			const itemAmount = document.querySelector(`#cart-offcanvas .item-amount[data-item-key="${itemKey}"]`);
			if (itemAmount) {
				itemAmount.textContent = item.amount;
			}

			// Update item total with tier pricing
			const itemTotal = document.querySelector(`#cart-offcanvas .item-total[data-item-key="${itemKey}"]`);
			if (itemTotal && item.current_total !== undefined) {
				const formatter = new Intl.NumberFormat('de-DE', {
					style: 'currency',
					currency: 'EUR'
				});
				itemTotal.textContent = formatter.format(item.current_total);
			}

			// Update item price
			const itemPrice = document.querySelector(`#cart-offcanvas .item-price[data-item-key="${itemKey}"]`);
			if (itemPrice && item.current_price !== undefined) {
				const formatter = new Intl.NumberFormat('de-DE', {
					style: 'currency',
					currency: 'EUR'
				});
				itemPrice.textContent = formatter.format(item.current_price);
			}
		});

		// Update offcanvas subtotal
		const subtotalElement = document.getElementById('offcanvas-cart-subtotal');
		if (subtotalElement && cartData.totals && cartData.totals.subtotal_formatted) {
			subtotalElement.textContent = cartData.totals.subtotal_formatted;
		}

		// Remove deleted items from offcanvas DOM
		if (cartData.cart && cartData.cart.items) {
			const currentItemKeys = Object.keys(cartData.cart.items);
			document.querySelectorAll('#cart-offcanvas [data-item-key]').forEach(element => {
				const itemKey = element.dataset.itemKey;
				if (itemKey && !currentItemKeys.includes(itemKey)) {
					// Find and remove the entire list item
					const listItem = element.closest('li');
					if (listItem) {
						listItem.remove();
					}
				}
			});
		}

		// If cart is empty, show empty message
		if (cartData.totals && cartData.totals.items_count === 0) {
			const cartContent = document.querySelector('#cart-offcanvas .offcanvas-body');
			if (cartContent) {
				cartContent.innerHTML = '<div class="alert alert-info"><?= Warehouse::getLabel('cart_is_empty'); ?></div>';
			}
		}
	}
</script>
