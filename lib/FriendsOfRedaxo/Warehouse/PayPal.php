<?php

namespace FriendsOfRedaxo\Warehouse;

use rex;
use rex_config;
use rex_response;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\HttpException;
use PayPalCheckoutSdk\Core\PayPalEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalHttp\HttpException as PayPalHttpHttpException;

class PayPal
{

    const MODE_SANDBOX = 'sandbox';
    const MODE_LIVE = 'live';

    // https://developer.paypal.com/api/rest/reference/currency-codes/
    const CURRENCY_CODES =
        [
        "AUD" => "Australian dollar",
        "BRL" => "Brazilian real 2",
        "CAD" => "Canadian dollar",
        "CNY" => "Chinese Renmenbi 3",
        "CZK" => "Czech koruna",
        "DKK" => "Danish krone",
        "EUR" => "Euro",
        "HKD" => "Hong Kong dollar",
        "HUF" => "Hungarian forint 1",
        "ILS" => "Israeli new shekel",
        "JPY" => "Japanese yen 1",
        "MYR" => "Malaysian ringgit 3",
        "MXN" => "Mexican peso",
        "TWD" => "New Taiwan dollar 1",
        "NZD" => "New Zealand dollar",
        "NOK" => "Norwegian krone",
        "PHP" => "Philippine peso",
        "PLN" => "Polish złoty",
        "GBP" => "Pound sterling",
        "RUB" => "Russian ruble",
        "SGD" => "Singapore dollar",
        "SEK" => "Swedish krona",
        "CHF" => "Swiss franc",
        "THB" => "Thai baht",
        "USD" => "United States dollar"
        ];


