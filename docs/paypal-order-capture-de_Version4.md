# PayPal PHP Server SDK (@paypal/PayPal-PHP-Server-SDK) – Technische Übersicht: Order erstellen und capturen

Diese Anleitung zeigt Schritt für Schritt, wie du mit der offiziellen PayPal PHP Server SDK (Repository: paypal/PayPal-PHP-Server-SDK) eine Order erstellst, den Warenkorb (Cart) modellierst, den Käufer zur Genehmigung weiterleitest und die Zahlung anschließend capturst. Zusätzlich werden Branding, Währungen/Länder, Versandadresse sowie Steuer-/Versand-/Rabattangaben abgedeckt – mit konkreten SDK-Klassen und Hinweisen zu englischen Parametern.

Wichtig:
- Diese SDK kapselt die PayPal Orders API v2. Die API-Objekte sind als PHP-Modelle/Builder verfügbar.
- Einige Experience-Felder sind in application_context vorhanden; Teile sind in PayPals neueren Spezifikationen in experience_context von payment_source gewandert. Nutze die SDK-Modelle wie bereitgestellt.

---

## 0) Voraussetzungen

- PHP 7.4+ (empfohlen 8.1+), ext-curl, ext-json
- PayPal REST API Credentials (Client ID, Secret) aus dem PayPal Developer Dashboard
- PayPal-Umgebungen:
  - Sandbox: https://api-m.sandbox.paypal.com
  - Live: https://api-m.paypal.com

Sichere Konfiguration (z. B. .env):
- PAYPAL_ENV=sandbox|production
- PAYPAL_CLIENT_ID, PAYPAL_CLIENT_SECRET
- RETURN_URL, CANCEL_URL
- BRAND_NAME, LOGO_URL (190×60, HTTPS)

---

## 1) SDK initialisieren und authentifizieren

Die SDK übernimmt den OAuth2 Client-Credentials-Flow intern. Du konfigurierst den Client mit Client ID/Secret und wählst Sandbox oder Live.

Beispiel:

```php
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use Psr\Log\LogLevel;

$client = PaypalServerSdkLib\PaypalServerSdkClientBuilder::init()
    ->clientCredentialsAuthCredentials(
        ClientCredentialsAuthCredentialsBuilder::init(
            $_ENV['PAYPAL_CLIENT_ID'],
            $_ENV['PAYPAL_CLIENT_SECRET']
        )
    )
    ->environment($_ENV['PAYPAL_ENV'] === 'production' ? Environment::PRODUCTION : Environment::SANDBOX)
    ->loggingConfiguration(
        LoggingConfigurationBuilder::init()
            ->level(LogLevel::INFO)
            ->requestConfiguration(RequestLoggingConfigurationBuilder::init()->body(true))
            ->responseConfiguration(ResponseLoggingConfigurationBuilder::init()->headers(true))
    )
    ->build();

$orders = $client->getOrdersController();
```

Hinweise:
- OAuth-Token-Handling erledigt die SDK. Du musst keinen Token manuell cachen.
- Für idempotente POST-Aufrufe (Create/Capture) kannst du eine PayPal-Request-Id setzen (siehe unten).

---

## 2) Warenkorb (Cart) modellieren: Items, Betrag, Breakdown

Zentrale Modelle:
- OrderRequest / OrderRequestBuilder
- PurchaseUnitRequest / PurchaseUnitRequestBuilder
- Item / ItemBuilder
- AmountWithBreakdown (+ Money, AmountBreakdown)
- ShippingDetails (optional)
- PayeeBase (optional; expliziter Zahlungsempfänger)

Wichtig: Die PayPal Orders-API zeigt im Checkout keine Produktbilder an. Das Item-Modell enthält Felder wie name, description, sku, quantity, unit_amount, tax, category – aber kein image_url. Produktbilder verwaltest du im Shop.

Beispiel: Items und Amount mit Breakdown (EUR, MwSt., Versand, Rabatt)

