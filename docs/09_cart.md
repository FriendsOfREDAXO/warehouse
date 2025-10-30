# Die Klasse `Cart`

Die Cart-Klasse verwaltet den Warenkorb im Warehouse-Addon. Sie ermöglicht das Hinzufügen, Entfernen und Modifizieren von Artikeln sowie die Berechnung von Gesamtpreisen und Gewichten. Mit der neuen Version unterstützt sie auch Kundendaten und Adressverwaltung.

> Die Cart-Klasse ist als Singleton konzipiert und speichert die Warenkorb-Daten in der PHP-Session.

## Warenkorb-Struktur

Der Warenkorb enthält folgende Hauptbereiche:

- `items`: Array mit den Warenkorb-Artikeln (mit eindeutigen Schlüsseln)
- `customer`: Referenz auf den Kunden (falls angemeldet)
- `customer_data`: Kundendaten für Gastkäufe
- `billing_address`: Referenz auf die Rechnungsadresse
- `delivery_address`: Referenz auf die Lieferadresse (falls abweichend)
- `last_update`: Zeitstempel der letzten Aktualisierung

### Artikel-Struktur im Warenkorb

Jeder Artikel im Warenkorb wird mit folgendem Schema gespeichert:

```php
[
    'type' => 'article|variant',      // Typ: Artikel oder Variante
    'article_id' => 123,              // ID des Hauptartikels
    'variant_id' => null|456,         // ID der Variante (falls vorhanden)
    'name' => 'Produktname',          // Name zum Zeitpunkt des Hinzufügens
    'price' => 19.99,                 // Preis zum Zeitpunkt des Hinzufügens
    'amount' => 2,                    // Anzahl
    'total' => 39.98,                 // Gesamtpreis (price * amount)
    'added_at' => 1640995200          // Zeitstempel des Hinzufügens
]
```

**Wichtig:** Preise werden zum Zeitpunkt des Hinzufügens zum Warenkorb gespeichert, um historische Preise zu bewahren.

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

## Kundendaten und Adressen

### `setCustomer(?Customer $customer)`

Verknüpft einen registrierten Kunden mit dem Warenkorb.

**Parameter:**

- `$customer` (Customer|null): Kunden-Objekt oder null zum Entfernen

```php
$cart = Cart::get();
$customer = Customer::get(123);
$cart->setCustomer($customer);
```

### `getCustomer(): ?Customer`

Gibt den mit dem Warenkorb verknüpften Kunden zurück.

```php
$cart = Cart::get();
$customer = $cart->getCustomer();
if ($customer) {
    echo "Kunde: " . $customer->getName();
}
```

### `setCustomerData(array $customer_data)`

Setzt Kundendaten für Gastkäufe (ohne Registrierung).

**Parameter:**

- `$customer_data` (array): Array mit Kundendaten

```php
$cart = Cart::get();
$cart->setCustomerData([
    'firstname' => 'Max',
    'lastname' => 'Mustermann',
    'email' => 'max@example.com',
    'phone' => '+49 123 456789',
    'address' => 'Musterstraße 1',
    'zip' => '12345',
    'city' => 'Musterstadt'
]);
```

### `getCustomerData(): array`

Gibt die Gastkunden-Daten zurück.

```php
$cart = Cart::get();
$data = $cart->getCustomerData();
echo $data['firstname'] . ' ' . $data['lastname'];
```

### `setBillingAddress(?CustomerAddress $address)`

Setzt die Rechnungsadresse für den Warenkorb.

```php
$cart = Cart::get();
$address = CustomerAddress::get(456);
$cart->setBillingAddress($address);
```

### `getBillingAddress(): ?CustomerAddress`

Gibt die Rechnungsadresse zurück.

### `setDeliveryAddress(?CustomerAddress $address)`

Setzt eine abweichende Lieferadresse.

**Hinweis:** Falls keine Lieferadresse gesetzt ist, wird die Rechnungsadresse verwendet.

### `getDeliveryAddress(): ?CustomerAddress`

Gibt die Lieferadresse zurück (oder die Rechnungsadresse als Fallback).

### `hasSeperateDeliveryAddress(): bool`

Prüft, ob eine separate Lieferadresse gesetzt ist.

