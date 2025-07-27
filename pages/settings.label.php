<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$formFields = [
    'cart' => [],
    'checkout' => [],
    'shipping' => [],
    'payment' => [],
    'paymentoptions' => [],
    'other' => []
];
$allFields = [
    // cart fields (text)
    ['label_cart', rex_i18n::msg('warehouse.settings.label_cart'), 'text'],
    ['label_cart_empty', rex_i18n::msg('warehouse.settings.label_cart_empty'), 'text'],
    ['label_cart_subtotal', rex_i18n::msg('warehouse.settings.label_cart_subtotal'), 'text'],
    ['label_cart_total', rex_i18n::msg('warehouse.settings.label_cart_total'), 'text'],
    ['label_cart_total_weight', rex_i18n::msg('warehouse.settings.label_cart_total_weight'), 'text'],
    ['label_remove_from_cart', rex_i18n::msg('warehouse.settings.label_remove_from_cart'), 'text'],
    ['label_back_to_cart', rex_i18n::msg('warehouse.settings.label_back_to_cart'), 'text'],
    ['label_cart_remove_item_confirm', rex_i18n::msg('warehouse.settings.label_cart_remove_item_confirm'), 'text'],
    ['label_cart_empty_confirm', rex_i18n::msg('warehouse.settings.label_cart_empty_confirm'), 'text'],
    ['label_next', rex_i18n::msg('warehouse.settings.label_next'), 'text'],
    ['label_article', rex_i18n::msg('warehouse.settings.label_article'), 'text'],
    ['label_price', rex_i18n::msg('warehouse.settings.label_price'), 'text'],
    ['label_quantity', rex_i18n::msg('warehouse.settings.label_quantity'), 'text'],
    ['label_total', rex_i18n::msg('warehouse.settings.label_total'), 'text'],
    // checkout fields (text)
    ['label_checkout', rex_i18n::msg('warehouse.settings.label_checkout'), 'text'],
    ['label_checkout_instant', rex_i18n::msg('warehouse.settings.label_checkout_instant'), 'text'],
    ['label_checkout_address', rex_i18n::msg('warehouse.settings.label_checkout_address'), 'text'],
    ['label_checkout_payment', rex_i18n::msg('warehouse.settings.label_checkout_payment'), 'text'],
    ['label_checkout_choose', rex_i18n::msg('warehouse.settings.label_checkout_choose'), 'text'],
    ['label_checkout_guest', rex_i18n::msg('warehouse.settings.label_checkout_guest'), 'text'],
    ['label_checkout_guest_text', rex_i18n::msg('warehouse.settings.label_checkout_guest_text'), 'text'],
    ['label_checkout_guest_continue', rex_i18n::msg('warehouse.settings.label_checkout_guest_continue'), 'text'],
    ['label_checkout_login', rex_i18n::msg('warehouse.settings.label_checkout_login'), 'text'],
    ['label_checkout_login_text', rex_i18n::msg('warehouse.settings.label_checkout_login_text'), 'text'],
    ['label_checkout_login_email', rex_i18n::msg('warehouse.settings.label_checkout_login_email'), 'text'],
    ['label_checkout_login_password', rex_i18n::msg('warehouse.settings.label_checkout_login_password'), 'text'],
    ['label_checkout_login_submit', rex_i18n::msg('warehouse.settings.label_checkout_login_submit'), 'text'],
    ['label_checkout_register_text', rex_i18n::msg('warehouse.settings.label_checkout_register_text'), 'text'],
    // payment options (text)
    ['label_payment_options', rex_i18n::msg('warehouse.settings.label_payment_options'), 'text'],
    // shipping (text)
    ['label_shipping_costs', rex_i18n::msg('warehouse.settings.label_shipping_costs'), 'text'],
    ['label_shipping_costs_free', rex_i18n::msg('warehouse.settings.label_shipping_costs_free'), 'text'],
    ['label_shipping_costs_weight', rex_i18n::msg('warehouse.settings.label_shipping_costs_weight'), 'text'],
];

$paymentOptions = Payment::getAllowedPaymentOptions();

