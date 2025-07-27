# Die Klasse `Checkout`

Die Checkout-Klasse verwaltet den Checkout-Prozess im Warehouse-Addon. Sie stellt Formulare für Gast-Bestellungen und Login-Prozesse bereit und integriert sich nahtlos in das YCom-System.

> Die Checkout-Klasse ist als statische Utility-Klasse konzipiert und bietet Methoden zur Generierung von Checkout-Formularen.

## Übersicht

Die Checkout-Klasse bietet folgende Hauptfunktionen:

- Gast-Checkout-Formulare
- Login-Formulare für registrierte Kunden
- Integration mit YCom-Authentifizierung
- Bootstrap 5-kompatible Formular-Templates

## Methoden und Beispiele

### `getContinueAsGuestForm()`

Erstellt ein YForm-Formular für die Gast-Bestellung (Checkout ohne Registrierung).

**Rückgabe:** `rex_yform` - Konfiguriertes YForm-Objekt

```php
use FriendsOfRedaxo\Warehouse\Checkout;

// Gast-Checkout-Formular erstellen
$guestForm = Checkout::getContinueAsGuestForm();

// Formular in Template ausgeben
echo $guestForm->getForm();
```

**Formular-Eigenschaften:**
- **Action:** Automatische Weiterleitung zur Checkout-URL der aktuellen Domain
- **CSS-Klassen:** `warehouse_checkout_guest`, Bootstrap 5-kompatibel
- **Template:** `bootstrap5,bootstrap`
- **Submit-Button:** "Als Gast fortfahren" mit `btn btn-primary w-100` Styling

### `getLoginForm()`

Erstellt ein YForm-Formular für den Login bereits registrierter Kunden.

**Rückgabe:** `rex_yform` - Konfiguriertes YForm-Objekt mit YCom-Authentifizierung

```php
use FriendsOfRedaxo\Warehouse\Checkout;

// Login-Formular erstellen
$loginForm = Checkout::getLoginForm();

// Formular in Template ausgeben  
echo $loginForm->getForm();
```

**Formular-Felder:**
- **Login-Feld:** E-Mail oder Benutzername (mit Autocomplete)
- **Passwort-Feld:** Passwort-Eingabe (mit Autocomplete)
- **Return-To:** Weiterleitung nach erfolgreichem Login
- **Submit-Button:** "Anmelden" mit Bootstrap-Styling

**Validierungen:**
- YCom-Authentifizierung mit konfigurierbaren Fehlermeldungen
- Pflichtfeld-Validierung für Login und Passwort
- Automatische Fehlerbehandlung bei falschen Anmeldedaten

## Formular-Konfiguration

### Gast-Formular-Konfiguration

```php
// Beispiel der internen Formular-Konfiguration
$yform->setObjectparams('form_action', Domain::getCurrent()->getCheckoutUrl());
$yform->setObjectparams('form_wrap_class', 'warehouse_checkout_guest');
$yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
$yform->setObjectparams('form_class', 'rex-yform warehouse_checkout_guest');
$yform->setObjectparams('form_name', 'warehouse_checkout_guest');
```

### Login-Formular-Konfiguration

```php
// Beispiel der internen Formular-Konfiguration
$yform->setObjectparams('form_action', Domain::getCurrent()->getCheckoutUrl());
$yform->setObjectparams('form_wrap_class', 'warehouse_checkout_login');
$yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
$yform->setObjectparams('form_class', 'rex-yform warehouse_checkout_login');
$yform->setObjectparams('form_name', 'warehouse_checkout_login');
```

## Integration in Templates

### YCom-Modus "choose"

```php
<?php
use FriendsOfRedaxo\Warehouse\Checkout;
use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Warehouse;

// Auswahl zwischen Gast und Login anzeigen
if (Warehouse::getConfig('ycom_mode') === 'choose' && Customer::getCurrent() === null) {
?>
    <div class="row g-3">
        <!-- Gast-Checkout -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= Warehouse::getLabel('checkout_guest') ?></h5>
                    <p class="card-text"><?= Warehouse::getLabel('checkout_guest_text') ?></p>
                    <?= Checkout::getContinueAsGuestForm()->getForm() ?>
                </div>
            </div>
        </div>
        
        <!-- Login -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= Warehouse::getLabel('checkout_login') ?></h5>
                    <p class="card-text"><?= Warehouse::getLabel('checkout_login_text') ?></p>
                    <?= Checkout::getLoginForm()->getForm() ?>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>
```

### YCom-Modus "enforce_account"