```php
$cart = Cart::get();
### `hasSeparateDeliveryAddress(): bool`

Prüft, ob eine separate Lieferadresse gesetzt ist.

```php
$cart = Cart::get();
if ($cart->hasSeparateDeliveryAddress()) {
    echo "Lieferung an andere Adresse";
}
```

### `autoSetCustomerFromYCom()`

Erkennt automatisch angemeldete YCom-Benutzer und verknüpft entsprechende Kundendaten.

**Hinweis:** Diese Methode wird automatisch beim Erstellen des Warenkorbs aufgerufen.

### `clearCustomerData()`

Löscht alle Kundendaten und Adressen aus dem Warenkorb.

```php
$cart = Cart::get();
$cart->clearCustomerData();
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

Leert den Warenkorb vollständig und entfernt alle checkout-bezogenen Daten aus der Session. Dies umfasst:

- Warenkorb-Daten (`warehouse_cart`)
- Kundendaten aus dem Checkout-Prozess (`user_data`)
- Zahlungsinformationen (`warehouse_payment`)

Wichtige Sessions wie YCom-Anmeldung oder REDAXO-Backend-Session bleiben erhalten.

```php
Cart::empty();
echo "Warenkorb und Checkout-Daten wurden geleert.";
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
            <?php foreach ($cart->getItems() as $item_key => $item): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($item['name']) ?>
                    <?php if ($item['type'] === 'variant'): ?>
                        <small class="text-muted">(Variante)</small>
                    <?php endif; ?>
                </td>
                <td><?= $item['amount'] ?>x</td>
                <td><?= number_format($item['total'], 2) ?>€</td>
                <td>
                    <a href="?rex-api-call=warehouse_cart_api&action=delete&article_id=<?= $item['article_id'] ?>&variant_id=<?= $item['variant_id'] ?>">
                        Entfernen
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <div class="cart-total">
            <strong>Gesamt: <?= Cart::getCartTotalFormatted() ?></strong>
        </div>
    <?php endif; ?>
</div>
```

## Vollständiges Beispiel: Checkout-Prozess

```php
<?php
use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\CustomerAddress;

// Warenkorb laden
$cart = Cart::get();

// Überprüfung ob Warenkorb nicht leer ist
if ($cart->isEmpty()) {
    echo "Warenkorb ist leer";
    return;
}

// Kundendaten setzen (für Gast-Checkout)
if (!$cart->getCustomer()) {
    $cart->setCustomerData([
        'firstname' => $_POST['firstname'],
        'lastname' => $_POST['lastname'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'zip' => $_POST['zip'],
        'city' => $_POST['city']
    ]);
}

// Warenkorb validieren
$validation = Cart::validateCart();
if ($validation !== true) {
    echo "Validierungsfehler: " . $validation;
    return;
}

// Bestellung erstellen
$order_saved = Cart::saveAsOrder('paypal_payment_123');

if ($order_saved) {
    // Warenkorb leeren nach erfolgreicher Bestellung
    Cart::empty();
    echo "Bestellung erfolgreich erstellt!";
} else {
    echo "Fehler beim Erstellen der Bestellung";
}
```

## API-Endpunkt

Der Warenkorb kann über AJAX-Aufrufe verwaltet werden:

```javascript
// Artikel hinzufügen
fetch('?rex-api-call=warehouse_cart_api&action=add&article_id=123&variant_id=456&amount=2')
    .then(response => response.json())
    .then(cart => console.log('Warenkorb aktualisiert', cart));

// Artikel entfernen
fetch('?rex-api-call=warehouse_cart_api&action=delete&article_id=123&variant_id=456')
    .then(response => response.json())
    .then(cart => console.log('Artikel entfernt', cart));

// Menge ändern
fetch('?rex-api-call=warehouse_cart_api&action=modify&article_id=123&variant_id=456&amount=1&mode=+')
    .then(response => response.json())
    .then(cart => console.log('Menge erhöht', cart));
```

## Warenkorb-Verhalten

### Flache Artikel-Struktur

Seit Version 2.4.0 verwendet der Warenkorb eine konsequent flache Struktur, in der sowohl Artikel als auch Varianten als einzelne Einträge in der Items-Liste existieren. Dies vereinfacht die Handhabung und macht das Verhalten konsistenter.

**Beispiel der flachen Struktur:**

```
* Artikel 1 Variante 1
* Artikel 1 Variante 2  
* Artikel 2
* Artikel 3
* Artikel 4 Variante 1
```

