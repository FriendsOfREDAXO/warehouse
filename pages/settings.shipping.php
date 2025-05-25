<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Shipping;
use FriendsOfRedaxo\Warehouse\Warehouse;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$form->addFieldset($this->i18n('warehouse.settings.shipping_costs'));

// Mindestbestellwert
$field = $form->addTextField('minimum_order_value');
$field->setLabel($this->i18n('warehouse.settings.minimum_order_value'));
$field->setAttribute('placeholder', '0.00');
$field->setAttribute('min', '0.00');
$field->setAttribute('step', '0.01');
$field->setAttribute('type', 'number');

// Versandkosten
$field = $form->addTextField('shipping_fee');
$field->setLabel($this->i18n('warehouse.settings.shipping_fee'));
$field->setAttribute('placeholder', '0.00');
$field->setAttribute('min', '0.00');
$field->setAttribute('step', '0.01');
$field->setAttribute('type', 'number');

// Versandkostenfrei ab
$field = $form->addTextField('free_shipping_from');
$field->setLabel($this->i18n('warehouse.settings.free_shipping_from'));
$field->setAttribute('placeholder', '0.00');
$field->setAttribute('min', '0.00');
$field->setAttribute('step', '0.01');
$field->setAttribute('type', 'number');

$field = $form->addSelectField('shipping_calculation_mode');
$field->setLabel($this->i18n('warehouse.settings.shipping_calculation_mode'));
$select = $field->getSelect();
foreach (Shipping::CALCULATION_MODE_OPTIONS as $key => $value) {
    $label = $value;
    if (strpos($label, 'translate:') === 0) {
        $label = substr($label, strlen('translate:'));
    }
    $select->addOption($this->i18n($label), $key);
}

// Versandkosten und Versandbedingungen Text
$field = $form->addTextareaField('shipping_conditions_text');
$field->setLabel($this->i18n('warehouse.settings.shipping_conditions_text'));
$field->setNotice($this->i18n('warehouse.settings.shipping_conditions_text.notice'));
$field->setAttribute('class', 'form-control ' . Warehouse::getConfig('editor'));

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse.settings'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');
?>
<div class="row">
    <div class="col-12 col-md-8">
        <?php echo $content; ?>
    </div>
    <div class="col-12 col-md-4">
        <?= rex_view::info('Die Länderauswahl befindet sich derzeit hartkodiert in der PayPal-Klasse und richtet sich daran aus. Wenn du eine Länderverwaltung benötigst, bspw. für OSS (One-Stop-Shop-Verfahren) oder Versandzonen, dann beteilige dich auf GitHub unter <a href="https://github.com/friendsofredaxo/warehouse/">https://github.com/friendsofredaxo/warehouse/</a>.'); ?>
        <?= rex_view::info('Tipp: Für die Berechnung des Versands ins Ausland gibt es auch den Extension Point <code>WAREHOUSE_CART_SHIPPING_COST</code>.'); ?>
    </div>
</div>
