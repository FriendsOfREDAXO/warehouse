# Die Klasse `MeineKlasse`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `MeineTabelle` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
$entries = MeineKlasse::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getEmailTemplateCustomer()`

Gibt den Wert für das Feld `email_template_customer` (E-Mail-Template an Kunden) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
echo $dataset->getEmailTemplateCustomer();
```

### `setEmailTemplateCustomer(mixed $value)`

Setzt den Wert für das Feld `email_template_customer` (E-Mail-Template an Kunden).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setEmailTemplateCustomer($value);
$dataset->save();
```

### `getYrewriteDomainId()`

Gibt den Wert für das Feld `yrewrite_domain_id` (Domain) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
$domain = $dataset->getYrewriteDomainId();
```

### `setYrewriteDomainId(int $value)`

Setzt den Wert für das Feld `yrewrite_domain_id` (Domain).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setYrewriteDomainId($value);
$dataset->save();
```

### `getCartArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `cart_art_id` (Warenkorb) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
$artikel = $dataset->getCartArtId(true);
```

### `setCartArtId(string $id)`

Setzt den Wert für das Feld `cart_art_id` (Warenkorb).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setCartArtId($id);
$dataset->save();
```

### `getShippinginfoArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `shippinginfo_art_id` (Versandinfo) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
$artikel = $dataset->getShippinginfoArtId(true);
```

### `setShippinginfoArtId(string $id)`

Setzt den Wert für das Feld `shippinginfo_art_id` (Versandinfo).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setShippinginfoArtId($id);
$dataset->save();
```

### `getAddressArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `address_art_id` (Adresse) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
$artikel = $dataset->getAddressArtId(true);
```

### `setAddressArtId(string $id)`

Setzt den Wert für das Feld `address_art_id` (Adresse).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setAddressArtId($id);
$dataset->save();
```

### `getOrderArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `order_art_id` (Bestellung) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
$artikel = $dataset->getOrderArtId(true);
```

### `setOrderArtId(string $id)`

Setzt den Wert für das Feld `order_art_id` (Bestellung).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setOrderArtId($id);
$dataset->save();
```

### `getPaymentErrorArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `payment_error_art_id` (Zahlungsfehler) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
$artikel = $dataset->getPaymentErrorArtId(true);
```

### `setPaymentErrorArtId(string $id)`

Setzt den Wert für das Feld `payment_error_art_id` (Zahlungsfehler).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setPaymentErrorArtId($id);
$dataset->save();
```

### `getThankyouArtId(bool $asArticle = false)`

Gibt den Wert für das Feld `thankyou_art_id` (Danke) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
$artikel = $dataset->getThankyouArtId(true);
```

### `setThankyouArtId(string $id)`

Setzt den Wert für das Feld `thankyou_art_id` (Danke).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setThankyouArtId($id);
$dataset->save();
```

### `getEmailTemplateSeller()`

Gibt den Wert für das Feld `email_template_seller` (E-Mail-Template an Verkäufer) zurück:

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
echo $dataset->getEmailTemplateSeller();
```

### `setEmailTemplateSeller(mixed $value)`

Setzt den Wert für das Feld `email_template_seller` (E-Mail-Template an Verkäufer).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setEmailTemplateSeller($value);
$dataset->save();
```

### `getOrderEmail()`

Gibt den Wert für das Feld `order_email` (Empfänger E-Mail für Bestellungen) zurück: Mehrere Empfänger durch Komma trennen

Beispiel:

```php
$dataset = warehouse_settings_domain::get($id);
echo $dataset->getOrderEmail();
```

### `setOrderEmail(mixed $value)`

Setzt den Wert für das Feld `order_email` (Empfänger E-Mail für Bestellungen).

```php
$dataset = warehouse_settings_domain::create();
$dataset->setOrderEmail($value);
$dataset->save();
```
