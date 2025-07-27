<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$formFields = [
    'cart' => [],
    'checkout' => [],
    'shipping' => [],
    'other' => []
];

$allFields = [
    ['label_cart', 'warehouse.settings.label_cart'],
    ['label_cart_empty', 'warehouse.settings.label_cart_empty'],
    ['label_cart_subtotal', 'warehouse.settings.label_cart_subtotal'],
    ['label_cart_total', 'warehouse.settings.label_cart_total'],
    ['label_cart_total_weight', 'warehouse.settings.label_cart_total_weight'],
    ['label_remove_from_cart', 'warehouse.settings.label_remove_from_cart'],
    ['label_back_to_cart', 'warehouse.settings.label_back_to_cart'],
    ['label_cart_remove_item_confirm', 'warehouse.settings.label_cart_remove_item_confirm'],
    ['label_cart_empty_confirm', 'warehouse.settings.label_cart_empty_confirm'],
    ['label_next', 'warehouse.settings.label_next'],
    ['label_article', 'warehouse.settings.label_article'],
    ['label_price', 'warehouse.settings.label_price'],
    ['label_quantity', 'warehouse.settings.label_quantity'],
    ['label_total', 'warehouse.settings.label_total'],
    ['label_checkout', 'warehouse.settings.label_checkout'],
    ['label_checkout_instant', 'warehouse.settings.label_checkout_instant'],
    ['label_checkout_address', 'warehouse.settings.label_checkout_address'],
    ['label_checkout_payment', 'warehouse.settings.label_checkout_payment'],
    ['label_payment_type', 'warehouse.settings.label_payment_type'],
    ['label_shipping_costs', 'warehouse.settings.label_shipping_costs'],
    ['label_shipping_costs_free', 'warehouse.settings.label_shipping_costs_free'],
    ['label_shipping_costs_weight', 'warehouse.settings.label_shipping_costs_weight'],
];

$used = [];
foreach ($allFields as [$name, $labelKey]) {
    if (in_array($name, $used)) {
        continue;
    }
    $used[] = $name;
    $field = $form->addInputField('text', $name, null, ['class' => 'form-control']);
    $field->setLabel(rex_i18n::msg($labelKey));
    if (strpos($name, 'cart') !== false) {
        $formFields['cart'][] = $field;
    } elseif (strpos($name, 'checkout') !== false) {
        $formFields['checkout'][] = $field;
    } elseif (strpos($name, 'shipping') !== false) {
        $formFields['shipping'][] = $field;
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
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML('<?xml encoding="utf-8" ?>' . $formHtml);
libxml_clear_errors();
$body = $dom->getElementsByTagName('body')->item(0);
$otherHtml = '';
foreach ($body->childNodes as $node) {
    if ($node->nodeType === XML_ELEMENT_NODE && $node->nodeName === 'div') {
        if ($node instanceof DOMElement && $node->getAttribute('class') === 'form-actions') {
            $otherHtml .= $dom->saveHTML($node);
        }
    }
    if ($node->nodeType === XML_ELEMENT_NODE && $node->nodeName === 'button') {
        $otherHtml .= $dom->saveHTML($node);
    }
}

$content .= $otherHtml;

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse_settings_general'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;
