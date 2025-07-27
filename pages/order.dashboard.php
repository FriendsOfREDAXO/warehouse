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

// Umsatzvergleich
$current_month = date('Y-m');
$last_month = date('Y-m', strtotime('-1 month'));
$year_ago_month = date('Y-m', strtotime('-1 year'));
$current_year = date('Y');

$revenue_current_month = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("DATE_FORMAT(createdate, '%Y-%m') = ?", [$current_month])
    ->findOne();

$revenue_last_month = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("DATE_FORMAT(createdate, '%Y-%m') = ?", [$last_month])
    ->findOne();

$revenue_year_ago = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("DATE_FORMAT(createdate, '%Y-%m') = ?", [$year_ago_month])
    ->findOne();

$revenue_current_year = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("YEAR(createdate) = ?", [$current_year])
    ->findOne();

$revenue_stats = '<table class="table table-striped">';
$revenue_stats .= '<thead><tr><th>Zeitraum</th><th class="text-right">Umsatz</th><th class="text-right">Vorperiode</th><th class="text-right">Vergleich</th></tr></thead>';
$revenue_stats .= '<tbody>';

// Aktueller Monat
$current_month_value = $revenue_current_month->getValue('total') ?? 0;
$last_month_value = $revenue_last_month->getValue('total') ?? 0;
$month_diff = $current_month_value - $last_month_value;
$month_percent = $last_month_value > 0 ? round(($month_diff / $last_month_value) * 100, 1) : 0;
$month_trend = $month_diff >= 0 ? 'text-success' : 'text-danger';
$month_icon = $month_diff >= 0 ? '↗' : '↘';

$revenue_stats .= '<tr><td><strong>Aktueller Monat</strong></td>';
$revenue_stats .= '<td class="text-right"><strong>' . Warehouse::getCurrencySign() . ' ' . number_format($current_month_value, 2, ',', '.') . '</strong></td>';
$revenue_stats .= '<td class="text-right">' . Warehouse::getCurrencySign() . ' ' . number_format($last_month_value, 2, ',', '.') . '</td>';
$revenue_stats .= '<td class="text-right ' . $month_trend . '"><strong>' . $month_icon . ' ' . ($month_percent > 0 ? '+' : '') . $month_percent . '%</strong></td></tr>';

// Letzte 4 Wochen
$four_weeks_ago = date('Y-m-d', strtotime('-4 weeks'));
$eight_weeks_ago = date('Y-m-d', strtotime('-8 weeks'));
$revenue_last_4_weeks = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("createdate >= ?", [$four_weeks_ago])
    ->findOne();
$revenue_prev_4_weeks = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("createdate >= ? AND createdate < ?", [$eight_weeks_ago, $four_weeks_ago])
    ->findOne();

$last_4_weeks_value = $revenue_last_4_weeks->getValue('total') ?? 0;
$prev_4_weeks_value = $revenue_prev_4_weeks->getValue('total') ?? 0;
$weeks_diff = $last_4_weeks_value - $prev_4_weeks_value;
$weeks_percent = $prev_4_weeks_value > 0 ? round(($weeks_diff / $prev_4_weeks_value) * 100, 1) : 0;
$weeks_trend = $weeks_diff >= 0 ? 'text-success' : 'text-danger';
$weeks_icon = $weeks_diff >= 0 ? '↗' : '↘';

$revenue_stats .= '<tr><td><strong>Letzte 4 Wochen</strong></td>';
$revenue_stats .= '<td class="text-right"><strong>' . Warehouse::getCurrencySign() . ' ' . number_format($last_4_weeks_value, 2, ',', '.') . '</strong></td>';
$revenue_stats .= '<td class="text-right">' . Warehouse::getCurrencySign() . ' ' . number_format($prev_4_weeks_value, 2, ',', '.') . '</td>';
$revenue_stats .= '<td class="text-right ' . $weeks_trend . '"><strong>' . $weeks_icon . ' ' . ($weeks_percent > 0 ? '+' : '') . $weeks_percent . '%</strong></td></tr>';

// Letzte 12 Monate
$twelve_months_ago = date('Y-m-d', strtotime('-12 months'));
$twentyfour_months_ago = date('Y-m-d', strtotime('-24 months'));
$revenue_last_12_months = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("createdate >= ?", [$twelve_months_ago])
    ->findOne();
