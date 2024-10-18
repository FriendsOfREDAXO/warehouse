<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$res = rex_sql::factory()->getArray('SELECT name FROM '.rex::getTable('yform_email_template'));
$etpl_options = array_column($res, 'name');

$form = rex_config_form::factory('warehouse');
$form->addFieldset('Warehouse - Einstellungen');

$field = $form->addTextField('store_name');
$field->setLabel('Name des Shops');
$field->setNotice('<code>rex_config::get("warehouse","store_name")</code> (z.B. <code>Martin Muster GmbH</code>) - wird z.B. bei PayPal Zahlung an PayPal übermittelt.');

$field = $form->addTextField('store_country_code');
$field->setLabel('Ländercode des Shops');
$field->setNotice('<code>rex_config::get("warehouse","store_country_code")</code> (z.B. <code>de-DE</code>) - wird z.B. bei PayPal Zahlung an PayPal übermittelt.');

$field = $form->addLinkmapField('cart_page');
$field->setLabel('Warenkorbseite');
$field->setNotice('<code>rex_config::get("warehouse","cart_page")</code>');

$field = $form->addLinkmapField('address_page');
$field->setLabel('Adresseingabe Seite');
$field->setNotice('<code>rex_config::get("warehouse","address_page")</code>');

$field = $form->addLinkmapField('order_page');
$field->setLabel('Bestellung Seite');
$field->setNotice('<code>rex_config::get("warehouse","order_page")</code>');

$field = $form->addLinkmapField('thankyou_page');
$field->setLabel('Danke Seite');
$field->setNotice('<code>rex_config::get("warehouse","thankyou_page")</code>');

$field = $form->addLinkmapField('payment_error');
$field->setLabel('Fehler bei der Bezahlung');
$field->setNotice('<code>rex_config::get("warehouse","payment_error")</code>');

$field = $form->addLinkmapField('shippinginfo_page');
$field->setLabel('Versandkosten Info');
$field->setNotice('<code>rex_config::get("warehouse","shippinginfo_page")</code>');

if (rex_addon::get('yrewrite')->isAvailable()) {    
    $domains = rex_yrewrite::getDomains();
    if (count($domains) > 1) {
        $form->addFieldset('Domain Einstellungen');
        $field = $form->addRawField('<p><strong>Hinweis:</strong> Die Angaben pro Domain sind optional. Wenn nichts eingetragen ist, wird der Wert aus den allgemeinen Einstellungen verwendet. Die Werte können z.B. mit <code>warehouse::get_config("cart_page")</code> ausgelesen werden. Die Funktion liefert für die aktive Domain den passenden Wert. Wenn kein Wert definiert wurde, wird der Standardwert geliefert.</p>');
        foreach ($domains as $domain) {
            if ($domain->getName() == 'default') {
                continue;
            }
            $field = $form->addRawField('<h3>Einstellungen für '.$domain->getName().'</h3>');
            $field = $form->addLinkmapField('cart_page_'.$domain->getId());
            $field->setLabel('Warenkorbseite');
            $field->setNotice('<code>rex_config::get("warehouse","cart_page_'.$domain->getId().'")</code>');
            
            $field = $form->addLinkmapField('address_page_'.$domain->getId());
            $field->setLabel('Adresseingabe Seite');
            $field->setNotice('<code>rex_config::get("warehouse","address_page_'.$domain->getId().'")</code>');
            
            $field = $form->addLinkmapField('order_page_'.$domain->getId());
            $field->setLabel('Bestellung Seite');
            $field->setNotice('<code>rex_config::get("warehouse","order_page_'.$domain->getId().'")</code>');
            
            $field = $form->addLinkmapField('thankyou_page_'.$domain->getId());
            $field->setLabel('Danke Seite');
            $field->setNotice('<code>rex_config::get("warehouse","thankyou_page_'.$domain->getId().'")</code>');
            
            $field = $form->addLinkmapField('payment_error_'.$domain->getId());
            $field->setLabel('Fehler bei der Bezahlung');
            $field->setNotice('<code>rex_config::get("warehouse","payment_error_'.$domain->getId().'")</code>');
            
            $field = $form->addLinkmapField('shippinginfo_page_'.$domain->getId());
            $field->setLabel('Versandkosten Info');
            $field->setNotice('<code>rex_config::get("warehouse","shippinginfo_page_'.$domain->getId().'")</code>');

            $field = $form->addLinkmapField('wallee_page_start_'.$domain->getId());
            $field->setLabel('Wallee Startseite');
            $field->setNotice('<code>rex_config::get("warehouse","wallee_page_start_'.$domain->getId().'")</code>');


        }
        $form->addFieldset('Weitere Einstellungen');
    }
}

$field = $form->addLinkmapField('my_orders_page');
$field->setLabel('Meine Bestellungen');
$field->setNotice('<code>rex_config::get("warehouse","my_orders_page")</code>');

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

// ==== E-Mail

$form->addFieldset('Bestätigungen / E-Mail Versand');

$field = $form->addSelectField('email_template_customer');
$field->setLabel('E-Mail Template Kunde');
$select = $field->getSelect();
$select->addOptions($etpl_options,true);
$field->setNotice('<code>rex_config::get("warehouse","email_template_customer")</code>');

$field = $form->addSelectField('email_template_seller');
$field->setLabel('E-Mail Template Bestellung');
$select = $field->getSelect();
$select->addOptions($etpl_options,true);
$field->setNotice('<code>rex_config::get("warehouse","email_template_seller")</code>');

