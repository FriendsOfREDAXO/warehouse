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
    
    public const availability =
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

    public const status =
        [
            'active' => 'translate:warehouse_article_variant.status.active',
            'draft' => 'translate:warehouse_article_variant.status.draft',
            'hidden' => 'translate:warehouse_article_variant.status.hidden',
        ];

    /* Haupt-Artikel */
    /** @api */
    public function getArticle() : ?rex_yform_manager_dataset
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
        return rex_i18n::rawMsg(self::availability[$this->getAvailability()] ?? '');
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
        
        return self::availability;
    }

    public static function getStatusOptions() : array
    {
        return self::status;
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
        if (!Warehouse::isPricePerAmountEnabled()) {
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
}
