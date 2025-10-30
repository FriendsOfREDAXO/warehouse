# Die Klasse `EMail`

Die EMail-Klasse ist für die E-Mail-Verwaltung im Warehouse-Addon vorgesehen. Aktuell ist die Klasse noch nicht implementiert, aber sie soll zukünftig die E-Mail-Funktionalität für Bestellbestätigungen, Rechnungen und andere Shop-Kommunikation bereitstellen.

> **Hinweis:** Diese Klasse ist derzeit noch nicht implementiert. Die folgende Dokumentation beschreibt die geplante Funktionalität.

## Geplante Übersicht

Die EMail-Klasse soll folgende Funktionen bereitstellen:

- Versendung von Bestellbestätigungen
- E-Mail-Templates für verschiedene Shop-Events
- Integration mit dem REDAXO PHPMailer-Addon
- Automatische E-Mail-Versendung bei Statusänderungen
- Mehrsprachige E-Mail-Templates

## Geplante Funktionalität

### E-Mail-Typen

Die Klasse soll verschiedene E-Mail-Typen unterstützen:

```php
// Geplante Konstanten
public const TYPE_ORDER_CONFIRMATION = 'order_confirmation';
public const TYPE_PAYMENT_CONFIRMATION = 'payment_confirmation';
public const TYPE_SHIPPING_NOTIFICATION = 'shipping_notification';
public const TYPE_INVOICE = 'invoice';
public const TYPE_DELIVERY_NOTE = 'delivery_note';
public const TYPE_ORDER_CANCELLED = 'order_cancelled';
public const TYPE_PASSWORD_RESET = 'password_reset';
public const TYPE_REGISTRATION_WELCOME = 'registration_welcome';
```

### Geplante Methoden

#### `sendOrderConfirmation(Order $order)`

Soll eine Bestellbestätigung an den Kunden senden.

```php
use FriendsOfRedaxo\Warehouse\EMail;
use FriendsOfRedaxo\Warehouse\Order;

// Geplante Verwendung
$order = Order::get($orderId);
$success = EMail::sendOrderConfirmation($order);
```

#### `sendPaymentConfirmation(Order $order)`

Soll eine Zahlungsbestätigung senden.

```php
// Geplante Verwendung
$order = Order::get($orderId);
EMail::sendPaymentConfirmation($order);
```

#### `sendShippingNotification(Order $order)`

Soll eine Versandbenachrichtigung senden.

```php
// Geplante Verwendung
$order = Order::get($orderId);
EMail::sendShippingNotification($order);
```

#### `sendCustomEmail(string $type, array $data, string $recipient)`

Soll eine benutzerdefinierte E-Mail senden.

```php
// Geplante Verwendung
EMail::sendCustomEmail(
    'custom_notification',
    ['customer_name' => 'Max Mustermann', 'message' => 'Ihre Bestellung ist bereit'],
    'customer@example.com'
);
```

## Template-Integration

### E-Mail-Templates

Die E-Mail-Templates sollen im Fragment-System integriert werden:

```
fragments/warehouse/email/
├── order_confirmation.php
├── payment_confirmation.php
├── shipping_notification.php
├── invoice.php
└── delivery_note.php
```

### Template-Struktur

```php
// Geplante Template-Struktur
<!-- fragments/warehouse/email/order_confirmation.php -->
<?php
/** @var Order $order */
/** @var Customer $customer */
?>
<html>
<head>
    <title>Bestellbestätigung #<?= $order->getId() ?></title>
</head>
<body>
    <h1>Vielen Dank für Ihre Bestellung!</h1>
    
    <p>Liebe/r <?= $customer->getFirstname() ?> <?= $customer->getLastname() ?>,</p>
    
    <p>wir haben Ihre Bestellung erhalten und bearbeiten sie schnellstmöglich.</p>
    
    <h2>Bestelldetails</h2>
    <p><strong>Bestellnummer:</strong> <?= $order->getId() ?></p>
    <p><strong>Bestelldatum:</strong> <?= date('d.m.Y H:i', strtotime($order->getCreatedate())) ?></p>
    
    <!-- Weitere Bestelldetails -->
    
</body>
</html>
```

## PHPMailer-Integration

Die Klasse soll das REDAXO PHPMailer-Addon nutzen:

