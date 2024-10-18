<?php /* Meine Bestellungen - Output */


if ($order_id = rex_get('order_id','int')) {
    // Detail
    $order = \FriendsOfRedaxo\Warehouse\Order::get_order_for_user($order_id);
    if ($order) {
        $fragment = new rex_fragment();
        $fragment->setVar('order',$order);
        echo $fragment->parse('warehouse_order_page.php');    
    } else {
        echo '<p class="uk-text-center">{{ Bestellung nicht gefunden }}</p>';
        echo '<p class="uk-text-center"><a href="'.rex_getUrl().'">{{ Zur Übersicht }}</a></p>';
    }
} else {
    // Listendarstellung
    $orders = \FriendsOfRedaxo\Warehouse\Order::get_orders_for_user();
    $fragment = new rex_fragment();
    $fragment->setVar('orders',$orders);
    echo $fragment->parse('warehouse_orders_page.php');    
}

?>
