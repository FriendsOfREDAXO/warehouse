<?php

namespace FriendsOfRedaxo\Warehouse\Api;

use Exception;
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Document;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Order as WarehouseOrder;
use FriendsOfRedaxo\Warehouse\Payment;
use FriendsOfRedaxo\Warehouse\Warehouse;
use rex;
use rex_api_function;
use rex_response;
use CoreInterfaces\Core\Response\ResponseInterface;
use PaypalServerSdkLib\Http\ApiResponse;

use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Session;
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
use PaypalServerSdkLib\Models\Builders\OrderApplicationContextBuilder;
use PaypalServerSdkLib\Models\Builders\NameBuilder;
use PaypalServerSdkLib\Models\Builders\AddressBuilder;
use PaypalServerSdkLib\Models\Builders\PayerBuilder;
use PaypalServerSdkLib\Models\Builders\PhoneWithTypeBuilder;
use PaypalServerSdkLib\Models\Builders\PhoneNumberBuilder;

class Order extends rex_api_function
{
    protected $published = true;

    public function execute()
    {

        rex_response::cleanOutputBuffers();

        if (rex_get('action', 'string', false) === false) {
            try {
                $response = [
                    "message" => "Warehouse API is running. If you see this, the API is working correctly - but if you see this as a consumer, something is wrong. Get in touch with the shop owner.",
                ];
                rex_response::setStatus('200');
                rex_response::sendJson($response);
            } catch (Exception $e) {
                rex_response::setStatus('500');
                rex_response::sendJson(["error" => $e->getMessage()]);
            }
        }
        if (rex_get('action', 'string') === 'order') {

            $cart = new Cart(); // Load cart from session

            // Use real cart data instead of test data
            try {
                $orderResponse = self::createOrder();
                rex_response::sendJson($orderResponse["jsonResponse"]);
            } catch (Exception $e) {
                rex_response::setStatus('500');
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
                rex_response::setStatus('500');
                rex_response::sendJson(["error" => $e->getMessage()]);
            }
        }

        exit;
    }

    /**
     * @param ApiResponse $response
     * @return array{jsonResponse: mixed, httpStatusCode: mixed}
     */
    public static function handleResponse(ApiResponse $response): array
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
     * @return array{jsonResponse: mixed, httpStatusCode: mixed}
     */
    public static function createOrder(): array
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
        $cart = new Cart(); // Load cart from session

        // Validate cart is not empty
        if ($cart->isEmpty()) {
            throw new Exception('Cart is empty - cannot create PayPal order');
        }

        $cart_total = Cart::getCartTotal(); // Total including shipping
        $cart_subtotal = Cart::getSubTotal(); // Items only, excluding shipping
        $shipping_cost = Shipping::getCost(); // Shipping costs
        $cart_items = $cart->getItems(); // Get items from Cart object

        // Build PayPal items from cart
        $paypal_items = [];
        foreach ($cart_items as $item) {
            $itemBuilder = ItemBuilder::init(
                $item['name'],
                MoneyBuilder::init($currency, number_format($item['price'], 2, '.', ''))->build(),
                (string) $item['amount']
            )
                ->description($item['name'])
                ->sku($item['sku'] ?? ($item['article_id'] . ($item['variant_id'] ? '-' . $item['variant_id'] : '')));

            // Add image URL if setting is enabled and image is available
            if (PayPal::shouldIncludeImages() && !empty($item['image'])) {
                $itemBuilder->imageUrl($item['image']);
            }

            $paypal_items[] = $itemBuilder->build();
        }

        $breakdown = AmountBreakdownBuilder::init()
            ->itemTotal(
                MoneyBuilder::init($currency, number_format($cart_subtotal, 2, '.', ''))->build()
            );

        // Add shipping if there are shipping costs
        if ($shipping_cost > 0) {
            $breakdown->shipping(
                MoneyBuilder::init($currency, number_format($shipping_cost, 2, '.', ''))->build()
            );
        }

        // Get customer data and addresses from session
        $customer = Session::getCustomerData();
        $shippingAddress = Session::getShippingAddressData();
        $billingAddress = Session::getBillingAddressData();

        // Build shipping details - use shipping address if provided, otherwise use billing address
        $shipping = null;
        $addressToUse = !empty($shippingAddress) ? $shippingAddress : $billingAddress;