```php
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\AmountBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;

// Items
$item1 = ItemBuilder::init('Premium T-Shirt', 'PHYSICAL_GOODS', '2')
    ->description('Bio-Baumwolle, Farbe Navy')
    ->sku('TSHIRT-001-NAVY')
    ->unitAmount(MoneyBuilder::init('EUR', '19.99')->build())
    ->tax(MoneyBuilder::init('EUR', '1.60')->build())
    ->build();

$item2 = ItemBuilder::init('Sticker Pack', 'PHYSICAL_GOODS', '1')
    ->description('5x Sticker')
    ->sku('STICKER-5')
    ->unitAmount(MoneyBuilder::init('EUR', '4.00')->build())
    ->tax(MoneyBuilder::init('EUR', '0.76')->build())
    ->build();

// Breakdown: Summe muss exakt aufgehen
$breakdown = AmountBreakdownBuilder::init()
    ->itemTotal(MoneyBuilder::init('EUR', '43.98')->build())
    ->shipping(MoneyBuilder::init('EUR', '4.99')->build())
    ->handling(MoneyBuilder::init('EUR', '0.00')->build())
    ->insurance(MoneyBuilder::init('EUR', '0.00')->build())
    ->taxTotal(MoneyBuilder::init('EUR', '3.96')->build())
    ->shippingDiscount(MoneyBuilder::init('EUR', '0.00')->build())
    ->discount(MoneyBuilder::init('EUR', '5.60')->build())
    ->build();

// Endbetrag mit Breakdown
$amount = AmountWithBreakdownBuilder::init('EUR', '39.33')
    ->breakdown($breakdown)
    ->build();
```

Tipps zu Preisen:
- Werte als Strings mit Punkt-Notation übergeben.
- Zero-decimal-Währungen (z. B. JPY) dürfen keine Nachkommastellen haben.
- Rechne im Shop intern mit Integer-Cents oder BCMath zur Vermeidung von Rundungsfehlern.

---

## 3) Merchant-Branding und Empfänger setzen

- Branding im OrderApplicationContext: brand_name, logo_url, locale, user_action, landing_page
- Optionaler Empfänger (payee) pro Purchase Unit (PayeeBase): merchant_id oder email_address

```php
use PaypalServerSdkLib\Models\Builders\OrderApplicationContextBuilder;
use PaypalServerSdkLib\Models\Builders\PayeeBaseBuilder;
use PaypalServerSdkLib\Models\OrderApplicationContextUserAction;
use PaypalServerSdkLib\Models\OrderApplicationContextLandingPage;
use PaypalServerSdkLib\Models\OrderApplicationContextShippingPreference;

$applicationContext = OrderApplicationContextBuilder::init()
    ->brandName($_ENV['BRAND_NAME'] ?? 'Mein Super Shop')
    ->locale('de-DE')
    ->logoUrl($_ENV['LOGO_URL'] ?? 'https://example.com/logo-190x60.png')
    ->userAction(OrderApplicationContextUserAction::PAY_NOW) // siehe Glossar
    ->landingPage(OrderApplicationContextLandingPage::LOGIN) // siehe Glossar
    ->shippingPreference(OrderApplicationContextShippingPreference::SET_PROVIDED_ADDRESS) // siehe Glossar
    ->returnUrl($_ENV['RETURN_URL'])
    ->cancelUrl($_ENV['CANCEL_URL'])
    ->build();

// Optional: Empfänger explizit setzen, falls von Standard-Konto abweichend
$payee = PayeeBaseBuilder::init()
    // ->merchantId('YOUR_MERCHANT_ID')
    ->emailAddress('merchant@deinshop.de')
    ->build();
```

---

## 4) Versand-/Lieferadresse und Ländereinschränkung

- Versandadresse in purchase_unit.shipping setzen.
- Länderbeschränkungen implementierst du in deiner Shop-Logik, indem du nur erlaubte ISO-2-Ländercodes zulässt.

```php
use PaypalServerSdkLib\Models\Builders\NameBuilder;
use PaypalServerSdkLib\Models\Builders\AddressPortableBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingDetailsBuilder;

$shipping = ShippingDetailsBuilder::init()
    ->name(NameBuilder::init()->fullName('Max Mustermann')->build())
    ->address(AddressPortableBuilder::init()
        ->addressLine1('Musterstraße 1')
        ->addressLine2('2. OG')
        ->adminArea2('Berlin') // Stadt
        ->adminArea1('BE')     // Bundesland/State (falls zutreffend)
        ->postalCode('10115')
        ->countryCode('DE')
        ->build()
    )
    ->build();

// Erlaubte Länder/Währungen (Shop-Validierung vor Create Order)
$allowedCurrencies = ['EUR', 'USD'];
$allowedCountries = ['DE', 'AT'];
if (!in_array($amount->getCurrencyCode(), $allowedCurrencies, true)) {
    throw new DomainException('Currency not supported');
}
if (!in_array('DE', $allowedCountries, true)) {
    throw new DomainException('Shipping country not supported');
}
```

---

## 5) Purchase Unit zusammensetzen

