# Setup

Das Warehouse verfügt über ein rudimentäres Setup, mit dem die Grundinstallation erleichtert wird. Zusätzlich sind einige manuelle Schritte zu erledigen. Daher gibt es hier eine Art Schritt-für-Schritt-Anleitung.

### Benötigte AddOns (Schritt 1)

Das Warehouse baut grundsätzlich auf folgenden AddOns auf, die zuvor installiert werden müssen:

- yform
- yrewrite
- url (bitte die Version 2 verwenden aus dem Git Repo)

Zusätzlich sind folgende AddOns hilfreich:

- developer
- theme
- adminer
- Quick Navigation
- TinyMCE

Das Warehouse kann zusätzlich mit der Community ycom verbunden werden, sodass man eine Kundenverwaltung aufbauen kann, die Kunden sich registrieren können und Basisdaten (z.B. Adresse) in eine Bestellung übernommen werden kann.

Also ... Schritt 1 besteht draus, die AddOns zu installieren und z.B. yrewrite nach dem Rezept von yrewrite zu konfigurieren.

### Schritt 2 - Module und E-Mail Templates anlegen

Für folgende Module bringt das Warehouse *Muster* mit. Ganz dezidiert werden diese Module als Muster gekennzeichnet. Die Anforderungen an jeden Shop sind doch sehr unterschiedlich. Daher sind dies lediglich Beispiele für eine funktionierende Konfiguration. Wer kein Feld "Firma" beim Kunden braucht, der wirft das natürlich raus. Wer eigene Felder braucht, der macht diese Felder natürlich rein.

Dieses Setup wird als Administrator unter dem Menüpunkt "Warehouse" bei "Setup" ausgeführt. Anschließend stehen die Module und E-Mail Templates zur Verfügung. Alles muss natürlich individuell angepasst werden. Schließlich kann ich nicht wissen wie euer Shopname ist und wer bei euch die E-Mails für die Bestellungen bekommt.

### Schritt 3 - Die Struktur in REDAXO anlegen

Grundsätzlich findet die Präsentation der Kategorien und Produkte aus dem Warehouse in einem einzigen REDAXO-Artikel statt. Das ist wichtig. Fangt also nicht an eine eigene Kategoriestruktur für die Produktpräsentation im REDAXO anzulegen. Dafür sind die Kategorien im Warehouse zuständig.

Dafür gibt es für jeden einzelnen Schritt im Bestellvorgang eine eigene Seite in REDAXO. Diese könnt ihr als Artikel oder Kategorie anlegen. Da diese Seiten nicht in einem Menü angezeigt werden müssen, können diese beispielsweise in einer Kategorie "Shop" als Artikel angelegt werden. Folgende Artikel werden benötigt:

- Warenkorb (Anzeige des Warenkorbs)
- Adresseingabe (Eingabe der Adresse und Bestelldaten)
- Paypal Start (falls Paypal verwendet werden soll)
- Paypal Erfolg (dto.)
- Paypal Fehler (dto.)
- Danke

Die Seiten "Danke" und "Paypal Fehler" sind rein redaktionelle Seiten. Diese können mit eigenen redaktionellen Inhalten gefüllt werden.

Für alle anderen Seiten gibt es die vorbereiteten Module. Diese also einfach auf die Seiten platzieren.

### Schritt 4 - das Warehouse konfigurieren

Das Warehouse hat eine Konfigurationsseite. Die einzelnen Felder sind selbsterklärend. Es muss nicht zwingend alles ausgefüllt werden.

### Schritt 5 - weitere Konfigurationen

Der PHP Mailer muss noch konfiguriert werden. Bitte führt den Test aus, damit auch sicher gestellt ist, dass die Mails auch wirklich ankommen.

Url muss konfiguriert werden. Im ersten Schritt ist wichtig, dass die Warehouse Produktseite mit dem Parameter `category_id` aus der Warehouse Tabelle `rex_warehouse_categories` verknüpft wird.

## Templates

Es genügt ein einziges Template, welches den Artikel mit `REX_ARTICLE[]` ausgibt. Weitere Beschreibung folgt.

## Fragmente

## Varianten

Varianten können sowohl eigenständige Artikel sein, die sich ein Merkmal (z.B. einen Beschreibungstext, ein Bild, weitere Merkmale) teilen. Sie können aber genauso als eigentständige Artikel behandelt und angezeigt werden.
Wenn ein Artikel als Variantenartikel angelegt ist, so wird er auch stets als Variantenartikel behandelt. Wenn der Hauptartikel ohne Variante bestellbar ist, so muss also eine Hauptvariante angelegt werden.

## Attribute

Attribute sind dazu gedacht den Artikeln eine oder mehrere Eigenschafte zuweisen zu können (Größen bei Schuhen oder Kleider, Farben etc.) Die Attribute können zu Gruppen zusammengefasst werden. Ein Artikel, dem eine Attributgruppe zugewiesen wird, erhält dann die einzelnen Attribute der Attributgruppe. Die für den jeweiligen Artikel verfügbaren Attribute können dann aus dem Set ausgewählt werden.

