<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_ycom_auth;
use rex_article;
use rex_user;
use rex_media;
use yrewrite_domain;
use rex_yform_manager_collection;
use rex_yform_manager_dataset;

class Order extends rex_yform_manager_dataset {
        
        /* Anrede */
        /** @api */
        public function getSalutation() : ?string {
            return $this->getValue("salutation");
        }
        /** @api */
        public function setSalutation(mixed $value) : self {
            $this->setValue("salutation", $value);
            return $this;
        }
    
        /* Vorname */
        /** @api */
        public function getFirstname() : mixed {
            return $this->getValue("firstname");
        }
        /** @api */
        public function setFirstname(mixed $value) : self {
            $this->setValue("firstname", $value);
            return $this;
        }
    
        /* Nachname */
        /** @api */
        public function getLastname() : mixed {
            return $this->getValue("lastname");
        }
        /** @api */
        public function setLastname(mixed $value) : self {
            $this->setValue("lastname", $value);
            return $this;
        }
    
        /* Firma */
        /** @api */
        public function getCompany() : mixed {
            return $this->getValue("company");
        }
        /** @api */
        public function setCompany(mixed $value) : self {
            $this->setValue("company", $value);
            return $this;
        }
    
        /* Adresse */
        /** @api */
        public function getAddress() : mixed {
            return $this->getValue("address");
        }
        /** @api */
        public function setAddress(mixed $value) : self {
            $this->setValue("address", $value);
            return $this;
        }
    
        /* PLZ */
        /** @api */
        public function getZip() : mixed {
            return $this->getValue("zip");
        }
        /** @api */
        public function setZip(mixed $value) : self {
            $this->setValue("zip", $value);
            return $this;
        }
    
        /* Stadt */
        /** @api */
        public function getCity() : mixed {
            return $this->getValue("city");
        }
        /** @api */
        public function setCity(mixed $value) : self {
            $this->setValue("city", $value);
            return $this;
        }
    
        /* Land */
        /** @api */
        public function getCountry() : ?string {
            return $this->getValue("country");
        }
        /** @api */
        public function setCountry(mixed $value) : self {
            $this->setValue("country", $value);
            return $this;
        }
    
        /* E-Mail */
        /** @api */
        public function getEmail() : ?string {
            return $this->getValue("email");
        }
        /** @api */
        public function setEmail(mixed $value) : self {
            $this->setValue("email", $value);
            return $this;
        }
    
        /* Erstellungsdatum */
        /** @api */
        public function getCreatedate() : ?string {
            return $this->getValue("createdate");
        }

        // TODO: IntldateFromatter verwenden
        /** @api */
        public function getCreatedateFormatted() : ?string {
            return date('d.m.Y H:i',strtotime($this->getValue("createdate")));
        }

        /** @api */
        public function setCreatedate(string $value) : self {
            $this->setValue("createdate", $value);
            return $this;
        }
    
        /* PayPal-ID */
        /** @api */
        public function getPaypalId() : mixed {
            return $this->getValue("paypal_id");
        }
        /** @api */
        public function setPaypalId(mixed $value) : self {
            $this->setValue("paypal_id", $value);
            return $this;
        }
    
        /* Zahlungs-ID */
        /** @api */
        public function getPaymentId() : ?string {
            return $this->getValue("payment_id");
        }
        /** @api */
        public function setPaymentId(mixed $value) : self {
            $this->setValue("payment_id", $value);
            return $this;
        }
    
        /* PayPal-Bestätigungstoken */
        /** @api */
        public function getPaypalConfirmToken() : ?string {
            return $this->getValue("paypal_confirm_token");
        }
        /** @api */
        public function setPaypalConfirmToken(mixed $value) : self {
            $this->setValue("paypal_confirm_token", $value);
            return $this;
        }
    
        /* Zahlungsbestätigung */
        /** @api */
        public function getPaymentConfirm() : ?string {
            return $this->getValue("payment_confirm");
        }
        /** @api */
        public function setPaymentConfirm(mixed $value) : self {
            $this->setValue("payment_confirm", $value);
            return $this;
        }
    
        /* Bestelltext */
        /** @api */
        public function getOrderText(bool $asPlaintext = false) : mixed {
            if($asPlaintext) {
                return strip_tags($this->getValue("order_text"));
            }
            return $this->getValue("order_text");
        }
        /** @api */
        public function setOrderText(mixed $value) : self {
            $this->setValue("order_text", $value);
            return $this;
        }
                
        /* Bestell-JSON */
        /** @api */
        public function getOrderJson(bool $asArray = true) : mixed {
            if($asArray) {
                return json_decode($this->getValue("order_json"), true);
            }
            return $this->getValue("order_json");
        }
        /** @api */
        public function setOrderJson(string $value) : self {
            $this->setValue("order_json", $value);
            return $this;
        }
                
        /* Bestellsumme */
        /** @api */
        public function getOrderTotal() : ?float {
            return $this->getValue("order_total");
        }
        /** @api */
        public function setOrderTotal(float $value) : self {
            $this->setValue("order_total", $value);
            return $this;
        }
                
        /* YCom-Benutzer-ID */
        /** @api */
        public function getYcomUser() : ?rex_yform_manager_dataset {
            return $this->getRelatedDataset("ycom_user_id");
        }

        public function setYComUser(int $ycom_user_id) : self {
            $this->setValue("ycom_user_id", $ycom_user_id);
            return $this;
        }
    
        /* Zahlungsart */
        /** @api */
        public function getPaymentType() : ?string {
            return $this->getValue("payment_type");
        }
        /** @api */
        public function setPaymentType(mixed $value) : self {
            $this->setValue("payment_type", $value);
            return $this;
        }
    
        /* Bezahlt */
        /** @api */
        public function getPayed() : ?bool {
            return $this->getValue("payed");
        }
        /** @api */
        public function setPayed(bool $value) : self {
            $this->setValue("payed", $value);
            return $this;
        }
    
        /* Importiert */
        /** @api */
        public function getImported() : bool {
            return $this->getValue("imported");
        }
        /** @api */
        public function setImported(bool $value) : self {
            $this->setValue("imported", $value);
            return $this;
        }

    public static function findByYComUserId(int $ycom_user_id = null) : ?rex_yform_manager_collection {
        if($ycom_user_id === null) {
            $ycom_user = rex_ycom_auth::getUser();
            $ycom_user_id = $ycom_user->getId();
        }
        $data = self::query()
                ->alias('orders')
                ->where('orders.ycom_user_id',$ycom_user_id)
                ->orderBy('createdate','desc')
        ;
        return $data->find();
    }
    
    public static function findByUuid($uuid) : ?self {
        return self::query()->where('uuid',$uuid)->findOne();
       
    }
}
