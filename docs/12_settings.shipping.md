# Versandkosten-Einstellungen

In diesem Bereich werden die grundlegenden Einstellungen für die Berechnung und Anzeige der Versandkosten im Shop vorgenommen.

| Einstellung                  | Typ              | Beschreibung                                                                                                   | Mögliche Optionen/Werte                                                                 |
|------------------------------|------------------|----------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------|
| minimum_order_value          | Textfeld         | Mindestbestellwert                                                                                             | Dezimalzahl, z.B. 0.00                                                                  |
| shipping_fee                 | Textfeld         | Versandkosten                                                                                                  | Dezimalzahl, z.B. 0.00                                                                  |
| free_shipping_from           | Textfeld         | Versandkostenfrei ab                                                                                           | Dezimalzahl, z.B. 0.00                                                                  |
| shipping_calculation_mode    | Select-Feld      | Versandkostenberechnung (Pauschal, nach Anzahl, nach Gewicht, nach Bestellsumme)                              | "default" (Pauschal), "quantity", "weight", "order_total"                            |
| shipping_conditions_text     | Textarea         | Text, der unter den Versandkosten angezeigt wird. Z.B. "Versandkostenfrei ab 50 Euro Bestellwert." oder "Versandkosten nach Gewicht gestaffelt." | Freitext, HTML erlaubt                                                                  |
