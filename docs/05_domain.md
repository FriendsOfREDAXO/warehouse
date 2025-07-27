# Die Klasse `Domain`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `MeineTabelle` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
$entries = Domain::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getYrewriteDomainId()`

Gibt den Wert für das Feld `yrewrite_domain_id` (Domain) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$domain = $dataset->getYrewriteDomainId();
```

### `setYrewriteDomainId(int $value)`

Setzt den Wert für das Feld `yrewrite_domain_id` (Domain).

```php
$dataset = Domain::create();
$dataset->setYrewriteDomainId($value);
$dataset->save();
```

### `getCartArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `cart_art_id` (Warenkorb) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$artikel = $dataset->getCartArtId(true);
```

### `setCartArtId(string $id)`

Setzt den Wert für das Feld `cart_art_id` (Warenkorb).

```php
$dataset = Domain::create();
$dataset->setCartArtId($id);
$dataset->save();
```

### `getCheckoutArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `checkout_art_id` (Kasse) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$artikel = $dataset->getCheckoutArtId(true);
```

### `setCheckoutArtId(string $id)`

Setzt den Wert für das Feld `checkout_art_id` (Kasse).

```php
$dataset = Domain::create();
$dataset->setCheckoutArtId($id);
$dataset->save();
```

### `getShippinginfoArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `shippinginfo_art_id` (Versandinfo) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$artikel = $dataset->getShippinginfoArtId(true);
```

### `setShippinginfoArtId(string $id)`

Setzt den Wert für das Feld `shippinginfo_art_id` (Versandinfo).

```php
$dataset = Domain::create();
$dataset->setShippinginfoArtId($id);
$dataset->save();
```

### `getAddressArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `address_art_id` (Adresse) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$artikel = $dataset->getAddressArtId(true);
```

### `setAddressArtId(string $id)`

Setzt den Wert für das Feld `address_art_id` (Adresse).

```php
$dataset = Domain::create();
$dataset->setAddressArtId($id);
$dataset->save();
```

### `getOrderArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `order_art_id` (Bestellung) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$artikel = $dataset->getOrderArtId(true);
```

### `setOrderArtId(string $id)`

Setzt den Wert für das Feld `order_art_id` (Bestellung).

```php
$dataset = Domain::create();
$dataset->setOrderArtId($id);
$dataset->save();
```

### `getPaymentErrorArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `payment_error_art_id` (Zahlungsfehler) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$artikel = $dataset->getPaymentErrorArtId(true);
```

### `setPaymentErrorArtId(string $id)`

Setzt den Wert für das Feld `payment_error_art_id` (Zahlungsfehler).

```php
$dataset = Domain::create();
$dataset->setPaymentErrorArtId($id);
$dataset->save();
```

### `getThankyouArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `thankyou_art_id` (Danke) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$artikel = $dataset->getThankyouArtId(true);
```

### `setThankyouArtId(string $id)`

Setzt den Wert für das Feld `thankyou_art_id` (Danke).

```php
$dataset = Domain::create();
$dataset->setThankyouArtId($id);
$dataset->save();
```

### `getEmailTemplateCustomer()`

Gibt den Wert für das Feld `email_template_customer` (E-Mail-Template an Kunden) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
echo $dataset->getEmailTemplateCustomer();
```

### `setEmailTemplateCustomer(mixed $value)`

Setzt den Wert für das Feld `email_template_customer` (E-Mail-Template an Kunden).

```php
$dataset = Domain::create();
$dataset->setEmailTemplateCustomer($value);
$dataset->save();
```

### `getEmailTemplateSeller()`

Gibt den Wert für das Feld `email_template_seller` (E-Mail-Template an Verkäufer) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
echo $dataset->getEmailTemplateSeller();
```

### `setEmailTemplateSeller(mixed $value)`

Setzt den Wert für das Feld `email_template_seller` (E-Mail-Template an Verkäufer).

```php
$dataset = Domain::create();
$dataset->setEmailTemplateSeller($value);
$dataset->save();
```

### `getOrderEmail()`

Gibt den Wert für das Feld `order_email` (Empfänger E-Mail für Bestellungen) zurück: Mehrere Empfänger durch Komma trennen

Beispiel:

```php
$dataset = Domain::get($id);
echo $dataset->getOrderEmail();
```

### `setOrderEmail(mixed $value)`

Setzt den Wert für das Feld `order_email` (Empfänger E-Mail für Bestellungen).

```php
$dataset = Domain::create();
$dataset->setOrderEmail($value);
$dataset->save();
```

### `getEmailSignature(bool $asPlaintext = false)`

Gibt den Wert für das Feld `email_signature` (E-Mail-Signatur) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
$text = $dataset->getEmailSignature(true);
```

### `setEmailSignature(mixed $value)`

Setzt den Wert für das Feld `email_signature` (E-Mail-Signatur).

```php
$dataset = Domain::create();
$dataset->setEmailSignature($value);
$dataset->save();
```

### `getSepaBankName()`

Gibt den Wert für das Feld `sepa_bank_name` (Bankname) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
echo $dataset->getSepaBankName();
```

### `setSepaBankName(mixed $value)`

Setzt den Wert für das Feld `sepa_bank_name` (Bankname).

```php
$dataset = Domain::create();
$dataset->setSepaBankName($value);
$dataset->save();
```

### `getSepaBic()`

Gibt den Wert für das Feld `sepa_bic` (BIC) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
echo $dataset->getSepaBic();
```

### `setSepaBic(mixed $value)`

Setzt den Wert für das Feld `sepa_bic` (BIC).

```php
$dataset = Domain::create();
$dataset->setSepaBic($value);
$dataset->save();
```

### `getSepaIban()`

Gibt den Wert für das Feld `sepa_iban` (IBAN) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
echo $dataset->getSepaIban();
```

### `setSepaIban(mixed $value)`

Setzt den Wert für das Feld `sepa_iban` (IBAN).

```php
$dataset = Domain::create();
$dataset->setSepaIban($value);
$dataset->save();
```

### `getSepaAccountHolderName()`

Gibt den Wert für das Feld `sepa_account_holder_name` (Kontoinhaber) zurück:

Beispiel:

```php
$dataset = Domain::get($id);
echo $dataset->getSepaAccountHolderName();
```

### `setSepaAccountHolderName(mixed $value)`

Setzt den Wert für das Feld `sepa_account_holder_name` (Kontoinhaber).

```php
$dataset = Domain::create();
$dataset->setSepaAccountHolderName($value);
$dataset->save();
```
