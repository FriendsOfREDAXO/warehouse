<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$field = $form->addInputField('text', 'label_cart', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_cart'));

$field = $form->addInputField('text', 'label_cart_empty', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_cart_empty'));

$field = $form->addInputField('text', 'label_shipping_costs', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_shipping_costs'));

$field = $form->addInputField('text', 'label_shipping_costs_free', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_shipping_costs_free'));

$field = $form->addInputField('text', 'label_shipping_costs_weight', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_shipping_costs_weight'));

$field = $form->addInputField('text', 'label_checkout', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_checkout'));

$field = $form->addInputField('text', 'label_checkout_instant', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_checkout_instant'));

$field = $form->addInputField('text', 'label_checkout_address', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_checkout_address'));

$field = $form->addInputField('text', 'label_checkout_payment', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_checkout_payment'));

$field = $form->addInputField('text', 'label_cart_subtotal', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_cart_subtotal'));

$field = $form->addInputField('text', 'label_cart_total', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_cart_total'));

$field = $form->addInputField('text', 'label_cart_total_weight', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.label_cart_total_weight'));

$field = $form->addInputField('text', 'label_next', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.labeL_next'));


$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse_settings_general'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
