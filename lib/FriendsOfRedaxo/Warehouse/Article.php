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
use Url\Url;

class Article extends rex_yform_manager_dataset
{
    
    public const availability =
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

    public const status =
        [
            'active' => 'translate:warehouse_article.status.active',
            'draft' => 'translate:warehouse_article.status.draft',
            'hidden' => 'translate:warehouse_article.status.hidden',
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
    public function getStatus() : mixed
    {
        return $this->getValue("status");
    }

    public function getStatusLabel() : ?string
    {
        return rex_i18n::rawMsg(self::status[$this->getStatus()] ?? '');
    }

    /** @api */
    public function setStatus(mixed $param) : mixed
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
    /** @api */
    public function getPrice() : ?float
    {
        return $this->getValue("price");
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
        return $formatter->formatCurrency($this->getPrice(), rex_config::get('warehouse', 'currency'));
    }

    /* Preis-Text */
    /** @api */
    public function getPriceText() : ?string
    {
        return $this->getValue("price_text");
    }
    /** @api */
    public function setPriceText(mixed $value) : self
    {
        $this->setValue("price_text", $value);
        return $this;
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
    /** @api */
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
        return rex_i18n::rawMsg(self::availability[$this->getAvailability()] ?? '');
    }


    /** @api */
    public function setAvailability(mixed $value) : self
    {
        $this->setValue("availability", $value);
        return $this;
    }

    public static function findArticle(int|array $category_ids = null, string $status = 'active', bool $available = true) : ?rex_yform_manager_collection
    {
        $query = self::query();
        if ($category_ids !== null) {
            $query->where('category_id', $category_ids);
        }
        $query->where('status', $status);
        if ($available) {
            $query->where('availability', 'InStock');
        }
        return $query->find();
    }

    public static function getAvailabilityOptions() : array
    {
        
        return self::availability;
    }

    public static function getStatusOptions() : array
    {
        return self::status;
    }

    public static function getTaxOptions() : array
    {
        return [
            '19' => '19%',
            '7' => '7%',
            '0' => '0%',
        ];
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
    
    public static function epYformDataList(rex_extension_point $ep)
    {
        /** @var rex_yform_manager_table $table */
        $table = $ep->getParam('table');
        if ($table->getTableName() !== self::table()->getTableName()) {
            return;
        }

        /** @var rex_yform_list $list */
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
                $params['_csrf_token'] = $token['_csrf_token'];
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
        $params['_csrf_token'] = rex_csrf_token::factory(self::table()->getCSRFKey())->getUrlParams()['_csrf_token'];
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

    public static function getBulkPrices() {
        return [];
    }
}
