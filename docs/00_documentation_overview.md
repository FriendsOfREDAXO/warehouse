# Warehouse-Klassen Dokumentations-Übersicht

Diese Datei bietet eine Übersicht über alle dokumentierten Klassen im Warehouse-Addon und deren Dokumentationsstatus.

## ✅ Vollständig dokumentierte Klassen

### Neu erstellte Dokumentationen

| Klasse | Dokumentation | Beschreibung |
|--------|---------------|--------------|
| `Search` | [`07_search.md`](07_search.md) | Suchfunktionen über Artikel, Varianten, Bestellungen und Kategorien |
| `Shipping` | [`08_shipping.md`](08_shipping.md) | Versandkostenberechnung mit verschiedenen Modi |
| `Cart` | [`09_cart.md`](09_cart.md) | Warenkorb-Verwaltung mit Session-Integration |
| `Document` | [`16_document.md`](16_document.md) | Dokumentnummer-Generierung (Bestellung, Lieferschein, Rechnung) |
| `Payment` | [`17_payment.md`](17_payment.md) | Zahlungsarten-Verwaltung mit Extension Points |
| `Warehouse` | [`18_warehouse.md`](18_warehouse.md) | Zentrale Hauptklasse mit Konfiguration und Utilities |
| `Checkout` | [`19_checkout.md`](19_checkout.md) | Checkout-Formulare für Gast- und Login-Prozess |
| `CustomerAddress` | [`20_customer_address.md`](20_customer_address.md) | Kundenadressen-Verwaltung mit verschiedenen Adresstypen |
| `Logger` | [`21_logger.md`](21_logger.md) | Umfassendes Logging für alle Shop-Ereignisse |
| `EMail` | [`22_email.md`](22_email.md) | E-Mail-Verwaltung (geplante Funktionalität) |

## 📝 Bereits vorhanden, aber verbesserungswürdig

Diese Dokumentationen existieren bereits, sollten aber überarbeitet und vervollständigt werden:

| Klasse | Dokumentation | Status | Priorität |
|--------|---------------|--------|-----------|
| `Article` | [`02_warehouse_article.md`](02_warehouse_article.md) | Unvollständig | Hoch |
| `ArticleVariant` | [`03_warehouse_article_variant.md`](03_warehouse_article_variant.md) | Unvollständig | Hoch |
| `Category` | [`03_warehouse_category.md`](03_warehouse_category.md) | Unvollständig | Hoch |
| `Order` | [`04_order.md`](04_order.md) | Unvollständig | Hoch |
| `Domain` | [`05_domain.md`](05_domain.md) | Unvollständig | Mittel |
| `Customer` | [`06_customer.md`](06_customer.md) | Unvollständig | Mittel |

## ❌ Noch fehlende Dokumentationen

Diese Klassen haben noch keine Dokumentation:

| Klasse | Geplante Dokumentation | Beschreibung | Priorität |
|--------|------------------------|--------------|-----------|
| `Dashboard` | `23_dashboard.md` | Backend-Dashboard mit Statistiken | Niedrig |
| `Frontend` | `24_frontend.md` | Frontend-Utility-Klasse | Niedrig |
| `PayPal` | `25_paypal.md` | PayPal-Integration | Mittel |

## Dokumentations-Struktur

Alle Klassen-Dokumentationen folgen einer einheitlichen Struktur:

### Standard-Abschnitte

1. **Überschrift und Einleitung**
   - Klassenname und kurze Beschreibung
   - Vererbungsinformationen (z.B. erweitert `rex_yform_manager_dataset`)

2. **Übersicht**
   - Hauptfunktionen der Klasse
   - Wichtige Eigenschaften

3. **Konstanten** (falls vorhanden)
   - Definierte Konstanten mit Beschreibung
   - Verwendungsbeispiele

4. **Methoden und Beispiele**
   - Alle öffentlichen Methoden
   - Parameter-Beschreibungen
   - Rückgabewerte
   - Praktische Code-Beispiele

5. **Praktische Anwendungsbeispiele**
   - Realistische Verwendungsszenarien
   - Komplexere Implementierungen

