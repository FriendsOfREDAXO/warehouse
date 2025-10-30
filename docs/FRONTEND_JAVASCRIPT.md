# Frontend JavaScript Dokumentation

## Übersicht

Ab Version 2.2.0 verwendet das Warehouse-Addon ein zentralisiertes JavaScript-System. Alle Frontend-Interaktionen werden durch die Datei `/assets/js/init.js` verwaltet, die inline-Scripts in den Fragmenten ersetzt.

## Architektur-Prinzipien

### 1. Zentrale JavaScript-Datei

Alle JavaScript-Funktionen sind in `/assets/js/init.js` konsolidiert:
- Warenkorb-Interaktionen
- Artikel-Detail-Funktionalität
- Checkout-Formular-Logik
- Globale Hilfsfunktionen

### 2. Data-Attribut-System

Statt CSS-Klassen oder IDs werden konsequent `data-warehouse-*` Attribute verwendet:
- **Vorteile:**
  - Klare Trennung von Styling und Funktionalität
  - Mehrfachverwendung von Elementen auf derselben Seite
  - Konsistente Namenskonvention
  - Bessere Wartbarkeit

### 3. Sicherheit

- Keine inline-Scripts (CSP-konform)
- Verwendung von Nonces für externe Scripts
- Keine inline-Event-Handler (`onclick`, `onchange`, etc.)

## Verwendete Data-Attribute

### Globale Attribute

#### `data-warehouse-cart-count`
Zeigt die Anzahl der Artikel im Warenkorb an. Wird automatisch aktualisiert.

```html
<span data-warehouse-cart-count>0</span>
```

### Warenkorb-Seite (`data-warehouse-cart-page`)

Haupt-Container für die Warenkorb-Seite.

```html
<div data-warehouse-cart-page>
    <!-- Warenkorb-Inhalt -->
</div>
```

#### Mengenänderung
- `data-warehouse-cart-quantity`: Action (modify)
- `data-warehouse-mode`: +, - oder set
- `data-warehouse-article-id`: Artikel-ID
- `data-warehouse-variant-id`: Varianten-ID (optional)
- `data-warehouse-amount`: Menge der Änderung

```html
<button data-warehouse-cart-quantity="modify" 
        data-warehouse-mode="+" 
        data-warehouse-article-id="123" 
        data-warehouse-variant-id="" 
        data-warehouse-amount="1">+</button>
```

#### Eingabefeld
- `data-warehouse-cart-input`: Markiert Eingabefeld
- `data-warehouse-item-key`: Eindeutiger Schlüssel für das Item

```html
<input data-warehouse-cart-input
       data-warehouse-article-id="123"
       data-warehouse-item-key="item_123"
       value="2">
```

#### Artikel löschen
- `data-warehouse-cart-delete`: Markiert Löschen-Button
- `data-warehouse-confirm`: Bestätigungsnachricht (optional)

```html
<button data-warehouse-cart-delete
        data-warehouse-article-id="123"
        data-warehouse-confirm="Wirklich entfernen?">
    Entfernen
</button>
```

#### Preisanzeige
- `data-warehouse-item-total`: Zeigt Gesamtpreis eines Items
- `data-warehouse-cart-subtotal`: Zeigt Warenkorb-Zwischensumme

```html
<div data-warehouse-item-total="item_123">49,99 €</div>
<div data-warehouse-cart-subtotal>99,98 €</div>
```

### Offcanvas-Warenkorb (`data-warehouse-offcanvas-cart`)

Haupt-Container für den Offcanvas-Warenkorb.

```html
<div data-warehouse-offcanvas-cart 
     data-warehouse-empty-message="Ihr Warenkorb ist leer">
    <div data-warehouse-offcanvas-body>
        <!-- Inhalt -->
    </div>
</div>
```

#### Spezifische Attribute
- `data-warehouse-offcanvas-body`: Container für Warenkorb-Inhalt
- `data-warehouse-item-amount`: Zeigt Artikel-Menge
- `data-warehouse-item-price`: Zeigt Einzelpreis
- `data-warehouse-cart-empty`: Warenkorb-Leeren-Button
- `data-warehouse-offcanvas-subtotal`: Zwischensumme

