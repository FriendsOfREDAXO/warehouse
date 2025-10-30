<?php

namespace FriendsOfRedaxo\Warehouse;

/** @var rex_fragment $this */

$order_id = $this->getVar('order_id', null);
// Available for future use: domain settings and email sender information
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
    <title>Bestellbestätigung</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Sehr geehrte(r) <b>REX_YFORM_DATA[field="salutation"] REX_YFORM_DATA[field="firstname"] REX_YFORM_DATA[field="lastname"]</b>,</p>

    <p>vielen Dank für Ihre Bestellung im Demo-Onlineshop! Falls Sie Änderungen an Ihrer Bestellung vornehmen oder den Status Ihrer Bestellung abfragen möchten, wenden Sie sich bitte direkt an uns:</p>
    <p>
        <b>Telefon:</b> 0123 4567890<br>
        <b>E-Mail:</b> <a href="mailto:info@demo-firma.de">info@demo-firma.de</a>
    </p>

    <?php if ('paypal' == $order->getPaymentId()) : ?>
        <p>Ihre Zahlung ist auf unserem PayPal-Konto eingegangen; wir werden nun Ihre Bestellung schnellstens auf den Weg zu Ihnen bringen.</p>
    <?php endif ?>

    <?php if ('prepayment' == $order->getPaymentId()) : ?>
        <p>Sobald wir Ihren Zahlungseingang auf unserem Konto feststellen, werden wir Ihre Bestellung schnellstens auf den Weg zu Ihnen bringen.</p>
    <?php endif ?>

    <p>Hier im Anhang finden Sie die Rechnung, die gesetzlich vorgeschriebene Belehrung und eine Vorlage zum Widerruf.</p>

    <p>Wir bestätigen Ihre Bestellung wie folgt:</p>

    <div style="margin: 20px 0; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
        <?= Warehouse::getOrderAsHtml() ?>
    </div>

    <div style="margin: 20px 0; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
        <?= Warehouse::getCustomerDataAsText() ?>
    </div>

    <?php if ('prepayment' == $order->getPaymentId()) : ?>
        <p><b>Ihre gewünschte Zahlungsweise:</b> Vorkasse</p>
        <p style="font-size: 14px;">
            <b>Verwendungszweck:</b><br>
            <b>Kontoinhaber:</b> Demo GmbH & Co. KG<br>
            <b>IBAN:</b> DE00 0000 0000 0000 0000 00<br>
            <b>BIC:</b> DEMOBIC1XXX<br>
            <b>Bank:</b> Musterbank AG
        </p>
    <?php endif ?>

    <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">

    <?= Warehouse::getConfig('email_signature') ?>
</body>

</html>
