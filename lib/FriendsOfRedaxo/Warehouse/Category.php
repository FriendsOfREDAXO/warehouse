<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_clang;
use rex_article;
use rex_user;
use rex_media;
use yrewrite_domain;
use rex_yform_manager_collection;
use rex_yform_manager_dataset;
use rex_url;
use rex_extension_point;
use rex_csrf_token;
use rex_i18n;

class Category extends \rex_yform_manager_dataset
{

    public const status =
        [
            'active' => 'translate:warehouse_article_variant.status.active',
            'draft' => 'translate:warehouse_article_variant.status.draft',
            'hidden' => 'translate:warehouse_article_variant.status.hidden',
        ];
    
    /* Status */
    /** @api */
    public function getStatus() : mixed
    {
        return $this->getValue("status");
    }
    /** @api */
    public function setStatus(mixed $param) : mixed
    {
        $this->setValue("status", $param);
        return $this;
    }
    
    /* Name */
    /** @api */
    public function getName() : mixed
    {
        return $this->getValue("name");
    }
    /** @api */
    public function setName(mixed $value) : self
    {
        $this->setValue("name", $value);
        return $this;
    }
    
    /* Teaser */
    /** @api */
    public function getTeaser() : ?string
    {
        return $this->getValue("teaser");
    }
    /** @api */
    public function setTeaser(mixed $value) : self
    {
        $this->setValue("teaser", $value);
        return $this;
    }
    
    /* Bild */
    /** @api */
    public function getImage() : mixed
    {
        $image = $this->getValue("image");
        if ($image) {
            return $image;
        }
        // Fallback-Bild aus den Einstellungen holen
        $fallback = \rex_config::get('warehouse', 'fallback_category_image');
        return $fallback ?: null;
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
        if (rex_media::get($filename)) {
            $this->setValue("image", $filename);
        }
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
    
    /* Übergeordente Kategorie */
    /** @api */
    public function getParent() : ?rex_yform_manager_dataset
    {
        return $this->getRelatedDataset("parent_id");
    }
    
    /* UUID */
    /** @api */
    public function getUuid() : mixed
    {
        return $this->getValue("uuid");
    }
    /** @api */
    public function setUuid(mixed $value) : self
    {
        $this->setValue("uuid", $value);
        return $this;
    }

    public function findChildren(int $status = 1)
    {
        return self::query()
            ->where('parent_id', $this->getId())
            ->where('status', $status, '>=')
            ->find();
    }

    public function getArticles(int $status = 1, int $limit = 48, int $offset = 0)
    {
        return Article::query()
            ->where('category_id', $this->getId())
            ->where('status', $status, '>=')
            ->limit($offset, $limit)
            ->find();
    }

    public static function findRootCategories(int $status = 1, int $limit = 48, int $offset = 0)
    {
        $categories = self::query()
            ->where('status', $status, '>=')
            ->where('parent_id', 0)
            ->orderBy('prio')
            ->limit($offset, $limit)
            ->find();
            
        return $categories;
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
    public function getForm(): \rex_yform
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

            return '<a href="' . rex_url::backendPage('warehouse/category', $params) . '">' . $a['value'] . '</a>';
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

    public function getUrl(string $profile = 'warehouse-category-id'): string
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

        return rex_url::backendPage('warehouse/category', $params);
    }
    public static function getBackendIcon(bool $label = false) :string
    {
        if ($label) {
            return '<i class="rex-icon fa-folder"></i> ' . rex_i18n::msg('warehouse_category.icon_label');
        }
        return '<i class="rex-icon fa-folder"></i>';
    }
}
