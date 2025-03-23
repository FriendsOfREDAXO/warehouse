# Die Klasse `Order`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_Order` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
use FriendsOfREDAXO\Warehouse\Order;
$entries = Order::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getSalutation()`

Gibt den Wert für das Feld `salutation` (Anrede) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getSalutation();
```

### `setSalutation(mixed $value)`

Setzt den Wert für das Feld `salutation` (Anrede).

```php
$dataset = Order::create();
$dataset->setSalutation($value);
$dataset->save();
```

### `getFirstname()`

Gibt den Wert für das Feld `firstname` (Vorname) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getFirstname();
```

### `setFirstname(mixed $value)`

Setzt den Wert für das Feld `firstname` (Vorname).

```php
$dataset = Order::create();
$dataset->setFirstname($value);
$dataset->save();
```

### `getLastname()`

Gibt den Wert für das Feld `lastname` (Nachname) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getLastname();
```

### `setLastname(mixed $value)`

Setzt den Wert für das Feld `lastname` (Nachname).

```php
$dataset = Order::create();
$dataset->setLastname($value);
$dataset->save();
```

### `getCompany()`

Gibt den Wert für das Feld `company` (Firma) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getCompany();
```

### `setCompany(mixed $value)`

Setzt den Wert für das Feld `company` (Firma).

```php
$dataset = Order::create();
$dataset->setCompany($value);
$dataset->save();
```

### `getAddress()`

Gibt den Wert für das Feld `address` (Adresse) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getAddress();
```

### `setAddress(mixed $value)`

Setzt den Wert für das Feld `address` (Adresse).

```php
$dataset = Order::create();
$dataset->setAddress($value);
$dataset->save();
```

### `getZip()`

Gibt den Wert für das Feld `zip` (PLZ) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getZip();
```

### `setZip(mixed $value)`

Setzt den Wert für das Feld `zip` (PLZ).

```php
$dataset = Order::create();
$dataset->setZip($value);
$dataset->save();
```

### `getCity()`

Gibt den Wert für das Feld `city` (Stadt) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getCity();
```

### `setCity(mixed $value)`

Setzt den Wert für das Feld `city` (Stadt).

```php
$dataset = Order::create();
$dataset->setCity($value);
$dataset->save();
```

### `getCountry()`

Gibt den Wert für das Feld `country` (Land) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getCountry();
```

### `setCountry(mixed $value)`

Setzt den Wert für das Feld `country` (Land).

```php
$dataset = Order::create();
$dataset->setCountry($value);
$dataset->save();
```

### `getEmail()`

Gibt den Wert für das Feld `email` (E-Mail) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getEmail();
```

### `setEmail(mixed $value)`

Setzt den Wert für das Feld `email` (E-Mail).

```php
$dataset = Order::create();
$dataset->setEmail($value);
$dataset->save();
```

### `getCreatedate()`

Gibt den Wert für das Feld `createdate` (Erstellungsdatum) zurück:

Beispiel:

```php
$dataset = Order::get($id);
$datestamp = $dataset->getCreatedate();
```

### `setCreatedate(string $value)`

Setzt den Wert für das Feld `createdate` (Erstellungsdatum).

```php
$dataset = Order::create();
$dataset->setCreatedate($value);
$dataset->save();
```

### `getPaypalId()`

Gibt den Wert für das Feld `paypal_id` (PayPal-ID) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getPaypalId();
```

### `setPaypalId(mixed $value)`

Setzt den Wert für das Feld `paypal_id` (PayPal-ID).

```php
$dataset = Order::create();
$dataset->setPaypalId($value);
$dataset->save();
```

### `getPaymentId()`

Gibt den Wert für das Feld `payment_id` (Zahlungs-ID) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getPaymentId();
```

### `setPaymentId(mixed $value)`

Setzt den Wert für das Feld `payment_id` (Zahlungs-ID).

