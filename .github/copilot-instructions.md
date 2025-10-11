Dieses Repository ist eine Erweiterung (Add-on) für das REDAXO CMS https://github.com/redaxo/redaxo, das Shop-Funktionalitäten zu Verfügung stellt:

1. Eine Produktverwaltung ("Artikel" und "Varianten") mit Kategorien und Eigenschaften wie Preis, Lagerbestand, Beschreibung, Verfügbarkeit
2. Ausgabe-Templates ("Fragmente"), die den Shop im Frontend anzeigen
3. Installationsroutinen, die die Einrichtung und den Betrieb vornehmen.

Das Add-on basiert auf der Erweiterung YForm und YOrm, einer Datenbanktabellen-Verwaltung mit Benutzeroberfläche sowie einem ORM für die Datenverarbeitung, es nutzt außerdem Core-Klassen (REDAXO Core-System) und etablierte Erweiterungen (Add-ons) für REDAXO namens `URL` und `YRewrite` für die Steuerung von SEO-optimierten URLs und Metadaten.

Du findest Informationen zu REDAXO und den entsprechenden Klassen und Konzepten unter folgenden URLs und Repositories:

* Technische Dokumentation und Tutorials / Erläuterungen REDAXO: <http://github.com/redaxo/docs> (z.B. zu Socket-Verbindungen, Extension Points, Backend-Pages und Service-Klassen wie rex_sql, rex_config_form, ...)
* Das Core-System und Core-Addons von REDAXO: https://github.com/redaxo/redaxo, insb. https://github.com/redaxo/redaxo <https://github.com/redaxo/redaxo/tree/main/redaxo/src/core/lib>. Hinweis: Die Dateien zu den Klassennamen enthalten nicht den Präfix `rex_`, d.h. z.B. die Klasse `rex_socket` ist in `redaxo/src/core/lib/util/socket/socket.php`, oder die Klasse `rex_fragment` ist in `redaxo/src/core/lib/fragment.php`.

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

6. **Labels und Texte** - Über `Warehouse::getLabel('key')` oder `rex_i18n::msg('key')`
   * Konfigurierbar in "Settings → Label"
   * Unterstützt Multi-Language via Sprog

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

## Entwicklungs-Richtlinien

Bei der Entwicklung von Funktionalitäten und beim Lösen von Bugs soll überprüft werden, ob nötige Informationen durch den Issue-Ersteller gegeben wurden, bspw. ein Stack Trace, ein Ablauf wann etwas zu einem Problem führt oder welcher Bereich oder Methode als Ausgangspunkt für eine Lösung gewählt werden soll. Wenn diese Informationen nicht gegeben wurden oder aus dem Kontext zu erschließen sind, brich ab und stelle Nachfragen, um den Bereich deiner Lösung einzugrenzen. Schildere, wie du vorgehen würdest.

### Code-Qualität und Best Practices

Achte auf folgende Regeln:

1. Vermeide inline-CSS und inline-JS - wenn doch erforderlich, dann mit einem Nonce ausstatten.
2. Verwende Best Practices in der PHP-Entwicklung wie Typisierung / Type Hinting.
3. Achte auf Rückwärtskompatibilität, wenn es sich um eine zentrale Funktion oder Funktionsänderung handelt und nicht nur um einen Bugfix.
4. Vermeide überkomplexe Lösungen und bevorzuge kurze prägnante, der Aufgabenstellung angemessene Lösungen - arbeite nur komplexer, wenn du dazu aufgefordert wirst oder nach Rückfrage, wenn du nach 3-4 Minuten Laufzeit nicht zu einer guten Lösung gekommen bist.
5. In einem PR wird die Versionsnummer `version` gemäß Semantic Versioning in der `package.yml` erhöht - je nachdem, ob es ein Bugfix/Patch, Minor Update mit neuen Funktionen oder Major Update ohne Rückwärtskompatibilität handelt. Mache in deinem PR einen Vorschlag, was ausgehend vom main-Repository die nächste Versionsnummer wäre.

Es gibt keinen Build-Prozess, sodass du den Code selbst testen kannst. Es ist nicht über composer, yarn oder einen anderen Pakete-Manager verfügbar.

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

### Code-Style-Regeln

