<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Shipping;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$form->addFieldset('translate:warehouse.settings.shipping_costs');

// Mindestbestellwert
$field = $form->addTextField('minimum_order_value');
$field->setLabel('translate:warehouse.settings.minimum_order_value');
$field->setAttribute('placeholder', '0.00');
$field->setAttribute('min', '0.00');
$field->setAttribute('step', '0.01');

// Versandkosten
$field = $form->addTextField('shipping_fee');
$field->setLabel('translate:warehouse.settings.shipping_fee');
$field->setAttribute('placeholder', '0.00');
$field->setAttribute('min', '0.00');
$field->setAttribute('step', '0.01');

// Versandkostenfrei ab
$field = $form->addTextField('free_shipping_from');
$field->setLabel('translate:warehouse.settings.free_shipping_from');
$field->setAttribute('placeholder', '0.00');
$field->setAttribute('min', '0.00');
$field->setAttribute('step', '0.01');

$field = $form->addSelectField('shipping_calculation_mode');
$field->setLabel('translate:warehouse.settings.shipping_calculation_mode');
$select = $field->getSelect();
$select->addOptions(Shipping::CALCULATION_MODE_OPTIONS);

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
