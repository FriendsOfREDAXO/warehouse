<?php

namespace FriendsOfRedaxo\Warehouse;

use rex;
use rex_login;
use rex_extension;
use rex_yrewrite;
use rex_article;
use rex_config;
use rex_yform_manager_dataset;
use rex_yform;
use rex_addon;
use rex_be_controller;
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

        if (rex_request('action', 'string') == 'add_to_cart') {
            // Warehouse::addToCart();
        }
        if (rex_request('action', 'string') == 'modify_cart') {
            // Warehouse::modifyCart();
        }

        if (rex_article::getCurrentId() == Warehouse::getConfig('order_page')) {
            Warehouse::emptyCart();
        }

        $manager = Url::resolveCurrent();
        if ($manager) {
            $profile = $manager->getProfile();
            $seo = $manager->getSeo();

            $data_id = (int) $manager->getDatasetId();
            if ($profile->getTableName() == rex::getTable('warehouse_article')) {
                if ($var_id = rex_get('var_id', 'int')) {
                    // $article = Article::getArt(0, [$data_id, $var_id], true);
                } else {
                    // $article = Article::get_articles(0, [$data_id], true, 0, 1);
                }
                $warehouse_prop['sitemode'] = 'article';
                // $warehouse_prop['seo_title'] = $article->getName();
                // $warehouse_prop['path'] = Warehouse::getCategoryPath($article->getCategoryId());
            } elseif ($profile->getTableName() == rex::getTable('warehouse_category')) {
                $warehouse_prop['sitemode'] = 'category';
                $warehouse_prop['seo_title'] = $seo['title'];
                $warehouse_prop['path'] = Warehouse::getCategoryPath($data_id);
            }
            $curl = rtrim(rex_yrewrite::getFullPath(), '/') . $_SERVER['REQUEST_URI'];
            rex_set_session('current_page', $curl);
        }
        // $warehouse_prop['tree'] = Warehouse::get_category_tree();
        rex::setProperty('warehouse_prop', $warehouse_prop);

        if (rex_article::getCurrentId() == Warehouse::getConfig('thankyou_page')) {
            // Bei Dankeseite Paypal bestätigen
            if (rex_get('paymentId')) {
                // Warehouse::set_cart_from_payment_id(rex_get('paymentId'));
                PayPal::ExecutePayment();
                // Führt den E-Mail Versand im Hintergrund aus
                Warehouse::emptyCart();
            }


            // Bei Dankeseite Wallee - Zahlungsbestätigung Wallee
            if (rex_get('action') == 'wpayment_confirm') {
                $user_data = Warehouse::getCustomerData();
                if (rex_get('key') == $user_data['payment_confirm']) {
                    // Confirm Order in db
                    $order = Order::query()
                        ->where('payment_confirm', rex_get('key'))->where('payed', 0)
                        ->findOne();
                    if (!$order) {
                        rex_redirect(rex_config::get("warehouse", "payment_error"));
                    }
                    $order->setValue('payed', 1);
                    if (!$order->save()) {
                        rex_redirect(Warehouse::getConfig("payment_error"));
                    }

                    // Send Mails
                    // Warehouse::send_mails();
                    // Warehouse::updateStockAfterOrder();
                    Warehouse::emptyCart();
                } else {
                    rex_redirect(Warehouse::getConfig("payment_error"));
                }
            }
        }
    });
}
