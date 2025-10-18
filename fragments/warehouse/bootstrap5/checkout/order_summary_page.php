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
						<table class="table" id="table_order_summary">
							<thead class="table-light">
								<tr>
									<th scope="col"><?= Warehouse::getLabel('article') ?></th>
									<th scope="col" class="text-end"><?= Warehouse::getLabel('price') ?></th>
									<th scope="col" class="text-end"><?= Warehouse::getLabel('quantity') ?></th>
									<th scope="col" class="text-end"><?= Warehouse::getLabel('total') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($cart_items as $item_key => $item) : ?>
								<tr>
									<td>
										<div class="row align-items-center">
											<div class="col-auto">
												<?php if (isset($item['image']) && $item['image']) : ?>
												<img src="<?= rex_url::media($item['image']) ?>" alt="<?= htmlspecialchars(html_entity_decode($item['name']), ENT_QUOTES, 'UTF-8') ?>" class="img-fluid" style="max-width: 80px;">
												<?php endif ?>
											</div>
											<div class="col">
												<div class="text-muted small">
													<?= htmlspecialchars($item['article_id'] . ($item['variant_id'] ? '-' . $item['variant_id'] : ''), ENT_QUOTES, 'UTF-8') ?>
												</div>
												<div><?= html_entity_decode($item['name']) ?></div>
												<?php if ($item['type'] === 'variant'): ?>
												<small class="text-muted d-block"><?= Warehouse::getLabel('product_variant') ?></small>
												<?php endif; ?>
											</div>
										</div>
									</td>
									<td class="text-end">
										<?= Warehouse::formatCurrency($item['price']) ?>
										<?php if ($with_tax && isset($item['tax'])): ?>
										<small class="text-muted d-block">(inkl. <?= $item['tax'] ?>% MwSt.)</small>
										<?php endif; ?>
									</td>
									<td class="text-end">
										<?= $item['amount'] ?>
									</td>
									<td class="text-end">
										<?= Warehouse::formatCurrency($item['total']) ?>
									</td>
								</tr>
								<?php endforeach ?>

								<!-- Zwischensumme -->
								<tr>
									<td colspan="3" class="border-top">
										<strong><?= Warehouse::getLabel('cart_subtotal') ?></strong>
									</td>
									<td class="text-end border-top">
										<?= Warehouse::formatCurrency(Cart::getSubTotalByMode(Warehouse::getPriceInputMode())) ?>
									</td>
								</tr>

								<!-- Steuer -->
								<?php if ($with_tax): ?>
								<tr>
									<td colspan="3">
										<strong>MwSt.</strong>
									</td>
									<td class="text-end">
										<?= Warehouse::formatCurrency(Cart::getTaxTotalByMode()) ?>
									</td>
								</tr>
								<?php endif; ?>

								<!-- Versandkosten -->
								<tr>
									<td colspan="3">
										<strong><?= Warehouse::getLabel('shipping_costs') ?></strong>
									</td>
									<td class="text-end">
										<?= Warehouse::formatCurrency($shipping) ?>
									</td>
								</tr>

								<!-- Gesamtsumme -->
								<tr class="table-info">
									<td colspan="3" class="fw-bold">
										<?= Warehouse::getLabel('cart_total') ?>
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

$billing_company_name = (isset($billingAddress[Customer::COMPANY]) && !empty($billingAddress[Customer::COMPANY])) ? $billingAddress[Customer::COMPANY] . ' ' . ($billingAddress[Customer::DEPARTMENT] ?? '') .'<br>': '';
$billing_vat_id = (isset($billingAddress['ust']) && !empty($billingAddress['ust'])) ? 'Ust. Identnummer: ' . $billingAddress['ust'] .'<br>': '';
$billing_title = (isset($billingAddress['title']) && !empty($billingAddress['title'])) ? ' ' . $billingAddress['title'] .' ': ' ';

echo "<strong>";
echo ($billingAddress[Customer::SALUTATION] ?? '') . $billing_title . ($billingAddress[Customer::FIRSTNAME] ?? '') . ' ' . ($billingAddress[Customer::LASTNAME] ?? '');
echo "</strong><br>";
echo $billing_company_name . $billing_vat_id;
echo ($billingAddress[Customer::ADDRESS] ?? '') . ' ' . ($billingAddress['housenumber'] ?? '') . '<br>';
echo ($billingAddress[Customer::ZIP] ?? '') . ' ' . ($billingAddress[Customer::CITY] ?? '') . '<br>';
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

$shipping_company_name = (isset($shippingAddress[Customer::COMPANY]) && !empty($shippingAddress[Customer::COMPANY])) ? $shippingAddress[Customer::COMPANY] . ' ' . ($shippingAddress[Customer::DEPARTMENT] ?? '') .'<br>': '';
$shipping_title = (isset($shippingAddress['title']) && !empty($shippingAddress['title'])) ? ' ' . $shippingAddress['title'] .' ': ' ';

echo "<strong>";
echo ($shippingAddress[Customer::SALUTATION] ?? '') . $shipping_title . ($shippingAddress[Customer::FIRSTNAME] ?? '') . ' ' . ($shippingAddress[Customer::LASTNAME] ?? '');
echo "</strong><br>";
echo $shipping_company_name;
echo ($shippingAddress[Customer::ADDRESS] ?? '') . ' ' . ($shippingAddress['housenumber'] ?? '') . '<br>';
echo ($shippingAddress[Customer::ZIP] ?? '') . ' ' . ($shippingAddress[Customer::CITY] ?? '') . '<br>';
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
// Only show PayPal button if PayPal was selected as payment method
if (isset($customerData['payment_type']) && $customerData['payment_type'] === 'paypal') {
    echo $this->getSubfragment('warehouse/bootstrap5/paypal/paypal-button.php');
}
?>