    // https://developer.paypal.com/api/rest/reference/country-codes/
    public const COUNTRY_CODES =
        [
            "AL" => "ALBANIA",
            "DZ" => "ALGERIA",
            "AD" => "ANDORRA",
            "AO" => "ANGOLA",
            "AI" => "ANGUILLA",
            "AG" => "ANTIGUA & BARBUDA",
            "AR" => "ARGENTINA",
            "AM" => "ARMENIA",
            "AW" => "ARUBA",
            "AU" => "AUSTRALIA",
            "AT" => "AUSTRIA",
            "AZ" => "AZERBAIJAN",
            "BS" => "BAHAMAS",
            "BH" => "BAHRAIN",
            "BB" => "BARBADOS",
            "BY" => "BELARUS",
            "BE" => "BELGIUM",
            "BZ" => "BELIZE",
            "BJ" => "BENIN",
            "BM" => "BERMUDA",
            "BT" => "BHUTAN",
            "BO" => "BOLIVIA",
            "BA" => "BOSNIA & HERZEGOVINA",
            "BW" => "BOTSWANA",
            "BR" => "BRAZIL",
            "VG" => "BRITISH VIRGIN ISLANDS",
            "BN" => "BRUNEI",
            "BG" => "BULGARIA",
            "BF" => "BURKINA FASO",
            "BI" => "BURUNDI",
            "KH" => "CAMBODIA",
            "CM" => "CAMEROON",
            "CA" => "CANADA",
            "CV" => "CAPE VERDE",
            "KY" => "CAYMAN ISLANDS",
            "TD" => "CHAD",
            "CL" => "CHILE",
            "C2" => "CHINA",
            "CO" => "COLOMBIA",
            "KM" => "COMOROS",
            "CG" => "CONGO - BRAZZAVILLE",
            "CD" => "CONGO - KINSHASA",
            "CK" => "COOK ISLANDS",
            "CR" => "COSTA RICA",
            "CI" => "CÔTE D’IVOIRE",
            "HR" => "CROATIA",
            "CY" => "CYPRUS",
            "CZ" => "CZECH REPUBLIC",
            "DK" => "DENMARK",
            "DJ" => "DJIBOUTI",
            "DM" => "DOMINICA",
            "DO" => "DOMINICAN REPUBLIC",
            "EC" => "ECUADOR",
            "EG" => "EGYPT",
            "SV" => "EL SALVADOR",
            "ER" => "ERITREA",
            "EE" => "ESTONIA",
            "ET" => "ETHIOPIA",
            "FK" => "FALKLAND ISLANDS",
            "FO" => "FAROE ISLANDS",
            "FJ" => "FIJI",
            "FI" => "FINLAND",
            "FR" => "FRANCE",
            "GF" => "FRENCH GUIANA",
            "PF" => "FRENCH POLYNESIA",
            "GA" => "GABON",
            "GM" => "GAMBIA",
            "GE" => "GEORGIA",
            "DE" => "GERMANY",
            "GI" => "GIBRALTAR",
            "GR" => "GREECE",
            "GL" => "GREENLAND",
            "GD" => "GRENADA",
            "GP" => "GUADELOUPE",
            "GT" => "GUATEMALA",
            "GN" => "GUINEA",
            "GW" => "GUINEA-BISSAU",
            "GY" => "GUYANA",
            "HN" => "HONDURAS",
            "HK" => "HONG KONG SAR CHINA",
            "HU" => "HUNGARY",
            "IS" => "ICELAND",
            "IN" => "INDIA",
            "ID" => "INDONESIA",
            "IE" => "IRELAND",
            "IL" => "ISRAEL",
            "IT" => "ITALY",
            "JM" => "JAMAICA",
            "JP" => "JAPAN",
            "JO" => "JORDAN",
            "KZ" => "KAZAKHSTAN",
            "KE" => "KENYA",
            "KI" => "KIRIBATI",
            "KW" => "KUWAIT",
            "KG" => "KYRGYZSTAN",
            "LA" => "LAOS",
            "LV" => "LATVIA",
            "LS" => "LESOTHO",
            "LI" => "LIECHTENSTEIN",
            "LT" => "LITHUANIA",
            "LU" => "LUXEMBOURG",
            "MK" => "MACEDONIA",
            "MG" => "MADAGASCAR",
            "MW" => "MALAWI",
            "MY" => "MALAYSIA",
            "MV" => "MALDIVES",
            "ML" => "MALI",
            "MT" => "MALTA",
            "MH" => "MARSHALL ISLANDS",
            "MQ" => "MARTINIQUE",
            "MR" => "MAURITANIA",
            "MU" => "MAURITIUS",
            "YT" => "MAYOTTE",
            "MX" => "MEXICO",
            "FM" => "MICRONESIA",
            "MD" => "MOLDOVA",
            "MC" => "MONACO",
            "MN" => "MONGOLIA",
            "ME" => "MONTENEGRO",
            "MS" => "MONTSERRAT",
            "MA" => "MOROCCO",
            "MZ" => "MOZAMBIQUE",
            "NA" => "NAMIBIA",
            "NR" => "NAURU",
            "NP" => "NEPAL",
            "NL" => "NETHERLANDS",
            "NC" => "NEW CALEDONIA",
            "NZ" => "NEW ZEALAND",
            "NI" => "NICARAGUA",
            "NE" => "NIGER",
            "NG" => "NIGERIA",
            "NU" => "NIUE",
            "NF" => "NORFOLK ISLAND",
            "NO" => "NORWAY",
            "OM" => "OMAN",
            "PW" => "PALAU",
            "PA" => "PANAMA",
            "PG" => "PAPUA NEW GUINEA",
            "PY" => "PARAGUAY",
            "PE" => "PERU",
            "PH" => "PHILIPPINES",
            "PN" => "PITCAIRN ISLANDS",
            "PL" => "POLAND",
            "PT" => "PORTUGAL",
            "QA" => "QATAR",
            "RE" => "RÉUNION",
            "RO" => "ROMANIA",
            "RU" => "RUSSIA",
            "RW" => "RWANDA",
            "WS" => "SAMOA",
            "SM" => "SAN MARINO",
            "ST" => "SÃO TOMÉ & PRÍNCIPE",
            "SA" => "SAUDI ARABIA",
            "SN" => "SENEGAL",
            "RS" => "SERBIA",
            "SC" => "SEYCHELLES",
            "SL" => "SIERRA LEONE",
            "SG" => "SINGAPORE",
            "SK" => "SLOVAKIA",
            "SI" => "SLOVENIA",
            "SB" => "SOLOMON ISLANDS",
            "SO" => "SOMALIA",
            "ZA" => "SOUTH AFRICA",
            "KR" => "SOUTH KOREA",
            "ES" => "SPAIN",
            "LK" => "SRI LANKA",
            "SH" => "ST. HELENA",
            "KN" => "ST. KITTS & NEVIS",
            "LC" => "ST. LUCIA",
            "PM" => "ST. PIERRE & MIQUELON",
            "VC" => "ST. VINCENT & GRENADINES",
            "SR" => "SURINAME",
            "SJ" => "SVALBARD & JAN MAYEN",
            "SZ" => "SWAZILAND",
            "SE" => "SWEDEN",
            "CH" => "SWITZERLAND",
            "TW" => "TAIWAN",
            "TJ" => "TAJIKISTAN",
            "TZ" => "TANZANIA",
            "TH" => "THAILAND",
            "TG" => "TOGO",
            "TO" => "TONGA",
            "TT" => "TRINIDAD & TOBAGO",
            "TN" => "TUNISIA",
            "TM" => "TURKMENISTAN",
            "TC" => "TURKS & CAICOS ISLANDS",
            "TV" => "TUVALU",
            "UG" => "UGANDA",
            "UA" => "UKRAINE",
            "AE" => "UNITED ARAB EMIRATES",
            "GB" => "UNITED KINGDOM",
            "US" => "UNITED STATES",
            "UY" => "URUGUAY",
            "VU" => "VANUATU",
            "VA" => "VATICAN",
            "VE" => "VENEZUELA",
            "VN" => "VIETNAM",
            "WF" => "WALLIS & FUTUNA",
            "YE" => "YEMEN",
            "ZM" => "ZAMBIA",
            "ZW" => "ZIMBABWE",
        ];

