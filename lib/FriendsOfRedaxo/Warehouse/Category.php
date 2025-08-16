<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_media;
use rex_yform_manager_dataset;
use rex_url;
use rex_extension_point;
use rex_csrf_token;
use rex_i18n;
use rex_yform_manager_collection;

class Category extends \rex_yform_manager_dataset
{

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_HIDDEN = 'hidden';
    
    public const STATUS_OPTIONS = [
        self::STATUS_ACTIVE => 'translate:warehouse_category.status.active',
        self::STATUS_DRAFT => 'translate:warehouse_category.status.draft',
        self::STATUS_HIDDEN => 'translate:warehouse_category.status.hidden',
    ];

    public const TABLE_NAME = 'warehouse_category';

    // Single point of truth for field names
    public const string ID = 'id';
    public const string PRIO = 'prio';
    public const string PARENT_ID = 'parent_id';
    public const string NAME = 'name';
    public const string TEASER = 'teaser';
    public const string IMAGE = 'image';
    public const string TEXT = 'text';
    public const string STATUS = 'status';
    public const string UUID = 'uuid';
    public const string UPDATEDATE = 'updatedate';
    public const string CREATEDATE = 'createdate';

    public const array FIELD_CONFIG = [
        self::ID => [],
        self::PRIO => [],
        self::PARENT_ID => [],
        self::NAME => [],
        self::TEASER => [],
        self::IMAGE => [],
        self::TEXT => [],
        self::STATUS => [],
        self::UUID => [],
        self::UPDATEDATE => [],
        self::CREATEDATE => [],
    ];
    
    /* Status */
    /** @api */
    public function getStatus() : ?string
    {
        // Rückgabe des Status, falls gesetzt, sonst Standardwert
        return $this->getValue("status") ? $this->getValue("status") : self::STATUS_DRAFT;
    }
    /** @api */
    public function setStatus(string $param) : mixed
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

    /**
     * @return rex_yform_manager_collection<self>
     */
    public function findChildren(string $status = self::STATUS_ACTIVE) : rex_yform_manager_collection
    {
        return self::query()
            ->where('parent_id', $this->getId())
            ->where('status', $status, '=')
            ->find();
    }

    public function getArticles(string|array $status = self::STATUS_ACTIVE, int $limit = 48, int $offset = 0): rex_yform_manager_collection
    {
        return Article::query()
            ->where('category_id', $this->getId())
            ->where('status', $status, '=')
            ->limit($offset, $limit)
            ->find();
    }

    public static function findRootCategories(string|array $status = self::STATUS_ACTIVE, int $limit = 48, int $offset = 0): rex_yform_manager_collection
    {
        $categories = self::query()
            ->where('status', $status, '=')
            ->where('parent_id', 0)
            ->orderBy('prio')
            ->limit($offset, $limit)
            ->find();
            
        return $categories;
    }

    /**
     * @return array<string, string>
     */
    public static function getStatusOptions(): array
    {
        return self::STATUS_OPTIONS;
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

    /**
     * @param rex_extension_point<mixed> $ep
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
            static function (array $a) {
                $_csrf_key = self::table()->getCSRFKey();
                $token = rex_csrf_token::factory($_csrf_key)->getUrlParams();

                $params = [];
                $params['table_name'] = self::table()->getTableName();
                $params['rex_yform_manager_popup'] = '0';
                $params['_csrf_token'] = $token['_csrf_token'] ?? '';
                /** @var rex_yform_manager_dataset $list */
                $list = $a['list'];
                $params['data_id'] = $list->getValue('id');
                $params['func'] = 'edit';

                return '<a href="' . rex_url::backendPage('warehouse/category', $params) . '">' . $a['value'] . '</a>';
            },
        );
        $list->setColumnFormat(
            'id',
            'custom',
            static function (array $a) {
                return $a['value'];
            },
        );
    }

    public function getUrl(string $profile = 'warehouse-category-id'): string
    {
        return rex_getUrl(null, null, [$profile => $this->getId()]);
    }

    public function getBackendUrl(): string
    {
        $params = [];
        $params['table_name'] = self::table()->getTableName();
        $params['rex_yform_manager_popup'] = '0';
        $urlParams = rex_csrf_token::factory(self::table()->getCSRFKey())->getUrlParams();
        $params['_csrf_token'] = $urlParams['_csrf_token'] ?? '';
        $params['data_id'] = $this->getId();
        $params['func'] = 'edit';

        return rex_url::backendPage('warehouse/category', $params);
    }
    public static function getBackendIcon(bool $label = false): string
    {
        if ($label) {
            return '<i class="rex-icon fa-folder"></i> ' . rex_i18n::msg('warehouse_category.icon_label');
        }
        return '<i class="rex-icon fa-folder"></i>';
    }
}
