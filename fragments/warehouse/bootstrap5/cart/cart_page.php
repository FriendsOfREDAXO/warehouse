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
										<small class="text-muted d-block"><?= Warehouse::getLabel('product_variant') ?></small>
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
									<button type="button" class="btn btn-sm cart-quantity-btn" 
										data-action="modify" data-mode="-" 
										data-article-id="<?= $item['article_id'] ?>" 
										data-variant-id="<?= $item['variant_id'] ?>" 
										data-amount="1">
										<i class="bi bi-dash"></i>
									</button>
									<input class="form-control wh-qty-input" 
										id="product-<?= $item_key ?>" 
										type="text" maxlength="3"
										value="<?= $item['amount'] ?>"
										data-article-id="<?= $item['article_id'] ?>" 
										data-variant-id="<?= $item['variant_id'] ?>"
										data-item-key="<?= $item_key ?>">
									<button type="button" class="btn btn-sm cart-quantity-btn" 
										data-action="modify" data-mode="+" 
										data-article-id="<?= $item['article_id'] ?>" 
										data-variant-id="<?= $item['variant_id'] ?>" 
										data-amount="1">
										<i class="bi bi-plus"></i>
									</button>
								</div>
								<div class="col">
									<div class="text-muted d-md-none">
										<?= Warehouse::getLabel('total') ?>
									</div>
									<div class="item-total" data-item-key="<?= $item_key ?>">
										<?= Warehouse::formatCurrency($item['total']) ?>
									</div>
								</div>
								<div class="col">
									<button type="button" class="btn btn-link text-danger cart-delete-btn" 
										data-action="delete" 
										data-article-id="<?= $item['article_id'] ?>" 
										data-variant-id="<?= $item['variant_id'] ?>" 
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
						<div class="col text-muted"><?= Warehouse::getLabel('shipping_costs') ?></div>
						<div class="col text">
							<?= Warehouse::formatCurrency((float) Shipping::getCost()) ?>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col text-muted"><?= Warehouse::getLabel('total') ?>
							(<?= Warehouse::getPriceInputMode() === 'gross' ? 'Brutto' : 'Netto' ?>)
						</div>
						<div class="col text-lead fw-bolder" id="cart-subtotal">
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
<script nonce="<?= rex_response::getNonce() ?>">
	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
	const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

	// Cart interaction handlers
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

				updateCartItem(action, articleId, variantId, amount, mode);
			});
		});

		// Handle delete button clicks
		document.querySelectorAll('.cart-delete-btn').forEach(function(button) {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				const articleId = this.dataset.articleId;
				const variantId = this.dataset.variantId;

				if (confirm('<?= rex_i18n::msg('warehouse.cart_remove_confirm', '') ?>')) {
					updateCartItem('delete', articleId, variantId);
				}
			});
		});

		// Handle quantity input changes
		document.querySelectorAll('.wh-qty-input').forEach(function(input) {
			input.addEventListener('change', function(e) {
				const articleId = this.dataset.articleId;
				const variantId = this.dataset.variantId;
				const newAmount = parseInt(this.value, 10);

				if (newAmount > 0) {
					updateCartItem('set', articleId, variantId, newAmount, 'set');
				} else {
					this.value = 1; // Reset to minimum
				}
			});

			// Prevent non-numeric input
			input.addEventListener('keypress', function(e) {
				if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
					e.preventDefault();
				}
			});
		});
	});

	function updateCartItem(action, articleId, variantId = null, amount = 1, mode = null) {
		// Build API URL
		let url = `index.php?rex_api_call=warehouse_cart_api&action=${action}`;
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
		loadingElements.forEach(el => el.classList.add('opacity-50'));

		fetch(url, {
			method: 'POST',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				updateCartDisplay(data);
			} else {
				console.error('Cart update failed:', data);
				alert('Fehler beim Aktualisieren des Warenkorbs.');
			}
		})
		.catch(error => {
			console.error('Cart update error:', error);
			alert('Fehler beim Aktualisieren des Warenkorbs.');
		})
		.finally(() => {
			// Remove loading state
			loadingElements.forEach(el => el.classList.remove('opacity-50'));
		});
	}

	function updateCartDisplay(cartData) {
		// Update item quantities and totals
		Object.entries(cartData.items).forEach(([itemKey, item]) => {
			// Update quantity input
			const quantityInput = document.querySelector(`input[data-item-key="${itemKey}"]`);
			if (quantityInput) {
				quantityInput.value = item.amount;
			}

			// Update item total with tier pricing
			const itemTotal = document.querySelector(`.item-total[data-item-key="${itemKey}"]`);
			if (itemTotal && item.current_total !== undefined) {
				// Format currency - this is a simplified version, should match Warehouse::formatCurrency
				const formatter = new Intl.NumberFormat('de-DE', {
					style: 'currency',
					currency: 'EUR'
				});
				itemTotal.textContent = formatter.format(item.current_total);
			}
		});

		// Update cart subtotal
		const subtotalElement = document.getElementById('cart-subtotal');
		if (subtotalElement && cartData.totals && cartData.totals.total_formatted) {
			subtotalElement.textContent = cartData.totals.total_formatted;
		}

		// Remove deleted items from DOM
		if (cartData.cart && cartData.cart.items) {
			const currentItemKeys = Object.keys(cartData.cart.items);
			document.querySelectorAll('[data-item-key]').forEach(element => {
				const itemKey = element.dataset.itemKey;
				if (itemKey && !currentItemKeys.includes(itemKey)) {
					// Find and remove the entire item container
					const itemContainer = element.closest('.card-body');
					if (itemContainer) {
						itemContainer.remove();
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
