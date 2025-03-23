# Die Klasse `Article`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_warehouse_article` zu.

> Es werden nachfolgend zur die durch dieses Addon ergänzte Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
use FriendsOfREDAXO\Warehouse\Article;
$articles = Article::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getName()`

Gibt den Wert für das Feld `name` (Name) zurück:

Beispiel:

```php
$dataset = Article::get($id);
echo $dataset->getName();
```

### `setName(mixed $value)`

Setzt den Wert für das Feld `name` (Name).

```php
$dataset = Article::create();
$dataset->setName($value);
$dataset->save();
```

### `getAvailability()`

Gibt den Wert für das Feld `availability` (Verfügbarkeit) zurück:

Beispiel:

```php
$dataset = Article::get($id);
echo $dataset->getAvailability();
```

### `setAvailability(mixed $value)`

Setzt den Wert für das Feld `availability` (Verfügbarkeit).

```php
$dataset = Article::create();
$dataset->setAvailability($value);
$dataset->save();
```

### `getCategoryId()`

Gibt den Wert für das Feld `category_id` (Kategorie) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$beziehung = $dataset->getCategoryId();
```

### `setCategoryId(mixed $value)`

Setzt den Wert für das Feld `category_id` (Kategorie).

```php
$dataset = Article::create();
$dataset->setCategoryId($value);
$dataset->save();
```

### `getStatus()`

Gibt den Wert für das Feld `status` (Status) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$auswahl = $dataset->getStatus();
```

### `setStatus(mixed $param)`

Setzt den Wert für das Feld `status` (Status).

```php
$dataset = Article::create();
$dataset->setStatus($param);
$dataset->save();
```

### `getPrice()`

Gibt den Wert für das Feld `price` (Preis) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$nummer = $dataset->getPrice();
```

### `setPrice(float $value)`

Setzt den Wert für das Feld `price` (Preis).

```php
$dataset = Article::create();
$dataset->setPrice($value);
$dataset->save();
```

### `getTax()`

Gibt den Wert für das Feld `tax` (Steuer) zurück:

Beispiel:

```php
$dataset = Article::get($id);
echo $dataset->getTax();
```

### `setTax(mixed $value)`

Setzt den Wert für das Feld `tax` (Steuer).

```php
$dataset = Article::create();
$dataset->setTax($value);
$dataset->save();
```

### `getPriceText()`

Gibt den Wert für das Feld `price_text` (Preis-Text) zurück:

Beispiel:

```php
$dataset = Article::get($id);
echo $dataset->getPriceText();
```

### `setPriceText(mixed $value)`

Setzt den Wert für das Feld `price_text` (Preis-Text).

```php
$dataset = Article::create();
$dataset->setPriceText($value);
$dataset->save();
```

### `getWeight()`

Gibt den Wert für das Feld `weight` (Gewicht) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$nummer = $dataset->getWeight();
```

### `setWeight(float $value)`

Setzt den Wert für das Feld `weight` (Gewicht).

```php
$dataset = Article::create();
$dataset->setWeight($value);
$dataset->save();
```

### `getShortText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `short_text` (Kurztext) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$text = $dataset->getShortText(true);
```

### `setShortText(mixed $value)`

Setzt den Wert für das Feld `short_text` (Kurztext).

```php
$dataset = Article::create();
$dataset->setShortText($value);
$dataset->save();
```

### `getText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `text` (Text) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$text = $dataset->getText(true);
```

### `setText(mixed $value)`

Setzt den Wert für das Feld `text` (Text).

```php
$dataset = Article::create();
$dataset->setText($value);
$dataset->save();
```

### `getImage(bool $asMedia = false)`

Gibt den Wert für das Feld `image` (Bild) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$media = $dataset->getImage(true);
```

### `setImage(string $filename)`

Setzt den Wert für das Feld `image` (Bild).

```php
$dataset = Article::create();
$dataset->setImage($filename);
$dataset->save();
```

### `getGallery(bool $asMedia = false)`

Gibt den Wert für das Feld `gallery` (Galerie) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$media = $dataset->getGallery(true);
```

### `setGallery(string $filename)`

Setzt den Wert für das Feld `gallery` (Galerie).

```php
$dataset = Article::create();
$dataset->setGallery($filename);
$dataset->save();
```

### `getVariantIds()`

Gibt den Wert für das Feld `variant_ids` (Varianten) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$beziehung = $dataset->getVariantIds();
```

### `setVariantIds(mixed $value)`

Setzt den Wert für das Feld `variant_ids` (Varianten).

```php
$dataset = Article::create();
$dataset->setVariantIds($value);
$dataset->save();
```

### `getUuid()`

Gibt den Wert für das Feld `uuid` (UUID) zurück:

Beispiel:

```php
$dataset = Article::get($id);
echo $dataset->getUuid();
```

### `setUuid(mixed $value)`

Setzt den Wert für das Feld `uuid` (UUID).

```php
$dataset = Article::create();
$dataset->setUuid($value);
$dataset->save();
```

### `getUpdatedate()`

Gibt den Wert für das Feld `updatedate` (Zuletzt geändert) zurück:

Beispiel:

```php
$dataset = Article::get($id);
$datestamp = $dataset->getUpdatedate();
```

### `setUpdatedate(string $value)`

Setzt den Wert für das Feld `updatedate` (Zuletzt geändert).

```php
$dataset = Article::create();
$dataset->setUpdatedate($value);
$dataset->save();
```
