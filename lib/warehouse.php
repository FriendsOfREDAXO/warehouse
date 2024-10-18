<?php

class warehouse
{

    static $fields = [
        'salutation','firstname', 'lastname', 'birthdate', 'company', 'department', 'address', 'zip', 'city', 'country', 'email', 'phone',
        'to_salutation','to_firstname', 'to_lastname', 'to_company', 'to_department', 'to_address', 'to_zip', 'to_city', 'to_country',
        'separate_delivery_address', 'payment_type', 'note', 'iban', 'bic', 'direct_debit_name', 'info_news_ok'
    ];

    static $age_checked_values = [
        'postident',
        'known',
        'other'
    ];

    public static function ensure_userdata_fields($user_data)
    {
        foreach (self::$fields as $field) {
            if (!isset($user_data[$field])) {
                $user_data[$field] = '';
            }
        }
        if (rex_plugin::get('ycom', 'auth')->isAvailable()) {
            $ycom_user = rex_ycom_auth::getUser();
            if ($ycom_user) {
                $ycom_userdata = $ycom_user->getData();
                // Sonderfall name
                if ($user_data['lastname'] == '') {
                    $user_data['lastname'] = $ycom_userdata['name'];
                }
                foreach ($user_data as $k => $v) {
                    if (isset($ycom_userdata[$k]) && $v == '') {
                        $user_data[$k] = $ycom_userdata[$k];
                    }
                }
                $user_data['ycom_userid'] = $ycom_user->getId();
                //            dump($ycom_user->getData());
            }
        }

        return $user_data;
    }

    public static function get_paypal_client_id()
    {
        if (rex_config::get('warehouse', 'sandboxmode')) {
            return rex_config::get('warehouse', 'paypal_sandbox_client_id');
        }
        return rex_config::get('warehouse', 'paypal_client_id');
    }

    public static function get_paypal_secret()
    {
        if (rex_config::get('warehouse', 'sandboxmode')) {
            return rex_config::get('warehouse', 'paypal_sandbox_secret');
        }
        return rex_config::get('warehouse', 'paypal_secret');
    }

    public static function add_to_cart()
    {
        $added = 0;
        $art_id = trim(rex_request('art_id'), '_');
        if (rex_request('art_type', 'string') == 'warehouse_single') {
            $art = warehouse_single_article::get_article();
            $art_uid = $art['art_id'];
        } else {
            $article = warehouse_articles::get_article($art_id);
            $attr_ids = rex_request('warehouse_attr', 'array', []);
            $art_uid = trim($art_id . '$$' . implode('$$', $attr_ids), '$');

            $art = [];
            $art['count'] = rex_request('order_count', 'int') ?: 1;
            if ($article->stock_item && $art['count'] > $article->stock) {
                $art['count'] = $article->stock;
            }
            $art['price'] = $article->get_price();
            if ($article->var_freeprice) {
                $art['price'] = abs(rex_request('price', 'float'));
                $art_id .= '-' . $art['price'];
                $art_uid = $art_id;
            }
            $art['name'] = $article->get_name();
            $art['cat_name'] = $article->cat_name;
            $art['cat_id'] = $article->cat_id;
            $art['description'] = $article->art_description;
            $art['image'] = $article->get_image();
            $art['art_id'] = $art_id;
            $art['var_id'] = $article->var_id;
            $art['var_whvarid'] = $article->var_whvarid;
            $art['var_freeprice'] = $article->var_freeprice ?? false;
            $art['whid'] = $article->whid;
            $art['tax'] = $article->tax;
            $art['stock_item'] = $article->stock_item;
            $art['stock'] = $article->stock;
            $art['free_shipping'] = $article->free_shipping;
            $art['attributes'] = [];
        }

        $cart = self::get_cart();
        if ($art['count'] > 0) {
            if (isset($cart[$art_uid])) {
                //            unset($cart[$art_id]);
                $cart[$art_uid]['count'] += $art['count'];
            } else {
                $cart[$art_uid] = $art;
            }
            $added = 1;
        }

        rex_set_session('warehouse_cart', $cart);
        //        dump($cart); exit;
        self::cart_recalc();
        if (rex_request('art_type', 'string') == 'warehouse_single' || (rex_config::get('warehouse', 'cart_mode') == 'page' && rex_request('article_id', 'int'))) {
            rex_redirect(rex_request('article_id'), '', ['showcart' => 1]);
        } else {
            self::redirect_from_cart($added, 1);
        }
    }


