<?php
namespace FriendsOfRedaxo\Warehouse;

use NumberFormatter;
use rex;
use rex_clang;
use rex_config;
use rex_i18n;
use rex_yform;
use rex_article;
use rex_csrf_token;
use rex_user;
use rex_media;
use rex_url;
use yrewrite_domain;
use rex_yform_manager_collection;
use rex_yform_manager_dataset;

class ArticleVariant extends rex_yform_manager_dataset
{
    
    public const AVAILABILITY =
        [
            'BackOrder' => 'translate:warehouse_article_variant.availability.BackOrder',
            'Discontinued' => 'translate:warehouse_article_variant.availability.Discontinued',
            'InStock' => 'translate:warehouse_article_variant.availability.InStock',
            'InStoreOnly' => 'translate:warehouse_article_variant.availability.InStoreOnly',
            'LimitedAvailability' => 'translate:warehouse_article_variant.availability.LimitedAvailability',
            'MadeToOrder' => 'translate:warehouse_article_variant.availability.MadeToOrder',
            'OnlineOnly' => 'translate:warehouse_article_variant.availability.OnlineOnly',
            'OutOfStock' => 'translate:warehouse_article_variant.availability.OutOfStock',
            'PreOrder' => 'translate:warehouse_article_variant.availability.PreOrder',
            'PreSale' => 'translate:warehouse_article_variant.availability.PreSale',
            'Reserved' => 'translate:warehouse_article_variant.availability.Reserved',
            'SoldOut' => 'translate:warehouse_article_variant.availability.SoldOut',
        ];

    public const AVAILABLE =
        [
            'InStock',
            'LimitedAvailability',
            'BackOrder',
            'PreOrder',
            'PreSale',
        ];

    public const STATUS =
        [
            'active' => 'translate:warehouse_article_variant.status.active',
            'draft' => 'translate:warehouse_article_variant.status.draft',
            'hidden' => 'translate:warehouse_article_variant.status.hidden',
        ];

    /* Haupt-Artikel */
    /** @api */
    public function getArticle() : ?Article
    {
        return $this->getRelatedDataset("article_id");
    }

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

    public function getTax() : ?float
    {
        $article = $this->getArticle();
        if ($article) {
            return $article->getTax();
        }
        return 0.0;
    }

    /* Preis */
    /** @api */    /**
     * Gibt den Preis zurück, netto oder brutto je nach Modus.
     * @param string|null $mode 'net' oder 'gross' (optional, sonst globaler Modus)
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
            if (Warehouse::getPriceInputMode() === 'net') {
                return $price * (1 + $tax / 100);
            }
            return $price;
        } else {
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
            
    /* Verfügbarkeit */
    /** @api */
    public function getAvailability() : mixed
    {
        return $this->getValue("availability");
    }

    public function getAvailabilityLabel() : ?string
    {
        return rex_i18n::rawMsg(self::AVAILABILITY[$this->getAvailability()] ?? '');
    }

    /** @api */
    public function setAvailability(mixed $param) : mixed
    {
        $this->setValue("availability", $param);
        return $this;
    }

    /* Bild */
    /** @api */
    public function getImage() : mixed
    {
        $image = $this->getValue("image");
        if (!$image) {
            $image = rex_config::get('warehouse', 'fallback_article_image');
        }
        return $image;
    }
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


    public static function getAvailabilityOptions() : array
    {
        
        return self::AVAILABILITY;
    }

    public static function getStatusOptions() : array
    {
        return self::STATUS;
    }
    

    public function getProjectValue(string $key)
    {
        return $this->getValue('project_' . $key);
    }

    public function setProjectValue(string $key, mixed $value) : self
    {
        $this->setValue('project_' . $key, $value);
        return $this;
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

        foreach ($removeFields as $field) {
            foreach ($yform->objparams['form_elements'] as $k => &$e) {
                if ($e[1] === $field) {
                    unset($yform->objparams['form_elements'][$k]);
                }
            }
        }

        return $yform;
    }

    public function getUrl(string $profile = 'warehouse-article-variant-id'): string
    {
        return rex_getUrl(null, null, [$profile => $this->getId()]);
    }

    public function getBackendUrl() :string
    {
        $params = [];
        $params['table_name'] = self::table()->getTableName();
        $params['rex_yform_manager_popup'] = '0';
        $params['_csrf_token'] = rex_csrf_token::factory(self::table()->getCSRFKey())->getUrlParams()['_csrf_token'];
        $params['data_id'] = $this->getId();
        $params['func'] = 'edit';

        return rex_url::backendPage('warehouse/article_variant', $params);
    }
    public static function getBackendIcon(bool $label = false) :string
    {
        if ($label) {
            return '<i class="rex-icon fa-cubes"></i> ' . rex_i18n::msg('warehouse_article_variant.icon_label');
        }
        return '<i class="rex-icon fa-cubes"></i>';
    }

    public function getBulkPrices() :array
    {
        $bulk_prices = (array) $this->getValue('bulk_prices');
        if (!empty($bulk_prices)) {
            $bulkPrices = json_decode($this->getValue('bulk_prices'), true);
            if (is_array($bulkPrices)) {
                return $bulkPrices;
            }
        } elseif ($this->getArticle() && !empty($this->getArticle()->getBulkPrices())) {
            // Fallback auf die Bulkpreise des Hauptartikels
            return $this->getArticle()->getBulkPrices();
        }
        return [];
    }

    /**
     * Gibt den Gesamtpreis für eine bestimmte Menge zurück, unter Berücksichtigung von Staffelpreisen, Modus und Fallbacks.
     * @param int $quantity
     * @param string|null $mode 'net' oder 'gross' (optional, sonst globaler Modus)
     * @return float
     */
    public function getPriceForQuantity(int $quantity, ?string $mode = null): float
    {
        // 1. Eigene Staffelpreise prüfen
        $bulkPrices = $this->getBulkPrices();
        $tax = (float)($this->getTax() ?? 0);
        if (!empty($bulkPrices)) {
            foreach ($bulkPrices as $bulk) {
                if (
                    isset($bulk['min'], $bulk['max'], $bulk['price']) &&
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
        if ($price !== null && $price !== '') {
            return (float)$price * $quantity;
        }
        $article = $this->getArticle();
        if ($article) {
            if (method_exists($article, 'getPriceForQuantity')) {
                return $article->getPriceForQuantity($quantity, $mode);
            } else {
                $articlePrice = $article->getPrice($mode);
                if ($articlePrice !== null && $articlePrice !== '') {
                    return (float)$articlePrice * $quantity;
                }
            }
        }
        // 4. Kein Preis gefunden
        return 0.0;
    }
}
