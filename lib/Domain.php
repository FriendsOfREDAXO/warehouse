<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_article;
use rex_sql;
use rex_yrewrite_domain;
use rex_yform_manager_dataset;
use rex_yrewrite;

class Domain extends rex_yform_manager_dataset
{
    // Single point of truth for field names
    public const string ID = 'id';
    public const string YREWRITE_DOMAIN_ID = 'yrewrite_domain_id';
    public const string CART_ART_ID = 'cart_art_id';
    public const string CHECKOUT_ART_ID = 'checkout_art_id';
    public const string SHIPPINGINFO_ART_ID = 'shippinginfo_art_id';
    public const string ADDRESS_ART_ID = 'address_art_id';
    public const string ORDER_ART_ID = 'order_art_id';
    public const string PAYMENT_ERROR_ART_ID = 'payment_error_art_id';
    public const string THANKYOU_ART_ID = 'thankyou_art_id';
    public const string EMAIL_TEMPLATE_CUSTOMER = 'email_template_customer';
    public const string EMAIL_TEMPLATE_SELLER = 'email_template_seller';
    public const string ORDER_EMAIL = 'order_email';
    public const string EMAIL_SIGNATURE = 'email_signature';
    public const string SEPA_BANK_NAME = 'sepa_bank_name';
    public const string SEPA_BIC = 'sepa_bic';
    public const string SEPA_IBAN = 'sepa_iban';
    public const string SEPA_ACCOUNT_HOLDER_NAME = 'sepa_account_holder_name';

    public const array FIELD_CONFIG = [
        self::ID => [],
        self::YREWRITE_DOMAIN_ID => [],
        self::CART_ART_ID => [],
        self::CHECKOUT_ART_ID => [],
        self::SHIPPINGINFO_ART_ID => [],
        self::ADDRESS_ART_ID => [],
        self::ORDER_ART_ID => [],
        self::PAYMENT_ERROR_ART_ID => [],
        self::THANKYOU_ART_ID => [],
        self::EMAIL_TEMPLATE_CUSTOMER => [],
        self::EMAIL_TEMPLATE_SELLER => [],
        self::ORDER_EMAIL => [],
        self::EMAIL_SIGNATURE => [],
        self::SEPA_BANK_NAME => [],
        self::SEPA_BIC => [],
        self::SEPA_IBAN => [],
        self::SEPA_ACCOUNT_HOLDER_NAME => [],
    ];

    public static function getCurrent() : ?self
    {
        $domain = rex_yrewrite::getCurrentDomain();
        if ($domain instanceof rex_yrewrite_domain) {
            $yrewrite_domain_id = $domain->getId();
            return self::query()->where(self::YREWRITE_DOMAIN_ID, $yrewrite_domain_id)->findOne();
        }
        return null;
    }

    public static function getCurrentUrl() : string
    {
        $domain = rex_yrewrite::getCurrentDomain();
        if ($domain instanceof rex_yrewrite_domain) {
            return $domain->getUrl();
        }
        return '';
    }
    
    /* Domain */
    /** @api */
    public function getYrewriteDomain() : ?rex_yrewrite_domain
    {
        return rex_yrewrite::getDomainById($this->getValue(self::YREWRITE_DOMAIN_ID));
    }
    /** @api */
    public function getYrewriteDomainId() : ?int
    {
        return $this->getValue(self::YREWRITE_DOMAIN_ID);
    }
    /** @api */
    public function setYrewriteDomainId(int $value) : self
    {
        $this->setValue(self::YREWRITE_DOMAIN_ID, $value);
        return $this;
    }

    /* Checkout */
    /** @api */
    public function getCheckoutArt() : ?rex_article
    {
        return rex_article::get($this->getValue(self::CHECKOUT_ART_ID));
    }
    public function getCheckoutArtId() : ?int
    {
        return $this->getValue(self::CHECKOUT_ART_ID);
    }
    /**
     * @param array<string, mixed> $params
     */
    public function getCheckoutUrl(array $params = [], string $divider = '&amp;') : string
    {
        if (null !== ($article = $this->getCheckoutArt())) {
            return $article->getUrl($params, $divider);
        }
        return '';
    }

    /* Warenkorb */
    /** @api */
    public function getCartArt() : ?rex_article
    {
        return rex_article::get($this->getValue(self::CART_ART_ID));
    }
    public function getCartArtId() : ?int
    {
        return $this->getValue(self::CART_ART_ID);
    }
    /**
     * @param array<string, mixed> $params
     */
    public function getCartArtUrl(array $params = [], string $divider = '&amp;') : string
    {
        if (null !== ($article = $this->getCartArt())) {
            return $article->getUrl($params, $divider);
        }
        return '';
    }
    /** @api */
    public function setCartArtId(int $id) : self
    {
        if (rex_article::get((int) $id)) {
            $this->setValue(self::CART_ART_ID, $id);
        }
        return $this;
    }

