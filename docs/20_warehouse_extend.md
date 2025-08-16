# Warehouse erweitern

Warehouse 2 unterscheidet sich in vielen Punkten zu seinem Vorgänger-Addon Warehouse und deren Varianten in freier Wildbahn.

Warehouse 2 konzentiert sich auf Kernfunktionen, die für die meisten Projekte ausreichend sind. Sollten spezielle Anforderungen bestehen, können diese durch eigene Erweiterungen realisiert werden. Entweder direkt im Add-on, als Erweiterung durch ein eigenes Add-on und / oder durch eigenen Projektcode.

## YForm erweitern

Werden an Kategorien, Produkten und Varianten zusätzliche Informationen benötigt, können diese über die YForm-Struktur erweitert werden. Um nicht in Konflikt mit späteren Updates zu geraten, gibt es zwei Empfehlungen:

1. Alle **eigenen YForm-Felder** in `rex_warehouse_*`-Tabellen sollten den Präfix `project_` erhalten. So ist klar, dass es sich um eigene Felder handelt. Die Feldwerte können dann anstelle von Standard-YForm-Methoden `getValue('project_feldname')` und `setValue('project_feldname', $value)` über die zusätzlichen Methoden `getProjectValue('feldname')` und `setProjectValue('feldname', $value)` abgerufen und gesetzt werden.
2. Für komplexere Aufgaben können **eigene Tabellen mit Relationen** erstellt werden, z.B. für zusätzliche Sprachen und Attribute. Diese Tabellen sollten nicht mit `rex_warehouse_*` beginnen, um Konflikte zu vermeiden. Ein möglicher Präfix wäre `rex_project_warehouse_*`.

## Fragmente überschreiben

Warehouse 2 bietet viele Fragmente, die überschrieben werden können. So können eigene Anpassungen vorgenommen werden, ohne das Add-on direkt zu verändern. Mögliche Anwendungsfälle sind:

1. **Anpassungen an der Darstellung** von Kategorien, Produkten und Varianten - auch an eigene Frameworks anstelle von Boostrap, wie z.B. ui-kit, ...
2. **Eigene Filter** für die Produktliste
3. **Eigene Sortierungen** für die Produktliste
4. **Eigene Felder** in der Produktliste und Detailansicht

## Extension Points nutzen

Warehouse 2 bietet viele Extension Points, die genutzt werden können, um eigene Funktionen zu erweitern. So können eigene Anpassungen vorgenommen werden, ohne das Add-on direkt zu verändern. Mögliche Anwendungsfälle sind:

1. Änderungen an der **Preisberechnung** für Produkte und Versandkosten
2. Ergänzungen im Warenkorb, bspw. für Gutschiene, Rabatte, ...
3. Lagerverwaltung, bspw. für Bestandsänderungen, ...
4. **Eigene Bestellstatus** und -aktionen
5. **Eigene Versandarten** und -kosten
6. **Eigene Zahlungsarten** und Zahlungsanbieter anstelle von PayPal, z.B. Stripe, Wallee, ...
7. **Eigene E-Mail-Templates** für Bestellbestätigung, Versandbestätigung, ...
8. **API-Integrationen** für externe Systeme wie sevdesk, Lexware, SAP, ...

Momentan gibt es folgende Extension Points:

### `WAREHOUSE_ORDER_NUMBER`

Ermöglicht das Modifizieren der nächsten Bestellnummer vor der Vergabe. Rückgabewert ist ein Integer.

**Beispiel:**

```php
rex_extension::register('WAREHOUSE_ORDER_NUMBER', function(rex_extension_point $ep) {
    $nummer = $ep->getSubject();
    // Beispiel: Präfix und Jahr hinzufügen
    return (int) (date('Y') . sprintf('%05d', $nummer));
});
```

### `WAREHOUSE_ORDER_NO_GENERATE`

Ermöglicht das Modifizieren der automatisch generierten Bestellnummern vor der Vergabe. Rückgabewert ist ein String. Standardformat: `YYYY-MM-####` mit monatlicher Zurücksetzung.

**Parameter:**
- Subject: Generierte Bestellnummer (String)
- Params: Array mit `year`, `month`, `counter`, `period`

**Beispiele:**

