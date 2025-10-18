<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Warehouse;

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$form = rex_config_form::factory('warehouse');

$field = $form->addSelectField('currency');
$field->setLabel(rex_i18n::msg('warehouse.settings.currency'));
$select = $field->getSelect();
$select->addOptions(
    PayPal::CURRENCY_CODES
);

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
$field->setNotice(rex_i18n::msg('warehouse.settings.cart_mode.notice'));

$field = $form->addSelectField('instant_checkout_enabled');
$field->setLabel(rex_i18n::msg('warehouse.settings.instant_checkout_enabled'));
$select = $field->getSelect();
$select->addOptions([
    '0' => 'Nein',
    '1' => 'Ja'
]);
$field->setNotice(rex_i18n::msg('warehouse.settings.instant_checkout_enabled.notice'));

$field = $form->addSelectField('ycom_mode');
$field->setLabel(rex_i18n::msg('warehouse.settings.ycom_mode'));
$select = $field->getSelect();

if (rex_addon::get('ycom')->isAvailable()) {
    $select->addOptions(Warehouse::YCOM_MODES);
} else {
    $select->addOptions([
        'guest_only'=>rex_i18n::msg('warehouse.settings.ycom_mode.guest_only')
    ]);
    $select->setAttribute('readonly', 'readonly');
}

$field->setNotice(rex_i18n::msg('warehouse.settings.ycom_mode.notice'));

// Gewicht ausblenden / einblenden in Formularen und in Optionen berücksichtigen / nicht berücksichtigen

$field = $form->addCheckboxField('enable_features');
$field->setLabel(rex_i18n::msg('warehouse.settings.enable_features'));
$field->addOption(rex_i18n::msg('warehouse.settings.enable_features.bulk_prices'), "bulk_prices");
$field->addOption(rex_i18n::msg('warehouse.settings.enable_features.weight'), "weight");
$field->addOption(rex_i18n::msg('warehouse.settings.enable_features.variants'), "variants");
$field->addOption(rex_i18n::msg('warehouse.settings.enable_features.stock'), "stock");
$field->addOption(rex_i18n::msg('warehouse.settings.enable_features.sku'), "sku");

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

$field = $form->addInputField('text', 'container_class', null, ['class' => 'form-control']);
$field->setLabel(rex_i18n::msg('warehouse.settings.container_class'));
$field->setNotice(rex_i18n::msg('warehouse.settings.container_class.notice'));

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse.settings.payment'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

?>
<div class="row">
	<div class="col-12 col-md-8">
		<?php echo $content; ?>
	</div>
	<div class="col-12 col-md-4">
		<?= rex_view::info(rex_i18n::msg('warehouse.settings.general.info')); ?>
		<?php

$content = '';
$content = '<p>' . rex_i18n::msg('warehouse.settings.tax_options.notice') . '</p>';
// Steuersätze als Tabelle ausgeben
$taxOptions = Article::getTaxOptions();
if (!empty($taxOptions)) {
    $table = '<table class="table table-striped">';
    $table .= '<thead><tr><th>' . rex_i18n::msg('warehouse.settings.tax_options') . '</th></tr></thead>';
    $table .= '<tbody>';
    foreach ($taxOptions as $tax) {
        $table .= '<tr><td>' . htmlspecialchars($tax) . '</td></tr>';
    }
    $table .= '</tbody></table>';
    $content .= $table;
} else {
    $content .= rex_i18n::msg('warehouse.settings.tax_options.empty');
}

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('warehouse.settings.tax_options'));
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');
?>
		<?= $content; ?>
	</div>
</div>