### Select

Das Select ist für Attribute gedacht, die keine weiteren Parameter (bestellbar, Preisauf- bzw. -abschlag) haben. Beispielsweise Farben bei T-Shirts. In der Attribute Tabelle werden dann alle verfügbaren Attribute eingetragen. Beim Artikel selbst kann dann ausgewählt werden, welche Attribute für diese speziellen Artikel zur Verfügung stehen. Damit lässt sich beispielsweise eine Vereinheitlichung bei Farbcodierungen realisieren.
Die für den Artikel verfügbaren Attribute werden nicht als einzelne Datensätze sondern als Set in der Datenbank abgelegt. Daher ist es möglicherweise aufwändiger einen Filter nach Attributen zu realisieren die per Select definiert wurden.

### Widget

Widget ist die richtige Einstellung, wenn Attribute einen Preisauf- bzw. -abschlag enthalten können. Die Attribute können dann individuell beim jeweiligen Artikel konfiguriert werden.

*Hinweis* Bei der Verwendung des Widgets werden jeweils _alle_ Attribute eines Artikels neu in die Tabelle rex_warehouse_attribute_values geschrieben. Die alten Werte werden jeweils gelöscht. Damit ist es möglich, dass sich die Ids der Datensätze in der Tabelle rex_warehouse_attribute_values ändern. Die Ids sollten also nicht für eigene Zwecke verwendet werden.

## Menü erweitern

Manchmal ist es wünschenswert, dass das Hauptmenü der Website auch die Kategorien des Shops anzeigt. Da im Warehouse nur eine REDAXO-Seite für die gesamte Ausgabe der Shopkategorien und Artikel verwendet wird, muss die Hauptnavigation entsprechend erweitert werden. Hierfür stehen verschiedene Komponenten bereit, die entweder komplett oder in Einzelteilen verwendet werden können. Zunächst bringt der Shop eine eigene Navigationsklasse `warehouse_nav` mit. Diese kann weitestgehend durch Parameter den eigenen Bedürfnissen angepasst werden. Man kann auch die gesamte Klasse nehmen, ins eigene Projekt AddOn packen, die Klasse umbenennen und nach eigenen Bedürfnissen anpassen.

Eine Menudefinition kann dann z.B. so aussehen:

```
function shopmenu ($cat) {
    $menutype = explode('|',trim($cat->getValue('cat_menu_type'),'|'));
    if (in_array(2,$menutype)) {
        $fragment = new rex_fragment();
        return $fragment->parse('warehouse_shop_menu.php');
    } else {
        return '';
    }
}

$mainnav = new warehouse_nav();
$mainnav->ulClasses = ['uk-navbar-nav','uk-nav uk-navbar-dropdown-nav',''];
$mainnav->dataAttribute = ['uk-navbar="offset: 0"',''];
$mainnav->fullTree = 1;
$mainnav->func_li_end = 'shopmenu';
```

Zusätzlich wird jetzt noch eine Kategorie Metainfo mit dem Namen `menu_type` als Multiselect und eigenen Definitionen angelegt. Wenn der Wert der Metainfo einer Kategorie (im Beispielcode) 2 hat, so wird die Funktion `shopmenu` ausgeführt und die Rückgabe der Funktion in den Menücode eingefügt. Im Beispiel wird hier das Fragment warehouse_shop_menu.php verwendet.

## Syteminfos zur Laufzeit

Das Warehouse stellt verschiedene Werte zum aktuellen Status zur Verfügung, die in Modulen oder Templates genutzt werden können.

### Property warehouse_prop

Bei der Property handelt es sich um Werte, die bei der Initialisierung der Seite gesetzt werden. Dies sind:

- sitemode - hier wird angegeben, ob der Shop gerade eine Shopkategorie (category) oder einen Artikel (article) oder nichts von beidem (none) anzeigt.
- seo_title - 
- path - Der Pfad innerhalb der Shop Kategorien
- tree - Der gesamte Kategoriebaum des Shops

### warehouse_cart

Siehe Funktionen

## Funktionen

Das Warehouse bietet verschiedene Funktionen an, mit denen Werte und Zustände abgefragt werden können. Die aktuellen Funktionen und Parameter findet ihr in den Klassen, möglicherweise wird diese Dokumentation nicht ständig aktualisiert.

### (array) warehouse::get_cart()

Liefert den aktuellen Warenkorb als Array. Für jeden Artikel im Warenkorb wird ein Arrayelement geliefert. Das Arrayelement hat folgende Unterelemente:

[count] => Anzahl 
[price] => Bruttopreis
[name] => Name des Artikels
[cat_name] => Name der Kategorie
[cat_id] => Id der Kategorie
[description] => Beschreibung
[image] => Bild
[art_id] => Id des Artikels
[var_id] => Id der Variante (sofern vorhanden)
[var_whvarid] => Bestellnummer der Variante
[whid] => Bestellnummer des Artikels
[tax] => Property Wert für den Steuersatz (z.B. tax_value_1)
[free_shipping] => 1, wenn der Artikel versandkostenfrei ist, sonst 0
[attributes] => Attribute (array) sofern gesetzt
[price_netto] => Stückpreis Netto
[total] => Summe Brutto
[taxval] => Steuerbetrag (Summe)
[taxpercent] => Steuersatz (Prozent)

Mit `count(warehouse::get_cart())` bekommt man raus, ob was im Warenkorb liegt oder nicht (0).

## YForm Einstellungen

### Textfelder konfigurieren

Die Textfelder in Warehouse können sowohl unformatiertem Text als auch mit HTML befüllt werden. Wenn HTML verwendet werden soll, können beliebige Editoren (TinyMCE, CKE, Markdown, Textile) verwendet werden. Dies kann über die Formatierungsfunktion der yform Felder vorgenommen werden. Beispiel für Textile mit dem Profil tinyMCE-text: `{"class":"tinyMCE-text"}`. Für die Verwendung eines HTML Editors muss das entsprechende AddOn installiert sein.

### Textfelder ausblenden

Wenn ein Feld nicht gebraucht wird, so kann es in der Konfiguration des yform Tabellenfeldes per `{"style":"display:none"}` ausgeblendet werden. Damit das Label nicht angezeigt wird, muss der Inhalt gelöscht werden. Alternativ kann man auch ein HTML Element um das Feld setzen und dies auf `display:none` formatieren.

### Mehrsprachigkeit

Textfelder sind generell mit dem Feldnamensuffix `_1` angelegt. Damit lässt sich der Shop auch mehrsprachig umsetzen. Bei einer zweisprachigen Website müssen die benötigten Textfelder zusätzlich über yform und dem Suffix `_2` angelegt werden. In den Fragmenten bzw. bei der Ausgabe kann dann mit `"feldnamen_".rex_clang::getCurrentId()` auf das Feld zugegriffen werden.

### Strukturierte Daten (structured data)

Das Beispielfragment warehouse_scheme_article_with_variants gibt strukturierte Daten aus.

## Templates

Die Templates benötigen nur geringe Anpassungen. Im Standardmodul für die Adresseingabe wird JQuery Code verwendet, um bei Zahlungsweise SEPA Lastschrift die Eingabefelder für die Bankverbindung einzublenden. Damit jQuery am Schluss der Seite geladen werden kann und der Code dennoch ausgeführt wird, wird er mit `rex::setProperty('js',$jscode)` gespeichert. Wenn dieser Code ausgeführt werden soll, ist ein `<?= rex::getProperty('js',''); ?>` vor dem schließenden `</body>`-Tag zu setzen.

Mustertemplates sind in der Beispielinstallation enthalten.

## E-Mail Templates

Die E-Mail Templates aus der Grundinstallation müssen noch für eigene Zwecke angepasst werden. Um bei einer Auswahl der Zahlungsweise _Vorauskasse_ einen Text im E-Mail Template auszugeben, kann folgender Code verwendet werden:

```
<?php if (REX_YFORM_DATA[field="payment_type"] == "prepayment") : ?>
Bitte überweisen Sie den Betrag auf das Konto
Wolfgang Bund
IBAN: DE19 6835 0048 0001 7140 21
BIC: SKLODE66XXX
<?php endif ?>
```


## Einen Shop als neue REDAXO Installation - ein Beispiel

### Benötigte AddOns

- yform
- yrewrite
- sprog

### Zusätzlich sinnvolle AddOns

- Blöcks
- Quicknavigation
- TinyMCE
- Developer
- Theme

*Schritt 1*
REDAXO Basis Installation auf neuer Domain/Subdomain oder lokaler Entwicklungsumgebung

*Schritt 2*
Über den Installer die oben benötigten AddOns herunterladen und installieren.

*Schritt 3*
Über Github das AddOn Url2 Version 3 beta herunterladen und installieren.
Über Github das AddOn warehouse herunterladen und installieren.

*Schritt 4*
Minimal Beispielimport Datenbank und Dateien installieren. Die Beispieldateien liegen dem AddOn im Verzeichnis `install/demo` bei.

*Schritt 5*
PHP Mailer konfigurieren.
Für Paypal Bestellungen in Warehouse Paypal Parameter ergänzen.

*Bekannte Fehler*
In der obigen Konfiguration kann die Bestelltabelle nicht aufgerufen werden. Hierfür muss zusätzlich die ycom installiert werden und in der ycom Usertabelle das Feld company angelegt werden.
Die Bilder in der Demo sind absichtlich verkleinert.

## Credits

Yakamara, Jan Kristinus, Thomas Blum, Gregor Harlan.
Thomas Skerbis, Daniel Weitenauer, Oliver Kreischer und viele mehr.