if (count($paymentOptions) > 0) {
    foreach ($paymentOptions as $key => $option) {
        $allFields[] = ['label_paymentoptions_' . $key, rex_i18n::msg('warehouse.settings.label_paymentoptions', rex_i18n::msg($option)), 'text'];
        $allFields[] = ['label_paymentoptions_' . $key .'_notice', rex_i18n::msg('warehouse.settings.label_paymentoptions_notice', rex_i18n::msg($option)), 'text'];
        $allFields[] = ['label_paymentoptions_' . $key . '_image', rex_i18n::msg('warehouse.settings.label_paymentoptions_image', rex_i18n::msg($option)), 'media'];
    }
}
$used = [];
foreach ($allFields as [$name, $label, $type]) {
    if (in_array($name, $used)) {
        continue;
    }
    $used[] = $name;
    if ($type === 'media') {
        $field = $form->addMediaField($name, null, ['class' => 'form-control']);
        $field->setLabel($label);
    } else {
        $field = $form->addInputField('text', $name, null, ['class' => 'form-control']);
        $field->setLabel($label);
    }
    if (strpos($name, 'cart_') !== false) {
        $formFields['cart'][] = $field;
    } elseif (strpos($name, 'checkout_') !== false) {
        $formFields['checkout'][] = $field;
    } elseif (strpos($name, 'shipping_') !== false) {
        $formFields['shipping'][] = $field;
    } elseif (strpos($name, 'paymentoptions_') !== false) {
        $formFields['paymentoptions'][] = $field;
    } elseif (strpos($name, 'payment_') !== false) {
        $formFields['payment'][] = $field;
    } else {
        $formFields['other'][] = $field;
    }
}

// Formular generieren und parsen
$content = '';
foreach ([
    'cart' => 'Warenkorb',
    'checkout' => 'Checkout',
    'shipping' => 'Versand',
    'paymentoptions' => 'Verfügbare Bezahloptionen',
    'payment' => 'Zahlung',
    'other' => 'Sonstiges'
] as $group => $legend) {
    if (count($formFields[$group]) === 0) {
        continue;
    }
    $groupContent = '';
    $fields = $formFields[$group];
    $rows = [];
    $row = [];
    foreach ($fields as $i => $field) {
        $row[] = $field->get();
        if (count($row) == 3) {
            $rows[] = $row;
            $row = [];
        }
    }
    if (count($row) > 0) {
        $rows[] = $row;
    }
    foreach ($rows as $rowFields) {
        $groupContent .= '<div class="row">';
        foreach ($rowFields as $fieldHtml) {
            $groupContent .= '<div class="col-md-4 col-xs-12">' . $fieldHtml . '</div>';
        }
        $groupContent .= '</div>';
    }
    $content .= '<fieldset style="margin-bottom:2em"><legend>' . htmlspecialchars($legend) . '</legend>' . $groupContent . '</fieldset>';
}

// Buttons und Controls extrahieren
$formHtml = $form->get();

// Extrahiere das Formular-Tag und die Felder
$formTagStart = strpos($formHtml, '<form');
$formTagEnd = strpos($formHtml, '>', $formTagStart);
$formOpenTag = substr($formHtml, $formTagStart, $formTagEnd - $formTagStart + 1);
$formCloseTag = '</form>';

// Extrahiere alles innerhalb des <form>...</form>
$formInner = substr($formHtml, $formTagEnd + 1, strrpos($formHtml, '</form>') - ($formTagEnd + 1));

// Extrahiere die Panel-Footer (Buttons) und Hidden-Fields
$matches = [];
preg_match('/(<div class="rex-form-panel-footer".*?<\/div>)/s', $formInner, $matches);
$panelFooter = $matches[1] ?? '';

preg_match_all('/<input[^>]+type="hidden"[^>]*>/i', $formInner, $hiddenFields);
$hiddenFieldsHtml = implode('', $hiddenFields[0]);

// Setze das neue Formular zusammen
$content = $formOpenTag . $content . $panelFooter . $hiddenFieldsHtml . $formCloseTag;

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse_settings_general'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
