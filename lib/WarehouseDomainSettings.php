<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_article;
use rex_sql;
use rex_yrewrite_domain;
use rex_yform_manager_dataset;
use rex_yrewrite;

class WarehouseDomainSettings extends rex_yform_manager_dataset
{
    
    /* Domain */
    /** @api */
    public function getYrewriteDomain() : ?rex_yrewrite_domain
    {
        return rex_yrewrite::getDomainById($this->getValue("yrewrite_domain_id"));
    }
    /** @api */
    public function getYrewriteDomainId() : ?int
    {
        return $this->getValue("yrewrite_domain_id");
    }
    /** @api */
    public function setYrewriteDomainId(int $value) : self
    {
        $this->setValue("yrewrite_domain_id", $value);
        return $this;
    }

    /* Warenkorb */
    /** @api */
    public function getCartArt() : ?rex_article
    {
        return rex_article::get($this->getValue("cart_art_id"));
    }
    public function getCartArtId() : ?int
    {
        return $this->getValue("cart_art_id");
    }
    public function getCartArtIdUrl() : ?string
    {
        if ($article = $this->getCartArt()) {
            return $article->getUrl();
        }
    }
    /** @api */
    public function setCartArtId(string $id) : self
    {
        if (rex_article::get($id)) {
            $this->getValue("cart_art_id", $id);
        }
        return $this;
    }

    /* Versandinfo */
    /** @api */
    public function getShippinginfoArt() : ?rex_article
    {
        return rex_article::get($this->getValue("shippinginfo_art_id"));
    }
    public function getShippinginfoArtId() : ?int
    {
        return $this->getValue("shippinginfo_art_id");
    }
    public function getShippinginfoArtIdUrl() : ?string
    {
        if ($article = $this->getShippinginfoArt()) {
            return $article->getUrl();
        }
    }
    /** @api */
    public function setShippinginfoArtId(string $id) : self
    {
        if (rex_article::get($id)) {
            $this->getValue("shippinginfo_art_id", $id);
        }
        return $this;
    }

    /* Adresse */
    /** @api */
    public function getAddressArt() : ?rex_article
    {
        return rex_article::get($this->getValue("address_art_id"));
    }
    public function getAddressArtId() : ?int
    {
        return $this->getValue("address_art_id");
    }
    public function getAddressArtIdUrl() : ?string
    {
        if ($article = $this->getAddressArt()) {
            return $article->getUrl();
        }
    }
    /** @api */
    public function setAddressArtId(string $id) : self
    {
        if (rex_article::get($id)) {
            $this->getValue("address_art_id", $id);
        }
        return $this;
    }

    /* Bestellung */
    /** @api */
    public function getOrderArt() : ?rex_article
    {
        return rex_article::get($this->getValue("order_art_id"));
    }
    public function getOrderArtId() : ?int
    {
        return $this->getValue("order_art_id");
    }
    public function getOrderArtIdUrl() : ?string
    {
        if ($article = $this->getOrderArt()) {
            return $article->getUrl();
        }
    }
    /** @api */
    public function setOrderArtId(string $id) : self
    {
        if (rex_article::get($id)) {
            $this->getValue("order_art_id", $id);
        }
        return $this;
    }

    /* Zahlungsfehler */
    /** @api */
    public function getPaymentErrorArt() : ?rex_article
    {
        return rex_article::get($this->getValue("payment_error_art_id"));
    }
    public function getPaymentErrorArtId() : ?int
    {
        return $this->getValue("payment_error_art_id");
    }
    public function getPaymentErrorArtIdUrl() : ?string
    {
        if ($article = $this->getPaymentErrorArt()) {
            return $article->getUrl();
        }
    }
    /** @api */
    public function setPaymentErrorArtId(string $id) : self
    {
        if (rex_article::get($id)) {
            $this->getValue("payment_error_art_id", $id);
        }
        return $this;
    }

    /* Danke */
    /** @api */
    public function getThankyouArt() : ?rex_article
    {
        return rex_article::get($this->getValue("thankyou_art_id"));
    }
    public function getThankyouArtId() : ?int
    {
        return $this->getValue("thankyou_art_id");
    }
    public function getThankyouArtIdUrl() : ?string
    {
        if ($article = $this->getThankyouArt()) {
            return $article->getUrl();
        }
    }
    /** @api */
    public function setThankyouArtId(string $id) : self
    {
        if (rex_article::get($id)) {
            $this->getValue("thankyou_art_id", $id);
        }
        return $this;
    }

    
    /* E-Mail-Template an Verkäufer */
    /** @api */
    public function getEmailTemplateSeller() : ?string
    {
        return $this->getValue("email_template_seller");
    }
    /** @api */
    public function setEmailTemplateSeller(mixed $value) : self
    {
        $this->setValue("email_template_seller", $value);
        return $this;
    }

    /* Empfänger E-Mail für Bestellungen */
    /** @api */
    public function getOrderEmail() : ?string
    {
        return $this->getValue("order_email");
    }
    /** @api */
    public function setOrderEmail(mixed $value) : self
    {
        $this->setValue("order_email", $value);
        return $this;
    }

    public static function getEmailTemplateOptions() : array
    {
        // E-Mail-Templates aus YForm auswählen, wenn YForm installiert
        if (\rex_addon::get('yform')->isAvailable()) {
            $options = [];
            $templates = rex_sql::factory()->getArray('SELECT id, name FROM ' . \rex::getTable('yform_email_template') . ' WHERE status = 1 ORDER BY name');
            foreach ($templates as $template) {
                $options[$template['id']] = $template['name'];
            }
            return $options;
        }
    }

}
