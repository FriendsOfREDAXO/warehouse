# Die Klasse `Article`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_warehouse_article` zu.

> Es werden nachfolgend nur die durch dieses Addon ergänzten Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
use FriendsOfRedaxo\Warehouse\Article;
$articles = Article::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getName()`

Gibt den Wert für das Feld `name` (Name) zurück:

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

### `getCategory()`

Gibt das zugehörige Kategorie-Datensatz-Objekt zurück:

```php
$dataset = Article::get($id);
$category = $dataset->getCategory();
```

### `getStatus()`

Gibt den Wert für das Feld `status` (Status) zurück:

```php
$dataset = Article::get($id);
echo $dataset->getStatus();
```

### `getStatusLabel()`

Gibt das übersetzte Label für den Status zurück:

```php
$dataset = Article::get($id);
echo $dataset->getStatusLabel();
```

### `setStatus(mixed $param)`

Setzt den Wert für das Feld `status` (Status).

```php
$dataset = Article::create();
$dataset->setStatus('active');
$dataset->save();
```

### `getWeight()`

Gibt den Wert für das Feld `weight` (Gewicht) zurück:

```php
$dataset = Article::get($id);
echo $dataset->getWeight();
```

### `setWeight(float $value)`

Setzt den Wert für das Feld `weight` (Gewicht).

```php
$dataset = Article::create();
$dataset->setWeight(1.5);
$dataset->save();
```

### `getImage()`

Gibt den Dateinamen des Bildes zurück (ggf. Fallback):

```php
$dataset = Article::get($id);
echo $dataset->getImage();
```

### `getImageAsMedia()`

Gibt das Bild als rex_media-Objekt zurück:

```php
$dataset = Article::get($id);
$media = $dataset->getImageAsMedia();
```

### `setImage(string $filename)`

Setzt den Wert für das Feld `image` (Bild).

```php
$dataset = Article::create();
$dataset->setImage('bild.jpg');
$dataset->save();
```

### `getGallery()`

Gibt den Wert für das Feld `gallery` (Galerie) zurück:

```php
$dataset = Article::get($id);
echo $dataset->getGallery();
```

### `getGalleryAsMedia()`

Gibt die Galerie als Array von rex_media-Objekten zurück:

```php
$dataset = Article::get($id);
$medien = $dataset->getGalleryAsMedia();
```

### `setGallery(string $filename)`

Setzt den Wert für das Feld `gallery` (Galerie).

```php
$dataset = Article::create();
$dataset->setGallery('bild1.jpg,bild2.jpg');
$dataset->save();
```

### `getShortText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `short_text` (Kurztext) zurück. Optional als Plaintext:

```php
$dataset = Article::get($id);
echo $dataset->getShortText(true);
```

### `setShortText(mixed $value)`

Setzt den Wert für das Feld `short_text` (Kurztext).

```php
$dataset = Article::create();
$dataset->setShortText('Kurzbeschreibung');
$dataset->save();
```

### `getText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `text` (Text) zurück. Optional als Plaintext:

```php
$dataset = Article::get($id);
echo $dataset->getText(true);
```

### `setText(mixed $value)`

Setzt den Wert für das Feld `text` (Text).

```php
$dataset = Article::create();
$dataset->setText('Langer Beschreibungstext');
$dataset->save();
```

### `getPrice()`

Gibt den Wert für das Feld `price` (Preis) zurück:

```php
$dataset = Article::get($id);
echo $dataset->getPrice();
```

### `setPrice(float $value)`

Setzt den Wert für das Feld `price` (Preis).

```php
$dataset = Article::create();
$dataset->setPrice(9.99);
$dataset->save();
```

### `getPriceFormatted()`

Gibt den Preis formatiert als Währungs-String zurück:

```php
$dataset = Article::get($id);
echo $dataset->getPriceFormatted();
```

### `getPriceText()`

Gibt den Wert für das Feld `price_text` (Preis-Text) zurück:

```php
$dataset = Article::get($id);
echo $dataset->getPriceText();
```

### `setPriceText(mixed $value)`

Setzt den Wert für das Feld `price_text` (Preis-Text).

```php
$dataset = Article::create();
$dataset->setPriceText('ab 9,99 €');
$dataset->save();
```

### `getTax()`

Gibt den Wert für das Feld `tax` (Steuer) zurück:

```php
$dataset = Article::get($id);
echo $dataset->getTax();
```

### `setTax(mixed $value)`

Setzt den Wert für das Feld `tax` (Steuer).

```php
$dataset = Article::create();
$dataset->setTax('19');
$dataset->save();
```

### `getUpdatedate()`

Gibt den Wert für das Feld `updatedate` (Zuletzt geändert) zurück:

```php
$dataset = Article::get($id);
echo $dataset->getUpdatedate();
```

### `setUpdatedate(string $value)`

Setzt den Wert für das Feld `updatedate` (Zuletzt geändert).

```php
$dataset = Article::create();
$dataset->setUpdatedate('2024-01-01 12:00:00');
$dataset->save();
```

### `getVariants()`

Gibt die zugehörigen Varianten als Collection zurück:

```php
$dataset = Article::get($id);
$varianten = $dataset->getVariants();
```

### `getAvailability()`

Gibt den Wert für das Feld `availability` (Verfügbarkeit) zurück:

```php
$dataset = Article::get($id);
echo $dataset->getAvailability();
```

### `getAvailabilityLabel()`

Gibt das übersetzte Label für die Verfügbarkeit zurück:

```php
$dataset = Article::get($id);
echo $dataset->getAvailabilityLabel();
```

### `setAvailability(mixed $value)`

Setzt den Wert für das Feld `availability` (Verfügbarkeit).

```php
$dataset = Article::create();
$dataset->setAvailability('InStock');
$dataset->save();
```

### `getProjectValue(string $key)`

Gibt den Wert eines projektbezogenen Feldes zurück:

```php
$dataset = Article::get($id);
echo $dataset->getProjectValue('foo');
```

### `setProjectValue(string $key, mixed $value)`

Setzt den Wert eines projektbezogenen Feldes:

```php
$dataset = Article::get($id);
$dataset->setProjectValue('foo', 'bar');
$dataset->save();
```

### `getUrl(string $profile = 'warehouse-article-id')`

Gibt die URL zum Artikel zurück:

```php
$dataset = Article::get($id);
echo $dataset->getUrl();
```

### `getBackendUrl()`

Gibt die Backend-URL zum Bearbeiten des Artikels zurück:

```php
$dataset = Article::get($id);
echo $dataset->getBackendUrl();
```

### `getBackendIcon(bool $label = false)`

Gibt das Backend-Icon (optional mit Label) zurück:

```php
$icon = Article::getBackendIcon();
$iconMitLabel = Article::getBackendIcon(true);
```

### `getBulkPrices()`

Gibt die Staffelpreise zurück (derzeit leer):

```php
$preise = Article::getBulkPrices();
```

### `getByUuid(string $uuid)`

Findet einen Artikel anhand der UUID:

```php
$article = Article::getByUuid('deine-uuid');
```
