<?php

namespace FriendsOfRedaxo\Warehouse;

/** @var rex_fragment $this */

$order_id = $this->getVar('order_id', null);
$order = Order::get($order_id);
if (!$order) {
    throw new \Exception('Order not found for the given order ID: ' . $order_id);
}

?>
Sehr geehrte(r) <?= htmlspecialchars($order->getValue('salutation')) ?> <?= htmlspecialchars($order->getValue('firstname')) ?> <?= htmlspecialchars($order->getValue('lastname')) ?>,

vielen Dank für Ihre Bestellung im Demo-Onlineshop! Falls Sie Änderungen an Ihrer Bestellung vornehmen oder den Status Ihrer Bestellung abfragen möchten, wenden Sie sich bitte direkt an uns:

Telefon: 0123 4567890
E-Mail: info@demo-firma.de

<?php if ($order->getPaymentId() == "paypal") : ?>
Ihre Zahlung ist auf unserem PayPal-Konto eingegangen; wir werden nun Ihre Bestellung schnellstens auf den Weg zu Ihnen bringen.
<?php endif ?>

<?php if ($order->getPaymentId() == "prepayment") : ?>
Sobald wir Ihren Zahlungseingang auf unserem Konto feststellen, werden wir Ihre Bestellung schnellstens auf den Weg zu Ihnen bringen.
<?php endif ?>

Hier im Anhang finden Sie die Rechnung, die gesetzlich vorgeschriebene Belehrung und eine Vorlage zum Widerruf.

Wir bestätigen Ihre Bestellung wie folgt:

<?php echo Warehouse::getOrderAsText(); ?>


<?php echo Warehouse::getCustomerDataAsText($order_id); ?>


<?php if ($order->getPaymentId() == "prepayment") : ?>
Ihre gewünschte Zahlungsweise: Vorkasse

Verwendungszweck:
Kontoinhaber: Demo GmbH & Co. KG
IBAN: DE00 0000 0000 0000 0000 00
BIC: DEMOBIC1XXX
Bank: Musterbank AG
<?php endif ?>

---

<?= Warehouse::getConfig('email_signature') ?>
