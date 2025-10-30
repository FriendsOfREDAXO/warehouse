<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Warehouse;

if (rex::isBackend()) {
    echo '<h2>Warehouse Invoice Address Form</h2>';
    return;
}

if (!rex_addon::get('warehouse')->isAvailable() || !rex_addon::get('ycom')->isAvailable()) {
    echo rex_view::error(rex_i18n::msg('warehouse.addon.missing'));
    return;
}

echo Warehouse::parse('ycom/invoice-address-form.php');