```php
<?php
use FriendsOfRedaxo\Warehouse\Checkout;
use FriendsOfRedaxo\Warehouse\Customer;

// Nur Login anzeigen, wenn Konto erforderlich ist
if (Customer::getCurrent() === null) {
    echo '<div class="login-required">';
    echo '<h3>Anmeldung erforderlich</h3>';
    echo '<p>Für eine Bestellung ist eine Anmeldung erforderlich.</p>';
    echo Checkout::getLoginForm()->getForm();
    echo '</div>';
}
?>
```

### YCom-Modus "guest_only"

```php
<?php
use FriendsOfRedaxo\Warehouse\Checkout;

// Nur Gast-Checkout anzeigen
echo '<div class="guest-checkout">';
echo '<h3>Als Gast bestellen</h3>';
echo Checkout::getContinueAsGuestForm()->getForm();
echo '</div>';
?>
```

## YCom-Modi Integration

Die Checkout-Klasse arbeitet eng mit den verschiedenen YCom-Modi zusammen:

### `enforce_account`
- Bestellung nur mit Kundenkonto möglich
- Nur Login-Formular wird angezeigt
- Registrierung erforderlich für neue Kunden

### `choose`
- Kunde kann zwischen Gast-Bestellung und Login wählen
- Beide Formulare werden nebeneinander angezeigt
- Flexible Checkout-Optionen

### `guest_only`
- Nur Gast-Bestellungen möglich
- Kein Login erforderlich
- Vereinfachter Checkout-Prozess

## Formular-Labels

Die verwendeten Labels können über die Warehouse-Konfiguration angepasst werden:

```php
// Standard-Labels
Warehouse::getLabel('checkout_guest'); // "Als Gast bestellen"
Warehouse::getLabel('checkout_guest_text'); // "Bestellen Sie ohne Registrierung"
Warehouse::getLabel('checkout_guest_continue'); // "Als Gast fortfahren"
Warehouse::getLabel('checkout_login'); // "Anmelden"
Warehouse::getLabel('checkout_login_text'); // "Bereits registriert?"
Warehouse::getLabel('checkout_login_email'); // "E-Mail oder Benutzername"
Warehouse::getLabel('checkout_login_password'); // "Passwort"
Warehouse::getLabel('checkout_login_submit'); // "Anmelden"
```

## Erweiterte Anpassungen

### Eigene Formular-Felder hinzufügen

```php
// Extension Point für Gast-Formular
rex_extension::register('WAREHOUSE_CHECKOUT_GUEST_FORM', function(rex_extension_point $ep) {
    $yform = $ep->getSubject();
    
    // Newsletter-Checkbox hinzufügen
    $yform->setValueField('checkbox', [
        'newsletter',
        'Newsletter abonnieren',
        '1',
        '0'
    ]);
    
    return $yform;
});
```

### Formular-Styling anpassen

```php
// Extension Point für Login-Formular
rex_extension::register('WAREHOUSE_CHECKOUT_LOGIN_FORM', function(rex_extension_point $ep) {
    $yform = $ep->getSubject();
    
    // Custom CSS-Klassen
    $yform->setObjectparams('form_class', 'rex-yform custom-login-form');
    
    return $yform;
});
```

## Fehlerbehandlung

Die Formulare enthalten automatische Fehlerbehandlung:

### Login-Fehler
- Falsche Anmeldedaten werden automatisch abgefangen
- Benutzerfreundliche Fehlermeldungen
- Validierung von Pflichtfeldern

### Gast-Checkout-Fehler
- Formular-Validierung über YForm
- Automatische Weiterleitung bei Erfolg
- Session-Management für Gastdaten

## AJAX-Integration

```javascript
// Beispiel für AJAX-basiertes Login
$('#warehouse_checkout_login').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            // Erfolgreiche Anmeldung
            location.reload();
        },
        error: function() {
            // Fehlerbehandlung
            alert('Anmeldung fehlgeschlagen');
        }
    });
});
```

## Best Practices

### Sicherheit
- Alle Formulare verwenden CSRF-Schutz
- Passwort-Felder haben entsprechende Autocomplete-Attribute
- Validierung erfolgt server- und clientseitig

### Benutzerfreundlichkeit
- Bootstrap 5-Styling für konsistente UI
- Responsive Design für mobile Geräte
- Klare Labels und Hilftexte

### Performance
- Formulare werden nur bei Bedarf generiert
- Minimale DOM-Manipulation
- Effiziente Session-Verwaltung

## Weiterführende Integration

```php
// Nach erfolgreichem Checkout
rex_extension::register('WAREHOUSE_CHECKOUT_SUCCESS', function(rex_extension_point $ep) {
    $mode = $ep->getParam('checkout_mode'); // 'guest' oder 'login'
    $customer_data = $ep->getParam('customer_data');
    
    // Weitere Aktionen nach Checkout
    if ($mode === 'guest') {
        // Gast-spezifische Nachbearbeitung
    } else {
        // Registrierte Kunden-Nachbearbeitung
    }
});
```