        if (!empty($addressToUse)) {
            // Try to get name from the specific address first, then fall back to customer data
            $shippingName = ($addressToUse['firstname'] ?? '') . ' ' . ($addressToUse['lastname'] ?? '');
            if (empty(trim($shippingName))) {
                $shippingName = ($customer['firstname'] ?? '') . ' ' . ($customer['lastname'] ?? '');
            }

            // Split full name into given name and surname because the SDK's NameBuilder
            // provides givenName() and surname() methods (no fullName()).
            $trimmedName = trim($shippingName);

            // ShippingDetailsBuilder expects a ShippingName object. Use the
            // ShippingNameBuilder which provides a fullName() setter.
            $shipping = ShippingDetailsBuilder::init()
                ->name(\PaypalServerSdkLib\Models\Builders\ShippingNameBuilder::init()->fullName($trimmedName)->build())
                ->address(
                    AddressBuilder::init($addressToUse['country'] ?? PayPal::getStoreCountryCode())
                        ->addressLine1($addressToUse['address'] ?? '')
                        ->adminArea2($addressToUse['city'] ?? '') // Stadt
                        ->postalCode($addressToUse['zip'] ?? '')
                        ->build()
                )
                ->build();
        }

        // Build payer information from customer data
        $payer = null;
        if (!empty($customer)) {
            $payerBuilder = PayerBuilder::init();

            // Add email address
            if (!empty($customer['email'])) {
                $payerBuilder->emailAddress($customer['email']);
            }

            // Add name information
            $firstName = $customer['firstname'] ?? '';
            $lastName = $customer['lastname'] ?? '';
            if (!empty($firstName) || !empty($lastName)) {
                $nameBuilder = NameBuilder::init();
                if (!empty($firstName)) {
                    $nameBuilder->givenName($firstName);
                }
                if (!empty($lastName)) {
                    $nameBuilder->surname($lastName);
                }
                $payerBuilder->name($nameBuilder->build());
            }

            // Add phone information if available
            if (!empty($customer['phone'])) {
                $phoneNumber = PhoneNumberBuilder::init($customer['phone'])->build();
                $phoneWithType = PhoneWithTypeBuilder::init($phoneNumber)->build();
                $payerBuilder->phone($phoneWithType);
            }

            $payer = $payerBuilder->build();
        }

        // Create application context with return URLs
        $return_url = PayPal::getSuccessPageUrl() ?: '';
        $cancel_url = PayPal::getErrorPageUrl() ?: '';

        $applicationContext = OrderApplicationContextBuilder::init()
            ->brandName(PayPal::getStoreName() ?: 'Shop')
            ->locale('de-DE')
            ->userAction(PaypalExperienceUserAction::PAY_NOW)
            ->landingPage(PaypalExperienceLandingPage::LOGIN)
            ->shippingPreference($shipping ? ShippingPreference::SET_PROVIDED_ADDRESS : ShippingPreference::NO_SHIPPING)
            ->returnUrl($return_url)
            ->cancelUrl($cancel_url)
            ->build();

        $orderRequestBuilder = OrderRequestBuilder::init("CAPTURE", [
            PurchaseUnitRequestBuilder::init(
                AmountWithBreakdownBuilder::init($currency, number_format($cart_total, 2, '.', ''))
                    ->breakdown($breakdown->build())
                    ->build()
            )
                ->items($paypal_items)
                ->customId(PayPal::getStoreName() . '-' . date('Y-m-d-H-i-s'))
                ->description('Order from ' . PayPal::getStoreName())
                ->shipping($shipping) // Add shipping details
                ->build(),
        ])
            ->applicationContext($applicationContext); // Add application context

        // Add payer information if available
        if ($payer !== null) {
            $orderRequestBuilder->payer($payer);
        }

        $collect = [
            "body" => $orderRequestBuilder->build(),
        ];


        $getOrdersController = $client->getOrdersController();
        $apiResponse = $getOrdersController->createOrder($collect);

        return self::handleResponse($apiResponse);
    }


    /**
     * Capture payment for the created order to complete the transaction.
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     * @param string $orderID
     * @return array{jsonResponse: mixed, httpStatusCode: mixed}
     */
    public static function captureOrder($orderID): array
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

        // Save the order to the database using Session data
        $capture = json_decode($apiResponse->getBody(), true);
        $payment_id = $capture['payer']['payer_id'] ?? $orderID;

        // Use Session::saveAsOrder to properly save with customer data, addresses, and cart items
        $saved = Session::saveAsOrder($payment_id);

        if ($saved) {
            // Update the saved order with PayPal specific data
            $order = WarehouseOrder::query()
                ->where('payment_id', $payment_id)
                ->orderBy('id', 'DESC')
                ->findOne();

            if ($order) {
                $order->setValue('paypal_id', $capture['id'] ?? '')
                    ->setOrderJson($apiResponse->getBody())
                    ->setValue('payment_status', Payment::PAYMENT_STATUS_COMPLETED)
                    ->setOrderTotal($capture['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? 0)
                    ->setValue('shipping_status', Shipping::SHIPPING_STATUS_NOT_SHIPPED);

                $order->save();
            } else {
                throw new Exception('Failed to find saved order after Session::saveAsOrder');
            }
        } else {
            throw new Exception('Failed to save order via Session::saveAsOrder');
        }


        return self::handleResponse($apiResponse);
    }
}
