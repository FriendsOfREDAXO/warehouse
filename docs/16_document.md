# Die Klasse `Document`

Die Document-Klasse verwaltet die Nummerierung und Generierung von Warehouse-Dokumenten wie Bestellnummern, Lieferscheinnummern und Rechnungsnummern.

> Die Document-Klasse bietet statische Methoden zur Verwaltung von Dokumentnummern mit Extension Points für individuelle Anpassungen.

## Übersicht

Die Document-Klasse stellt zentrale Funktionen für die Dokumentenverwaltung bereit:

- Bestellnummern-Generierung
- Lieferscheinnummern-Generierung  
- Rechnungsnummern-Generierung
- Extension Points für individuelle Nummerierungslogik

## Methoden und Beispiele

### `getOrderNumber()`

Gibt die aktuelle Bestellnummer zurück. Die Nummer kann über einen Extension Point modifiziert werden.

**Rückgabe:** Integer (Bestellnummer)

```php
use FriendsOfRedaxo\Warehouse\Document;

// Aktuelle Bestellnummer abrufen
$orderNumber = Document::getOrderNumber();
echo "Nächste Bestellnummer: " . $orderNumber;
```

**Extension Point:** `WAREHOUSE_ORDER_NUMBER`

```php
// Beispiel: Bestellnummer mit Präfix
rex_extension::register('WAREHOUSE_ORDER_NUMBER', function(rex_extension_point $ep) {
    $number = $ep->getSubject();
    return 'WH-' . str_pad($number, 6, '0', STR_PAD_LEFT);
});
```

### `getDeliveryNoteNumber()`

Gibt die aktuelle Lieferscheinnummer zurück. Die Nummer kann über einen Extension Point modifiziert werden.

**Rückgabe:** Integer (Lieferscheinnummer)

```php
use FriendsOfRedaxo\Warehouse\Document;

// Aktuelle Lieferscheinnummer abrufen
$deliveryNumber = Document::getDeliveryNoteNumber();
echo "Nächste Lieferscheinnummer: " . $deliveryNumber;
```

**Extension Point:** `WAREHOUSE_DELIVERY_NOTE_NUMBER`

```php
// Beispiel: Lieferscheinnummer mit Jahresprefix
rex_extension::register('WAREHOUSE_DELIVERY_NOTE_NUMBER', function(rex_extension_point $ep) {
    $number = $ep->getSubject();
    $year = date('Y');
    return $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
});
```

### `generateOrderNo()`

Generiert und gibt die nächste Bestellnummer als String zurück. Die Nummer kann über einen Extension Point modifiziert werden. Standardformat: `YYYY-MM-####` mit monatlicher Zurücksetzung.

**Rückgabe:** String (Bestellnummer)

```php
use FriendsOfRedaxo\Warehouse\Document;

// Nächste Bestellnummer generieren
$orderNo = Document::generateOrderNo();
echo "Neue Bestellnummer: " . $orderNo; // z.B. "2024-01-0001"
```

**Extension Point:** `WAREHOUSE_ORDER_NO_GENERATE`

```php
// Beispiel: Bestellnummer mit eigenem Präfix
rex_extension::register('WAREHOUSE_ORDER_NO_GENERATE', function(rex_extension_point $ep) {
    $orderNo = $ep->getSubject();
    $params = $ep->getParams();
    
    return 'WH-' . $orderNo;
});

// Beispiel: Komplett eigenes Format
rex_extension::register('WAREHOUSE_ORDER_NO_GENERATE', function(rex_extension_point $ep) {
    $params = $ep->getParams();
    $year = $params['year'];
    $counter = $params['counter'];
    
    return $year . sprintf('%06d', $counter);
});
```

### `assignOrderNo()`

Weist einer Bestellung automatisch eine Bestellnummer zu, wenn sie noch keine hat.

```php
use FriendsOfRedaxo\Warehouse\Document;
use FriendsOfRedaxo\Warehouse\Order;

// Bestellung erstellen und Nummer zuweisen
$order = Order::create();
Document::assignOrderNo($order);
$order->save();

echo "Zugewiesene Bestellnummer: " . $order->getOrderNo();
```

### `getInvoiceNumber()`

Gibt die aktuelle Rechnungsnummer zurück. Die Nummer kann über einen Extension Point modifiziert werden.

**Rückgabe:** Integer (Rechnungsnummer)

```php
use FriendsOfRedaxo\Warehouse\Document;

// Aktuelle Rechnungsnummer abrufen
$invoiceNumber = Document::getInvoiceNumber();
echo "Nächste Rechnungsnummer: " . $invoiceNumber;
```

**Extension Point:** `WAREHOUSE_INVOICE_NUMBER`

```php
// Beispiel: Rechnungsnummer mit Monats-/Jahresformat
rex_extension::register('WAREHOUSE_INVOICE_NUMBER', function(rex_extension_point $ep) {
    $number = $ep->getSubject();
    $date = date('Ym');
    return $date . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
});
```

## Konfiguration

Die Dokumentnummern werden in der REDAXO-Konfiguration gespeichert:

- `warehouse.order_number`: Aktuelle Bestellnummer (Standard: 1)
- `warehouse.delivery_note_number`: Aktuelle Lieferscheinnummer (Standard: 1)
- `warehouse.invoice_number`: Aktuelle Rechnungsnummer (Standard: 1)

## Extension Points

### `WAREHOUSE_ORDER_NUMBER`

Ermöglicht die Anpassung der Bestellnummern-Generierung.

**Parameter:**

- Subject: Aktuelle Bestellnummer (Integer)

**Beispiele:**

```php
// Einfaches Format mit Präfix
rex_extension::register('WAREHOUSE_ORDER_NUMBER', function(rex_extension_point $ep) {
    return 'B' . $ep->getSubject();
});

// Format mit aktueller Jahreszahl
rex_extension::register('WAREHOUSE_ORDER_NUMBER', function(rex_extension_point $ep) {
    $number = $ep->getSubject();
    return date('Y') . sprintf('%05d', $number);
});

// Komplexeres Format basierend auf Datum
rex_extension::register('WAREHOUSE_ORDER_NUMBER', function(rex_extension_point $ep) {
    $number = $ep->getSubject();
    $date = date('ymd');
    return "WH{$date}" . sprintf('%03d', $number);
});
```

### `WAREHOUSE_DELIVERY_NOTE_NUMBER`

Ermöglicht die Anpassung der Lieferscheinnummern-Generierung.

**Parameter:**

- Subject: Aktuelle Lieferscheinnummer (Integer)

**Beispiele:**

```php
// Lieferschein mit L-Präfix
rex_extension::register('WAREHOUSE_DELIVERY_NOTE_NUMBER', function(rex_extension_point $ep) {
    return 'L' . sprintf('%06d', $ep->getSubject());
});
```

### `WAREHOUSE_INVOICE_NUMBER`

Ermöglicht die Anpassung der Rechnungsnummern-Generierung.

**Parameter:**

- Subject: Aktuelle Rechnungsnummer (Integer)

**Beispiele:**

```php
// Rechnung mit R-Präfix und Jahresangabe
rex_extension::register('WAREHOUSE_INVOICE_NUMBER', function(rex_extension_point $ep) {
    $number = $ep->getSubject();
    $year = date('Y');
    return "R{$year}" . sprintf('%04d', $number);
});
```

## Verwendung in der Bestellabwicklung

```php
use FriendsOfRedaxo\Warehouse\Document;
use FriendsOfRedaxo\Warehouse\Order;

// Bei Bestellerstellung
$order = Order::create();
$order->setOrderNumber(Document::getOrderNumber());
$order->save();

// Bei Lieferung
$deliveryNumber = Document::getDeliveryNoteNumber();
$order->setDeliveryNoteNumber($deliveryNumber);

// Bei Rechnungsstellung
$invoiceNumber = Document::getInvoiceNumber();
$order->setInvoiceNumber($invoiceNumber);
```

## Nummerierung zurücksetzen

Die Nummerierung kann über die REDAXO-Konfiguration zurückgesetzt werden:

```php
use FriendsOfRedaxo\Warehouse\Warehouse;

// Bestellnummern auf 1000 setzen
Warehouse::setConfig('order_number', 1000);

// Lieferscheinnummern zurücksetzen
Warehouse::setConfig('delivery_note_number', 1);

// Rechnungsnummern auf Jahresbeginn setzen
Warehouse::setConfig('invoice_number', 1);
```

## Automatische Inkrementierung

Die Nummern werden automatisch erhöht, wenn eine neue Bestellung, ein Lieferschein oder eine Rechnung erstellt wird. Die Inkrementierung erfolgt in der jeweiligen Geschäftslogik des Warehouse-Addons.

## Best Practices

### Eindeutige Nummerierung

```php
// Sicherstellen, dass Nummern eindeutig sind
rex_extension::register('WAREHOUSE_ORDER_NUMBER', function(rex_extension_point $ep) {
    $number = $ep->getSubject();
    $unique_id = time() . $number;
    return $unique_id;
});
```

### Jahresbasierte Nummerierung

```php
// Nummerierung pro Jahr zurücksetzen
rex_extension::register('WAREHOUSE_ORDER_NUMBER', function(rex_extension_point $ep) {
    $number = $ep->getSubject();
    $year = date('Y');
    
    // Prüfe, ob neues Jahr begonnen hat
    $lastYear = rex_config::get('warehouse', 'last_order_year', $year);
    if ($year != $lastYear) {
        rex_config::set('warehouse', 'order_number', 1);
        rex_config::set('warehouse', 'last_order_year', $year);
        $number = 1;
    }
    
    return $year . sprintf('%05d', $number);
});
```

## Integration mit PDF-Generierung

```php
// In PDF-Templates verwenden
$orderNumber = Document::getOrderNumber();
$invoiceNumber = Document::getInvoiceNumber();

echo "Bestellnummer: " . $orderNumber;
echo "Rechnungsnummer: " . $invoiceNumber;
```