```php
rex_extension::register('WAREHOUSE_ORDER_NO_GENERATE', function(rex_extension_point $ep) {
    $orderNo = $ep->getSubject();
    // Beispiel: Präfix hinzufügen
    return 'B-' . $orderNo;
});

// Beispiel: Komplett eigenes Format mit Jahres-Counter
rex_extension::register('WAREHOUSE_ORDER_NO_GENERATE', function(rex_extension_point $ep) {
    $params = $ep->getParams();
    $year = $params['year'];
    $counter = $params['counter'];
    
    return $year . sprintf('%06d', $counter);
});
```

### `WAREHOUSE_DELIVERY_NOTE_NUMBER`

Ermöglicht das Modifizieren der nächsten Lieferscheinnummer vor der Vergabe. Rückgabewert ist ein Integer.

**Beispiel:**

```php
rex_extension::register('WAREHOUSE_DELIVERY_NOTE_NUMBER', function(rex_extension_point $ep) {
    $nummer = $ep->getSubject();
    // Beispiel: 10000er-Offset für Lieferscheine
    return $nummer + 10000;
});
```

### `WAREHOUSE_INVOICE_NUMBER`

Ermöglicht das Modifizieren der nächsten Rechnungsnummer vor der Vergabe. Rückgabewert ist ein Integer.

**Beispiel:**

```php
rex_extension::register('WAREHOUSE_INVOICE_NUMBER', function(rex_extension_point $ep) {
    $nummer = $ep->getSubject();
    // Beispiel: Rechnungsnummer mit führenden Nullen
    return str_pad($nummer, 8, '0', STR_PAD_LEFT);
});
```

### `WAREHOUSE_PAYMENT_OPTIONS`

Ermöglicht das Hinzufügen oder Modifizieren der verfügbaren Zahlungsarten im Shop. Rückgabewert ist ein Array mit Zahlungsoptionen.

**Beispiel:**

```php
rex_extension::register('WAREHOUSE_PAYMENT_OPTIONS', function(rex_extension_point $ep) {
    $options = $ep->getSubject();
    // Beispiel: Stripe ergänzen
    $options['stripe'] = 'Stripe';
    return $options;
});
```

### `WAREHOUSE_TAX_OPTIONS`

Ermöglicht das Hinzufügen oder Modifizieren der verfügbaren Steuersätze für Artikel. Rückgabewert ist ein Array mit Steuersätzen.

**Beispiel:**

```php
rex_extension::register('WAREHOUSE_TAX_OPTIONS', function(rex_extension_point $ep) {
    $taxOptions = $ep->getSubject();
    // Beispiel: 5% ergänzen
    $taxOptions['5'] = '5%';
    return $taxOptions;
});
```

### `WAREHOUSE_CART_VALIDATE`

Ermöglicht eigene Validierungen des Warenkorbs, z. B. Mindestbestellwert, Verfügbarkeit, etc. Rückgabewert kann ein Fehler-String oder ein modifizierter Warenkorb sein.

**Beispiel:**

```php
rex_extension::register('WAREHOUSE_CART_VALIDATE', function(rex_extension_point $ep) {
    $cart = $ep->getParam('cart');
    // Beispiel: Nur Artikel mit Lagerbestand zulassen
    foreach ($cart as $item) {
        if ($item['stock'] <= 0) {
            return 'Ein Artikel ist nicht mehr verfügbar.';
        }
    }
    return $cart;
});
```

### `WAREHOUSE_CART_SHIPPING_COST`

Ermöglicht die Anpassung der Versandkostenberechnung. Rückgabewert ist der finale Versandkostenbetrag.

**Beispiel:**

```php
rex_extension::register('WAREHOUSE_CART_SHIPPING_COST', function(rex_extension_point $ep) {
    $cost = $ep->getSubject();
    $cart = $ep->getParam('cart');
    // Beispiel: Versandkostenfrei ab 100 Euro
    if ($ep->getParam('total_price') >= 100) {
        return 0;
    }
    return $cost;
});
```

### `WAREHOUSE_DASHBOARD`

Ermöglicht die Modifikation des Dashboard-Layouts im Backend. Das Layout ist als mehrdimensionales Array strukturiert und kann erweitert, modifiziert oder reduziert werden. Rückgabewert ist das modifizierte Layout-Array.

**Beispiel:**

