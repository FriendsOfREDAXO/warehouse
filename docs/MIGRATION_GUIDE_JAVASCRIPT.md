# Migration Guide: Inline Scripts to data-warehouse-* System

## Übersicht

Diese Anleitung hilft Entwicklern bei der Migration von Custom-Fragmenten, die noch das alte System mit CSS-Klassen und inline-Scripts verwenden, zum neuen `data-warehouse-*` System mit zentralem JavaScript in `/assets/js/init.js`.

## Vorteile der Migration

- ✅ **Sicherheit**: CSP-konform, keine inline-Scripts
- ✅ **Wartbarkeit**: Zentrales JavaScript statt verstreuter inline-Scripts
- ✅ **Flexibilität**: Mehrfachverwendung von Elementen auf derselben Seite
- ✅ **Performance**: JavaScript wird nur einmal geladen und gecacht

## Schritt-für-Schritt-Anleitung

### 1. CSS-Klassen durch Data-Attribute ersetzen

#### Warenkorb-Buttons (Menge ändern)

**Vorher:**
```html
<button class="cart-quantity-btn" 
        data-action="modify" 
        data-mode="+" 
        data-article-id="123" 
        data-variant-id="456" 
        data-amount="1">+</button>
```

**Nachher:**
```html
<button data-warehouse-cart-quantity="modify" 
        data-warehouse-mode="+" 
        data-warehouse-article-id="123" 
        data-warehouse-variant-id="456" 
        data-warehouse-amount="1">+</button>
```

#### Löschen-Buttons

**Vorher:**
```html
<button class="cart-delete-btn" 
        data-action="delete" 
        data-article-id="123" 
        data-variant-id="456">
    Entfernen
</button>
```

**Nachher:**
```html
<button data-warehouse-cart-delete
        data-warehouse-article-id="123" 
        data-warehouse-variant-id="456"
        data-warehouse-confirm="Artikel wirklich entfernen?">
    Entfernen
</button>
```

#### Eingabefelder

**Vorher:**
```html
<input class="wh-qty-input" 
       data-article-id="123" 
       data-variant-id="456"
       data-item-key="item_123"
       value="2">
```

**Nachher:**
```html
<input data-warehouse-cart-input
       data-warehouse-article-id="123" 
       data-warehouse-variant-id="456"
       data-warehouse-item-key="item_123"
       value="2">
```

### 2. IDs durch Data-Attribute ersetzen

#### Warenkorb-Zwischensumme

**Vorher:**
```html
<div id="cart-subtotal">99,98 €</div>
```

**Nachher:**
```html
<div data-warehouse-cart-subtotal>99,98 €</div>
```

#### Item-Total

**Vorher:**
```html
<div class="item-total" data-item-key="item_123">49,99 €</div>
```

**Nachher:**
```html
<div data-warehouse-item-total="item_123">49,99 €</div>
```

### 3. Container-Attribute hinzufügen

Fügen Sie dem Haupt-Container Ihres Fragments das entsprechende data-Attribut hinzu:

#### Warenkorb-Seite

**Vorher:**
```html
<div class="container">
    <!-- Warenkorb-Inhalt -->
</div>
```

**Nachher:**
```html
<div class="container" data-warehouse-cart-page>
    <!-- Warenkorb-Inhalt -->
</div>
```

#### Offcanvas-Warenkorb

**Vorher:**
```html
<div class="offcanvas" id="cart-offcanvas">
    <div class="offcanvas-body">
        <!-- Inhalt -->
    </div>
</div>
```

**Nachher:**
```html
<div class="offcanvas" id="cart-offcanvas" 
     data-warehouse-offcanvas-cart
     data-warehouse-empty-message="<?= Warehouse::getLabel('cart_is_empty') ?>">
    <div class="offcanvas-body" data-warehouse-offcanvas-body>
        <!-- Inhalt -->
    </div>
</div>
```

#### Artikeldetails

**Vorher:**
```html
<div class="row">
    <!-- Artikeldetails -->
</div>
```

**Nachher:**
```html
<div class="row" data-warehouse-article-detail>
    <!-- Artikeldetails -->
</div>
```

### 4. Inline-Scripts entfernen

#### Warenkorb-Funktionalität

**Vorher:**
```html
<script nonce="<?= rex_response::getNonce() ?>">
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cart-quantity-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.dataset.action;
            const mode = this.dataset.mode;
            // ... mehr Code
            updateCartItem(action, articleId, variantId, amount, mode);
        });
    });
});

function updateCartItem(action, articleId, variantId, amount, mode) {
    // ... viel Code
}
</script>
```