$revenue_prev_12_months = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("createdate >= ? AND createdate < ?", [$twentyfour_months_ago, $twelve_months_ago])
    ->findOne();

$last_12_months_value = $revenue_last_12_months->getValue('total') ?? 0;
$prev_12_months_value = $revenue_prev_12_months->getValue('total') ?? 0;
$months_diff = $last_12_months_value - $prev_12_months_value;
$months_percent = $prev_12_months_value > 0 ? round(($months_diff / $prev_12_months_value) * 100, 1) : 0;
$months_trend = $months_diff >= 0 ? 'text-success' : 'text-danger';
$months_icon = $months_diff >= 0 ? '↗' : '↘';

$revenue_stats .= '<tr><td><strong>Letzte 12 Monate</strong></td>';
$revenue_stats .= '<td class="text-right"><strong>' . Warehouse::getCurrencySign() . ' ' . number_format($last_12_months_value, 2, ',', '.') . '</strong></td>';
$revenue_stats .= '<td class="text-right">' . Warehouse::getCurrencySign() . ' ' . number_format($prev_12_months_value, 2, ',', '.') . '</td>';
$revenue_stats .= '<td class="text-right ' . $months_trend . '"><strong>' . $months_icon . ' ' . ($months_percent > 0 ? '+' : '') . $months_percent . '%</strong></td></tr>';

// Aktuelles Jahr
$current_year_value = $revenue_current_year->getValue('total') ?? 0;
$last_year = date('Y', strtotime('-1 year'));
$revenue_last_year = Order::query()
    ->selectRaw('COALESCE(SUM(order_total), 0) as total')
    ->whereRaw("YEAR(createdate) = ?", [$last_year])
    ->findOne();
$last_year_value = $revenue_last_year->getValue('total') ?? 0;
$year_diff = $current_year_value - $last_year_value;
$year_percent = $last_year_value > 0 ? round(($year_diff / $last_year_value) * 100, 1) : 0;
$year_trend = $year_diff >= 0 ? 'text-success' : 'text-danger';
$year_icon = $year_diff >= 0 ? '↗' : '↘';

$revenue_stats .= '<tr><td><strong>Aktuelles Jahr</strong></td>';
$revenue_stats .= '<td class="text-right"><strong>' . Warehouse::getCurrencySign() . ' ' . number_format($current_year_value, 2, ',', '.') . '</strong></td>';
$revenue_stats .= '<td class="text-right">' . Warehouse::getCurrencySign() . ' ' . number_format($last_year_value, 2, ',', '.') . '</td>';
$revenue_stats .= '<td class="text-right ' . $year_trend . '"><strong>' . $year_icon . ' ' . ($year_percent > 0 ? '+' : '') . $year_percent . '%</strong></td></tr>';

$revenue_stats .= '</tbody></table>';

// Offene Bestellungen (nicht bezahlte Bestellungen)
$unpaid_orders = Order::query()
    ->where('payed', 0)
    ->find();

$unpaid_count = count($unpaid_orders);
$unpaid_total = 0;
foreach ($unpaid_orders as $order) {
    $unpaid_total += $order->getOrderTotal();
}

$unpaid_stats = '<div class="alert alert-warning">';
$unpaid_stats .= '<h4>' . $unpaid_count . ' offene Bestellungen</h4>';
$unpaid_stats .= '<p>Gesamtwert: <strong>' . Warehouse::getCurrencySign() . ' ' . number_format($unpaid_total, 2, ',', '.') . '</strong></p>';
$unpaid_stats .= '</div>';

// Zu versendende Bestellungen (bezahlt, aber noch nicht versendet)
$paid_unshipped_orders = Order::query()
    ->where('payed', 1)
    ->where('shipping_status', Order::SHIPPING_STATUS_NOT_SHIPPED)
    ->find();

$paid_unshipped_count = count($paid_unshipped_orders);
$paid_unshipped_total = 0;
foreach ($paid_unshipped_orders as $order) {
    $paid_unshipped_total += $order->getOrderTotal();
}

