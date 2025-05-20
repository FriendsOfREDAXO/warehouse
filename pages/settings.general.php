<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\PayPal;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$field = $form->addSelectField('currency');
$field->setLabel('Währung');
$select = $field->getSelect();
$select->addOptions(
    PayPal::CURRENCY_CODES
);

$field = $form->addTextField('currency_symbol');
$field->setLabel('Währungssymbol (z.B. €)');
$field->setNotice('<code>rex_config::get("warehouse","currency_symbol")</code>');

$field = $form->addTextField('tax_options');
$field->setLabel('Steuersätze');
$field->setNotice('Mögliche Steuersätze zur Auswahl in Artikeln und Varianten. Standard: <code>19,7,0</code> für 19%, 7% und 0% Steuersatz');
$field->setAttribute('placeholder', '19,7,0');
$field->setAttribute('pattern', '^[0-9\.,]+$');

$field = $form->addSelectField('shipping_allowed');
$field->setLabel('Erlaubte Länder für die Lieferung');
$select = $field->getSelect();
$select->addOptions(
    PayPal::COUNTRY_CODES
);
$field->setAttribute('multiple', 'multiple');
$field->setAttribute('size', '20');

// TODO: Warenkorb-Aktion ausblenden / einblenden in Formularen und in Optionen berücksichtigen / nicht berücksichtigen

$field = $form->addSelectField('cart_mode');
$field->setLabel('Nach Warenkorb-Aktion');
$select = $field->getSelect();
$select->addOptions([
    'cart'=>'in den Warenkorb wechseln',
    'page'=>'auf der Artikelseite bleiben'
]);
$select->setAttribute('disabled', 'disabled');

$field->setNotice('Es kann entweder die Warenkorbseite aufgerufen werden oder die vorherige Artikelseite. Wenn die Artikelseite aufgerufen wird, so wird showcart=1 als Get-Parameter angehängt.');

// TODO: Gewicht ausblenden / einblenden in Formularen und in Optionen berücksichtigen / nicht berücksichtigen

$field = $form->addCheckboxField('enable_features');
$field->setLabel('Zusätzliche Optionen für Artikel und Varianten');
$field->addOption('Staffelpreise abfragen', "bulk_prices");
$field->addOption('Artikelgewicht abfragen', "weight");
$field->addOption('Varianten zulassen', "variants");

// $field->setAttribute('disabled', 'disabled');

$field = $form->addInputField('text', 'editor', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.editor'));
$field->setNotice(rex_i18n::msg('warehouse.settings.editor.notice'));


$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse_settings_general'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
