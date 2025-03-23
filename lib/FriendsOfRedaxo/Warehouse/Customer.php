<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_ycom_user;

class Customer
{
    const CUSTOMER = [
        'firstname' => '',
        'lastname' => '',
        'company' => '',
        'billing_address' => [
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'street' => '',
            'zip' => '',
            'city' => '',
        ],
        'shipping_address' => [
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'street' => '',
            'zip' => '',
            'city' => '',
        ],
        'phone' => '',
        'email' => '',
        'birthdate' => '',
    ];
    
    private $customer = [];

    // initialisieren des Kunden
    public static function init()
    {
        if (rex_session('warehouse_customer', 'array', null) === null) {
            rex_set_session('warehouse_customer', self::CUSTOMER);
        }
    }

    // set und get Methoden für Vorname, Nachname, Firma, Rechnungsadresse (Straße, PLZ, Ort), Lieferadresse (Straße, PLZ, Ort), Telefon, E-Mail
    /** @api */
    public function setFirstname(string $firstname): self
    {
        $this->customer['firstname'] = $firstname;
        return $this;
    }
    /** @api */
    public function getFirstname(): string
    {
        return $this->customer['firstname'];
    }

    /** @api */
    public function setLastname(string $lastname): self
    {
        $this->customer['lastname'] = $lastname;
        return $this;
    }
    /** @api */
    public function getLastname(): string
    {
        return $this->customer['lastname'];
    }

    /** @api */
    public function setCompany(string $company): self
    {
        $this->customer['company'] = $company;
        return $this;
    }
    /** @api */
    public function getCompany(): string
    {
        return $this->customer['company'];
    }

    /** @api */
    public function setBillingAddress(string $street, string $zip, string $city): self
    {
        $this->customer['billing_address']['firstname'] = $this->customer['firstname'];
        $this->customer['billing_address']['lastname'] = $this->customer['lastname'];
        $this->customer['billing_address']['company'] = $this->customer['company'];
        $this->customer['billing_address']['street'] = $street;
        $this->customer['billing_address']['zip'] = $zip;
        $this->customer['billing_address']['city'] = $city;
        return $this;
    }
    /** @api */
    public function getBillingAddress(): array
    {
        return $this->customer['billing_address'];
    }

    /** @api */
    public function setShippingAddress(string $street, string $zip, string $city): self
    {
        $this->customer['shipping_address']['firstname'] = $this->customer['firstname'];
        $this->customer['shipping_address']['lastname'] = $this->customer['lastname'];
        $this->customer['shipping_address']['company'] = $this->customer['company'];
        $this->customer['shipping_address']['street'] = $street;
        $this->customer['shipping_address']['zip'] = $zip;
        $this->customer['shipping_address']['city'] = $city;
        return $this;
    }

    /** @api */
    public function getShippingAddress(): array
    {
        return $this->customer['shipping_address'];
    }

    /** @api */
    public function setPhone(string $phone): self
    {
        $this->customer['phone'] = $phone;
        return $this;
    }

    /** @api */
    public function getPhone(): string
    {
        return $this->customer['phone'];
    }

    /** @api */
    public function setEmail(string $email): self
    {
        $this->customer['email'] = $email;
        return $this;
    }

    /** @api */
    public function getEmail(): string
    {
        return $this->customer['email'];
    }

    /** @api */
    public function getBirthdate(): string
    {
        return $this->customer['birthdate'];
    }

    /** @api */
    public function setBirthdate(string $birthdate): self
    {
        $this->customer['birthdate'] = $birthdate;
        return $this;
    }


    // speichern des Kunden in der Session
    /** @api */
    public function save()
    {
        rex_set_session('warehouse_customer', $this->customer);
    }

    // laden des Kunden aus der Session
    /** @api */
    public function load()
    {
        $this->customer = rex_session('warehouse_customer', 'array', []);
    }

    // löschen des Kunden aus der Session
    /** @api */
    public function delete()
    {
        rex_set_session('warehouse_customer', []);
    }

    // Customer aus YCom-User erstellen, wenn YCom installiert ist und der User eingeloggt ist
    /** @api */
    public function createFromYComUser()
    {
        if (rex_addon::get('ycom')->isAvailable()) {
            $user = rex_ycom_user::getMe();
            if($user === null) {
                return;
            }
            $this->setFirstname($user->getValue('firstname'));
            $this->setLastname($user->getValue('lastname'));
            $this->setEmail($user->getValue('email'));
            $this->setPhone($user->getValue('phone'));
            $this->setCompany($user->getValue('company'));
            $this->setBillingAddress($user->getValue('address_street'), $user->getValue('address_zip'), $user->getValue('address_city'));
            $this->setShippingAddress($user->getValue('address_street'), $user->getValue('address_zip'), $user->getValue('address_city'));
            $this->save();
        }
    }

}
