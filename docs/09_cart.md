# Die Klasse `Cart`

Die Cart-Klasse verwaltet den Warenkorb im Warehouse-Addon. Sie ermöglicht das Hinzufügen, Entfernen und Modifizieren von Artikeln sowie die Berechnung von Gesamtpreisen und Gewichten.

> Die Cart-Klasse ist als Singleton konzipiert und speichert die Warenkorb-Daten in der PHP-Session.

## Warenkorb-Struktur

Der Warenkorb enthält folgende Hauptbereiche:

- `items`: Array mit den Warenkorb-Artikeln
- `address`: Adressdaten (optional)
- `last_update`: Zeitstempel der letzten Aktualisierung

## Instanziierung

```php
use FriendsOfRedaxo\Warehouse\Cart;

// Warenkorb-Instanz erhalten
$cart = Cart::get();
```

## Methoden und Beispiele

### `get()`

Gibt eine Warenkorb-Instanz zurück (Singleton-Pattern).

**Rückgabe:** Cart-Objekt

```php
use FriendsOfRedaxo\Warehouse\Cart;

$cart = Cart::get();
```

### `getItems()`

Gibt alle Artikel im Warenkorb zurück.

**Rückgabe:** Array mit Warenkorb-Artikeln

```php
$cart = Cart::get();
$items = $cart->getItems();

foreach ($items as $uuid => $item) {
    echo $item['name'] . " - " . $item['amount'] . "x<br>";
}
```

### `isEmpty()`

Prüft, ob der Warenkorb leer ist.

**Rückgabe:** Boolean

```php
$cart = Cart::get();

if ($cart->isEmpty()) {
    echo "Ihr Warenkorb ist leer.";
} else {
    echo "Sie haben " . $cart->count() . " Artikel im Warenkorb.";
}
```

### `count()`

Gibt die Gesamtanzahl der Artikel im Warenkorb zurück.

**Rückgabe:** Integer (Anzahl aller Artikel)

```php
$cart = Cart::get();
echo "Artikel im Warenkorb: " . $cart->count();
```

### `add(int $article_id, int $article_variant_id = null, int $quantity = 1)`

Fügt einen Artikel zum Warenkorb hinzu.

**Parameter:**

- `$article_id` (int): ID des Artikels
- `$article_variant_id` (int|null): ID der Variante (optional)
- `$quantity` (int): Anzahl (Standard: 1)

**Rückgabe:** Boolean (Erfolg)

```php
$cart = Cart::get();

// Artikel hinzufügen
$success = $cart->add(123, null, 2);

// Artikel mit Variante hinzufügen
$success = $cart->add(123, 456, 1);
```

### `modify(int $article_id, int $article_variant_id, int|false $quantity, string $mode = '=')`

Modifiziert die Anzahl eines Artikels im Warenkorb.

**Parameter:**

- `$article_id` (int): ID des Artikels
- `$article_variant_id` (int): ID der Variante
- `$quantity` (int|false): Neue Anzahl oder false zum Entfernen
- `$mode` (string): Modus ('=', '+', '-')

```php
$cart = Cart::get();

// Anzahl setzen
$cart->modify(123, 456, 5, '=');

// Anzahl erhöhen
$cart->modify(123, 456, 1, '+');

// Anzahl verringern
$cart->modify(123, 456, 1, '-');
```

### `remove(int $article_id, int $variant_id)`

Entfernt einen Artikel vollständig aus dem Warenkorb.

**Parameter:**

- `$article_id` (int): ID des Artikels
- `$variant_id` (int): ID der Variante

```php
$cart = Cart::get();
$cart->remove(123, 456);
```

### `update($items)`

Aktualisiert den gesamten Warenkorb mit neuen Daten.

**Parameter:**

- `$items` (array): Neue Warenkorb-Daten

```php
$cart = Cart::get();
$newItems = [
    'uuid1' => [
        'id' => 123,
        'name' => 'Artikel 1',
        'amount' => 2,
        'price' => 19.99
    ]
];
$cart->update($newItems);
```

