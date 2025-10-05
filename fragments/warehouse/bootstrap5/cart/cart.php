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
<table class="table table-striped table-hover table-bordered" data-warehouse-cart-table>
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
<div class="d-inline-flex align-items-center gap-1">
<button type="button" class="btn btn-outline-secondary btn-sm px-2 py-0"
data-warehouse-cart-quantity="modify" 
data-warehouse-mode="-" 
data-warehouse-article-id="<?= $item['article_id'] ?>" 
data-warehouse-variant-id="<?= $item['variant_id'] ?>" 
data-warehouse-amount="1"
data-warehouse-original-text="-">-</button>
<span class="mx-2" data-warehouse-item-amount="<?= $item_key ?>"><?= $item['amount'] ?></span>
<button type="button" class="btn btn-outline-secondary btn-sm px-2 py-0"
data-warehouse-cart-quantity="modify" 
data-warehouse-mode="+" 
data-warehouse-article-id="<?= $item['article_id'] ?>" 
data-warehouse-variant-id="<?= $item['variant_id'] ?>" 
data-warehouse-amount="1"
data-warehouse-original-text="+">+</button>
</div>
</td>
<td class="align-right" data-warehouse-item-total="<?= $item_key ?>">
<?= Warehouse::formatCurrency($item['total']) ?>
</td>
<td>
<button type="button" class="btn btn-outline-danger btn-sm px-2 py-0"
data-warehouse-cart-delete
data-warehouse-article-id="<?= $item['article_id'] ?>" 
data-warehouse-variant-id="<?= $item['variant_id'] ?>"
data-warehouse-confirm="<?= rex_escape(Warehouse::getLabel('cart_remove_item_confirm')) ?>"><?= Warehouse::getLabel('remove_from_cart') ?></button>
</td>
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
			<td class="align-right" data-warehouse-table-subtotal>
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
		<p><a data-warehouse-cart-next class="btn btn-primary"
				href="<?= $domain?->getCheckoutUrl() ?? '' ?>"
				class="white_big_circle"><?= Warehouse::getLabel('next'); ?></a>
		</p>
	</div>
</div>

<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" nonce="<?= rex_response::getNonce() ?>"></script>
