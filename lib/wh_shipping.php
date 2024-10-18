<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of warehouse_shipping
 *
 * @author wolfgang
 */
class warehouse_shipping {
    
    public static function get_cost() {

        $cart = warehouse::get_cart();



        // prüft, ob überhaupt Artikel mit Fracht im Warenkorb liegen.
        $free_shipping = true;
        foreach ($cart as $ci) {
            if (!$ci['free_shipping']) {
                $free_shipping = false;
                break;
            }
        }
        if ($free_shipping) {
            return 0;
        }

        if (rex_config::get('warehouse','shipping_mode') == 'pieces') {
            // Nach Stück
            $sum_pcs = 0;
            foreach ($cart as $ci) {
                if ((int) $ci['free_shipping'] < 1) {
                    $sum_pcs += $ci['count'];
                }
            }
            if (($shipping = self::check_val($sum_pcs)) !== false) {
                return $shipping;
            }
        } elseif (rex_config::get('warehouse','shipping_mode') == 'weight') {
            // Nach Gewicht (Alle weight zusammengezählt)
            $weight = 0;
            foreach ($cart as $uid=>$item) {
                $warehouse_article = warehouse_articles::get_article($uid);
//                dump($warehouse_article);
                $art_weight = 0;
                if ($warehouse_article && isset($warehouse_article->weight) && $warehouse_article->weight) {
                    $art_weight = $warehouse_article->weight * $item['count'];
                }
                if ($warehouse_article && isset($warehouse_article->var_weight) && (float) $warehouse_article->var_weight) {
                    $art_weight = $warehouse_article->var_weight * $item['count'];
                }
                $weight += $art_weight;
            }
            $shipping = self::check_val($weight);
            if ($shipping !== false) {
                return $shipping;
            }            

        } elseif (rex_config::get('warehouse','shipping_mode') == 'order_total') {
            $sum_brutto = warehouse::get_sub_total();
            $shipping = self::check_val($sum_brutto);
            if ($shipping !== false) {
                return $shipping;
            }            
        }
        return rex_config::get('warehouse', 'shipping');
    }
    
    /**
     * Führt den Vergleich auf Basis der in den Settings gesetzten json Parameter durch
     * 
     * @param type $check_val
     * @return boolean
     */
    private static function check_val($check_val) {
        $shipping_params = json_decode(rex_config::get('warehouse','shipping_parameters'));
        $stop = false;
        foreach ($shipping_params as $param) {
            switch ($param[0]) {
                case '>':
                    if ((float) $check_val > (float) $param[1]) {
                        return $param[2];
                        $stop = true;
                    }
                    break;
                case '>=':
                    if ((float) $check_val >= (float) $param[1]) {
                        return $param[2];
                        $stop = true;
                    }
                    break;
                case '<':
                    if ((float) $check_val < (float) $param[1]) {
                        return $param[2];
                        $stop = true;
                    }
                    break;
                case '<=':
                    if ((float) $check_val <= (float) $param[1]) {
                        return $param[2];
                        $stop = true;
                    }
                    break;
                case '=':
                    if ((float) $check_val == (float) $param[1]) {
                        return $param[2];
                        $stop = true;
                    }
                    break;
            }
            if ($stop) {
                break;
            }
        }
        return false;        
    }
    
    
}
