<?php

$res = rex_sql::factory()->getArray('SELECT name FROM '.rex::getTable('yform_email_template'));
$etpl_options = array_column($res, 'name');

$form = rex_config_form::factory('warehouse');

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

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;