**Nachher:**
```html
<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" 
        nonce="<?= rex_response::getNonce() ?>"></script>
```

#### Artikeldetails mit Mengenselektor

**Vorher:**
```html
<script nonce="<?= rex_response::getNonce() ?>">
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.switch_count');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const input = document.getElementById('warehouse_count_123');
            let currentValue = parseInt(input.value, 10);
            const changeValue = parseInt(this.getAttribute('data-value'), 10);
            // ... mehr Code
        });
    });
});
</script>
```

**Nachher:**
```html
<!-- Nur init.js einbinden, Rest wird automatisch behandelt -->
<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" 
        nonce="<?= rex_response::getNonce() ?>"></script>
```

Und im HTML:
```html
<div data-warehouse-article-detail>
    <button data-warehouse-quantity-switch="-1" 
            data-warehouse-quantity-input="warehouse_count_123">-</button>
    <input id="warehouse_count_123" 
           type="number" 
           data-warehouse-quantity-input 
           value="1">
    <button data-warehouse-quantity-switch="+1" 
            data-warehouse-quantity-input="warehouse_count_123">+</button>
</div>
```

### 5. Formular-Integration

#### Add-to-Cart-Formular

**Vorher:**
```html
<form id="warehouse_form_detail">
    <input type="hidden" name="article_id" value="123">
    <input type="number" name="order_count" value="1">
    <button type="submit">In den Warenkorb</button>
</form>

<script nonce="<?= rex_response::getNonce() ?>">
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('warehouse_form_detail');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // ... AJAX-Code
    });
});
</script>
```

**Nachher:**
```html
<form data-warehouse-add-form>
    <input type="hidden" name="article_id" value="123">
    <input type="number" name="order_count" value="1">
    <button type="submit">In den Warenkorb</button>
</form>

<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" 
        nonce="<?= rex_response::getNonce() ?>"></script>
```

## Spezielle Szenarien

### Staffelpreise

#### Preis-Display mit Staffelpreisen

**Vorher:**
```html
<div id="warehouse_art_price" data-price="29.99">
    <span class="fs-3">29,99 €</span>
</div>

<script nonce="<?= rex_response::getNonce() ?>">
const priceElement = document.getElementById('warehouse_art_price');
const basePrice = parseFloat(priceElement.dataset.price);
const bulkPrices = <?= json_encode($bulkPrices) ?>;

function updatePriceDisplay(quantity) {
    // ... Preis-Berechnung
}
</script>
```

**Nachher:**
```html
<div data-warehouse-price-display 
     data-warehouse-base-price="29.99"
     data-warehouse-bulk-prices='<?= json_encode($bulkPrices) ?>'>
    <span data-warehouse-price-value class="fs-3">29,99 €</span>
</div>

<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" 
        nonce="<?= rex_response::getNonce() ?>"></script>
```

### Varianten-Auswahl

**Vorher:**
```html
<button class="nav-link" 
        data-price="34.99" 
        data-art_id="456">
    Größe L
</button>

<script nonce="<?= rex_response::getNonce() ?>">
const activeVariant = document.querySelector('.nav-link.active[data-art_id]');
if (activeVariant) {
    variantId = activeVariant.getAttribute('data-art_id');
}
</script>
```

**Nachher:**
```html
<button class="nav-link" 
        data-warehouse-variant
        data-warehouse-variant-id="456"
        data-warehouse-variant-price="34.99">
    Größe L
</button>

<!-- JavaScript übernimmt automatisch die Varianten-Erkennung -->
```

### Checkout-Formular

**Vorher:**
```html
<input type="checkbox" id="different_shipping_address">
<div id="shipping-address-fields" style="display: none;">
    <!-- Felder -->
</div>

<script nonce="<?= rex_request::nonce(); ?>">
document.addEventListener("DOMContentLoaded", function() {
    const checkbox = document.getElementById("different_shipping_address");
    const shippingFields = document.getElementById("shipping-address-fields");
    
    checkbox.addEventListener("change", function() {
        if (this.checked) {
            shippingFields.style.display = "block";
        } else {
            shippingFields.style.display = "none";
        }
    });
});
</script>
```

**Nachher:**
```html
<input type="checkbox" data-warehouse-shipping-toggle>
<div data-warehouse-shipping-fields 
     data-warehouse-has-data="<?= !empty($shipping_data) ? 'true' : 'false' ?>" 
     style="display: none;">
    <!-- Felder -->
</div>

<script src="<?= rex_url::addonAssets('warehouse', 'js/init.js') ?>" 
        nonce="<?= rex_response::getNonce() ?>"></script>
```

