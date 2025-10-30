<?php

namespace FriendsOfRedaxo\Warehouse;

use rex;
use rex_config;
use rex_response;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\ShippingAddress;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use Psr\Log\LogLevel;

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

    public static function getClientId(): string
    {
        if (rex_config::get('warehouse', 'sandboxmode')) {
            return rex_config::get('warehouse', 'paypal_sandbox_client_id');
        }
        return rex_config::get('warehouse', 'paypal_client_id');
    }

    public static function getClientSecret(): string
    {
        if (rex_config::get('warehouse', 'sandboxmode')) {
            return rex_config::get('warehouse', 'paypal_sandbox_secret');
        }
        return rex_config::get('warehouse', 'paypal_secret');
    }

    public static function createClient(): \PaypalServerSdkLib\PaypalServerSdkClient
    {
        $client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    self::getClientId(),
                    self::getClientSecret()
                )
            )
            ->environment(Environment::SANDBOX)
            ->loggingConfiguration(
                LoggingConfigurationBuilder::init()
                    ->level(LogLevel::INFO)
                    ->requestConfiguration(RequestLoggingConfigurationBuilder::init()->body(true))
                    ->responseConfiguration(ResponseLoggingConfigurationBuilder::init()->headers(true))
            )
            ->build();

        return $client;
    }

    public static function createOrder(): void
    {
    }

    public static function getMode(): string
    {
        return rex_config::get('warehouse', 'sandboxmode', self::MODE_SANDBOX) ? self::MODE_SANDBOX : self::MODE_LIVE;
    }

    public static function getEnvironment(): string
    {
        return self::getMode() === self::MODE_LIVE ? Environment::PRODUCTION : Environment::SANDBOX;
    }

    public static function getStoreName(): string
    {
        return rex_config::get('warehouse', 'store_name', '');
    }

    public static function getStoreCountryCode(): string
    {
        return rex_config::get('warehouse', 'store_country_code', 'DE');
    }

    public static function getStyleConfig(): string
    {
        $style = json_encode([
            'shape' => rex_config::get('warehouse', 'paypal_button_shape', 'rect'),
            'size' => rex_config::get('warehouse', 'paypal_button_size', 'responsive'),
            'color' => rex_config::get('warehouse', 'paypal_button_color', 'gold'),
            'label' => rex_config::get('warehouse', 'paypal_button_label', 'paypal'),
            'layout' => rex_config::get('warehouse', 'paypal_button_layout', 'horizontal'),
            'height' => rex_config::get('warehouse', 'paypal_button_height', ''),
            'fundingSource' => rex_config::get('warehouse', 'paypal_button_funding_source', 'paypal')
        ]);
        return $style !== false ? $style : '{}';
    }

    public static function getErrorPageUrl(): string
    {
        $domain = Domain::getCurrent();
        return $domain ? $domain->getThankyouArtUrl() : '';
    }

    public static function getSuccessPageUrl(): string
    {
        $domain = Domain::getCurrent();
        return $domain ? $domain->getThankyouArtUrl() : '';
    }

    public static function shouldIncludeImages(): bool
    {
        return (bool) rex_config::get('warehouse', 'paypal_include_images', false);
    }
}