```php
rex_extension::register('WAREHOUSE_DASHBOARD', function(rex_extension_point $ep) {
    $layout = $ep->getSubject();
    $dashboard = $ep->getParam('dashboard');
    
    // Beispiel: Neue Sektion hinzufügen
    $layout['custom_row'] = [
        'custom_section' => [
            'col' => 12,
            'content' => '<div class="alert alert-success">Eigene Dashboard-Sektion</div>'
        ]
    ];
    
    // Beispiel: Bestehende Sektion entfernen
    unset($layout['statistics_row']['recent_customers']);
    
    // Beispiel: Sektion modifizieren
    $layout['status_row']['unpaid_orders']['col'] = 12;
    unset($layout['status_row']['shipping_orders']);
    
    return $layout;
});
```

### `WAREHOUSE_ORDER_CREATED`

Wird ausgelöst, wenn eine neue Bestellung erfolgreich in der Datenbank gespeichert wurde. Dies ermöglicht die Weiterverarbeitung von Bestellungen über externe APIs wie sevdesk, Lexware oder eigene SAP-Systeme. Das Subject ist die neu erstellte Order-Instanz.

**Parameter:**
- Subject: Die neu erstellte Order-Instanz
- Keine zusätzlichen Parameter

**Beispiel - Lexware Integration:**

```php
rex_extension::register('WAREHOUSE_ORDER_CREATED', function(rex_extension_point $ep) {
    /** @var \FriendsOfRedaxo\Warehouse\Order $order */
    $order = $ep->getSubject();
    
    try {
        // Lexware API-Integration
        $lexwareData = [
            'kunde' => [
                'name' => $order->getFirstname() . ' ' . $order->getLastname(),
                'email' => $order->getEmail(),
                'firma' => $order->getCompany(),
                'adresse' => $order->getAddress(),
                'plz' => $order->getZip(),
                'ort' => $order->getCity(),
                'land' => $order->getCountry()
            ],
            'rechnung' => [
                'bestellnummer' => $order->getOrderNo(),
                'betrag' => $order->getOrderTotal(),
                'waehrung' => 'EUR',
                'datum' => date('Y-m-d'),
                'zahlungsart' => $order->getValue('payment_type')
            ]
        ];
        
        // HTTP-Request an Lexware API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.lexware.de/v1/rechnungen',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($lexwareData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . rex_config::get('warehouse', 'lexware_api_token')
            ]
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode === 201) {
            // Erfolgreich übertragen - Optional: Lexware-ID in der Bestellung speichern
            $responseData = json_decode($response, true);
            $order->setValue('lexware_invoice_id', $responseData['id'] ?? null);
            $order->save();
            
            rex_logger::logInfo('warehouse', 'Bestellung ' . $order->getOrderNo() . ' erfolgreich an Lexware übertragen');
        } else {
            rex_logger::logError('warehouse', 'Fehler bei Lexware-Übertragung für Bestellung ' . $order->getOrderNo() . ': HTTP ' . $httpCode);
        }
    } catch (Exception $e) {
        rex_logger::logError('warehouse', 'Fehler bei Lexware-Integration: ' . $e->getMessage());
    }
});
```

**Beispiel - Allgemeine API-Integration:**

```php
rex_extension::register('WAREHOUSE_ORDER_CREATED', function(rex_extension_point $ep) {
    /** @var \FriendsOfRedaxo\Warehouse\Order $order */
    $order = $ep->getSubject();
    
    // Bestellung an externes System weiterleiten
    $apiEndpoint = rex_config::get('warehouse', 'external_api_endpoint');
    $apiKey = rex_config::get('warehouse', 'external_api_key');
    
    if ($apiEndpoint && $apiKey) {
        $orderData = [
            'order_id' => $order->getId(),
            'order_no' => $order->getOrderNo(),
            'customer' => [
                'firstname' => $order->getFirstname(),
                'lastname' => $order->getLastname(),
                'email' => $order->getEmail(),
                'company' => $order->getCompany()
            ],
            'total' => $order->getOrderTotal(),
            'items' => json_decode($order->getOrderJson(), true),
            'created' => $order->getValue('createdate')
        ];
        
        // Asynchrone Übertragung (empfohlen für bessere Performance)
        rex_cronjob::addTask('warehouse_api_sync', [
            'endpoint' => $apiEndpoint,
            'api_key' => $apiKey,
            'data' => $orderData
        ]);
    }
});
```