    const CURRENCY_SIGNS =
        [
            "AUD" => "$",
            "BRL" => "R$",
            "CAD" => "$",
            "CNY" => "¥",
            "CZK" => "Kč",
            "DKK" => "kr.",
            "EUR" => "€",
            "HKD" => "$",
            "HUF" => "Ft",
            "ILS" => "₪",
            "JPY" => "¥",
            "MYR" => "$",
            "MXN" => "$",
            "TWD" => "$",
            "NZD" => "$",
            "NOK" => "kr.",
            "PHP" => "₱",
            "PLN" => "zł",
            "GBP" => "£",
            "RUB" => '₽',
            'SGD' => '$',
            'SEK' => 'kr',
            'CHF' => 'CHF',
            'THB' => '฿',
            'USD' => '$'
        ];

    /**
     * Returns PayPal HTTP client instance with environment which has access
     * credentials context. This can be used invoke PayPal API's provided the
     * credentials have the access to do so.
     */
    public static function getClient() : PayPalHttpClient
    {
        return new PayPalHttpClient(self::initEnviroment());
    }
    /**
     * Setting up and Returns PayPal SDK environment with PayPal Access credentials.
     * For demo purpose, we are using SandboxEnvironment. In production this will be
     * ProductionEnvironment.
     */
    public static function initEnviroment() :PayPalEnvironment
    {
        $clientId = getenv("CLIENT_ID") ?: Warehouse::getPaypalClientId();
        $clientSecret = getenv("CLIENT_SECRET") ?: Warehouse::getPaypalSecret();

        if (rex_config::get('warehouse', 'sandboxmode')) {
            return new SandboxEnvironment($clientId, $clientSecret);
        } else {
            return new ProductionEnvironment($clientId, $clientSecret);
        }
    }

