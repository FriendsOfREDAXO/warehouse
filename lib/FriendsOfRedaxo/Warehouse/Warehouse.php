<?php

namespace FriendsOfRedaxo\Warehouse;

use InvalidArgumentException;
use rex_ycom_auth;
use rex_config;
use rex_sql;
use rex;
use rex_response;
use rex_addon;
use rex_clang;
use rex_exception;
use rex_logger;
use rex_yform;
use rex_fragment;
use rex_yform_email_template;

class Warehouse
{

    public static $fields = [
        'salutation','firstname', 'lastname', 'birthdate', 'company', 'department', 'address', 'zip', 'city', 'country', 'email', 'phone',
        'to_salutation','to_firstname', 'to_lastname', 'to_company', 'to_department', 'to_address', 'to_zip', 'to_city', 'to_country',
        'separate_delivery_address', 'payment_type', 'note', 'iban', 'bic', 'direct_debit_name', 'info_news_ok'
    ];

    public const PAYMENT_OPTIONS = [
        'prepayment' => '{{ payment_type_prepayment }}',
        'invoice' => '{{ payment_type_invoice }}',
        'paypal' => '{{ payment_type_paypal }}',
        'direct_debit' => '{{ payment_type_direct_debit }}',
    ];

    /*
    public static $age_checked_values = [
        'known',
        'other'
    ];
    */

    public static function getPaypalClientId() :string
    {
        if (rex_config::get('warehouse', 'sandboxmode')) {
            return rex_config::get('warehouse', 'paypal_sandbox_client_id');
        }
        return rex_config::get('warehouse', 'paypal_client_id');
    }

    public static function getPaypalSecret() :string
    {
        if (rex_config::get('warehouse', 'sandboxmode')) {
            return rex_config::get('warehouse', 'paypal_sandbox_secret');
        }
        return rex_config::get('warehouse', 'paypal_secret');
    }

    public static function addToCart(int $article_id, int $article_variant_id = null, int $quantity = 1) :bool
    {
        $added = false;
        if($article_variant_id > 0) {
            $article_variant = ArticleVariant::get($article_variant_id);
            $article = $article_variant->getArticle();
        } else {
            $article = Article::get($article_id);
        }

        $cart = self::getCart();
        if ($quantity >= 1) {
            $cart[$article->getId()]['count'] += $quantity;
            $added = true;
        }

        rex_set_session('warehouse_cart', $cart);
        self::cartUpdate();

        // TODO: Aktion nach hinzufügen zum Warenkorb - entweder direkt zum Checkout, zum Warekorb oder auf der Artikelseite bleiben
        /*
        if (rex_request('art_type', 'string') == 'warehouse_single' || (rex_config::get('warehouse', 'cart_mode') == 'page' && rex_request('article_id', 'int'))) {
            rex_redirect(rex_request('article_id'), '', ['showcart' => 1]);
        } else {
            self::redirect_from_cart($added, 1);
        }
        */
        return $added;
    }


    /**
     * Warenkorb aktualisieren (Preise, Steuern, Gesamtsumme)
     * @return void 
     */
    public static function cartUpdate() :void
    {
        $cart = self::getCart();
        // TODO: Warenkorb aktualisieren
        rex_set_session('warehouse_cart', $cart);
    }

    public static function modifyCart(int $article_id, int $article_variant_id, int|false $quantity, string $mode = '=') :void
    {
        $cart = self::getCart();
        if($quantity === false) {
            unset($cart[$article_id]);
        }
        // mode = "=" => "set", "+" => "add", "-" => "remove"
        if($mode == '=' && $quantity !== false) {
            $cart[$article_id][$article_variant_id]['count'] = $quantity;
        } elseif($mode == '+') {
            $cart[$article_id][$article_variant_id]['count'] += $quantity;
        } elseif($mode == '-') {
            $cart[$article_id][$article_variant_id]['count'] -= $quantity;
        }
        // Check if quantity is valid, no negative values - remove article from cart
        if ($cart[$article_id][$article_variant_id]['count'] <= 0) {
            unset($cart[$article_id][$article_variant_id]);
        }
        rex_set_session('warehouse_cart', $cart);
        self::cartUpdate();
    }

