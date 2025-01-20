<?php

$form = rex_config_form::factory('warehouse');

// ==== Giropay

$form->addFieldset('Giropay Einstellungen');

$field = $form->addTextField('giropay_merchand_id');
$field->setLabel('Giropay Merchand Id');

$field = $form->addTextField('giropay_project_id');
$field->setLabel('Giropay Projekt Id');

$field = $form->addTextField('giropay_project_pw');
$field->setLabel('Giropay Projekt Passwort');

$field = $form->addLinkmapField('giropay_page_start');
$field->setLabel('Giropay Startseite');
$field->setNotice('<code>rex_config::get("warehouse","giropay_page_start")</code>');

$field = $form->addLinkmapField('giropay_page_notify');
$field->setLabel('Giropay Notify Seite');
$field->setNotice('<code>rex_config::get("warehouse","giropay_page_notify")</code> - Seite, die der Girocheckout Server aufruft. Wird nicht im Browser aufgerufen!');

$field = $form->addLinkmapField('giropay_page_redirect');
$field->setLabel('Giropay Redirect Seite');
$field->setNotice('<code>rex_config::get("warehouse","giropay_page_redirect")</code> - Seite, auf die nach Abschluss der Zahlung weitergeleitet wird.');

$field = $form->addLinkmapField('giropay_page_error');
$field->setLabel('Giropay Fehler');
$field->setNotice('<code>rex_config::get("warehouse","giropay_page_error")</code>');

$field = $form->addLinkmapField('giropay_page_success');
$field->setLabel('Giropay Zahlung erfolgreich');
$field->setNotice('<code>rex_config::get("warehouse","giropay_page_success")</code>');

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;