    public static function createOrder()
    {
        $client = self::getClient();
        $request = new OrdersCreateRequest();
        $params = json_decode(rex_config::get('warehouse', 'paypal_getparams'), true);
        $return_url = trim(rex::getServer(), '/') . rex_getUrl(rex_config::get('warehouse', 'paypal_page_success'), '', $params ?? [], '&');
        $cancel_url = trim(rex::getServer(), '/') . rex_getUrl(rex_config::get('warehouse', 'paypal_page_error'));
        $cart = Warehouse::getCart();
        $user_data = Warehouse::getCustomerData();

        $user_data['to_firstname'] = $user_data['to_firstname'] ?: $user_data['firstname'] ?? '';
        $user_data['to_lastname'] = $user_data['to_lastname'] ?: $user_data['lastname'] ?? '';
        $user_data['to_address'] = $user_data['to_address'] ?: $user_data['address'] ?? '';
        $user_data['to_department'] = $user_data['to_department'] ?: $user_data['department'] ?? '';
        $user_data['to_country'] = $user_data['to_country'] ?: $user_data['country'] ?? '';
        $user_data['to_zip'] = $user_data['to_zip'] ?: $user_data['zip'] ?? '';
        $user_data['to_city'] = $user_data['to_city'] ?: $user_data['city'] ?? '';

        $items = [];
        foreach ($cart as $position) {
            $items[] = [
                'name' => $position['name'],
                'description' => $position['cat_name'],
                'sku' => $position['whid'],
                'unit_amount' => [
                    'currency_code' => Warehouse::getCurrencySign(),
                    'value' => number_format($position['price_netto'], 2), // netto
                ],
                'tax' => [
                    'currency_code' => Warehouse::getCurrencySign(),
                    'value' => number_format($position['taxsingle'], 2),
                ],
                'quantity' => $position['amount'],
                'category' => 'PHYSICAL_GOODS', // DIGITAL_GOODS oder PHYSICAL_GOODS
            ];
        }

        // https://developer.paypal.com/docs/checkout/reference/server-integration/set-up-transaction/
        $purchase_units = [
            [
                'reference_id' => 'none',
                'description' => rex_config::get("warehouse", "store_name"),
                'custom_id' => $user_data['to_firstname'].' '.$user_data['to_lastname'],
                'soft_descriptor' => 'Webshop',
                'amount' =>
                [
                    'currency_code' => Warehouse::getCurrencySign(),
                    'value' => number_format(Warehouse::getCartTotal(), 2),
                    'breakdown' =>
                    [
                        'item_total' => [
                            'currency_code' => Warehouse::getCurrencySign(),
                            'value' => number_format(Warehouse::getSubTotalNetto(), 2),
                        ],
                        'shipping' => [
                            'currency_code' => Warehouse::getCurrencySign(),
                            'value' => number_format(Shipping::getCost(), 2),
                        ],
                        /*
                        'handling' =>
                        [
                            'currency_code' => Warehouse::getCurrencySign(),
                            'value' => '0.00',
                        ],
                        */
                        'tax_total' => [
                            'currency_code' => Warehouse::getCurrencySign(),
                            'value' => number_format(Warehouse::getTaxTotal(), 2),
                        ],
                        'shipping_discount' => [
                            'currency_code' => Warehouse::getCurrencySign(),
                            'value' => number_format(Warehouse::getDiscountValue(), 2),
                        ],
                    ],
                ],
                'items' => $items,
                'shipping' => [
                    'method' => 'Versandweg',
                    'address' =>
                    [
                        'address_line_1' => $user_data['to_address'] ,
                        'address_line_2' => $user_data['to_department'],
                        'admin_area_2' => $user_data['to_city'],
                        'admin_area_1' => '',
                        'postal_code' => $user_data['to_zip'],
                        'country_code' => $user_data['to_country'],
                    ],
                ],
            ],
        ];

        //        dump($purchase_units); exit;

        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",

            "purchase_units" => $purchase_units,
            "application_context" => [
                'brand_name' => rex_config::get("warehouse", "store_name"),
                'locale' => rex_config::get("warehouse", "store_country_code"),
                'landing_page' => 'BILLING',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'user_action' => 'PAY_NOW',
                "cancel_url" => $cancel_url,
                "return_url" => $return_url
            ]
        ];
        try {
            $response = $client->execute($request);
            Warehouse::saveCartAsOrder($response['result']->id);
            rex_set_session('pp_order_id', $response['result']->id);
            foreach ($response['result']->links as $link) {
                if ($link->rel == 'approve') {
                    rex_response::sendRedirect($link->href);
                }
            }
        } catch (PayPalHttpHttpException $ex) {
        }
        return $response;
    }
    public static function ExecutePayment()
    {
        $env = rex_config::get('warehouse', 'sandboxmode') ? self::MODE_SANDBOX : self::MODE_LIVE;
        $client_id = Warehouse::getPaypalClientId();
        $paypal_secret = Warehouse::getPaypalSecret();
        $client = self::getClient();
        // $response->result->id gives the orderId of the order created above
        $order_id = rex_session('pp_order_id');
        $request = new OrdersCaptureRequest($order_id);
        $request->prefer('return=representation');
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($request);
            return $response;
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            //            print_r($response);
        } catch (PayPalHttpHttpException $ex) {
            echo $ex->statusCode;
            print_r($ex->getMessage());
        }
        return;
    }
}