    public static function deleteArticleFromCart(int $article_id, int $variant_id) :void
    {
        self::modifyCart($article_id, $variant_id, false);
    }

    /**
     * Total (Warenkorb mit Shipping)
     * @return float
     */
    public static function getCartTotal() :float
    {
        $sum = (float) self::getSubTotal();
        $sum += (float) Shipping::getCost();
        $sum -= (float) self::getDiscountValue();
        return $sum;
    }

    /*
     * Warenkorbrabatt
     */
    public static function getDiscountValue()
    {
        return 0;
    }

    /**
     * Sub Total (Warenkorb ohne Versandkosten)
     * @return float
     */
    public static function getSubTotal() :float
    {
        $cart = self::getCart();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['total'];
        }
        return $sum;
    }

    public static function getSubTotalNetto()
    {
        $cart = self::getCart();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['price_netto'] * $item['count'];
        }
        return round($sum, 2);
    }

    public static function getTaxTotal()
    {
        $cart = self::getCart();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['taxval'];
        }
        return round($sum, 2);
    }

    public static function countCart()
    {
        return count(self::getCart());
    }

    public static function getCart()
    {
        return rex_session('warehouse_cart', 'array');
    }

    public static function getCustomerData()
    {
        return rex_session('user_data', 'array');
    }

    public static function emptyCart()
    {
        rex_unset_session('warehouse_cart');
    }

    public static function getTax()
    {
        // TODO: Tax pro Warenkorb-Inhalt berechnen
        return 0;
    }

    public static function getCartNetto()
    {
        return self::getSubTotalNetto();
    }

    public static function saveCartAsOrder(string $payment_id = '') :bool
    {
        $order = Order::create();
        $cart = self::getCart();

        $shipping = Shipping::getCost();
        $user_data = rex_session('user_data', 'array');

        $order->setOrderTotal(self::getCartTotal());
        $order->setPaymentId($payment_id);
        $order->setPaymentType($user_data['payment_type']);
        $order->setPaymentConfirm($user_data['payment_confirm']);
        $order->setOrderJson(json_encode([
            'cart' => $cart,
            'user_data' => $user_data
        ]));
        $order->setCreateDate(date('Y-m-d H:i:s'));
        $order->setOrderText(self::getOrderAsText());
        $order->setFirstname($user_data['firstname']);
        $order->setLastname($user_data['lastname']);
        // $order->setBirthdate($user_data['birthdate'] ?? '');
        $order->setAddress($user_data['address'] ?? '');
        $order->setZip($user_data['zip'] ?? '');
        $order->setCity($user_data['city'] ?? '');
        $order->setEmail($user_data['email']);

        if (rex_addon::get('ycom')->isAvailable()) {
            $ycom_user = rex_ycom_auth::getUser();
            if ($ycom_user) {
                $values['ycom_user_id'] = $ycom_user->getId();
            }
        }
        
        return $order->save();
    }

    public static function getOrderAsText()
    {
        $cart = self::getCart();
        $shipping = Shipping::getCost();
        $total = self::getCartTotal();

        $return = '';
        $return .= mb_str_pad('Art. Nr.', 20, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad('Artikel', 45, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad('Anzahl', 7, ' ', STR_PAD_LEFT);
        $return .= mb_str_pad(rex_config::get('warehouse', 'currency'), 10, ' ', STR_PAD_LEFT);
        $return .= mb_str_pad(rex_config::get('warehouse', 'currency'), 10, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        $return .= str_repeat('-', 92);
        $return .= PHP_EOL;

        foreach ($cart as $pos) {
            if ($pos['var_whvarid']) {
                $return .= mb_str_pad(mb_substr(html_entity_decode($pos['var_whvarid']), 0, 20), 20, ' ', STR_PAD_RIGHT);
            } else {
                $return .= mb_str_pad(mb_substr(html_entity_decode($pos['whid']), 0, 20), 20, ' ', STR_PAD_RIGHT);
            }
            $return .= mb_str_pad(mb_substr(html_entity_decode($pos['name']), 0, 45), 45, ' ', STR_PAD_RIGHT);
            $return .= mb_str_pad($pos['count'], 7, ' ', STR_PAD_LEFT);
            $return .= mb_str_pad(number_format($pos['price_netto'], 2), 10, ' ', STR_PAD_LEFT);
            $return .= mb_str_pad(number_format($pos['price_netto'] * $pos['count'], 2), 10, ' ', STR_PAD_LEFT);
            $return .= PHP_EOL;
            if (is_array($pos['attributes'])) {
                foreach ($pos['attributes'] as $attr) {
                    $return .= str_repeat(' ', 20);
                    $return .= mb_substr(html_entity_decode($attr['value'] . '  ' . $attr['at_name'] . ': ' . $attr['label']), 0, 70);
                    $return .= PHP_EOL;
                }
            }
            $return .= str_repeat(' ', 20);
            $return .= mb_substr(html_entity_decode('Steuer: ' . $pos['taxpercent'] . '% = ' . number_format($pos['taxval'], 2)), 0, 70);
            $return .= PHP_EOL;
        }
        $return .= str_repeat('-', 92);
        $return .= PHP_EOL;
        $return .= mb_str_pad('Summe', 55, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad(number_format(Warehouse::getSubTotalNetto(), 2), 37, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        $return .= mb_str_pad('Mehrwertsteuer', 55, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad(number_format(Warehouse::getTaxTotal(), 2), 37, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        if (Warehouse::getDiscountValue()) {
            $return .= mb_str_pad(rex_config::get("warehouse", "global_discount_text"), 55, ' ', STR_PAD_RIGHT);
            $return .= mb_str_pad(number_format(Warehouse::getDiscountValue(), 2), 37, ' ', STR_PAD_LEFT);
            $return .= PHP_EOL;
        }
        $return .= mb_str_pad('Versand', 55, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad(number_format($shipping, 2), 37, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        $return .= str_repeat('-', 92);
        $return .= PHP_EOL;
        $return .= mb_str_pad('Total', 55, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad(number_format($total, 2), 37, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        $return .= str_repeat('=', 92);
        $return .= PHP_EOL;

        return $return;
    }



    public static function getOrderAsHtml()
    {
        $cart = self::getCart();
        $shipping = Shipping::getCost();
        $total = self::getCartTotal();
        $return = '';

        $return .= '<table><thead><tr><th>';
        $return .= 'Art. Nr.</th><th>';
        $return .= 'Artikel</th><th style="text-align:right">';
        $return .= 'Anzahl</th><th style="text-align:right">';
        $return .= rex_config::get('warehouse', 'currency') . '</th><th style="text-align:right">';
        $return .= rex_config::get('warehouse', 'currency') . '</th></tr></head><tbody>';


        foreach ($cart as $pos) {
            $return .= '<tr><td>';
            if ($pos['var_whvarid']) {
                $return .= mb_substr(html_entity_decode($pos['var_whvarid']), 0, 20) . '</td><td>';
            } else {
                $return .= mb_substr(html_entity_decode($pos['whid']), 0, 20) . '</td><td>';
            }
            $return .= mb_substr(html_entity_decode($pos['name']), 0, 45);

            if (is_array($pos['attributes'])) {
                foreach ($pos['attributes'] as $attr) {
                    $return .= '<br>';
                    $return .= mb_substr(html_entity_decode($attr['value'] . '  ' . $attr['at_name'] . ': ' . $attr['label']), 0, 70);
                }
            }

            $return .= '<br>' . html_entity_decode('Steuer: ' . $pos['taxpercent'] . '% = ' . number_format($pos['taxval'], 2));

            $return .= '</td><td style="text-align:right">';

            $return .= $pos['count'] . '</td><td style="text-align:right">';
            $return .= number_format($pos['price_netto'], 2) . '</td><td style="text-align:right">';
            $return .= number_format($pos['price_netto'] * $pos['count'], 2) . '</td></tr>';
        }
        $return .= '<tr class="topline"><td></td><td>Summe</td><td></td><td></td><td style="text-align:right">';
        $return .= number_format(Warehouse::getSubTotalNetto(), 2) . '</td></tr>';
        $return .= '<tr><td></td><td>Mehrwertsteuer</td><td></td><td></td><td style="text-align:right">';
        $return .= number_format(Warehouse::getTaxTotal(), 2) . '</td></tr>';

        if (Warehouse::getDiscountValue()) {
            $return .= '<tr><td></td><td>' . rex_config::get("warehouse", "global_discount_text") . '</td><td></td><td style="text-align:right">';

            $return .= number_format(Warehouse::getDiscountValue(), 2) . '</td></tr>';
        }
        $return .= '<tr><td></td><td>Versand</td><td></td><td></td><td style="text-align:right">';
        $return .= number_format($shipping, 2) . '</td></tr>';

        $return .= '<tr class="topline bottomthickline"><td></td><td>Total</td><td></td><td></td><td style="text-align:right">';
        $return .= number_format($total, 2) . '</td></tr>';

        $return .= '</tbody></table>';
        return $return;
    }


    public static function getCustomerDataAsText()
    {

        $user_data = self::getCustomerData();

        $return = '';

        $return .= 'Adresse' . PHP_EOL;
        $return .= PHP_EOL;

        $return .= ($user_data['company'] ?? '') ? $user_data['company'] . PHP_EOL : '';
        $return .= ($user_data['salutation'] ?? '') ? $user_data['salutation'] . PHP_EOL : '';
        $return .= $user_data['firstname'] . ' ' . $user_data['lastname'] . PHP_EOL;
        $return .= ($user_data['department'] ?? '') ? $user_data['department'] . PHP_EOL : '';
        $return .= ($user_data['address'] ?? '') ? $user_data['address'] . PHP_EOL : '';
        $return .= trim(($user_data['country'] ?? '') . ' ' . ($user_data['zip'] ?? '') . ' ' . ($user_data['city'] ?? '')) . PHP_EOL;
        $return .= PHP_EOL;
        $return .= ($user_data['phone'] ?? '') ? 'Telefon: ' . $user_data['phone'] . PHP_EOL : '';
        $return .= ($user_data['email'] ?? '') ? $user_data['email'] . PHP_EOL : '';
        $return .= PHP_EOL;
        if (isset($user_data['birthdate']) && $user_data['birthdate']) {
            $return .= 'Geburtsdatum:' . PHP_EOL;
            $return .= $user_data['birthdate'] . PHP_EOL;
        }
        $return .= PHP_EOL;
        $return .= 'Lieferadresse' . PHP_EOL;
        $return .= PHP_EOL;


        $return .= ($user_data['to_company'] ?? '') ? $user_data['to_company'] . PHP_EOL : '';
        $return .= ($user_data['to_salutation'] ?? '') . PHP_EOL;
        $return .= $user_data['to_firstname'] . ' ' . $user_data['to_lastname'] . PHP_EOL;
        $return .= ($user_data['to_department'] ?? '') ? $user_data['to_department'] . PHP_EOL : '';
        $return .= ($user_data['to_address'] ?? '') ? $user_data['to_address'] . PHP_EOL : '';
        $return .= trim($user_data['to_country'] . ' ' . $user_data['to_zip'] . ' ' . $user_data['to_city']) . PHP_EOL;
        $return .= PHP_EOL;
        $return .= ($user_data['note'] ?? '') ? 'Bemerkung:' . PHP_EOL . $user_data['note'] . PHP_EOL : '';
        $return .= PHP_EOL;
$return .= 'Zahlungsweise: ' . (self::PAYMENT_OPTIONS[$user_data['payment_type']] ?? $user_data['payment_type']) . PHP_EOL;
        $return .= PHP_EOL;
        if ($user_data['payment_type'] == 'direct_debit') {
            $return .= 'IBAN: ' . $user_data['iban'] . PHP_EOL;
            $return .= 'BIC: ' . $user_data['bic'] . PHP_EOL;
            if ($user_data['direct_debit_name']) {
                $return .= 'Kontoinhaber: ' . $user_data['direct_debit_name'] . PHP_EOL;
            } else {
                $return .= 'Kontoinhaber: ' . $user_data['firstname'] . ' ' . $user_data['lastname'] . PHP_EOL;
            }
        }

        return $return;
    }

    /**
     * execute_payment wird aufgerufen, wenn die Zahlung abgeschlossen ist.
     */
    public static function PaypalPaymentApproved($payment) :bool
    {
        // TODO: Stattdessen response auswerten, PayPalHttp\HttpResponse, dort id, status auswerten. Ansatz war in warehouse v1
        $order = Order::query()->where('payment_id', $payment->id)->where('payment_confirm', '')->findOne();
        if ($order) {
            $order->setPaymentConfirm(date('Y-m-d H:i:s'));
            return $order->save();
        }
        return false;
    }

    public static function PaypalPaymentApprovedViaResponse($response)
    {
        $order = Order::query()->where('payment_id', $response->result->id)->where('payment_confirm', '')->findOne();
        if ($order) {
            $order->setPaypalConfirmToken(json_encode($response));
            $order->setPaymentConfirm(date('Y-m-d H:i:s'));
            return $order->save();
        }
    }

    public static function countItemsInCart()
    {
        return Cart::countItems();
    }

    /**
     * Aufruf aus Action der Adresseingabe
     * @param type $params
     */
    public static function saveCartInSession($params)
    {
        $value_pool = $params->params['value_pool']['email'];
        rex_set_session('warehouse_data', $value_pool);
    }

    /**
     * Aufruf aus Action der Adresseingabe
     * @param type $params
     */
    public static function saveCustomerInSession($params)
    {
        $value_pool = $params->params['value_pool']['email'];
        foreach (self::$fields as $field) {
            if (in_array('to_' . $field, self::$fields)) {
                $value_pool['to_' . $field] = $value_pool['to_' . $field] ?? ($value_pool[$field] ?? '');
            }
        }

        rex_set_session('user_data', $value_pool);
    }

    public static function getCategoryPath(int $cat_id)
    {
        $category = Category::get($cat_id);
        if (!$category) {
            return [];
        }
        $path = [];

        $current_category = $category;
        while ($current_category !== null) {
            $path[] = $current_category;
            $current_category = $current_category->getParent();
        }
        return array_reverse($path);
    }

    public static function getPaymentOptions()
    {
        return self::PAYMENT_OPTIONS;
    }

    public static function send_notification_email($send_redirect = true, $order_id = '')
    {
        $cart = self::getCart();
        $warehouse_userdata = self::getCustomerData();

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

        foreach (explode(',', Warehouse::getConfig('order_email')) as $email) {
            $yf->setActionField('tpl2email', [Warehouse::getConfig('email_template_seller'), $email]);
        }
        $yf->setActionField('tpl2email', [Warehouse::getConfig('email_template_customer'), 'email']);
        $yf->setActionField('callback', ['warehouse::clear_cart']);

        $yf->getForm();
        $yf->setObjectparams('send', 1);
        $yf->executeActions();
        if (rex::isDebugMode()) {
            rex_logger::factory()->log('notice', 'Warehouse Order Email sent', [], __FILE__, __LINE__);
        }
        if ($send_redirect) {
            rex_response::sendRedirect(rex_getUrl(Warehouse::getConfig('thankyou_page'), '', json_decode(rex_config::get('warehouse', 'paypal_getparams'), true), '&'));
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
        $warehouse_userdata = Warehouse::getCustomerData();

        $yf = new rex_yform();

        $yf->setObjectparams('csrf_protection', false);

        $yf->setValueField('hidden', ['email', $warehouse_userdata['email']]);
        $yf->setValueField('hidden', ['company', $warehouse_userdata['company']]);
        $yf->setValueField('hidden', ['salutation', $warehouse_userdata['salutation']]);
        $yf->setValueField('hidden', ['firstname', $warehouse_userdata['firstname']]);
        $yf->setValueField('hidden', ['lastname', $warehouse_userdata['lastname']]);
        $yf->setValueField('hidden', ['payment_type', $warehouse_userdata['payment_type']]);

        foreach (explode(',', Warehouse::getConfig('order_email')) as $email) {
            $yf->setValueField('html', ['', $email]);
            $yf->setActionField('tpl2email', [Warehouse::getConfig('email_template_seller'), trim($email)]);
        }

        $etpl = Warehouse::getConfig('email_template_customer');
        if (rex_yform_email_template::getTemplate($etpl . '_' . rex_clang::getCurrent()->getCode())) {
            $etpl = $etpl . '_' . rex_clang::getCurrent()->getCode();
        }
        $yf->setActionField('tpl2email', [$etpl, 'email']);

        $yf->executeActions();
        $yf->setObjectparams('send', 1);
        $yf->getForm();
    }

    /** @api */
    public static function getConfig(string $key) :mixed
    {
        return rex_config::get('warehouse', $key);
    }
}