### Warenkorb-Tabelle (`data-warehouse-cart-table`)

Alternative Darstellung als Tabelle.

```html
<table data-warehouse-cart-table>
    <!-- Tabelleninhalt -->
</table>
```

#### Spezifische Attribute
- `data-warehouse-table-subtotal`: Zwischensumme in Tabelle
- `data-warehouse-cart-next`: Weiter-Button mit Loading-Animation

### Artikel-Detail (`data-warehouse-article-detail`)

Haupt-Container für Artikeldetails.

```html
<div data-warehouse-article-detail>
    <!-- Artikeldetails -->
</div>
```

#### Mengenauswahl
- `data-warehouse-quantity-switch`: Wert für +/-
- `data-warehouse-quantity-input`: ID des Input-Feldes
- `data-warehouse-quantity-input` (am Input): Markiert Mengen-Eingabefeld

```html
<button data-warehouse-quantity-switch="-1" 
        data-warehouse-quantity-input="qty_input">-</button>
<input id="qty_input" data-warehouse-quantity-input value="1">
<button data-warehouse-quantity-switch="+1" 
        data-warehouse-quantity-input="qty_input">+</button>
```

#### Preisanzeige mit Staffelpreisen
- `data-warehouse-price-display`: Container für Preisanzeige
- `data-warehouse-base-price`: Basispreis
- `data-warehouse-bulk-prices`: JSON-Array mit Staffelpreisen
- `data-warehouse-price-value`: Element für Preiswert

```html
<div data-warehouse-price-display 
     data-warehouse-base-price="29.99"
     data-warehouse-bulk-prices='[{"min":10,"max":50,"price":"24.99"}]'>
    <span data-warehouse-price-value>29,99 €</span>
</div>
```

#### Zum Warenkorb hinzufügen
- `data-warehouse-add-form`: Formular zum Hinzufügen

```html
<form data-warehouse-add-form>
    <input type="hidden" name="article_id" value="123">
    <input type="number" name="order_count" value="1">
    <button type="submit">In den Warenkorb</button>
</form>
```

#### Varianten
- `data-warehouse-variant`: Markiert Varianten-Button
- `data-warehouse-variant-id`: Varianten-ID
- `data-warehouse-variant-price`: Varianten-Preis

```html
<button data-warehouse-variant
        data-warehouse-variant-id="456"
        data-warehouse-variant-price="34.99">
    Größe L
</button>
```

### Checkout-Formular (`data-warehouse-checkout-form`)

Haupt-Container für Checkout-Formular.

```html
<form data-warehouse-checkout-form>
    <!-- Formularfelder -->
</form>
```

#### Lieferadresse
- `data-warehouse-shipping-toggle`: Checkbox für abweichende Lieferadresse
- `data-warehouse-shipping-fields`: Container für Lieferadress-Felder
- `data-warehouse-has-data`: Gibt an, ob bereits Daten vorhanden sind

```html
<input type="checkbox" data-warehouse-shipping-toggle>
<div data-warehouse-shipping-fields 
     data-warehouse-has-data="false" 
     style="display: none;">
    <!-- Lieferadress-Felder -->
</div>
```

## JavaScript-API

### Globale Funktionen

#### `updateGlobalCartCount(itemsCount)`

Aktualisiert alle Elemente mit `data-warehouse-cart-count`.

```javascript
// Warenkorb-Anzahl manuell aktualisieren
if (window.updateGlobalCartCount) {
    window.updateGlobalCartCount(5);
}
```

### Interne Funktionen

Diese Funktionen sind in der IIFE gekapselt und nicht direkt zugänglich:

- `updateCart(action, articleId, variantId, amount, mode, onSuccess, onError)`
- `formatCurrency(value, currency, locale)`
- `initCartPage()`, `initOffcanvasCart()`, `initCartTable()`
- `initArticleDetail()`, `initCheckoutForm()`