Jeder Eintrag ist ein eigenständiges Cart-Item mit einheitlichen Feldern, unabhängig davon, ob es sich um einen einfachen Artikel oder eine Variante handelt.

## Migration von älteren Versionen

Falls Sie von einer älteren Cart-Version migrieren, beachten Sie folgende Änderungen:

1. **Neue Artikelschlüssel**: Statt UUIDs werden jetzt `article_id` (+ `_variant_id`) als Schlüssel verwendet
2. **Preisspeicherung**: Preise werden beim Hinzufügen gespeichert, nicht mehr dynamisch berechnet
3. **Kundendaten**: Neue Methoden für Customer- und Address-Management
4. **Variantensupport**: Verbesserte Unterstützung für Artikel mit Varianten
5. **Einheitliche Feldnamen**: Alle Cart-Items verwenden jetzt die gleichen Feldnamen (`article_id`, `variant_id`, `name`, `amount`, `total`), unabhängig davon ob es sich um Artikel oder Varianten handelt

### Wichtige Feldname-Änderungen:

| Alt | Neu | Beschreibung |
|-----|-----|-------------|
| `id`, `whid` | `article_id` | ID des Hauptartikels |
| `var_whvarid` | `variant_id` | ID der Variante (falls vorhanden) |
| `count` | `amount` | Anzahl |
| `article_name`, `var_bezeichnung` | `name` | Produktname |
| `type` | `type` | Typ: 'article' oder 'variant' |

### Beispiel-Migration

```php
// Alt (vor Version 2.1)
foreach ($cart_items as $uuid => $item) {
    echo $item['id'] . ': ' . $item['name'];
}

// Neu (ab Version 2.1)
foreach ($cart_items as $item_key => $item) {
    echo $item['article_id'] . ': ' . $item['name'];
    if ($item['type'] === 'variant') {
        echo ' (Variante: ' . $item['variant_id'] . ')';
    }
}
```

## Automatische Aktualisierung der Warenkorb-Anzeige

Ab Version 2.2.0 verwendet das Warehouse-Addon ein zentralisiertes JavaScript-System für alle Warenkorb-Interaktionen. Die gesamte JavaScript-Funktionalität ist in der Datei `/assets/js/init.js` konsolidiert, wodurch inline-Scripts vermieden und die Wartbarkeit verbessert wird.

### Frontend-Integration

Um die JavaScript-Funktionalität zu nutzen, binden Sie die `init.js` in Ihrem Template ein:

```php
<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" nonce="<?= rex_response::getNonce() ?>"></script>
```

Die Fragmente `cart_page.php`, `offcanvas_cart.php`, `cart.php`, `article/details.php` und `checkout/form-guest.php` binden dieses Script automatisch ein.

### Verwendung von data-warehouse-* Attributen

Das System verwendet konsistent `data-warehouse-*` Attribute für alle Selektoren. Dies ermöglicht eine klare Trennung zwischen Styling (CSS-Klassen) und Funktionalität (data-Attribute).

#### Warenkorb-Anzahl anzeigen

Um die Artikelanzahl im Warenkorb anzuzeigen (z.B. in der Navigation), fügen Sie das Attribut `data-warehouse-cart-count` hinzu:

```html
<span class="badge" data-warehouse-cart-count><?= Cart::create()->count() ?></span>
```

Alle Elemente mit diesem Attribut werden automatisch aktualisiert, wenn sich der Warenkorb ändert - ohne dass die Seite neu geladen werden muss.

#### Artikel zum Warenkorb hinzufügen

Formular mit data-Attributen für die Artikeldetailseite:

```html
<div data-warehouse-article-detail>
    <form data-warehouse-add-form>
        <input type="hidden" name="article_id" value="123">
        <input type="number" name="order_count" value="1" data-warehouse-quantity-input>
        <button type="submit">In den Warenkorb</button>
    </form>
</div>
```

#### Mengenselektor mit +/- Buttons

```html
<div data-warehouse-article-detail>
    <button data-warehouse-quantity-switch="-1" 
            data-warehouse-quantity-input="quantity_input_id">-</button>
    <input id="quantity_input_id" type="number" value="1" data-warehouse-quantity-input>
    <button data-warehouse-quantity-switch="+1" 
            data-warehouse-quantity-input="quantity_input_id">+</button>
</div>
```

#### Warenkorb-Seite mit Mengenänderung

