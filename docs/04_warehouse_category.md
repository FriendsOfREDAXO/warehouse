# Die Klasse `MeineKlasse`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `MeineTabelle` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
$entries = MeineKlasse::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getParentId()`

Gibt den Wert für das Feld `parent_id` (Übergeordente Kategorie) zurück:

Beispiel:

```php
$dataset = warehouse_category::get($id);
$beziehung = $dataset->getParentId();
```

### `setParentId(mixed $value)`

Setzt den Wert für das Feld `parent_id` (Übergeordente Kategorie).

```php
$dataset = warehouse_category::create();
$dataset->setParentId($value);
$dataset->save();
```

### `getName()`

Gibt den Wert für das Feld `name` (Name) zurück:

Beispiel:

```php
$dataset = warehouse_category::get($id);
echo $dataset->getName();
```

### `setName(mixed $value)`

Setzt den Wert für das Feld `name` (Name).

```php
$dataset = warehouse_category::create();
$dataset->setName($value);
$dataset->save();
```

### `getTeaser()`

Gibt den Wert für das Feld `teaser` (Teaser) zurück:

Beispiel:

```php
$dataset = warehouse_category::get($id);
echo $dataset->getTeaser();
```

### `setTeaser(mixed $value)`

Setzt den Wert für das Feld `teaser` (Teaser).

```php
$dataset = warehouse_category::create();
$dataset->setTeaser($value);
$dataset->save();
```

### `getImage(bool $asMedia = false)`

Gibt den Wert für das Feld `image` (Bild) zurück:

Beispiel:

```php
$dataset = warehouse_category::get($id);
$media = $dataset->getImage(true);
```

### `setImage(string $filename)`

Setzt den Wert für das Feld `image` (Bild).

```php
$dataset = warehouse_category::create();
$dataset->setImage($filename);
$dataset->save();
```

### `getText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `text` (Text) zurück:

Beispiel:

```php
$dataset = warehouse_category::get($id);
$text = $dataset->getText(true);
```

### `setText(mixed $value)`

Setzt den Wert für das Feld `text` (Text).

```php
$dataset = warehouse_category::create();
$dataset->setText($value);
$dataset->save();
```

### `getStatus()`

Gibt den Wert für das Feld `status` (Status) zurück:

Beispiel:

```php
$dataset = warehouse_category::get($id);
$auswahl = $dataset->getStatus();
```

### `setStatus(mixed $param)`

Setzt den Wert für das Feld `status` (Status).

```php
$dataset = warehouse_category::create();
$dataset->setStatus($param);
$dataset->save();
```

### `getUuid()`

Gibt den Wert für das Feld `uuid` (UUID) zurück:

Beispiel:

```php
$dataset = warehouse_category::get($id);
echo $dataset->getUuid();
```

### `setUuid(mixed $value)`

Setzt den Wert für das Feld `uuid` (UUID).

```php
$dataset = warehouse_category::create();
$dataset->setUuid($value);
$dataset->save();
```

### `getUpdatedate()`

Gibt den Wert für das Feld `updatedate` (Zuletzt geändert) zurück:

Beispiel:

```php
$dataset = warehouse_category::get($id);
$datestamp = $dataset->getUpdatedate();
```

### `setUpdatedate(string $value)`

Setzt den Wert für das Feld `updatedate` (Zuletzt geändert).

```php
$dataset = warehouse_category::create();
$dataset->setUpdatedate($value);
$dataset->save();
```