Die Regeln basieren auf der REDAXO PHP-CS-Fixer-Konfiguration (`redaxo/php-cs-fixer-config`), die folgende Standards durchsetzt:
- PSR-12 Coding Standard
- REDAXO-spezifische Konventionen
- Strict Types Declaration
- Konsistente Code-Formatierung

## Testing und Validation

### Manuelle Tests

Da es keinen automatisierten Test-Suite gibt, müssen Änderungen manuell getestet werden:

1. **Installation in REDAXO-Testumgebung:**
   - Kopiere das Add-on in den REDAXO `addons/` Ordner
   - Installiere das Add-on über das Backend
   - Prüfe, ob alle Abhängigkeiten erfüllt sind

2. **Funktionale Tests:**
   - Teste alle geänderten Funktionen im Frontend und Backend
   - Prüfe die Warenkorb-Funktionalität
   - Teste den Checkout-Prozess
   - Validiere die Bestellabwicklung
   - Prüfe PayPal-Integration (Sandbox-Modus)

3. **Browser-Tests:**
   - Teste in verschiedenen Browsern (Chrome, Firefox, Safari)
   - Prüfe responsive Design auf mobilen Geräten
   - Validiere JavaScript-Funktionalität

4. **Backend-Tests:**
   - Teste alle Backend-Seiten (Order, Settings, Docs)
   - Prüfe YForm-Tabellen und Formulare
   - Validiere Setup-Funktionen

### Dokumentations-Validierung

Nach Code-Änderungen:
1. Prüfe, ob die Dokumentation in `/docs/` noch aktuell ist
2. Aktualisiere betroffene Markdown-Dateien
3. Teste die Dokumentationsanzeige im Backend unter "Warehouse → Docs"

## Entwicklungs-Workflow

### Typischer Workflow für eine Code-Änderung:

1. **Verstehen der Anforderung:**
   - Lies das Issue sorgfältig
   - Prüfe Stack Traces und Fehlermeldungen
   - Stelle Nachfragen, wenn Informationen fehlen

2. **Code-Änderungen vornehmen:**
   - Mache minimale, gezielte Änderungen
   - Achte auf Rückwärtskompatibilität
   - Verwende Type Hints und moderne PHP-Syntax

3. **Code-Style prüfen:**
   ```bash
   composer cs-fix
   ```

4. **Manuelle Tests durchführen:**
   - Installiere das Add-on in einer Test-REDAXO-Instanz
   - Teste alle betroffenen Funktionen
   - Prüfe Frontend und Backend

5. **Dokumentation aktualisieren:**
   - Aktualisiere relevante `/docs/*.md` Dateien
   - Prüfe `docs/00_documentation_overview.md` auf Vollständigkeit

6. **Version aktualisieren:**
   - Erhöhe die Version in `package.yml` nach Semantic Versioning
   - PATCH (2.0.1): Bugfixes
   - MINOR (2.1.0): Neue Features (rückwärtskompatibel)
   - MAJOR (3.0.0): Breaking Changes

7. **Commit und PR:**
   - Schreibe aussagekräftige Commit-Messages
   - Beschreibe alle Änderungen im PR
   - Verlinke relevante Issues

## Wie Code-Änderungen zu verifizieren sind

### Vor dem Commit:

1. **Linting:**
   ```bash
   composer cs-dry  # Prüfen
   composer cs-fix  # Korrigieren
   ```

2. **Dateien prüfen:**
   - Wurden nur relevante Dateien geändert?
   - Keine ungewollten Änderungen an vendor/, node_modules/, etc.?
   - `.gitignore` korrekt konfiguriert?

3. **Code-Review:**
   - Sind alle Änderungen minimal und fokussiert?
   - Wurde Type Hinting verwendet?
   - Sind Security-Best-Practices beachtet (kein XSS, SQL-Injection, etc.)?
   - Verwenden Fragmente `data-warehouse-*` Attribute statt Inline-JS?

### Nach dem Commit:

1. **GitHub Actions überprüfen:**
   - Warten auf CI/CD Pipeline-Ergebnisse (falls vorhanden)
   - Prüfe auf Fehler oder Warnungen

2. **Dokumentation:**
   - Ist die Dokumentation aktuell?
   - Sind alle neuen Features dokumentiert?
   - Sind Code-Beispiele korrekt?

3. **Version:**
   - Wurde die Version in `package.yml` korrekt erhöht?
   - Entspricht die Versionserhöhung der Art der Änderung?
