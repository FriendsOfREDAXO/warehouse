<?php

$form = rex_config_form::factory('warehouse');

// ==== Wallee

$form->addFieldset('Wallee Einstellungen');

$field = $form->addCheckboxField('wallee_sandboxmode');
$field->setLabel('Wallee Sandbox ein');
$field->addOption('Wallee Sandbox ein', "1");
$field->setNotice('<code>rex_config::get("warehouse","wallee_sandboxmode")</code> - Wallee Sandbox Mode (0 oder 1)');

$field = $form->addTextField('wallee_live_space_id');
$field->setLabel('Wallee Live SpaceId');
$field->setNotice('<code>rex_config::get("warehouse","wallee_live_space_id")</code> - Wallee Space Id');

$field = $form->addTextField('wallee_live_user_id');
$field->setLabel('Wallee Live UserId');
$field->setNotice('<code>rex_config::get("warehouse","wallee_live_user_id")</code> - Wallee User Id');

$field = $form->addTextField('wallee_live_secret');
$field->setLabel('Wallee Live Secret');
$field->setNotice('<code>rex_config::get("warehouse","wallee_live_secret")</code> - Wallee Secret');

$field = $form->addTextField('wallee_sandbox_space_id');
$field->setLabel('Wallee Sandbox SpaceId');
$field->setNotice('<code>rex_config::get("warehouse","wallee_sandbox_space_id")</code> - Wallee Space Id');

$field = $form->addTextField('wallee_sandbox_user_id');
$field->setLabel('Wallee Sandbox UserId');
$field->setNotice('<code>rex_config::get("warehouse","wallee_sandbox_user_id")</code> - Wallee User Id');

$field = $form->addTextField('wallee_sandbox_secret');
$field->setLabel('Wallee Sandbox Secret');
$field->setNotice('<code>rex_config::get("warehouse","wallee_sandbox_secret")</code> - Wallee Secret');

$field = $form->addLinkmapField('wallee_page_start');
$field->setLabel('Wallee Startseite');
$field->setNotice('<code>rex_config::get("warehouse","wallee_page_start")</code>');

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;

