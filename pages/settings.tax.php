<?php

$form = rex_config_form::factory('warehouse');

$form->addFieldset('Mehrwertsteuer Einstellungen');

$field = $form->addTextField('tax_value');
$field->setLabel('Steuersatz [%]');
$field->setNotice('<code>rex_config::get("warehouse","tax_value")</code>');

$field = $form->addTextField('tax_value_1');
$field->setLabel('Steuersatz 1 [%]');
$field->setNotice('<code>rex_config::get("warehouse","tax_value_1")</code>');

$field = $form->addTextField('tax_value_2');
$field->setLabel('Steuersatz 2 [%]');
$field->setNotice('<code>rex_config::get("warehouse","tax_value_2")</code>');

$field = $form->addTextField('tax_value_3');
$field->setLabel('Steuersatz 3 [%]');
$field->setNotice('<code>rex_config::get("warehouse","tax_value_3")</code>');

$field = $form->addTextField('tax_value_4');
$field->setLabel('Steuersatz 4 [%]');
$field->setNotice('<code>rex_config::get("warehouse","tax_value_4")</code>');

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
