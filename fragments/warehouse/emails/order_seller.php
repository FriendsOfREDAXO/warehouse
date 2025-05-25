<?php

namespace FriendsOfRedaxo\Warehouse;

/** @var rex_fragment $this */

$order_id = $this->getVar('order_id', null);
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
        <?php echo warehouse::getOrderAsHtml(); ?>
    </div>

    <div style="margin: 20px 0; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
        <?php echo warehouse::getCustomerData(); ?>
    </div>

    <?php if ("REX_YFORM_DATA[field=payment_type]" == "prepayment") : ?>
        <p><b>Gewünschte Zahlungsweise:</b> Vorkasse</p>
    <?php endif ?>

    <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">

    <?= Warehouse::getConfig('email_signature') ?>
</body>
</html>
