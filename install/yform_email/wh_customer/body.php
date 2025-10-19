<?php

/** @var rex_yform $yform */

// Get order_id from form field value
$order_id = 0;
$order_id_field = $yform->getValueField('order_id');
if ($order_id_field) {
    $order_id = (int) $order_id_field->getValue();
}

// Get domain_id from form field value
$domain_id = 0;
$domain_id_field = $yform->getValueField('domain_id');
if ($domain_id_field) {
    $domain_id = (int) $domain_id_field->getValue();
}

// Get email_from_email from form field value
$email_from_email = '';
$email_from_email_field = $yform->getValueField('email_from_email');
if ($email_from_email_field) {
    $email_from_email = (string) $email_from_email_field->getValue();
}

// Get email_from_name from form field value
$email_from_name = '';
$email_from_name_field = $yform->getValueField('email_from_name');
if ($email_from_name_field) {
    $email_from_name = (string) $email_from_name_field->getValue();
}

echo rex_fragment::factory('warehouse/emails/order_customer_text.php')->parse([
    'order_id' => $order_id,
    'domain_id' => $domain_id,
    'email_from_email' => $email_from_email,
    'email_from_name' => $email_from_name,
]);
