<?php

namespace FriendsOfRedaxo\Warehouse;

/** @var rex_fragment $this */

$order_id = $this->getVar('order_id', null);
$order = Order::get($order_id);
if (!$order) {
    throw new \Exception('Order not found for the given order ID: ' . $order_id);
}

?>
Wir haben folgende Bestellung erhalten:

<?php echo Warehouse::getOrderAsText(); ?>


<?php echo Warehouse::getCustomerDataAsText($order_id); ?>


<?php if ($order->getPaymentId() == "prepayment") : ?>
Gewünschte Zahlungsweise: Vorkasse
<?php endif ?>

---

<?= Warehouse::getConfig('email_signature') ?>