```php
// Geplante Implementierung
use rex_mailer;

class EMail
{
    public static function send(string $type, array $data): bool
    {
        $mailer = rex_mailer::factory();
        
        // Template laden und rendern
        $template = self::renderTemplate($type, $data);
        
        // E-Mail konfigurieren
        $mailer->addAddress($data['recipient_email'], $data['recipient_name'] ?? '');
        $mailer->Subject = self::getSubject($type, $data);
        $mailer->Body = $template['html'];
        $mailer->AltBody = $template['text'];
        
        return $mailer->send();
    }
    
    private static function renderTemplate(string $type, array $data): array
    {
        // Template-Rendering mit rex_fragment
        $fragment = new rex_fragment();
        
        foreach ($data as $key => $value) {
            $fragment->setVar($key, $value);
        }
        
        $html = $fragment->parse('warehouse/email/' . $type . '.php');
        $text = strip_tags($html); // Vereinfachte Text-Version
        
        return [
            'html' => $html,
            'text' => $text
        ];
    }
}
```

## Konfiguration

### E-Mail-Einstellungen

```php
// Geplante Konfigurationsoptionen
Warehouse::setConfig('email_from_address', 'shop@example.com');
Warehouse::setConfig('email_from_name', 'Mein Online-Shop');
Warehouse::setConfig('email_reply_to', 'support@example.com');
Warehouse::setConfig('email_bcc', 'orders@example.com');
```

### Template-Konfiguration

```php
// Geplante Template-Konfiguration
Warehouse::setConfig('email_templates', [
    'order_confirmation' => [
        'subject' => 'Bestellbestätigung #{order_id}',
        'template' => 'order_confirmation.php',
        'enabled' => true
    ],
    'payment_confirmation' => [
        'subject' => 'Zahlungsbestätigung für Bestellung #{order_id}',
        'template' => 'payment_confirmation.php',
        'enabled' => true
    ]
]);
```

## Event-basierte E-Mails

### Extension Points

```php
// Geplante Extension Points für automatische E-Mails
rex_extension::register('WAREHOUSE_ORDER_PLACED', function(rex_extension_point $ep) {
    $order = $ep->getParam('order');
    EMail::sendOrderConfirmation($order);
});

rex_extension::register('WAREHOUSE_PAYMENT_SUCCESS', function(rex_extension_point $ep) {
    $order = $ep->getParam('order');
    EMail::sendPaymentConfirmation($order);
});

rex_extension::register('WAREHOUSE_ORDER_SHIPPED', function(rex_extension_point $ep) {
    $order = $ep->getParam('order');
    EMail::sendShippingNotification($order);
});
```

## Mehrsprachigkeit

### Sprach-abhängige Templates

```php
// Geplante Mehrsprachen-Unterstützung
fragments/warehouse/email/
├── de_de/
│   ├── order_confirmation.php
│   └── payment_confirmation.php
├── en_gb/
│   ├── order_confirmation.php
│   └── payment_confirmation.php
└── default/
    ├── order_confirmation.php
    └── payment_confirmation.php
```

### Sprachauswahl

```php
// Geplante Implementierung
public static function sendOrderConfirmation(Order $order): bool
{
    $customer = $order->getCustomer();
    $language = $customer ? $customer->getLanguage() : 'de_de';
    
    return self::send('order_confirmation', [
        'order' => $order,
        'customer' => $customer,
        'language' => $language,
        'recipient_email' => $order->getEmail()
    ]);
}
```

## Anhänge

### PDF-Anhänge

```php
// Geplante PDF-Anhang-Funktionalität
public static function sendInvoiceWithPDF(Order $order): bool
{
    // PDF generieren
    $pdfContent = Document::generateInvoicePDF($order);
    $pdfPath = rex_path::addonData('warehouse', 'temp/invoice_' . $order->getId() . '.pdf');
    file_put_contents($pdfPath, $pdfContent);
    
    // E-Mail mit Anhang senden
    $mailer = rex_mailer::factory();
    $mailer->addAttachment($pdfPath, 'Rechnung_' . $order->getId() . '.pdf');
    
    // ... weitere E-Mail-Konfiguration
    
    $result = $mailer->send();
    
    // Temporäre Datei löschen
    unlink($pdfPath);
    
    return $result;
}
```

## Logging und Debugging

### E-Mail-Logging

