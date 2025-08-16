<?php
namespace FriendsOfRedaxo\Warehouse;

use NumberFormatter;
use rex_config;
use rex_i18n;
use rex_yform;
use rex_media;
use rex_yform_manager_collection;
use rex_yform_manager_dataset;
use rex_url;
use rex_extension_point;
use rex_csrf_token;
use rex_extension;

class Article extends rex_yform_manager_dataset
{
    
    public const AVAILABILITY =
        [
            'BackOrder' => 'translate:warehouse_article.availability.BackOrder',
            'Discontinued' => 'translate:warehouse_article.availability.Discontinued',
            'InStock' => 'translate:warehouse_article.availability.InStock',
            'InStoreOnly' => 'translate:warehouse_article.availability.InStoreOnly',
            'LimitedAvailability' => 'translate:warehouse_article.availability.LimitedAvailability',
            'MadeToOrder' => 'translate:warehouse_article.availability.MadeToOrder',
            'OnlineOnly' => 'translate:warehouse_article.availability.OnlineOnly',
            'OutOfStock' => 'translate:warehouse_article.availability.OutOfStock',
            'PreOrder' => 'translate:warehouse_article.availability.PreOrder',
            'PreSale' => 'translate:warehouse_article.availability.PreSale',
            'Reserved' => 'translate:warehouse_article.availability.Reserved',
            'SoldOut' => 'translate:warehouse_article.availability.SoldOut',
        ];