## Statische Methoden

### `totalWeight()`

Berechnet das Gesamtgewicht aller Artikel im Warenkorb.

**Rückgabe:** Float (Gewicht in der konfigurierten Einheit)

```php
$totalWeight = Cart::totalWeight();
echo "Gesamtgewicht: " . $totalWeight . "kg";
```

### `getTotal()`

Berechnet die Gesamtsumme des Warenkorbs.

**Rückgabe:** Float (Gesamtpreis)

```php
$total = Cart::getTotal();
echo "Gesamtsumme: " . number_format($total, 2) . "€";
```

### `getCartTotal()`

Alternative Methode zur Gesamtsummen-Berechnung.

**Rückgabe:** Float

```php
$total = Cart::getCartTotal();
```

### `getCartTotalFormatted()`

Gibt die Gesamtsumme als formatierten String zurück.

**Rückgabe:** String mit formatiertem Preis

```php
echo Cart::getCartTotalFormatted(); // z.B. "€ 49,95"
```

### `getSubTotal()`

Berechnet die Zwischensumme (ohne Versandkosten).

**Rückgabe:** Float

```php
$subtotal = Cart::getSubTotal();
echo "Zwischensumme: " . number_format($subtotal, 2) . "€";
```

### `empty()`

Leert den Warenkorb vollständig.

```php
Cart::empty();
echo "Warenkorb wurde geleert.";
```

## Rabatt-Funktionen

### `getDiscountValue()`

Gibt den aktuellen Rabbatt-Wert zurück.

**Rückgabe:** Float

```php
$discount = Cart::getDiscountValue();
```

### `getDiscountValueFormatted()`

Gibt den Rabatt als formatierten String zurück.

**Rückgabe:** String

```php
echo Cart::getDiscountValueFormatted();
```

## Versandkosten

### `totalShippingCosts()`

Berechnet die Versandkosten für den aktuellen Warenkorb.

**Rückgabe:** Float

```php
$cart = Cart::get();
$shipping = $cart->totalShippingCosts();
echo "Versandkosten: " . number_format($shipping, 2) . "€";
```

## Session-Integration

Der Warenkorb wird automatisch in der PHP-Session gespeichert:

- Session-Key: `warehouse_cart`
- Persistierung über Seitenwechsel
- Automatische Initialisierung bei erstem Zugriff

## Demo-Modus

Für Entwicklungs- und Testzwecke kann ein Demo-Warenkorb aktiviert werden:

```php
$cart = Cart::get();
$cart->setDemoCart(); // Füllt den Warenkorb mit Testdaten
```

## Bestellungs-Integration

### `createOrderFromCart($user_data)`

Erstellt eine Bestellung aus dem aktuellen Warenkorb.

**Parameter:**

- `$user_data` (array): Kundendaten für die Bestellung

**Rückgabe:** Boolean (Erfolg)

```php
$cart = Cart::get();
$userData = [
    'firstname' => 'Max',
    'lastname' => 'Mustermann',
    'email' => 'max@example.com',
    'address' => 'Musterstraße 1',
    'zip' => '12345',
    'city' => 'Musterstadt'
];

$success = $cart->createOrderFromCart($userData);
```

## Verwendung im Template

```php
<?php
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Warehouse;

$cart = Cart::get();
?>

<div class="cart-summary">
    <h3>Warenkorb (<?= $cart->count() ?> Artikel)</h3>
    
    <?php if ($cart->isEmpty()): ?>
        <p>Ihr Warenkorb ist leer.</p>
    <?php else: ?>
        <table>
            <?php foreach ($cart->getItems() as $uuid => $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['amount'] ?>x</td>
                <td><?= number_format($item['total'], 2) ?>€</td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <div class="cart-total">
            <strong>Gesamt: <?= Cart::getCartTotalFormatted() ?></strong>
        </div>
    <?php endif; ?>
</div>
```
