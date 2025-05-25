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
use rex_path;
use rex_yform_email_template;

class Warehouse
{

    public const PATH_ARTICLE = 'warehouse/article/';
    public const PATH_ARTICLE_VARIANT = 'warehouse/article_variant/';
    public const PATH_CATEGORY = 'warehouse/category/';
    public const PATH_ORDER = 'warehouse/order/list';
    public const PATH_ORDER_DETAIL = 'warehouse/order/detail';
    

    public static $fields = [
        'salutation','firstname', 'lastname', 'birthdate', 'company', 'department', 'address', 'zip', 'city', 'country', 'email', 'phone',
        'to_salutation','to_firstname', 'to_lastname', 'to_company', 'to_department', 'to_address', 'to_zip', 'to_city', 'to_country',
        'separate_delivery_address', 'payment_type', 'note', 'iban', 'bic', 'direct_debit_name', 'info_news_ok'
    ];

    public const PAYMENT_OPTIONS = [
        'prepayment' => 'translate:warehouse.payment_options.prepayment',
        'invoice' => 'translate:warehouse.payment_options.invoice',
        'paypal' => 'translate:warehouse.payment_options.paypal',
        'direct_debit' => 'translate:warehouse.payment_options.direct_debit'
    ];

    public static function getCurrencySign() :string
    {
        return PayPal::CURRENCY_SIGNS[Warehouse::getConfig('currency')];
    }

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

    public static function getCustomerData()
    {
        return rex_session('user_data', 'array');
    }

