<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Session;
use FriendsOfRedaxo\Warehouse\Warehouse;

$customer = $this->getVar('customer');
$cart = Session::getCartData();
$payment = Session::getPaymentData();
$domain = Domain::getCurrent();
$warehouse_cart_text = '';

$this->subfragment('warehouse/bootstrap5/checkout/order_summary_page.php');

$yform = new rex_yform();
$yform->setObjectparams('form_action', $domain?->getCheckoutUrl(['continue_with' => 'summary']) ?? '');
$yform->setObjectparams('form_class', 'rex-yform wh-form summary');
$yform->setObjectparams('form_anchor', 'formular');
$yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
$yform->setObjectparams('form_name', 'warehouse_checkout_summary');
$yform->setObjectparams('real_field_names', true);

// Bestellübersicht anzeigen
$yform->setValueField('html', ['', $warehouse_cart_text]);

$yform->setValueField('privacy_policy', ['agb', Warehouse::getLabel('legal_agb_privacy'), '0,1', '0']);
$yform->setValidateField('empty', ['agb', Warehouse::getLabel('validation_agb_required')]);
$yform->setValueField('privacy_policy', ['privacy_policy', Warehouse::getLabel('legal_privacy_policy'), '0,1', '0']);
$yform->setValidateField('empty', ['privacy_policy', Warehouse::getLabel('validation_privacy_required')]);

$yform->setValueField('submit_once', ['send', Warehouse::getLabel('label_checkout_submit_order'), Warehouse::getLabel('label_checkout_submit_order_wait'), '[no_db]', '', 'btn btn-primary mt-3']);
/*
if (in_array($customer['payment_type'],['invoice','prepayment','direct_debit'])) {
    $yform->setActionField('callback', ['FriendsOfRedaxo\Warehouse\Warehouse::save_order']);
    foreach (explode(',', rex_config::get('warehouse', 'order_email')) as $email) {
        $yform->setActionField('tpl2email', [rex_config::get('warehouse', 'email_template_seller'), $email]);
    }
    $yform->setActionField('tpl2email', [rex_config::get('warehouse', 'email_template_customer'), 'email']);
    $yform->setActionField('callback', ['FriendsOfRedaxo\Warehouse\Warehouse::clear_cart']);
    $yform->setActionField('redirect', [rex_config::get('warehouse', 'thankyou_page')]);
} elseif ($customer['payment_type'] == 'paypal') {
    $yform->setActionField('redirect', [rex_config::get('warehouse', 'paypal_page_start')]);
}
    */

echo $yform->getForm();
