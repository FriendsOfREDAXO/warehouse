# Setup

Im Bereich Setup können zu Beginn zusätzliche REDAXO-Konfigurationen vorgenommen werden.

## Verfügbare Setup-Funktionen

### 1. Tabellen reparieren
Repariert die Datenbankstruktur und importiert die YForm-Tablesets neu. Diese Funktion führt folgende Schritte aus:
- Ausführung von `update_scheme.php` zur Aktualisierung der Datenbankstruktur
- Import aller YForm-Tablesets (Domain-Einstellungen, Artikel, Varianten, Kategorien, Bestellungen, Adressen)
- Cache-Bereinigung für YForm-Tabellen

⚠️ **Achtung:** Diese Funktion kann nicht rückgängig gemacht werden.

### 2. Struktur anlegen  
Erstellt die Shop-Struktur mit Kategorien und Domain-Profil. Diese Funktion:
- Erstellt die grundlegende Shop-Struktur (Shop, Produkte, Bestellungen, Warenkorb-Seiten)
- Legt das Domain-Profil mit Standard-Einstellungen an
- Funktioniert nur, wenn noch kein Domain-Profil existiert

### 3. URL-Profile anlegen
Installiert die URL-Profile für SEO-optimierte URLs:
- URL-Profil für Artikel
- URL-Profil für Kategorien  
- URL-Profil für Bestellungen

**Voraussetzung:** Das URL-Addon muss installiert und aktiviert sein.

### 4. Media Manager Profile anlegen
Während der Installation werden automatisch drei Media Manager Profile erstellt:
- `warehouse_category`: 300x300px für Kategorie-Bilder
- `warehouse_article`: 800x600px für Artikel-Bilder  
- `warehouse_article_preview`: 400x300px für Artikel-Vorschaubilder

Alle Profile verwenden den `resize`-Effekt mit maximalem Resizing ohne Vergrößerung.

**Voraussetzung:** Das Media Manager Addon muss installiert und aktiviert sein.

### 5. Demo-Daten importieren
Importiert Demo-Artikel, Kategorien und Bestellungen für Testzwecke:
- Fügt Demo-Bilder in den Medienpool ein
- Erstellt Beispiel-Artikel und Kategorien
- Generiert Test-Bestellungen

**Voraussetzung:** Das Tracks-Addon muss für die Medien-Verwaltung verfügbar sein.

⚠️ **Achtung:** Bestehende Daten können überschrieben werden.

### 6. Shop zurücksetzen
Löscht alle Demo-Daten und setzt den Shop auf den Ausgangszustand zurück:
- Leert alle Warehouse-Tabellen (Artikel, Varianten, Kategorien, Bestellungen, Adressen)
- Entfernt das Domain-Profil

⚠️ **ACHTUNG:** Alle Artikel, Kategorien und Bestellungen werden unwiderruflich gelöscht!

## Weitere Setup-Optionen

### 7. Warehouse-Modul installieren
Installation oder Aktualisierung des Warehouse-Moduls für die Frontend-Ausgabe.

### 8. E-Mail-Templates installieren
Installation der E-Mail-Templates für Kunden- und Verkäufer-Benachrichtigungen.

## Sicherheitshinweise

- Alle Aktionen sind mit CSRF-Schutz versehen
- Destruktive Aktionen erfordern eine Bestätigung
- Bei Fehlern werden detaillierte Fehlermeldungen angezeigt
- Erfolgreiche Aktionen werden mit grünen Erfolgsmeldungen bestätigt

## Empfohlene Reihenfolge

Für eine Neuinstallation wird folgende Reihenfolge empfohlen:

1. **Tabellen reparieren** - Sicherstellen, dass die Datenbankstruktur aktuell ist
2. **Struktur anlegen** - Grundlegende Shop-Struktur erstellen
3. **URL-Profile anlegen** - SEO-optimierte URLs einrichten
4. **Warehouse-Modul installieren** - Frontend-Modul bereitstellen
5. **E-Mail-Templates installieren** - E-Mail-Kommunikation einrichten
6. **Demo-Daten importieren** - Zum Testen (optional)
