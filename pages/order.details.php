<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$table_name = 'rex_warehouse_order';

if (rex_request('data_id', 'int') <= 0) {
    echo rex_view::error($addon->i18n('warehouse.order.details.error.no_data_id'));
    return;
}

$order = FriendsOfRedaxo\Warehouse\Order::get(rex_request('data_id', 'int'));

if (!$order) {
    echo rex_view::error($addon->i18n('warehouse.order.details.error.order_not_found'));
    return;
}

?>
<div class="panel">
	<div class="container content">
		<div class="row">
			<div class="col-md-3">
				<?= $order->getSalutation() ?>
				<?= $order->getFirstname() ?>
				<?= $order->getLastname() ?><br />
				<?= $order->getCompany() ?><br />
				<?= $order->getAddress() ?>
				<?= $order->getZip() ?>
				<?= $order->getCity() ?><br />
				<?= $order->getCountry() ?><br />
				<?= $order->getEmail() ?><br />
			</div>
			<div class="col-md-6"></div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-header">
						<span class="badge">
							<?= '' # $order->getPaymentType()?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h1>Bestelldetails zu Bestell-ID:
					<?= $order->getId() ?>
			</div>
		</div>
		<div class="row cart">
			<div class="col-md-12">
			</div>
		</div>
		<div class="row additional-details">
			<div class="col-md-12">
				<h2>Zusätzliche Details</h2>
				<p>
					<strong>Bestelldatum:</strong>
					<?= $order->getCreateDate() ?><br />
					<strong>Gesamtbetrag:</strong>
					<?= $order->getOrderTotal() ?>
					<?= FriendsOfRedaxo\Warehouse\Warehouse::getCurrencySign() ?><br />
					<strong>Zahlungs-ID:</strong>
					<?= $order->getPaymentId() ?><br />
					<strong>PayPal ID:</strong>
					<?= $order->getPaypalId() ?><br />
				</p>
			</div>
		</div>
	</div>
</div>
