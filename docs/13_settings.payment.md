# Zahlung

In diesem Bereich werden die Einstellungen für Zahlungsanbieter (z.B. PayPal) und Shop-Informationen vorgenommen.

| Einstellung                   | Typ              | Beschreibung                                                                                                   | Mögliche Optionen/Format                                                  |
|-------------------------------|------------------|----------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------|
| store_name                    | Textfeld         | Name des Shops, der bei Zahlungen über PayPal verwendet wird. z.B. <code>Martin Muster GmbH</code>, wird u.a. bei Zahlung an PayPal übermittelt. | Freitext                                                                  |
| store_country_code            | Select-Feld      | Ländercode des Shops, der an PayPal übermittelt wird. z.B. <code>de-DE</code> - wird u.a. bei Zahlung an PayPal übermittelt. Optionen aus `PayPal::COUNTRY_CODES`. | Werte aus `PayPal::COUNTRY_CODES`                                         |
| paypal_client_id              | Textfeld         | Paypal Client Id                                                                                               | Freitext                                                                  |
| paypal_secret                 | Textfeld         | Paypal Secret                                                                                                  | Freitext                                                                  |
| sandboxmode                   | Checkbox-Feld    | Paypal Sandbox aktivieren (Testbestellungen)                                                                   | Aktivierung: Option "1"                                                  |
| paypal_sandbox_client_id      | Textfeld         | Paypal Sandbox Client Id                                                                                       | Freitext                                                                  |
| paypal_sandbox_secret         | Textfeld         | Paypal Sandbox Secret                                                                                          | Freitext                                                                  |
| paypal_getparams              | Textfeld         | Paypal Zusätzliche Get-Parameter für Paypal. z.B. um Maintenance bei der Entwicklung zu verwenden. Als JSON in der Form <code>{"key1":"value1","key2":"value2"}</code> angeben. | JSON-Format                                                               |

**Nicht (mehr) implementiert:**

- `paypal_page_start`, `paypal_page_success`, `paypal_page_error` (nicht im Formular vorhanden)

**Hinweise:**

- Die tatsächlichen Feldbezeichnungen und Hinweise werden über die Sprachdatei (`de_de.lang`) bereitgestellt.
- Die Reihenfolge und Feldtypen entsprechen der aktuellen Implementierung in `settings.payment.php`.
- Derzeit wird nur PayPal als Zahlungsanbieter unterstützt. Beteilige dich an der Weiterentwicklung auf GitHub unter <a href="https://github.com/friendsofredaxo/warehouse/">https://github.com/friendsofredaxo/warehouse/</a>.
