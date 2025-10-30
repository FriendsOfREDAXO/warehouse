# Die Klasse `Search`

Kind-Klasse von `rex_sql`, bietet Suchfunktionen für das Warehouse-Addon. Ermöglicht eine übergreifende Suche über Artikel, Varianten, Bestellungen und Kategorien.

> Die Search-Klasse erweitert `rex_sql` und bietet spezielle Suchfunktionen für Warehouse-Inhalte.

## Suchfunktionen

Die Search-Klasse bietet eine einheitliche Suchschnittstelle über alle wichtigen Warehouse-Tabellen hinweg.

## Methoden und Beispiele

### `query(string $query, int $limit = 50)`

Führt eine übergreifende Suche über Artikel, Varianten, Bestellungen und Kategorien durch.

**Parameter:**

- `$query` (string): Der Suchbegriff
- `$limit` (int): Maximale Anzahl der Ergebnisse (Standard: 50)

**Rückgabe:** Array mit Suchergebnissen

```php
use FriendsOfRedaxo\Warehouse\Search;

// Einfache Suche
$results = Search::query('Testprodukt', 20);

// Suche nach UUID
$results = Search::query('uuid-string');

// Suche nach ID
$results = Search::query('123');
```

**Suchergebnis-Struktur:**
Jedes Ergebnis enthält folgende Felder:

- `source`: Typ der Quelle ('article', 'article_variant', 'order', 'category')
- `id`: ID des Datensatzes
- `uuid`: UUID (falls vorhanden)
- `name`: Name/Bezeichnung
- `details`: Zusätzliche Details (je nach Typ)
- `createdate`: Erstellungsdatum
- `updatedate`: Letzte Änderung

### `getForm()`

Gibt das Such-Formular für das Backend zurück.

**Rückgabe:** HTML-String des Such-Formulars

```php
use FriendsOfRedaxo\Warehouse\Search;

// Such-Formular anzeigen
echo Search::getForm();
```

## Suchbereiche

Die Suche durchsucht folgende Bereiche:

### Artikel (`rex_warehouse_article`)

- Name
- Kurzbeschreibung (short_text)
- UUID
- ID

### Artikel-Varianten (`rex_warehouse_article_variant`)

- Name
- UUID
- ID

### Bestellungen (`rex_warehouse_order`)

- Vorname und Nachname
- Firma
- E-Mail-Adresse
- ID

### Kategorien (`rex_warehouse_category`)

- Name
- UUID
- ID

## Sucharten

Die Suche unterstützt verschiedene Sucharten:

1. **Volltext-Suche:** Verwendet MySQL FULLTEXT-Index mit BOOLEAN MODE
2. **UUID-Suche:** Partielle Übereinstimmung mit LIKE
3. **ID-Suche:** Exakte Übereinstimmung nach numerischer ID

## Verwendung im Backend

Das Such-Formular wird automatisch im Backend eingebunden und ermöglicht eine schnelle Navigation zu gefundenen Inhalten.

```php
// Beispiel für erweiterte Suche mit Verarbeitung der Ergebnisse
$searchTerm = 'Beispiel';
$results = Search::query($searchTerm, 10);

foreach ($results as $result) {
    echo "Gefunden in {$result['source']}: {$result['name']}\n";
    if ($result['details']) {
        echo "Details: {$result['details']}\n";
    }
}
```
