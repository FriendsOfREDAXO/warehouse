<?php

$form = rex_config_form::factory('warehouse');

// ==== Rabatt

$form->addFieldset('Rabatt');

$field = $form->addTextField('global_discount_text');
$field->setLabel('Text Allgemeiner Rabatt (Warenkorbrabatt)');
$field->setNotice('Der Text wird im Warenkorb und in der Bestätigung angezeigt.<br><code>rex_config::get("warehouse","global_discount_text")</code>');

$field = $form->addTextField('global_discount');
$field->setLabel('Warenkorbrabatt in %');
$field->setNotice('Der Wert wird zur Berechnung des Warenkorbrabatts verwendet. 0 eingeben für keinen Rabatt.<br><code>rex_config::get("warehouse","global_discount")</code>');


$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;

