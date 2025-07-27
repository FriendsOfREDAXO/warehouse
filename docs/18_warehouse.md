# Die Klasse `Warehouse`

Die Warehouse-Klasse ist die zentrale Hauptklasse des Warehouse-Addons. Sie stellt grundlegende Funktionen, Konfigurationsmethoden und Utility-Funktionen für das gesamte Addon bereit.

> Die Warehouse-Klasse fungiert als zentrale Service-Klasse mit statischen Methoden für alle wichtigen Addon-Funktionen.

## Übersicht

Die Warehouse-Klasse bietet folgende Hauptfunktionen:

- Konfigurationsverwaltung
- Währungsformatierung
- Bestellungsformatierung (Text/HTML)
- Template-Rendering
- Feature-Management
- Session-Verwaltung

## Konstanten

### Backend-Pfade

```php
public const PATH_ARTICLE = 'warehouse/article/';
public const PATH_ARTICLE_VARIANT = 'warehouse/article_variant/';
public const PATH_CATEGORY = 'warehouse/category/';
public const PATH_ORDER = 'warehouse/order/list';
public const PATH_ORDER_DETAIL = 'warehouse/order/detail';
```

### YCom-Modi

```php
public const YCOM_MODES = [
    'enforce_account' => 'warehouse.ycom_mode.enforce_account',
    'choose' => 'warehouse.ycom_mode.choose',
    'guest_only' => 'warehouse.ycom_mode.guest_only',
];
```

## Konfigurationsmethoden

### `getConfig(string $key, mixed $default = null)`

Liest einen Konfigurationswert aus der Warehouse-Konfiguration.

**Parameter:**
- `$key` (string): Konfigurationsschlüssel
- `$default` (mixed): Standardwert falls Schlüssel nicht existiert

**Rückgabe:** Mixed - Konfigurationswert

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

// Währung abrufen
$currency = Warehouse::getConfig('currency', 'EUR');

// Versandkosten abrufen
$shippingFee = Warehouse::getConfig('shipping_fee', 0.0);

// Mit Standardwert
$taxRate = Warehouse::getConfig('default_tax_rate', 19);
```

### `setConfig(string $key, mixed $value)`

Setzt einen Konfigurationswert in der Warehouse-Konfiguration.

**Parameter:**
- `$key` (string): Konfigurationsschlüssel
- `$value` (mixed): Zu setzender Wert

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

// Währung setzen
Warehouse::setConfig('currency', 'EUR');

// Versandkosten setzen
Warehouse::setConfig('shipping_fee', 4.95);

// Demo-Modus aktivieren
Warehouse::setConfig('demo_mode', true);
```

### `getLabel(string $key)`

Gibt ein übersetztes Label aus der Warehouse-Konfiguration zurück.

**Parameter:**
- `$key` (string): Label-Schlüssel (ohne 'label_' Präfix)

**Rückgabe:** String - Übersetztes Label

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

// Labels abrufen
echo Warehouse::getLabel('cart'); // "Warenkorb"
echo Warehouse::getLabel('checkout'); // "Zur Kasse"
echo Warehouse::getLabel('add_to_cart'); // "In den Warenkorb"
```

## Währungsfunktionen

### `getCurrencySign()`

Gibt das Währungssymbol basierend auf der konfigurierten Währung zurück.

**Rückgabe:** String - Währungssymbol

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

$currencySign = Warehouse::getCurrencySign();
echo "Preis: 19,95 " . $currencySign; // "Preis: 19,95 €"
```

## Feature-Management

### `getEnabledFeatures()`

Gibt ein Array der aktivierten Features zurück.

**Rückgabe:** Array - Liste der aktivierten Features

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

$features = Warehouse::getEnabledFeatures();
// z.B. ['bulk_prices', 'weight', 'variants', 'stock']
```

### Feature-Prüfmethoden

```php
use FriendsOfREDAXO\Warehouse\Warehouse;

// Staffelpreise aktiviert?
if (Warehouse::isBulkPricesEnabled()) {
    // Staffelpreise-Logik
}

// Gewichtsberechnung aktiviert?
if (Warehouse::isWeightEnabled()) {
    // Gewichts-Logik
}

// Varianten aktiviert?
if (Warehouse::isVariantsEnabled()) {
    // Varianten-Logik
}

// Lagerbestände aktiviert?
if (Warehouse::isStockEnabled()) {
    // Lagerbestand-Logik
}
```

## Bestellungsformatierung

### `getOrderAsText()`

Formatiert den aktuellen Warenkorb als Text für E-Mails oder Rechnungen.

**Rückgabe:** String - Formatierte Bestellung als Text

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

$orderText = Warehouse::getOrderAsText();
echo '<pre>' . $orderText . '</pre>';
```

**Ausgabe-Beispiel:**
```
Art. Nr.            Artikel                              Anzahl   €        €
------------------------------------------------------------------------------------
WH-001              Beispiel-Artikel                          2    19,95    39,90
                    Steuer: 19% = 7,58
------------------------------------------------------------------------------------
Summe                                                                       39,90
Mehrwertsteuer                                                               7,58
Versand                                                                      4,95
------------------------------------------------------------------------------------
Total                                                                       52,43
====================================================================================
```

### `getOrderAsHtml()`

Formatiert den aktuellen Warenkorb als HTML-Tabelle.

**Rückgabe:** String - Formatierte Bestellung als HTML

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

