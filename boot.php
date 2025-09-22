<?php

namespace FriendsOfRedaxo\Warehouse;

use FriendsOfRedaxo\Warehouse\QuickNavigation\QuickNavigationButton;
use rex;
use rex_login;
use rex_extension;
use rex_yform_manager_dataset;
use rex_yform;
use rex_addon;
use rex_api_function;
use rex_article;
use rex_be_controller;
use rex_extension_point;
use rex_fragment;
use rex_request;
use rex_response;
use rex_view;
use rex_yrewrite;
use Url\Url;

/** @var rex_addon $this */

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass('rex_warehouse_article', Article::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_article_variant', ArticleVariant::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_category', Category::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_order', Order::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_settings_domain', Domain::class);
    rex_yform_manager_dataset::setModelClass('rex_ycom_user', Customer::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_customer_address', CustomerAddress::class);
}

rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $ep) {
    if (rex_addon::get('ycom')->isAvailable() && !rex::isSafeMode()) {
        rex_yform_manager_dataset::setModelClass('rex_ycom_user', Customer::class);
    }
});

rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $ep) {
    if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
        rex_yform::addTemplatePath($this->getPath('ytemplates'));
    }
});

// Nur, wenn auf Backend-Seiten des Warehouse-Addons
if (rex::isBackend() && rex_be_controller::getCurrentPagePart(1) == 'warehouse') {
    // CSS im Backend laden
    rex_view::addCssFile($this->getAssetsUrl('css/backend.css'));
    // JS im Backend laden
    rex_view::addJsFile($this->getAssetsUrl('js/backend.js'));
}


if (rex::isFrontend()) {
    rex_login::startSession();

    $domain  = Domain::getCurrent();
    $this->setProperty('warehouse_domain', $domain);

    $action = rex_request('warehouse_deeplink', 'string', '');
    if ($action !== '' && $domain) {
        if ($action === 'cart') {
            rex_response::sendRedirect($domain->getCartArtUrl());
        } elseif ($action === 'order') {
            rex_response::sendRedirect($domain->getOrderArtUrl());
        }
    }

}

    rex_extension::register('PACKAGES_INCLUDED', function () {

        if (rex_addon::get('url')->isAvailable()) {
            
            $manager = Url::resolveCurrent();
            if ($manager) {
                \rex_extension::register('URL_SEO_TAGS', function(\rex_extension_point $ep) use ($manager) {
                    $tags = $ep->getSubject();

                    $titleValues = [];
                    $article = rex_article::get($manager->getArticleId());
                    $title = strip_tags($tags['title']);

                    if ($manager->getSeoTitle()) {
                        $titleValues[] = $manager->getSeoTitle();
                    }
                    if ($article) {
                        $domain = rex_yrewrite::getDomainByArticleId($article->getId());
                        $title = $domain->getTitle();
                        $titleValues[] = $article->getName();
                    }
                    if (count($titleValues)) {
                        $title = "abc" . rex_escape(str_replace('%T', implode(' / ', $titleValues), $title));
                    }
                    if ('' !== rex::getServerName()) {
                        $title = "xyz" . rex_escape(str_replace('%SN', rex::getServerName(), $title));
                    }

                    $tags['title'] = sprintf('<title>%s</title>', $title);
                    $ep->setSubject($tags);
                });
            }
        }
    });

    
/* quick_navigation Suche */
if (rex::isBackend() && rex::getUser() && rex_addon::get('quick_navigation')->isAvailable()) {
    \FriendsOfRedaxo\QuickNavigation\Button\ButtonRegistry::registerButton(new QuickNavigationButton(), 5);
}

/* in der Factory-Klasse rex_navigation den EP nutzen, um den Warenkorb-Button zu registrieren */
if (rex::isFrontend()) {
    rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
        $domain = Domain::getCurrent();
        $cartArtId = $domain ? $domain->getCartArtId() : null;

        if ($cartArtId) {
            // Dynamischer Text für den Link
            $fragment = new rex_fragment();
            $checkout_button = $fragment->parse('/warehouse/bootstrap5/navigation/cart.php');

            // Pattern sucht das <li> mit der passenden Klasse und ersetzt den gesamten <a>...</a> Inhalt
            // Verwende ein nicht-lazy Pattern, damit nur der <a> innerhalb des passenden <li> ersetzt wird
            $pattern = '/(<li\s+class="rex-article-' . preg_quote((string) $cartArtId, '/') . '[^"]*".*?>\s*)<a\b[^>]*>.*?<\/a>(\s*<\/li>)/s';

            // Ersetze den kompletten <a>...</a> durch den Button-Fragment
            $replacement = '$1' . $checkout_button . '$2';
            $subject = $ep->getSubject();
            $subject = preg_replace($pattern, $replacement, $subject);
            return $subject;
        }
        return $ep->getSubject();
    });
}

// EP für WAREHOUSE_TAX verwenden und weitere Steuersätze hinzufügen
rex_extension::register('WAREHOUSE_TAX_OTIONS', function (rex_extension_point $ep) {
    /** @var array<int,string> $taxes */
    $taxes = $ep->getSubject();
    $taxes[42] = '42%';
    krsort($taxes);
    return $taxes;
});

// APIs verfügbar machen
rex_api_function::register('WAREHOUSE_ORDER', Api\Order::class);
rex_api_function::register('WAREHOUSE_CART_API', Api\CartApi::class);

// YFORM-EPs registrieren
rex_extension::register('YFORM_DATA_LIST', Article::epYformDataList(...));
rex_extension::register('YFORM_DATA_LIST', Category::epYformDataList(...));
rex_extension::register('YFORM_DATA_LIST', Order::epYformDataList(...));
rex_extension::register('YFORM_DATA_LIST_ACTION_BUTTONS', Order::epYformDataListActionButtons(...));

rex_extension::register('YFORM_DATA_ADDED', function (rex_extension_point $ep) {
    $table = $ep->getParam('table');
    if ($table && $table->getTableName() === rex::getTable('warehouse_order')) {
        $data = $ep->getParam('data');
        if ($data instanceof Order) {
            rex_extension::registerPoint(new rex_extension_point('WAREHOUSE_ORDER_CREATED', $data));
        }
    }
});