    public static function cart_recalc()
    {
        $cart = self::get_cart();
        foreach ($cart as $k => $art) {
            // Artikel nochmal aus der db einlesen, um zu prüfen, ob er nicht zwischenzeitlich verkauft wurde
            $warehouse_art = warehouse_articles::get_article($k);
            if (isset($art['stock_item'])) {
                if ($art['stock_item'] && ($art['count'] > $warehouse_art->stock)) {
                    $cart[$k]['count'] = $warehouse_art->stock;
                }
            }

            $taxpercent = 0;
            if ($art['tax']) {
                $taxpercent = rex_config::get('warehouse', $art['tax']);
            }
            $factor = (100 + $taxpercent) / 100;
            $cart[$k]['price_netto'] = round((float) $cart[$k]['price'] / $factor, 2);
            $cart[$k]['total'] = $cart[$k]['price'] * $cart[$k]['count'];
            $cart[$k]['taxsingle'] = ((float) $cart[$k]['price'] - $cart[$k]['price_netto']);
            $cart[$k]['taxval'] = ((float) $cart[$k]['price'] - $cart[$k]['price_netto']) * $cart[$k]['count'];
            $cart[$k]['taxpercent'] = $taxpercent;
            $cart[$k]['total'] = $cart[$k]['price'] * $cart[$k]['count'];
        }
        rex_set_session('warehouse_cart', $cart);
    }


    /**
     * Löscht showcart=1 aus der Url
     * @param type $url
     * @return type
     */
    public static function clean_url($url)
    {
        $prev_url = str_replace('?showcart=1&', '?', $url);
        $prev_url = str_replace('?showcart=1', '', $prev_url);
        $prev_url = str_replace('&showcart=1', '', $prev_url);
        return $prev_url;
    }