```php
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;

$purchaseUnit = PurchaseUnitRequestBuilder::init($amount)
    ->referenceId('CART-12345')
    ->customId('ORDER-100001')
    ->invoiceId('INV-100001')
    ->description('Bestellung #100001')
    ->payee($payee)        // optional
    ->items([$item1, $item2])
    ->shipping($shipping)  // abhängig von shipping_preference
    ->build();
```

---

## 6) Order erstellen (intent, application_context), Approve-Link extrahieren

- intent legt fest, ob sofort „captured“ oder zunächst „autorisiert“ wird.

```php
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\OrderIntent; // enum-Konstante 'CAPTURE' oder 'AUTHORIZE'

$orderRequest = OrderRequestBuilder::init(OrderIntent::CAPTURE, [$purchaseUnit])
    ->applicationContext($applicationContext)
    ->build();

// Create Order (idempotent, volle Repräsentation anfordern)
$apiResponse = $orders->createOrder([
    'prefer' => 'return=representation',
    'paypalRequestId' => bin2hex(random_bytes(16)),
    'body' => $orderRequest,
]);
/** @var PaypalServerSdkLib\Models\Order $createdOrder */
$createdOrder = $apiResponse->getResult();
$orderId = $createdOrder->getId();

// Käufer zur Genehmigung weiterleiten (rel=approve)
$approveUrl = null;
foreach ($createdOrder->getLinks() ?? [] as $link) {
    if ($link->getRel() === 'approve') {
        $approveUrl = $link->getHref();
        break;
    }
}
if (!$approveUrl) {
    throw new RuntimeException('Approve link not found');
}
// 302 Redirect auf $approveUrl
```

---

## 7) Käufer-Rückkehr und Capture

Nach Genehmigung leitet PayPal zur return_url um und übergibt den token (Order-ID). Anschließend capturen:

```php
use PaypalServerSdkLib\Models\Builders\OrderCaptureRequestBuilder;

// token = Order-ID aus Query (?token=...)
$orderId = $_GET['token'] ?? null;
if (!$orderId) {
    throw new InvalidArgumentException('Missing token (order id)');
}

// Optionaler Body (z. B. payment_source); meist leer ausreichend
$captureReq = OrderCaptureRequestBuilder::init()->build();

$captureRes = $orders->captureOrder([
    'id' => $orderId,
    'prefer' => 'return=representation',
    'paypalRequestId' => bin2hex(random_bytes(16)), // Idempotenz
    'body' => $captureReq,
]);

/** @var PaypalServerSdkLib\Models\Order $capturedOrder */
$capturedOrder = $captureRes->getResult();
$status = $capturedOrder->getStatus(); // z. B. COMPLETED

$pus = $capturedOrder->getPurchaseUnits() ?? [];
$captures = $pus[0]->getPayments()->getCaptures() ?? [];
foreach ($captures as $cap) {
    // $cap->getId(), $cap->getStatus(), $cap->getAmount(), $cap->getSellerReceivableBreakdown(), ...
}
```

Best Practices:
- Verwende PayPal-Request-Id für Idempotenz (Reloads, Doppelklicks).
- Verifiziere Betrag, Währung und Empfänger vor der Bestellfreigabe.
- Nutze Webhooks (z. B. PAYMENT.CAPTURE.COMPLETED), um finalen Status unabhängig vom Browser zu bestätigen.

---

## 8) Währungen und Länder einschränken

- Setze die Währung über AmountWithBreakdown.currency_code.
- PayPal erzwingt keine Order-seitige Allowlist. Implementiere die Einschränkung in deiner Shop-Logik und übermittle nur erlaubte Kombinationen.
- Beachte Zero-Decimal-Währungen (keine Nachkommastellen).

---

## 9) Steuer, Versand, Rabatte – mit und ohne MwSt.

- Item-Steuern pro Position: Item.tax
- Gesamtsummen: Amount.breakdown.item_total, shipping, handling, insurance, tax_total, discount, shipping_discount
- Mathematische Identität muss gelten:
  amount.value = item_total + shipping + handling + insurance + tax_total − shipping_discount − discount

Gutscheincodes:
- Es gibt kein Feld zur Code-Bezeichnung im Orders-Payload. Hinterlege den Code intern (DB) oder verwende custom_id/reference_id/invoice_id zur Nachverfolgung.

---

## 10) Glossar: Englische Parameter und wann du sie nutzt

- intent = CAPTURE
  - Deutsch: „sofort einziehen (abbuchen)“
  - Einsatz: Standard-B2C-Shops, physische/digitale Güter mit sofortiger Zahlung; geringe Fulfillment-Latenz.
