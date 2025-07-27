# Die Klasse `Payment`

Die Payment-Klasse verwaltet die verfügbaren Zahlungsarten im Warehouse-Addon. Sie bietet Methoden zur Konfiguration und Abfrage von Zahlungsoptionen mit Extension Points für individuelle Erweiterungen.

> Die Payment-Klasse ist als statische Utility-Klasse konzipiert und verwaltet die Zahlungsarten-Konfiguration.

## Übersicht

Die Payment-Klasse stellt folgende Funktionen bereit:

- Definition der verfügbaren Zahlungsarten
- Konfiguration erlaubter Zahlungsarten
- Extension Points für eigene Zahlungsarten
- Integration in den Checkout-Prozess

## Konstanten

### `PAYMENT_OPTIONS`

Definiert die Standard-Zahlungsarten des Warehouse-Addons:

- `'prepayment'`: Vorkasse
- `'invoice'`: Kauf auf Rechnung
- `'paypal'`: PayPal
- `'direct_debit'`: Lastschrift

```php
use FriendsOfRedaxo\Warehouse\Payment;

// Alle verfügbaren Standard-Zahlungsarten
$options = Payment::PAYMENT_OPTIONS;
```

## Methoden und Beispiele

### `getPaymentOptions()`

Gibt alle verfügbaren Zahlungsarten zurück, einschließlich der über Extension Points hinzugefügten.

**Rückgabe:** Array mit Zahlungsarten (Key => Label)

```php
use FriendsOfRedaxo\Warehouse\Payment;

// Alle verfügbaren Zahlungsarten abrufen
$paymentOptions = Payment::getPaymentOptions();

foreach ($paymentOptions as $key => $label) {
    echo $key . ': ' . rex_i18n::msg($label) . '<br>';
}
```

### `getAllowedPaymentOptions()`

Gibt nur die in der Konfiguration aktivierten Zahlungsarten zurück.

**Rückgabe:** Array mit erlaubten Zahlungsarten (Key => Label)

```php
use FriendsOfRedaxo\Warehouse\Payment;

// Nur erlaubte Zahlungsarten abrufen
$allowedOptions = Payment::getAllowedPaymentOptions();

// Im Checkout-Formular verwenden
foreach ($allowedOptions as $key => $label) {
    echo '<input type="radio" name="payment_method" value="' . $key . '">';
    echo rex_i18n::msg($label) . '<br>';
}
```

## Extension Points

### `WAREHOUSE_PAYMENT_OPTIONS`

Ermöglicht das Hinzufügen eigener Zahlungsarten.

**Parameter:**
- Subject: Array der verfügbaren Zahlungsarten

**Beispiele:**

```php
// Eigene Zahlungsart hinzufügen
rex_extension::register('WAREHOUSE_PAYMENT_OPTIONS', function(rex_extension_point $ep) {
    $payment_options = $ep->getSubject();
    
    // Kreditkarte hinzufügen
    $payment_options['credit_card'] = 'warehouse.payment_options.credit_card';
    
    // Sofortüberweisung hinzufügen
    $payment_options['sofort'] = 'warehouse.payment_options.sofort';
    
    // Klarna hinzufügen
    $payment_options['klarna'] = 'warehouse.payment_options.klarna';
    
    return $payment_options;
});

// Bestimmte Zahlungsart entfernen
rex_extension::register('WAREHOUSE_PAYMENT_OPTIONS', function(rex_extension_point $ep) {
    $payment_options = $ep->getSubject();
    
    // PayPal deaktivieren
    unset($payment_options['paypal']);
    
    return $payment_options;
});
```

## Konfiguration

Die erlaubten Zahlungsarten werden in der REDAXO-Konfiguration gespeichert:

- `warehouse.allowed_payment_options`: String mit erlaubten Zahlungsarten (Format: `|key1|key2|key3|`)

**Standard-Konfiguration:** `|prepayment|invoice|direct_debit|`

### Konfiguration setzen

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

// Nur Vorkasse und Rechnung erlauben
Warehouse::setConfig('allowed_payment_options', '|prepayment|invoice|');

