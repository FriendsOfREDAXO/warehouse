# warehouse
Warehouse: REDAXO Shop AddOn

Das Warehouse stellt Basisfunktionalitäten für einen Webshop in REDAXO zur Verfügung:

* ein flexible Produktdatenbank auf yform Basis für Kategorien, beliebig viele Unterkategorien, Artikel, Varianten und Attribute
* einen Warenkorb
* einen Bestellprozess inkl. PayPal SDK auf Basis der api v2

Die Ausgabe basiert auf Fragmenten, sodass der Shop sich in jede Umgebung einfügen lässt.
Über das AddOn ycom ist eine Benutzerverwaltung möglich.

Aktuell handelt es sich noch um einen Prototyp.

Eine Livevorschau kann man hier anschauen: https://warehouse.ferien-am-tressower-see.de/ - diese ist allerdings nicht zwingend auf dem neuesten Stand.

Von diesem Liveshop sind Demodaten im AddOn enthalten. Sowohl Files als auch Datenbank können aus dem Verzeichnis install/demo in REDAXO importiert werden. Achtung! Vorhandene Inhalte, Templates und Module werden dabei gelöscht.

## Gewichte

Ebenso wie eine Versandberechnung nach Warenwert und nach Stückzahl möglich ist, ist auch eine Berechnung der Versandkosten nach Gewicht möglich. Um sicherzustellen, dass auch ein Gewicht im Artikel eingetragen wird, kann man in den Einstellungen festlegen, dass im Backend das Gewicht > 0 überprüft wird. In der Artikeltabelle muss hierfür eine Customfunction für die Validierung eingetragen werden: warehouse::check_input_weight, das zugehörige Feld muss das Feld weight sein. Wenn es sich nicht um Variantenartikel handelt, wird das Gewicht des Hauptartikels verwendet. Wenn bei der Variante 0 als Gewicht eingetragen wird, wird ebenfalls das Gewicht des Hauptartikels zur Berechnung verwendet.

## Staffelpreise

Sowohl für Artikel als auch für Varianten lassen sich Staffelpreise hinterlegen. Wenn für Varianten unterschiedliche Preise gelten, so muss beim Hauptartikel der Preis 0 eingetragen werden und für jede Variante muss ein Preis erfasst werden.

## Einzelartikel (neu in 0.2)

Es gibt jetzt ganz neu, auf vielfachen Wunsch, eine Möglichkeit Einzelartikel in den Shop zu packen. Das heißt: der Artikel wird nicht in der Artikeltabelle von warehouse abgelegt sondern ganz easy als REDAXO Artikel angelegt. Im REDAXO Artikel muss man dann das Modul "Warehouse Einzelartikel" einbauen. Als Moduleingabe kann man lediglich eine Artikelnummer angeben, einen Artikelnamen und einen Preis. WICHTIG: Die Artikelnummer muss unique, also einmalig sein, denn sonst findet das System den Artikel nicht korrekt. Für alle Insider: der Slice speichert zusätzlich hidden im value20 noch den Wert wh_single. Der Slice muss online sein. Über dieses Modul "Warehouse Einzelartikel" wird nur der Bestellteil (also Eingabemöglichkeit der Anzahl und Bestellbutton und Preis) ausgegeben. Die Artikelbeschreibung wird in normalen Inhaltsmodulen aufgebaut.

Auf einer REDAXO Artikelseite können mehrere Blöcke "Warehouse Einzelartikel" angelegt werden.

In dieser Version wird auch immer der Mehrwertsteuersatz aus Steuersatz 1 verwendet. Es ist das auch wiederum mehr oder weniger ein Beispiel, dass das Warehouse sehr flexibel auf eigene Bedürfnisse angepasst werden kann. Wenn jetzt also die Anfrage kommt: Einzelartikel mit Varianten - geht das? Natürlich geht das, aber es ist nicht ausprogrammiert.

## Multidomainfähigkeit (neu in 1.1.beta)

Das Warehouse hat eine Multidomainfähigkeit bekommen. Das heißt, dass der Warenkorb, der Checkout und die E-Mail Einstellungen für jede installierte Domain individuell vorgenommen werden können. Die Parameter werden jeweils mit ihrer Domain-Id abgelegt. Es gibt also einen Eintrag `cart_page` für die allgemeine Domain und einen Eintrag `cart_page_2` für die zweite Domain. Der Aufruf `warehouse::get_config("cart_page")` liefert den gewünschten Wert für die aktuelle Domain. Wenn für die Domain kein Wert gefunden wird, wird der Standardwert aus dem Eintrag `cart_page` geliefert. Die Einträge für die Domain sind daher optional.


## Installation der Demo

### Benötigte AddOns

- yform
- yrewrite
- sprog

### Zusätzlich sinnvolle AddOns

- Quicknavigation
- TinyMCE
- Developer
- Theme

#### Schritt 1
REDAXO Basis Installation auf neuer Domain/Subdomain oder lokaler Entwicklungsumgebung

#### Schritt 2
Über den Installer die oben benötigten AddOns herunterladen und installieren.

#### Schritt 3
Über Github das AddOn [Url](https://github.com/tbaddade/redaxo_url) mindestens Version 2.0.0-beta3 herunterladen und installieren.
Über Github das AddOn warehouse herunterladen und installieren.

#### Schritt 4
Minimal Beispielimport Datenbank und Dateien installieren. Die Beispieldateien liegen dem AddOn im Verzeichnis `install/demo` bei.

#### Schritt 5
PHP Mailer konfigurieren.
Für Paypal Bestellungen in Warehouse Paypal Parameter ergänzen.

### Ergänzungen

im Warenkorb kann die Anzahl per Input-Felder geändert werden.
<input type="hidden" name="action" value="modify_cart">
<input type="hidden" name="mod" value="qty">
<input name="<?= $uid ?>" type="text" maxlength="3" value="<?= $item['count'] ?>">

#### Bekannte Fehler
In der obigen Konfiguration kann die Bestelltabelle nicht aufgerufen werden. Hierfür muss zusätzlich die ycom installiert werden und in der ycom Usertabelle das Feld company angelegt werden.
Die Bilder in der Demo sind absichtlich verkleinert.

Use at your own risk. Issues gerne auf Github https://github.com/FriendsOfREDAXO/warehouse packen.
