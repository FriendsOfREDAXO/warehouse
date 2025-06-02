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

### `WAREHOUSE_*`

TODO: Hier alle EPs beschreiben, die es am Ende in v2 geschafft haben.
