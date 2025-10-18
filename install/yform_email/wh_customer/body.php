<?php
/** @var rex_yform $yform */

// Get order_id from form field value
$order_id = 0;
$order_id_field = $yform->getValueField('order_id');
if ($order_id_field) {
    $order_id = $order_id_field->getValue();
}

echo rex_fragment::factory('warehouse/emails/order_customer_text.php')->parse([
    'order_id' => $order_id,
]);
