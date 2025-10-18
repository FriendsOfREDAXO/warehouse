<?php
/** @var rex_yform $yform */

$order_id = $yform->getObjectparams('order_id') ?? rex_request('order_id', 'int', 0);

echo rex_fragment::factory('warehouse/emails/order_customer_text.php')->parse([
    'order_id' => $order_id,
]);
