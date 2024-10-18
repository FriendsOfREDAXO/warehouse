<?php

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\HttpException;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

if (rex::isDebugMode()) {
    ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}


class warehouse_paypal
{
    /**
     * Returns PayPal HTTP client instance with environment which has access
     * credentials context. This can be used invoke PayPal API's provided the
     * credentials have the access to do so.
     */
    public static function client()
    {
        return new PayPalHttpClient(self::environment());
    }
    /**
     * Setting up and Returns PayPal SDK environment with PayPal Access credentials.
     * For demo purpose, we are using SandboxEnvironment. In production this will be
     * ProductionEnvironment.
     */
    public static function environment()
    {
        $clientId = getenv("CLIENT_ID") ?: warehouse::get_paypal_client_id();
        $clientSecret = getenv("CLIENT_SECRET") ?: warehouse::get_paypal_secret();

        if (rex_config::get('warehouse', 'sandboxmode')) {
            return new SandboxEnvironment($clientId, $clientSecret);
        } else {
            return new ProductionEnvironment($clientId, $clientSecret);
        }
    }

    public static function create_order()
    {
        $client = self::client();
        $request = new OrdersCreateRequest();
        $params = json_decode(rex_config::get('warehouse', 'paypal_getparams'), true);
        $return_url = trim(rex::getServer(), '/') . rex_getUrl(rex_config::get('warehouse', 'paypal_page_success'), '', $params ?? [], '&');
        $cancel_url = trim(rex::getServer(), '/') . rex_getUrl(rex_config::get('warehouse', 'paypal_page_error'));
        $cart = warehouse::get_cart();
        $user_data = warehouse::get_user_data();

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
                    'currency_code' => rex_config::get('warehouse', 'currency'),
                    'value' => number_format($position['price_netto'],2), // netto
                ],
                'tax' => [
                    'currency_code' => rex_config::get('warehouse', 'currency'),
                    'value' => number_format($position['taxsingle'], 2),
                ],
                'quantity' => $position['count'],
                'category' => 'PHYSICAL_GOODS', // DIGITAL_GOODS oder PHYSICAL_GOODS
            ];
        }

        // https://developer.paypal.com/docs/checkout/reference/server-integration/set-up-transaction/
        $purchase_units = [
            [
                'reference_id' => 'none',
                'description' => rex_config::get("warehouse","store_name"),
                'custom_id' => $user_data['to_firstname'].' '.$user_data['to_lastname'],
                'soft_descriptor' => 'Webshop',
                'amount' =>
                [
                    'currency_code' => rex_config::get('warehouse', 'currency'),
                    'value' => number_format(warehouse::get_cart_total(), 2),
                    'breakdown' =>
                    [
                        'item_total' => [
                            'currency_code' => rex_config::get('warehouse', 'currency'),
                            'value' => number_format(warehouse::get_sub_total_netto(), 2),
                        ],
                        'shipping' => [
                            'currency_code' => rex_config::get('warehouse', 'currency'),
                            'value' => number_format((float) warehouse::get_shipping_cost(), 2),
                        ],
                        /*
                        'handling' =>
                        [
                            'currency_code' => rex_config::get('warehouse', 'currency'),
                            'value' => '0.00',
                        ],
                        */
                        'tax_total' => [
                            'currency_code' => rex_config::get('warehouse', 'currency'),
                            'value' => number_format(warehouse::get_tax_total(), 2),
                        ],
                        'shipping_discount' => [
                            'currency_code' => rex_config::get('warehouse', 'currency'),
                            'value' => number_format(warehouse::get_discount_value(), 2),
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
                'brand_name' => rex_config::get("warehouse","store_name"),
                'locale' => rex_config::get("warehouse","store_country_code"),
                'landing_page' => 'BILLING',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'user_action' => 'PAY_NOW',
                "cancel_url" => $cancel_url,
                "return_url" => $return_url
            ]
        ];
        try {
//            dump($request); exit;
            $response = $client->execute($request);
            warehouse::save_order_to_db($response->result->id);
            rex_set_session('pp_order_id', $response->result->id);
            foreach ($response->result->links as $link) {
                if ($link->rel == 'approve') {
                    rex_response::sendRedirect($link->href);
                }
            }
        } catch (HttpException $ex) {
        }
        return $response;
    }
    static function execute_payment()
    {
        $env = rex_config::get('warehouse', 'sandboxmode') ? 'sandbox' : 'live';
        $client_id = warehouse::get_paypal_client_id();
        $paypal_secret = warehouse::get_paypal_secret();
        $client = self::client();
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
        } catch (HttpException $ex) {
            echo $ex->statusCode;
            print_r($ex->getMessage());
        }
        return;
    }
}
