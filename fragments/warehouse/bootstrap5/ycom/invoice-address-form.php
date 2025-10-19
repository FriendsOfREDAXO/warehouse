<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\CustomerAddress;
use FriendsOfRedaxo\Warehouse\Warehouse;

// Get current logged-in YCom user
$current_ycom_user = rex_ycom_auth::getUser();
if (!$current_ycom_user) {
    echo '<div class="alert alert-warning">' . Warehouse::getLabel('ycom_not_logged_in') . '</div>';
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
$yform->setObjectparams('form_action', rex_getUrl(rex_article::getCurrentId()));
$yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
$yform->setObjectparams('form_showformafterupdate', 1);
$yform->setObjectparams('real_field_names', true);

// Pre-fill form with existing data if available
if ($dataset) {
    $yform->setFormData($dataset->getData());
}

// Hidden fields
$yform->setValueField('hidden', [CustomerAddress::YCOM_USER_ID, '', $current_ycom_user_id]);
$yform->setValueField('hidden', [CustomerAddress::TYPE, '', 'billing']);

// Form fields
$yform->setValueField('text', [CustomerAddress::COMPANY, Warehouse::getLabel('address_company'), '', '0']);

$yform->setValueField('text', [CustomerAddress::NAME, Warehouse::getLabel('address_name'), '', '0']);
$yform->setValidateField('empty', [CustomerAddress::NAME, Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', [CustomerAddress::STREET, Warehouse::getLabel('address_street'), '', '0']);
$yform->setValidateField('empty', [CustomerAddress::STREET, Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', [CustomerAddress::ZIP, Warehouse::getLabel('address_zip'), '', '0']);
$yform->setValidateField('empty', [CustomerAddress::ZIP, Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', [CustomerAddress::CITY, Warehouse::getLabel('address_city'), '', '0']);
$yform->setValidateField('empty', [CustomerAddress::CITY, Warehouse::getLabel('validation_required_fields')]);

$yform->setValueField('text', [CustomerAddress::COUNTRY, Warehouse::getLabel('address_country'), '', '0']);

// Validation for unique address (only for new records)
if (!$dataset) {
    $yform->setValidateField('unique', [CustomerAddress::YCOM_USER_ID . ',' . CustomerAddress::TYPE, 'translate:warehouse_customer_address.validate.unique.ycom_user_id_and_type', '', '0']);
}

// Submit button
$yform->setValueField('submit', ['submit', Warehouse::getLabel('address_save'), '', 'no_db']);

// Action: save to database
if ($dataset) {
    $yform->setActionField('db', ['rex_warehouse_customer_address', 'id=' . $dataset->getId()]);
} else {
    $yform->setActionField('db', ['rex_warehouse_customer_address']);
}

// Show success message only when form is successfully saved
$yform->setActionField('showtext', [Warehouse::getLabel('address_saved_successfully'), '<div class="alert alert-success">', '</div>', '0']);

echo $yform->getForm();
