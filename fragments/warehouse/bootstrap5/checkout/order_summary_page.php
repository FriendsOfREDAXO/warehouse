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
/** @var Cart $cart */
$cart = Session::getCartData();
/** @var array $payment */
$payment = Payment::loadFromSession();
/** @var Domain $domain */
$domain = Domain::getCurrent();

// Variablen für die Anzeige definieren
$cart_items = $cart->getItems();
$with_tax = Warehouse::getPriceInputMode() === 'gross';
$shipping = Shipping::getCost();

?>

<div class="container">
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

	<!-- Lieferadresse -->
	<div class="row mt-4">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="mb-0"><?= Warehouse::getLabel('address_shipping') ?></h3>
				</div>
				<div class="card-body">
					<address>
						<?php
                        // Customer-Daten korrekt laden
                        $customerData = is_array($customer) ? $customer : [];
if (empty($customerData)) {
    // Fallback: Daten aus Session laden
    $customerData = rex_session('user_data', 'array', []);
}

$firma = (isset($customerData['company']) && !empty($customerData['company'])) ? $customerData['company'] . ' ' . ($customerData['department'] ?? '') .'<br>': '';
$ust = (isset($customerData['ust']) && !empty($customerData['ust'])) ? 'Ust. Identnummer: ' . $customerData['ust'] .'<br>': '';
$title = (isset($customerData['title']) && !empty($customerData['title'])) ? ' ' . $customerData['title'] .' ': ' ';

echo "<strong>";
echo ($customerData['salutation'] ?? '') . $title . ($customerData['firstname'] ?? '') . ' ' . ($customerData['lastname'] ?? '');
echo "</strong><br>";
echo $firma . $ust;
echo ($customerData['address'] ?? '') . ' ' . ($customerData['housenumber'] ?? '') . '<br>';
echo ($customerData['zip'] ?? '') . ' ' . ($customerData['city'] ?? '') . '<br>';
echo($customerData['country'] ?? '');
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
					<?= Warehouse::getLabel('paymentoptions_' . ($customerData['payment_type'] ?? '')) ?>
				</div>
			</div>
		</div>
	</div>
</div>


<?php
echo "paypal";
echo $this->getSubfragment('warehouse/bootstrap5/paypal/paypal-button.php');
?>
