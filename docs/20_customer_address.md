# Die Klasse `CustomerAddress`

Die CustomerAddress-Klasse verwaltet Kundenadressen im Warehouse-Addon. Sie erweitert `rex_yform_manager_dataset` und ermöglicht die Speicherung verschiedener Adresstypen für YCom-Benutzer.

> Kind-Klasse von `rex_yform_manager_dataset`, damit stehen alle Methoden von YOrm-Datasets zur Verfügung. Greift auf die Tabelle `rex_warehouse_customer_address` zu.

## Übersicht

Die CustomerAddress-Klasse bietet folgende Funktionen:

- Verwaltung verschiedener Adresstypen (privat, Firma, Versand, Rechnung)
- Verknüpfung mit YCom-Benutzern
- Vollständige Adressdaten-Verwaltung
- API-kompatible Getter- und Setter-Methoden

## Konstanten

### `TYPE_OPTIONS`

Definiert die verfügbaren Adresstypen:

```php
const TYPE_OPTIONS = [
    'private' => 'translate:warehouse.customer_address.type.private',    // Privat
    'company' => 'translate:warehouse.customer_address.type.company',    // Firma
    'shipping' => 'translate:warehouse.customer_address.type.shipping',  // Lieferadresse
    'billing' => 'translate:warehouse.customer_address.type.billing',    // Rechnungsadresse
    'other' => 'translate:warehouse.customer_address.type.other'         // Sonstige
];
```

## Alle Einträge erhalten

```php
use FriendsOfRedaxo\Warehouse\CustomerAddress;

// Alle Adressen abrufen
$addresses = CustomerAddress::query()->find();

// Adressen eines bestimmten Benutzers
$userAddresses = CustomerAddress::query()
    ->where('ycom_user_id', $userId)
    ->find();

// Nur Lieferadressen
$shippingAddresses = CustomerAddress::query()
    ->where('type', 'shipping')
    ->find();
```

## Methoden und Beispiele

### Benutzer-Verknüpfung

#### `getYcomUser()`

Gibt das verknüpfte YCom-Benutzer-Objekt zurück.

**Rückgabe:** `rex_yform_manager_dataset|null` - YCom-Benutzer-Objekt

```php
$address = CustomerAddress::get($id);
$user = $address->getYcomUser();

if ($user) {
    echo "Adresse gehört zu: " . $user->getValue('firstname') . ' ' . $user->getValue('lastname');
}
```

### Adresstyp-Verwaltung

#### `getType()`

Gibt den Adresstyp zurück.

**Rückgabe:** `string|null` - Adresstyp

```php
$address = CustomerAddress::get($id);
$type = $address->getType();

echo "Adresstyp: " . $type; // z.B. "shipping"
```

#### `setType(mixed $value)`

Setzt den Adresstyp.

**Parameter:**

- `$value` (mixed): Adresstyp

```php
$address = CustomerAddress::create();
$address->setType('shipping');
$address->save();
```

#### `getTypeOptions()`

Gibt alle verfügbaren Adresstypen zurück (statische Methode).

**Rückgabe:** `array` - Array mit Adresstyp-Optionen

```php
$typeOptions = CustomerAddress::getTypeOptions();

foreach ($typeOptions as $key => $label) {
    echo '<option value="' . $key . '">' . rex_i18n::msg($label) . '</option>';
}
```

### Firmendaten

#### `getCompany()`

Gibt den Firmennamen zurück.

**Rückgabe:** `string|null` - Firmenname

```php
$address = CustomerAddress::get($id);
$company = $address->getCompany();

if ($company) {
    echo "Firma: " . $company;
}
```

#### `setCompany(mixed $value)`

Setzt den Firmennamen.

**Parameter:**

- `$value` (mixed): Firmenname

```php
$address = CustomerAddress::create();
$address->setCompany('Muster GmbH');
$address->save();
```

### Personendaten

#### `getName()`

Gibt den vollständigen Namen zurück.

**Rückgabe:** `string|null` - Name

```php
$address = CustomerAddress::get($id);
echo "Name: " . $address->getName();
```

#### `setName(mixed $value)`

Setzt den Namen.

**Parameter:**

- `$value` (mixed): Name

```php
$address = CustomerAddress::create();
$address->setName('Max Mustermann');
$address->save();
```

### Straßenadresse

#### `getStreet()`

Gibt die Straße mit Hausnummer zurück.

**Rückgabe:** `string|null` - Straße

```php
$address = CustomerAddress::get($id);
echo "Straße: " . $address->getStreet();
```

#### `setStreet(mixed $value)`

