<?php

use Alexplusde\Tracks\Structure;
use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Logger;

/* Benötigte Demo-Artikel anlegen */

if (rex_addon::get('yform')->isAvailable() && rex_addon::get('tracks')->isAvailable()) {
    // Überprüfe, ob schon ein Domain-Profil existiert
    if (Domain::query()->findOne() === null) {

        // Shop
        $shop_id = Structure::addChildCategory(0, 'Shop', 0, 0, 1);

        // Produkte
        $products_id = Structure::addChildCategory($shop_id, 'Produkte', 0, 0, 1);

        // Bestellungen
        $orders_id = Structure::addChildCategory($shop_id, 'Bestellungen', 0, 0, 1);

        // Warenkorb
        $cart_id = Structure::addChildCategory($shop_id, 'Warenkorb', 0, 0, 1);

        // Bezahlvorgang PayPal
        $checkout_id = Structure::addChildArticle($cart_id, 'Bezahlvorgang', 0, 0, 1);

        // Bezahlvorgang PayPal - Zahlung erfolgreich
        $thankyou_id = Structure::addChildArticle($cart_id, 'Zahlung erfolgreich', 0, 0, 1);

        // Bezahlvorgang PayPal - Zahlung fehlgeschlagen
        $cancel_id = Structure::addChildArticle($cart_id, 'Zahlung fehlgeschlagen', 0, 0, 1);

        /* Domain-Profil anlegen */
        $domain_profile = Domain::create();
        $domain_profile
            ->setCartArtId($cart_id)
            ->setThankyouArtId($thankyou_id)
            ->setPaymentErrorArtId($cancel_id)
            ->setOrderArtId($orders_id)
            ->setShippinginfoArtId($checkout_id)
            ->setAddressArtId($shop_id)
            ->setEmailTemplateSeller('warehouse_seller')
            ->setOrderEmail(rex::getUser()->getValue('email'));
        $domain_profile->save();

        Logger::log('install', 'Das Domain-Profil wurde erfolgreich angelegt.');

    } else {

        // Domain-Profil existiert bereits
        echo rex_view::error('Es existiert bereits ein Domain-Profil. Bitte löschen Sie dieses, um die Struktur und das Domain-Profil erneut zu initialisieren.');

    }
}
