<?php

namespace FriendsOfRedaxo\Warehouse;

class Cart
{
    public static function countPieces()
    {
        $cart = Warehouse::getCart();
        $sum_pcs = 0;
        foreach ($cart as $ci) {
            $sum_pcs += $ci['count'];
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
                $art_weight = $warehouse_article->weight * $item['count'];
            }
            if ($warehouse_article && isset($warehouse_article->var_weight) && (float) $warehouse_article->var_weight) {
                $art_weight = $warehouse_article->var_weight * $item['count'];
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
        foreach ($cart as $ci) {
            $total += $ci['price'] * $ci['count'];
        }
        return $total;
    }
    
}