$orderHtml = Warehouse::getOrderAsHtml();
echo $orderHtml;
```

## Kundendaten-Verwaltung

### `getCustomerData()`

Gibt die aktuellen Kundendaten aus der Session zurück.

**Rückgabe:** Array - Kundendaten

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

$customerData = Warehouse::getCustomerData();
echo "Kunde: " . $customerData['firstname'] . ' ' . $customerData['lastname'];
```

### `getCustomerDataAsText()`

Formatiert die Kundendaten als Text für E-Mails oder Dokumente.

**Rückgabe:** String - Formatierte Kundendaten

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

$customerText = Warehouse::getCustomerDataAsText();
echo '<pre>' . $customerText . '</pre>';
```

### `saveCustomerInSession($params)`

Speichert Kundendaten in der Session (wird aus YForm-Actions aufgerufen).

**Parameter:**
- `$params` (object): YForm-Parameter-Objekt

```php
// Wird automatisch aus YForm-Actions aufgerufen
// rex_yform_manager_action::addAction('warehouse_save_customer', 'Warehouse::saveCustomerInSession');
```

## Template-Rendering

### `parse(string $file, array $values = [])`

Rendert ein Warehouse-Template mit den übergebenen Variablen.

**Parameter:**
- `$file` (string): Template-Pfad relativ zum Framework-Ordner
- `$values` (array): Variablen für das Template

**Rückgabe:** String - Gerendertes HTML

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

// Warenkorb rendern
echo Warehouse::parse('cart/cart.php', [
    'title' => 'Mein Warenkorb',
    'show_shipping' => true
]);

// Artikel-Liste rendern
echo Warehouse::parse('article/list.php', [
    'category' => $category,
    'articles' => $articles,
    'limit' => 12
]);
```

## Framework-Integration

Das Template-System unterstützt verschiedene Frontend-Frameworks:

- **Bootstrap 5** (Standard): `fragments/warehouse/bootstrap5/`
- **UIKit**: `fragments/warehouse/uikit/`
- **Custom**: Eigene Framework-Ordner

```php
// Framework in Konfiguration setzen
Warehouse::setConfig('framework', 'bootstrap5');
```

## Preismodus-Verwaltung

### `getPriceInputMode()`

Gibt den globalen Modus für die Preiseingabe zurück.

**Rückgabe:** String - 'net' oder 'gross'

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

$priceMode = Warehouse::getPriceInputMode();

if ($priceMode === 'gross') {
    echo "Preise werden brutto eingegeben";
} else {
    echo "Preise werden netto eingegeben";
}
```

## Utility-Funktionen

### `getCategoryPath(int $cat_id)`

Gibt den Kategoriepfad als Array zurück.

**Parameter:**
- `$cat_id` (int): Kategorie-ID

**Rückgabe:** Array - Kategoriepfad von Root bis zur angegebenen Kategorie

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

$path = Warehouse::getCategoryPath(123);
foreach ($path as $category) {
    echo $category->getName() . ' > ';
}
```

### `isDemoMode()`

Prüft, ob der Demo-Modus aktiviert ist.

**Rückgabe:** Boolean

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

if (Warehouse::isDemoMode()) {
    echo "Demo-Modus ist aktiviert";
}
```

### `restore_session_from_payment_id($payment_id)`

Stellt eine Session basierend auf einer Payment-ID wieder her.

**Parameter:**
- `$payment_id` (string): Payment-ID

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

// Session für PayPal-Rückkehr wiederherstellen
Warehouse::restore_session_from_payment_id($payment_id);
```

## Checkout-Integration

### `callbackCheckoutRedirect($params)`

Callback-Funktion für Checkout-Weiterleitungen basierend auf der Zahlungsart.

**Parameter:**
- `$params` (object): YForm-Parameter-Objekt

```php
// Wird automatisch aus YForm-Actions aufgerufen
// Leitet je nach Zahlungsart weiter (z.B. zu PayPal)
```

## Verwendung in Templates

### Basis-Template-Struktur

```php
<?php
use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Cart;

// Konfiguration abrufen
$currency = Warehouse::getCurrencySign();
$labels = [
    'cart' => Warehouse::getLabel('cart'),
    'checkout' => Warehouse::getLabel('checkout'),
    'add_to_cart' => Warehouse::getLabel('add_to_cart')
];
?>

<div class="warehouse-shop">
    <h1><?= $labels['cart'] ?></h1>
    
    <?= Warehouse::parse('cart/cart.php', [
        'currency' => $currency,
        'labels' => $labels
    ]); ?>
    
    <a href="<?= rex_getUrl($checkout_article_id) ?>" class="btn btn-primary">
        <?= $labels['checkout'] ?>
    </a>
</div>
```

### Feature-abhängige Templates

```php
<?php
use FriendsOfRedaxo\Warehouse\Warehouse;

$article = $this->getVar('article');
?>

<div class="product">
    <h2><?= $article->getName() ?></h2>
    
    <?php if (Warehouse::isVariantsEnabled()): ?>
        <?= Warehouse::parse('article/variants.php', ['article' => $article]); ?>
    <?php endif; ?>
    
    <?php if (Warehouse::isBulkPricesEnabled()): ?>
        <?= Warehouse::parse('article/bulk_prices.php', ['article' => $article]); ?>
    <?php endif; ?>
    
    <?php if (Warehouse::isStockEnabled()): ?>
        <div class="stock-info">
            Lagerbestand: <?= $article->getStock() ?>
        </div>
    <?php endif; ?>
</div>
```
