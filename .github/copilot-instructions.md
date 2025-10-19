# Warehouse - REDAXO CMS Shop Add-on

## Project Overview

Dieses Repository ist eine Erweiterung (Add-on) für das REDAXO CMS (https://github.com/redaxo/redaxo), das Shop-Funktionalitäten bereitstellt:

1. **Produktverwaltung**: "Artikel" und "Varianten" mit Kategorien und Eigenschaften wie Preis, Lagerbestand, Beschreibung, Verfügbarkeit
2. **Frontend-Templates**: "Fragmente" (PHP-basierte Templates), die den Shop im Frontend anzeigen
3. **Installation & Setup**: Routinen für die Einrichtung und den Betrieb

### Technology Stack

- **PHP**: ^8.3
- **REDAXO CMS**: ^5.19
- **YForm**: ^5.0 (Datenbanktabellen-Verwaltung und ORM)
- **YForm Field**: >=2.12.0
- **YRewrite**: ^2.9 (SEO-freundliche URLs)
- **URL Add-on**: für SEO-optimierte URLs und Metadaten
- **PayPal SDK**: 1.1.0 (Server-SDK für Zahlungsabwicklung)
- **Bootstrap 5**: für Frontend-Templates (Standard-Framework)

### Key Dependencies

Das Add-on basiert auf:
- **YForm & YOrm**: Datenbanktabellen-Verwaltung mit Benutzeroberfläche sowie ORM für die Datenverarbeitung
- **REDAXO Core**: Core-Klassen für System-Funktionalität
- **URL Add-on**: Steuerung von SEO-optimierten URLs
- **YRewrite**: Metadaten-Verwaltung
- **YCom** (optional): Benutzerverwaltung für Kundenkonten

### External Resources

Informationen zu REDAXO und den entsprechenden Klassen und Konzepten:
- **REDAXO Dokumentation**: <http://github.com/redaxo/docs> (Socket-Verbindungen, Extension Points, Backend-Pages, Service-Klassen wie rex_sql, rex_config_form, ...)
- **REDAXO Core-System**: https://github.com/redaxo/redaxo
- **Core-Libraries**: https://github.com/redaxo/redaxo/tree/main/redaxo/src/core/lib

**Hinweis**: Die Dateien zu Klassennamen enthalten nicht den Präfix `rex_`, z.B.:
- Die Klasse `rex_socket` ist in `redaxo/src/core/lib/util/socket/socket.php`
- Die Klasse `rex_fragment` ist in `redaxo/src/core/lib/fragment.php`

## Quick Reference

### Common Commands

```bash
# Install dependencies
composer install

# Code style checking
composer cs-dry   # Check only (dry-run)
composer cs-fix   # Auto-fix code style issues

# Git workflow
git status        # Check changed files
git diff          # View changes
git add .         # Stage changes
git commit -m "Feature: Description"  # Commit with message
```

### File Locations

| Type | Location | Description |
|------|----------|-------------|
| Main classes | `/lib/` | PHP classes (Article, Cart, Order, etc.) |
| Frontend templates | `/fragments/warehouse/bootstrap5/` | PHP-based fragments |
| Backend pages | `/pages/` | Admin interface pages |
| Documentation | `/docs/` | Markdown documentation |
| Installation | `/install/` | SQL schemas and setup files |
| Language files | `/lang/` | Translations (de_de.lang, en_gb.lang) |
| Assets | `/assets/` | JavaScript, CSS, images |
| API endpoints | `/lib/Api/` | REST API classes |

## Quick Start

### Development Setup

#### Prerequisites
- PHP 8.3 or higher
- REDAXO 5.19+ installed
- Composer installed

#### Installation Steps

1. **Clone and install dependencies**:
   ```bash
   cd /path/to/warehouse
   composer install
   ```

2. **Install in REDAXO**:
   ```bash
   # Copy add-on to REDAXO
   cp -r /path/to/warehouse/ /path/to/redaxo/redaxo/src/addons/warehouse/
   
   # Or with rsync (includes hidden files):
   # rsync -a /path/to/warehouse/ /path/to/redaxo/redaxo/src/addons/warehouse/
   
   # Then in REDAXO Backend:
   # AddOns → Warehouse → Install
   ```

3. **Verify installation**:
   - Check that all dependencies are satisfied
   - Navigate to "Warehouse" in REDAXO Backend
   - Run setup functions if needed (Settings → Setup)

### Build & Test

**Important**: No separate build process required. The add-on runs directly in REDAXO.

**Code Style**:
```bash
composer cs-dry  # Check only
composer cs-fix  # Auto-fix
```

**Testing**: 
- No automated unit tests
- Manual testing in REDAXO test instance required
- See "Testing & Validation" section for detailed checklist

## Repository-Struktur und Dateien 

### Hauptdateien

* `install.php` - wird ausgeführt bei der Installation des Add-ons
* `uninstall.php` - wird ausgeführt bei der Deinstallation
* `update.php` - wird ausgeführt beim Update der Erweiterung über den REDAXO Installer
* `boot.php` - wird bei jedem Seitenaufruf ausgeführt, registriert YForm-Model-Klassen, Extension Points und API-Endpunkte
* `package.yml` - Konfigurationsdatei mit Metadaten, Abhängigkeiten, Backend-Seiten-Struktur und Default-Konfiguration

### Verzeichnisstruktur

* `/lib/` - Enthält alle PHP-Klassen des Add-ons
  * Hauptklassen: `Article.php`, `ArticleVariant.php`, `Category.php`, `Order.php`, `Cart.php`, `Checkout.php`, `Payment.php`, `Shipping.php`, `Warehouse.php`
  * API-Klassen in `/lib/Api/`: REST-API Endpunkte für Frontend-Interaktionen (CartApi, Order, BillingAddressApi, ShippingAddressApi)
  * Utility-Klassen: `Domain.php`, `Session.php`, `Logger.php`, `Customer.php`, `CustomerAddress.php`, `Dashboard.php`, `Search.php`, `Document.php`
  * Payment-Integration: `PayPal.php`, `PayPalPayment.php`
  * YForm-Custom-Fields in `/lib/yform/field/value/`: Eigene YForm-Feldtypen wie `warehouse_payment_options.php`
  
* `/docs/` - Markdown-Dokumentation zur technischen Dokumentation
  * Dateiformat: `00_documentation_overview.md`, `02_warehouse_article.md`, usw.
  * Enthält Klassen-Dokumentationen, Setup-Anleitungen und Migration Guides
  * Wird im Backend unter "Warehouse → Docs" angezeigt (siehe `/pages/docs.php`)
  * Bei Code-Änderungen muss die entsprechende Dokumentation auf Korrektheit geprüft und aktualisiert werden
  
* `/install/` - Installationsdateien
  * SQL-Tabellenschema-Definitionen als JSON-Dateien für YForm-Tabellen
  * Demo-Daten und Beispiel-Konfigurationen
  
* `/pages/` - Backend-Seiten und Oberflächen zur Verwaltung
  * `article.php`, `article_variant.php`, `category.php` - Produktverwaltung
  * `order.*.php` - Bestellübersicht, Details, Kunden, Dashboard
  * `settings.*.php` - Shop-Einstellungen (general, payment, shipping, domain, setup, label, log, documents, discount)
  * `search.php` - Globale Suchfunktion über Artikel, Varianten, Kategorien
  * `docs.php` - Dokumentationsseite (zeigt Markdown-Dateien aus `/docs/` an)
  * `info.php` - Übersichtsseite mit Add-on-Informationen
  
* `/fragments/` - Frontend-Templates (PHP-basierte Ausgabe-Templates)
  * `/fragments/warehouse/bootstrap5/` - Bootstrap 5 basierte Templates (Standard-Framework)
    * `/article/` - Artikeldetails, Artikelliste, Varianten-Auswahl
    * `/cart/` - Warenkorb-Ansichten (cart.php, cart_page.php, offcanvas_cart.php)
    * `/checkout/` - Checkout-Prozess, Formulare, Bezahlung, Zusammenfassung
    * `/category/` - Kategorieansichten
    * `/navigation/` - Navigationskomponenten
    * `/paypal/` - PayPal-Integration Templates
    * `/template/` - Basis-Templates und Layouts
    * `/my_orders/` - Bestellübersicht für eingeloggte Kunden
    * `/ycom/` - YCom-Integration (Login, Registrierung)
  * `/fragments/warehouse/backend/` - Backend-spezifische Fragmente
  * `/fragments/warehouse/emails/` - E-Mail-Templates
  * `/fragments/warehouse/scheme/` - Schema-Templates
  
* `/assets/` - JavaScript, CSS, Bilder und andere statische Ressourcen
  * JS-Module mit `data-warehouse-*` Attribut-System für interaktive Funktionalität

* `/lang/` - Sprachdateien für Backend und Frontend (Deutsch: `de_de.lang`, Englisch: `en_gb.lang`)

* `/ytemplates/` - YForm-Templates für Backend-Formulare

## Klassen und deren Aufgaben

Die Funktionalitäten lassen sich anhand der Klassen grob unterteilen in:

### Produktverwaltung und Katalog

* **`Article`** (`/lib/Article.php`) - Hauptklasse für Artikel
  * Erweitert `rex_yform_manager_dataset`
  * Verwaltung von Artikeln mit Eigenschaften wie Name, Beschreibung, Preis, Lagerbestand
  * Methoden: `getPrice()`, `getName()`, `getCategory()`, `getImages()`, `isAvailable()`, `getVariants()`
  * Kann Varianten haben (Beziehung zu ArticleVariant)
  * Unterstützt Staffelpreise, Kategorien, und Custom Fields
  * SEO-Methoden für URLs und Meta-Tags

* **`ArticleVariant`** (`/lib/ArticleVariant.php`) - Varianten eines Artikels
  * Erweitert `rex_yform_manager_dataset`
  * Varianten mit eigenen Preisen, SKU, Lagerbestand
  * Methoden: `getArticle()`, `getPrice()`, `getStock()`, `isAvailable()`
  * Staffelpreise und Verfügbarkeit
  
* **`Category`** (`/lib/Category.php`) - Kategorien für Artikel
  * Erweitert `rex_yform_manager_dataset`
  * Hierarchische Kategoriestruktur mit Parent-Child-Beziehungen
  * Methoden: `getArticles()`, `getChildren()`, `getParent()`, `getPath()`
  * SEO-Methoden für URLs und Meta-Tags

### Warenkorb und Session

* **`Cart`** (`/lib/Cart.php`) - Warenkorb-Verwaltung
  * Session-basierter Warenkorb für Artikel und Varianten
  * Methoden: `addItem()`, `removeItem()`, `updateQuantity()`, `getItems()`, `getTotal()`, `getTax()`, `clear()`
  * Berechnung von Zwischensummen, Steuern, Versandkosten
  * Integration mit Shipping-Klasse für Versandkostenberechnung
  * Item-Keys für eindeutige Identifikation von Artikeln/Varianten im Warenkorb
  
* **`Session`** (`/lib/Session.php`) - Session-Verwaltung
  * Wrapper für PHP-Session-Funktionen
  * Methoden: `get()`, `set()`, `has()`, `delete()`
  * Genutzt von Cart und Checkout

### Bestellung und Checkout

* **`Order`** (`/lib/Order.php`) - Bestellverwaltung
  * Erweitert `rex_yform_manager_dataset`
  * Speichert Bestelldaten: Bestellnummer, Kundendaten, Adresse, Artikel, Preise, Status
  * Methoden: `getOrderNo()`, `getItems()`, `getCustomer()`, `getTotal()`, `getStatus()`, `setStatus()`
  * Status-Management für Bestellabwicklung
  * Beziehung zu Customer und CustomerAddress
  * PayPal-Integration Felder (paypal_id, payment_id)
  
* **`Checkout`** (`/lib/Checkout.php`) - Checkout-Prozess
  * Koordiniert den Checkout-Flow
  * Formulare für Gast-Checkout und Login-Checkout
  * Methoden: `getGuestForm()`, `getLoginForm()`, `processOrder()`
  * Integration mit YCom für Kundenkonten

* **`Customer`** (`/lib/Customer.php`) - Kundeninformationen
  * Erweitert YCom-User-Klasse
  * Methoden: `getOrders()`, `getAddresses()`, `getDefaultAddress()`
  * Optional, wenn YCom nicht installiert ist

* **`CustomerAddress`** (`/lib/CustomerAddress.php`) - Kundenadressverwaltung
  * Erweitert `rex_yform_manager_dataset`
  * Lieferadressen und Rechnungsadressen
  * Methoden: `getCustomer()`, `getType()`, `isDefault()`, `setAsDefault()`

### Bezahlung und Versand

* **`Payment`** (`/lib/Payment.php`) - Zahlungsarten-Verwaltung
  * Methoden: `getAllowedPaymentOptions()`, `getPaymentOptionLabel()`, `getPaymentOptionNotice()`
  * Konfigurierbare Zahlungsarten: Vorkasse, Rechnung, Lastschrift, PayPal
  * Extension Points für eigene Zahlungsarten
  
* **`PayPal`** (`/lib/PayPal.php`) - PayPal-Integration
  * Integration mit PayPal REST API SDK
  * Methoden: `createOrder()`, `capturePayment()`, `getAccessToken()`
  * Sandbox-Modus für Tests
  * Button-Style-Konfiguration
  
* **`PayPalPayment`** (`/lib/PayPalPayment.php`) - PayPal-Zahlungsdaten
  * Erweitert `rex_yform_manager_dataset`
  * Speichert PayPal-Transaktionsdaten
  
* **`Shipping`** (`/lib/Shipping.php`) - Versandkostenberechnung
  * Verschiedene Berechnungsmodi: Pauschale, gewichtsbasiert, bestellwertbasiert
  * Methoden: `calculateShippingCosts()`, `getFreeShippingThreshold()`
  * Extension Points für eigene Versandlogik

### Utility-Klassen und Services

* **`Warehouse`** (`/lib/Warehouse.php`) - Zentrale Hauptklasse
  * Statische Utility-Methoden für den gesamten Shop
  * Konstanten für Pfade: `PATH_ARTICLE`, `PATH_CATEGORY`, `PATH_ORDER`
  * Methoden: `formatCurrency()`, `getLabel()`, `getCurrency()`, `getTaxRate()`
  * Zugriff auf Konfiguration über `rex_config::get('warehouse', 'key')`
  * YCom-Modi: enforce_account, choose, guest_only
  
* **`Domain`** (`/lib/Domain.php`) - Multi-Domain-Unterstützung
  * Erweitert `rex_yform_manager_dataset`
  * Domain-spezifische Konfigurationen
  * Methoden: `getCurrent()`, `getCartArtUrl()`, `getOrderArtUrl()`, `getArticleUrl()`
  * Integration mit YRewrite für mehrsprachige Shops
  
* **`Search`** (`/lib/Search.php`) - Suchfunktionalität
  * Suche über Artikel, Varianten, Kategorien, Bestellungen
  * Methoden: `search()`, `searchArticles()`, `searchOrders()`
  * Fulltext-Suche in verschiedenen Feldern
  
* **`Logger`** (`/lib/Logger.php`) - Logging
  * Logging für Shop-Ereignisse und Fehler
  * Methoden: `log()`, `logOrder()`, `logPayment()`, `logError()`
  * Integration mit rex_logger
  
* **`Dashboard`** (`/lib/Dashboard.php`) - Backend-Dashboard
  * Statistiken und Übersichten für das Backend
  * Methoden: `getOrderStats()`, `getRevenueStats()`, `getTopProducts()`
  * Wird auf der Order-Dashboard-Seite verwendet
  
* **`Document`** (`/lib/Document.php`) - Dokumentnummer-Generierung
  * Generierung von Bestellnummern, Rechnungsnummern, Lieferscheinnummern
  * Methoden: `getNextOrderNumber()`, `getNextInvoiceNumber()`, `getNextDeliveryNoteNumber()`
  * Konfigurierbare Nummernschemata

* **`Frontend`** (`/lib/Frontend.php`) - Frontend-Utility-Klasse (aktuell leer, für zukünftige Erweiterungen)

* **`EMail`** (`/lib/EMail.php`) - E-Mail-Verwaltung (geplante Funktionalität)
  * Geplant: `sendOrderConfirmation()`, `sendPaymentConfirmation()`, `sendShippingNotification()`
  * E-Mail-Templates in `/fragments/warehouse/emails/`

### API-Klassen (REST-Endpunkte für Frontend)

* **`Api\CartApi`** (`/lib/Api/CartApi.php`) - Warenkorb-API
  * AJAX-Endpunkte für Warenkorb-Operationen
  * Wird via `rex_api_function::factory('warehouse_cart_api')->execute()` aufgerufen
  * Actions: add, remove, update, clear
  
* **`Api\Order`** (`/lib/Api/Order.php`) - Bestell-API
  * AJAX-Endpunkte für Bestellabwicklung
  * Wird via `rex_api_function::factory('warehouse_order')->execute()` aufgerufen
  
* **`Api\BillingAddressApi`** (`/lib/Api/BillingAddressApi.php`) - Rechnungsadresse-API
* **`Api\ShippingAddressApi`** (`/lib/Api/ShippingAddressApi.php`) - Lieferadresse-API
* **`Api\Cart`** (`/lib/Api/Cart.php`) - Alternative Cart-API
* **`Api\Search`** (`/lib/Api/Search.php`) - Such-API
* **`Api\QuickNavigationSearch`** (`/lib/Api/QuickNavigationSearch.php`) - Quick-Navigation Such-API

Diese spiegeln sich nicht nur in der Klassen-Struktur, sondern auch in der Ordner- und Fragmente-Struktur wider.

## Backend-Seiten (Pages)

Das Add-on stellt verschiedene Backend-Seiten zur Verfügung, die in der `package.yml` definiert sind:

### Hauptbereiche

1. **Order (Bestellverwaltung)** - `warehouse/order`
   * **Dashboard** (`order.dashboard.php`) - Übersicht mit Statistiken und Kennzahlen
   * **List** (`order.list.php`) - Liste aller Bestellungen mit Filter- und Sortierfunktionen
   * **Details** (`order.details.php`) - Detailansicht einer Bestellung mit allen Informationen
   * **Customer** (`order.customer.php`) - YForm-Tabelle für Kundenverwaltung
   * **Customer Address** (`order.customer_address.php`) - YForm-Tabelle für Kundenadressverwaltung

2. **Produktverwaltung**
   * **Article** (`article.php`) - YForm-Tabelle für Artikel-Verwaltung
   * **Article Variant** (`article_variant.php`) - YForm-Tabelle für Varianten-Verwaltung
   * **Category** (`category.php`) - YForm-Tabelle für Kategorien-Verwaltung

3. **Settings (Einstellungen)** - `warehouse/settings`
   * **General** (`settings.general.php`) - Grundeinstellungen (Shop-Name, Währung, Steuersatz, Framework)
   * **Payment** (`settings.payment.php`) - Zahlungsarten-Konfiguration (PayPal, Rechnung, Vorkasse, Lastschrift)
   * **Shipping** (`settings.shipping.php`) - Versandkosten-Konfiguration
   * **Domain** (`settings.domain.php`) - Multi-Domain-Konfiguration
   * **Discount** (`settings.discount.php`) - Rabatt-Verwaltung
   * **Documents** (`settings.documents.php`) - Dokumenten-Templates (Rechnung, Lieferschein)
   * **Label** (`settings.label.php`) - Frontend-Label und Texte anpassen
   * **Log** (`settings.log.php`) - System-Logs und Error-Logs
   * **Setup** (`settings.setup.php`) - Setup-Funktionen (Tabellen reparieren, Demo-Daten, URL-Profile, Struktur anlegen)

4. **Weitere Bereiche**
   * **Search** (`search.php`) - Globale Suche über Artikel, Varianten, Kategorien und Bestellungen
   * **Docs** (`docs.php`) - Dokumentation aus `/docs/` Markdown-Dateien (wird mit `rex_markdown::factory()->parseWithToc()` gerendert)
   * **Info** (`info.php`) - Übersichtsseite mit Add-on-Informationen
   * **System Log** (`system.log.warehouse.php`) - System-Log-Eintrag unter "System → Log"

## Fragmente und Templates

Fragmente sind PHP-basierte Templates zur Frontend-Ausgabe. Sie nutzen `rex_fragment` und werden mit `$fragment->parse('pfad/zum/fragment.php')` gerendert.

### Fragment-Struktur

Alle Frontend-Fragmente befinden sich in `/fragments/warehouse/bootstrap5/` und sind in Bootstrap 5 geschrieben.

#### Wichtige Fragment-Verzeichnisse

* **`/article/`** - Artikelansichten
  * `details.php` - Detailansicht eines Artikels mit Bildern, Preis, Beschreibung
  * `details_with_variants.php` - Detailansicht mit Varianten-Auswahl
  * `list.php` - Artikelliste (z.B. für Kategorieansichten)

* **`/cart/`** - Warenkorb-Ansichten
  * `cart.php` - Warenkorb-Tabelle mit Artikeln, Mengen, Preisen (Kern-Fragment)
  * `cart_page.php` - Komplette Warenkorb-Seite mit Navigation und Checkout-Button
  * `offcanvas_cart.php` - Offcanvas-Warenkorb für Overlay-Darstellung

* **`/checkout/`** - Checkout-Prozess
  * `checkout_page.php` - Haupt-Checkout-Seite
  * `form-guest.php` - Formular für Gast-Checkout mit Adressfeldern
  * `form-login.php` - Login-Formular für registrierte Kunden
  * `ycom_choose.php` - Auswahl zwischen Gast-Checkout und Login
  * `payment.php` - Zahlungsarten-Auswahl
  * `summary.php` - Bestellzusammenfassung vor Abschluss
  * `order_summary_page.php` - Bestellbestätigung nach erfolgreicher Bestellung

* **`/category/`** - Kategorieansichten
  * Templates für Kategorie-Listen und -Details

* **`/navigation/`** - Navigationskomponenten
  * Kategorie-Navigation, Breadcrumbs

* **`/paypal/`** - PayPal-Integration
  * PayPal-Button und Checkout-Flow

* **`/my_orders/`** - Bestellübersicht für eingeloggte Kunden
  * Liste der eigenen Bestellungen
  * Bestelldetails

* **`/template/`** - Basis-Templates und Layouts
  * Wiederverwendbare Template-Komponenten

* **`/ycom/`** - YCom-Integration
  * Login-, Registrierungs- und Profil-Formulare

### Fragment-Entwicklung: Richtlinien

1. **Bootstrap 5** - Alle Fragmente nutzen Bootstrap 5 CSS-Klassen
2. **Kein Inline-CSS/JS** - Verwende `data-warehouse-*` Attribute statt Inline-Scripts
3. **data-warehouse-* System** - Interaktivität über deklarative Attribute:
   * `data-warehouse-cart-page` - Container für Warenkorb-Seite
   * `data-warehouse-cart-table` - Warenkorb-Tabelle
   * `data-warehouse-offcanvas-cart` - Offcanvas-Warenkorb
   * `data-warehouse-article-detail` - Artikeldetail-Container
   * `data-warehouse-add-to-cart` - Button zum Warenkorb hinzufügen
   * `data-warehouse-remove-item` - Button zum Entfernen aus Warenkorb
   * `data-warehouse-quantity-input` - Mengen-Eingabefeld
   * `data-warehouse-cart-subtotal` - Zwischensumme-Anzeige
   * `data-warehouse-item-total` - Artikel-Gesamtpreis-Anzeige
   * Siehe `/docs/MIGRATION_GUIDE_JAVASCRIPT.md` und `/docs/FRONTEND_JAVASCRIPT.md` für vollständige Liste

4. **Nonce für Scripts** - Falls Inline-Scripts nötig sind: `<script nonce="<?= rex_response::getNonce() ?>">`

5. **Fragment-Variablen** - Übergabe via `$fragment->setVar('name', $value)`
   ```php
   $fragment = new rex_fragment();
   $fragment->setVar('article', $article);
   $fragment->setVar('cart', $cart);
   echo $fragment->parse('warehouse/bootstrap5/article/details.php');
   ```

6. **Labels und Texte - Wichtig: Unterschiedliche Methoden für Frontend und Backend!**
   
   **Frontend (Fragmente in `/fragments/warehouse/bootstrap5/` und E-Mails in `/fragments/warehouse/emails/`)**:
   * **Verwende AUSSCHLIESSLICH** `Warehouse::getLabel('key')`
   * Konfigurierbar in "Settings → Label" im Backend
   * Unterstützt Multi-Language via Sprog
   * Beispiele:
     ```php
     <?= Warehouse::getLabel('cart') ?>
     <?= Warehouse::getLabel('checkout') ?>
     <?= Warehouse::getLabel('add_to_cart') ?>
     ```
   
   **Backend (Seiten in `/pages/`, YForm-Konfigurationen, Backend-Fragmente)**:
   * **Verwende** `rex_i18n::msg('key')` für direkte Übersetzungen
   * **Oder** `translate:key` Placeholder in YForm-Feldern
   * Übersetzungen in `/lang/*.lang` Dateien (z.B. `de_de.lang`, `en_gb.lang`)
   * Beispiele:
     ```php
     // In Backend-Seiten:
     $field->setLabel(rex_i18n::msg('warehouse.settings.payment.store_name'));
     echo rex_view::title(rex_i18n::msg('warehouse.title'));
     
     // In YForm-Konfigurationen (z.B. settings.shipping.php):
     if (strpos($label, 'translate:') === 0) {
         $label = substr($label, strlen('translate:'));
         $label = rex_i18n::msg($label);
     }
     ```
   
   **Wichtig**: Diese Trennung ist essentiell, da Frontend-Labels dynamisch vom Shop-Betreiber angepasst werden können, während Backend-Texte fest übersetzt sind.

7. **Preisformatierung** - `Warehouse::formatCurrency($price)` für konsistente Währungsdarstellung

8. **URL-Generierung** - Über Domain-Klasse: `$domain->getArticleUrl($article)`, `$domain->getCartArtUrl()`

## YForm und Datenbank-Integration

Das Add-on nutzt YForm (YOrm) für die Datenbank-Verwaltung:

### Datenbanktabellen

Alle Tabellen haben den Präfix `rex_warehouse_`:

* `rex_warehouse_article` - Artikel mit Feldern wie name, description, price, stock, category_id, images
* `rex_warehouse_article_variant` - Varianten mit article_id (Relation), sku, price, stock
* `rex_warehouse_category` - Kategorien mit name, parent_id (hierarchisch), image
* `rex_warehouse_order` - Bestellungen mit order_no, customer_id, total, status, payment_id, shipping_address, billing_address
* `rex_warehouse_customer_address` - Kundenadressen mit customer_id, type (billing/shipping), address, zip, city, country
* `rex_warehouse_settings_domain` - Domain-Konfigurationen für Multi-Domain-Setups
* `rex_ycom_user` - Kunden (optional, wenn YCom installiert ist)

### YForm-Model-Klassen

In `boot.php` werden die Model-Klassen registriert:

```php
rex_yform_manager_dataset::setModelClass('rex_warehouse_article', Article::class);
rex_yform_manager_dataset::setModelClass('rex_warehouse_article_variant', ArticleVariant::class);
rex_yform_manager_dataset::setModelClass('rex_warehouse_category', Category::class);
rex_yform_manager_dataset::setModelClass('rex_warehouse_order', Order::class);
rex_yform_manager_dataset::setModelClass('rex_warehouse_settings_domain', Domain::class);
rex_yform_manager_dataset::setModelClass('rex_ycom_user', Customer::class);
rex_yform_manager_dataset::setModelClass('rex_warehouse_customer_address', CustomerAddress::class);
```

### Custom YForm-Felder

* `/lib/yform/field/value/warehouse_payment_options.php` - Custom YForm-Feld für Zahlungsarten-Auswahl
* `/lib/yform/field/value/warehouse_marker.php` - Custom YForm-Feld für Marker/Tags

### Projektspezifische Felder

Empfehlung: Verwende für projektspezifische Felder den Präfix `project_` in Feldnamen, z.B.:
* `project_manufacturer` - Hersteller
* `project_warranty_months` - Garantie in Monaten
* `project_custom_field` - Beliebiges Custom Field

Diese können via YForm in der Backend-Tabellenverwaltung hinzugefügt werden und sind dann in den Model-Klassen verfügbar.

## Extension Points und Hooks

Das Add-on bietet verschiedene Extension Points für eigene Anpassungen:

### Wichtige Extension Points

* **`WAREHOUSE_CART_ITEM_ADDED`** - Nach Hinzufügen eines Artikels zum Warenkorb
* **`WAREHOUSE_CART_ITEM_REMOVED`** - Nach Entfernen eines Artikels aus Warenkorb
* **`WAREHOUSE_CART_UPDATED`** - Nach Update des Warenkorbs
* **`WAREHOUSE_ORDER_CREATED`** - Nach Erstellen einer Bestellung
* **`WAREHOUSE_ORDER_STATUS_CHANGED`** - Nach Änderung des Bestellstatus
* **`WAREHOUSE_SHIPPING_CALCULATE`** - Für eigene Versandkostenberechnung
* **`WAREHOUSE_PAYMENT_PROCESS`** - Für eigene Zahlungsabwicklung
* **`WAREHOUSE_PRICE_CALCULATE`** - Für eigene Preisberechnung (z.B. Rabatte)

Verwendung:
```php
rex_extension::register('WAREHOUSE_ORDER_CREATED', function(rex_extension_point $ep) {
    $order = $ep->getSubject(); // Order-Objekt
    // Eigene Logik, z.B. E-Mail versenden, externe API aufrufen
});
```

### URL-Integration

In `boot.php` wird das URL-Addon integriert für SEO-freundliche URLs:
* `Url\Url::resolveCurrent()` - Ermittlung der aktuellen URL
* Extension Point `URL_SEO_TAGS` - Anpassung von SEO-Meta-Tags für Artikel und Kategorien

## Session und Warenkorb-System

### Session-Verwaltung

* Session wird mit `rex_login::startSession()` im Frontend gestartet (in `boot.php`)
* Session-Klasse (`Session.php`) bietet Wrapper-Methoden
* Warenkorb wird in Session unter Schlüssel `warehouse_cart` gespeichert

### Warenkorb-Logik

* Item-Keys: Eindeutige Identifikation von Artikeln/Varianten im Warenkorb
  * Format: `article_{id}` oder `variant_{id}`
* Mengenbasiert: Jeder Artikel/Variante hat eine Menge
* Berechnung: Zwischensumme, Steuern, Versandkosten, Gesamtsumme
* Persistenz: In Session, nicht in Datenbank (bis zur Bestellung)

### AJAX-Integration

* Frontend-Interaktionen über AJAX mit `rex_api_function`
* Endpunkte in `/lib/Api/` registriert in `boot.php`:
  ```php
  rex_api_function::register('warehouse_order', Api\Order::class);
  rex_api_function::register('warehouse_cart_api', Api\CartApi::class);
  ```
* Aufruf via JavaScript: `fetch('?rex-api-call=warehouse_cart_api&action=add')`

## Multi-Language und Multi-Domain

### Mehrsprachigkeit

* **Sprog-Integration** - Wenn Sprog installiert ist, können Labels übersetzt werden
* **YRewrite-Integration** - Sprachabhängige URLs über YRewrite
* **Language-Fallbacks** - In Fragment-Templates für E-Mails

### Multi-Domain

* Domain-Klasse (`Domain.php`) verwaltet verschiedene Domains
* Jede Domain kann eigene Konfigurationen haben:
  * Eigene Warenkorb-URL (`getCartArtUrl()`)
  * Eigene Bestell-URL (`getOrderArtUrl()`)
  * Eigene Artikel-URLs (`getArticleUrl()`)
* `Domain::getCurrent()` - Ermittlung der aktuellen Domain
* In `boot.php` wird aktuelle Domain als Property gespeichert

## PayPal-Integration

* **PayPal SDK** - Nutzt PayPal REST API (Server-SDK)
* **Sandbox-Modus** - Konfigurierbar für Tests
* **Button-Konfiguration** - Style, Farbe, Größe in Settings anpassbar
* **Checkout-Flow**:
  1. Kunde wählt PayPal als Zahlungsart
  2. PayPal-Button wird gerendert (Fragment `/paypal/`)
  3. Order wird erstellt via `PayPal::createOrder()`
  4. Nach Zahlung: Capture via `PayPal::capturePayment()`
  5. Order-Status wird aktualisiert
* **Konfiguration** in Settings → Payment:
  * Client ID und Secret (Live + Sandbox)
  * Erlaubte Zahlungsquellen
  * Button-Style-Optionen

## Development Guidelines

### Issue Analysis & Planning

Bei der Entwicklung von Funktionalitäten und beim Lösen von Bugs:
1. **Überprüfe vorhandene Informationen**: Ist ein Stack Trace vorhanden? Ist der Ablauf beschrieben?
2. **Stelle Nachfragen**: Wenn wichtige Informationen fehlen, brich ab und frage nach Details
3. **Beschreibe dein Vorgehen**: Erkläre, wie du das Problem lösen würdest, bevor du beginnst
4. **Grenze den Bereich ein**: Identifiziere die betroffenen Klassen, Methoden und Dateien

### Code Quality & Best Practices

**Wichtige Regeln**:

1. **Kein Inline-CSS/JS**: Vermeide inline-CSS und inline-JS. Falls erforderlich, verwende ein Nonce: `<script nonce="<?= rex_response::getNonce() ?>">`

2. **PHP Best Practices**:
   - Verwende Type Hinting für alle Funktionsparameter und Rückgabewerte
   - Nutze moderne PHP-Syntax (PHP 8.3)
   - Befolge PSR-12 Coding Standards

3. **Rückwärtskompatibilität**:
   - Achte auf Rückwärtskompatibilität bei zentralen Funktionen
   - Breaking Changes nur bei Major-Versionen
   - Bugfixes sollten keine Breaking Changes enthalten

4. **Einfachheit bevorzugen**:
   - Bevorzuge einfache, prägnante Lösungen
   - Vermeide überkomplexe Architekturen
   - Nur bei Bedarf komplexere Lösungen implementieren

5. **Semantic Versioning**:
   - **PATCH** (z.B. 2.0.1): Bugfixes, keine neuen Features
   - **MINOR** (z.B. 2.1.0): Neue Features, rückwärtskompatibel
   - **MAJOR** (z.B. 3.0.0): Breaking Changes
   - Versionsnummer in `package.yml` entsprechend anpassen

6. **Security Best Practices**:
   - Verhindere XSS durch korrekte Ausgabe-Escaping
   - Nutze YForm/YOrm für Datenbankzugriffe (verhindert SQL-Injection)
   - Validiere alle Benutzereingaben
   - Verwende `rex_csrf_token::factory()` für CSRF-Schutz

7. **Sprachunterstützung (Language Support) - KRITISCH**:
   
   **Frontend und E-Mails - NUR `Warehouse::getLabel()`**:
   - In allen Fragmenten unter `/fragments/warehouse/bootstrap5/`
   - In allen E-Mail-Templates unter `/fragments/warehouse/emails/`
   - **Niemals** `rex_i18n::msg()` im Frontend verwenden!
   - Grund: Frontend-Labels sind vom Shop-Betreiber anpassbar (Settings → Label)
   
   **Backend - NUR `rex_i18n::msg()` oder `translate:` Placeholder**:
   - In allen Backend-Seiten unter `/pages/`
   - In Backend-Fragmenten unter `/fragments/warehouse/backend/`
   - In YForm-Konfigurationen: `translate:key` Placeholder
   - **Niemals** `Warehouse::getLabel()` im Backend verwenden!
   - Übersetzungen in `/lang/*.lang` Dateien
   
   **Beispiele**:
   ```php
   // ✅ RICHTIG - Frontend Fragment:
   <h2><?= Warehouse::getLabel('cart_title') ?></h2>
   
   // ❌ FALSCH - Frontend Fragment:
   <h2><?= rex_i18n::msg('warehouse.cart_title') ?></h2>
   
   // ✅ RICHTIG - Backend Seite:
   $field->setLabel(rex_i18n::msg('warehouse.settings.payment.store_name'));
   
   // ❌ FALSCH - Backend Seite:
   $field->setLabel(Warehouse::getLabel('store_name'));
   ```

### Distribution

**Wichtig**: Es gibt keinen Build-Prozess. Das Add-on wird nicht über Composer/NPM/Yarn verteilt.
- Das Add-on wird direkt in REDAXO installiert
- Dependencies werden via Composer verwaltet, aber das Add-on selbst nicht
- Testing erfolgt in einer lokalen REDAXO-Instanz

## Code-Qualität und Linting

Dieses Projekt verwendet PHP-CS-Fixer für Code-Style-Prüfungen.

### PHP-CS-Fixer ausführen

**Voraussetzungen:**
- Composer muss installiert sein
- Dependencies müssen installiert sein: `composer install`

**Linting-Befehle:**

```bash
# Code-Style prüfen (Dry-Run, ohne Änderungen vorzunehmen)
composer cs-dry

# Code-Style automatisch korrigieren
composer cs-fix
```

**Wichtig:** Führe immer `composer cs-fix` vor dem Commit aus, um sicherzustellen, dass der Code den Coding-Standards entspricht.

**Hinweis:** Bei Pull Requests wird automatisch eine GitHub Action ausgeführt (`.github/workflows/code-style.yml`), die PHP-CS-Fixer anwendet und Code-Style-Fixes automatisch committed. Dennoch sollten Änderungen lokal vor dem Push geprüft werden.

### Code-Style-Regeln

Die Regeln basieren auf der REDAXO PHP-CS-Fixer-Konfiguration (`redaxo/php-cs-fixer-config`), die folgende Standards durchsetzt:
- PSR-12 Coding Standard
- REDAXO-spezifische Konventionen
- Strict Types Declaration
- Konsistente Code-Formatierung

## Testing & Validation

**Wichtig**: Es gibt keine automatisierten Unit-Tests. Alle Tests erfolgen manuell in einer REDAXO-Testinstanz.

### Manual Testing Workflow

#### 1. Installation Testing
```bash
# Add-on in REDAXO-Testumgebung kopieren (inkl. versteckter Dateien)
rsync -a /path/to/warehouse/ /path/to/redaxo/redaxo/src/addons/warehouse/

# Im REDAXO Backend:
# - AddOns → Warehouse → Installieren
# - Abhängigkeiten werden automatisch geprüft
```

#### 2. Functional Testing

**Frontend**:
- [ ] Artikelansicht (Details, Varianten)
- [ ] Kategorieansicht
- [ ] Warenkorb (Hinzufügen, Entfernen, Mengen ändern)
- [ ] Checkout-Prozess (Gast & Login)
- [ ] Bestellabschluss
- [ ] PayPal-Integration (Sandbox-Modus)
- [ ] Kundenkonten (wenn YCom installiert)

**Backend**:
- [ ] Bestellübersicht und -details
- [ ] Artikel-/Varianten-Verwaltung
- [ ] Kategorien-Verwaltung
- [ ] Einstellungen (General, Payment, Shipping, etc.)
- [ ] Dokumentations-Seite
- [ ] Setup-Funktionen

#### 3. Browser & Device Testing
- [ ] Chrome, Firefox, Safari
- [ ] Mobile responsive Design
- [ ] JavaScript-Funktionalität (data-warehouse-* System)
- [ ] AJAX-Endpunkte (Warenkorb, Checkout)

#### 4. Security Testing
- [ ] XSS-Verhinderung in Formularen
- [ ] CSRF-Token-Validierung
- [ ] SQL-Injection-Schutz (via YOrm)
- [ ] Zugriffskontrolle (Backend-Permissions)

### Documentation Validation

Nach jeder Code-Änderung:
1. **Prüfen**: Ist die Dokumentation in `/docs/` noch aktuell?
2. **Aktualisieren**: Betroffene Markdown-Dateien anpassen
3. **Testen**: Dokumentationsanzeige im Backend unter "Warehouse → Docs" prüfen
4. **Code-Beispiele**: Alle Code-Beispiele auf Korrektheit prüfen

## Development Workflow

### Standard Workflow für Code-Änderungen

#### 1. Requirements Analysis
- [ ] Issue sorgfältig lesen
- [ ] Stack Traces und Fehlermeldungen analysieren
- [ ] Fehlende Informationen beim Issue-Ersteller erfragen
- [ ] Betroffene Dateien und Klassen identifizieren

#### 2. Implementation
- [ ] **Minimale Änderungen**: Nur das Nötigste ändern
- [ ] **Rückwärtskompatibilität**: Bei nicht-Bugfixes beachten
- [ ] **Type Hints**: Alle Parameter und Rückgabewerte typisieren
- [ ] **Moderne PHP-Syntax**: PHP 8.3 Features nutzen
- [ ] **Security**: XSS, SQL-Injection, CSRF beachten

#### 3. Code Quality Check
```bash
# Code-Style automatisch korrigieren
composer cs-fix

# Nur prüfen (optional)
composer cs-dry
```

#### 4. Manual Testing
```bash
# Add-on in Test-REDAXO installieren (aus dem warehouse Verzeichnis)
cp -r . /path/to/redaxo/redaxo/src/addons/warehouse/

# Oder mit rsync (empfohlen, inkl. versteckter Dateien):
# rsync -a /path/to/warehouse/ /path/to/redaxo/redaxo/src/addons/warehouse/

# Im REDAXO Backend:
# - AddOns → Warehouse → Re-installieren oder Update
```

**Testing Checkliste**:
- [ ] Betroffene Frontend-Funktionen testen
- [ ] Betroffene Backend-Seiten prüfen
- [ ] Browser-Kompatibilität (Chrome, Firefox, Safari)
- [ ] Mobile Responsive Design
- [ ] JavaScript-Funktionalität
- [ ] PayPal-Integration (falls betroffen, Sandbox nutzen)

#### 5. Documentation Update
- [ ] `/docs/*.md` Dateien auf Aktualität prüfen
- [ ] Code-Beispiele anpassen
- [ ] Neue Features dokumentieren
- [ ] Backend-Dokumentation unter "Warehouse → Docs" testen

#### 6. Version Update (package.yml)
Semantic Versioning beachten:
- **PATCH** (2.0.1): Nur Bugfixes
- **MINOR** (2.1.0): Neue Features, rückwärtskompatibel
- **MAJOR** (3.0.0): Breaking Changes

#### 7. Commit & Pull Request
```bash
# Aussagekräftige Commit-Message
git commit -m "Fix: Beschreibung des Bugfix" 
# oder
git commit -m "Feature: Beschreibung des neuen Features"
```

**PR-Beschreibung sollte enthalten**:
- Was wurde geändert?
- Warum wurde es geändert?
- Wie wurde es getestet?
- Verlinkte Issues (#123)

## Code Verification Checklist

### Pre-Commit Verification

#### 1. Code Style & Linting
```bash
composer cs-fix  # Automatisch korrigieren
composer cs-dry  # Optional: Nur prüfen
```

#### 2. File Changes Review
```bash
git status
git diff
```
Prüfe:
- [ ] Nur relevante Dateien geändert?
- [ ] Keine ungewollten Änderungen (vendor/, node_modules/, etc.)?
- [ ] `.gitignore` korrekt konfiguriert?
- [ ] Keine Secrets oder Credentials committed?

#### 3. Code Quality Review
- [ ] **Minimale Änderungen**: Nur das Nötigste geändert?
- [ ] **Type Hinting**: Alle Parameter und Rückgabewerte typisiert?
- [ ] **Security**: XSS, SQL-Injection, CSRF-Schutz beachtet?
- [ ] **Fragmente**: `data-warehouse-*` Attribute statt Inline-JS?
- [ ] **Nonce**: Inline-Scripts mit `<?= rex_response::getNonce() ?>` ausgestattet?
- [ ] **Rückwärtskompatibilität**: Bei nicht-Bugfixes gewahrt?

#### 4. Documentation Check
- [ ] Relevante `/docs/*.md` Dateien aktualisiert?
- [ ] Neue Features dokumentiert?
- [ ] Code-Beispiele korrekt und funktionsfähig?
- [ ] `package.yml` Version erhöht (Semantic Versioning)?

### Post-Commit Verification

#### 1. CI/CD Pipeline (GitHub Actions)
Nach Push/PR wird automatisch ausgeführt:
- `.github/workflows/code-style.yml`: PHP-CS-Fixer
- Automatische Fixes werden committed
- Status im "Actions"-Tab prüfen
- Bei Fehlern: Im PR angezeigt

#### 2. Pull Request Review
Prüfe im PR:
- [ ] CI/CD Pipelines erfolgreich?
- [ ] Alle Änderungen wie erwartet?
- [ ] PR-Beschreibung vollständig?
- [ ] Issues korrekt verlinkt?

#### 3. Final Checks
- [ ] Version in `package.yml` korrekt erhöht?
- [ ] Dokumentation vollständig?
- [ ] Manuelle Tests erfolgreich durchgeführt?