## Checkliste für die Migration

- [ ] Alle CSS-Klassen durch `data-warehouse-*` Attribute ersetzt
- [ ] Alle IDs durch `data-warehouse-*` Attribute ersetzt (außer für `<label for="...">`
- [ ] Container-Attribute (`data-warehouse-cart-page`, etc.) hinzugefügt
- [ ] Alle inline-Scripts entfernt
- [ ] `init.js` Script-Tag hinzugefügt
- [ ] Nonce für Script-Tag verwendet
- [ ] Funktionalität im Browser getestet
- [ ] Browser-Konsole auf Fehler geprüft
- [ ] Mehrfachverwendung getestet (wenn anwendbar)

## Testen der Migration

### 1. Visuelle Prüfung

Öffnen Sie die Seite im Browser und prüfen Sie:
- [ ] Layout ist unverändert
- [ ] Alle Buttons sind sichtbar
- [ ] Alle Formularfelder sind sichtbar

### 2. Funktionstest

Testen Sie folgende Funktionen:
- [ ] Artikel zum Warenkorb hinzufügen
- [ ] Menge im Warenkorb ändern (+/-)
- [ ] Menge per Eingabefeld ändern
- [ ] Artikel aus Warenkorb entfernen
- [ ] Warenkorb-Anzahl wird aktualisiert
- [ ] Staffelpreise werden berechnet (wenn vorhanden)
- [ ] Varianten können gewählt werden (wenn vorhanden)

### 3. Browser-Konsole

Prüfen Sie die Browser-Konsole (F12) auf:
- [ ] Keine JavaScript-Fehler
- [ ] Keine 404-Fehler (init.js lädt korrekt)
- [ ] Keine CSP-Violations

### 4. Mehrfach-Instanzen

Wenn Sie `data-warehouse-cart-count` mehrfach verwenden:
- [ ] Alle Instanzen zeigen denselben Wert
- [ ] Alle Instanzen werden gleichzeitig aktualisiert

## Häufige Probleme und Lösungen

### Problem: Buttons reagieren nicht

**Ursache**: Container-Attribut fehlt

**Lösung**: Fügen Sie das richtige Container-Attribut hinzu:
```html
<div data-warehouse-cart-page>
    <!-- oder -->
<div data-warehouse-offcanvas-cart>
    <!-- oder -->
<div data-warehouse-article-detail>
```

### Problem: Warenkorb-Anzahl wird nicht aktualisiert

**Ursache**: `data-warehouse-cart-count` fehlt

**Lösung**: Fügen Sie das Attribut hinzu:
```html
<span data-warehouse-cart-count><?= Cart::create()->count() ?></span>
```

### Problem: Staffelpreise werden nicht berechnet

**Ursache**: JSON-Format ist falsch oder Attribute fehlen

**Lösung**: Prüfen Sie das Format:
```html
<div data-warehouse-price-display 
     data-warehouse-base-price="29.99"
     data-warehouse-bulk-prices='<?= json_encode($bulkPrices, JSON_HEX_APOS) ?>'>
    <span data-warehouse-price-value>29,99 €</span>
</div>
```

### Problem: Variante wird nicht erkannt

**Ursache**: Attribute fehlen oder falsch gesetzt

**Lösung**: Stellen Sie sicher, dass:
1. Der Button `data-warehouse-variant` hat
2. Der Button `data-warehouse-variant-id` hat
3. Der Button die Klasse `active` hat (für Bootstrap)

### Problem: init.js lädt nicht

**Ursache**: Pfad ist falsch

**Lösung**: Verwenden Sie immer:
```php
rex_url::addonAssets('warehouse', 'js/init.js')
```

## Support

Bei Fragen oder Problemen:
- Dokumentation: `/docs/FRONTEND_JAVASCRIPT.md`
- GitHub Issues: https://github.com/FriendsOfREDAXO/warehouse/issues
- Beispiele: Siehe Standard-Fragmente in `/fragments/warehouse/bootstrap5/`

## Referenz

Vollständige Übersicht aller data-Attribute:
- [FRONTEND_JAVASCRIPT.md](FRONTEND_JAVASCRIPT.md)

Aktualisierte Standard-Fragmente als Referenz:
- `fragments/warehouse/bootstrap5/cart/cart_page.php`
- `fragments/warehouse/bootstrap5/cart/offcanvas_cart.php`
- `fragments/warehouse/bootstrap5/cart/cart.php`
- `fragments/warehouse/bootstrap5/article/details.php`
- `fragments/warehouse/bootstrap5/checkout/form-guest.php`
