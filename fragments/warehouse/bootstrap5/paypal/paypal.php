<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Logger;
use FriendsOfRedaxo\Warehouse\Order;
use FriendsOfRedaxo\Warehouse\Payment as WarehousePayment;
use FriendsOfRedaxo\Warehouse\PayPal as WarehousePayPal;
use FriendsOfRedaxo\Warehouse\Warehouse;

// Check if PayPal classes are available (should be loaded in boot.php)
if (!class_exists('PayPal\Rest\ApiContext')) {
    Logger::log('paypal_error', 'PayPal SDK classes not available - make sure composer install was run in the warehouse addon');
    echo "<h2>Fehler bei der PayPal-Zahlung</h2>";
    echo "<p>Die PayPal SDK ist nicht korrekt installiert. Bitte führen Sie 'composer install' im warehouse-Addon Verzeichnis aus.</p>";
    return;
}

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

$domain = Domain::getCurrent();
$cart = Cart::loadCartFromSession();

/* cart Beispiel-Array
FriendsOfRedaxo\Warehouse\Cart {#195 ▼
    +cart: array:3 [▼
        "items" => array:2 [▼
            "uuid1" => array:8 [▼
                "type" => "article"
                "id" => "123"
                "name" => "Artikelname"
                "price" => 19.99
                "amount" => 2
                "total" => 39.98
                "image" => "image.jpg"
                "cat_name" => "Kategorie"
            ]
            "uuid2" => array:9 [▼
                "type" => "article"
                "id" => "456"
                "name" => "Anderer Artikel mit Varianten"
                "total" => 64.98
                "price" => 19.99
                "amount" => 2
                "image" => "image2.jpg"
                "cat_name" => "Andere Kategorie"
                "variants" => array:2 [▼
                    "variant1" => array:7 [▼
                        "type" => "variant"
                        "id" => "456-1"
                        "name" => "Variante 1"
                        "price" => 29.99
                        "amount" => 1
                        "total" => 29.99
                        "image" => "variant1.jpg"
                    ]
                    "variant2" => array:7 [▼
                        "type" => "variant"
                        "id" => "456-2"
                        "name" => "Variante 2"
                        "price" => 34.99
                        "amount" => 1
                        "total" => 34.99
                        "image" => "variant2.jpg"
                    ]
                ]
            ]
        ]
        "address" => []
        "last_update" => 1753638533
    ]
}
    */