    public const AVAILABLE =
        [
            'InStock',
            'LimitedAvailability',
            'BackOrder',
            'PreOrder',
            'PreSale',
        ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_HIDDEN = 'hidden';

    public const STATUS_OPTIONS =
        [
            'active' => 'translate:warehouse_article.status.active',
            'draft' => 'translate:warehouse_article.status.draft',
            'hidden' => 'translate:warehouse_article.status.hidden',
        ];

    public const DEFAULT_TAX_OPTIONS = [
        '19' => '19%',
        '7' => '7%',
        '0' => '0%',
    ];

    /* Name */
    /** @api */
    public function getName() : ?string
    {
        return $this->getValue("name");
    }
    /** @api */
    public function setName(mixed $value) : self
    {
        $this->setValue("name", $value);
        return $this;
    }

    /* Kategorie */
    /** @api */
    public function getCategory() : ?rex_yform_manager_dataset
    {
        return $this->getRelatedDataset("category_id");
    }

    /* Status */
    /** @api */
    public function getStatus() : ?string
    {
        return $this->getValue("status");
    }

    public function getStatusLabel() : ?string
    {
        return rex_i18n::rawMsg(self::STATUS_OPTIONS[$this->getStatus()] ?? '');
    }

    /** @api */
    public function setStatus(string $param) : mixed
    {
        $this->setValue("status", $param);
        return $this;
    }

    /* Gewicht */
    /** @api */
    public function getWeight() : ?float
    {
        return $this->getValue("weight");
    }
    /** @api */
    public function setWeight(float $value) : self
    {
        $this->setValue("weight", $value);
        return $this;
    }
            
    /* Bild */
    /** @api */
    public function getImage() : mixed
    {
        $image = $this->getValue("image");
        if (!$image && ($fallback = rex_config::get('warehouse', 'fallback_article_image'))) {
            return $fallback;
        }
        return $image;
    }

    /** @api */
    public function getImageAsMedia() : ?rex_media
    {
        $image = $this->getImage();
        if ($image) {
            return rex_media::get($image);
        }
        return null;
    }

    /** @api */
    public function setImage(string $filename) : self
    {
        $this->setValue("image", $filename);
        return $this;
    }
            
    /* Galerie */
    /** @api */
    public function getGallery() : mixed
    {
        return $this->getValue("gallery");
    }

    /** @api
     * @return array<rex_media>
    */
    public function getGalleryAsMedia() : ?array
    {
        $filenames = explode(',', $this->getValue("gallery"));
        $medias = [];
        foreach ($filenames as $filename) {
            $media = rex_media::get($filename);
            if ($media) {
                $medias[] = $media;
            }
        }
        return $medias;
    }

    /** @api */
    public function setGallery(string $filename) : self
    {
        $this->setValue("gallery", $filename);
        return $this;
    }
            
    /* Kurztext */
    /** @api */
    public function getShortText(bool $asPlaintext = false) : ?string
    {
        if ($asPlaintext) {
            return strip_tags($this->getValue("short_text"));
        }
        return $this->getValue("short_text");
    }
    /** @api */
    public function setShortText(mixed $value) : self
    {
        $this->setValue("short_text", $value);
        return $this;
    }
            
    /* Text */
    /** @api */
    public function getText(bool $asPlaintext = false) : ?string
    {
        if ($asPlaintext) {
            return strip_tags($this->getValue("text"));
        }
        return $this->getValue("text");
    }
    /** @api */
    public function setText(mixed $value) : self
    {
        $this->setValue("text", $value);
        return $this;
    }
            
    /* Preis */
    /** @api
     * Gibt den Preis zurück, netto oder brutto je nach Modus.
     * @param 'net'|'gross'|null $mode 'net' oder 'gross' (optional, sonst globaler Modus)
     * @return float|null
     */
    public function getPrice(?string $mode = null): ?float
    {
        $price = $this->getValue("price");
        if ($price === null) {
            return null;
        }
        $tax = (float)($this->getTax() ?? 0);
        if ($mode === null) {
            $mode = Warehouse::getPriceInputMode();
        }
        if ($mode === 'gross') {
            // Preis ist brutto, ggf. umrechnen falls netto gespeichert
            if (Warehouse::getPriceInputMode() === 'net') {
                return $price * (1 + $tax / 100);
            }
            return $price;
        } else {
            // Preis ist netto, ggf. umrechnen falls brutto gespeichert
            if (Warehouse::getPriceInputMode() === 'gross') {
                return $price / (1 + $tax / 100);
            }
            return $price;
        }
    }
    /** @api */
    public function setPrice(float $value) : self
    {
        $this->setValue("price", $value);
        return $this;
    }

    public function getPriceFormatted() : string
    {
        $formatter = new NumberFormatter('de_de', NumberFormatter::CURRENCY);
        $price = $this->getPrice();
        if ($price === null) {
            return '';
        }
        $currencyCode = Warehouse::getCurrency();
        $formatted = $formatter->formatCurrency($price, $currencyCode);
        return $formatted;
    }

    /* Steuer */
    /** @api */
    public function getTax() : ?string
    {
        return $this->getValue("tax");
    }
    /** @api */
    public function setTax(mixed $value) : self
    {
        $this->setValue("tax", $value);
        return $this;
    }

    /* Zuletzt geändert */
    /** @api */
    public function getUpdatedate() : ?string
    {
        return $this->getValue("updatedate");
    }
    /** @api */
    public function setUpdatedate(string $value) : self
    {
        $this->setValue("updatedate", $value);
        return $this;
    }

    /* Varianten */
    /**
     * @api
     * @return rex_yform_manager_collection<ArticleVariant>|null
     */
    public function getVariants() : ?rex_yform_manager_collection
    {
        return $this->getRelatedCollection("variant_ids");
    }

    /* Verfügbarkeit */
    /** @api */
    public function getAvailability() : ?string
    {
        return $this->getValue("availability");
    }

    public function getAvailabilityLabel() : ?string
    {
        return rex_i18n::rawMsg(self::AVAILABILITY[$this->getAvailability()] ?? '');
    }


    /** @api */
    public function setAvailability(mixed $value) : self
    {
        $this->setValue("availability", $value);
        return $this;
    }

    /**
     * @param int|array<int>|null $category_ids
     * @return rex_yform_manager_collection<Article>|null
     */
    public static function findArticle(int|array $category_ids = null, string $status = 'active', bool $availableOnly = true) : rex_yform_manager_collection
    {
        $query = self::query();
        if ($category_ids !== null) {
            $query->where('category_id', $category_ids);
        }
        $query->where('status', $status);
        if ($availableOnly) {
            $query->where('availability', 'InStock');
        }
        return $query->find();
    }

    /**
     * @return array<string, string>
     */
    public static function getAvailabilityOptions() : array
    {
        
        return self::AVAILABILITY;
    }

    /**
     * @return array<string, string>
     */
    public static function getStatusOptions() : array
    {
        return self::STATUS_OPTIONS;
    }

    /**
     * @return array<string, string>
     */
    public static function getTaxOptions() : array
    {

        // Statt statischer Werte können zusätzlich über einen Extension Point weitere Steuersätze hinzugefügt werden.
        $taxOptions = rex_extension::registerPoint(new rex_extension_point('WAREHOUSE_TAX_OPTIONS', self::DEFAULT_TAX_OPTIONS));
        if (!is_array($taxOptions)) {
            $taxOptions = self::DEFAULT_TAX_OPTIONS;
        }
        return $taxOptions;
    }

    public function getProjectValue(string $key): mixed
    {
        return $this->getValue('project_' . $key);
    }

    public function setProjectValue(string $key, mixed $value) : self
    {
        $this->setValue('project_' . $key, $value);
        return $this;
    }
    
    /**
     * @param rex_extension_point<mixed> $ep
     * @return void
     */
    public static function epYformDataList(rex_extension_point $ep): void
    {
        /** @var \rex_yform_manager_table $table */
        $table = $ep->getParam('table');
        if ($table->getTableName() !== self::table()->getTableName()) {
            return;
        }

        /** @var \rex_yform_list $list */
        $list = $ep->getSubject();

        $list->setColumnFormat(
            'name',
            'custom',
            static function ($a) {
                $_csrf_key = self::table()->getCSRFKey();
                $token = rex_csrf_token::factory($_csrf_key)->getUrlParams();

                $params = [];
                $params['table_name'] = self::table()->getTableName();
                $params['rex_yform_manager_popup'] = '0';
                $params['_csrf_token'] = $token['_csrf_token'] ?? '';
                $params['data_id'] = $a['list']->getValue('id');
                $params['func'] = 'edit';

                return '<a href="' . rex_url::backendPage('warehouse/article', $params) . '">' . $a['value'] . '</a>';
            },
        );
        $list->setColumnFormat(
            'id',
            'custom',
            static function ($a) {
                return $a['value'];
            },
        );
    }

    /**
     * Standards für das Formular anpassen
     * - Editor-Konfiguration einfügen.
     *
     * @api
     */
    public function getForm(): rex_yform
    {
        $yform = parent::getForm();

        $suchtext = '###warehouse_editor###';
        foreach ($yform->objparams['form_elements'] as $k => &$e) {
            if ('textarea' === $e[0] && str_contains($e[5], $suchtext)) {
                $e[5] = str_replace($suchtext, \rex_config::get('warehouse', 'editor'), $e[5]);
            }
        }

        $removeFields = [];
        if (!Warehouse::isBulkPricesEnabled()) {
            $removeFields[] = 'bulk_prices';
        }
        if (!Warehouse::isWeightEnabled()) {
            $removeFields[] = 'weight';
        }
        if (!Warehouse::isVariantsEnabled()) {
            $removeFields[] = 'variant_ids';
        }
        if (!Warehouse::isVariantsEnabled()) {
            $removeFields[] = 'stock';
        }

        foreach ($removeFields as $field) {
            foreach ($yform->objparams['form_elements'] as $k => &$e) {
                if ($e[1] === $field) {
                    unset($yform->objparams['form_elements'][$k]);
                }
            }
        }

        return $yform;
    }

    public function getUrl(string $profile = 'warehouse-article-id'): string
    {
        return rex_getUrl(null, null, [$profile => $this->getId()]);
    }

    public function getBackendUrl() :string
    {
        $params = [];
        $params['table_name'] = self::table()->getTableName();
        $params['rex_yform_manager_popup'] = '0';
        $tokenParams = rex_csrf_token::factory(self::table()->getCSRFKey())->getUrlParams();
        $params['_csrf_token'] = $tokenParams['_csrf_token'] ?? '';
        $params['data_id'] = $this->getId();
        $params['func'] = 'edit';

        return rex_url::backendPage('warehouse/article', $params);
    }
    public static function getBackendIcon(bool $label = false) :string
    {
        if ($label) {
            return '<i class="rex-icon fa-cube"></i> ' . rex_i18n::msg('warehouse_article.icon_label');
        }
        return '<i class="rex-icon fa-cube"></i>';
    }

    /**
     * Gibt die Staffelpreise (Bulk Prices) dieses Artikels zurück.
     * @return array<array{min: string|int, max: string|int, price: string|float}>
     */
    public function getBulkPrices(): array
    {
        $bulk_prices = (array) $this->getValue('bulk_prices');
        if (!empty($bulk_prices)) {
            $bulkPrices = @json_decode($this->getValue('bulk_prices'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }
            if (is_array($bulkPrices)) {
                return $bulkPrices;
            }
        }
        return [];
    }

    /**
     * Gibt den Gesamtpreis für eine bestimmte Menge zurück, unter Berücksichtigung von Staffelpreisen und Modus.
     * @param int $quantity
     * @param 'net'|'gross'|null $mode 'net' oder 'gross' (optional, sonst globaler Modus)
     * @return float
     */
    public function getPriceForQuantity(int $quantity, ?string $mode = null): float
    {
        $bulkPrices = $this->getBulkPrices();
        $tax = (float)($this->getTax() ?? 0);
        if (!empty($bulkPrices)) {
            foreach ($bulkPrices as $bulk) {
                if (
                    $quantity >= (int)$bulk['min'] &&
                    ($quantity <= (int)$bulk['max'] || (int)$bulk['max'] === 0)
                ) {
                    $price = (float)$bulk['price'];
                    if ($mode === null) {
                        $mode = Warehouse::getPriceInputMode();
                    }
                    if ($mode === 'gross' && Warehouse::getPriceInputMode() === 'net') {
                        $price = $price * (1 + $tax / 100);
                    } elseif ($mode === 'net' && Warehouse::getPriceInputMode() === 'gross') {
                        $price = $price / (1 + $tax / 100);
                    }
                    return $price * $quantity;
                }
            }
        }
        $price = $this->getPrice($mode);
        if ($price !== null && (float)$price !== 0.0) {
            return (float)$price * $quantity;
        }
        // 3. Kein Preis gefunden
        return 0.0;
    }

    public static function getByUuid(string $uuid) : ?self
    {
        $query = self::query();
        $query->where('uuid', $uuid);
        return $query->findOne();
    }
}
