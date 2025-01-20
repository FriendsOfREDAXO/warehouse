<?php

$form = rex_config_form::factory('warehouse');

$form->addFieldset('Warehouse - Seitenmapping');

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
    }
}

$field = $form->addLinkmapField('my_orders_page');
$field->setLabel('Meine Bestellungen');
$field->setNotice('<code>rex_config::get("warehouse","my_orders_page")</code>');

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;

