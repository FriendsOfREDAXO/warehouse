<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_ycom_auth;
use FriendsOfRedaxo\Warehouse\CustomerAddress;
use rex_yform_manager_dataset;

const SALUTATION_FIELD = 'salutation';
const FIRSTNAME_FIELD = 'firstname';
const LASTNAME_FIELD = 'lastname';
const COMPANY_FIELD = 'company';
const DEPARTMENT_FIELD = 'department';
const ADDRESS_FIELD = 'address';
const PHONE_FIELD = 'phone';
const ZIP_FIELD = 'zip';
const CITY_FIELD = 'city';

const EMAIL_FIELD = 'email';

const ALL_FIELDS = [
    SALUTATION_FIELD,
    FIRSTNAME_FIELD,
    LASTNAME_FIELD,
    COMPANY_FIELD,
    DEPARTMENT_FIELD,
    ADDRESS_FIELD,
    PHONE_FIELD,
    ZIP_FIELD,
    CITY_FIELD,
    EMAIL_FIELD
];

/**
 * Class Customer
 *
 * Represents a customer in the warehouse system.
 * Extends the YForm manager dataset for customer data management.
 */

class Customer extends rex_yform_manager_dataset
{
    // Single point of truth for field names
    public const string ID = 'id';
    public const string LOGIN = 'login';
    public const string EMAIL = 'email';
    public const string FIRSTNAME = 'firstname';
    public const string LASTNAME = 'lastname';
    public const string SALUTATION = 'salutation';
    public const string COMPANY = 'company';
    public const string DEPARTMENT = 'department';
    public const string ADDRESS = 'address';
    public const string PHONE = 'phone';
    public const string ZIP = 'zip';
    public const string CITY = 'city';
    public const string STATUS = 'status';
    public const string CREATEDATE = 'createdate';
    public const string UPDATEDATE = 'updatedate';

    public const array FIELD_CONFIG = [
        self::ID => [],
        self::LOGIN => [],
        self::EMAIL => [],
        self::FIRSTNAME => [],
        self::LASTNAME => [],
        self::SALUTATION => [],
        self::COMPANY => [],
        self::DEPARTMENT => [],
        self::ADDRESS => [],
        self::PHONE => [],
        self::ZIP => [],
        self::CITY => [],
        self::STATUS => [],
        self::CREATEDATE => [],
        self::UPDATEDATE => [],
    ];

    public static function getCurrent()
    {
        $user_id = rex_ycom_auth::getUser()?->getValue(self::ID) ?? null;
        if (!$user_id) {
            return null;
        }
        $customer = self::get($user_id);

        return $customer;
    }
    
    /* E-Mail */
    /** @api */
    public function getEmail() : ?string
    {
        return $this->getValue(self::EMAIL);
    }
    /** @api */
    public function setEmail(mixed $value) : self
    {
        $this->setValue(self::EMAIL, $value);
        return $this;
    }


    /* Vorname */
    /** @api */
    public function getFirstname() : ?string
    {
        return $this->getValue(self::FIRSTNAME);
    }
    /** @api */
    public function setFirstname(mixed $value) : self
    {
        $this->setValue(self::FIRSTNAME, $value);
        return $this;
    }
    /* Nachname */
    /** @api */
    public function getLastname() : ?string
    {
        return $this->getValue(self::LASTNAME);
    }
    /** @api */
    public function setLastname(mixed $value) : self
    {
        $this->setValue(self::LASTNAME, $value);
        return $this;
    }

    /* Anrede */
    /** @api */
    public function getSalutation() : ?string
    {
        return $this->getValue(self::SALUTATION);
    }
    /** @api */
    public function setSalutation(mixed $value) : self
    {
        $this->setValue(self::SALUTATION, $value);
        return $this;
    }

    /** @api */
    public function getFullName() : string
    {
        $salutation = $this->getSalutation() ? $this->getSalutation() . ' ' : '';
        return $salutation . $this->getFirstname() . ' ' . $this->getLastname();
    }

    public function getShippingAddress() : ?CustomerAddress
    {
        $address = CustomerAddress::query()
            ->where(CustomerAddress::YCOM_USER_ID, $this->getValue(self::ID))
            ->where(CustomerAddress::TYPE, 'shipping')
            ->findOne();
        if ($address) {
            return $address;
        }
        // Fallback to the main address if no shipping address is set
        $address = CustomerAddress::query()
            ->where(CustomerAddress::YCOM_USER_ID, $this->getValue(self::ID))
            ->findOne();
        if ($address) {
            return $address;
        }
        return null;
    }

    public function saveInSession() {
        $data = [
            self::ID => $this->getId(),
            self::EMAIL => $this->getEmail(),
            self::SALUTATION => $this->getSalutation(),
            self::FIRSTNAME => $this->getFirstname(),
            self::LASTNAME => $this->getLastname(),
            self::COMPANY => $this->getCompany(),
            // self::DEPARTMENT => $this->getDepartment(),
            self::ADDRESS => $this->getAddress(),
            self::PHONE => $this->getPhone(),
            self::ZIP => $this->getZip(),
            self::CITY => $this->getCity()
        ];
        // Save the customer data in the session
        Session::setCustomer($data);
    }
    
    /** @api */
    public function getCompany(): ?string
    {
        return $this->getValue(self::COMPANY);
    }
    /** @api */
    public function setCompany(mixed $value): self
    {
        $this->setValue(self::COMPANY, $value);
        return $this;
    }

    /** @api */
    public function getDepartment(): ?string
    {
        return $this->getValue(self::DEPARTMENT);
    }
    /** @api */
    public function setDepartment(mixed $value): self
    {
        $this->setValue(self::DEPARTMENT, $value);
        return $this;
    }

    /** @api */
    public function getAddress(): ?string
    {
        return $this->getValue(self::ADDRESS);
    }
    /** @api */
    public function setAddress(mixed $value): self
    {
        $this->setValue(self::ADDRESS, $value);
        return $this;
    }

    /** @api */
    public function getZip(): ?string
    {
        return $this->getValue(self::ZIP);
    }
    /** @api */
    public function setZip(mixed $value): self
    {
        $this->setValue(self::ZIP, $value);
        return $this;
    }

    /** @api */
    public function getCity(): ?string
    {
        return $this->getValue(self::CITY);
    }
    /** @api */
    public function setCity(mixed $value): self
    {
        $this->setValue(self::CITY, $value);
        return $this;
    }

    /** @api */
    public function getPhone(): ?string
    {
        return $this->getValue(self::PHONE);
    }
    /** @api */
    public function setPhone(mixed $value): self
    {
        $this->setValue(self::PHONE, $value);
        return $this;
    }

}
