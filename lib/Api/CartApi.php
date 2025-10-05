<?php

namespace FriendsOfRedaxo\Warehouse\Api;

use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\ArticleVariant;
use FriendsOfRedaxo\Warehouse\Warehouse;
use rex;
use rex_api_function;
use rex_request;
use rex_response;

class CartApi extends rex_api_function
{
    protected $published = true;

    public function execute(): void
    {
        // index.php?rex-api-call=warehouse_cart_api&action=set&article_id=1&variant_id=1&amount=1
        if (rex_request('action', 'string') == 'set') {
            Warehouse::modifyCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null), rex_request('amount', 'int'), 'set');
        }
        // index.php?rex-api-call=warehouse_cart_api&action=add&article_id=1&variant_id=1&amount=1
        if (rex_request('action', 'string') == 'add') {
            Warehouse::addToCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null), rex_request('amount', 'int'));
        }
        // index.php?rex-api-call=warehouse_cart_api&action=modify&article_id=1&variant_id=1&amount=1&mode=add
        // index.php?rex-api-call=warehouse_cart_api&action=modify&article_id=1&variant_id=1&amount=1&mode=remove
        // index.php?rex-api-call=warehouse_cart_api&action=modify&article_id=1&variant_id=1&amount=1&mode=set
        if (rex_request('action', 'string') == 'modify') {
            Warehouse::modifyCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null), rex_request('amount', 'int'), rex_request('mode', 'string', '+'));
        }

        // index.php?rex-api-call=warehouse_cart_api&action=remove&article_id=1&variant_id=1&amount=1&mode=remove
        if (rex_request('action', 'string') == 'remove') {
            Warehouse::modifyCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null), rex_request('amount', 'int', 1), rex_request('mode', 'string', '='));
        }

        // index.php?rex-api-call=warehouse_cart_api&action=delete&article_id=1&variant_id=1
        if (rex_request('action', 'string') == 'delete') {
            Warehouse::deleteArticleFromCart(rex_request('article_id', 'int'), rex_request('variant_id', 'int', null));
        }

        // index.php?rex-api-call=warehouse_cart_api&action=empty
        if (rex_request('action', 'string') == 'empty') {
            Warehouse::emptyCart();
        }

        // Gebe den Cart als JSON zurück mit zusätzlichen Informationen
        $cart = Warehouse::getCart();
        $response = [
            'success' => true,
            'cart' => [
                'items' => $cart->getItems(),
                'last_update' => $cart->cart['last_update'] ?? time()
            ],
            'totals' => [
                'subtotal' => $cart::getSubTotal(),
                'subtotal_formatted' => $cart->getSubTotalFormatted(),
                'total' => $cart::getTotal(),
                'subtotal' => $cart->getSubTotal(),
                'subtotal_formatted' => $cart->getSubTotalFormatted(),
                'total' => $cart->getTotal(),
                'total_formatted' => Warehouse::formatCurrency($cart->getTotal()),
                'items_count' => $cart->count(),
            ],
            'items' => []
        ];

        // Füge erweiterte Item-Informationen hinzu mit Tier-Pricing
        foreach ($cart->getItems() as $item_key => $item) {
            $itemInfo = $item;
            
            if ($item['type'] === 'variant' && $item['variant_id']) {
                $variant = ArticleVariant::get($item['variant_id']);
                if ($variant) {
                    $itemInfo['bulk_prices'] = $variant->getBulkPrices();
                    $itemInfo['current_price'] = $variant->getPriceForQuantity($item['amount']) / $item['amount'];
                    $itemInfo['current_total'] = $variant->getPriceForQuantity($item['amount']);
                }
            } else {
                $article = Article::get($item['article_id']);
                if ($article) {
                    $itemInfo['bulk_prices'] = $article->getBulkPrices();
                    if ($item['amount'] > 0) {
                        $itemInfo['current_price'] = $article->getPriceForQuantity($item['amount']) / $item['amount'];
                    } else {
                        $itemInfo['current_price'] = null;
                    }
                    $itemInfo['current_total'] = $article->getPriceForQuantity($item['amount']);
                }
            }
            
            $response['items'][$item_key] = $itemInfo;
        }

        $responseJson = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        rex_response::cleanOutputBuffers();
        rex_response::setStatus(rex_response::HTTP_OK);
        rex_response::sendContent($responseJson, 'application/json');
        exit;
    }
}
