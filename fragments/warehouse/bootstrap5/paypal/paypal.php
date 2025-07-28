<?php
/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Warehouse;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Exceptions\ApiException;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\AmountBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\Builders\PayeeBaseBuilder;
use PaypalServerSdkLib\Models\Builders\AddressBuilder;
use PaypalServerSdkLib\Models\Builders\PayerBuilder;
use PaypalServerSdkLib\Models\Builders\NameBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingDetailsBuilder;

// Step 1: Initialize the PayPal Client with proper error handling
function createPayPalClient()
{
    try {
        $client = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    PayPal::getClientId(),
                    PayPal::getClientSecret()
                )
            )
            ->environment(Environment::SANDBOX) // Use SANDBOX for testing
            ->build();
            
        // Test the credentials by trying to get an OAuth token
        $oauthController = $client->getOAuthAuthorizationController();
        echo "✅ PayPal client initialized successfully\n";
        
        return $client;
        
    } catch (Exception $e) {
        echo "❌ Failed to initialize PayPal client: " . $e->getMessage() . "\n";
        echo "Please check your PayPal credentials.\n";
        echo "Get your credentials from: https://developer.paypal.com/\n";
        return null;
    }
}

// Step 2: Create an Order
function createOrder($client)
{
    try {
        // Define items for the order
        $items = [
            ItemBuilder::init(
                "Premium T-Shirt",
                MoneyBuilder::init("USD", "25.00")->build(),
                "2"
            )
                ->description("High quality cotton t-shirt")
                ->sku("TSHIRT-001")
                ->build(),
            
            ItemBuilder::init(
                "Shipping",
                MoneyBuilder::init("USD", "5.00")->build(),
                "1"
            )
                ->description("Standard shipping")
                ->sku("SHIP-STD")
                ->build()
        ];

        // Calculate totals
        $itemTotal = "55.00"; // (25.00 * 2) + 5.00
        $totalAmount = "55.00";

        // Create payee information
        $payee = PayeeBaseBuilder::init()
            ->emailAddress("merchant@example.com")
            ->build();

        // Create shipping address
        $shippingAddress = AddressBuilder::init("DE")
            ->addressLine1("123 Main Street")
            ->addressLine2("Apt 4B")
            ->adminArea2("San Francisco")
            ->adminArea1("CA")
            ->postalCode("94107")
            ->build();

        // Create shipping details
        $shippingDetails = ShippingDetailsBuilder::init()
            ->address($shippingAddress)
            ->build();

        // Create purchase unit
        $purchaseUnit = PurchaseUnitRequestBuilder::init(
            AmountWithBreakdownBuilder::init("USD", $totalAmount)
                ->breakdown(
                    AmountBreakdownBuilder::init()
                        ->itemTotal(MoneyBuilder::init("USD", $itemTotal)->build())
                        ->build()
                )
                ->build()
        )
            ->items($items)
            ->payee($payee)
            ->description("Order for premium merchandise")
            ->customId("CUSTOM-ORDER-2024-001")
            ->invoiceId("INV-001")
            ->shipping($shippingDetails)
            ->build();

        // Create the order request
        $orderRequest = OrderRequestBuilder::init(
            "CAPTURE",
            [$purchaseUnit]
        )->build();

        echo "🔄 Creating PayPal order...\n";

        // Make the API call
        $response = $client->getOrdersController()->createOrder([
            'body' => $orderRequest,
            'prefer' => 'return=representation'
        ]);

        $orderData = json_decode($response->getBody(), true);
        
        echo "✅ Order created successfully!\n";
        echo "Order ID: " . $orderData['id'] . "\n";
        echo "Status: " . $orderData['status'] . "\n";
        
        // Find the approval URL
        foreach ($orderData['links'] as $link) {
            if ($link['rel'] === 'approve') {
                echo "Approval URL: " . $link['href'] . "\n";
                break;
            }
        }
        
        return $orderData;
        
    } catch (ApiException $e) {
        echo "❌ PayPal API Error: " . $e->getMessage() . "\n";
        echo "Response Code: " . $e->getResponseCode() . "\n";
        echo "Response Body: " . $e->getResponseBody() . "\n";
        return null;
    } catch (Exception $e) {
        echo "❌ General Error: " . $e->getMessage() . "\n";
        return null;
    }
}

// Step 3: Main execution
echo "🚀 Starting PayPal integration...\n\n";


// Initialize client
$client = createPayPalClient();

if ($client) {
    // Create order
    $order = createOrder($client);
    
    if ($order) {
        echo "\n✅ PayPal integration completed successfully!\n";
        echo "Next steps:\n";
        echo "1. Redirect user to the approval URL above\n";
        echo "2. After user approves, capture the payment using order ID: " . $order['id'] . "\n";
    }
} else {
    echo "❌ PayPal integration failed. Please check your credentials.\n";
}
/*
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Logger;
use FriendsOfRedaxo\Warehouse\Order;
use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Warehouse;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\Money;

$domain = Domain::getCurrent();

// 1. PayPal-Client initialisieren
$client = PayPal::createClient();

// 2. Warenkorb-Inhalt definieren
$cart = Cart::loadCartFromSession();
$items = [];
foreach ($cart->getItems() as $uuid => $cartItem) {
    if (isset($cartItem['variants']) && is_array($cartItem['variants'])) {
        foreach ($cartItem['variants'] as $variant) {
            $items[] = new Item(
                $variant['name'],
                new Money('EUR', number_format($variant['price'], 2, '.', '')),
                $variant['amount'] ?? 1
            );
        }
    } else {
        $items[] = new Item(
            $cartItem['name'],
            new Money('EUR', number_format($cartItem['price'], 2, '.', '')),
            $cartItem['amount'] ?? 1
        );
    }
}

dd($client);

if (rex::isFrontend()) {

}
*/