```php
$dataset = Order::create();
$dataset->setPaymentId($value);
$dataset->save();
```

### `getPaypalConfirmToken()`

Gibt den Wert für das Feld `paypal_confirm_token` (PayPal-Bestätigungstoken) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getPaypalConfirmToken();
```

### `setPaypalConfirmToken(mixed $value)`

Setzt den Wert für das Feld `paypal_confirm_token` (PayPal-Bestätigungstoken).

```php
$dataset = Order::create();
$dataset->setPaypalConfirmToken($value);
$dataset->save();
```

### `getPaymentConfirm()`

Gibt den Wert für das Feld `payment_confirm` (Zahlungsbestätigung) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getPaymentConfirm();
```

### `setPaymentConfirm(mixed $value)`

Setzt den Wert für das Feld `payment_confirm` (Zahlungsbestätigung).

```php
$dataset = Order::create();
$dataset->setPaymentConfirm($value);
$dataset->save();
```

### `getOrderText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `order_text` (Bestelltext) zurück:

Beispiel:

```php
$dataset = Order::get($id);
$text = $dataset->getOrderText(true);
```

### `setOrderText(mixed $value)`

Setzt den Wert für das Feld `order_text` (Bestelltext).

```php
$dataset = Order::create();
$dataset->setOrderText($value);
$dataset->save();
```

### `getOrderJson(bool $asPlaintext = false)`

Gibt den Wert für das Feld `order_json` (Bestell-JSON) zurück:

Beispiel:

```php
$dataset = Order::get($id);
$text = $dataset->getOrderJson(true);
```

### `setOrderJson(mixed $value)`

Setzt den Wert für das Feld `order_json` (Bestell-JSON).

```php
$dataset = Order::create();
$dataset->setOrderJson($value);
$dataset->save();
```

### `getOrderTotal()`

Gibt den Wert für das Feld `order_total` (Bestellsumme) zurück:

Beispiel:

```php
$dataset = Order::get($id);
$nummer = $dataset->getOrderTotal();
```

### `setOrderTotal(float $value)`

Setzt den Wert für das Feld `order_total` (Bestellsumme).

```php
$dataset = Order::create();
$dataset->setOrderTotal($value);
$dataset->save();
```

### `getYcomUserid()`

Gibt den Wert für das Feld `ycom_userid` (YCom-Benutzer-ID) zurück:

Beispiel:

```php
$dataset = Order::get($id);
$beziehung = $dataset->getYcomUserid();
```

### `setYcomUserid(mixed $value)`

Setzt den Wert für das Feld `ycom_userid` (YCom-Benutzer-ID).

```php
$dataset = Order::create();
$dataset->setYcomUserid($value);
$dataset->save();
```

### `getPaymentType()`

Gibt den Wert für das Feld `payment_type` (Zahlungsart) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getPaymentType();
```

### `setPaymentType(mixed $value)`

Setzt den Wert für das Feld `payment_type` (Zahlungsart).

```php
$dataset = Order::create();
$dataset->setPaymentType($value);
$dataset->save();
```

### `getPayed()`

Gibt den Wert für das Feld `payed` (Bezahlt) zurück:

Beispiel:

```php
$dataset = Order::get($id);
echo $dataset->getPayed();
```

### `setPayed(mixed $value)`

Setzt den Wert für das Feld `payed` (Bezahlt).

```php
$dataset = Order::create();
$dataset->setPayed($value);
$dataset->save();
```

### `getImported(bool $asBool = false)`

Gibt den Wert für das Feld `imported` (Importiert) zurück: Wird extern verwaltet - bitte nicht über REDAXO anpassen!

Beispiel:

```php
$dataset = Order::get($id);
$wert = $dataset->getImported(true);
```

### `setImported(int $value = 1)`

Setzt den Wert für das Feld `imported` (Importiert).

```php
$dataset = Order::create();
$dataset->setImported(1);
$dataset->save();
```
