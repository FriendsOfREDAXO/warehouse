<?php

namespace FriendsOfRedaxo\Warehouse;

use rex;
use rex_yform_manager_dataset;
use rex_yform;
use rex_addon;

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass('rex_warehouse_article', Article::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_article_variant', ArticleVariant::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_category', Category::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_order', Order::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_settings_domain', Domain::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_country', Country::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_shipping', Shipping::class);
}

rex_yform::addTemplatePath($this->getPath('ytemplates'));

if (rex::isFrontend()) {
    Frontend::init();
}
