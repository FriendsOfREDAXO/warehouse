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
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eine neue Bestellung ist eingetroffen!</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Wir haben folgende Bestellung erhalten:</p>

    <div style="margin: 20px 0; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
        <?php echo Warehouse::getOrderAsHtml(); ?>
    </div>

    <div style="margin: 20px 0; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
        <?php echo Warehouse::getCustomerDataAsText($order_id); ?>
    </div>

    <?php if ($order->getPaymentId() == "prepayment") : ?>
        <p><b>Gewünschte Zahlungsweise:</b> Vorkasse</p>
    <?php endif ?>

    <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">

    <?= Warehouse::getConfig('email_signature') ?>
</body>
</html>
