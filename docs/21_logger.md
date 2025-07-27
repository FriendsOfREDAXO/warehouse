# Die Klasse `Logger`

Die Logger-Klasse bietet umfassendes Logging für alle wichtigen Warehouse-Ereignisse. Sie ermöglicht die Nachverfolgung von Benutzeraktionen, Bestellungen, Zahlungen und anderen Shop-Aktivitäten.

> Die Logger-Klasse ist als statische Utility-Klasse konzipiert und nutzt das REDAXO-Logging-System für persistente Protokollierung.

## Übersicht

Die Logger-Klasse bietet folgende Funktionen:

- Strukturiertes Logging aller Shop-Ereignisse
- Konfigurierbare Aktivierung/Deaktivierung
- Automatische Log-Rotation bei Dateigröße-Limits
- Vordefinierte Event-Konstanten
- JSON-basierte Parameter-Serialisierung

## Event-Konstanten

### Warenkorb-Events

```php
public const EVENT_ADD_CART = 'add_cart';           // Artikel zum Warenkorb hinzugefügt
public const EVENT_REMOVE_CART = 'remove_cart';     // Artikel aus Warenkorb entfernt
public const EVENT_EMPTY_CART = 'empty_cart';       // Warenkorb geleert
public const EVENT_UPDATE_CART = 'update_cart';     // Warenkorb aktualisiert
```

### Checkout- und Zahlungs-Events

```php
public const EVENT_CHECKOUT = 'checkout';                    // Checkout-Prozess gestartet
public const EVENT_START_PAYMENT = 'start_payment';         // Zahlung initiiert
public const EVENT_PAYMENT_SUCCESS = 'payment_success';     // Zahlung erfolgreich
public const EVENT_PAYMENT_FAILED = 'payment_failed';       // Zahlung fehlgeschlagen
```

### Bestell-Events

```php
public const EVENT_ORDER_PLACED = 'order_placed';       // Bestellung aufgegeben
public const EVENT_ORDER_CANCELLED = 'order_cancelled'; // Bestellung storniert
public const EVENT_ORDER_SHIPPED = 'order_shipped';     // Bestellung versandt
public const EVENT_ORDER_RETURNED = 'order_returned';   // Bestellung retourniert
```

### Benutzer-Events

```php
public const EVENT_LOGIN = 'login';         // Benutzer angemeldet
public const EVENT_LOGOUT = 'logout';       // Benutzer abgemeldet
public const EVENT_REGISTER = 'register';   // Benutzer registriert
```

### Alle Events

```php
public const EVENTS = [
    self::EVENT_ADD_CART, self::EVENT_REMOVE_CART, self::EVENT_EMPTY_CART,
    self::EVENT_UPDATE_CART, self::EVENT_CHECKOUT, self::EVENT_START_PAYMENT,
    self::EVENT_PAYMENT_SUCCESS, self::EVENT_PAYMENT_FAILED, 
    self::EVENT_ORDER_PLACED, self::EVENT_ORDER_CANCELLED,
    self::EVENT_ORDER_SHIPPED, self::EVENT_ORDER_RETURNED,
    self::EVENT_LOGIN, self::EVENT_LOGOUT, self::EVENT_REGISTER
];
```

## Methoden und Beispiele

### `log(string $event, string $message, ?int $order_id = null, ?int $article_id = null, ?int $article_variant_id = null, array $params = [])`

Protokolliert ein Ereignis mit allen relevanten Daten.

**Parameter:**
- `$event` (string): Event-Typ (verwende Konstanten)
- `$message` (string): Beschreibende Nachricht
- `$order_id` (int|null): Optional - Bestell-ID
- `$article_id` (int|null): Optional - Artikel-ID
- `$article_variant_id` (int|null): Optional - Artikel-Varianten-ID
- `$params` (array): Optional - Zusätzliche Parameter

```php
use FriendsOfRedaxo\Warehouse\Logger;

// Einfaches Event loggen
Logger::log(Logger::EVENT_ADD_CART, 'Artikel zum Warenkorb hinzugefügt');

// Event mit Artikel-ID
Logger::log(
    Logger::EVENT_ADD_CART,
    'T-Shirt Größe L zum Warenkorb hinzugefügt',
    null,
    123,  // Artikel-ID
    456   // Varianten-ID
);

// Event mit allen Parametern
Logger::log(
    Logger::EVENT_ORDER_PLACED,
    'Neue Bestellung aufgegeben',
    789,  // Bestell-ID
    null,
    null,
    [
        'customer_id' => 42,
        'total_amount' => 99.95,
        'payment_method' => 'paypal',
        'shipping_address' => 'Musterstraße 1, 12345 Musterstadt'
    ]
);
```

### Logger-Status verwalten

#### `activate()`

Aktiviert das Logging für das Warehouse-Addon.

```php
use FriendsOfRedaxo\Warehouse\Logger;

// Logging aktivieren
Logger::activate();
```

#### `deactivate()`

Deaktiviert das Logging für das Warehouse-Addon.

