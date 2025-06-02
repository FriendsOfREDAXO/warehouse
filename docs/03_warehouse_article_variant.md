# Die Klasse `ArticleVariant`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_warehouse_article_variant` zu.

> Es werden nachfolgend nur die durch dieses Addon ergänzten Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
use FriendsOfRedaxo\Warehouse\ArticleVariant;
$variants = ArticleVariant::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getArticle()`

Gibt das zugehörige Haupt-Artikel-Objekt zurück:

```php
$dataset = ArticleVariant::get($id);
$article = $dataset->getArticle();
```

### `getName()`

Gibt den Wert für das Feld `name` (Name) zurück:

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getName();
```

### `setName(mixed $value)`

Setzt den Wert für das Feld `name` (Name).

```php
$dataset = ArticleVariant::create();
$dataset->setName('Größe L');
$dataset->save();
```

### `getPrice()`

Gibt den Wert für das Feld `price` (Preis) zurück:

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getPrice();
```

### `setPrice(float $value)`

Setzt den Wert für das Feld `price` (Preis).

```php
$dataset = ArticleVariant::create();
$dataset->setPrice(19.99);
$dataset->save();
```

### `getBulkPrices()`

Gibt die Staffelpreise als Array zurück (ggf. Fallback auf Hauptartikel):

```php
$dataset = ArticleVariant::get($id);
$preise = $dataset->getBulkPrices();
```

### `getWeight()`

Gibt den Wert für das Feld `weight` (Gewicht) zurück:

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getWeight();
```

### `setWeight(float $value)`

Setzt den Wert für das Feld `weight` (Gewicht).

```php
$dataset = ArticleVariant::create();
$dataset->setWeight(0.5);
$dataset->save();
```

### `getAvailability()`

Gibt den Wert für das Feld `availability` (Verfügbarkeit) zurück:

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getAvailability();
```

### `getAvailabilityLabel()`

Gibt das übersetzte Label für die Verfügbarkeit zurück:

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getAvailabilityLabel();
```

### `setAvailability(mixed $param)`

Setzt den Wert für das Feld `availability` (Verfügbarkeit).

```php
$dataset = ArticleVariant::create();
$dataset->setAvailability('InStock');
$dataset->save();
```

### `getImage()`

Gibt den Dateinamen des Bildes zurück (ggf. Fallback):

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getImage();
```

### `getImageAsMedia()`

Gibt das Bild als rex_media-Objekt zurück:

```php
$dataset = ArticleVariant::get($id);
$media = $dataset->getImageAsMedia();
```

### `setImage(string $filename)`

Setzt den Wert für das Feld `image` (Bild).

```php
$dataset = ArticleVariant::create();
$dataset->setImage('bild.jpg');
$dataset->save();
```

### `getProjectValue(string $key)`

Gibt den Wert eines projektbezogenen Feldes zurück:

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getProjectValue('foo');
```

### `setProjectValue(string $key, mixed $value)`

Setzt den Wert eines projektbezogenen Feldes:

```php
$dataset = ArticleVariant::get($id);
$dataset->setProjectValue('foo', 'bar');
$dataset->save();
```

### `getBackendUrl()`

Gibt die Backend-URL zum Bearbeiten der Variante zurück:

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getBackendUrl();
```

### `getBackendIcon(bool $label = false)`

Gibt das Backend-Icon (optional mit Label) zurück:

```php
$icon = ArticleVariant::getBackendIcon();
$iconMitLabel = ArticleVariant::getBackendIcon(true);
```

### `getUrl(string $profile = 'warehouse-article-variant-id')`

Gibt die URL zur Variante zurück:

```php
$dataset = ArticleVariant::get($id);
echo $dataset->getUrl();
```

### `getAvailabilityOptions()`

Gibt die verfügbaren Verfügbarkeitsoptionen zurück:

```php
$options = ArticleVariant::getAvailabilityOptions();
```

### `getStatusOptions()`

Gibt die verfügbaren Statusoptionen zurück:

```php
$options = ArticleVariant::getStatusOptions();
```
