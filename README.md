# Warehouse 2 - Shop-Addon für REDAXO ^5.17 (Work in Progress)

Das Warehouse stellt Basisfunktionalitäten für einen Webshop in REDAXO zur Verfügung:

* Produktdatenbank auf YForm-Basis
* Kategorien, Artikel, Varianten und Attribute
* Warenkorb
* Bestellprozess inkl. PayPal SDK auf Basis der API v2

Die Ausgabe basiert auf Fragmenten, sodass der Shop sich in jede Umgebung einfügen lässt.

Über das AddOn `ycom` ist eine Benutzerverwaltung möglich.

## Installation

### Vorausssetzungen

* REDAXO ^5.17
* PHP ^8.3
* YForm ^4.1
* YRewrite ^2.9

optional:

* Für Mehrsprachigkeit wird das AddOn `sprog` benötigt.
* Für SEO-freundliche URLs wird das AddOn `url` benötigt.

Nicht vergessen:

* PHP Mailer konfigurieren.
* Für Paypal-Bestellungen in Warehouse Paypal-Einstellungen ergänzen.

## Weitere Features

### Gewichte

Ebenso wie eine Versandberechnung nach Warenwert und nach Stückzahl möglich ist, ist auch eine Berechnung der Versandkosten nach Gewicht möglich. Um sicherzustellen, dass auch ein Gewicht im Artikel eingetragen wird, kann man in den Einstellungen festlegen, dass im Backend das Gewicht > 0 überprüft wird. In der Artikeltabelle muss hierfür eine Customfunction für die Validierung eingetragen werden: warehouse::check_input_weight, das zugehörige Feld muss das Feld weight sein. Wenn es sich nicht um Variantenartikel handelt, wird das Gewicht des Hauptartikels verwendet. Wenn bei der Variante 0 als Gewicht eingetragen wird, wird ebenfalls das Gewicht des Hauptartikels zur Berechnung verwendet.

### Staffelpreise

Sowohl für Artikel als auch für Varianten lassen sich Staffelpreise hinterlegen. Wenn für Varianten unterschiedliche Preise gelten, so muss beim Hauptartikel der Preis 0 eingetragen werden und für jede Variante muss ein Preis erfasst werden.

### Einzelartikel

Es gibt jetzt ganz neu, auf vielfachen Wunsch, eine Möglichkeit Einzelartikel in den Shop zu packen. Das heißt: der Artikel wird nicht in der Artikeltabelle von warehouse abgelegt sondern ganz easy als REDAXO Artikel angelegt. Im REDAXO Artikel muss man dann das Modul "Warehouse Einzelartikel" einbauen. Als Moduleingabe kann man lediglich eine Artikelnummer angeben, einen Artikelnamen und einen Preis. WICHTIG: Die Artikelnummer muss unique, also einmalig sein, denn sonst findet das System den Artikel nicht korrekt. Für alle Insider: der Slice speichert zusätzlich hidden im value20 noch den Wert warehouse_single. Der Slice muss online sein. Über dieses Modul "Warehouse Einzelartikel" wird nur der Bestellteil (also Eingabemöglichkeit der Anzahl und Bestellbutton und Preis) ausgegeben. Die Artikelbeschreibung wird in normalen Inhaltsmodulen aufgebaut.

Auf einer REDAXO Artikelseite können mehrere Blöcke "Warehouse Einzelartikel" angelegt werden.

In dieser Version wird auch immer der Mehrwertsteuersatz aus Steuersatz 1 verwendet. Es ist das auch wiederum mehr oder weniger ein Beispiel, dass das Warehouse sehr flexibel auf eigene Bedürfnisse angepasst werden kann. Wenn jetzt also die Anfrage kommt: Einzelartikel mit Varianten - geht das? Natürlich geht das, aber es ist nicht ausprogrammiert.

### Multidomainfähigkeit

Das Warehouse hat eine Multidomainfähigkeit bekommen. Das heißt, dass der Warenkorb, der Checkout und die E-Mail Einstellungen für jede installierte Domain individuell vorgenommen werden können. Die Parameter werden jeweils mit ihrer Domain-Id abgelegt. Es gibt also einen Eintrag `cart_page` für die allgemeine Domain und einen Eintrag `cart_page_2` für die zweite Domain. Der Aufruf `warehouse::get_config("cart_page")` liefert den gewünschten Wert für die aktuelle Domain. Wenn für die Domain kein Wert gefunden wird, wird der Standardwert aus dem Eintrag `cart_page` geliefert. Die Einträge für die Domain sind daher optional.

### Ergänzungen

im Warenkorb kann die Anzahl per Input-Felder geändert werden.

```php
<input type="hidden" name="action" value="modify_cart">
<input type="hidden" name="mod" value="qty">
<input name="<?= $uid ?>" type="text" maxlength="3" value="<?= $item['count'] ?>">
```

### Bekannte Fehler

In der obigen Konfiguration kann die Bestelltabelle nicht aufgerufen werden. Hierfür muss zusätzlich die ycom installiert werden und in der ycom Usertabelle das Feld company angelegt werden.
Die Bilder in der Demo sind absichtlich verkleinert.

## Lizenz, Autor, Credits

### Lizenz

MIT Lizenz, siehe [LICENSE.md](https://github.com/FriendsOfREDAXO/warehouse/blob/main/LICENSE.md)

### Autor

**Friends Of REDAXO**
[https://github.com/FriendsOfREDAXO](https://github.com/FriendsOfREDAXO)

### Projekt-Leads

[Alexander Walther](https://github.com/alxndr-w), [Thomas Rotzek](https://github.com/rotzek)

### Credits

[Contributors:](https://github.com/FriendsOfREDAXO/consent_manager/graphs/contributors)

Ursprüngliche Entwicklung von: [Wolfgang Bund](https://github.com/dtpop).