$field = $form->addTextField('order_email');
$field->setLabel('Bestellungen E-Mail Empfänger');
$field->setNotice('Mehrere E-Mail Empfänger können mit Komma getrennt werden.<br><code>rex_config::get("warehouse","order_email")</code>');



if (rex_addon::get('yrewrite')->isAvailable()) {    
    $domains = rex_yrewrite::getDomains();
    if (count($domains) > 1) {
        $form->addFieldset('Domain Einstellungen für den E-Mail Versand');
        $field = $form->addRawField('<p><strong>Hinweis:</strong> Die Angaben pro Domain sind optional. Wenn nichts eingetragen ist, wird der Wert aus den allgemeinen Einstellungen verwendet. Die Werte können z.B. mit <code>warehouse::get_config("cart_page")</code> ausgelesen werden. Die Funktion liefert für die aktive Domain den passenden Wert. Wenn kein Wert definiert wurde, wird der Standardwert geliefert.</p>');
        foreach ($domains as $domain) {
            if ($domain->getName() == 'default') {
                continue;
            }
            
            $field = $form->addRawField('<h3>Einstellungen für '.$domain->getName().'</h3>');

            $field = $form->addSelectField('email_template_customer_'.$domain->getId());
            $field->setLabel('E-Mail Template Kunde');
            $select = $field->getSelect();
            $select->addOptions($etpl_options,true);
            $field->setNotice('<code>warehouse::get_config("email_template_customer")</code>');
            
            $field = $form->addSelectField('email_template_seller_'.$domain->getId());
            $field->setLabel('E-Mail Template Bestellung');
            $select = $field->getSelect();
            $select->addOptions($etpl_options,true);
            $field->setNotice('<code>warehouse::get_config("email_template_seller")</code>');
            
            $field = $form->addTextField('order_email_'.$domain->getId());
            $field->setLabel('Bestellungen E-Mail Empfänger');
            $field->setNotice('Mehrere E-Mail Empfänger können mit Komma getrennt werden.<br><code>warehouse::get_config("order_email")</code>');
            
            
        }
    }
}






// ==== Rabatt

$form->addFieldset('Rabatt');

$field = $form->addTextField('global_discount_text');
$field->setLabel('Text Allgemeiner Rabatt (Warenkorbrabatt)');
$field->setNotice('Der Text wird im Warenkorb und in der Bestätigung angezeigt.<br><code>rex_config::get("warehouse","global_discount_text")</code>');

$field = $form->addTextField('global_discount');
$field->setLabel('Warenkorbrabatt in %');
$field->setNotice('Der Wert wird zur Berechnung des Warenkorbrabatts verwendet. 0 eingeben für keinen Rabatt.<br><code>rex_config::get("warehouse","global_discount")</code>');


// ==== Paypal

$form->addFieldset('Paypal Einstellungen');

$field = $form->addTextField('paypal_client_id');
$field->setLabel('Paypal Client Id');

$field = $form->addTextField('paypal_secret');
$field->setLabel('Paypal Secret');

$field = $form->addCheckboxField('sandboxmode');
$field->setLabel('Paypal Sandbox ein');
$field->addOption('Paypal Sandbox ein', "1");


$field = $form->addTextField('paypal_sandbox_client_id');
$field->setLabel('Paypal Sandbox Client Id');

$field = $form->addTextField('paypal_sandbox_secret');
$field->setLabel('Paypal Sandbox Secret');

$field = $form->addTextField('paypal_getparams');
$field->setLabel('Paypal Zusätzliche Get-Parameter für Paypal');
$field->setNotice('z.B. um Maintenance bei der Entwicklung zu verwenden. Als JSON in der Form <code>{"key1":"value1","key2":"value2"}</code> angeben.');

$field = $form->addLinkmapField('paypal_page_start');
$field->setLabel('Paypal Startseite');
$field->setNotice('<code>rex_config::get("warehouse","paypal_page_start")</code>');

$field = $form->addLinkmapField('paypal_page_success');
$field->setLabel('Paypal Zahlung erfolgt');
$field->setNotice('<code>rex_config::get("warehouse","paypal_page_success")</code>');

$field = $form->addLinkmapField('paypal_page_error');
$field->setLabel('Paypal Fehler');
$field->setNotice('<code>rex_config::get("warehouse","paypal_page_error")</code>');

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


// ==== Frachtrechnung

$form->addFieldset('Frachtrechnung');

$field = $form->addTextField('shipping');
$field->setLabel('Versandkosten Standard');
$field->setNotice('Kann leer bleiben, wenn Sonderfrachtberechnung definiert ist.');

$field = $form->addSelectField('shipping_mode');
$field->setLabel('Frachtberechnung');
$select = $field->getSelect();
$select->addOptions([
    0 => 'Standard (Pauschal)',
    'pieces' => 'nach Stück',
    'weight' => 'nach Gewicht',
    'order_total' => 'Betrag (brutto)',
]);

$field = $form->addTextField('shipping_parameters');
$field->setLabel('Fracht Parameter');
$field->setNotice('Paramter für die Frachtberechnung. Als JSON in der Form <code>[[">",4,10.5],[">",2,7.9],[">",0,5.9]]</code> angeben. Jede Bedingung besteht aus drei Elementen. Als Kondition sind die Angaben <code>&gt;</code>, '
        . '<code>&lt;</code>, <code>&gt;=</code>, <code>&lt;=</code> oder <code>=</code> möglich. Der zweite Wert steht für die Anzahl, der dritte für den Frachtpreis. Die erste Bedingung die erfüllt ist, wird für die Frachtberechnung verwendet. Wenn keine Bedingung erfüllt ist, wird der Standardfrachtpreis berechnet.');

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
