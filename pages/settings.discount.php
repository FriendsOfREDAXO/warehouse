<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

// Meldung ausgeben, dass Rabatte zukünftig in einem eigenen Addon entwickelt werden.

echo rex_view::info(rex_i18n::rawMsg('warehouse.discount_info'));
