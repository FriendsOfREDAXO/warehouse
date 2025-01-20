<?php

$form = rex_config_form::factory('warehouse');

$form->addFieldset('Warehouse - Einstellungen');

$field = $form->addTextField('store_name');
$field->setLabel('Name des Shops');
$field->setNotice('<code>rex_config::get("warehouse","store_name")</code> (z.B. <code>Martin Muster GmbH</code>) - wird z.B. bei PayPal Zahlung an PayPal übermittelt.');

$field = $form->addTextField('store_country_code');
$field->setLabel('Ländercode des Shops');
$field->setNotice('<code>rex_config::get("warehouse","store_country_code")</code> (z.B. <code>de-DE</code>) - wird z.B. bei PayPal Zahlung an PayPal übermittelt.');

$field = $form->addTextField('currency');
$field->setLabel('Währung (z.B. EUR)');
$field->setNotice('<code>rex_config::get("warehouse","currency")</code>');
// $field->setNotice('Es können mehrere Adressen angegeben werden. Adressen bitte mit Komma trennen.');

$field = $form->addTextField('currency_symbol');
$field->setLabel('Währungssymbol (z.B. €)');
$field->setNotice('<code>rex_config::get("warehouse","currency_symbol")</code>');

$field = $form->addTextField('country_codes');
$field->setLabel('Mögliche Länder für die Lieferung');
$field->setNotice('Als JSON in der Form <code>{"Deutschland":"DE","Österreich":"AT","Schweiz":"CH"}</code> angeben.<br><code>rex_config::get("warehouse","country_codes")</code>');

$field = $form->addSelectField('cart_mode');
$field->setLabel('Warenkorb Modus');
$select = $field->getSelect();
$select->addOptions([
    'cart'=>'Warenkorb',
    'page'=>'Artikelseite'
]);
$field->setNotice('Es kann entweder die Warenkorbseite aufgerufen werden oder die vorherige Artikelseite. Wenn die Artikelseite aufgerufen wird, so wird showcart=1 als Get-Parameter angehängt.<br><code>rex_config::get("warehouse","cart_mode")</code>');

$field = $form->addCheckboxField('check_weight');
$field->setLabel('Artikelgewicht prüfen');
$field->addOption('Artikelgewicht prüfen', "1");
$field->setNotice('Wenn die Checkbox angewählt ist, wird bei der Artikeleingabe im Backend geprüft, ob auch ein Gewicht angegeben wurde. Es ist dann nicht möglich, Artikel ohne Gewicht zu erfassen. Für die Gewichtsprüfung muss zusätzlich in der Artikeltabelle in yform die Customfunction warehouse::check_input_weight zur Validierung verwendet werden.<br><code>rex_config::get("warehouse","check_weight")</code>');

// ==== Alterscheck

$form->addFieldset('Alterscheck');

$field = $form->addCheckboxField('agecheck');
$field->setLabel('Alterscheck aktivieren');
$field->addOption('Alterscheck aktivieren', "1");
$field->setNotice('Wenn der Alterscheck aktiviert ist, kann eine Erstbestellung nur per giropay Alterscheck ausgeführt werden. Wenn der Besucher über die Community eingeloggt ist, wird der Alterscheck in der Community gespeichert. Wenn der Alterscheck in der Community gespeichert ist und der Benutzer eingeloggt ist, kann er auch mit anderen Zahlungsweisen bezahlen.');

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;

