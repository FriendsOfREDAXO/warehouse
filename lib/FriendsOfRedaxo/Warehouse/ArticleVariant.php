<?php
namespace FriendsOfRedaxo\Warehouse;

use NumberFormatter;
use rex;
use rex_clang;
use rex_config;
use rex_i18n;
use rex_yform;
use rex_article;
use rex_user;
use rex_media;
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
    public function getImage(bool $asMedia = false) : mixed
    {
        if ($asMedia) {
            return rex_media::get($this->getValue("image"));
        }
        return $this->getValue("image");
    }
    /** @api */
    public function setImage(string $filename) : self
    {
        $this->setValue("image", $filename);
        return $this;
    }


    public static function getAvailabilityOptions()
    {
        
        return self::availability;
    }

    public static function getStatusOptions()
    {
        return self::status;
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

        return $yform;
    }
}