```php
// Geplante Logging-Integration
public static function send(string $type, array $data): bool
{
    $success = false;
    
    try {
        // E-Mail senden
        $success = self::sendMail($type, $data);
        
        // Erfolg loggen
        Logger::log(
            'email_sent',
            sprintf('E-Mail "%s" erfolgreich gesendet an %s', $type, $data['recipient_email']),
            $data['order_id'] ?? null,
            null,
            null,
            ['email_type' => $type, 'recipient' => $data['recipient_email']]
        );
        
    } catch (Exception $e) {
        // Fehler loggen
        Logger::log(
            'email_failed',
            sprintf('E-Mail "%s" konnte nicht gesendet werden: %s', $type, $e->getMessage()),
            $data['order_id'] ?? null,
            null,
            null,
            ['email_type' => $type, 'error' => $e->getMessage()]
        );
    }
    
    return $success;
}
```

## Warteschlange (Queue)

### Asynchrone E-Mail-Versendung

```php
// Geplante Queue-Funktionalität
public static function queueEmail(string $type, array $data): bool
{
    // E-Mail in Warteschlange einreihen statt sofort zu senden
    $queueEntry = [
        'type' => $type,
        'data' => $data,
        'created' => date('Y-m-d H:i:s'),
        'attempts' => 0,
        'status' => 'pending'
    ];
    
    // In Datenbank speichern
    $sql = rex_sql::factory();
    $sql->setTable('rex_warehouse_email_queue');
    $sql->setValues($queueEntry);
    $sql->insert();
    
    return true;
}

public static function processQueue(): int
{
    $processed = 0;
    
    // Warteschlange abarbeiten
    $queue = rex_sql::factory();
    $queue->setQuery("SELECT * FROM rex_warehouse_email_queue WHERE status = 'pending' LIMIT 10");
    
    foreach ($queue->getArray() as $item) {
        if (self::send($item['type'], json_decode($item['data'], true))) {
            // Erfolgreich gesendet - aus Warteschlange entfernen
            rex_sql::factory()
                ->setTable('rex_warehouse_email_queue')
                ->setWhere('id = :id', ['id' => $item['id']])
                ->delete();
            
            $processed++;
        }
    }
    
    return $processed;
}
```

## Implementierungsrichtlinien

Wenn die EMail-Klasse implementiert wird, sollten folgende Prinzipien beachtet werden:

### 1. Dependency Injection

```php
// Testbarkeit durch Dependency Injection
class EMail
{
    private static $mailerFactory;
    
    public static function setMailerFactory(callable $factory): void
    {
        self::$mailerFactory = $factory;
    }
    
    private static function getMailer()
    {
        return self::$mailerFactory ? (self::$mailerFactory)() : rex_mailer::factory();
    }
}
```

### 2. Template-Flexibilität

```php
// Ermögliche überschreibbare Templates
private static function getTemplatePath(string $type, string $language = null): string
{
    $language = $language ?: rex_clang::getCurrentId();
    
    // Prüfe spezifische Sprache
    $specificPath = "warehouse/email/{$language}/{$type}.php";
    if (rex_fragment::exists($specificPath)) {
        return $specificPath;
    }
    
    // Fallback zu Standard
    return "warehouse/email/default/{$type}.php";
}
```

### 3. Fehlerbehandlung

```php
// Robuste Fehlerbehandlung
public static function send(string $type, array $data): bool
{
    try {
        // Validierung
        if (!self::validateEmailData($type, $data)) {
            throw new InvalidArgumentException('Ungültige E-Mail-Daten');
        }
        
        // Template existiert?
        if (!self::templateExists($type)) {
            throw new RuntimeException('E-Mail-Template nicht gefunden: ' . $type);
        }
        
        // Senden
        return self::sendMail($type, $data);
        
    } catch (Exception $e) {
        // Logging
        Logger::log('email_error', $e->getMessage(), null, null, null, [
            'type' => $type,
            'exception' => get_class($e)
        ]);
        
        return false;
    }
}
```

## Migration und Upgrades

Wenn die EMail-Klasse implementiert wird, sollte eine Migrationsstrategie existieren:

```php
// Migration für bestehende Installationen
public static function migrate(): void
{
    // Standard Templates kopieren
    self::installDefaultTemplates();
    
    // Konfiguration migrieren
    self::migrateEmailConfiguration();
    
    // Queue-Tabelle erstellen
    self::createQueueTable();
}
```

Diese Dokumentation dient als Leitfaden für die zukünftige Implementierung der EMail-Klasse und zeigt die erwartete Funktionalität auf.
