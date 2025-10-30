<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Order;

?>
<!-- BEGIN my_orders -->
<?php
if ($order_id = rex_get('order_id', 'int')) {
    // Detailseite
    $order = Order::findByUuid((string) $order_id);
    if ($order) {
        $fragment = new rex_fragment();
        $fragment->setVar('order', $order);
        echo $fragment->parse('warehouse/bootstrap5/my_orders/details.php');
    } else {
        echo '<p>' . Warehouse::getLabel('order_not_found') . '</p>';
        echo '<p><a href="' . rex_getUrl() . '">' . Warehouse::getLabel('order_back_to_overview') . '</a></p>';
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
