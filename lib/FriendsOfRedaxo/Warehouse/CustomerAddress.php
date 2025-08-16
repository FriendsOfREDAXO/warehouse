<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_ycom_auth;
use rex_ycom_user;
use rex_yform_manager_dataset;

class CustomerAddress extends rex_yform_manager_dataset
{
    const TYPE_OPTIONS = [
        'private' => 'translate:warehouse.customer_address.type.private',
        'company' => 'translate:warehouse.customer_address.type.company',
        'shipping' => 'translate:warehouse.customer_address.type.shipping',
        'billing' => 'translate:warehouse.customer_address.type.billing',
        'other' => 'translate:warehouse.customer_address.type.other'
    ];

    /* YCom-Benutzer-ID */
    /** @api */
    public function getYcomUser() : ?rex_yform_manager_dataset
    {
        return $this->getRelatedDataset("ycom_user_id");
    }

    /* Typ */
    /** @api */
    public function getType() : ?string
    {
        return $this->getValue("type");
    }
    /** @api */
    public function setType(mixed $value) : self
    {
        $this->setValue("type", $value);
        return $this;
    }
    
    /** @api */
    public static function getTypeOptions() : array
    {
        return self::TYPE_OPTIONS;
    }

    /* Firma */
    /** @api */
    public function getCompany() : ?string
    {
        return $this->getValue("company");
    }
    /** @api */
    public function setCompany(mixed $value) : self
    {
        $this->setValue("company", $value);
        return $this;
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

    /* Straße */
    /** @api */
    public function getStreet() : ?string
    {
        return $this->getValue("street");
    }
    /** @api */
    public function setStreet(mixed $value) : self
    {
        $this->setValue("street", $value);
        return $this;
    }

    /* PLZ */
    /** @api */
    public function getZip() : ?string
    {
        return $this->getValue("zip");
    }
    /** @api */
    public function setZip(mixed $value) : self
    {
        $this->setValue("zip", $value);
        return $this;
    }

    /* Stadt */
    /** @api */
    public function getCity() : ?string
    {
        return $this->getValue("city");
    }
    /** @api */
    public function setCity(mixed $value) : self
    {
        $this->setValue("city", $value);
        return $this;
    }

    /* Land */
    /** @api */
    public function getCountry() : ?string
    {
        return $this->getValue("country");
    }
    /** @api */
    public function setCountry(mixed $value) : self
    {
        $this->setValue("country", $value);
        return $this;
    }

    public function saveInSession() {
        $data = [
            'type' => $this->getType(),
            'company' => $this->getCompany(),
            'name' => $this->getName(),
            'street' => $this->getStreet(),
            'zip' => $this->getZip(),
            'city' => $this->getCity(),
            'country' => $this->getCountry(),
        ];

        if($this->getType() === 'shipping') {
            Session::setShippingAddress($data);
        } elseif($this->getType() === 'billing') {
            Session::setBillingAddress($data);
        } else {
            // Handle other types if necessary
        }
    }


}
