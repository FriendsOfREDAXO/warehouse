<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_ycom_auth;
use FriendsOfRedaxo\Warehouse\CustomerAddress;
use rex_yform_manager_dataset;

class Customer extends rex_yform_manager_dataset
{
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
    public function getEmail() : mixed {
        return $this->getValue("email");
    }
    /** @api */
    public function setEmail(mixed $value) : self {
        $this->setValue("email", $value);
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
    /* [translate:warehouse.ycom_user.lastname] */
    /** @api */
    public function getLastname() : ?string {
        return $this->getValue("lastname");
    }
    /** @api */
    public function setLastname(mixed $value) : self {
        $this->setValue("lastname", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.salutation] */
    /** @api */
    public function getSalutation() : ?string {
        return $this->getValue("salutation");
    }
    /** @api */
    public function setSalutation(mixed $value) : self {
        $this->setValue("salutation", $value);
        return $this;
    }

    /** @api */
    public function getFullName() : string {
        $salutation = $this->getSalutation() ? $this->getSalutation() . ' ' : '';
        return $salutation . $this->getFirstname() . ' ' . $this->getLastname();
    }

    /* [translate:warehouse.ycom_user.company] */
    /** @api */
    public function getCompany() : ?string {
        return $this->getValue("company");
    }
    /** @api */
    public function setCompany(mixed $value) : self {
        $this->setValue("company", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.department] */
    /** @api */
    public function getDepartment() : ?string {
        return $this->getValue("department");
    }
    /** @api */
    public function setDepartment(mixed $value) : self {
        $this->setValue("department", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.address] */
    /** @api */
    public function getAddress() : ?string {
        return $this->getValue("address");
    }
    /** @api */
    public function setAddress(mixed $value) : self {
        $this->setValue("address", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.phone] */
    /** @api */
    public function getPhone() : ?string {
        return $this->getValue("phone");
    }
    /** @api */
    public function setPhone(mixed $value) : self {
        $this->setValue("phone", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.zip] */
    /** @api */
    public function getZip() : ?string {
        return $this->getValue("zip");
    }
    /** @api */
    public function setZip(mixed $value) : self {
        $this->setValue("zip", $value);
        return $this;
    }

    /* [translate:warehouse.ycom_user.city] */
    /** @api */
    public function getCity() : ?string {
        return $this->getValue("city");
    }
    /** @api */
    public function setCity(mixed $value) : self {
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

}
