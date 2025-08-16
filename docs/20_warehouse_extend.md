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

### `WAREHOUSE_ORDER_CREATED`

Wird ausgelöst, wenn eine neue Bestellung erstellt wurde. Ermöglicht die Verarbeitung von Bestellungen für externe Systeme wie sevdesk, Lexware oder SAP-Integrationen.

**Parameter:**
- `subject`: Die erstellte Bestellung als `Order`-Objekt

**Beispiel für Lexware-API-Integration:**

```php
rex_extension::register('WAREHOUSE_ORDER_CREATED', function(rex_extension_point $ep) {
    /** @var \FriendsOfRedaxo\Warehouse\Order $order */
    $order = $ep->getSubject();
    
    // Bestelldaten für Lexware API vorbereiten
    $lexwareData = [
        'kunde' => [
            'name' => $order->getFirstname() . ' ' . $order->getLastname(),
            'email' => $order->getEmail(),
            'adresse' => $order->getAddress(),
            'plz' => $order->getZip(),
            'ort' => $order->getCity(),
            'land' => $order->getCountry(),
        ],
        'rechnung' => [
            'bestellnummer' => $order->getOrderNo(),
            'betrag' => $order->getOrderTotal(),
            'datum' => $order->getCreatedate(),
            'positionen' => []
        ]
    ];
    
    // Bestellpositionen hinzufügen
    $orderJson = $order->getOrderJson(true);
    foreach ($orderJson['items'] ?? [] as $item) {
        $lexwareData['rechnung']['positionen'][] = [
            'artikel' => $item['name'],
            'menge' => $item['amount'],
            'preis' => $item['price']
        ];
    }
    
    // HTTP-Request an Lexware API
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.lexware.de/rechnungen',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($lexwareData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . rex_config::get('project', 'lexware_api_token')
        ]
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    // Fehlerbehandlung
    if ($httpCode !== 200) {
        rex_logger::logError('warehouse', 'Lexware API Error for Order #' . $order->getId() . ': ' . $response);
    }
});
```

**Beispiel für allgemeine Webhook-Integration:**

```php
rex_extension::register('WAREHOUSE_ORDER_CREATED', function(rex_extension_point $ep) {
    /** @var \FriendsOfRedaxo\Warehouse\Order $order */
    $order = $ep->getSubject();
    
    $webhookData = [
        'event' => 'order.created',
        'order_id' => $order->getId(),
        'order_no' => $order->getOrderNo(),
        'customer' => [
            'name' => $order->getFirstname() . ' ' . $order->getLastname(),
            'email' => $order->getEmail()
        ],
        'total' => $order->getOrderTotal(),
        'items' => $order->getOrderJson(true)['items'] ?? []
    ];
    
    // Webhook an mehrere Endpunkte senden
    $webhookUrls = rex_config::get('project', 'webhook_urls', []);
    foreach ($webhookUrls as $url) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($webhookData),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);
        
        curl_exec($curl);
        curl_close($curl);
    }
});
```

**Beispiel für SAP-Integration:**

```php
rex_extension::register('WAREHOUSE_ORDER_CREATED', function(rex_extension_point $ep) {
    /** @var \FriendsOfRedaxo\Warehouse\Order $order */
    $order = $ep->getSubject();
    
    // SAP RFC Verbindung (hier vereinfacht)
    $sapData = [
        'VBELN' => $order->getOrderNo(),  // Verkaufsbeleg
        'KUNNR' => $order->getYcomUser()?->getId(), // Kundennummer
        'NETWR' => $order->getOrderTotal(), // Nettowert
        'AUDAT' => date('Ymd', strtotime($order->getCreatedate())), // Belegdatum
    ];
    
    // RFC-Aufruf an SAP (vereinfacht)
    try {
        $rfc = new SAPRfc();
        $rfc->call('BAPI_SALESORDER_CREATEFROMDAT2', $sapData);
    } catch (Exception $e) {
        rex_logger::logError('warehouse', 'SAP Integration failed for Order #' . $order->getId() . ': ' . $e->getMessage());
    }
});
```

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
