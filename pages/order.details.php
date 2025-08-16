<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Order;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Shipping;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$table_name = 'rex_warehouse_order';

if (rex_request('data_id', 'int') <= 0) {
    echo rex_view::error($addon->i18n('warehouse.order.details.error.no_data_id'));
    return;
}

$order = Order::get(rex_request('data_id', 'int'));

if (!$order) {
    echo rex_view::error($addon->i18n('warehouse.order.details.error.order_not_found'));
    return;
}

$func = rex_request('func', 'string');
$csrf = rex_csrf_token::factory('warehouse_order_details');

// Handle order actions
if ('' !== $func) {
    if (!$csrf->isValid()) {
        echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    } else {
        switch ($func) {
            case 'update_payment_status':
                $new_status = rex_request('payment_status', 'string');
                if ($new_status && array_key_exists($new_status, Payment::getPaymentStatusOptions())) {
                    $order->setValue('payment_status', $new_status);
                    $order->save();
                    echo rex_view::success($addon->i18n('warehouse.order.details.success.payment_status_updated'));
                } else {
                    echo rex_view::error($addon->i18n('warehouse.order.details.error.invalid_payment_status'));
                }
                break;

            case 'update_shipping_status':
                $new_status = rex_request('shipping_status', 'string');
                if ($new_status && array_key_exists($new_status, Shipping::getShippingStatusOptions())) {
                    $order->setValue('shipping_status', $new_status);
                    $order->save();
                    echo rex_view::success($addon->i18n('warehouse.order.details.success.shipping_status_updated'));
                } else {
                    echo rex_view::error($addon->i18n('warehouse.order.details.error.invalid_shipping_status'));
                }
                break;

            case 'generate_invoice':
                try {
                    $pdfPath = $order->createInvoicePdf();
                    if ($pdfPath) {
                        echo rex_view::success($addon->i18n('warehouse.order.details.success.invoice_generated'));
                    } else {
                        echo rex_view::error($addon->i18n('warehouse.order.details.error.invoice_generation_failed'));
                    }
                } catch (Exception $e) {
                    echo rex_view::error($addon->i18n('warehouse.order.details.error.invoice_generation_failed') . ' ' . $e->getMessage());
                }
                break;

            case 'generate_delivery_note':
                try {
                    $pdfPath = $order->createDeliveryNotePdf();
                    if ($pdfPath) {
                        echo rex_view::success($addon->i18n('warehouse.order.details.success.delivery_note_generated'));
                    } else {
                        echo rex_view::error($addon->i18n('warehouse.order.details.error.delivery_note_generation_failed'));
                    }
                } catch (Exception $e) {
                    echo rex_view::error($addon->i18n('warehouse.order.details.error.delivery_note_generation_failed') . ' ' . $e->getMessage());
                }
                break;
        }
        
        // Reload order after changes
        $order = Order::get(rex_request('data_id', 'int'));
    }
}

?>
<div class="panel">
	<div class="container content">
		<div class="row">
			<div class="col-md-3">
				<h3>Kundendaten</h3>
				<?= $order->getSalutation() ?>
				<?= $order->getFirstname() ?>
				<?= $order->getLastname() ?><br />
				<?php if ($order->getCompany()): ?>
					<?= $order->getCompany() ?><br />
				<?php endif; ?>
				<?= $order->getAddress() ?><br />
				<?= $order->getZip() ?>
				<?= $order->getCity() ?><br />
				<?= $order->getCountry() ?><br />
				<?= $order->getEmail() ?><br />
			</div>
			<div class="col-md-6">
				<h3>Bestelldetails zu Bestell-ID: <?= $order->getId() ?></h3>
				<p>
					<strong>Bestelldatum:</strong>
					<?= $order->getCreateDate() ?><br />
					<strong>Gesamtbetrag:</strong>
					<?= FriendsOfRedaxo\Warehouse\Warehouse::formatCurrency($order->getOrderTotal()) ?><br />
					<strong>Zahlungs-ID:</strong>
					<?= $order->getPaymentId() ?><br />
					<strong>PayPal ID:</strong>
					<?= $order->getPaypalId() ?><br />
					<strong>Aktueller Zahlungsstatus:</strong>
					<span class="badge badge-<?= $order->getValue('payment_status') === Payment::PAYMENT_STATUS_COMPLETED ? 'success' : 'warning' ?>">
						<?= $order->getValue('payment_status') ? Payment::getPaymentStatusOptions()[$order->getValue('payment_status')] ?? $order->getValue('payment_status') : 'Nicht gesetzt' ?>
					</span><br />
					<strong>Aktueller Versandstatus:</strong>
					<span class="badge badge-<?= $order->getValue('shipping_status') === Shipping::SHIPPING_STATUS_SHIPPED ? 'success' : 'warning' ?>">
						<?= $order->getValue('shipping_status') ? Shipping::getShippingStatusOptions()[$order->getValue('shipping_status')] ?? $order->getValue('shipping_status') : 'Nicht gesetzt' ?>
					</span>
				</p>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-header">
						<span class="badge">
							<?= $order->getValue('payment_type') ?? 'Nicht gesetzt' ?>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
