<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_config;
use rex_sql;
use rex;
use rex_extension;
use rex_extension_point;
use rex_logger;
use rex_fragment;
use rex_path;
use rex_yform_manager_dataset;
use NumberFormatter;

class Warehouse
{

    public const PATH_ARTICLE = 'warehouse/article/';
    public const PATH_ARTICLE_VARIANT = 'warehouse/article_variant/';
    public const PATH_CATEGORY = 'warehouse/category/';
    public const PATH_ORDER = 'warehouse/order/list';
    public const PATH_ORDER_DETAIL = 'warehouse/order/detail';
    

    public const YCOM_MODES = [
        'enforce_account' => 'warehouse.ycom_mode.enforce_account',
        'choose' => 'warehouse.ycom_mode.choose',
        'guest_only' => 'warehouse.ycom_mode.guest_only',
    ];

    /**
     * @deprecated Use Warehouse::getCurrency() instead
     */
    public static function getCurrencySign() :string
    {
        return PayPal::CURRENCY_SIGNS[self::getCurrency()];
    }

    public static function getCurrency() :string
    {
        return rex_config::get('warehouse', 'currency', 'EUR');
    }

    /**
     * Format a currency value using NumberFormatter
     * @param float|null $value The value to format
     * @param string $locale Locale for formatting (default: 'de_DE')
     * @return string Formatted currency string
     */
    public static function formatCurrency(?float $value, string $locale = 'de_DE'): string
    {
        if ($value === null) {
            return '';
        }
        
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $currencyCode = self::getCurrency();
        $formatted = $formatter->formatCurrency($value, $currencyCode);
        
        return $formatted !== false ? $formatted : '';
    }
    
