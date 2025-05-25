<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Order;
?>
<!-- BEGIN my_orders -->
<?php
if ($order_id = rex_get('order_id', 'int')) {
    // Detailseite
    $order = Order::findByUuid($order_id);
    if ($order) {
        $fragment = new rex_fragment();
        $fragment->setVar('order', $order);
        echo $fragment->parse('warehouse/bootstrap5/my_orders/details.php');
    } else {
        echo '<p>{{ Bestellung nicht gefunden }}</p>';
        echo '<p><a href="' . rex_getUrl() . '">{{ Zur Übersicht }}</a></p>';
    }
} else {
    // Listendarstellung
    $orders = Order::findByYComUserId();
    $fragment = new rex_fragment();
    $fragment->setVar('orders', $orders);
    echo $fragment->parse('warehouse/bootstrap5/my_orders/list.php');
}
?>
<!-- END my_orders -->
