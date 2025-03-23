# Warehouse 2 - Shop-Add-on für REDAXO ^5.17 (Work in Progress)

Das Warehouse stellt Basisfunktionalitäten für einen Webshop in REDAXO zur Verfügung:

* Produktdatenbank auf YForm-Basis
* Kategorien, Artikel, Varianten und Attribute
* Warenkorb
* Bestellprozess inkl. PayPal SDK auf Basis der API v2
* Extension Points für eigene Anpassungen (z. B. Versandkostenberechnung)

Die Ausgabe basiert auf Fragmenten, sodass der Shop sich in jede Umgebung einfügen lässt.

Über das Add-on `ycom` ist eine Benutzerverwaltung möglich.

## Installation

### Voraussetzungen

* REDAXO ^5.17
* PHP ^8.3
* YForm ^4.1
* YForm Field ^2.9
* YRewrite ^2.9

optional:

* Für Mehrsprachigkeit wird das Add-on `sprog` benötigt.
* Für SEO-freundliche URLs wird das Add-on `url` benötigt.
* Für Kundenkonten und Login wird das Add-on `ycom` benötigt.

Nicht vergessen:

* PHP Mailer konfigurieren.
* Für Paypal-Bestellungen in Warehouse Paypal-Einstellungen ergänzen.

## Weitere Features

### Artikel und Varianten

Artikel bestehen standardmäßig aus ID, Name und optionalen Eigenschaften. Über YForm können beliebig viele weitere Felder hinzugefügt werden. Empfehlung: Verwende für projektspezifische Felder den Präfix `project_` in deinen Feldnamen.

```php
// Findet alle verfügbaren Artikel
FriendsOfREDAXO\Warehouse\Article::query()->find();
```

### Staffelpreise

Für Artikel und Varianten können zusätzlich Staffelpreise (Mengenrabatt) hinterlegt werden.

### Gewicht und Versandkosten

Artikel können ein Gewicht haben, das für die Versandkostenberechnung genutzt wird. Die Versandkosten können nach Warenwert, Stückzahl oder Gewicht berechnet werden.

> Hinweis: In Version 2 gibt es ein Feld, um Gewicht zu hinterlegen - die Versandkostenberechnung muss jedoch vom Entwickler über den Extension Point `WAREHOUSE_` implementiert werden.

### Steuern

Artikel können mit einem Steuersatz versehen werden. Standardmäßig stehen `0%`, `7%` und `19%` zur Auswahl.

### Lagerbestand

Artikel können einen Lagerbestand haben, der beim Kauf automatisch aktualisiert wird.

### Direktkauf

Artikel können ohne Warenkorb direkt gekauft und bezahlt werden.

### Zahlungsmöglichkeiten

Standardmäßig stehen `PayPal` und `Vorkasse` zur Verfügung. Weitere Zahlungsmöglichkeiten können über den Extension Point `WAREHOUSE_PAYMENT` hinzugefügt werden.

### Multidomain-Fähigkeit

In Warehouse 2 wurde die Multidomain-Fähigkeit verbessert. Es können jetzt beliebig viele Domains.

### Mehrsprachigkeit

In Warehouse 2 gibt es derzeit keine integrierte Mehrsprachigkeit für Artikel. Es wird empfohlen, das Add-on `sprog` zu verwenden. Zusätzlich können die Artikel und Varianten um eine eigene Sprachenverwaltung erweitert werden, z. B. per eigener Datenbank-Tabelle mit `be_manager_relation`.

### Rabatte und Gutschein-Codes

Rabatte und Gutschein-Codes können über den Extension Point `WAREHOUSE_DISCOUNT` hinzugefügt werden.

### Kundenkonto

Über das Add-on `ycom` können Kundenkonten und Rechnungsadresse sowie Lieferadresse angelegt werden.

## Lizenz, Autor, Credits

### Lizenz

MIT-Lizenz, siehe [LICENSE.md](https://github.com/FriendsOfREDAXO/warehouse/blob/main/LICENSE.md)

### Autor

**Friends Of REDAXO**
[https://github.com/FriendsOfREDAXO](https://github.com/FriendsOfREDAXO)

### Projekt-Leads

[Alexander Walther](https://github.com/alxndr-w), [Thomas Rotzek](https://github.com/rotzek)

### Credits

[Contributors:](https://github.com/FriendsOfREDAXO/consent_manager/graphs/contributors)

Ursprüngliche Entwicklung von: [Wolfgang Bund](https://github.com/dtpop).
