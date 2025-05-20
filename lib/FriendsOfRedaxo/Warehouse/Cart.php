<?php

namespace FriendsOfRedaxo\Warehouse;

class Cart
{
    public $cart = [];
    // initialisieren des Warenkorbs
    public static function init()
    {
        if (rex_session('warehouse_cart', 'array', null) === null) {
            rex_set_session('warehouse_cart', []);
        }
    }
    public static function countItems()
    {
        $cart = Warehouse::getCart();
        $sum_pcs = 0;
        foreach ($cart as $ci) {
            $sum_pcs += $ci['amount'];
        }
        return $sum_pcs;
    }

    // Wiege das Gewicht aller Artikel im Warenkorb
    public static function weighWeight()
    {
        $cart = Warehouse::getCart();
        $weight = 0;
        /*
        foreach ($cart as $uid => $item) {
            $warehouse_article = Article::getArticle($uid);
            $art_weight = 0;
            if ($warehouse_article && isset($warehouse_article->weight) && $warehouse_article->weight) {
                $art_weight = $warehouse_article->weight * $item['amount'];
            }
            if ($warehouse_article && isset($warehouse_article->var_weight) && (float) $warehouse_article->var_weight) {
                $art_weight = $warehouse_article->var_weight * $item['amount'];
            }
            $weight += $art_weight;
        }
        */
        return $weight;
    }

    // Berechne Gesamtsumme des Warenkorbs
    public static function calculateTotal()
    {
        $cart = Warehouse::getCart();
        $total = 0;
        foreach ($cart as $cart_item) {
            $total += $cart_item['price'] * $cart_item['amount'];
        }
        return $total;
    }
    
}