if(rex::isFrontend()) {

// Tax and shipping settings
$taxRate = 0.19; // 19% German VAT
$shippingCost = 5.90; // Flat shipping within Germany

// ----------- PayPal API Context ----------- //
$apiContext = new ApiContext(
    new OAuthTokenCredential(
        WarehousePayPal::getClientId(), 
        WarehousePayPal::getClientSecret()  
    )
);

// Set German locale
$apiContext->setConfig([
    'locale' => 'de_DE',
    'http.ConnectionTimeOut' => 30,
    'log.LogEnabled' => false
]);

// ----------- Build Cart Items ----------- //
$paypalItems = [];
$subtotal = 0.0;

// Handle cart items with variants
foreach ($cart->getItems() as $uuid => $article) {
    // Add main article item
    $item = (new Item())
        ->setName($article['name'])
        ->setSku($article['id']) // Use ID as SKU
        ->setCurrency(Warehouse::getConfig('currency', 'EUR')) // Default to EUR
        ->setQuantity($article['amount'])
        ->setPrice(number_format($article['price'], 2, '.', '')); // Ensure price is formatted correctly

    $paypalItems[] = $item;
    $subtotal += $article['price'] * $article['amount'];

    // Handle variants if they exist
    if (isset($article['variants']) && is_array($article['variants'])) {
        foreach ($article['variants'] as $variantUuid => $variant) {
            $variantItem = (new Item())
                ->setName($variant['name'])
                ->setSku($variant['id'])
                ->setCurrency(Warehouse::getConfig('currency', 'EUR'))
                ->setQuantity($variant['amount'])
                ->setPrice(number_format($variant['price'], 2, '.', ''));

            $paypalItems[] = $variantItem;
            $subtotal += $variant['price'] * $variant['amount'];
        }
    }
}

// ----------- Tax Calculation ----------- //
$tax = round($subtotal * $taxRate, 2);

// ----------- Build Amount Details ----------- //
$details = new Details();
$details->setShipping(number_format($shippingCost, 2, '.', ''))
        ->setTax(number_format($tax, 2, '.', ''))
        ->setSubtotal(number_format($subtotal, 2, '.', ''));

// ----------- Total Amount ----------- //
$total = $subtotal + $tax + $shippingCost;

$amount = new Amount();
$amount->setCurrency('EUR')
       ->setTotal(number_format($total, 2, '.', ''))
       ->setDetails($details);

// ----------- Item List ----------- //
$itemList = new ItemList();
$itemList->setItems($paypalItems);

// Get shipping address from cart or session
$cartData = $cart->cart ?? [];
$shippingAddress = $cartData['address'] ?? [];
$prefillPayer = $cartData['address'] ?? [];

// Set shipping address if available
if (!empty($shippingAddress)) {
    $itemList->setShippingAddress(
        (new ShippingAddress())
            ->setRecipientName($shippingAddress['recipient_name'] ?? ($shippingAddress['firstname'] ?? '') . ' ' . ($shippingAddress['lastname'] ?? ''))
            ->setLine1($shippingAddress['line1'] ?? $shippingAddress['street'] ?? '')
            ->setCity($shippingAddress['city'] ?? '')
            ->setCountryCode($shippingAddress['country_code'] ?? 'DE')
            ->setPostalCode($shippingAddress['postal_code'] ?? $shippingAddress['zip'] ?? '')
    );
}

// ----------- Payer ----------- //
$payer = new Payer();
$payer->setPaymentMethod('paypal');

if (!empty($prefillPayer)) {
    $payerInfo = new PayerInfo();
    $payerInfo->setFirstName($prefillPayer['first_name'] ?? $prefillPayer['firstname'] ?? '')
              ->setLastName($prefillPayer['last_name'] ?? $prefillPayer['lastname'] ?? '')
              ->setEmail($prefillPayer['email'] ?? '');
    $payer->setPayerInfo($payerInfo);
}

// ----------- Transaction ----------- //
$transaction = new Transaction();
$transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription('Bestellung in Ihrem Shop')
            ->setInvoiceNumber(uniqid('order_')); // Order tracking

// ----------- Redirect URLs ----------- //
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl($domain->getCheckoutUrl(['continue_with' => 'paypal_succeded']))
             ->setCancelUrl($domain->getCheckoutUrl(['continue_with' => 'paypal_canceled']));

// ----------- Payment Creation ----------- //
$payment = new Payment();
$payment->setIntent('sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions([$transaction]);

// ----------- Execute and Track Order ----------- //
try {
    $payment->create($apiContext);

    $order = Order::create();
    $order->setPaymentId($payment->getId());
    $order->setOrderJson(json_encode([
        'paypal_payment_id' => $payment->getId(),
        'items' => $paypalItems,
        'invoice_number' => $transaction->getInvoiceNumber(),
        'payer_info' => $prefillPayer,
        'shipping_address' => $shippingAddress
    ]));
    $order->setOrderTotal($total);
    
    // Set customer information if available
    if (!empty($prefillPayer)) {
        $order->setFirstname($prefillPayer['firstname'] ?? $prefillPayer['first_name'] ?? '');
        $order->setLastname($prefillPayer['lastname'] ?? $prefillPayer['last_name'] ?? '');
        $order->setEmail($prefillPayer['email'] ?? '');
    }
    
    // Set shipping address if available
    if (!empty($shippingAddress)) {
        $order->setAddress($shippingAddress['street'] ?? $shippingAddress['line1'] ?? '');
        $order->setZip($shippingAddress['zip'] ?? $shippingAddress['postal_code'] ?? '');
        $order->setCity($shippingAddress['city'] ?? '');
        $order->setCountry($shippingAddress['country_code'] ?? 'DE');
    }
    
    $order->setCreatedate(date('Y-m-d H:i:s'));
    $order->setPaymentType('paypal');
    $order->save();

    // Redirect user to PayPal approval URL
    rex_response::sendRedirect($payment->getApprovalLink());
    exit;
} catch (Exception $ex) {
    Logger::log('paypal_error', 'Error creating PayPal payment: ' . $ex->getMessage());
    // Handle error gracefully, e.g., show an error message to the user
    echo "<h2>Fehler bei der PayPal-Zahlung</h2>";
    echo "<p>Es ist ein Problem bei der Erstellung der PayPal-Zahlung aufgetreten. Bitte versuchen Sie es später erneut.</p>";
    echo "<p>" . htmlspecialchars($ex->getMessage()) . "</p>";
}

/*
    if(rex_request('continue_with', 'string') === 'paypal_create_order') {
        Logger::log('paypal_create_order', 'Creating PayPal order');
        FriendsOfRedaxo\Warehouse\PayPal::createOrder();
        exit;
    }

    if(rex_request('continue_with', 'string') === 'paypal_execute_payment') {
        Logger::log('paypal_execute_payment', 'Execute Payment');
        FriendsOfRedaxo\Warehouse\PayPal::executePayment();
        FriendsOfRedaxo\Warehouse\Warehouse::PaypalPaymentApprovedViaResponse($response);
        rex_response::sendRedirect(rex_getUrl($domain->getThankyouArtUrl(), '', $params ?? [] , '&'));    
        exit;
    }

    if(rex_request('continue_with', 'string') === 'paypal_cancel_payment') {
        Logger::log('paypal_cancel_payment', 'Cancel Payment');
        FriendsOfRedaxo\Warehouse\PayPal::cancelPayment();
        rex_response::sendRedirect(rex_getUrl($domain->getCheckoutUrl(['continue_with' => 'summary', 'paypal_canceled' => 1])));
        exit;
    }

    if(rex_request('continue_with', 'string') === 'paypal_return') {
        Logger::log('paypal_return', 'Return from PayPal');
        FriendsOfRedaxo\Warehouse\PayPal::returnFromPayPal();
        rex_response::sendRedirect(rex_getUrl($domain->getCheckoutUrl(['continue_with' => 'summary'])));
        exit;
    }
 */   
}
?>
