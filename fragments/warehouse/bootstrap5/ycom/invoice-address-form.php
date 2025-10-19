<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\CustomerAddress;
use rex_ycom_auth;
use rex_yform;
use rex_yform_manager_table;

// Get current logged-in YCom user
$current_ycom_user = rex_ycom_auth::getUser();
if (!$current_ycom_user) {
    echo '<div class="alert alert-warning">' . rex_i18n::msg('warehouse.ycom.not_logged_in') . '</div>';
    return;
}

$current_ycom_user_id = $current_ycom_user->getValue('id');

// Query existing invoice address for this user
$dataset = rex_yform_manager_table::get('rex_warehouse_customer_address')
    ->query()
    ->where(CustomerAddress::YCOM_USER_ID, $current_ycom_user_id)
    ->where(CustomerAddress::TYPE, 'billing')
    ->findOne();

$yform = new rex_yform();
$yform->setObjectparams('form_name', 'table-rex_warehouse_customer_address');
$yform->setObjectparams('form_action', rex_getUrl('REX_ARTICLE_ID'));
$yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
$yform->setObjectparams('form_showformafterupdate', 1);
$yform->setObjectparams('real_field_names', true);

// Pre-fill form with existing data if available
if ($dataset) {
    $yform->setFormData($dataset->getData());
}

// Hidden fields
$yform->setValueField('hidden', [CustomerAddress::YCOM_USER_ID, 'translate:warehouse_customer_address.ycom_user_id', $current_ycom_user_id]);
$yform->setValueField('hidden', [CustomerAddress::TYPE, 'translate:warehouse_customer_address.type', 'billing']);

// Form fields
$yform->setValueField('text', [CustomerAddress::COMPANY, 'translate:warehouse_customer_address.company', '', '0']);

$yform->setValueField('text', [CustomerAddress::NAME, 'translate:warehouse_customer_address.name', '', '0']);
$yform->setValidateField('empty', [CustomerAddress::NAME, 'translate:warehouse.validation.required']);

$yform->setValueField('text', [CustomerAddress::STREET, 'translate:warehouse_customer_address.street', '', '0']);
$yform->setValidateField('empty', [CustomerAddress::STREET, 'translate:warehouse.validation.required']);

$yform->setValueField('text', [CustomerAddress::ZIP, 'translate:warehouse_customer_address.zip', '', '0']);
$yform->setValidateField('empty', [CustomerAddress::ZIP, 'translate:warehouse.validation.required']);

$yform->setValueField('text', [CustomerAddress::CITY, 'translate:warehouse_customer_address.city', '', '0']);
$yform->setValidateField('empty', [CustomerAddress::CITY, 'translate:warehouse.validation.required']);

$yform->setValueField('text', [CustomerAddress::COUNTRY, 'translate:warehouse_customer_address.country', '', '0']);

// Validation for unique address
$yform->setValidateField('unique', [CustomerAddress::YCOM_USER_ID . ',' . CustomerAddress::TYPE, 'translate:warehouse_customer_address.validate.unique.ycom_user_id_and_type', '', '0']);

// Submit button
$yform->setValueField('submit', ['submit', 'translate:warehouse.save', '', 'no_db']);

// Action: save to database
if ($dataset) {
    $yform->setActionField('db', ['rex_warehouse_customer_address', 'id=' . $dataset->getId()]);
} else {
    $yform->setActionField('db', ['rex_warehouse_customer_address']);
}

// Show success message if form was submitted successfully
$form_output = $yform->getForm();

if ($yform->objparams['form_show_submit']) {
    echo $form_output;
} else {
    // Form was submitted successfully
    echo '<div class="alert alert-success">' . rex_i18n::msg('warehouse.address.saved_successfully') . '</div>';
    echo $form_output;
}
