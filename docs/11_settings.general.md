# Grundeinstellungen

In den Grundeinstellungen werden die wesentlichen Einstellungen für den Betrieb des AddOns vorgenommen.

| Einstellung                | Typ                         | Beschreibung                                                                                                   | Mögliche Optionen/Werte                                                                 |
|----------------------------|-----------------------------|----------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------|
| currency                   | Select-Feld                 | Währungsauswahl für Transaktionen. Die verfügbaren Werte stammen aus `PayPal::CURRENCY_CODES`.                 | Werte aus `PayPal::CURRENCY_CODES` (z.B. EUR, USD, …)                                  |
| tax_options                | Textfeld                    | Mögliche Steuersätze zur Auswahl in Artikeln und Varianten. Standard: <code>19,7,0</code> für 19%, 7% und 0% Steuersatz | Kommagetrennte Zahlenwerte; Standard: "19,7,0"                                         |
| shipping_allowed           | Mehrfach-Select-Feld        | Auswahl der erlaubten Lieferländer. Die Optionen werden aus `PayPal::COUNTRY_CODES` geladen.                   | Werte aus `PayPal::COUNTRY_CODES` (Ländercodes)                                         |
| cart_mode                  | Select-Feld                 | Es kann entweder die Warenkorbseite aufgerufen werden oder die vorherige Artikelseite. Wenn die Artikelseite aufgerufen wird, so wird showcart=1 als Get-Parameter angehängt. | "cart" (Warenkorbseite), "page" (Artikelseite)                                        |
| ycom_mode                  | Select-Feld                 | Benötigt das Add-on YCom. Hinweis: Wenn das Add-on YCom nachträglich installiert wurde, muss das Add-on Warehouse reinstalliert werden. | Werte aus `Warehouse::YCOM_MODES` oder "guest_only"                                    |
| enable_features            | Checkbox-Feld (Mehrfach)    | Zusätzliche Optionen für Artikel und Varianten: Staffelpreise abfragen, Artikelgewicht abfragen, Varianten zulassen | "bulk_prices", "weight", "variants"                                                 |
| editor                     | Textfeld                    | z.B. <code>class="form-control redactor-editor--default"</code>                                              | Freitext                                                                                |
| fallback_category_image    | MediaField                  | Wird verwendet, wenn kein Bild für die Kategorie vorhanden ist.                                                 | Medienpool-Auswahl                                                                      |
| fallback_article_image     | MediaField                  | Wird verwendet, wenn kein Bild für den Artikel oder die Variante vorhanden ist.                                | Medienpool-Auswahl                                                                      |
| framework                  | Textfeld                    | Derzeit wird nur Bootstrap 5 unterstützt. <code>bootstrap5</code> ist der Standard-Wert. Du kannst auch den Ordner <code>src/addons/warehouse/fragments/bootstrap5</code> nach <code>src/addons/project/fragments/bootstrap5</code> kopieren und anpassen. Weitere Informationen dazu in den Docs. | Freitext |

**Nicht (mehr) implementiert:**

- `currency_symbol` (wurde entfernt - verwenden Sie `Warehouse::formatCurrency()` für Währungsformatierung)
- `enable_weight` (wird über enable_features/weight abgedeckt)