- intent = AUTHORIZE
  - Deutsch: „vorautorisieren (reservieren), später einziehen“
  - Einsatz: Preorder, maßgefertigte Ware, variable Lieferzeiten/Kosten; B2B/hochpreisige Artikel. Danach separat Capture per Payments API/Authorize-Capture.
- application_context.user_action = PAY_NOW
  - Deutsch: „Jetzt bezahlen“-Button
  - Einsatz: Direkter Kaufabschluss ohne weitere Shop-Schritte nach PayPal-Genehmigung.
- application_context.user_action = CONTINUE
  - Deutsch: „Weiter“-Button
  - Einsatz: Wenn Käufer nach Genehmigung in deinem Shop noch Schritte hat (z. B. Review).
- application_context.landing_page = LOGIN
  - Deutsch: Login-Seite zuerst
  - Einsatz: Wenn deine Kunden überwiegend PayPal-Konten nutzen (Account-Klientel).
- application_context.landing_page = GUEST_CHECKOUT
  - Deutsch: Gastkauf/ Karteneingabe zuerst
  - Einsatz: Wenn viele Neukunden ohne PayPal-Konto zahlen (Kredit-/Debitkarte über PayPal).
- application_context.shipping_preference = GET_FROM_FILE
  - Deutsch: Adresse „aus der Datei“ (aus PayPal-Konto des Käufers)
  - Einsatz: Konsumgüter an PayPal-Standardadresse, du sammelst keine Adresse im Shop.
- application_context.shipping_preference = SET_PROVIDED_ADDRESS
  - Deutsch: „angegebene Adresse verwenden“ (vom Shop)
  - Einsatz: Du erfasst die Lieferadresse im Shop (Checkout vor PayPal).
- application_context.shipping_preference = NO_SHIPPING
  - Deutsch: „kein Versand“ (keine Adresse erfragen)
  - Einsatz: Digitale Güter, Services, Spenden.

Hinweis: In neueren PayPal-Schemata ist landing_page in experience_context je payment_source verschoben; diese SDK-Version bietet weiterhin application_context-Felder.

---

## 11) Kompakter End-to-End-Fluss

1) Client initialisieren (Sandbox/Live, Credentials, Logging)
2) Cart → Items, AmountWithBreakdown erstellen
3) PurchaseUnit (optional payee, shipping) erstellen
4) OrderRequest (intent, application_context) erstellen
5) OrdersController::createOrder(...) aufrufen, Approve-Link redirecten
6) Return-Handler: token (Order-ID) entgegennehmen
7) OrdersController::captureOrder(...) aufrufen (idempotent)
8) Ergebnis prüfen, Order persistieren, Bestätigung anzeigen
9) Webhooks registrieren und verarbeiten

---

## Weiterführende Links

Repository & SDK
- Repo (Code): https://github.com/paypal/PayPal-PHP-Server-SDK
- README – Init, Environments, Auth: https://github.com/paypal/PayPal-PHP-Server-SDK/blob/main/README.md
- Orders Controller Doku: https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/1.1.0/doc/controllers/orders.md
- OAuth2 (Client Credentials) in SDK: https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/1.1.0/doc/auth/oauth-2-client-credentials-grant.md
- Logging-Konfiguration: https://www.github.com/paypal/PayPal-PHP-Server-SDK/tree/1.1.0/doc/logging-configuration-builder.md

PayPal Developer Docs (API-Referenz)
- Orders API v2 Überblick: https://developer.paypal.com/docs/api/orders/v2/
- Create Order: https://developer.paypal.com/docs/api/orders/v2/#orders_create
- Capture Order: https://developer.paypal.com/docs/api/orders/v2/#orders_capture
- Item Objekt: https://developer.paypal.com/docs/api/orders/v2/#definition-item
- Amount Breakdown: https://developer.paypal.com/docs/api/orders/v2/#definition-amount_with_breakdown
- Application Context: https://developer.paypal.com/docs/api/orders/v2/#definition-application_context

Guides & Betrieb
- Server-Integration (Capture): https://developer.paypal.com/docs/checkout/reference/server-integration/capture-order/
- Webhooks & Events: https://developer.paypal.com/docs/api-basics/notifications/webhooks/event-names/
- Unterstützte Währungen: https://developer.paypal.com/docs/reports/reference/paypal-supported-currencies/
- Zero-Decimal-Währungen: https://developer.paypal.com/docs/api/reference/currency-codes/#zero-decimal
- Idempotency (PayPal-Request-Id): https://developer.paypal.com/api/rest/responses/#idempotency