Die Warenkorb-Seite verwendet folgende Attribute:

```html
<div data-warehouse-cart-page>
    <!-- Mengenänderung -->
    <button data-warehouse-cart-quantity="modify" 
            data-warehouse-mode="-" 
            data-warehouse-article-id="123" 
            data-warehouse-variant-id="" 
            data-warehouse-amount="1">-</button>
    
    <input data-warehouse-cart-input
           data-warehouse-article-id="123" 
           data-warehouse-variant-id=""
           data-warehouse-item-key="item_123"
           value="2">
    
    <button data-warehouse-cart-quantity="modify" 
            data-warehouse-mode="+" 
            data-warehouse-article-id="123" 
            data-warehouse-variant-id="" 
            data-warehouse-amount="1">+</button>
    
    <!-- Artikel entfernen -->
    <button data-warehouse-cart-delete
            data-warehouse-article-id="123" 
            data-warehouse-variant-id=""
            data-warehouse-confirm="Artikel wirklich entfernen?">
        Entfernen
    </button>
    
    <!-- Preisanzeige -->
    <div data-warehouse-item-total="item_123">49,99 €</div>
    <div data-warehouse-cart-subtotal>99,98 €</div>
</div>
```

#### Offcanvas-Warenkorb

```html
<div data-warehouse-offcanvas-cart 
     data-warehouse-empty-message="Ihr Warenkorb ist leer">
    <div data-warehouse-offcanvas-body>
        <!-- Warenkorb-Inhalt -->
        <span data-warehouse-item-amount="item_123">2</span>
        <span data-warehouse-item-price="item_123">24,99 €</span>
        <span data-warehouse-item-total="item_123">49,98 €</span>
        
        <!-- Löschen-Button -->
        <button data-warehouse-cart-delete
                data-warehouse-article-id="123"
                data-warehouse-variant-id=""
                data-warehouse-confirm="Artikel entfernen?">×</button>
        
        <!-- Warenkorb leeren -->
        <button data-warehouse-cart-empty
                data-warehouse-confirm="Warenkorb leeren?">
            Warenkorb leeren
        </button>
        
        <div data-warehouse-offcanvas-subtotal>99,98 €</div>
    </div>
</div>
```

#### Checkout-Formular mit abweichender Lieferadresse

```html
<form data-warehouse-checkout-form>
    <input type="checkbox" data-warehouse-shipping-toggle>
    
    <div data-warehouse-shipping-fields 
         data-warehouse-has-data="false" 
         style="display: none;">
        <!-- Lieferadress-Felder -->
    </div>
</form>
```

### Technische Details

Die Aktualisierung erfolgt durch eine globale JavaScript-Funktion `updateGlobalCartCount(itemsCount)`, die automatisch von allen Warenkorb-Update-Funktionen aufgerufen wird. Diese Funktion ist auch global verfügbar für benutzerdefinierte Erweiterungen:

```javascript
// Manuelles Aktualisieren der Warenkorb-Anzahl
if (window.updateGlobalCartCount) {
    window.updateGlobalCartCount(5); // Setzt Anzahl auf 5
}
```

Die zentrale JavaScript-Datei `/assets/js/init.js` enthält:
- Globale Warenkorb-Anzahl-Aktualisierung
- Cart API-Kommunikation
- Währungsformatierung
- Handler für Warenkorb-Seite, Offcanvas-Cart und Warenkorb-Tabelle
- Handler für Artikeldetails mit Staffelpreisen
- Handler für Checkout-Formulare

### Vorteile des data-Attribut-Systems

1. **Mehrfachverwendung**: Elemente können mehrfach auf derselben Seite vorkommen (z.B. Warenkorb-Anzahl in Kopf- und Fußzeile)
2. **Klare Trennung**: Styling (CSS-Klassen) und Funktionalität (data-Attribute) sind getrennt
3. **Wartbarkeit**: Zentrale JavaScript-Datei statt inline-Scripts in jedem Fragment
4. **Sicherheit**: Verwendung von Nonces für Script-Tags, kein inline-CSS oder inline-JS
5. **Konsistenz**: Einheitliches Namensschema (`data-warehouse-*`) für alle Selektoren
6. **Performance**: JavaScript wird nur einmal geladen und gecacht

Alle Warenkorb-Fragmente sind bereits für dieses System konfiguriert und verwenden die standardisierten data-Attribute.