// Section: Address Information
$orderJson = $order->getOrderJson(true);
$billingAddress = $orderJson['billing_address'] ?? [];
$shippingAddress = $orderJson['shipping_address'] ?? [];

if (!empty($billingAddress) || !empty($shippingAddress)) {
    $content = '';
    
    // Add CSS for copyable addresses
    $content .= '<style nonce="' . rex_response::getNonce() . '">
        .warehouse-copyable-address {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid #dee2e6;
            transition: background-color 0.3s ease;
        }
        .warehouse-copyable-address:hover {
            background: #e9ecef;
        }
    </style>';
    
    $content .= '<div class="row">';
    
    // Billing Address
    if (!empty($billingAddress)) {
        $content .= '<div class="col-md-6">';
        $content .= '<h4>Rechnungsadresse <small class="text-muted">(Klicken zum Kopieren)</small></h4>';
        $content .= '<div class="warehouse-copyable-address" data-warehouse-copy title="Klicken um Adresse zu kopieren">';
        
        $addressLines = [];
        if (!empty($billingAddress['firstname']) || !empty($billingAddress['lastname'])) {
            $addressLines[] = trim(($billingAddress['firstname'] ?? '') . ' ' . ($billingAddress['lastname'] ?? ''));
        }
        if (!empty($billingAddress['company'])) {
            $addressLines[] = $billingAddress['company'];
        }
        if (!empty($billingAddress['department'])) {
            $addressLines[] = $billingAddress['department'];
        }
        if (!empty($billingAddress['address'])) {
            $addressLines[] = $billingAddress['address'];
        }
        if (!empty($billingAddress['zip']) || !empty($billingAddress['city'])) {
            $addressLines[] = trim(($billingAddress['zip'] ?? '') . ' ' . ($billingAddress['city'] ?? ''));
        }
        if (!empty($billingAddress['country'])) {
            $addressLines[] = $billingAddress['country'];
        }
        if (!empty($billingAddress['email'])) {
            $addressLines[] = $billingAddress['email'];
        }
        if (!empty($billingAddress['phone'])) {
            $addressLines[] = $billingAddress['phone'];
        }
        
        $content .= implode('<br>', array_filter(array_map('htmlspecialchars', $addressLines)));
        $content .= '</div>';
        $content .= '</div>';
    }
    
    // Shipping Address
    if (!empty($shippingAddress)) {
        $content .= '<div class="col-md-6">';
        $content .= '<h4>Lieferadresse <small class="text-muted">(Klicken zum Kopieren)</small></h4>';
        $content .= '<div class="warehouse-copyable-address" data-warehouse-copy title="Klicken um Adresse zu kopieren">';
        
        $addressLines = [];
        if (!empty($shippingAddress['firstname']) || !empty($shippingAddress['lastname'])) {
            $addressLines[] = trim(($shippingAddress['firstname'] ?? '') . ' ' . ($shippingAddress['lastname'] ?? ''));
        }
        if (!empty($shippingAddress['company'])) {
            $addressLines[] = $shippingAddress['company'];
        }
        if (!empty($shippingAddress['address'])) {
            $addressLines[] = $shippingAddress['address'];
        }
        if (!empty($shippingAddress['zip']) || !empty($shippingAddress['city'])) {
            $addressLines[] = trim(($shippingAddress['zip'] ?? '') . ' ' . ($shippingAddress['city'] ?? ''));
        }
        if (!empty($shippingAddress['country'])) {
            $addressLines[] = $shippingAddress['country'];
        }
        
        $content .= implode('<br>', array_filter(array_map('htmlspecialchars', $addressLines)));
        $content .= '</div>';
        $content .= '</div>';
    }
    
    $content .= '</div>';
    
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'info', false);
    $fragment->setVar('title', 'Adressdaten', false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
}