Setzt die Straße mit Hausnummer.

**Parameter:**

- `$value` (mixed): Straße

```php
$address = CustomerAddress::create();
$address->setStreet('Musterstraße 123');
$address->save();
```

### Postleitzahl

#### `getZip()`

Gibt die Postleitzahl zurück.

**Rückgabe:** `string|null` - PLZ

```php
$address = CustomerAddress::get($id);
echo "PLZ: " . $address->getZip();
```

#### `setZip(mixed $value)`

Setzt die Postleitzahl.

**Parameter:**

- `$value` (mixed): PLZ

```php
$address = CustomerAddress::create();
$address->setZip('12345');
$address->save();
```

### Stadt

#### `getCity()`

Gibt den Stadtnamen zurück.

**Rückgabe:** `string|null` - Stadt

```php
$address = CustomerAddress::get($id);
echo "Stadt: " . $address->getCity();
```

#### `setCity(mixed $value)`

Setzt den Stadtnamen.

**Parameter:**

- `$value` (mixed): Stadt

```php
$address = CustomerAddress::create();
$address->setCity('Musterstadt');
$address->save();
```

### Land

#### `getCountry()`

Gibt das Land zurück.

**Rückgabe:** `string|null` - Land

```php
$address = CustomerAddress::get($id);
echo "Land: " . $address->getCountry();
```

#### `setCountry(mixed $value)`

Setzt das Land.

**Parameter:**

- `$value` (mixed): Land

```php
$address = CustomerAddress::create();
$address->setCountry('Deutschland');
$address->save();
```

## Praktische Anwendungsbeispiele

### Neue Adresse erstellen

```php
use FriendsOfRedaxo\Warehouse\CustomerAddress;
use rex_ycom_auth;

// Aktuelle Benutzer-ID ermitteln
$user = rex_ycom_auth::getUser();
$userId = $user ? $user->getId() : null;

// Neue Lieferadresse erstellen
$address = CustomerAddress::create();
$address->setValue('ycom_user_id', $userId);
$address->setType('shipping');
$address->setName('Max Mustermann');
$address->setStreet('Lieferstraße 456');
$address->setZip('54321');
$address->setCity('Lieferstadt');
$address->setCountry('Deutschland');
$address->save();

echo "Lieferadresse wurde erstellt mit ID: " . $address->getId();
```

### Alle Adressen eines Benutzers abrufen

```php
use FriendsOfRedaxo\Warehouse\CustomerAddress;

$userId = 123;
$addresses = CustomerAddress::query()
    ->where('ycom_user_id', $userId)
    ->orderBy('type ASC, name ASC')
    ->find();

foreach ($addresses as $address) {
    echo '<div class="address-card">';
    echo '<h4>' . $address->getName() . ' (' . $address->getType() . ')</h4>';
    
    if ($address->getCompany()) {
        echo '<p><strong>' . $address->getCompany() . '</strong></p>';
    }
    
    echo '<p>';
    echo $address->getStreet() . '<br>';
    echo $address->getZip() . ' ' . $address->getCity() . '<br>';
    echo $address->getCountry();
    echo '</p>';
    echo '</div>';
}
```

### Standard-Rechnungsadresse finden

```php
use FriendsOfRedaxo\Warehouse\CustomerAddress;

$userId = 123;
$billingAddress = CustomerAddress::query()
    ->where('ycom_user_id', $userId)
    ->where('type', 'billing')
    ->findOne();

if (!$billingAddress) {
    // Fallback: Erste private Adresse verwenden
    $billingAddress = CustomerAddress::query()
        ->where('ycom_user_id', $userId)
        ->where('type', 'private')
        ->findOne();
}

if ($billingAddress) {
    echo "Rechnungsadresse: " . $billingAddress->getName();
}
```

### Adresstyp-Dropdown für Formulare

```php
use FriendsOfRedaxo\Warehouse\CustomerAddress;

$typeOptions = CustomerAddress::getTypeOptions();

echo '<select name="address_type" class="form-control">';
echo '<option value="">Adresstyp wählen</option>';

foreach ($typeOptions as $key => $label) {
    echo '<option value="' . $key . '">' . rex_i18n::msg($label) . '</option>';
}

echo '</select>';
```

### Adresse formatiert ausgeben

```php
function formatAddress(CustomerAddress $address, bool $includeCompany = true): string {
    $output = [];
    
    if ($includeCompany && $address->getCompany()) {
        $output[] = $address->getCompany();
    }
    
    $output[] = $address->getName();
    $output[] = $address->getStreet();
    $output[] = $address->getZip() . ' ' . $address->getCity();
    
    if ($address->getCountry()) {
        $output[] = $address->getCountry();
    }
    
    return implode("\n", array_filter($output));
}

// Verwendung
$address = CustomerAddress::get($id);
echo '<pre>' . htmlspecialchars(formatAddress($address)) . '</pre>';
```

