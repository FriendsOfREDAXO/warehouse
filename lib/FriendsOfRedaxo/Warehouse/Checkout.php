<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_yform;

class Checkout {

    public static function getContinueAsGuestForm() : \rex_yform
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

        // Ausschließlich einen Submit-Button für die Gast-Bestellung
        $yform->setValueField('submit_once', [
            'continue_as_guest',
            Warehouse::getLabel('checkout_guest_continue'),
            'btn btn-primary w-100',
            '',
            ['ycom_mode' => 'guest_only']
        ]);
        return $yform;
    }

    public static function getLoginForm() : rex_yform
    {
        // Formular für Login
        $yform = new rex_yform();
        
        $yform->setObjectparams('form_action', Domain::getCurrent()->getCheckoutUrl());
        $yform->setObjectparams('form_wrap_class', 'warehouse_checkout_login');
        $yform->setObjectparams('form_ytemplate', 'bootstrap5,bootstrap');
        $yform->setObjectparams('form_class', 'rex-yform warehouse_checkout_login');
        $yform->setObjectparams('form_anchor', '');
        $yform->setObjectparams('form_name', 'warehouse_checkout_login');

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
}