## Integration in Templates

### Standard-Integration

Fügen Sie die init.js in Ihr Template ein:

```php
<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" 
        nonce="<?= rex_response::getNonce() ?>"></script>
```

### Automatische Integration

Die folgenden Fragmente binden init.js automatisch ein:
- `cart_page.php`
- `offcanvas_cart.php`
- `cart.php`
- `article/details.php`
- `checkout/form-guest.php`

### Custom-Integration

Wenn Sie eigene Fragmente erstellen:

1. Verwenden Sie `data-warehouse-*` Attribute für Ihre Elemente
2. Binden Sie init.js einmalig im Template ein
3. Die Event-Handler werden automatisch registriert

## Best Practices

### 1. Eindeutige Item-Keys

Verwenden Sie eindeutige Keys für Warenkorb-Items:

```php
$item_key = $item['article_id'] . ($item['variant_id'] ? '_' . $item['variant_id'] : '');
```

### 2. Mehrfachverwendung

Nutzen Sie die Möglichkeit, Elemente mehrfach zu verwenden:

```html
<!-- Im Header -->
<span data-warehouse-cart-count><?= Cart::create()->count() ?></span>

<!-- Im Footer -->
<span data-warehouse-cart-count><?= Cart::create()->count() ?></span>
```

Beide werden automatisch aktualisiert.

### 3. Bestätigungsnachrichten

Nutzen Sie `data-warehouse-confirm` für Nutzerfreundlichkeit:

```html
<button data-warehouse-cart-delete
        data-warehouse-confirm="<?= rex_i18n::msg('confirm_remove') ?>">
```

### 4. Varianten-Unterstützung

Prüfen Sie immer, ob eine Variante vorhanden ist:

```php
data-warehouse-variant-id="<?= $item['variant_id'] ?? '' ?>"
```

### 5. Nonce-Verwendung

Verwenden Sie immer Nonces für Script-Tags:

```php
nonce="<?= rex_response::getNonce() ?>"
```

## Migration von älteren Versionen

### Schritt 1: CSS-Klassen ersetzen

Alt:
```html
<button class="cart-delete-btn" 
        data-article-id="123">
```

Neu:
```html
<button data-warehouse-cart-delete
        data-warehouse-article-id="123">
```

### Schritt 2: Inline-Scripts entfernen

Alt:
```html
<script nonce="...">
    document.querySelector('.cart-btn').addEventListener('click', ...);
</script>
```

Neu:
```html
<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" 
        nonce="<?= rex_response::getNonce() ?>"></script>
```

### Schritt 3: IDs durch Data-Attribute ersetzen

Alt:
```html
<div id="cart-subtotal">99,99 €</div>
```

Neu:
```html
<div data-warehouse-cart-subtotal>99,99 €</div>
```

## Troubleshooting

### Warenkorb-Anzahl wird nicht aktualisiert

1. Prüfen Sie, ob init.js geladen ist
2. Prüfen Sie die Browser-Konsole auf Fehler
3. Verifizieren Sie, dass `data-warehouse-cart-count` gesetzt ist

### Buttons reagieren nicht

1. Prüfen Sie, ob der Container das richtige data-Attribut hat (z.B. `data-warehouse-cart-page`)
2. Prüfen Sie, ob alle erforderlichen Attribute gesetzt sind
3. Prüfen Sie die Browser-Konsole auf JavaScript-Fehler

### Staffelpreise werden nicht aktualisiert

1. Prüfen Sie das Format des `data-warehouse-bulk-prices` JSON
2. Verifizieren Sie, dass `data-warehouse-price-display` am Container gesetzt ist
3. Prüfen Sie, ob `data-warehouse-price-value` am Preis-Element vorhanden ist

## Support und Weiterentwicklung

- GitHub: https://github.com/FriendsOfREDAXO/warehouse
- Issues: https://github.com/FriendsOfREDAXO/warehouse/issues
- Dokumentation: `/docs/` im Repository
