# Die Klasse `Category`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_Category` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
use FriendsOfREDAXO\Warehouse\Category;
$entries = Category::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getParentId()`

Gibt den Wert für das Feld `parent_id` ([translate:Category.parent_id]) zurück:

Beispiel:

```php
$dataset = Category::get($id);
$beziehung = $dataset->getParentId();
```

### `setParentId(mixed $value)`

Setzt den Wert für das Feld `parent_id` ([translate:Category.parent_id]).

```php
$dataset = Category::create();
$dataset->setParentId($value);
$dataset->save();
```

### `getName()`

Gibt den Wert für das Feld `name` ([translate:Category.name]) zurück:

Beispiel:

```php
$dataset = Category::get($id);
echo $dataset->getName();
```

### `setName(mixed $value)`

Setzt den Wert für das Feld `name` ([translate:Category.name]).

```php
$dataset = Category::create();
$dataset->setName($value);
$dataset->save();
```

### `getTeaser()`

Gibt den Wert für das Feld `teaser` ([translate:Category.teaser]) zurück:

Beispiel:

```php
$dataset = Category::get($id);
echo $dataset->getTeaser();
```

### `setTeaser(mixed $value)`

Setzt den Wert für das Feld `teaser` ([translate:Category.teaser]).

```php
$dataset = Category::create();
$dataset->setTeaser($value);
$dataset->save();
```

### `getImage(bool $asMedia = false)`

Gibt den Wert für das Feld `image` ([translate:Category.image]) zurück:

Beispiel:

```php
$dataset = Category::get($id);
$media = $dataset->getImage(true);
```

### `setImage(string $filename)`

Setzt den Wert für das Feld `image` ([translate:Category.image]).

```php
$dataset = Category::create();
$dataset->setImage($filename);
$dataset->save();
```

### `getText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `text` ([translate:Category.text]) zurück:

Beispiel:

```php
$dataset = Category::get($id);
$text = $dataset->getText(true);
```

### `setText(mixed $value)`

Setzt den Wert für das Feld `text` ([translate:Category.text]).

```php
$dataset = Category::create();
$dataset->setText($value);
$dataset->save();
```

### `getStatus()`

Gibt den Wert für das Feld `status` ([translate:Category.status]) zurück:

Beispiel:

```php
$dataset = Category::get($id);
$auswahl = $dataset->getStatus();
```

### `setStatus(mixed $param)`

Setzt den Wert für das Feld `status` ([translate:Category.status]).

```php
$dataset = Category::create();
$dataset->setStatus($param);
$dataset->save();
```

### `getUuid()`

Gibt den Wert für das Feld `uuid` ([translate:Category.uuid]) zurück:

Beispiel:

```php
$dataset = Category::get($id);
echo $dataset->getUuid();
```

### `setUuid(mixed $value)`

Setzt den Wert für das Feld `uuid` ([translate:Category.uuid]).

```php
$dataset = Category::create();
$dataset->setUuid($value);
$dataset->save();
```

### `getUpdatedate()`

Gibt den Wert für das Feld `updatedate` ([translate:Category.updatedate]) zurück:

Beispiel:

```php
$dataset = Category::get($id);
$datestamp = $dataset->getUpdatedate();
```

### `setUpdatedate(string $value)`

Setzt den Wert für das Feld `updatedate` ([translate:Category.updatedate]).

```php
$dataset = Category::create();
$dataset->setUpdatedate($value);
$dataset->save();
```
