# Die Klasse `Customer`

Kind-Klasse von `rex_yform_manager_dataset` bzw `rex_ycom_user`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_ycom_user` zu.

> Es werden nachfolgend die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
$entries = MeineKlasse::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getEmail()`

Gibt den Wert für das Feld `email` (E-Mail) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getEmail();
```

### `setEmail(mixed $value)`

Setzt den Wert für das Feld `email` (E-Mail).

```php
$dataset = Customer::create();
$dataset->setEmail($value);
$dataset->save();
```

### `getFirstname()`

Gibt den Wert für das Feld `firstname` (Vorname) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getFirstname();
```

### `setFirstname(mixed $value)`

Setzt den Wert für das Feld `firstname` (Vorname).

```php
$dataset = Customer::create();
$dataset->setFirstname($value);
$dataset->save();
```

### `getLastname()`

Gibt den Wert für das Feld `lastname` ([translate:warehouse.ycom_user.lastname]) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getLastname();
```

### `setLastname(mixed $value)`

Setzt den Wert für das Feld `lastname` ([translate:warehouse.ycom_user.lastname]).

```php
$dataset = Customer::create();
$dataset->setLastname($value);
$dataset->save();
```

### `getSalutation()`

Gibt den Wert für das Feld `salutation` ([translate:warehouse.ycom_user.salutation]) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getSalutation();
```

### `setSalutation(mixed $value)`

Setzt den Wert für das Feld `salutation` ([translate:warehouse.ycom_user.salutation]).

```php
$dataset = Customer::create();
$dataset->setSalutation($value);
$dataset->save();
```

### `getFullName()`

Gibt den vollständigen Namen (inkl. Anrede, Vorname, Nachname) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getFullName();
```

### `getCompany()`

Gibt den Wert für das Feld `company` ([translate:warehouse.ycom_user.company]) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getCompany();
```

### `setCompany(mixed $value)`

Setzt den Wert für das Feld `company` ([translate:warehouse.ycom_user.company]).

```php
$dataset = Customer::create();
$dataset->setCompany($value);
$dataset->save();
```

### `getDepartment()`

Gibt den Wert für das Feld `department` ([translate:warehouse.ycom_user.department]) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getDepartment();
```

### `setDepartment(mixed $value)`

Setzt den Wert für das Feld `department` ([translate:warehouse.ycom_user.department]).

```php
$dataset = Customer::create();
$dataset->setDepartment($value);
$dataset->save();
```

### `getAddress()`

Gibt den Wert für das Feld `address` ([translate:warehouse.ycom_user.address]) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getAddress();
```

### `setAddress(mixed $value)`

Setzt den Wert für das Feld `address` ([translate:warehouse.ycom_user.address]).

```php
$dataset = Customer::create();
$dataset->setAddress($value);
$dataset->save();
```

### `getPhone()`

Gibt den Wert für das Feld `phone` ([translate:warehouse.ycom_user.phone]) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getPhone();
```

### `setPhone(mixed $value)`

Setzt den Wert für das Feld `phone` ([translate:warehouse.ycom_user.phone]).

```php
$dataset = Customer::create();
$dataset->setPhone($value);
$dataset->save();
```

### `getZip()`

Gibt den Wert für das Feld `zip` ([translate:warehouse.ycom_user.zip]) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getZip();
```

### `setZip(mixed $value)`

Setzt den Wert für das Feld `zip` ([translate:warehouse.ycom_user.zip]).

```php
$dataset = Customer::create();
$dataset->setZip($value);
$dataset->save();
```

### `getCity()`

Gibt den Wert für das Feld `city` ([translate:warehouse.ycom_user.city]) zurück:

Beispiel:

```php
$dataset = Customer::get($id);
echo $dataset->getCity();
```

### `setCity(mixed $value)`

Setzt den Wert für das Feld `city` ([translate:warehouse.ycom_user.city]).

```php
$dataset = Customer::create();
$dataset->setCity($value);
$dataset->save();
```

### `getShippingAddress()`

Gibt die Versandadresse des Kunden zurück (Objekt vom Typ `CustomerAddress` oder `null`).

Beispiel:

```php
$dataset = Customer::get($id);
$shippingAddress = $dataset->getShippingAddress();
```
