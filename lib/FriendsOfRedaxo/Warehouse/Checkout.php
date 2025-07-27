<?php

namespace FriendsOfRedaxo\Warehouse;

use Address;
use rex_yform;

class Checkout
{

    public static function getContinueAsGuestForm(): \rex_yform
    {
        // Formular für Gast-Bestellung
        // Hier wird ein YForm-Objekt erstellt, das die Gast-Bestellung ermöglicht       
        $yform = new rex_yform();

        $yform->setObjectparams('form_action', Domain::getCurrent()->getCheckoutUrl());
        $yform->setObjectparams('form_wrap_class', 'warehouse_checkout_guest');
        $yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
        $yform->setObjectparams('form_class', 'rex-yform warehouse_checkout_guest');
        $yform->setObjectparams('form_anchor', '');
        $yform->setObjectparams('form_name', 'warehouse_checkout_guest');
        $yform->setObjectparams('real_field_names', true);

        // Ausschließlich einen Submit-Button für die Gast-Bestellung
        $yform->setValueField('submit_once', [
            'continue_as_guest',
            Warehouse::getLabel('checkout_guest_continue'),
            'btn btn-primary w-100',
            '',
            ['ycom_mode' => 'guest_only']
        ]);

        // Wenn abgesendet, dann Redirect zur Gäste-Bestellseite
        $yform->setActionField('redirect', [
            Domain::getCurrent()->getCheckoutUrl() . '?continue_as=guest'
        ]);
        return $yform;
    }

    public static function getLoginForm(): rex_yform
    {
        // Formular für Login
        $yform = new rex_yform();

        $yform->setObjectparams('form_action', Domain::getCurrent()->getCheckoutUrl());
        $yform->setObjectparams('form_wrap_class', 'warehouse_checkout_login');
        $yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
        $yform->setObjectparams('form_class', 'rex-yform warehouse_checkout_login');
        $yform->setObjectparams('form_anchor', '');
        $yform->setObjectparams('form_name', 'warehouse_checkout_login');
        $yform->setObjectparams('real_field_names', true);

        // YCom Auth Validierung für Login
        $yform->setValidateField('ycom_auth', [
            'login',
            'password',
            null,
            'Bitte geben Sie Benutzername und Passwort ein',
            'Login fehlgeschlagen. Bitte überprüfen Sie Ihre Eingaben.'
        ]);

        // Login-Feld (E-Mail oder Benutzername)
        $yform->setValueField('text', [
            'login',
            Warehouse::getLabel('checkout_login_email'),
            '',
            '',
            '{"required":"required","autocomplete":"email"}'
        ]);
        $yform->setValidateField('empty', ['login', 'Bitte geben Sie Ihre E-Mail-Adresse oder Ihren Benutzernamen ein']);

        // Passwort-Feld
        $yform->setValueField('password', [
            'password',
            Warehouse::getLabel('checkout_login_password'),
            '',
            '',
            '{"required":"required","autocomplete":"current-password"}'
        ]);
        $yform->setValidateField('empty', ['password', 'Bitte geben Sie Ihr Passwort ein']);

        // Return-To Feld für Weiterleitung nach Login
        $yform->setValueField('ycom_auth_returnto', ['returnTo']);

        // Submit-Button
        $yform->setValueField('submit_once', [
            'submit',
            Warehouse::getLabel('checkout_login_submit', 'Anmelden'),
            'btn btn-primary w-100'
        ]);

        return $yform;
    }

    /**
     * Nachdem das Gäste-Formular abgesendet wurde, werden die 
     * Angaben des Kunden in der Session zwischengespeichert, 
     * um sie beim Bezahlvorgang verfügbar zu haben.
     * @param type $params
     */
    public static function saveCustomerInSession($params)
    {
        $value_pool = $params->params['value_pool']['email'];
        $customer_session = [];
        foreach ($value_pool as $field => $value) {
            // Wenn es ein Feld mit 'to_' startet
            if (str_starts_with($field, 'to_')) {
                // Entferne 'to_' vom Feldnamen
                $new_field = substr($field, 3);
                // Speichere den Wert im Session-Array
                $customer_session[$new_field] = $value;
            } else {
                // Andernfalls speichere den Wert direkt
                $customer_session[$field] = $value;
            }
        }

        rex_set_session('user_data', $customer_session);
    }

    public static function loadCustomerFromSession(): ?Customer
    {
        $user_data = rex_session('user_data', 'array', []);
        if (empty($user_data)) {
            return null;
        }

        // Erstelle ein Customer-Objekt aus den Session-Daten
        $customer = Customer::create();
        $customer->setFirstname($user_data['firstname'] ?? '');
        $customer->setLastname($user_data['lastname'] ?? '');
        $customer->setCompany($user_data['company'] ?? '');
        $customer->setDepartment($user_data['department'] ?? '');
        $customer->setAddress($user_data['address'] ?? '');
        $customer->setZip($user_data['zip'] ?? '');
        $customer->setCity($user_data['city'] ?? '');
        $customer->setEmail($user_data['email'] ?? '');
        $customer->setPhone($user_data['phone'] ?? '');

        // Setze die Lieferadresse, falls vorhanden
        if (isset($user_data['to_name'])) {
            $shippingAddress = CustomerAddress::create();
            $shippingAddress->setName($user_data['to_name']);
            $shippingAddress->setCompany($user_data['to_company'] ?? '');
            $shippingAddress->setStreet($user_data['to_address'] ?? '');
            $shippingAddress->setZip($user_data['to_zip'] ?? '');
            $shippingAddress->setCity($user_data['to_city'] ?? '');
            // $customer->setShippingAddress($shippingAddress);
        }

        return $customer;
    }
}
