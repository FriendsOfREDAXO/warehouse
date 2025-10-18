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

    // Single point of truth for field names
    public const string ID = 'id';
    public const string YCOM_USER_ID = 'ycom_user_id';
    public const string TYPE = 'type';
    public const string COMPANY = 'company';
    public const string NAME = 'name';
    public const string STREET = 'street';
    public const string ZIP = 'zip';
    public const string CITY = 'city';
    public const string COUNTRY = 'country';

    public const array FIELD_CONFIG = [
        self::ID => [],
        self::YCOM_USER_ID => [],
        self::TYPE => [],
        self::COMPANY => [],
        self::NAME => [],
        self::STREET => [],
        self::ZIP => [],
        self::CITY => [],
        self::COUNTRY => [],
    ];

    /* YCom-Benutzer-ID */
    /** @api */
    public function getYcomUser() : ?rex_yform_manager_dataset
    {
        return $this->getRelatedDataset(self::YCOM_USER_ID);
    }

    /* Typ */
    /** @api */
    public function getType() : ?string
    {
        return $this->getValue(self::TYPE);
    }
    /** @api */
    public function setType(mixed $value) : self
    {
        $this->setValue(self::TYPE, $value);
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
        return $this->getValue(self::COMPANY);
    }
    /** @api */
    public function setCompany(mixed $value) : self
    {
        $this->setValue(self::COMPANY, $value);
        return $this;
    }

    /* Name */
    /** @api */
    public function getName() : ?string
    {
        return $this->getValue(self::NAME);
    }
    /** @api */
    public function setName(mixed $value) : self
    {
        $this->setValue(self::NAME, $value);
        return $this;
    }

    /* Straße */
    /** @api */
    public function getStreet() : ?string
    {
        return $this->getValue(self::STREET);
    }
    /** @api */
    public function setStreet(mixed $value) : self
    {
        $this->setValue(self::STREET, $value);
        return $this;
    }

    /* PLZ */
    /** @api */
    public function getZip() : ?string
    {
        return $this->getValue(self::ZIP);
    }
    /** @api */
    public function setZip(mixed $value) : self
    {
        $this->setValue(self::ZIP, $value);
        return $this;
    }

    /* Stadt */
    /** @api */
    public function getCity() : ?string
    {
        return $this->getValue(self::CITY);
    }
    /** @api */
    public function setCity(mixed $value) : self
    {
        $this->setValue(self::CITY, $value);
        return $this;
    }

    /* Land */
    /** @api */
    public function getCountry() : ?string
    {
        return $this->getValue(self::COUNTRY);
    }
    /** @api */
    public function setCountry(mixed $value) : self
    {
        $this->setValue(self::COUNTRY, $value);
        return $this;
    }

    public function saveInSession() {
        $data = [
            self::TYPE => $this->getType(),
            self::COMPANY => $this->getCompany(),
            self::NAME => $this->getName(),
            self::STREET => $this->getStreet(),
            self::ZIP => $this->getZip(),
            self::CITY => $this->getCity(),
            self::COUNTRY => $this->getCountry(),
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