// Section 1: Payment Status Actions
$content = '';
$content .= '<p>Ändern Sie den Zahlungsstatus der Bestellung.</p>';
$content .= '<form method="post" class="form-inline">';
$content .= '<input type="hidden" name="func" value="update_payment_status">';
$content .= '<input type="hidden" name="data_id" value="' . $order->getId() . '">';
foreach ($csrf->getUrlParams() as $name => $value) {
    $content .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
}
$content .= '<div class="input-group">';
$content .= '<select name="payment_status" class="form-control" required>';
$content .= '<option value="">Status wählen...</option>';
foreach (Payment::getPaymentStatusOptions() as $key => $label) {
    $selected = ($order->getValue('payment_status') === $key) ? 'selected' : '';
    $content .= '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
}
$content .= '</select>';
$content .= '<span class="input-group-btn">';
$content .= '<button type="submit" class="btn btn-primary">Zahlungsstatus aktualisieren</button>';
$content .= '</span>';
$content .= '</div>';
$content .= '</form>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', 'Zahlungsstatus ändern', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Section 2: Shipping Status Actions
$content = '';
$content .= '<p>Ändern Sie den Versandstatus der Bestellung.</p>';
$content .= '<form method="post" class="form-inline">';
$content .= '<input type="hidden" name="func" value="update_shipping_status">';
$content .= '<input type="hidden" name="data_id" value="' . $order->getId() . '">';
foreach ($csrf->getUrlParams() as $name => $value) {
    $content .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
}
$content .= '<div class="input-group">';
$content .= '<select name="shipping_status" class="form-control" required>';
$content .= '<option value="">Status wählen...</option>';
foreach (Shipping::getShippingStatusOptions() as $key => $label) {
    $selected = ($order->getValue('shipping_status') === $key) ? 'selected' : '';
    $content .= '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
}
$content .= '</select>';
$content .= '<span class="input-group-btn">';
$content .= '<button type="submit" class="btn btn-primary">Versandstatus aktualisieren</button>';
$content .= '</span>';
$content .= '</div>';
$content .= '</form>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', 'Versandstatus ändern', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Section 3: Document Generation
$content = '';
$content .= '<p>Generieren Sie Dokumente für diese Bestellung.</p>';
$content .= '<div class="btn-group" role="group">';
if (rex_addon::get('pdfout')->isAvailable()) {
    $content .= '<a class="btn btn-success" href="' . rex_url::currentBackendPage(['func' => 'generate_invoice', 'data_id' => $order->getId()] + $csrf->getUrlParams()) . '" data-confirm="Rechnung für Bestellung ' . $order->getId() . ' generieren?">Rechnung generieren</a>';
    $content .= '<a class="btn btn-warning" href="' . rex_url::currentBackendPage(['func' => 'generate_delivery_note', 'data_id' => $order->getId()] + $csrf->getUrlParams()) . '" data-confirm="Lieferschein für Bestellung ' . $order->getId() . ' generieren?">Lieferschein generieren</a>';
} else {
    $content .= '<div class="alert alert-warning">Das PDFOut Add-on ist nicht verfügbar. Dokumentenerstellung nicht möglich.</div>';
}
$content .= '</div>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'default', false);
$fragment->setVar('title', 'Dokumente generieren', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

// Section 4: Extension Point for additional actions
$additional_actions = [];
$additional_actions = rex_extension::registerPoint(new rex_extension_point(
    'WAREHOUSE_ORDER_DETAIL_ACTIONS',
    $additional_actions,
    [
        'order' => $order,
        'csrf' => $csrf
    ]
));

if (!empty($additional_actions)) {
    $content = '';
    $content .= '<p>Zusätzliche Aktionen für diese Bestellung.</p>';
    $content .= '<div class="btn-group" role="group">';
    foreach ($additional_actions as $action) {
        if (isset($action['content'])) {
            $content .= $action['content'];
        }
    }
    $content .= '</div>';

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'default', false);
    $fragment->setVar('title', 'Weitere Aktionen', false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
}
?>
