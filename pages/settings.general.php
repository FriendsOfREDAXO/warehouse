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
$field->setLabel(rex_i18n::msg('warehouse.settings.currency'));
$select = $field->getSelect();
$select->addOptions(
    PayPal::CURRENCY_CODES
);

$field = $form->addTextField('currency_symbol');
$field->setLabel(rex_i18n::msg('warehouse.settings.currency_symbol'));
$field->setNotice(rex_i18n::msg('warehouse.settings.currency_symbol.notice'));

$field = $form->addTextField('tax_options');
$field->setLabel(rex_i18n::msg('warehouse.settings.tax_options'));
$field->setNotice(rex_i18n::msg('warehouse.settings.tax_options.notice'));
$field->setAttribute('placeholder', '19,7,0');
$field->setAttribute('pattern', '^[0-9\.,]+$');

$field = $form->addSelectField('shipping_allowed');
$field->setLabel(rex_i18n::msg('warehouse.settings.shipping_allowed'));
$select = $field->getSelect();
$select->addOptions(
    PayPal::COUNTRY_CODES
);
$field->setAttribute('multiple', 'multiple');
$field->setAttribute('size', '20');

// TODO: Warenkorb-Aktion ausblenden / einblenden in Formularen und in Optionen berücksichtigen / nicht berücksichtigen

$field = $form->addSelectField('cart_mode');
$field->setLabel(rex_i18n::msg('warehouse.settings.cart_mode'));
$select = $field->getSelect();
$select->addOptions([
    'cart'=>rex_i18n::msg('warehouse.settings.cart_mode.cart'),
    'page'=>rex_i18n::msg('warehouse.settings.cart_mode.page')
]);
$select->setAttribute('disabled', 'disabled');

$field->setNotice(rex_i18n::msg('warehouse.settings.cart_mode.notice'));

// Gewicht ausblenden / einblenden in Formularen und in Optionen berücksichtigen / nicht berücksichtigen

$field = $form->addCheckboxField('enable_features');
$field->setLabel(rex_i18n::msg('warehouse.settings.enable_features'));
$field->addOption(rex_i18n::msg('warehouse.settings.enable_features.bulk_prices'), "bulk_prices");
$field->addOption(rex_i18n::msg('warehouse.settings.enable_features.weight'), "weight");
$field->addOption(rex_i18n::msg('warehouse.settings.enable_features.variants'), "variants");

$field = $form->addInputField('text', 'editor', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.editor'));
$field->setNotice(rex_i18n::msg('warehouse.settings.editor.notice'));

$field = $form->addMediaField('fallback_category_image');
$field->setLabel(rex_i18n::msg('warehouse.settings.fallback_category_image'));
$field->setNotice(rex_i18n::msg('warehouse.settings.fallback_category_image.notice'));

$field = $form->addMediaField('fallback_article_image');
$field->setLabel(rex_i18n::msg('warehouse.settings.fallback_article_image'));
$field->setNotice(rex_i18n::msg('warehouse.settings.fallback_article_image.notice'));

$field = $form->addInputField('text', 'framework', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.framework'));
$field->setNotice(rex_i18n::msg('warehouse.settings.framework.notice'));

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse_settings_general'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
