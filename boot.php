<?php

rex_yform_manager_dataset::setModelClass('rex_warehouse_articles', FriendsOfRedaxo\Warehouse\Article::class);
rex_yform_manager_dataset::setModelClass('rex_warehouse_categories', \FriendsOfRedaxo\Warehouse\Category::class);
rex_yform_manager_dataset::setModelClass('rex_warehouse_orders', \FriendsOfRedaxo\Warehouse\Order::class);

rex_yform::addTemplatePath($this->getPath('ytemplates'));
// rex_yform::addTemplatePath(rex_path::addon('warehouse', 'ytemplates'));


if (rex::isBackend()) {
    rex_view::addJsFile($this->getAssetsUrl('scripts/be_script.js'));
}

if (rex::isFrontend()) {

    rex_login::startSession();

    rex_extension::register('PACKAGES_INCLUDED', function () {

        $user_path = rex_session('user_path','array');
        if (!$_REQUEST || (count($_REQUEST) == 1) && isset($_REQUEST['PHPSESSID'])) {
            // ausschliessen: Fehler Artikel, Shop Detailseite
            if (false === in_array(rex_article::getCurrentId(),[rex_article::getNotfoundArticleId(),25])) {
                $user_path[] = rex_article::getCurrentId();
                $user_path = array_slice($user_path,-5);
                rex_set_session('user_path',$user_path);
//                dump($user_path);
            }
        }

        if (rex_request('action', 'string') == 'add_to_cart') {
            FriendsOfRedaxo\Warehouse\Warehouse::add_to_cart();
        }
        if (rex_request('action', 'string') == 'modify_cart') {
            // wird aufgerufen aus dem Warenkorb mit mod=+1 oder mod=-1 + art_id=...
            FriendsOfRedaxo\Warehouse\Warehouse::modify_cart();
        }
        // löscht Anzahl 0-Artikel auf der Bestellbestätigungsseite komplett aus dem Warenkorb
        if (rex_article::getCurrentId() == FriendsOfRedaxo\Warehouse\Warehouse::get_config('order_page')) {
            FriendsOfRedaxo\Warehouse\Warehouse::clean_cart();
        }

        $manager = Url\Url::resolveCurrent();
        $warehouse_prop = [
            'sitemode' => 'none',
            'seo_title' => '',
            'path' => [],
            'tree' => []
        ];
        if ($manager) {
            $profile = $manager->getProfile();
            $seo = $manager->getSeo();

            $data_id = (int) $manager->getDatasetId();
            if ($profile->getTableName() == rex::getTable('warehouse_articles')) {

                if ($var_id = rex_get('var_id', 'int')) {
                    $article = FriendsOfRedaxo\Warehouse\Article::get_articles(0, [$data_id, $var_id], true);
                } else {
                    $article = FriendsOfRedaxo\Warehouse\Article::get_articles(0, [$data_id], true, 0, 1);
                }
                $warehouse_prop['sitemode'] = 'article';
                $warehouse_prop['seo_title'] = $article->get_name();
                $warehouse_prop['path'] = FriendsOfRedaxo\Warehouse\Warehouse::get_path($article->category_id);
            } elseif ($profile->getTableName() == rex::getTable('warehouse_categories')) {

                $warehouse_prop['sitemode'] = 'category';
                $warehouse_prop['seo_title'] = $seo['title'];
                $warehouse_prop['path'] = FriendsOfRedaxo\Warehouse\Warehouse::get_path($data_id);
            }
            $curl = rtrim(rex_yrewrite::getFullPath(), '/') . $_SERVER['REQUEST_URI'];
            rex_set_session('current_page', $curl);
        }
        $warehouse_prop['tree'] = FriendsOfRedaxo\Warehouse\Warehouse::get_category_tree();
        rex::setProperty('warehouse_prop', $warehouse_prop);

        if (rex_article::getCurrentId() == FriendsOfRedaxo\Warehouse\Warehouse::get_config('thankyou_page')) {
            // Bei Dankeseite Paypal bestätigen
            if (rex_get('paymentId')) {
                FriendsOfRedaxo\Warehouse\Warehouse::set_cart_from_payment_id(rex_get('paymentId'));
                FriendsOfRedaxo\Warehouse\PayPal::execute_payment();
                // Führt den E-Mail Versand im Hintergrund aus
//                $yf = warehouse::summary_form(true);
                FriendsOfRedaxo\Warehouse\Warehouse::clear_cart();
            }


            // Bei Dankeseite Wallee - Zahlungsbestätigung Wallee
            if (rex_get('action') == 'wpayment_confirm') {
                $user_data = FriendsOfRedaxo\Warehouse\Warehouse::get_user_data();
                if (rex_get('key') == $user_data['payment_confirm']) {
                    // Confirm Order in db
                    $order = \FriendsOfRedaxo\Warehouse\Order::query()
                        ->where('payment_confirm',rex_get('key'))->where('payed',0)
                        ->findOne();
                    if (!$order) {
                        rex_redirect(rex_config::get("warehouse","payment_error"));
                    }
                    $order->setValue('payed',1);
                    if (!$order->save()) {
                        rex_redirect(FriendsOfRedaxo\Warehouse\Warehouse::get_config("payment_error"));
                    }

                    // Send Mails
                    FriendsOfRedaxo\Warehouse\Warehouse::send_mails();
                    FriendsOfRedaxo\Warehouse\Warehouse::update_stock();
                    FriendsOfRedaxo\Warehouse\Warehouse::clear_cart();
                } else {
                    rex_redirect(FriendsOfRedaxo\Warehouse\Warehouse::get_config("payment_error"));
                }
            }
        }
    });
}