```php
use FriendsOfRedaxo\Warehouse\Logger;

// Logging deaktivieren
Logger::deactivate();
```

#### `isActive()`

Prüft, ob das Logging aktiviert ist.

**Rückgabe:** `bool` - True wenn aktiv

```php
use FriendsOfRedaxo\Warehouse\Logger;

if (Logger::isActive()) {
    echo "Logging ist aktiviert";
} else {
    echo "Logging ist deaktiviert";
}
```

### Log-Dateien verwalten

#### `logFile()`

Gibt den Pfad zur Log-Datei zurück.

**Rückgabe:** `string` - Pfad zur Log-Datei

```php
use FriendsOfRedaxo\Warehouse\Logger;

$logPath = Logger::logFile();
echo "Log-Datei: " . $logPath; // /redaxo/data/log/warehouse.log
```

#### `logFolder()`

Gibt den Pfad zum Log-Ordner zurück.

**Rückgabe:** `string` - Pfad zum Log-Ordner

```php
use FriendsOfRedaxo\Warehouse\Logger;

$logFolder = Logger::logFolder();
echo "Log-Ordner: " . $logFolder;
```

#### `delete()`

Löscht die Log-Datei.

**Rückgabe:** `bool` - True bei Erfolg

```php
use FriendsOfRedaxo\Warehouse\Logger;

if (Logger::delete()) {
    echo "Log-Datei wurde erfolgreich gelöscht";
} else {
    echo "Fehler beim Löschen der Log-Datei";
}
```

## Praktische Anwendungsbeispiele

### Warenkorb-Aktivitäten loggen

```php
use FriendsOfRedaxo\Warehouse\Logger;
use FriendsOfRedaxo\Warehouse\Article;

// Artikel zum Warenkorb hinzufügen
$article = Article::get(123);
$quantity = 2;

Logger::log(
    Logger::EVENT_ADD_CART,
    sprintf('Artikel "%s" (%dx) zum Warenkorb hinzugefügt', $article->getName(), $quantity),
    null,
    $article->getId(),
    null,
    [
        'quantity' => $quantity,
        'price' => $article->getPrice(),
        'user_session' => session_id(),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]
);
```

### Bestellvorgang protokollieren

```php
use FriendsOfRedaxo\Warehouse\Logger;
use FriendsOfRedaxo\Warehouse\Order;
use FriendsOfRedaxo\Warehouse\Customer;

// Bestellung aufgegeben
$order = Order::get($orderId);
$customer = Customer::getCurrent();

Logger::log(
    Logger::EVENT_ORDER_PLACED,
    sprintf('Bestellung #%s von Kunde %s aufgegeben', $order->getId(), $customer->getEmail()),
    $order->getId(),
    null,
    null,
    [
        'customer_id' => $customer->getId(),
        'order_total' => $order->getOrderTotal(),
        'payment_method' => $order->getPaymentType(),
        'items_count' => count($order->getItems()),
        'timestamp' => date('Y-m-d H:i:s')
    ]
);
```

### Zahlungsereignisse verfolgen

```php
use FriendsOfRedaxo\Warehouse\Logger;

// PayPal-Zahlung gestartet
Logger::log(
    Logger::EVENT_START_PAYMENT,
    'PayPal-Zahlung für Bestellung initiiert',
    $orderId,
    null,
    null,
    [
        'payment_provider' => 'paypal',
        'amount' => $orderTotal,
        'currency' => 'EUR',
        'paypal_order_id' => $paypalOrderId
    ]
);

// Zahlung erfolgreich
Logger::log(
    Logger::EVENT_PAYMENT_SUCCESS,
    'PayPal-Zahlung erfolgreich abgeschlossen',
    $orderId,
    null,
    null,
    [
        'payment_provider' => 'paypal',
        'transaction_id' => $transactionId,
        'amount_paid' => $paidAmount,
        'fee' => $paypalFee
    ]
);

// Zahlung fehlgeschlagen
Logger::log(
    Logger::EVENT_PAYMENT_FAILED,
    'PayPal-Zahlung fehlgeschlagen',
    $orderId,
    null,
    null,
    [
        'payment_provider' => 'paypal',
        'error_code' => $errorCode,
        'error_message' => $errorMessage,
        'retry_attempt' => $retryCount
    ]
);
```

### Benutzer-Aktivitäten protokollieren

```php
use FriendsOfRedaxo\Warehouse\Logger;
use rex_ycom_auth;

// Benutzer-Login
$user = rex_ycom_auth::getUser();
if ($user) {
    Logger::log(
        Logger::EVENT_LOGIN,
        sprintf('Benutzer %s (%s) hat sich angemeldet', $user->getValue('email'), $user->getValue('firstname')),
        null,
        null,
        null,
        [
            'user_id' => $user->getId(),
            'email' => $user->getValue('email'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]
    );
}
```

## Log-Format

Die Log-Einträge werden im CSV-Format gespeichert mit folgender Struktur:

```csv
Datum,Zeit,Event,Message,Order-ID,Artikel-ID,Varianten-ID,Parameter-JSON
```

