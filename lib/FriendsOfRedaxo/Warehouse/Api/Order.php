<?php

namespace FriendsOfRedaxo\Warehouse\Api;

use Exception;
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Order as WarehouseOrder;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;
use rex;
use rex_api_function;
use rex_response;

use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Shipping;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthManager;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use Psr\Log\LogLevel;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\AmountBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\ItemCategory;
use PaypalServerSdkLib\Models\Builders\ShippingDetailsBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingNameBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingOptionBuilder;
use PaypalServerSdkLib\Models\ShippingType;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\Builders\PaypalWalletBuilder;
use PaypalServerSdkLib\Models\Builders\PaypalWalletExperienceContextBuilder;
use PaypalServerSdkLib\Models\ShippingPreference;
use PaypalServerSdkLib\Models\PaypalExperienceLandingPage;
use PaypalServerSdkLib\Models\PaypalExperienceUserAction;
use PaypalServerSdkLib\Models\Builders\CallbackConfigurationBuilder;
use PaypalServerSdkLib\Models\Builders\PhoneNumberWithCountryCodeBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentSourceBuilder;
use PaypalServerSdkLib\Models\CallbackEvents;
use PaypalServerSdkLib\Models\OAuthToken;

class Order extends rex_api_function
{
    protected $published = true;

    public function execute(): void
    {

        rex_response::cleanOutputBuffers();

        if (rex_get('action', 'string', false) === false) {
            try {
                $response = [
                    "message" => "Warehouse API is running. If you see this, the API is working correctly - but if you see this as a consumer, something is wrong. Get in touch with the shop owner.",
                ];
                rex_response::setStatus(200);
                rex_response::sendJson($response);
            } catch (Exception $e) {
                rex_response::setStatus(500);
                rex_response::sendJson(["error" => $e->getMessage()]);
            }
        }
        if (rex_get('action', 'string') === 'order') {

            $data = Cart::loadCartFromSession();

            $cart = $data->cart;

            $cart_test = [[
                'id' => "YOUR_PRODUCT_ID",
                'quantity' => 1
                ],[
                'id' => "YOUR_PRODUCT_ID2",
                'quantity' => 3
                ]];

            try {
                $orderResponse = self::createOrder($cart_test);
                rex_response::sendJson($orderResponse["jsonResponse"]);
            } catch (Exception $e) {
                rex_response::setStatus(500);
                rex_response::sendJson(["error" => $e->getMessage()]);
            }
        }

        if (rex_get('action', 'string') === 'capture') {
            $orderID = rex_get('order_id', 'string', '');
            header("Content-Type: application/json");
            try {
                $captureResponse = self::captureOrder($orderID);
                rex_response::sendJson($captureResponse["jsonResponse"]);
            } catch (Exception $e) {
                rex_response::setStatus(500);
                rex_response::sendJson(["error" => $e->getMessage()]);
            }
        }

        exit;
    }
        
    public static function handleResponse($response)
    {
        $jsonResponse = json_decode($response->getBody(), true);
        return [
            "jsonResponse" => $jsonResponse,
            "httpStatusCode" => $response->getStatusCode(),
        ];
    }

    /**
     * Create an order to start the transaction.
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_create
     */
    public static function createOrder($cart)
    {

        global $client;
        $PAYPAL_CLIENT_ID = PayPal::getClientId();
        $PAYPAL_CLIENT_SECRET = PayPal::getClientSecret();

        $client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $PAYPAL_CLIENT_ID,
                    $PAYPAL_CLIENT_SECRET
                )
            )
            ->environment(PayPal::getEnvironment())
            ->build();

        
        // Get dynamic currency and cart totals
        $currency = Warehouse::getCurrency();
        $cart_data = Cart::loadCartFromSession();
        $cart_total = Cart::getCartTotal();
        $cart_items = $cart_data->cart->getItems();
        
        $collect =[
            "body" => OrderRequestBuilder::init("CAPTURE", [
                PurchaseUnitRequestBuilder::init(
                    AmountWithBreakdownBuilder::init($currency, number_format($cart_total, 2, '.', ''))
                        ->breakdown(
                            AmountBreakdownBuilder::init()
                                ->itemTotal(
                                    MoneyBuilder::init($currency, number_format($cart_total, 2, '.', ''))->build()
                                )
                                ->build()
                        )
                        ->build()
                )
                    // lookup item details in `cart` from database
                    ->items([
                        ItemBuilder::init(
                            "T-Shirt",
                            MoneyBuilder::init($currency, number_format($cart_total, 2, '.', ''))->build(),
                            "1"
                        )
                            ->description("Super Fresh Shirt")
                            ->sku("sku01")
                            ->build(),
                    ])

                    ->build(),
            ])
            ->build(),
        ];
    

        $getOrdersController = $client->getOrdersController();
        $apiResponse = $getOrdersController->createOrder($collect);

        return self::handleResponse($apiResponse);
    }


    /**
     * Capture payment for the created order to complete the transaction.
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     */
    public static function captureOrder($orderID)
    {
        global $client;
        $PAYPAL_CLIENT_ID = PayPal::getClientId();
        $PAYPAL_CLIENT_SECRET = PayPal::getClientSecret();

        $client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    $PAYPAL_CLIENT_ID,
                    $PAYPAL_CLIENT_SECRET
                )
            )
            ->environment(PayPal::getEnvironment())
            ->build();


        $captureBody = [
            "id" => $orderID,
        ];

        $apiResponse = $client->getOrdersController()->captureOrder($captureBody);

        // save the order to the database;
        $capture = json_decode($apiResponse->getBody(), true);
        $order = WarehouseOrder::create()
        ->setValue('paypal_id', $capture['id'])
        ->setValue('payment_id', $capture['payer']['payer_id'])
        ->setOrderJson($apiResponse->getBody())
        ->setValue('payment_status', Payment::PAYMENT_STATUS_COMPLETED)
        ->setOrderTotal($capture['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? 0)
        ->setValue('shipping_status', Shipping::SHIPPING_STATUS_NOT_SHIPPED)
        ->save();

        
        return self::handleResponse($apiResponse);
    
    }

}