    public static function getOrderAsText()
    {
        $cart = Cart::get();
        $shipping = Shipping::getCost();
        $total = $cart->getTotal();

        $return = '';
        $return .= mb_str_pad('Art. Nr.', 20, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad('Artikel', 45, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad('Anzahl', 7, ' ', STR_PAD_LEFT);
        $return .= mb_str_pad(Warehouse::getCurrencySign(), 10, ' ', STR_PAD_LEFT);
        $return .= mb_str_pad(Warehouse::getCurrencySign(), 10, ' ', STR_PAD_LEFT);
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
            $return .= mb_str_pad($pos['amount'], 7, ' ', STR_PAD_LEFT);
            $return .= mb_str_pad(number_format($pos['price_netto'], 2), 10, ' ', STR_PAD_LEFT);
            $return .= mb_str_pad(number_format($pos['price_netto'] * $pos['amount'], 2), 10, ' ', STR_PAD_LEFT);
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
        $return .= mb_str_pad(number_format($cart->getSubTotalNetto(), 2), 37, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        $return .= mb_str_pad('Mehrwertsteuer', 55, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad(number_format($cart->getTaxTotal(), 2), 37, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        if ($cart->getDiscountValue()) {
            $return .= mb_str_pad(rex_config::get("warehouse", "global_discount_text"), 55, ' ', STR_PAD_RIGHT);
            $return .= mb_str_pad(number_format($cart->getDiscountValue(), 2), 37, ' ', STR_PAD_LEFT);
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
        $cart = Cart::get();
        $shipping = Shipping::getCost();
        $total = $cart->getCartTotal();
        $return = '';

        $return .= '<table><thead><tr><th>';
        $return .= 'Art. Nr.</th><th>';
        $return .= 'Artikel</th><th style="text-align:right">';
        $return .= 'Anzahl</th><th style="text-align:right">';
        $return .= Warehouse::getCurrencySign() . '</th><th style="text-align:right">';
        $return .= Warehouse::getCurrencySign() . '</th></tr></head><tbody>';


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

            $return .= $pos['amount'] . '</td><td style="text-align:right">';
            $return .= number_format($pos['price_netto'], 2) . '</td><td style="text-align:right">';
            $return .= number_format($pos['price_netto'] * $pos['amount'], 2) . '</td></tr>';
        }
        $return .= '<tr class="topline"><td></td><td>Summe</td><td></td><td></td><td style="text-align:right">';
        $return .= number_format($cart->getSubTotalNetto(), 2) . '</td></tr>';
        $return .= '<tr><td></td><td>Mehrwertsteuer</td><td></td><td></td><td style="text-align:right">';
        $return .= number_format($cart->getTaxTotal(), 2) . '</td></tr>';

        if ($cart->getDiscountValue()) {
            $return .= '<tr><td></td><td>' . rex_config::get("warehouse", "global_discount_text") . '</td><td></td><td style="text-align:right">';

            $return .= number_format($cart->getDiscountValue(), 2) . '</td></tr>';
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

    public static function sendNotificationEmail($send_redirect = true, $order_id = '')
    {
        $cart = Cart::get();
        $warehouse_userdata = self::getCustomerData();

        $yform = new rex_yform();
        $fragment = new rex_fragment();
        $fragment->setVar('cart', $cart);
        $fragment->setVar('warehouse_userdata', $warehouse_userdata);

        $yform->setObjectparams('csrf_protection', false);
        $yform->setValueField('hidden', ['order_id', $order_id]);
        $yform->setValueField('hidden', ['email', $warehouse_userdata['email']]);
        $yform->setValueField('hidden', ['firstname', $warehouse_userdata['firstname']]);
        $yform->setValueField('hidden', ['lastname', $warehouse_userdata['lastname']]);
        $yform->setValueField('hidden', ['iban', $warehouse_userdata['iban']]);
        $yform->setValueField('hidden', ['bic', $warehouse_userdata['bic']]);
        $yform->setValueField('hidden', ['direct_debit_name', $warehouse_userdata['direct_debit_name']]);
        $yform->setValueField('hidden', ['payment_type', $warehouse_userdata['payment_type']]);
        $yform->setValueField('hidden', ['info_news_ok', $warehouse_userdata['info_news_ok']]);

        foreach (explode(',', Warehouse::getConfig('order_email')) as $email) {
            $yform->setActionField('tpl2email', [Warehouse::getConfig('email_template_seller'), $email]);
        }
        $yform->setActionField('tpl2email', [Warehouse::getConfig('email_template_customer'), 'email']);
        $yform->setActionField('callback', ['warehouse::clear_cart']);

        $yform->getForm();
        $yform->setObjectparams('send', 1);
        $yform->executeActions();
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

    public static function sendMails()
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

    /** @api */
    public static function getLabel(string $key) :string
    {
        $label = rex_config::get('warehouse', "label_".$key);
        if ($label === null || $label === '') {
            Logger::log('warning', 'Label for key "'.$key.'" not found in warehouse config.');
            return "{{ $key }}";
        }
        return $label;
    }

    /** @api */
    public static function setConfig(string $key, mixed $value) :void
    {
        rex_config::set('warehouse', $key, $value);
    }

    public static function getEnabledFeatures() :array
    {
        $value = rex_config::get('warehouse', 'enable_features');
        if (is_string($value)) {
            return explode('|', $value);
        }
        return [];
    }

    public static function isBulkPricesEnabled() :bool
    {
        // Überprüfe, ob 'bulk_prices' im Config-Wert vorhanden ist
        return in_array('bulk_prices', self::getEnabledFeatures());
    }

    public static function isWeightEnabled() :bool
    {
        // Überprüfe, ob 'weight' im Config-Wert vorhanden ist
        return in_array('weight', self::getEnabledFeatures());
    }

    public static function isVariantsEnabled() :bool
    {
        // Überprüfe, ob 'variants' im Config-Wert vorhanden ist
        return in_array('variants', self::getEnabledFeatures());
    }
    /** @api */
    public static function parse(string $file, array $values = [])
    {
        $fragment = new rex_fragment();
        $framework = Warehouse::getConfig('framework') ?: 'bootstrap5';
        $fragment_path = rex_path::addon('warehouse', 'fragments' .\DIRECTORY_SEPARATOR. 'warehouse' .\DIRECTORY_SEPARATOR. $framework  . \DIRECTORY_SEPARATOR . $file);

        $title = $values['title'] ?? '';
        $description = $values['description'] ?? '';
        
        if (file_exists($fragment_path)) {
            $fragment->setVar('title', $title);
            $fragment->setVar('description', $description, false);
            foreach($values as $key => $value) {
                $fragment->setVar($key, $value, false);
            }
            return $fragment->parse('warehouse' .\DIRECTORY_SEPARATOR. $framework  . \DIRECTORY_SEPARATOR . $file);
        }
    }

    public static function isDemoMode() :bool
    {
        return true;
    }

}