6. **Integration und Templates** (falls relevant)
   - Template-Integration
   - Extension Points
   - Konfigurationsmöglichkeiten

## Verwendete Konventionen

### Code-Beispiele

```php
use FriendsOfRedaxo\Warehouse\KlassenName;

// Kurze Beschreibung
$beispiel = KlassenName::methode($parameter);
```

### Parameter-Dokumentation

**Parameter:**

- `$parameter` (typ): Beschreibung des Parameters

**Rückgabe:** `typ` - Beschreibung des Rückgabewerts

### Methoden-Dokumentation

```php
/** @api */
public function methodenName(typ $parameter): rückgabe_typ
```

## Extension Points

Viele Klassen bieten Extension Points für individuelle Anpassungen:

| Extension Point | Klasse | Zweck |
|-----------------|--------|-------|
| `WAREHOUSE_ORDER_NUMBER` | Document | Bestellnummern anpassen |
| `WAREHOUSE_ORDER_CREATED` | Order | Bestellungen via API weiterverarbeiten |
| `WAREHOUSE_PAYMENT_OPTIONS` | Payment | Zahlungsarten erweitern |
| `WAREHOUSE_CART_SHIPPING_COST` | Shipping | Versandkosten berechnen |
| `WAREHOUSE_CART_VALIDATE` | Cart | Warenkorb validieren |
| `WAREHOUSE_DASHBOARD` | Dashboard | Dashboard-Layout anpassen |

## Template-Integration

Die meisten Klassen unterstützen das Fragment-System:

```
fragments/warehouse/
├── bootstrap5/          # Bootstrap 5 Templates (Standard)
│   ├── cart/
│   ├── checkout/
│   ├── article/
│   └── ...
├── uikit/              # UIKit Templates (Optional)
└── custom/             # Eigene Templates
```

## API-Kompatibilität

Alle dokumentierten Methoden mit `/** @api */` Kommentar sind Teil der öffentlichen API und werden in zukünftigen Versionen rückwärtskompatibel bleiben.

## Mitwirkende

Die Dokumentation wurde erstellt für:

- **Entwickler:** Vollständige API-Referenz
- **Integratoren:** Praktische Verwendungsbeispiele  
- **Theme-Ersteller:** Template-Integration
- **Shop-Betreiber:** Konfigurationsmöglichkeiten

## Updates und Wartung

Die Dokumentation sollte bei folgenden Änderungen aktualisiert werden:

1. **Neue öffentliche Methoden:** Vollständige Dokumentation mit Beispielen
2. **Geänderte Parameter:** Parameter-Dokumentation aktualisieren
3. **Neue Extension Points:** Extension Point-Liste erweitern
4. **Template-Änderungen:** Template-Beispiele anpassen
5. **Konfigurationsoptionen:** Konfigurationsdokumentation ergänzen

## Nächste Schritte

### Priorität 1: Bestehende Dokumentationen vervollständigen

- Article-Klasse: Alle Methoden und Eigenschaften dokumentieren
- ArticleVariant-Klasse: Staffelpreise und Verfügbarkeit
- Category-Klasse: Hierarchie und Artikel-Zuordnung
- Order-Klasse: Bestellstatus und Zahlungsabwicklung

### Priorität 2: Fehlende Dokumentationen erstellen

- Dashboard-Klasse für Backend-Funktionen
- PayPal-Klasse für Zahlungsintegration
- Frontend-Klasse für Shop-Frontend

### Priorität 3: Dokumentations-Qualität verbessern

- Mehr praktische Beispiele hinzufügen
- Template-Integration vertiefen
- Performance-Tipps ergänzen
- Troubleshooting-Abschnitte erweitern

## Feedback und Beiträge

Die Dokumentation ist ein lebendiges Dokument. Feedback und Verbesserungsvorschläge sind willkommen:

- **Issues:** Für Fehler oder fehlende Informationen
- **Pull Requests:** Für direkte Verbesserungen
- **Diskussionen:** Für größere Änderungen oder neue Ideen

Die vollständige und aktuelle Dokumentation trägt zur Benutzerfreundlichkeit und Adoption des Warehouse-Addons bei.
