<?php

/**
 * Warehouse Dashboard Class
 *
 * @package FriendsOfRedaxo\Warehouse
 */

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_addon_interface;
use rex_fragment;
use rex_url;
use rex_yform_manager_table;
use rex_csrf_token;
use rex_formatter;
use rex_i18n;
use ycom_user;

class Dashboard
{
    private rex_addon_interface $addon;

    public function __construct()
    {
        $this->addon = rex_addon::get('warehouse');
    }

    /**
     * Erstellt den Umsatzvergleich
     */
    public function getRevenueComparisonSection(): string
    {
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
        $revenue_stats .= '<td class="text-right"><strong>' . Warehouse::formatCurrency($current_month_value) . '</strong></td>';
        $revenue_stats .= '<td class="text-right">' . Warehouse::formatCurrency($last_month_value) . '</td>';
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
        $revenue_stats .= '<td class="text-right"><strong>' . Warehouse::formatCurrency($last_4_weeks_value) . '</strong></td>';
        $revenue_stats .= '<td class="text-right">' . Warehouse::formatCurrency($prev_4_weeks_value) . '</td>';
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
        $revenue_stats .= '<td class="text-right"><strong>' . Warehouse::formatCurrency($last_12_months_value) . '</strong></td>';
        $revenue_stats .= '<td class="text-right">' . Warehouse::formatCurrency($prev_12_months_value) . '</td>';
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
        $revenue_stats .= '<td class="text-right"><strong>' . Warehouse::formatCurrency($current_year_value) . '</strong></td>';
        $revenue_stats .= '<td class="text-right">' . Warehouse::formatCurrency($last_year_value) . '</td>';
        $revenue_stats .= '<td class="text-right ' . $year_trend . '"><strong>' . $year_icon . ' ' . ($year_percent > 0 ? '+' : '') . $year_percent . '%</strong></td></tr>';

        $revenue_stats .= '</tbody></table>';

        $fragment = new rex_fragment();
        $fragment->setVar('title', 'Umsatzvergleich', false);
        $fragment->setVar('body', $revenue_stats, false);
        return $fragment->parse('core/page/section.php');
    }

    /**
     * Erstellt die Section für offene Bestellungen
     */
    public function getUnpaidOrdersSection(): string
    {
        $unpaid_orders = Order::query()
            ->where(Order::PAYMENT_STATUS, PAYMENT::PAYMENT_STATUS_PENDING)
            ->find();

        $unpaid_count = count($unpaid_orders);
        $unpaid_total = 0;
        foreach ($unpaid_orders as $order) {
            $unpaid_total += $order->getOrderTotal();
        }

        $unpaid_stats = '<div class="alert alert-warning">';
        $unpaid_stats .= '<h4>' . $unpaid_count . ' offene Bestellungen</h4>';
        $unpaid_stats .= '<p>Gesamtwert: <strong>' . Warehouse::formatCurrency($unpaid_total) . '</strong></p>';
        $unpaid_stats .= '</div>';

        $fragment = new rex_fragment();
        $fragment->setVar('title', 'Offene Bestellungen', false);
        $fragment->setVar('body', $unpaid_stats, false);
        return $fragment->parse('core/page/section.php');
    }

    /**
     * Erstellt die Section für zu versendende Bestellungen
     */
    public function getShippingOrdersSection(): string
    {
        $paid_unshipped_orders = Order::query()
            ->where(Order::PAYMENT_STATUS, PAYMENT::PAYMENT_STATUS_COMPLETED)
            ->where('shipping_status', Shipping::SHIPPING_STATUS_NOT_SHIPPED)
            ->find();

        $paid_unshipped_count = count($paid_unshipped_orders);
        $paid_unshipped_total = 0;
        foreach ($paid_unshipped_orders as $order) {
            $paid_unshipped_total += $order->getOrderTotal();
        }

        $paid_unshipped_stats = '<div class="alert alert-info">';
        $paid_unshipped_stats .= '<h4>' . $paid_unshipped_count . ' zu versendende Bestellungen</h4>';
        $paid_unshipped_stats .= '<p>Gesamtwert: <strong>' . Warehouse::formatCurrency($paid_unshipped_total) . '</strong></p>';
        $paid_unshipped_stats .= '</div>';

        $fragment = new rex_fragment();
        $fragment->setVar('title', 'Zu versendende Bestellungen', false);
        $fragment->setVar('body', $paid_unshipped_stats, false);
        return $fragment->parse('core/page/section.php');
    }

