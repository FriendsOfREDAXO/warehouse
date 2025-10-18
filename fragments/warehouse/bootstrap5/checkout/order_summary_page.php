<?php
/** @var rex_fragment $this */
use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Session;
use FriendsOfRedaxo\Warehouse\Shipping;

/** @var array $customer */
$customer = $this->getVar('customer', []);
/** @var array $cart */
$cart = Session::getCartData();
/** @var array $payment */
$payment = Payment::loadFromSession();
/** @var Domain $domain */
$domain = Domain::getCurrent();

// Variablen für die Anzeige definieren
$cart_items = $cart['items'] ?? [];
$with_tax = Warehouse::getPriceInputMode() === 'gross';
$shipping = Shipping::getCost();

$containerClass = Warehouse::getConfig('container_class', 'container');
$containerClass = ($containerClass === null) ? 'container' : htmlspecialchars($containerClass);

?>

<div class="<?= $containerClass ?>">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h2 class="mb-0"><?= Warehouse::getLabel('order_summary') ?></h2>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table_order_summary">
							<thead class="table-light">
								<tr>
									<th scope="col"><?= Warehouse::getLabel('article') ?></th>
									<th scope="col"><?= Warehouse::getLabel('product_description') ?></th>
									<?php if ($with_tax): ?>
									<th scope="col" class="text-end"><?= Warehouse::getLabel('tax_column') ?></th>
									<?php endif; ?>
									<th scope="col" class="text-end">
										<?= Warehouse::getCurrency() ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($cart_items as $item_key => $item) : ?>
								<tr>
									<td>
										<div class="text-muted small">
											<?= $item['article_id'] . ($item['variant_id'] ? '-' . $item['variant_id'] : '') ?>
										</div>
										<?= trim(html_entity_decode($item['name']), ' -') ?><br>
										<small class="text-muted">
											<?= $item['amount'] ?>
											x à
											<?= Warehouse::formatCurrency($item['price']) ?>
										</small>
									</td>
									<td>
										<?php if ($item['type'] === 'variant'): ?>
										<div class="text-muted small">Variante</div>
										<?php endif; ?>
									</td>
									<?php if ($with_tax): ?>
									<td class="text-end">
										<?= isset($item['tax']) ? $item['tax'] : '19' ?>%
									</td>
									<?php endif; ?>
									<td class="text-end">
										<?= Warehouse::formatCurrency($item['total']) ?>
									</td>
								</tr>
								<?php endforeach ?>

								<!-- Versandkosten -->
								<tr>
									<td colspan="<?= $with_tax ? '3' : '2' ?>"
										class="border-top">
										<strong><?= Warehouse::getLabel('shipping_costs') ?></strong>
									</td>
									<td class="text-end border-top">
										<?= Warehouse::formatCurrency($shipping) ?>
									</td>
								</tr>

								<!-- Steuer -->
								<?php if ($with_tax): ?>
								<tr>
									<td colspan="3">
										<strong><?= Warehouse::getLabel('tax_total') ?></strong>
									</td>
									<td class="text-end">
										<?= Warehouse::formatCurrency(Cart::getTaxTotalByMode()) ?>
									</td>
								</tr>
								<?php endif; ?>

								<!-- Gesamtsumme -->
								<tr class="table-info">
									<td colspan="<?= $with_tax ? '3' : '2' ?>"
										class="fw-bold">
										<?= $with_tax ? Warehouse::getLabel('total_with_tax') : Warehouse::getLabel('total_net') ?>
									</td>
									<td class="text-end fw-bold">
										<?= Cart::getCartTotalByModeFormatted(Warehouse::getPriceInputMode()) ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Rechnungsadresse -->
	<div class="row mt-4">
		<div class="col-12 col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="mb-0"><?= Warehouse::getLabel('address_billing') ?></h3>
				</div>
				<div class="card-body">
					<address>
						<?php
// Get billing address from session
$billingAddress = Session::getBillingAddressData();

// Fallback to customer data if billing address is not set
if (empty($billingAddress)) {
    $customerData = is_array($customer) ? $customer : [];
    if (empty($customerData)) {
        $customerData = rex_session('user_data', 'array', []);
    }
    $billingAddress = $customerData;
}

$firma = (isset($billingAddress['company']) && !empty($billingAddress['company'])) ? $billingAddress['company'] . ' ' . ($billingAddress['department'] ?? '') .'<br>': '';
$ust = (isset($billingAddress['ust']) && !empty($billingAddress['ust'])) ? 'Ust. Identnummer: ' . $billingAddress['ust'] .'<br>': '';
$title = (isset($billingAddress['title']) && !empty($billingAddress['title'])) ? ' ' . $billingAddress['title'] .' ': ' ';

echo "<strong>";
echo ($billingAddress['salutation'] ?? '') . $title . ($billingAddress['firstname'] ?? '') . ' ' . ($billingAddress['lastname'] ?? '');
echo "</strong><br>";
echo $firma . $ust;
echo ($billingAddress['address'] ?? '') . ' ' . ($billingAddress['housenumber'] ?? '') . '<br>';
echo ($billingAddress['zip'] ?? '') . ' ' . ($billingAddress['city'] ?? '') . '<br>';
echo($billingAddress['country'] ?? '');
?>
					</address>
				</div>
			</div>
		</div>

		<!-- Lieferadresse -->
		<div class="col-12 col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="mb-0"><?= Warehouse::getLabel('address_shipping') ?></h3>
				</div>
				<div class="card-body">
					<address>
						<?php
// Get shipping address from session
$shippingAddress = Session::getShippingAddressData();

// If no separate shipping address, use billing address
if (empty($shippingAddress)) {
    $shippingAddress = $billingAddress;
}

$firma = (isset($shippingAddress['company']) && !empty($shippingAddress['company'])) ? $shippingAddress['company'] . ' ' . ($shippingAddress['department'] ?? '') .'<br>': '';

echo "<strong>";
echo ($shippingAddress['salutation'] ?? '') . ' ' . ($shippingAddress['firstname'] ?? '') . ' ' . ($shippingAddress['lastname'] ?? '');
echo "</strong><br>";
echo $firma;
echo ($shippingAddress['address'] ?? '') . ' ' . ($shippingAddress['housenumber'] ?? '') . '<br>';
echo ($shippingAddress['zip'] ?? '') . ' ' . ($shippingAddress['city'] ?? '') . '<br>';
echo($shippingAddress['country'] ?? '');
?>
					</address>
				</div>
			</div>
		</div>
	</div>

	<!-- Zahlungsart -->
	<div class="row mt-4">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="mb-0">
						<?= Warehouse::getLabel('payment_type'); ?>
					</h3>
				</div>
				<div class="card-body">
					<?php
// Get payment type from customer data or session
$customerData = is_array($customer) ? $customer : [];
if (empty($customerData)) {
    $customerData = rex_session('user_data', 'array', []);
}
echo Warehouse::getLabel('paymentoptions_' . ($customerData['payment_type'] ?? ''));
?>
				</div>
			</div>
		</div>
	</div>
</div>


<?php
echo "paypal";
echo $this->getSubfragment('warehouse/bootstrap5/paypal/paypal-button.php');
?>