    public static function getOrderAsText(): string
    {
        $cart = Cart::create();
        $shipping = Shipping::getCost();
        $total = Cart::getTotal();

        $return = '';
        $return .= mb_str_pad('Art. Nr.', 20, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad('Artikel', 45, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad('Anzahl', 7, ' ', STR_PAD_LEFT);
        $return .= mb_str_pad(Warehouse::getCurrency(), 10, ' ', STR_PAD_LEFT);
        $return .= mb_str_pad(Warehouse::getCurrency(), 10, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        $return .= str_repeat('-', 92);
        $return .= PHP_EOL;

        foreach ($cart->getItems() as $item) {
            $article_number = $item['article_id'];
            if ($item['type'] === 'variant' && $item['variant_id']) {
                $article_number .= '-' . $item['variant_id'];
            }
            
            $return .= mb_str_pad(mb_substr(html_entity_decode($article_number), 0, 20), 20, ' ', STR_PAD_RIGHT);
            $return .= mb_str_pad(mb_substr(html_entity_decode($item['name']), 0, 45), 45, ' ', STR_PAD_RIGHT);
            $return .= mb_str_pad($item['amount'], 7, ' ', STR_PAD_LEFT);
            $return .= mb_str_pad(number_format($item['price'], 2), 10, ' ', STR_PAD_LEFT);
            $return .= mb_str_pad(number_format($item['total'], 2), 10, ' ', STR_PAD_LEFT);
            $return .= PHP_EOL;
            
            // Calculate tax for this item
            $tax_amount = 0;
            $tax_rate = 0;
            if ($item['type'] === 'variant' && $item['variant_id']) {
                $variant = ArticleVariant::get($item['variant_id']);
                if ($variant) {
                    $tax_rate = $variant->getTax();
                    $net_price = $variant->getPrice('net');
                    $tax_amount = ($item['price'] - $net_price) * $item['amount'];
                }
            } else {
                $article = Article::get($item['article_id']);
                if ($article) {
                    $tax_rate = $article->getTax();
                    $net_price = $article->getPrice('net');
                    $tax_amount = ($item['price'] - $net_price) * $item['amount'];
                }
            }
            
            $return .= str_repeat(' ', 20);
            $return .= mb_substr(html_entity_decode('Steuer: ' . $tax_rate . '% = ' . number_format($tax_amount, 2)), 0, 70);
            $return .= PHP_EOL;
        }
        $return .= str_repeat('-', 92);
        $return .= PHP_EOL;
        $return .= mb_str_pad('Summe', 55, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad(number_format(Cart::getSubTotal(), 2), 37, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        $return .= mb_str_pad('Mehrwertsteuer', 55, ' ', STR_PAD_RIGHT);
        $return .= mb_str_pad(number_format(Cart::getTaxTotalByMode(), 2), 37, ' ', STR_PAD_LEFT);
        $return .= PHP_EOL;
        if (Cart::getDiscountValue()) {
            $return .= mb_str_pad(rex_config::get("warehouse", "global_discount_text"), 55, ' ', STR_PAD_RIGHT);
            $return .= mb_str_pad(number_format(Cart::getDiscountValue(), 2), 37, ' ', STR_PAD_LEFT);
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



    public static function getOrderAsHtml(): string
    {
        $cart = Cart::create();
        $shipping = Shipping::getCost();
        $total = $cart->getCartTotal();
        $return = '';

        $return .= '<table><thead><tr><th>';
        $return .= 'Art. Nr.</th><th>';
        $return .= 'Artikel</th><th style="text-align:right">';
        $return .= 'Anzahl</th><th style="text-align:right">';
        $return .= Warehouse::getCurrency() . '</th><th style="text-align:right">';
        $return .= Warehouse::getCurrency() . '</th></tr></head><tbody>';


        foreach ($cart->getItems() as $pos) {
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


    public static function getCustomerDataAsText(): string
    {

        $user_data = Cart::create()->getCustomerData();

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
        $return .= 'Zahlungsweise: ' . (Payment::PAYMENT_OPTIONS[$user_data['payment_type']] ?? $user_data['payment_type']) . PHP_EOL;
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

    public static function callbackCheckoutRedirect(rex_yform_manager_dataset $params): void
    {
        // Je nachdem, welche Bezahlung im Formular ausgewählt wurde, wird der Nutzer weitergeleitet
        $payment_type = $params->getValue('payment_type');
        $domain = Domain::getCurrent();
        if ($payment_type == 'paypal') {

        }

    }

    /**
     * @return array<Category>
     */
    public static function getCategoryPath(int $cat_id): array
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

    public static function restore_session_from_payment_id(string $payment_id): void
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
            ]) ?: '', [], __FILE__, __LINE__);
        }
        session_id((string) $result[0]['session_id']);
    }

    /** @api */
    public static function getConfig(string $key, mixed $default = null) :mixed
    {
        return rex_config::get('warehouse', $key, $default);
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

    /**
     * @return array<string>
     */
    public static function getEnabledFeatures(): array
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

    public static function isStockEnabled() :bool
    {
        // Überprüfe, ob 'stock' im Config-Wert vorhanden ist
        return in_array('stock', self::getEnabledFeatures());
    }
    /**
     * @api
     * @param array<string, mixed> $values
     */
    public static function parse(string $file, array $values = []): ?string
    {
        $fragment = new rex_fragment();
        $framework = Warehouse::getConfig('framework') ?: 'bootstrap5';
        $fragment_path = rex_path::addon('warehouse', 'fragments' .\DIRECTORY_SEPARATOR. 'warehouse' .\DIRECTORY_SEPARATOR. $framework  . \DIRECTORY_SEPARATOR . $file);

        $title = $values['title'] ?? '';
        $description = $values['description'] ?? '';
        
        if (file_exists($fragment_path)) {
            $fragment->setVar('title', $title);
            $fragment->setVar('description', $description, false);
            foreach ($values as $key => $value) {
                $fragment->setVar($key, $value, false);
            }
            return $fragment->parse('warehouse' .\DIRECTORY_SEPARATOR. $framework  . \DIRECTORY_SEPARATOR . $file);
        }
        return null;
    }

    public static function isDemoMode() :bool
    {
        return true;
    }
    /**
     * Gibt den globalen Modus für die Preiseingabe zurück.
     *
     * Mögliche Rückgabewerte:
     * - 'net'   für Netto-Preiseingabe
     * - 'gross' für Brutto-Preiseingabe
     *
     * @return 'net'|'gross' Modus der Preiseingabe
     */
    public static function getPriceInputMode(): string
    {
        $mode = rex_config::get('warehouse', 'price_input_mode', 'net');
        return $mode === 'gross' ? 'gross' : 'net';
    }

    /**
     * Get global cart instance
     */
    public static function getCart(): Cart
    {
        return Cart::create();
    }

    /**
     * Add item to cart
     */
    public static function addToCart(int $article_id, ?int $variant_id = null, int $amount = 1): bool
    {
        $cart = Cart::create();
        return $cart->add($article_id, $variant_id, $amount);
    }

    /**
     * Modify cart item
     */
    public static function modifyCart(int $article_id, ?int $variant_id = null, int $amount = 1, string $mode = '='): void
    {
        $cart = Cart::create();
        $cart->modify($article_id, $variant_id, $amount, $mode);
    }

    /**
     * Delete article from cart
     */
    public static function deleteArticleFromCart(int $article_id, ?int $variant_id = null): void
    {
        $cart = Cart::create();
        $cart->remove($article_id, $variant_id);
    }

    /**
     * Empty the cart
     */
    public static function emptyCart(): void
    {
        Cart::empty();
    }

}
