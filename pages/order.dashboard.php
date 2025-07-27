<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Order;
use FriendsOfRedaxo\Warehouse\Warehouse;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

// Die letzten 5 Bestellungen
$orders = Order::query()->orderBy('createdate', 'DESC')
    ->limit(5)
    ->find();

// Ausgabe als Tabelle mit ID, Datum und Gesamtbetrag
if (count($orders) > 0) {
    $orders_output = '<table class="table table-striped">';
    $orders_output .= '<thead><tr><th>#</th><th>' . $addon->i18n('warehouse.order.dashboard.date') . '</th><th class="text-right">'.Warehouse::getCurrencySign().'</th></tr></thead>';
    $orders_output .= '<tbody>';
    foreach ($orders as $order) {
        $orders_output .= '<tr>';
        $orders_output .= '<td>' . htmlspecialchars($order->getId()) . '</td>';
        $orders_output .= '<td>' . htmlspecialchars($order->getCreatedateFormatted()) . '</td>';
        $orders_output .= '<td class="text-right">' . number_format($order->getOrderTotal(), 2, ',') . '</td>';
        $orders_output .= '</tr>';
    }
    $orders_output .= '</tbody></table>';
    $orders_output;
} else {
    $orders_output = '<p>' . $addon->i18n('warehouse.order.dashboard.no_orders') . '</p>';
}
// Summe der letzten Bestellungen je Monat - 5 Monate
$orders_by_month = Order::query()
    ->selectRaw('DATE_FORMAT(createdate, "%Y-%m") as month, SUM(order_total) as order_total')
    ->groupBy('month')
    ->orderBy('month', 'DESC')
    ->limit(5)
    ->find();

// Backend-Sprache ermitteln
$lang = rex_i18n::getLocale();
setlocale(LC_TIME, $lang . '.UTF-8');

$orders_by_month_output = '<table class="table table-striped">';
$orders_by_month_output .= '<thead><tr><th>' . $addon->i18n('warehouse.order.dashboard.month') . '</th><th>' . $addon->i18n('warehouse.order.dashboard.total') . '</th></tr></thead>';
$orders_by_month_output .= '<tbody>';
foreach ($orders_by_month as $order) {
    $timestamp = strtotime($order->month . '-01');
    // Monat lokalisiert ausgeben
    $month_localized = rex_formatter::intlDate($timestamp, 'MMMM yyyy');
    $orders_by_month_output .= '<tr>';
    $orders_by_month_output .= '<td>' . htmlspecialchars($month_localized) . '</td>';
    $orders_by_month_output .= '<td>' . Warehouse::getCurrencySign() . htmlspecialchars(number_format($order->getOrderTotal(), 2)) . '</td>';
    $orders_by_month_output .= '</tr>';
}
$orders_by_month_output .= '</tbody></table>';

// Die letzten 5 Registrierungen in ycom_user

$users = Customer::query()
    ->orderBy('id', 'DESC')
    ->limit(5)
    ->find();

$customers_output = '<table class="table table-striped">';
$customers_output .= '<thead><tr><th>#</th><th>' . $addon->i18n('warehouse.order.dashboard.customer.name') . '</th><th>' . $addon->i18n('warehouse.order.dashboard.customer.email') . '</th></tr></thead>';
$customers_output .= '<tbody>';
foreach ($users as $user) {
    /** @var ycom_user $user */
    $customers_output .= '<tr>';
    $customers_output .= '<td>' . htmlspecialchars($user->getValue('id')) . '</td>';
    $customers_output .= '<td>' . htmlspecialchars($user->getValue('name')) . '</td>';
    $customers_output .= '<td>' . htmlspecialchars($user->getValue('email')) . '</td>';
    $customers_output .= '</tr>';
}
$customers_output .= '</tbody></table>';

// Panels für jede Spalte
$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('warehouse.order.dashboard.recent_orders'), false);
$fragment->setVar('body', $orders_output, false);
$recent_orders_panel = $fragment->parse('core/page/section.php');

$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('warehouse.order.dashboard.orders_by_month'), false);
$fragment->setVar('body', $orders_by_month_output, false);
$orders_by_month_panel = $fragment->parse('core/page/section.php');

$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('warehouse.order.dashboard.recent_customers'), false);
$fragment->setVar('body', $customers_output, false);
$recent_customers_panel = $fragment->parse('core/page/section.php');

?>
<div class="row">
	<div class="col-md-4">
		<?= $recent_orders_panel ?>
	</div>
	<div class="col-md-4">
		<?= $orders_by_month_panel ?>
	</div>
	<div class="col-md-4">
		<?= $recent_customers_panel ?>
	</div>
</div>