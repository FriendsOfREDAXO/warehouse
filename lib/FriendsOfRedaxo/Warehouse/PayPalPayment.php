<?php

require_once 'vendor/autoload.php';

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use PaypalServerSdkLib\Models\OrderRequest;
use PaypalServerSdkLib\Models\PurchaseUnitRequest;
use PaypalServerSdkLib\Models\AmountWithBreakdown;
use PaypalServerSdkLib\Models\Money;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\ApplicationContext;
use PaypalServerSdkLib\Models\ShippingDetail;
use PaypalServerSdkLib\Models\Address;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Name;
use PaypalServerSdkLib\Models\PaymentSource;
use PaypalServerSdkLib\Models\OrdersPayPal;
use PaypalServerSdkLib\Models\ExperienceContext;
use PaypalServerSdkLib\Models\Payee;
use PaypalServerSdkLib\Models\PayeeBase;
use PaypalServerSdkLib\Models\PurchaseUnit;
use Psr\Log\LogLevel;

class PayPalPayment
{
    private $client;
    
    public function __construct(string $clientId, string $clientSecret)
    {
    }
    
    public function createPayment()
    {
        try {
            // 3. Warenkorb-Inhalt definieren
            
            // Gesamtbetrag anhand Cart berechnen
            $itemTotal = $cart->getTotal();
            $shipping = $cart->totalShippingCosts();
            $tax = 0;
            $total = number_format($itemTotal + $shipping + $tax, 2, '.', '');
            
            // 5. Adressdaten (Lieferadresse)
            $shippingAddress = new Address('DE');
            $shippingAddress->setAddressLine1('Musterstraße 123');
            $shippingAddress->setAddressLine2('Apartment 4B');
            $shippingAddress->setAdminArea2('München');
            $shippingAddress->setAdminArea1('Bayern');
            $shippingAddress->setPostalCode('80331');
            $shippingAddress->setCountryCode('DE');

            $shippingName = new Name([
                'given_name' => 'Max',
                'surname' => 'Mustermann'
            ]);
            
            
            // 6. Rechnungsdaten (über Payee)
            $payee = new PayeeBase();
            $payee->setEmailAddress('mail@example.com');
            $payee->setMerchantId('MERCHANT123');
            
            // Purchase Unit mit allen Details
            $purchaseUnit = new PurchaseUnit([
                'reference_id' => 'PUHF',
                'amount' => new AmountWithBreakdown('EUR', 999.99),
                'items' => $items,
                'shipping' => $shipping,
                'payee' => $payee,
                // 7. Shop-Informationen
                'description' => 'Bestellung von MusterShop GmbH',
                'custom_id' => 'ORDER-2024-001',
                'invoice_id' => 'INV-2024-001',
                'soft_descriptor' => 'MUSTERSHOP*ONLINE'
            ]);
            
            // 2. Sprache und 4. Liefergebiet Einschränkung über Application Context
            $applicationContext = new ApplicationContext([
                'locale' => 'de_DE', // Gewünschte Sprache
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                // 8. URL-Parameter für erfolgreiche und fehlgeschlagene Zahlung
                'return_url' => Domain::getCurrent()->getCheckoutUrl(["paypal" => 'success']),
                'cancel_url' => Domain::getCurrent()->getCheckoutUrl(["paypal" => 'failed']),
                'brand_name' => 'MusterShop GmbH',
                'landing_page' => 'BILLING',
                'user_action' => 'PAY_NOW'
            ]);
            
            // PayPal Payment Source mit Experience Context
            $experienceContext = new ExperienceContext([
                'locale' => 'de_DE',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                // 4. Einschränkung des Liefergebiets
                'allowed_countries' => ['DE'], // Nur Deutschland
                'return_url' => 'https://example.com/payment/success',
                'cancel_url' => 'https://example.com/payment/cancel'
            ]);
            
            $paymentSource = new PaymentSource([
                'paypal' => new OrdersPayPal([
                    'experience_context' => $experienceContext
                ])
            ]);
            
            // Order Request erstellen
            $orderRequest = new OrderRequest([
                'intent' => 'CAPTURE',
                'purchase_units' => [$purchaseUnit],
                'payment_source' => $paymentSource,
                'application_context' => $applicationContext
            ]);
            
            // 9. Weitere Pflichtangaben über Header
            $headers = [
                'PayPal-Request-Id' => uniqid('REQ-', true), // Eindeutige Request ID
                'Prefer' => 'return=representation',
                'PayPal-Partner-Attribution-Id' => 'MusterShop_SP_DE',
                'Content-Type' => 'application/json'
            ];
            
            // Order erstellen
            $ordersController = $this->client->getOrdersController();
            $response = $ordersController->ordersCreate($orderRequest, $headers);
            
            if ($response->getStatusCode() === 201) {
                $orderPayPal = $response->getResult();
                
                // Approval URL für Weiterleitung extrahieren
                $approvalUrl = null;
                foreach ($orderPayPal->getLinks() as $link) {
                    if ($link->getRel() === 'approve') {
                        $approvalUrl = $link->getHref();
                        break;
                    }
                }
                
                return [
                    'success' => true,
                    'order_id' => $orderPayPal->getId(),
                    'status' => $orderPayPal->getStatus(),
                    'approval_url' => $approvalUrl,
                    'order' => $orderPayPal
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Order creation failed',
                    'status_code' => $response->getStatusCode()
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    // Methode zum Bestätigen der Zahlung nach Rückkehr von PayPal
    public function capturePayment($orderId)
    {
        try {
            $ordersController = $this->client->getOrdersController();
            $response = $ordersController->ordersCapture($orderId);
            
            if ($response->getStatusCode() === 201) {
                return [
                    'success' => true,
                    'capture' => $response->getResult()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Payment capture failed'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

// Verwendungsbeispiel
$clientId = 'YOUR_PAYPAL_CLIENT_ID';
$clientSecret = 'YOUR_PAYPAL_CLIENT_SECRET';

$paypalPayment = new PayPalPaymentExample($clientId, $clientSecret, Environment::SANDBOX);
$result = $paypalPayment->createPayment();

if ($result['success']) {
    echo "Order erstellt: " . $result['order_id'] . "\n";
    echo "Status: " . $result['status'] . "\n";
    echo "Approval URL: " . $result['approval_url'] . "\n";
    
    // Benutzer zur approval_url weiterleiten
    header('Location: ' . $result['approval_url']);
} else {
    echo "Fehler: " . $result['error'] . "\n";
}
