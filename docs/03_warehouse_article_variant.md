# Die Klasse `MeineKlasse`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `MeineTabelle` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
$entries = MeineKlasse::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getArticleId()`

Gibt den Wert für das Feld `article_id` (Haupt-Artikel) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
$beziehung = $dataset->getArticleId();
```

### `setArticleId(mixed $value)`

Setzt den Wert für das Feld `article_id` (Haupt-Artikel).

```php
$dataset = warehouse_article_variant::create();
$dataset->setArticleId($value);
$dataset->save();
```

### `getName()`

Gibt den Wert für das Feld `name` (Name) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
echo $dataset->getName();
```

### `setName(mixed $value)`

Setzt den Wert für das Feld `name` (Name).

```php
$dataset = warehouse_article_variant::create();
$dataset->setName($value);
$dataset->save();
```

### `getPrice()`

Gibt den Wert für das Feld `price` (Preis) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
$nummer = $dataset->getPrice();
```

### `setPrice(float $value)`

Setzt den Wert für das Feld `price` (Preis).

```php
$dataset = warehouse_article_variant::create();
$dataset->setPrice($value);
$dataset->save();
```

### `getBulkPrices()`

Gibt den Wert für das Feld `bulk_prices` (Staffelpreise) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
$tabelle = $dataset->getBulkPrices();
```

### `setBulkPrices(array|string $value)`

Setzt den Wert für das Feld `bulk_prices` (Staffelpreise).

```php
$dataset = warehouse_article_variant::create();
$dataset->setBulkPrices($value);
$dataset->save();
```

### `getWeight()`

Gibt den Wert für das Feld `weight` (Gewicht) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
$nummer = $dataset->getWeight();
```

### `setWeight(float $value)`

Setzt den Wert für das Feld `weight` (Gewicht).

```php
$dataset = warehouse_article_variant::create();
$dataset->setWeight($value);
$dataset->save();
```

### `getAvailability()`

Gibt den Wert für das Feld `availability` (Verfügbarkeit) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
$auswahl = $dataset->getAvailability();
```

### `setAvailability(mixed $param)`

Setzt den Wert für das Feld `availability` (Verfügbarkeit).

```php
$dataset = warehouse_article_variant::create();
$dataset->setAvailability($param);
$dataset->save();
```

### `getImage(bool $asMedia = false)`

Gibt den Wert für das Feld `image` (Bild) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
$media = $dataset->getImage(true);
```

### `setImage(string $filename)`

Setzt den Wert für das Feld `image` (Bild).

```php
$dataset = warehouse_article_variant::create();
$dataset->setImage($filename);
$dataset->save();
```

### `getStatus()`

Gibt den Wert für das Feld `status` (Status) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
$auswahl = $dataset->getStatus();
```

### `setStatus(mixed $param)`

Setzt den Wert für das Feld `status` (Status).

```php
$dataset = warehouse_article_variant::create();
$dataset->setStatus($param);
$dataset->save();
```

### `getUuid()`

Gibt den Wert für das Feld `uuid` (UUID) zurück:

Beispiel:

```php
$dataset = warehouse_article_variant::get($id);
echo $dataset->getUuid();
```

### `setUuid(mixed $value)`

Setzt den Wert für das Feld `uuid` (UUID).

```php
$dataset = warehouse_article_variant::create();
$dataset->setUuid($value);
$dataset->save();
```
