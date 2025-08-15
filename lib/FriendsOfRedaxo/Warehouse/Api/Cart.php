<?php

namespace FriendsOfRedaxo\Warehouse\Api;

use FriendsOfRedaxo\Warehouse\Warehouse;
use rex;
use rex_api_function;
use rex_response;

class Cart extends rex_api_function
{
    protected $published = true;

    public function execute(): void
    {
        // index.php?rex_api_call=cart&action=set&article_id=1&variant_id=1&amount=1
        if(rex_request('action', 'string') == 'set') {
            Warehouse::modifyCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null), rex_request('amount', 'int'), 'set');
        }
        // index.php?rex_api_call=cart&action=add&article_id=1&variant_id=1&amount=1
        if (rex_request('action', 'string') == 'add') {
            Warehouse::addToCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null), rex_request('amount', 'int'));
        }
        // index.php?rex_api_call=cart&action=modify&article_id=1&variant_id=1&amount=1&mode=add
        // index.php?rex_api_call=cart&action=modify&article_id=1&variant_id=1&amount=1&mode=remove
        // index.php?rex_api_call=cart&action=modify&article_id=1&variant_id=1&amount=1&mode=set
        if (rex_request('action', 'string') == 'modify') {
            Warehouse::modifyCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null), rex_request('amount', 'int'), rex_request('mode', 'string', '+'));
        }

        // index.php?rex_api_call=cart&action=remove&article_id=1&variant_id=1&amount=1&mode=remove
        if (rex_request('action', 'string') == 'remove') {
            Warehouse::modifyCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null), rex_request('amount', 'int', 1), rex_request('mode', 'string', '='));
        }

        // index.php?rex_api_call=cart&action=delete&article_id=1&variant_id=1
        if (rex_request('action', 'string') == 'delete') {
            Warehouse::deleteArticleFromCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null));
        }

        // index.php?rex_api_call=cart&action=empty
        if (rex_request('action', 'string') == 'empty') {
            Warehouse::emptyCart();
        }

        // Gebe den Cart als JSON zurück
        $cart = Warehouse::getCart();
        $cartJson = json_encode($cart, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if ($cartJson === false) {
            $cartJson = '{"error":"Failed to encode cart data"}';
        }

        rex_response::cleanOutputBuffers();
        rex_response::sendContent($cartJson, 'application/json');
        exit;
    }
}
