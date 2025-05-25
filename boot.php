<?php

namespace FriendsOfRedaxo\Warehouse;

use FriendsOfRedaxo\Warehouse\QuickNavigation\QuickNavigationButton;
use rex;
use rex_login;
use rex_extension;
use rex_yrewrite;
use rex_article;
use rex_yform_manager_dataset;
use rex_yform;
use rex_addon;
use rex_request;
use rex_be_controller;
use rex_response;
use rex_view;
use Url\Url;

/** @var rex_addon $this */

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


// Nur, wenn auf Backend-Seiten des Warehouse-Addons
if (rex::isBackend() && rex_be_controller::getCurrentPagePart(1) == 'warehouse') {
    // CSS im Backend laden
    rex_view::addCssFile($this->getAssetsUrl('css/backend.css'));
}


if (rex::isFrontend()) {
    rex_login::startSession();

    rex_extension::register('PACKAGES_INCLUDED', function () {

        if (rex_addon::get('url') !== null) {
            $manager = Url::resolveCurrent();
            if ($manager) {
                $profile = $manager->getProfile();
                $seo = $manager->getSeo();

                $data_id = (int) $manager->getDatasetId();
                if ($profile->getTableName() == rex::getTable('warehouse_article')) {
                    $warehouse_prop['sitemode'] = 'article';
                } elseif ($profile->getTableName() == rex::getTable('warehouse_category')) {
                    $warehouse_prop['sitemode'] = 'category';
                    $warehouse_prop['seo_title'] = $seo['title'];
                    $warehouse_prop['path'] = Warehouse::getCategoryPath($data_id);
                }
                $curl = rtrim(rex_yrewrite::getFullPath(), '/') . $_SERVER['REQUEST_URI'];
                rex_set_session('current_page', $curl);
            }

            if (rex_article::getCurrentId() == Warehouse::getConfig('thankyou_page')) {
                if (rex_get('paymentId')) {
                    PayPal::ExecutePayment();
                    Warehouse::emptyCart();
                }
            }
        }
    });
}

if (rex::isBackend()) {
    rex_extension::register('YFORM_DATA_LIST', Article::epYformDataList(...));
    rex_extension::register('YFORM_DATA_LIST', Category::epYformDataList(...));
    rex_extension::register('YFORM_DATA_LIST', Order::epYformDataList(...));
    rex_extension::register('YFORM_DATA_LIST_ACTION_BUTTONS', Order::epYformDataListActionButtons(...));
}

/* Javascript-Assets */
if (rex::isBackend() && rex::getUser()) {
    rex_view::addJsFile($this->getAssetsUrl('js/backend.js'));
}

/* quick_navigation Suche */
if (rex::isBackend() && rex_addon::get('quick_navigation')->isAvailable()) {
    \FriendsOfRedaxo\QuickNavigation\Button\ButtonRegistry::registerButton(new QuickNavigationButton(), 5);
}

if(rex::isFrontend()) {
    $domain  = Domain::getCurrent();
    $this->setProperty('domain', $domain);

    $action = rex_request('warehouse_deeplink', 'string', '');
    if($action !== '') {
        switch ($action) {
            case 'cart':
                rex_response::sendRedirect($domain->getCartArtUrl());
                break;
            case 'order':
                rex_response::sendRedirect($domain->getOrderArtUrl());
                break;
        }
    }
}
