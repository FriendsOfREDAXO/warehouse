# Grundeinstellungen

In den Grundeinstellungen werden die wesentlichen Einstellungen für den Betrieb des AddOns vorgenommen.

| Einstellung       | Typ                         | Beschreibung                                                                                                                                          | Mögliche Optionen/Werte                                          |
| ----------------- | --------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------- |
| currency          | Select-Feld                 | Währungsauswahl für Transaktionen. Die verfügbaren Werte stammen aus `PayPal::CURRENCY_CODES`.                                                         | Werte aus `PayPal::CURRENCY_CODES` (z.B. EUR, USD, …)            |
| currency_symbol   | Textfeld                    | Festlegung des Währungssymbols (z. B. "€"). Der Wert wird per `rex_config::get("warehouse", "currency_symbol")` abgerufen.                               | Freitext (z. B. "€")                                               |
| tax_options       | Textfeld (mit Pattern)      | Definiert die zur Auswahl stehenden Steuersätze für Artikel/Varianten. Hinweis: Standardwert ist "19,7,0" für 19%, 7% und 0%.                          | Kommagetrennte Zahlenwerte; Standard: "19,7,0"                    |
| shipping_allowed  | Mehrfach-Select-Feld        | Auswahl der erlaubten Lieferländer. Die Optionen werden aus `PayPal::COUNTRY_CODES` geladen.                                                         | Werte aus `PayPal::COUNTRY_CODES` (Ländercodes)                    |
| cart_mode         | Select-Feld (disabled)      | Legt fest, ob nach einer Warenkorb-Aktion zur Warenkorbseite gewechselt oder auf der Artikelseite geblieben wird.                                      | "cart" (in den Warenkorb wechseln), "page" (auf der Artikelseite bleiben) |
| enable_weight     | Checkbox-Feld (disabled)    | Aktiviert oder deaktiviert die Verwendung des Artikelgewichts.                                                                                       | Bei Aktivierung: "1" (Artikelgewicht verwenden)                   |
| editor            | Textfeld (Input Field)      | Bestimmt den Editor, der zur Bearbeitung bzw. Anzeige von Inhalten genutzt wird.                                                                     | Freitext; Hinweise siehe `rex_i18n::msg('warehouse.settings.editor.notice')` |