$paid_unshipped_stats = '<div class="alert alert-info">';
$paid_unshipped_stats .= '<h4>' . $paid_unshipped_count . ' zu versendende Bestellungen</h4>';
$paid_unshipped_stats .= '<p>Gesamtwert: <strong>' . Warehouse::getCurrencySign() . ' ' . number_format($paid_unshipped_total, 2, ',', '.') . '</strong></p>';
$paid_unshipped_stats .= '</div>';

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
        // Korrekte URL-Generierung mit CSRF-Token für YForm Manager
        $order_table = rex_yform_manager_table::get('rex_warehouse_order');
        $csrf_params = rex_csrf_token::factory($order_table->getCSRFKey())->getUrlParams();
        $order_edit_url = rex_url::backendPage('warehouse/order/list', array_merge([
            'table_name' => 'rex_warehouse_order',
            'data_id' => $order->getId(),
            'func' => 'edit'
        ], $csrf_params));
        
        $orders_output .= '<tr>';
        $orders_output .= '<td><a href="' . $order_edit_url . '">' . htmlspecialchars($order->getId()) . '</a></td>';
        $orders_output .= '<td><a href="' . $order_edit_url . '">' . htmlspecialchars($order->getCreatedateFormatted()) . '</a></td>';
        $orders_output .= '<td class="text-right"><a href="' . $order_edit_url . '">' . number_format($order->getOrderTotal(), 2, ',') . '</a></td>';
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
    // Korrekte URL-Generierung mit CSRF-Token für YForm Manager
    $customer_table = rex_yform_manager_table::get('rex_ycom_user');
    $csrf_params = rex_csrf_token::factory($customer_table->getCSRFKey())->getUrlParams();
    $customer_edit_url = rex_url::backendPage('warehouse/order/customer', array_merge([
        'table_name' => 'rex_ycom_user',
        'data_id' => $user->getValue('id'),
        'func' => 'edit'
    ], $csrf_params));
    
    $customers_output .= '<tr>';
    $customers_output .= '<td><a href="' . $customer_edit_url . '">' . htmlspecialchars($user->getValue('id')) . '</a></td>';
    $customers_output .= '<td><a href="' . $customer_edit_url . '">' . htmlspecialchars($user->getValue('name')) . '</a></td>';
    $customers_output .= '<td><a href="' . $customer_edit_url . '">' . htmlspecialchars($user->getValue('email')) . '</a></td>';
    $customers_output .= '</tr>';
}
$customers_output .= '</tbody></table>';

// Panels für jede Spalte
$fragment = new rex_fragment();
$fragment->setVar('title', 'Umsatzvergleich', false);
$fragment->setVar('body', $revenue_stats, false);
$revenue_panel = $fragment->parse('core/page/section.php');

$fragment = new rex_fragment();
$fragment->setVar('title', 'Offene Bestellungen', false);
$fragment->setVar('body', $unpaid_stats, false);
$unpaid_panel = $fragment->parse('core/page/section.php');

$fragment = new rex_fragment();
$fragment->setVar('title', 'Zu versendende Bestellungen', false);
$fragment->setVar('body', $paid_unshipped_stats, false);
$paid_unshipped_panel = $fragment->parse('core/page/section.php');

$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('warehouse.order.dashboard.recent_orders'), false);
$fragment->setVar('body', $orders_output, false);
$fragment->setVar('buttons', '<a href="' . rex_url::backendPage('warehouse/order/list') . '" class="btn btn-primary btn-sm"><i class="rex-icon rex-icon-table"></i> Alle Bestellungen anzeigen</a>', false);
$recent_orders_panel = $fragment->parse('core/page/section.php');

$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('warehouse.order.dashboard.orders_by_month'), false);
$fragment->setVar('body', $orders_by_month_output, false);
$orders_by_month_panel = $fragment->parse('core/page/section.php');

$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('warehouse.order.dashboard.recent_customers'), false);
$fragment->setVar('body', $customers_output, false);
$fragment->setVar('buttons', '<a href="' . rex_url::backendPage('warehouse/order/customer') . '" class="btn btn-primary btn-sm"><i class="rex-icon rex-icon-user"></i> Alle Kunden anzeigen</a>', false);
$recent_customers_panel = $fragment->parse('core/page/section.php');

?>
<!-- Erste Reihe: Umsatzvergleich -->
<div class="row">
	<div class="col-md-12">
		<?= $revenue_panel ?>
	</div>
</div>

<!-- Zweite Reihe: Bestellstatus -->
<div class="row">
	<div class="col-md-6">
		<?= $unpaid_panel ?>
	</div>
	<div class="col-md-6">
		<?= $paid_unshipped_panel ?>
	</div>
</div>

<!-- Dritte Reihe: Bestehende Statistiken -->
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