    /* Versandinfo */
    /** @api */
    public function getShippinginfoArt() : ?rex_article
    {
        return rex_article::get($this->getValue(self::SHIPPINGINFO_ART_ID));
    }
    public function getShippinginfoArtId() : ?int
    {
        return $this->getValue(self::SHIPPINGINFO_ART_ID);
    }
    /**
     * @param array<string, mixed> $params
     */
    public function getShippinginfoArtUrl(array $params = [], string $divider = '&amp;') : string
    {
        if ($article = $this->getShippinginfoArt()) {
            return $article->getUrl($params, $divider);
        }
        return '';
    }
    /** @api */
    public function setShippinginfoArtId(int $id) : self
    {
        if (rex_article::get((int) $id)) {
            $this->setValue(self::SHIPPINGINFO_ART_ID, $id);
        }
        return $this;
    }

    /* Adresse */
    /** @api */
    public function getAddressArt() : ?rex_article
    {
        return rex_article::get($this->getValue(self::ADDRESS_ART_ID));
    }
    public function getAddressArtId() : ?int
    {
        return $this->getValue(self::ADDRESS_ART_ID);
    }
    /**
     * @param array<string, mixed> $params
     */
    public function getAddressArtUrl(array $params = [], string $divider = '&amp;') : string
    {
        if (null !== ($article = $this->getAddressArt())) {
            return $article->getUrl($params, $divider);
        }
        return '';
    }
    /** @api */
    public function setAddressArtId(int $id) : self
    {
        if (rex_article::get((int) $id)) {
            $this->setValue(self::ADDRESS_ART_ID, $id);
        }
        return $this;
    }

    /* Bestellung */
    /** @api */
    public function getOrderArt() : ?rex_article
    {
        return rex_article::get($this->getValue(self::ORDER_ART_ID));
    }
    public function getOrderArtId() : ?int
    {
        return $this->getValue(self::ORDER_ART_ID);
    }
    /**
     * @param array<string, mixed> $params
     */
    public function getOrderArtUrl(array $params = [], string $divider = '&amp;') : string
    {
        if (null !== $article = $this->getOrderArt()) {
            return $article->getUrl($params, $divider);
        }
        return '';
    }
    /** @api */
    public function setOrderArtId(int $id) : self
    {
        if (rex_article::get((int) $id)) {
            $this->setValue(self::ORDER_ART_ID, $id);
        }
        return $this;
    }

    /* Zahlungsfehler */
    /** @api */
    public function getPaymentErrorArt() : ?rex_article
    {
        return rex_article::get($this->getValue(self::PAYMENT_ERROR_ART_ID));
    }
    public function getPaymentErrorArtId() : ?int
    {
        return $this->getValue(self::PAYMENT_ERROR_ART_ID);
    }
    /**
     * @param array<string, mixed> $params
     */
    public function getPaymentErrorArtUrl(array $params = [], string $divider = '&amp;') : string
    {
        if (null !== ($article = $this->getPaymentErrorArt())) {
            return $article->getUrl($params, $divider);
        }
        return '';
    }
    /** @api */
    public function setPaymentErrorArtId(int $id) : self
    {
        if (rex_article::get((int) $id)) {
            $this->setValue(self::PAYMENT_ERROR_ART_ID, $id);
        }
        return $this;
    }

    /* Danke */
    /** @api */
    public function getThankyouArt() : ?rex_article
    {
        return rex_article::get($this->getValue(self::THANKYOU_ART_ID));
    }
    public function getThankyouArtId() : ?int
    {
        return $this->getValue(self::THANKYOU_ART_ID);
    }
    /**
     * @param array<string, mixed> $params
     */
    public function getThankyouArtUrl(array $params = [], string $divider = '&amp;') : string
    {
        if (null !== ($article = $this->getThankyouArt())) {
            return $article->getUrl($params, $divider);
        }
        return '';
    }
    /** @api */
    public function setThankyouArtId(int $id) : self
    {
        if (rex_article::get((int) $id)) {
            $this->setValue(self::THANKYOU_ART_ID, $id);
        }
        return $this;
    }

    
    /* E-Mail-Template an Verkäufer */
    /** @api */
    public function getEmailTemplateSeller() : ?string
    {
        return $this->getValue(self::EMAIL_TEMPLATE_SELLER);
    }
    /** @api */
    public function setEmailTemplateSeller(mixed $value) : self
    {
        $this->setValue(self::EMAIL_TEMPLATE_SELLER, $value);
        return $this;
    }

    /* Empfänger E-Mail für Bestellungen */
    /** @api */
    public function getOrderEmail() : ?string
    {
        return $this->getValue(self::ORDER_EMAIL);
    }
    /** @api */
    public function setOrderEmail(mixed $value) : self
    {
        $this->setValue(self::ORDER_EMAIL, $value);
        return $this;
    }

    /**
     * @return array<int|string, string>
     */
    public static function getEmailTemplateOptions() : array
    {
        // E-Mail-Templates aus YForm auswählen, wenn YForm installiert
        if (\rex_addon::get('yform')->isAvailable()) {
            $options = [];
            $templates = rex_sql::factory()->getArray('SELECT id, name FROM ' . \rex::getTable('yform_email_template') . ' ORDER BY name');
            foreach ($templates as $template) {
                $options[$template['id']] = $template['name'];
            }
            return $options;
        }
        return [];
    }

}
