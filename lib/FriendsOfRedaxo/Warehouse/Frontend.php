<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_extension;
use rex;
use rex_login;
use rex_article;
use rex_config;
use rex_yrewrite;
use Url\Url;

class Frontend {
    public static function init() {

        rex_login::startSession();
    
        rex_extension::register('PACKAGES_INCLUDED', function () {
    
            if (rex_request('action', 'string') == 'add_to_cart') {
                Warehouse::addToCart();
            }
            if (rex_request('action', 'string') == 'modify_cart') {
                Warehouse::modifyCart();
            }
    
            if (rex_article::getCurrentId() == Warehouse::getConfig('order_page')) {
                Warehouse::clean_cart();
            }
    
            $manager = Url::resolveCurrent();
            $warehouse_prop = [
                'sitemode' => 'none',
                'seo_title' => '',
                'path' => [],
                'tree' => []
            ];
            if ($manager) {
                $profile = $manager->getProfile();
                $seo = $manager->getSeo();
    
                $data_id = (int)$manager->getDatasetId();
                if ($profile->getTableName() == rex::getTable('warehouse_article')) {
                    if ($var_id = rex_get('var_id', 'int')) {
                        $article = Article::get_articles(0, [$data_id, $var_id], true);
                    } else {
                        $article = Article::get_articles(0, [$data_id], true, 0, 1);
                    }
                    $warehouse_prop['sitemode'] = 'article';
                    $warehouse_prop['seo_title'] = $article->get_name();
                    $warehouse_prop['path'] = Warehouse::getCategoryPath($article->category_id);
                } elseif ($profile->getTableName() == rex::getTable('warehouse_category')) {
                    $warehouse_prop['sitemode'] = 'category';
                    $warehouse_prop['seo_title'] = $seo['title'];
                    $warehouse_prop['path'] = Warehouse::getCategoryPath($data_id);
                }
                $curl = rtrim(rex_yrewrite::getFullPath(), '/') . $_SERVER['REQUEST_URI'];
                rex_set_session('current_page', $curl);
            }
            $warehouse_prop['tree'] = Warehouse::get_category_tree();
            rex::setProperty('warehouse_prop', $warehouse_prop);
    
            if (rex_article::getCurrentId() == Warehouse::getConfig('thankyou_page')) {
                // Bei Dankeseite Paypal bestätigen
                if (rex_get('paymentId')) {
                    Warehouse::set_cart_from_payment_id(rex_get('paymentId'));
                    PayPal::ExecutePayment();
                    // Führt den E-Mail Versand im Hintergrund aus
                    //                $yf = warehouse::summary_form(true);
                    Warehouse::clear_cart();
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
                        Warehouse::send_mails();
                        Warehouse::updateStockAfterOrder();
                        Warehouse::clear_cart();
                    } else {
                        rex_redirect(Warehouse::getConfig("payment_error"));
                    }
                }
            }
        });
    }
}
