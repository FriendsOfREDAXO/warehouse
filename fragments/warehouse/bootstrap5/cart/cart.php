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
					<a data-warehouse-cart-item-amount="-1"
						href="?rex_api_call=warehouse_cart_api&action=modify&article_id=<?= $item['article_id'] ?>&variant_id=<?= $item['variant_id'] ?>&amount=1&mode=-"
						class="btn btn-outline-secondary btn-sm px-2 py-0">-</a>
					<span
						class="mx-2 warehouse-cart-item-amount"><?= $item['amount'] ?></span>
					<a data-warehouse-cart-item-amount="+1"
						href="?rex_api_call=warehouse_cart_api&action=modify&article_id=<?= $item['article_id'] ?>&variant_id=<?= $item['variant_id'] ?>&amount=1&mode=+"
						class="btn btn-outline-secondary btn-sm px-2 py-0">+</a>
				</div>
			</td>
			<td class="align-right">
				<?= Warehouse::formatCurrency($item['total']) ?>
			</td>
			<td>
				<a data-warehouse-cart-action="remove"
					href="?rex_api_call=warehouse_cart_api&action=delete&article_id=<?= $item['article_id'] ?>&variant_id=<?= $item['variant_id'] ?>"
					class="btn btn-outline-danger btn-sm px-2 py-0"><?= Warehouse::getLabel('remove_from_cart') ?></a>
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
			<td class="align-right">
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
			<td class="align-left">Versand</td>
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
	// Wenn data-warehouse-cart-action="remove" angeklickt wird, confirm anzeigen
	document.querySelectorAll('[data-warehouse-cart-action="remove"]').forEach(function(element) {
		element.addEventListener('click', function(event) {
			event.preventDefault();
			// Bootstrap Loading Animation
			const loadingButton = event.target.closest('a[data-warehouse-cart-action="remove"]');
			if (loadingButton) {
				loadingButton.classList.add('disabled');
				loadingButton.innerHTML =
					'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
			}
			if (confirm(
					'<?= rex_escape(Warehouse::getLabel('cart_remove_item_confirm')) ?>'
				)) {
				window.location.href = this.href;
			} else {
				// Wenn der Nutzer nicht bestätigt, dann Button wieder zurücksetzen
				if (loadingButton) {
					loadingButton.classList.remove('disabled');
					loadingButton.innerHTML =
						'<?= rex_escape(Warehouse::getLabel('remove_from_cart')) ?>';
				}
			}
		});
	});
	// Wenn die Menge geändert wird, dann Zahl im DOM aktualisieren
	document.querySelectorAll('[data-warehouse-cart-item="quantity"]').forEach(function(element) {
		element.addEventListener('click', function(event) {
			event.preventDefault();
			// Bootstrap Loading Animation 
			const loadingButton = event.target.closest('a[data-warehouse-cart-item-amount]');
			if (loadingButton) {
				loadingButton.classList.add('disabled');
				loadingButton.innerHTML =
					'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
			}

			const target = event.target.closest('[data-warehouse-cart-item-amount]');
			if (target) {
				const mod = target.getAttribute('data-warehouse-cart-item-amount');
				const amountElement = element.querySelector('.warehouse-cart-item-amount');
				let amount = parseInt(amountElement.textContent, 10);
				if (mod === '+1') {
					amount++;
				} else if (mod === '-1' && amount > 1) {
					amount--;
				}
				amountElement.textContent = amount;
			}
		});
	});

	// Klick auf weiter - Loading Animation
	document.querySelectorAll('[data-warehouse-cart-action="next"]').forEach(function(element) {
		element.addEventListener('click', function(event) {
			event.preventDefault();
			// Bootstrap Loading Animation
			const loadingButton = event.target.closest('a[data-warehouse-cart-action="next"]');
			if (loadingButton) {
				loadingButton.classList.add('disabled');
				loadingButton.innerHTML =
					'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
			}
			window.location.href = this.href;
		});
	});
</script>
