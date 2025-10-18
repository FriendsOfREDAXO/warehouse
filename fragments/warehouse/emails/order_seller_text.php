<?php

namespace FriendsOfRedaxo\Warehouse;

/** @var rex_fragment $this */

$order_id = $this->getVar('order_id', null);
$domain_id = $this->getVar('domain_id', null);
$email_from_email = $this->getVar('email_from_email', '');
$email_from_name = $this->getVar('email_from_name', '');

$order = Order::get($order_id);
if (!$order) {
    throw new \Exception('Order not found for the given order ID: ' . $order_id);
}

?>
Wir haben folgende Bestellung erhalten:

<?php echo Warehouse::getOrderAsText($order_id); ?>


<?php echo Warehouse::getCustomerDataAsText($order_id); ?>


<?php if ($order->getPaymentId() == "prepayment") : ?>
Gewünschte Zahlungsweise: Vorkasse
<?php endif ?>

---

<?= Warehouse::getConfig('email_signature') ?>