    /**
     * Erstellt die Section für neueste Bestellungen
     */
    public function getRecentOrdersSection(): string
    {
        $orders = Order::query()->orderBy('createdate', 'DESC')
            ->limit(5)
            ->find();

        if (count($orders) > 0) {
            $orders_output = '<table class="table table-striped">';
            $orders_output .= '<thead><tr><th>#</th><th>' . $this->addon->i18n('warehouse.order.dashboard.date') . '</th><th class="text-right">'.Warehouse::getCurrency().'</th></tr></thead>';
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
                $orders_output .= '<td class="text-right"><a href="' . $order_edit_url . '">' . Warehouse::formatCurrency($order->getOrderTotal()) . '</a></td>';
                $orders_output .= '</tr>';
            }
            $orders_output .= '</tbody></table>';
        } else {
            $orders_output = '<p>' . $this->addon->i18n('warehouse.order.dashboard.no_orders') . '</p>';
        }

        $fragment = new rex_fragment();
        $fragment->setVar('title', $this->addon->i18n('warehouse.order.dashboard.recent_orders'), false);
        $fragment->setVar('body', $orders_output, false);
        $fragment->setVar('buttons', '<a href="' . rex_url::backendPage('warehouse/order/list') . '" class="btn btn-primary btn-sm"><i class="rex-icon rex-icon-table"></i> Alle Bestellungen anzeigen</a>', false);
        return $fragment->parse('core/page/section.php');
    }

    /**
     * Erstellt die Section für Bestellungen nach Monaten
     */
    public function getOrdersByMonthSection(): string
    {
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
        $orders_by_month_output .= '<thead><tr><th>' . $this->addon->i18n('warehouse.order.dashboard.month') . '</th><th>' . $this->addon->i18n('warehouse.order.dashboard.total') . '</th></tr></thead>';
        $orders_by_month_output .= '<tbody>';
        foreach ($orders_by_month as $order) {
            $timestamp = strtotime($order->month . '-01');
            // Monat lokalisiert ausgeben
            $month_localized = rex_formatter::intlDate($timestamp, 'MMMM yyyy');
            $orders_by_month_output .= '<tr>';
            $orders_by_month_output .= '<td>' . htmlspecialchars($month_localized) . '</td>';
            $orders_by_month_output .= '<td>' . htmlspecialchars(Warehouse::formatCurrency($order->getOrderTotal())) . '</td>';
            $orders_by_month_output .= '</tr>';
        }
        $orders_by_month_output .= '</tbody></table>';

        $fragment = new rex_fragment();
        $fragment->setVar('title', $this->addon->i18n('warehouse.order.dashboard.orders_by_month'), false);
        $fragment->setVar('body', $orders_by_month_output, false);
        return $fragment->parse('core/page/section.php');
    }

    /**
     * Erstellt die Section für neueste Kunden
     */
    public function getRecentCustomersSection(): string
    {
        $users = Customer::query()
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->find();

        $customers_output = '<table class="table table-striped">';
        $customers_output .= '<thead><tr><th>#</th><th>' . $this->addon->i18n('warehouse.order.dashboard.customer.name') . '</th><th>' . $this->addon->i18n('warehouse.order.dashboard.customer.email') . '</th></tr></thead>';
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

        $fragment = new rex_fragment();
        $fragment->setVar('title', $this->addon->i18n('warehouse.order.dashboard.recent_customers'), false);
        $fragment->setVar('body', $customers_output, false);
        $fragment->setVar('buttons', '<a href="' . rex_url::backendPage('warehouse/order/customer') . '" class="btn btn-primary btn-sm"><i class="rex-icon rex-icon-user"></i> Alle Kunden anzeigen</a>', false);
        return $fragment->parse('core/page/section.php');
    }

    /**
     * Erstellt das komplette Dashboard-Layout als mehrdimensionales Array
     *
     * @return array Struktur: [row_key => [col_key => ['col' => int, 'content' => string]]]
     */
    public function getDashboardLayout(): array
    {
        $layout = [
            'revenue_row' => [
                'revenue_comparison' => [
                    'col' => 12,
                    'content' => $this->getRevenueComparisonSection()
                ]
            ],
            'status_row' => [
                'unpaid_orders' => [
                    'col' => 6,
                    'content' => $this->getUnpaidOrdersSection()
                ],
                'shipping_orders' => [
                    'col' => 6,
                    'content' => $this->getShippingOrdersSection()
                ]
            ],
            'statistics_row' => [
                'recent_orders' => [
                    'col' => 4,
                    'content' => $this->getRecentOrdersSection()
                ],
                'orders_by_month' => [
                    'col' => 4,
                    'content' => $this->getOrdersByMonthSection()
                ],
                'recent_customers' => [
                    'col' => 4,
                    'content' => $this->getRecentCustomersSection()
                ]
            ]
        ];

        // Extension Point für Dashboard-Modifikation
        return \rex_extension::registerPoint(new \rex_extension_point('WAREHOUSE_DASHBOARD', $layout, [
            'dashboard' => $this
        ]));
    }
}
