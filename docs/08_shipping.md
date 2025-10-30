# Die Klasse `Shipping`

Die Shipping-Klasse verwaltet die Berechnung von Versandkosten im Warehouse-Addon. Sie bietet verschiedene Berechnungsmodi und Extension Points für individuelle Anpassungen.

> Die Shipping-Klasse ist als statische Utility-Klasse konzipiert und bietet Methoden zur Versandkostenberechnung.

## Berechnungsmodi

Die Klasse unterstützt verschiedene Modi zur Berechnung der Versandkosten:

## Konstanten

### `CALCULATION_MODE_OPTIONS`

Definiert die verfügbaren Berechnungsmodi für Versandkosten:

- `''` (Standard): Versandkostenfrei ab Bestellwert
- `'quantity'`: Berechnung nach Anzahl der Artikel
- `'weight'`: Berechnung nach Gewicht
- `'order_total'`: Erweiterte Berechnung nach Bestellwert

## Methoden und Beispiele

### `getCost()`

Berechnet die Versandkosten für den aktuellen Warenkorb.

**Rückgabe:** Float-Wert der Versandkosten

```php
use FriendsOfRedaxo\Warehouse\Shipping;

// Versandkosten berechnen
$shippingCost = Shipping::getCost();
echo "Versandkosten: " . $shippingCost . "€";
```

Die Methode berücksichtigt folgende Faktoren:

- Warenkorb-Gesamtwert
- Gesamtgewicht der Artikel
- Anzahl der Artikel
- Konfigurierte Versandkosten
- Grenzwert für versandkostenfreie Lieferung
- Ausgewählter Berechnungsmodus

### `getCostFormatted()`

Gibt die Versandkosten als formatierten String mit Währungssymbol zurück.

**Rückgabe:** String mit formatiertem Preis

```php
use FriendsOfRedaxo\Warehouse\Shipping;

// Formatierte Versandkosten
echo Shipping::getCostFormatted(); // z.B. "€ 4,95"
```

## Berechnungslogik

### Standard-Modus (`''`)

```php
// Beispiel: Versandkostenfrei ab 50€
if ($bestellwert >= 50.00) {
    $versandkosten = 0;
} else {
    $versandkosten = 4.95; // Standardversandkosten
}
```

### Mengen-Modus (`'quantity'`)

Berechnung basierend auf der Anzahl der Artikel im Warenkorb.

```php
// Beispiel: Versandkostenfrei ab 5 Artikeln
if ($anzahl_artikel >= 5) {
    $versandkosten = 0;
}
```

### Gewichts-Modus (`'weight'`)

Berechnung basierend auf dem Gesamtgewicht der Bestellung.

```php
// Beispiel: Versandkostenfrei ab 2kg
if ($gesamtgewicht >= 2.0) {
    $versandkosten = 0;
}
```

### Erweiterte Bestellwert-Berechnung (`'order_total'`)

Erweiterte Logik für die Berechnung nach Bestellwert (aktuell identisch mit Standard-Modus).

## Extension Point

### `WAREHOUSE_CART_SHIPPING_COST`

Ermöglicht die individuelle Anpassung der Versandkostenberechnung.

**Parameter:**

- `cart`: Aktueller Warenkorb
- `total_weight`: Gesamtgewicht
- `total_pieces`: Anzahl Artikel
- `total_price`: Gesamtpreis
- `free_shipping_from`: Grenzwert für versandkostenfreie Lieferung
- `shipping_fee`: Standardversandkosten
- `minimum_order_value`: Mindestbestellwert
- `shipping_calculation_mode`: Berechnungsmodus

```php
rex_extension::register('WAREHOUSE_CART_SHIPPING_COST', function(rex_extension_point $ep) {
    $shipping_cost = $ep->getSubject();
    $params = $ep->getParams();
    
    // Beispiel: Spezielle Versandkostenberechnung
    if ($params['total_weight'] > 10) {
        return 15.00; // Schwere Pakete
    }
    
    return $shipping_cost;
});
```

## Konfiguration

Die Versandkostenberechnung basiert auf folgenden REDAXO-Konfigurationswerten:

- `warehouse.shipping_fee`: Standardversandkosten
- `warehouse.free_shipping_from`: Grenzwert für versandkostenfreie Lieferung
- `warehouse.minimum_order_value`: Mindestbestellwert
- `warehouse.shipping_calculation_mode`: Berechnungsmodus

## Integration

Die Shipping-Klasse wird automatisch in Warenkorb und Checkout-Prozess integriert:

```php
// Verwendung im Template
$cart = Cart::get();
$shipping = Shipping::getCost();
$total = $cart->getTotal() + $shipping;

echo "Warenwert: " . number_format($cart->getTotal(), 2) . "€<br>";
echo "Versand: " . Shipping::getCostFormatted() . "<br>";
echo "Gesamt: " . number_format($total, 2) . "€";
```
