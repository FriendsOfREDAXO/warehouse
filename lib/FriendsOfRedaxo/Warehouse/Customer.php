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
        $user_id = rex_ycom_auth::getUser()?->getValue('id') ?? null;
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
        return $this->getValue("email");
    }
    /** @api */
    public function setEmail(mixed $value) : self
    {
        $this->setValue("email", $value);
        return $this;
    }


    /* Vorname */
    /** @api */
    public function getFirstname() : ?string
    {
        return $this->getValue("firstname");
    }
    /** @api */
    public function setFirstname(mixed $value) : self
    {
        $this->setValue("firstname", $value);
        return $this;
    }
    /* [translate:warehouse.ycom_user.lastname] */
    /** @api */
    public function getLastname() : ?string
    {
        return $this->getValue("lastname");
    }
    /** @api */
    public function setLastname(mixed $value) : self
    {
        $this->setValue("lastname", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.salutation] */
    /** @api */
    public function getSalutation() : ?string
    {
        return $this->getValue("salutation");
    }
    /** @api */
    public function setSalutation(mixed $value) : self
    {
        $this->setValue("salutation", $value);
        return $this;
    }

    /** @api */
    public function getFullName() : string
    {
        $salutation = $this->getSalutation() ? $this->getSalutation() . ' ' : '';
        return $salutation . $this->getFirstname() . ' ' . $this->getLastname();
    }

    /* [translate:warehouse.ycom_user.company] */
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

    /* [translate:warehouse.ycom_user.department] */
    /** @api */
    public function getDepartment() : ?string
    {
        return $this->getValue("department");
    }
    /** @api */
    public function setDepartment(mixed $value) : self
    {
        $this->setValue("department", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.address] */
    /** @api */
    public function getAddress() : ?string
    {
        return $this->getValue("address");
    }
    /** @api */
    public function setAddress(mixed $value) : self
    {
        $this->setValue("address", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.phone] */
    /** @api */
    public function getPhone() : ?string
    {
        return $this->getValue("phone");
    }
    /** @api */
    public function setPhone(mixed $value) : self
    {
        $this->setValue("phone", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.zip] */
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

    /* [translate:warehouse.ycom_user.city] */
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

    public function getShippingAddress() : ?CustomerAddress
    {
        $address = CustomerAddress::query()
            ->where('ycom_user_id', $this->getValue('id'))
            ->where('type', 'shipping')
            ->findOne();
        if ($address) {
            return $address;
        }
        // Fallback to the main address if no shipping address is set
        $address = CustomerAddress::query()
            ->where('ycom_user_id', $this->getValue('id'))
            ->findOne();
        if ($address) {
            return $address;
        }
        return null;
    }

    public function saveInSession() {
        $data = [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'salutation' => $this->getSalutation(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'company' => $this->getCompany(),
            // 'department' => $this->getDepartment(),
            'address' => $this->getAddress(),
            'phone' => $this->getPhone(),
            'zip' => $this->getZip(),
            'city' => $this->getCity()
        ];
        // Save the customer data in the session
        Session::setCustomer($data);
    }
}
