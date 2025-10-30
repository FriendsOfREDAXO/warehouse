# Die Klasse `Category`

Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_category` zu.

> Es werden nachfolgend nur die durch dieses Addon ergänzten Methoden beschrieben. Lerne mehr über YOrm und den Methoden für Querys, Datasets und Collections in der [YOrm Doku](https://github.com/yakamara/yform/blob/master/docs/04_yorm.md)

## Alle Einträge erhalten

```php
use FriendsOfRedaxo\Warehouse\Category;
$entries = Category::query()->find(); // YOrm-Standard-Methode zum Finden von Einträgen, lässt sich mit where(), Limit(), etc. einschränken und Filtern.
```

## Methoden und Beispiele

### `getParent()`

Gibt das übergeordnete Kategorie-Objekt zurück:

```php
$dataset = Category::get($id);
$parent = $dataset->getParent();
```

### `getName()`

Gibt den Wert für das Feld `name` zurück:

```php
$dataset = Category::get($id);
echo $dataset->getName();
```

### `setName(mixed $value)`

Setzt den Wert für das Feld `name`.

```php
$dataset = Category::create();
$dataset->setName('Kategorie-Name');
$dataset->save();
```

### `getTeaser()`

Gibt den Wert für das Feld `teaser` zurück:

```php
$dataset = Category::get($id);
echo $dataset->getTeaser();
```

### `setTeaser(mixed $value)`

Setzt den Wert für das Feld `teaser`.

```php
$dataset = Category::create();
$dataset->setTeaser('Kurzer Teasertext');
$dataset->save();
```

### `getImage()`

Gibt den Dateinamen des Bildes zurück (ggf. Fallback):

```php
$dataset = Category::get($id);
echo $dataset->getImage();
```

### `getImageAsMedia()`

Gibt das Bild als rex_media-Objekt zurück:

```php
$dataset = Category::get($id);
$media = $dataset->getImageAsMedia();
```

### `setImage(string $filename)`

Setzt den Wert für das Feld `image` (nur wenn die Datei existiert).

```php
$dataset = Category::create();
$dataset->setImage('bild.jpg');
$dataset->save();
```

### `getText(bool $asPlaintext = false)`

Gibt den Wert für das Feld `text` zurück. Optional als Plaintext:

```php
$dataset = Category::get($id);
echo $dataset->getText(true);
```

### `setText(mixed $value)`

Setzt den Wert für das Feld `text`.

```php
$dataset = Category::create();
$dataset->setText('Beschreibungstext');
$dataset->save();
```

### `getStatus()`

Gibt den Wert für das Feld `status` zurück:

```php
$dataset = Category::get($id);
echo $dataset->getStatus();
```

### `setStatus(mixed $param)`

Setzt den Wert für das Feld `status`.

```php
$dataset = Category::create();
$dataset->setStatus('active');
$dataset->save();
```

### `getUuid()`

Gibt den Wert für das Feld `uuid` zurück:

```php
$dataset = Category::get($id);
echo $dataset->getUuid();
```

### `setUuid(mixed $value)`

Setzt den Wert für das Feld `uuid`.

```php
$dataset = Category::create();
$dataset->setUuid('deine-uuid');
$dataset->save();
```

### `getUpdatedate()`

Gibt den Wert für das Feld `updatedate` zurück:

```php
$dataset = Category::get($id);
echo $dataset->getUpdatedate();
```

### `setUpdatedate(string $value)`

Setzt den Wert für das Feld `updatedate`.

```php
$dataset = Category::create();
$dataset->setUpdatedate('2024-01-01 12:00:00');
$dataset->save();
```

### `findChildren(int $status = 1)`

Gibt die Kind-Kategorien zurück:

```php
$dataset = Category::get($id);
$children = $dataset->findChildren();
```

### `getArticles(int $status = 1, int $limit = 48, int $offset = 0)`

Gibt die zugehörigen Artikel zurück:

```php
$dataset = Category::get($id);
$articles = $dataset->getArticles();
```

### `findRootCategories(int $status = 1, int $limit = 48, int $offset = 0)`

Gibt die Root-Kategorien zurück:

```php
$categories = Category::findRootCategories();
```

### `getStatusOptions()`

Gibt die verfügbaren Statusoptionen zurück:

```php
$options = Category::getStatusOptions();
```

### `getProjectValue(string $key)`

Gibt den Wert eines projektbezogenen Feldes zurück:

```php
$dataset = Category::get($id);
echo $dataset->getProjectValue('foo');
```

### `setProjectValue(string $key, mixed $value)`

Setzt den Wert eines projektbezogenen Feldes:

```php
$dataset = Category::get($id);
$dataset->setProjectValue('foo', 'bar');
$dataset->save();
```

### `getBackendUrl()`

Gibt die Backend-URL zum Bearbeiten der Kategorie zurück:

```php
$dataset = Category::get($id);
echo $dataset->getBackendUrl();
```

### `getBackendIcon(bool $label = false)`

Gibt das Backend-Icon (optional mit Label) zurück:

```php
$icon = Category::getBackendIcon();
$iconMitLabel = Category::getBackendIcon(true);
```

### `getUrl(string $profile = 'warehouse-category-id')`

Gibt die URL zur Kategorie zurück:

```php
$dataset = Category::get($id);
echo $dataset->getUrl();
```