**Beispiel-Log-Eintrag:**
```csv
2024-01-15,14:30:25,add_cart,"Artikel T-Shirt Größe L zum Warenkorb hinzugefügt",,"123","456","{""quantity"":2,""price"":19.95,""user_session"":""abc123""}"
```

## Integration in eigene Funktionen

### Eigene Logger-Wrapper

```php
class ShopLogger {
    
    public static function logCartAction(string $action, $article, int $quantity, array $context = []) {
        $params = array_merge([
            'quantity' => $quantity,
            'article_name' => $article->getName(),
            'article_price' => $article->getPrice(),
        ], $context);
        
        Logger::log(
            'cart_' . $action,
            sprintf('Warenkorb-Aktion: %s für Artikel "%s"', $action, $article->getName()),
            null,
            $article->getId(),
            null,
            $params
        );
    }
    
    public static function logOrderStatus(Order $order, string $newStatus, string $oldStatus = null) {
        Logger::log(
            Logger::EVENT_ORDER_SHIPPED, // oder entsprechendes Event
            sprintf('Bestellstatus geändert von "%s" zu "%s"', $oldStatus, $newStatus),
            $order->getId(),
            null,
            null,
            [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => rex_backend_login::createUser()->getLogin() ?? 'system'
            ]
        );
    }
}
```

### Extension Points Integration

```php
// In der boot.php oder project addon
rex_extension::register('WAREHOUSE_CART_ADD', function(rex_extension_point $ep) {
    $article = $ep->getParam('article');
    $quantity = $ep->getParam('quantity');
    
    Logger::log(
        Logger::EVENT_ADD_CART,
        sprintf('Artikel "%s" hinzugefügt', $article->getName()),
        null,
        $article->getId(),
        null,
        ['quantity' => $quantity]
    );
});
```

## Performance-Überlegungen

### Conditional Logging

```php
// Nur in wichtigen Fällen loggen
if (Logger::isActive() && $order->getOrderTotal() > 1000) {
    Logger::log(
        Logger::EVENT_ORDER_PLACED,
        'Hochwertige Bestellung aufgegeben',
        $order->getId(),
        null,
        null,
        ['high_value' => true, 'amount' => $order->getOrderTotal()]
    );
}
```

### Batch-Logging für mehrere Events

```php
// Mehrere verwandte Events sammeln und auf einmal loggen
$cartEvents = [];
foreach ($cartItems as $item) {
    $cartEvents[] = [
        'event' => Logger::EVENT_ADD_CART,
        'message' => 'Artikel hinzugefügt: ' . $item['name'],
        'article_id' => $item['id'],
        'params' => ['quantity' => $item['quantity']]
    ];
}

// Alle Events auf einmal loggen
foreach ($cartEvents as $event) {
    Logger::log(
        $event['event'],
        $event['message'],
        null,
        $event['article_id'],
        null,
        $event['params']
    );
}
```

## Log-Auswertung

### Log-Datei lesen

```php
// Log-Einträge analysieren
$logFile = Logger::logFile();
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES);
    
    foreach ($lines as $line) {
        $data = str_getcsv($line);
        if (count($data) >= 6) {
            list($date, $time, $event, $message, $order_id, $article_id, $variant_id, $params_json) = $data;
            
            $params = json_decode($params_json, true) ?: [];
            
            echo sprintf("[%s %s] %s: %s\n", $date, $time, $event, $message);
        }
    }
}
```

### Statistiken erstellen

```php
function getLogStatistics(): array {
    $stats = ['events' => [], 'total' => 0];
    $logFile = Logger::logFile();
    
    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES);
        
        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) >= 3) {
                $event = $data[2];
                $stats['events'][$event] = ($stats['events'][$event] ?? 0) + 1;
                $stats['total']++;
            }
        }
    }
    
    return $stats;
}

// Verwendung
$stats = getLogStatistics();
echo "Gesamt-Events: " . $stats['total'] . "\n";
foreach ($stats['events'] as $event => $count) {
    echo "$event: $count\n";
}
```

## Konfiguration

### Logger-Einstellungen

```php
// Maximale Log-Dateigröße anpassen (in Bytes)
// Standard: 20 MB
Logger::$maxFileSize = 50000000; // 50 MB
```

### Automatische Log-Rotation

Das REDAXO-Logging-System rotiert automatisch Log-Dateien wenn die maximale Größe erreicht wird. Alte Logs werden mit einem Zeitstempel umbenannt.

## Troubleshooting

### Logger aktiviert sich nicht

```php
// Prüfen ob Addon verfügbar ist
$addon = rex_addon::get('warehouse');
if (!$addon->isAvailable()) {
    echo "Warehouse-Addon nicht verfügbar";
}

// Manuell Konfiguration setzen
$addon->setConfig('log', 1);
```

### Log-Datei nicht beschreibbar

```php
// Berechtigungen prüfen
$logFile = Logger::logFile();
$logDir = dirname($logFile);

if (!is_writable($logDir)) {
    echo "Log-Verzeichnis nicht beschreibbar: " . $logDir;
}
```
