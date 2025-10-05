<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Shipping;
use FriendsOfRedaxo\Warehouse\Warehouse;

$cart = Cart::create();
$cart_items = $cart->getItems();

$domain = Domain::getCurrent();

if (!$cart_items || count($cart_items) === 0) {

    echo '<p class="text-center">' . rex_i18n::msg('warehouse.cart_empty') . '</p>';
    return;
}
?>
<table class="table table-striped table-hover table-bordered">
	<thead>
		<tr>
			<th class="align-left">
				<?= Warehouse::getLabel('article') ?>
			</th>
			<th class="align-right">
				<?= Warehouse::getLabel('price') ?>
			</th>
			<th class="tm-quantity-column no-wrap">
				<?= Warehouse::getLabel('quantity') ?>
			</th>
			<th class="align-right">
				<?= Warehouse::getLabel('total') ?>
			</th>
			<th class="text-center" style="width: 20px;"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($cart_items as $item_key => $item) : ?>
		<tr data-article-key="<?= $item_key ?>">
			<td class="align-left">
				<?= html_entity_decode($item['name']) ?>
				<?php if ($item['type'] === 'variant'): ?>
					<small class="text-muted d-block"><?= Warehouse::getLabel('product_variant') ?></small>
				<?php endif; ?>
			</td>
			<td class="align-right">
				<?= Warehouse::formatCurrency($item['price']) ?>
			</td>
			<td class="no-wrap" data-warehouse-cart-item="quantity">
				<div class="d-inline-flex align-items-center gap-1">
					<button type="button" class="btn btn-outline-secondary btn-sm px-2 py-0 cart-quantity-btn"
						data-action="modify" data-mode="-" 
						data-article-id="<?= $item['article_id'] ?>" 
						data-variant-id="<?= $item['variant_id'] ?>" 
						data-amount="1">-</button>
					<span class="mx-2 warehouse-cart-item-amount" data-item-key="<?= $item_key ?>"><?= $item['amount'] ?></span>
					<button type="button" class="btn btn-outline-secondary btn-sm px-2 py-0 cart-quantity-btn"
						data-action="modify" data-mode="+" 
						data-article-id="<?= $item['article_id'] ?>" 
						data-variant-id="<?= $item['variant_id'] ?>" 
						data-amount="1">+</button>
				</div>
			</td>
			<td class="align-right item-total" data-item-key="<?= $item_key ?>">
				<?= Warehouse::formatCurrency($item['total']) ?>
			</td>
			<td>
				<button type="button" class="btn btn-outline-danger btn-sm px-2 py-0 cart-delete-btn"
					data-action="delete" 
					data-article-id="<?= $item['article_id'] ?>" 
					data-variant-id="<?= $item['variant_id'] ?>"><?= Warehouse::getLabel('remove_from_cart') ?></button>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td class="align-left">
				<?= Warehouse::getLabel('shipping_costs'); ?>
				<a class="text-decoration-none" href="#" data-bs-toggle="modal"
					data-bs-target="#warehouseShippingCostModal">ℹ️</a>
			</td>
			<td></td>
			<td></td>
			<td class="align-right">
				<?= Shipping::getCostFormatted() ?>
			</td>
			<td></td>
		</tr>
		<!-- Netto/Brutto-Ausgabe im Tabellen-Cart -->
		<tr>
			<td class="align-left">Zwischensumme
				(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
			</td>
			<td></td>
			<td></td>
			<td class="align-right" id="cart-table-subtotal">
				<?= Cart::getSubTotalByModeFormatted(Warehouse::getPriceInputMode()) ?>
			</td>
			<td></td>
		</tr>
		<tr>
			<td class="align-left">MwSt.</td>
			<td></td>
			<td></td>
			<td class="align-right">
				<?= Warehouse::formatCurrency(Cart::getTaxTotalByMode()) ?>
			</td>
			<td></td>
		</tr>
		<tr>
			<td class="align-left"><?= Warehouse::getLabel('shipping_costs') ?></td>
			<td></td>
			<td></td>
			<td class="align-right">
				<?= Shipping::getCostFormatted() ?>
			</td>
			<td></td>
		</tr>
		<tr>
			<td class="align-left"><?= Warehouse::getLabel('total') ?>
				(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
			</td>
			<td></td>
			<td></td>
			<td class="align-right fw-bolder">
				<?= Cart::getCartTotalByModeFormatted(Warehouse::getPriceInputMode()) ?>
			</td>
			<td></td>
		</tr>
	</tbody>
</table>
<div class="position-sticky bottom-0 bg-white shadow p-3">
	<div class="row g-2">
		<div class="col text-muted h4">
			<?= Warehouse::getLabel('cart_subtotal') ?>
		</div>
		<div class="col-auto h4 fw-bolder">
			<?= Cart::getSubTotalFormatted() ?>
		</div>
		<p><a data-warehouse-cart-action="next" class="btn btn-primary"
				href="<?= $domain?->getCheckoutUrl() ?? '' ?>"
				class="white_big_circle"><?= Warehouse::getLabel('next'); ?></a>
		</p>
	</div>
</div>

<script nonce="<?= rex_response::getNonce() ?>" type="module">
	// Global function to update cart count in navigation
	function updateGlobalCartCount(itemsCount) {
		document.querySelectorAll('[data-warehouse-cart-count]').forEach(element => {
			element.textContent = itemsCount;
		});
	}
	window.updateGlobalCartCount = updateGlobalCartCount;

	// Handle cart table interactions with JavaScript and API calls
	document.addEventListener('DOMContentLoaded', function() {
		// Handle quantity button clicks
		document.querySelectorAll('.cart-quantity-btn').forEach(function(button) {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				const action = this.dataset.action;
				const mode = this.dataset.mode;
				const articleId = this.dataset.articleId;
				const variantId = this.dataset.variantId;
				const amount = this.dataset.amount;

				updateCartTableItem(action, articleId, variantId, amount, mode);
			});
		});

		// Handle delete button clicks
		document.querySelectorAll('.cart-delete-btn').forEach(function(button) {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				const articleId = this.dataset.articleId;
				const variantId = this.dataset.variantId;

				if (confirm('<?= rex_escape(Warehouse::getLabel('cart_remove_item_confirm')) ?>')) {
					updateCartTableItem('delete', articleId, variantId);
				}
			});
		});

		// Handle next button clicks with loading animation
		document.querySelectorAll('[data-warehouse-cart-action="next"]').forEach(function(element) {
			element.addEventListener('click', function(event) {
				const loadingButton = event.target.closest('a[data-warehouse-cart-action="next"]');
				if (loadingButton) {
					loadingButton.classList.add('disabled');
					loadingButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
				}
			});
		});
	});

	function updateCartTableItem(action, articleId, variantId = null, amount = 1, mode = null) {
		// Build API URL
		let url = `index.php?rex-api-call=warehouse_cart_api&action=${action}`;
		url += `&article_id=${encodeURIComponent(articleId)}`;
		if (variantId && variantId !== 'null' && variantId !== '') {
			url += `&variant_id=${encodeURIComponent(variantId)}`;
		}
		url += `&amount=${encodeURIComponent(amount)}`;
		if (mode) {
			url += `&mode=${encodeURIComponent(mode)}`;
		}

		// Show loading state
		const loadingElements = document.querySelectorAll(`[data-article-id="${articleId}"][data-variant-id="${variantId || ''}"]`);
		loadingElements.forEach(el => {
			el.classList.add('disabled');
			if (el.tagName === 'BUTTON') {
				el.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
			}
		});

		fetch(url, {
			method: 'POST',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				updateCartTableDisplay(data);
			} else {
				console.error('Cart table update failed:', data);
				alert('Fehler beim Aktualisieren des Warenkorbs.');
			}
		})
		.catch(error => {
			console.error('Cart table update error:', error);
			alert('Fehler beim Aktualisieren des Warenkorbs.');
		})
		.finally(() => {
			// Reset loading state
			loadingElements.forEach(el => {
				el.classList.remove('disabled');
				if (el.tagName === 'BUTTON') {
					if (el.classList.contains('cart-quantity-btn')) {
						el.innerHTML = el.dataset.mode === '+' ? '+' : '-';
					} else if (el.classList.contains('cart-delete-btn')) {
						el.innerHTML = '<?= rex_escape(Warehouse::getLabel('remove_from_cart')) ?>';
					}
				}
			});
		});
	}

	function updateCartTableDisplay(cartData) {
		// Update global cart count in navigation
		if (cartData.totals && cartData.totals.items_count !== undefined) {
			updateGlobalCartCount(cartData.totals.items_count);
		}

		// Update item quantities and totals in table
		Object.entries(cartData.items).forEach(([itemKey, item]) => {
			// Update quantity display
			const quantitySpan = document.querySelector(`.warehouse-cart-item-amount[data-item-key="${itemKey}"]`);
			if (quantitySpan) {
				quantitySpan.textContent = item.amount;
			}

			// Update item total with tier pricing
			const itemTotal = document.querySelector(`.item-total[data-item-key="${itemKey}"]`);
			if (itemTotal && item.current_total !== undefined) {
				const formatter = new Intl.NumberFormat('de-DE', {
					style: 'currency',
					currency: 'EUR'
				});
				itemTotal.textContent = formatter.format(item.current_total);
			}
		});

		// Update cart table subtotal
		const subtotalElement = document.getElementById('cart-table-subtotal');
		if (subtotalElement && cartData.totals && cartData.totals.subtotal_formatted) {
			subtotalElement.textContent = cartData.totals.subtotal_formatted;
		}

		// Remove deleted items from table DOM
		if (cartData.cart && cartData.cart.items) {
			const currentItemKeys = Object.keys(cartData.cart.items);
			document.querySelectorAll('tr [data-item-key]').forEach(element => {
				const itemKey = element.dataset.itemKey;
				if (itemKey && !currentItemKeys.includes(itemKey)) {
					// Find and remove the entire table row
					const tableRow = element.closest('tr');
					if (tableRow) {
						tableRow.remove();
					}
				}
			});
		}

		// If cart is empty, reload page to show empty cart message
		if (cartData.totals && cartData.totals.items_count === 0) {
			window.location.reload();
		}
	}
</script>
