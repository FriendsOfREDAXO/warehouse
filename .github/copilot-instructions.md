Dieses Repository ist eine Erweiterung (Add-on) für das REDAXO CMS https://github.com/redaxo/redaxo, das Shop-Funktionalitäten zu Verfügung stellt:

1. Eine Produktverwaltung ("Artikel" und "Varianten") mit Kategorien und Eigenschaften wie Preis, Lagerbestand, Beschreibung, Verfügbarkeit
2. Ausgabe-Templates ("Fragmente"), die den Shop im Frontend anzeigen
3. Installationsroutinen, die die Einrichtung und den Betrieb vornehmen.

Das Add-on basiert auf der Erweiterung YForm und YOrm, einer Datenbanktabellen-Verwaltung mit Benutzeroberfläche sowie einem ORM für die Datenverarbeitung, es nutzt außerdem Core-Klassen (REDAXO Core-System) und etablierte Erweiterungen (Add-ons) für REDAXO namens `URL` und `YRewrite` für die Steuerung von SEO-optimierten URLs und Metadaten.

Du findest Informationen zu REDAXO und den entsprechenden Klassen und Konzepten unter folgenden URLs und Repositories:

* Technische Dokumentation und Tutorials / Erläuterungen REDAXO: <http://github.com/redaxo/docs> (z.B. zu Socket-Verbindungen, Extension Points, Backend-Pages und Service-Klassen wie rex_sql, rex_config_form, ...)
* Das Core-System und Core-Addons von REDAXO: https://github.com/redaxo/redaxo, insb. https://github.com/redaxo/redaxo <https://github.com/redaxo/redaxo/tree/main/redaxo/src/core/lib>. Hinweis: Die Dateien zu den Klassennamen enthalten nicht den Präfix `rex_`, d.h. z.B. die Klasse `rex_socket` ist in `redaxo/src/core/lib/util/socket/socket.php`, oder die Klasse `rex_fragment` ist in `redaxo/src/core/lib/fragment.php`. 

Struktur:
* `install.php` - wird ausgeführt bei der Installation
* `uninstall.php` - wird ausgeführt bei der Deinstallation
* `update.php` - wird ausgeführt beim Update der Erweiterung über den REDAXO Installer
* `boot.php`: wird ausgeführt bei jedem Seitenaufruf
* `/lib/`: der Ordner, der die Klassen enthält
* `/docs/`: der Ordner, der Markdown-Dokumente zur technischen Dokumentation enthält und bei Code- und Funktionsanpassungen auf Korrektheit zu überprüfen ist
* `/install/`: der Ordner, der zusätzliche Dateien zur Installation enthält und für die Funktionalität erforderlich ist, bspw. Tabellenschema-Definitionen für die Datenbanktabellen von `warehouse`
* `/pages/`: der Ordner, der die Backend-Ansicht mit Oberfläche enthält, um Daten zu verwalten und Konfigurationen vorzunehmen.
* `/fragments/`: der Ordner, der Ausgabe-Templates (in REDAXO: "Fragmente") enthält und in Bootstrap 5 geschrieben wird - möglichst ohne eigenes zusätzliches JS und CSS.

Die Funktionalitäten lassen sich anhand der Klassen grob unterteilen in:

* Verwaltung, Filterung von Artikeln, Varianten und dessen Informationen und Metadaten (Preise, Beschreibung, Verfügbarkeit, Kategorien): Die Klassen `Article`, `ArticleVariant`und `Category`
* Die Warenkorb-Funktionalitäten: `Cart`
* Die Bestellung und Bezahlung: `Checkout` und `Payment`

Diese spiegeln sich nicht nur in der Klassen-Struktur, sondern auch in der Ordner und Fragmente-Struktur wider.

Bei der Entwicklung von Funktionalitäten und beim Lösen von Bugs soll überprüft werden, ob nötige Informationen durch den Issue-Ersteller gegeben wurden, bspw. ein Stack Trace, ein Ablauf wann etwas zu einem Problem führt oder welcher Bereich oder Methode als Ausgangspunkt für eine Lösung gewählt werden soll. Wenn diese Informationen nicht gegeben wurden oder aus dem Kontext zu erschließen sind, brich ab und stelle Nachfragen, um den Bereich deiner Lösung einzugrenzen. Schildere, wie du vorgehen würdest.

Achte auf folgende Regeln:

1. Vermeide inline-CSS und inline-JS - wenn doch erforderlich, dann mit einem Nonce ausstatten.
2. Verwende Best Practices in der PHP-Entwicklung wie Typisierung / Type Hinting.
3. Achte auf Rückwärtskompatibilität, wenn es sich um eine zentrale Funktion oder Funktionsänderung handelt und nicht nur um einen Bugfix.
4. Vermeide überkomplexe Lösungen und bevorzuge kurze prägnante, der Aufgabenstellung angemessene Lösungen - arbeite nur komplexer, wenn du dazu aufgefordert wirst oder nach Rückfrage, wenn du nach 3-4 Minuten Laufzeit nicht zu einer guten Lösung gekommen bist.
5. In einem PR wird die Versionsnummer `version` gemäß Semantic Versioning in der `package.yml` erhöht - je nachdem, ob es ein Bugfix/Patch, Minor Update mit neuen Funktionen oder Major Update ohne Rückwärtskompatibilität handelt. Mache in deinem PR einen Vorschlag, was ausgehend vom main-Repository die nächste Versionsnummer wäre.

Es gibt keinen Build-Prozess, sodass du den Code selbst testen kannst. Es ist nicht über composer, yarn oder einen anderen Pakete-Manager verfügbar.