## Integration mit YForm-Formularen

### Adresse-Formular erstellen

```php
use rex_yform;

$yform = new rex_yform();
$yform->setObjectparams('form_anchor', false);

// Adresstyp
$yform->setValueField('select', [
    'type',
    'Adresstyp',
    CustomerAddress::getTypeOptions()
]);

// Name
$yform->setValueField('text', ['name', 'Name']);
$yform->setValidateField('empty', ['name', 'Bitte geben Sie einen Namen ein']);

// Firma (optional)
$yform->setValueField('text', ['company', 'Firma (optional)']);

// Straße
$yform->setValueField('text', ['street', 'Straße']);
$yform->setValidateField('empty', ['street', 'Bitte geben Sie die Straße ein']);

// PLZ
$yform->setValueField('text', ['zip', 'PLZ']);
$yform->setValidateField('empty', ['zip', 'Bitte geben Sie die PLZ ein']);

// Stadt
$yform->setValueField('text', ['city', 'Stadt']);
$yform->setValidateField('empty', ['city', 'Bitte geben Sie die Stadt ein']);

// Land
$yform->setValueField('text', ['country', 'Land']);

echo $yform->getForm();
```

## Verwendung in Templates

### Adressliste anzeigen

```php
<?php
use FriendsOfRedaxo\Warehouse\CustomerAddress;
use FriendsOfRedaxo\Warehouse\Customer;

$customer = Customer::getCurrent();
if ($customer) {
    $addresses = CustomerAddress::query()
        ->where('ycom_user_id', $customer->getId())
        ->find();
?>

<div class="customer-addresses">
    <h3>Meine Adressen</h3>
    
    <?php if (count($addresses) > 0): ?>
        <div class="row">
            <?php foreach ($addresses as $address): ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <strong><?= htmlspecialchars($address->getName()) ?></strong>
                        <span class="badge bg-secondary"><?= rex_i18n::msg(CustomerAddress::TYPE_OPTIONS[$address->getType()]) ?></span>
                    </div>
                    <div class="card-body">
                        <?php if ($address->getCompany()): ?>
                            <p class="mb-1"><strong><?= htmlspecialchars($address->getCompany()) ?></strong></p>
                        <?php endif; ?>
                        <p class="mb-1"><?= htmlspecialchars($address->getStreet()) ?></p>
                        <p class="mb-1"><?= htmlspecialchars($address->getZip() . ' ' . $address->getCity()) ?></p>
                        <?php if ($address->getCountry()): ?>
                            <p class="mb-0"><?= htmlspecialchars($address->getCountry()) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="edit-address.php?id=<?= $address->getId() ?>" class="btn btn-sm btn-outline-primary">Bearbeiten</a>
                        <a href="delete-address.php?id=<?= $address->getId() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Adresse wirklich löschen?')">Löschen</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">Sie haben noch keine Adressen gespeichert.</p>
    <?php endif; ?>
    
    <div class="mt-3">
        <a href="add-address.php" class="btn btn-primary">Neue Adresse hinzufügen</a>
    </div>
</div>

<?php
}
?>
```

## Best Practices

### Adressvalidierung

```php
function validateAddress(CustomerAddress $address): array {
    $errors = [];
    
    if (!$address->getName()) {
        $errors[] = 'Name ist erforderlich';
    }
    
    if (!$address->getStreet()) {
        $errors[] = 'Straße ist erforderlich';
    }
    
    if (!$address->getZip()) {
        $errors[] = 'PLZ ist erforderlich';
    }
    
    if (!$address->getCity()) {
        $errors[] = 'Stadt ist erforderlich';
    }
    
    // PLZ-Format prüfen (beispielhaft für Deutschland)
    if ($address->getZip() && !preg_match('/^\d{5}$/', $address->getZip())) {
        $errors[] = 'PLZ muss aus 5 Ziffern bestehen';
    }
    
    return $errors;
}
```

### Duplikate vermeiden

```php
function findSimilarAddress(CustomerAddress $newAddress): ?CustomerAddress {
    return CustomerAddress::query()
        ->where('ycom_user_id', $newAddress->getValue('ycom_user_id'))
        ->where('street', $newAddress->getStreet())
        ->where('zip', $newAddress->getZip())
        ->where('city', $newAddress->getCity())
        ->findOne();
}
```