    /**
     * Regelt das Redirect nach Modifikationen des Warenkorbs
     * Aus Artikelseiten wird der request Parameter current_article als Redaxo Article Id angenommen
     * 
     * @param type $added - wenn ein Artikel erfolgreich hinzugefügt wurde, wird 1 übergeben
     */
    public static function redirect_from_cart($added = 0, $force_current_page = 0)
    {
        //        dump($_SERVER); exit;
        $old_page_id = rex_request('current_article', 'int', 0);
        if (rex_request('action') == 'modify_cart' || rex_request('action') == 'add_group_to_cart') {
            $added = 1;
        }

        if (rex_session('current_page') && (!$old_page_id || $force_current_page)) {
            $prev_url = self::clean_url(rex_session('current_page'));
            $deli = strpos($prev_url, '?') ? '&' : '?';
            if (rex_config::get('warehouse', 'cart_mode') == 'cart') {
                rex_redirect(warehouse::get_config('cart_page'));
            } else {
                rex_response::sendRedirect($prev_url . $deli . 'showcart=1');
            }
        }

        if ($added && $old_page_id > 0) {
            // wenn in den Settings auf Cart eingestellt ist, auf Cart weiterleiten
            if (rex_config::get('warehouse', 'cart_mode') == 'cart') {
                rex_redirect(warehouse::get_config('cart_page'));
            } else {
                if (rex_request('old_url')) {
                    $oldpage = rex_request('old_url');
                    if ($added) {
                        $oldpage .= '?showcart=1';
                    }
                } else {
                    if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
                        $oldpage = explode('?', $_SERVER['HTTP_REFERER'])[0] . '?showcart=1';
                    } else {
                        $oldpage = rtrim(rex::getServer(), '/') . rex_getUrl(rex_request('current_article'), '', ['showcart' => 1]);
                    }
                }
                rex_response::sendRedirect($oldpage); // Redirect from Del
            }
        } elseif ($old_page_id > 0) {
            $oldpage = rtrim(rex::getServer(), '/') . rex_getUrl(rex_request('current_article'), '', ['error' => 1]);
            rex_response::sendRedirect($oldpage);
        } else {
            rex_redirect(warehouse::get_config('cart_page'));
        }
    }

    /**
     * 
     */
    public static function modify_cart()
    {
        $cart = self::get_cart();
        $art_uid = rex_get('art_uid');
        $mod = rex_get('mod', 'string');
        if (isset($cart[$art_uid])) {
            if ($mod == 'del') {
                unset($cart[$art_uid]);
            } else {
                $cart[$art_uid]['count'] += (int) $mod;
                if ($cart[$art_uid]['count'] < 0) {
                    $cart[$art_uid]['count'] = 0;
                }
            }
            rex_set_session('warehouse_cart', $cart);
        }
        self::cart_recalc();
        if (rex_request('showcart', 'int')) {
            self::redirect_from_cart();
        } else {
            rex_redirect(warehouse::get_config('cart_page'));
        }
    }

    /**
     * Aufruf aus dem Warenkorb
     * Feldnamen = Artikel_Ids.
     */
    public static function modify_qty() {
        $cart = self::get_cart();
        foreach ($cart as $art_uid=>$item) {
            if ($qty = rex_request($art_uid,'int')) {
                $cart[$art_uid]['count'] = $qty;
            }
        }
        rex_set_session('warehouse_cart', $cart);
        self::cart_recalc();
    }

    /**
     * Total (Warenkorb mit Shipping)
     * @return type
     */
    public static function get_cart_total()
    {
        $sum = (float) self::get_sub_total();
        $sum += (float) self::get_shipping_cost();
        $sum -= (float) self::get_discount_value();
        return $sum;
    }

    /*
     * Warenkorbrabatt
     */
    public static function get_discount_value()
    {
        if (!rex_config::get("warehouse", "global_discount")) {
            return 0;
        }
        $total = self::get_sub_total();
        $discount_value = round(($total * rex_config::get("warehouse", "global_discount") / 100), 2);
        return $discount_value;
    }

    /**
     * Sub Total (Warenkorb ohne Shipping)
     * @return type
     */
    public static function get_sub_total()
    {
        $cart = self::get_cart();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['total'];
        }
        return $sum;
    }

    public static function get_sub_total_netto()
    {
        $cart = self::get_cart();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['price_netto'] * $item['count'];
        }
        return round($sum, 2);
    }

    public static function get_tax_total()
    {
        $cart = self::get_cart();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['taxval'];
        }
        return round($sum, 2);
    }

    public static function cart_positions_count()
    {
        return count(rex_session('warehouse_cart', 'array'));
    }

    /**
     * Versandkosten nur berechnen, wenn versandkostenpflichtige Artikel verschickt werden
     * @return type
     */
    public static function get_shipping_cost()
    {
        return warehouse_shipping::get_cost();
    }

    public static function get_cart()
    {
        return rex_session('warehouse_cart', 'array');
    }

    public static function get_user_data()
    {
        return rex_session('user_data', 'array');
    }

    /**
     * Warenkorb kann nur geladen werden, wenn die Bestellung noch nicht abgeschlossen ist
     * @param type $payment_id
     */
    public static function set_cart_from_payment_id($payment_id)
    {
        $data = rex_sql::factory()
            ->setTable(rex::getTable('warehouse_orders'))
            ->setWhere('payment_id = :payment_id', ['payment_id' => $payment_id])
            //                ->setWhere('paypal_confirm = :empty', ['empty' => ''])
            ->select('order_json')
            ->getArray();
        if ($data) {
            $cart_data = json_decode($data[0]['order_json'], true);
            rex_set_session('warehouse_cart', $cart_data['cart']);
            rex_set_session('user_data', $cart_data['user_data']);
        }
    }

    public static function get_tax()
    {
        $sub_total = self::get_sub_total();
        $tax = rex_config::get('warehouse', 'tax_value');
        $tax_value = round(($sub_total / (100 + $tax) * $tax), 2);
        return $tax_value;
    }

    public static function get_cart_netto()
    {
        return self::get_sub_total_netto();
    }

    public static function clear_cart()
    {
        rex_unset_session('warehouse_cart');
    }

    // für Aufruf aus yform Action
    public static function save_order()
    {
        self::save_order_to_db(0);
    }

    /**
     * 
     * @param type $payment_id
     */
    public static function save_order_to_db($payment_id = '')
    {
        $cart = self::get_cart();
        //        $cart['payment_confirm'] = md5(microtime(true).rand(0,999).'myspecialSecret');
        //        rex_set_session('warehouse_cart',$cart);

        foreach ($cart as $k => $v) {
            unset($v['attributes']);
            unset($v['description']);
            $cart[$k] = $v;
        }

        $shipping = self::get_shipping_cost();
        $total = self::get_cart_total();
        $user_data = rex_session('user_data', 'array');
        $user_data['payment_confirm'] = md5(microtime(true) . rand(0, 1000) . 'mySpecialsecret');
        rex_set_session('user_data', $user_data);

        $order_text = self::get_user_data_text();
        $order_text .= PHP_EOL . PHP_EOL;
        $order_text .= self::get_order_text();


        $sql = rex_sql::factory();

        $sql->setTable(rex::getTable('warehouse_orders'));
        $fields = $sql->select()->getFieldnames();        

        $sql->setDebug();
        $values = [
            'order_total' => $total,
            'payment_id' => $payment_id,
            'session_id' => session_id(),
            'payment_type' => $user_data['payment_type'],
            'payment_confirm' => $user_data['payment_confirm'],
            'order_json' => json_encode([
                'cart' => $cart,
                'user_data' => $user_data
            ]),
            'createdate' => date('Y-m-d H:i:s'),
            'order_text' => $order_text,
            'firstname' => $user_data['firstname'],
            'lastname' => $user_data['lastname'],
            'birthdate' => $user_data['birthdate'] ?? '',
            'address' => $user_data['address'] ?? '',
            'zip' => $user_data['zip'] ?? '',
            'city' => $user_data['city'] ?? '',
            'email' => $user_data['email']
        ];

        foreach ($values as $k=>$v) {
            if (!in_array($k,$fields)) {
                unset($values[$k]);
            }
        }

        if (rex_addon::get('ycom')->isAvailable()) {
            $ycom_user = rex_ycom_auth::getUser();
            if ($ycom_user) {
                $values['ycom_userid'] = $ycom_user->id;
            }
        }

        $sql->setTable(rex::getTable('warehouse_orders'));
        $sql->setValues($values);
        $sql->insert();
        return $sql->getLastId();
    }

    public static function get_order_text()
    {
        $cart = self::get_cart();
        $shipping = (float) self::get_shipping_cost();
        $total = (float) self::get_cart_total();

        $out = '';
        $out .= mb_str_pad('Art. Nr.', 20, ' ', STR_PAD_RIGHT);
        $out .= mb_str_pad('Artikel', 45, ' ', STR_PAD_RIGHT);
        $out .= mb_str_pad('Anzahl', 7, ' ', STR_PAD_LEFT);
        $out .= mb_str_pad(rex_config::get('warehouse', 'currency'), 10, ' ', STR_PAD_LEFT);
        $out .= mb_str_pad(rex_config::get('warehouse', 'currency'), 10, ' ', STR_PAD_LEFT);
        $out .= PHP_EOL;
        $out .= str_repeat('-', 92);
        $out .= PHP_EOL;

        foreach ($cart as $pos) {
            if ($pos['var_whvarid']) {
                $out .= mb_str_pad(mb_substr(html_entity_decode($pos['var_whvarid']), 0, 20), 20, ' ', STR_PAD_RIGHT);
            } else {
                $out .= mb_str_pad(mb_substr(html_entity_decode($pos['whid']), 0, 20), 20, ' ', STR_PAD_RIGHT);
            }
            $out .= mb_str_pad(mb_substr(html_entity_decode($pos['name']), 0, 45), 45, ' ', STR_PAD_RIGHT);
            $out .= mb_str_pad($pos['count'], 7, ' ', STR_PAD_LEFT);
            $out .= mb_str_pad(number_format($pos['price_netto'], 2), 10, ' ', STR_PAD_LEFT);
            $out .= mb_str_pad(number_format($pos['price_netto'] * $pos['count'], 2), 10, ' ', STR_PAD_LEFT);
            $out .= PHP_EOL;
            if (is_array($pos['attributes'])) {
                foreach ($pos['attributes'] as $attr) {
                    $out .= str_repeat(' ', 20);
                    $out .= mb_substr(html_entity_decode($attr['value'] . '  ' . $attr['at_name'] . ': ' . $attr['label']), 0, 70);
                    $out .= PHP_EOL;
                }
            }
            $out .= str_repeat(' ', 20);
            $out .= mb_substr(html_entity_decode('Steuer: ' . $pos['taxpercent'] . '% = ' . number_format($pos['taxval'], 2)), 0, 70);
            $out .= PHP_EOL;
        }
        $out .= str_repeat('-', 92);
        $out .= PHP_EOL;
        $out .= mb_str_pad('Summe', 55, ' ', STR_PAD_RIGHT);
        $out .= mb_str_pad(number_format(warehouse::get_sub_total_netto(), 2), 37, ' ', STR_PAD_LEFT);
        $out .= PHP_EOL;
        $out .= mb_str_pad('Mehrwertsteuer', 55, ' ', STR_PAD_RIGHT);
        $out .= mb_str_pad(number_format(warehouse::get_tax_total(), 2), 37, ' ', STR_PAD_LEFT);
        $out .= PHP_EOL;
        if (warehouse::get_discount_value()) {
            $out .= mb_str_pad(rex_config::get("warehouse", "global_discount_text"), 55, ' ', STR_PAD_RIGHT);
            $out .= mb_str_pad(number_format(warehouse::get_discount_value(), 2), 37, ' ', STR_PAD_LEFT);
            $out .= PHP_EOL;
        }
        $out .= mb_str_pad('Versand', 55, ' ', STR_PAD_RIGHT);
        $out .= mb_str_pad(number_format($shipping, 2), 37, ' ', STR_PAD_LEFT);
        $out .= PHP_EOL;
        $out .= str_repeat('-', 92);
        $out .= PHP_EOL;
        $out .= mb_str_pad('Total', 55, ' ', STR_PAD_RIGHT);
        $out .= mb_str_pad(number_format($total, 2), 37, ' ', STR_PAD_LEFT);
        $out .= PHP_EOL;
        $out .= str_repeat('=', 92);
        $out .= PHP_EOL;

        return $out;
    }



    public static function get_order_html()
    {
        $cart = self::get_cart();
        $shipping = (float) self::get_shipping_cost();
        $total = (float) self::get_cart_total();
        $out = '';

        $out .= '<style>
            table { border-collapse: collapse; }
            td { padding: 0 4px; vertical-align: top; }
            th { padding: 0 4px; }
            .bottomline td { border-bottom: 1px solid black }
            .bottomline th { border-bottom: 1px solid black }
            .bottomthickline td { border-bottom: 2px solid black }
            .topline td { border-top: 1px solid black }
            th { text-align: left }
        </style>';

        $out .= '<table style="width:700px"><thead><tr class="bottomline"><th>';
        $out .= 'Art. Nr.</th><th>';
        $out .= 'Artikel</th><th style="text-align:right">';
        $out .= 'Anzahl</th><th style="text-align:right">';
        $out .= rex_config::get('warehouse', 'currency') . '</th><th style="text-align:right">';
        $out .= rex_config::get('warehouse', 'currency') . '</th></tr></head><tbody>';


        foreach ($cart as $pos) {
            $out .= '<tr><td>';
            if ($pos['var_whvarid']) {
                $out .= mb_substr(html_entity_decode($pos['var_whvarid']), 0, 20) . '</td><td>';
            } else {
                $out .= mb_substr(html_entity_decode($pos['whid']), 0, 20) . '</td><td>';
            }
            $out .= mb_substr(html_entity_decode($pos['name']), 0, 45);

            if (is_array($pos['attributes'])) {
                foreach ($pos['attributes'] as $attr) {
                    $out .= '<br>';
                    $out .= mb_substr(html_entity_decode($attr['value'] . '  ' . $attr['at_name'] . ': ' . $attr['label']), 0, 70);
                }
            }

            $out .= '<br>' . html_entity_decode('Steuer: ' . $pos['taxpercent'] . '% = ' . number_format($pos['taxval'], 2));

            $out .= '</td><td style="text-align:right">';

            $out .= $pos['count'] . '</td><td style="text-align:right">';
            $out .= number_format($pos['price_netto'], 2) . '</td><td style="text-align:right">';
            $out .= number_format($pos['price_netto'] * $pos['count'], 2) . '</td></tr>';
        }
        $out .= '<tr class="topline"><td></td><td>Summe</td><td></td><td></td><td style="text-align:right">';
        $out .= number_format(warehouse::get_sub_total_netto(), 2) . '</td></tr>';
        $out .= '<tr><td></td><td>Mehrwertsteuer</td><td></td><td></td><td style="text-align:right">';
        $out .= number_format(warehouse::get_tax_total(), 2) . '</td></tr>';

        if (warehouse::get_discount_value()) {
            $out .= '<tr><td></td><td>' . rex_config::get("warehouse", "global_discount_text") . '</td><td></td><td style="text-align:right">';

            $out .= number_format(warehouse::get_discount_value(), 2) . '</td></tr>';
        }
        $out .= '<tr><td></td><td>Versand</td><td></td><td></td><td style="text-align:right">';
        $out .= number_format($shipping, 2) . '</td></tr>';

        $out .= '<tr class="topline bottomthickline"><td></td><td>Total</td><td></td><td></td><td style="text-align:right">';
        $out .= number_format($total, 2) . '</td></tr>';

        $out .= '</tbody></table>';
        return $out;
    }


    public static function get_user_data_text()
    {

        $user_data = self::get_user_data();

        $out = '';

        $out .= 'Adresse' . PHP_EOL;
        $out .= PHP_EOL;

        $out .= ($user_data['company'] ?? '') ? $user_data['company'] . PHP_EOL : '';
        $out .= ($user_data['salutation'] ?? '') ? $user_data['salutation'] . PHP_EOL : '';
        $out .= $user_data['firstname'] . ' ' . $user_data['lastname'] . PHP_EOL;
        $out .= ($user_data['department'] ?? '') ? $user_data['department'] . PHP_EOL : '';
        $out .= ($user_data['address'] ?? '') ? $user_data['address'] . PHP_EOL : '';
        $out .= trim(($user_data['country'] ?? '') . ' ' . ($user_data['zip'] ?? '') . ' ' . ($user_data['city'] ?? '')) . PHP_EOL;
        //        rex_logger::factory()->log('info','hier',[],__FILE__,__LINE__);
        $out .= PHP_EOL;
        $out .= ($user_data['phone'] ?? '') ? 'Telefon: ' . $user_data['phone'] . PHP_EOL : '';
        $out .= ($user_data['email'] ?? '') ? $user_data['email'] . PHP_EOL : '';
        $out .= PHP_EOL;
        if (isset($user_data['birthdate']) && $user_data['birthdate']) {
            $out .= 'Geburtsdatum:' . PHP_EOL;
            $out .= $user_data['birthdate'] . PHP_EOL;
        }
        $out .= PHP_EOL;
        $out .= 'Lieferadresse' . PHP_EOL;
        $out .= PHP_EOL;


        $out .= ($user_data['to_company'] ?? '') ? $user_data['to_company'] . PHP_EOL : '';
        $out .= ($user_data['to_salutation'] ?? '') . PHP_EOL;
        $out .= $user_data['to_firstname'] . ' ' . $user_data['to_lastname'] . PHP_EOL;
        $out .= ($user_data['to_department'] ?? '') ? $user_data['to_department'] . PHP_EOL : '';
        $out .= ($user_data['to_address'] ?? '') ? $user_data['to_address'] . PHP_EOL : '';
        $out .= trim($user_data['to_country'] . ' ' . $user_data['to_zip'] . ' ' . $user_data['to_city']) . PHP_EOL;
        $out .= PHP_EOL;
        $out .= ($user_data['note'] ?? '') ? 'Bemerkung:' . PHP_EOL . $user_data['note'] . PHP_EOL : '';
        $out .= PHP_EOL;
        $out .= 'Zahlungsweise: ' . self::get_payment_type($user_data['payment_type']) . PHP_EOL;
        $out .= PHP_EOL;
        if ($user_data['payment_type'] == 'direct_debit') {
            $out .= 'IBAN: ' . $user_data['iban'] . PHP_EOL;
            $out .= 'BIC: ' . $user_data['bic'] . PHP_EOL;
            if ($user_data['direct_debit_name']) {
                $out .= 'Kontoinhaber: ' . $user_data['direct_debit_name'] . PHP_EOL;
            } else {
                $out .= 'Kontoinhaber: ' . $user_data['firstname'] . ' ' . $user_data['lastname'] . PHP_EOL;
            }
        }

        return $out;
    }

    /**
     * Funktion wird aus warehouse_paypal->execute_payment aufgerufen, wenn die Zahlung abgeschlossen ist.
     * Kann nur einmal ausgeführt werden (wenn payment_confirm noch leer ist).
     * 
     */
    public static function paypal_approved($payment)
    {
        $sql = rex_sql::factory()->setTable(rex::getTable('warehouse_orders'))
            ->setWhere('payment_id = :payment_id', ['payment_id' => $payment->id])
            ->setWhere('payment_confirm = :empty', ['empty' => '']);
        $sql->setValue('payment_confirm', date('Y-m-d H:i:s'));
        $sql->update();
        // db
        // $payment->id = paypalId
    }

    public static function paypal_approved_v2($response)
    {

        /* $response
PayPalHttp\HttpResponse {#170 ▼
    +statusCode: 201
    +result: {#151 ▼
        +"id": "37G433630R1366212"
        +"intent": "CAPTURE"
        +"status": "COMPLETED"
        +"purchase_units": array:1 [▼
            0 => {#147 ▶}
        ]
        +"payer": {#167 ▶}
        +"create_time": "2021-08-04T10:25:05Z"
        +"update_time": "2021-08-04T10:25:15Z"
        +"links": array:1 [▼
            0 => {#169 ▶}
        ]
    }
    +headers: array:10 [▼
        "" => ""
        "Content-Type" => "application/json"
        "Content-Length" => "1653"
        "Connection" => "keep-alive"
        "Date" => "Wed, 04 Aug 2021 10"
        "Application_id" => "APP-80W284485P519543T"
        "Cache-Control" => "max-age=0, no-cache, no-store, must-revalidate"
        "Caller_acct_num" => "TYMDVHDDBLE62"
        "Paypal-Debug-Id" => "b9d0345b88d06"
        "Strict-Transport-Security" => "max-age=31536000; includeSubDomains"
    ]
}
*/


        $sql = rex_sql::factory()->setTable(rex::getTable('warehouse_orders'))
            ->setWhere('payment_id = :payment_id AND payment_confirm = ""', ['payment_id' => $response->result->id]);
        $sql->setValue('paypal_confirm_token', json_encode($response));
        $sql->setValue('payment_confirm', date('Y-m-d H:i:s'));
        $sql->update();
        // db
        // $payment->id = paypalId
    }

    public static function get_items_count_in_basket()
    {
        return count(rex_session('warehouse_cart', 'array'));
    }

    /**
     * Aufruf aus Action der Adresseingabe
     * @param type $params
     */
    public static function save_cart_in_session($params)
    {
        $value_pool = $params->params['value_pool']['email'];
        rex_set_session('warehouse_data', $value_pool);
    }

    /**
     * Aufruf aus Action der Adresseingabe
     * @param type $params
     */
    public static function save_userdata_in_session($params)
    {
        $value_pool = $params->params['value_pool']['email'];
        foreach (self::$fields as $field) {
            if (in_array('to_' . $field, self::$fields)) {
                $value_pool['to_' . $field] = $value_pool['to_' . $field] ?? ($value_pool[$field] ?? '');
            }
        }

        rex_set_session('user_data', $value_pool);
    }

    /**
     * Sortiert Elemente aus, die 0 Stück haben
     */
    public static function clean_cart()
    {
        $mycart = self::get_cart();
        foreach ($mycart as $k => $v) {
            if ($v['count'] == 0) {
                unset($mycart[$k]);
            }
        }
        rex_set_session('warehouse_cart', $mycart);
    }

    public static function get_path($cat_id)
    {

        $path = [];
        $qry = 'SELECT name_' . rex_clang::getCurrentId() . ' `name`, `id`, parent_id FROM ' . rex::getTable('warehouse_categories') . ' WHERE `id` = :id';
        $sql = rex_sql::factory();
        while ($cat_id > 0) {
            $current = $sql->getArray($qry, ['id' => $cat_id]);
            $path[] = $current[0];
            $cat_id = $current[0]['parent_id'];
        }
        return array_reverse($path);
    }

    public static function get_category_tree($depth = 2)
    {
        $otree = new warehouse_helper();
        $otree->set_query('SELECT id, name_' . rex_clang::getCurrentId() . ' name, image, parent_id FROM ' . rex::getTable('warehouse_categories') . ' WHERE status = 1 AND parent_id = |parent_id| ORDER BY prio');
        $otree->set_maxlev($depth);
        $tree = $otree->sql_full_tree();
        return $tree;
    }

    public static function get_payment_type($payment_key)
    {
        $payment_types = [
            'prepayment' => '{{ payment_type_prepayment }}',
            'invoice' => '{{ payment_type_invoice }}',
            'paypal' => '{{ payment_type_paypal }}',
            'direct_debit' => '{{ payment_type_direct_debit }}',
        ];
        if (isset($payment_types[$payment_key])) {
            return $payment_types[$payment_key];
        } else {
            return $payment_key;
        }
    }


    public static function get_available_payment_types()
    {
        $current_payment_types = [
            '{{ payment_type_prepayment }}' => 'prepayment',
            '{{ payment_type_direct_debit }}' => 'direct_debit',
            '{{ payment_type_paypal }}' => 'paypal'
        ];

        return $current_payment_types;
    }


    public static function update_order($id, $values, $where = [])
    {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('warehouse_orders'));
        $sql->setValues($values);
        $sql->setWhere('id = :id', ['id' => $id]);
        $sql->update();
    }


    public static function send_notification_email($send_redirect = true, $order_id = '')
    {
        $cart = self::get_cart();
        $warehouse_userdata = self::get_user_data();

        $yf = new rex_yform();
        $fragment = new rex_fragment();
        $fragment->setVar('cart', $cart);
        $fragment->setVar('warehouse_userdata', $warehouse_userdata);

        $yf->setObjectparams('csrf_protection', false);
        $yf->setValueField('hidden', ['order_id', $order_id]);
        $yf->setValueField('hidden', ['email', $warehouse_userdata['email']]);
        $yf->setValueField('hidden', ['firstname', $warehouse_userdata['firstname']]);
        $yf->setValueField('hidden', ['lastname', $warehouse_userdata['lastname']]);
        $yf->setValueField('hidden', ['iban', $warehouse_userdata['iban']]);
        $yf->setValueField('hidden', ['bic', $warehouse_userdata['bic']]);
        $yf->setValueField('hidden', ['direct_debit_name', $warehouse_userdata['direct_debit_name']]);
        $yf->setValueField('hidden', ['payment_type', $warehouse_userdata['payment_type']]);
        $yf->setValueField('hidden', ['info_news_ok', $warehouse_userdata['info_news_ok']]);

        foreach (explode(',', warehouse::get_config('order_email')) as $email) {
            $yf->setActionField('tpl2email', [warehouse::get_config('email_template_seller'), '', $email]);
        }
        $yf->setActionField('tpl2email', [warehouse::get_config('email_template_customer'), 'email']);
        $yf->setActionField('callback', ['warehouse::clear_cart']);

        $yf->getForm();
        $yf->setObjectparams('send', 1);
        $yf->executeActions();
        if (rex::isDebugMode()) {
            rex_logger::factory()->log('notice', 'Warehouse Order Email sent', [], __FILE__, __LINE__);
        }
        if ($send_redirect) {
            rex_response::sendRedirect(rex_getUrl(warehouse::get_config('thankyou_page'), '', json_decode(rex_config::get('warehouse', 'paypal_getparams'), true), '&'));
        }
    }

    public static function restore_session_from_payment_id($payment_id)
    {
        $sql = rex_sql::factory()->setTable(rex::getTable('warehouse_orders'));
        $sql->setWhere('payment_id = :payment_id', ['payment_id' => $payment_id]);
        $sql->select('session_id');
        $result = $sql->getArray();
        if (count($result) != 1) {
            return;
        }
        if (rex::isDebugMode()) {
            rex_logger::factory()->log('notice', json_encode([
                'payment_id' => $payment_id,
                'session_id' => $result[0]['session_id']
            ]), [], __FILE__, __LINE__);
        }
        session_id($result[0]['session_id']);
    }

    public static function send_mails()
    {
        $warehouse_userdata = warehouse::get_user_data();

        $yf = new rex_yform();

        $yf->setObjectparams('csrf_protection', false);

        $yf->setValueField('hidden', ['email', $warehouse_userdata['email']]);
        $yf->setValueField('hidden', ['company', $warehouse_userdata['company']]);
        $yf->setValueField('hidden', ['salutation', $warehouse_userdata['salutation']]);
        $yf->setValueField('hidden', ['firstname', $warehouse_userdata['firstname']]);
        $yf->setValueField('hidden', ['lastname', $warehouse_userdata['lastname']]);
        $yf->setValueField('hidden', ['payment_type', $warehouse_userdata['payment_type']]);

        foreach (explode(',', warehouse::get_config('order_email')) as $email) {
            $yf->setValueField('html', ['', $email]);
            $yf->setActionField('tpl2email', [warehouse::get_config('email_template_seller'), trim($email)]);
        }

        $etpl = warehouse::get_config('email_template_customer');
        if (rex_yform_email_template::getTemplate($etpl . '_' . rex_clang::getCurrent()->getCode())) {
            $etpl = $etpl . '_' . rex_clang::getCurrent()->getCode();
        }
        $yf->setActionField('tpl2email', [$etpl, 'email']);

        $yf->executeActions();
        $yf->setObjectparams('send', 1);
        $yf->getForm();
    }

    /**
     * Aktualisiert nach der Zahlung die Lagerbestände
     */
    public static function update_stock () {
        $cart = self::get_cart();
        foreach ($cart as $k=>$art) {
            if ($art['stock_item'] ?? false) {
                $warehouse_art = warehouse_articles::get_article($k);
                $warehouse_art->stock = $warehouse_art->stock - $art['count'];
                $warehouse_art->save();
            }
        }

    }



    /**
     * check_input_weight
     * 
     * Kann als Validate Funktion in yform verwendet werden.
     * 
     * warehouse::check_input_weight
     * 
     * Feld weight
     */
    public static function check_input_weight ($params, $vars, $names = '', $yform) {

        if (!trim(rex_config::get('warehouse','check_weight'),'|')) {
            // wenn in den Einstellungen kein Gewichtscheck aktiviert ist, Gewicht nicht prüfen
            return false;
        }

        $names = explode(',', $names);

        $art_weight = 0;
        $var_weight = 0;

        $has_error = false;
        $has_variants = false;

        foreach ($yform->getObjects() as $Object) {            
            if ($Object->getName() == 'weight') {
                $art_weight = (float) $Object->getValue();
            }
        }            

        foreach ($yform->getObjects() as $Object) {

            if ($Object->getName() == 'variants_id') {

                $be_relation_values = $Object->getValue();
                $table = rex_yform_manager_table::get(rex::getTable('warehouse_article_variants'));

                // ----- Find PrioFieldname if exists
                $prioFieldName = '';
                $fields = [];
                foreach ($table->getFields() as $field) {
                    if ('value' == $field->getType()) {
                        if ('prio' == $field->getTypeName()) {
                            $prioFieldName = $field->getName();
                        } else {
                            $fields[] = $field->getName();
                        }
                    }
                }
                $weight_field_num = 0;
                foreach ($fields as $k=>$v) {
                    // an welcher Stelle der Fieldlist steht das Gewichtsfeld? => $weight_field_num
                    if ('weight' == $v) {
                        $weight_field_num = $k;
                        break;
                    }
                }

                foreach ($be_relation_values as $be_value) {
                    $has_variants = true;
                    $var_weight = (float) $be_value[$weight_field_num] ?? 0;
                    if (($art_weight + $var_weight) == 0) {
                        $has_error = true;
                    }
                }
                
            }

        }

        if (!$has_variants && $art_weight == 0) {
            $has_error = true;
        }

        return $has_error;

    }

    public static function get_config($param) {
        $config_value = rex_config::get("warehouse", $param);
        if (!rex_addon::get('yrewrite')->isAvailable()) {
            return $config_value;
        }
        $domain = rex_yrewrite::getCurrentDomain();
        if ($domain) {
            $param_val = rex_config::get('warehouse',$param.'_'.$domain->getId());
            if ($param_val) {
                $config_value = $param_val;
            }
        }
        return $config_value;
    }







}