// Alle Zahlungsarten erlauben
Warehouse::setConfig('allowed_payment_options', '|prepayment|invoice|paypal|direct_debit|');
```

## Standard-Zahlungsarten

### Vorkasse (`prepayment`)

```php
// Prüfung auf Vorkasse
if ($payment_method === 'prepayment') {
    // Bankdaten anzeigen
    // Bestellung als "Zahlung ausstehend" markieren
}
```

### Rechnung (`invoice`)

```php
// Prüfung auf Rechnung
if ($payment_method === 'invoice') {
    // Rechnung generieren
    // Zahlungsfrist setzen
}
```

### PayPal (`paypal`)

```php
// Prüfung auf PayPal
if ($payment_method === 'paypal') {
    // PayPal-Integration verwenden
    // Weiterleitung zu PayPal
}
```

### Lastschrift (`direct_debit`)

```php
// Prüfung auf Lastschrift
if ($payment_method === 'direct_debit') {
    // SEPA-Formular anzeigen
    // Lastschrift-Mandat erstellen
}
```

## Integration in Templates

### Checkout-Formular

```php
<?php
use FriendsOfRedaxo\Warehouse\Payment;

$allowedPayments = Payment::getAllowedPaymentOptions();
?>

<fieldset>
    <legend>Zahlungsart wählen</legend>
    
    <?php foreach ($allowedPayments as $key => $label): ?>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="payment_method" 
               value="<?= $key ?>" id="payment_<?= $key ?>" required>
        <label class="form-check-label" for="payment_<?= $key ?>">
            <?= rex_i18n::msg($label) ?>
        </label>
    </div>
    <?php endforeach; ?>
</fieldset>
```

### Zahlungsart-spezifische Inhalte

```php
<?php
$selectedPayment = rex_request('payment_method', 'string');
?>

<div id="payment-details">
    <?php switch ($selectedPayment): ?>
        <?php case 'prepayment': ?>
            <div class="alert alert-info">
                <h5>Bankverbindung für Vorkasse:</h5>
                <p>IBAN: DE12 3456 7890 1234 5678 90<br>
                   BIC: ABCDEFGH<br>
                   Verwendungszweck: Bestellung #<?= $order_number ?></p>
            </div>
            <?php break; ?>
            
        <?php case 'paypal': ?>
            <div class="paypal-button-container">
                <!-- PayPal-Button wird hier eingefügt -->
            </div>
            <?php break; ?>
            
        <?php case 'direct_debit': ?>
            <div class="form-group">
                <label for="iban">IBAN:</label>
                <input type="text" name="iban" id="iban" class="form-control" required>
            </div>
            <?php break; ?>
    <?php endswitch; ?>
</div>
```

## Erweiterte Zahlungsarten-Integration

### Stripe-Integration

```php
rex_extension::register('WAREHOUSE_PAYMENT_OPTIONS', function(rex_extension_point $ep) {
    $payment_options = $ep->getSubject();
    $payment_options['stripe'] = 'warehouse.payment_options.stripe';
    return $payment_options;
});

// Stripe-spezifische Behandlung
rex_extension::register('WAREHOUSE_PAYMENT_PROCESS', function(rex_extension_point $ep) {
    $payment_method = $ep->getParam('payment_method');
    $order = $ep->getParam('order');
    
    if ($payment_method === 'stripe') {
        // Stripe-Payment-Intent erstellen
        // Zahlung verarbeiten
    }
});
```

### Amazon Pay

```php
rex_extension::register('WAREHOUSE_PAYMENT_OPTIONS', function(rex_extension_point $ep) {
    $payment_options = $ep->getSubject();
    $payment_options['amazon_pay'] = 'warehouse.payment_options.amazon_pay';
    return $payment_options;
});
```

## Validierung

```php
use FriendsOfRedaxo\Warehouse\Payment;

// Zahlungsart validieren
function validatePaymentMethod($payment_method) {
    $allowedPayments = Payment::getAllowedPaymentOptions();
    return array_key_exists($payment_method, $allowedPayments);
}

// Verwendung
$payment_method = rex_request('payment_method', 'string');
if (!validatePaymentMethod($payment_method)) {
    throw new Exception('Ungültige Zahlungsart ausgewählt');
}
```

## Sprachdateien

Für eigene Zahlungsarten sollten entsprechende Sprachschlüssel definiert werden:

```lang
# In der lang/de_de.lang Datei
warehouse.payment_options.prepayment = Vorkasse
warehouse.payment_options.invoice = Kauf auf Rechnung
warehouse.payment_options.paypal = PayPal
warehouse.payment_options.direct_debit = Lastschrift
warehouse.payment_options.credit_card = Kreditkarte
warehouse.payment_options.sofort = Sofortüberweisung
```
