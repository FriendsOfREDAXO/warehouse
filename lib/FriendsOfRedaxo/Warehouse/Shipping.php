<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_config;
use rex_extension;
use rex_extension_point;

class Shipping {

    public const CALCULATION_MODE_OPTIONS = [
        '' => 'translate:warehouse.settings.shipping_calculation_mode.options.default',
        'quantity' => 'translate:warehouse.settings.shipping_calculation_mode.options.quantity',
        'weight' => 'translate:warehouse.settings.shipping_calculation_mode.options.weight',
        'order_total' => 'translate:warehouse.settings.shipping_calculation_mode.options.order_total',
    ];

    public static function getCost() :float {

        $cart = Cart::get();

        $total_weight = $cart->totalWeight();
        $total_pieces = $cart->count();
        $total_price = $cart->getTotal();

        $free_shipping_from = (float) rex_config::get('warehouse', 'free_shipping_from');
        $shipping_fee = (float) rex_config::get('warehouse', 'shipping_fee');
        $minimum_order_value = (float) rex_config::get('warehouse', 'minimum_order_value');
        $shipping_calculation_mode = (string) rex_config::get('warehouse', 'shipping_calculation_mode');

        $return = $shipping_fee;
        // Wenn Standard-Shipping-Modus ausgewählt ist, dann nur Standard-Berechnung durchführen
        if($shipping_calculation_mode == '') {
            if ($total_price >= $free_shipping_from) {
                $return = 0;
            }
        }

        if($shipping_calculation_mode == 'quantity') {
            if ($total_pieces >= $free_shipping_from) {
                $return = 0;
            }
            // TODO: Implement quantity calculation
        }

        if($shipping_calculation_mode == 'weight') {
            if ($total_weight >= $free_shipping_from) {
                $return = 0;
            }
            // TODO: Implement weight calculation
        }

        if($shipping_calculation_mode == 'order_total') {
            if ($total_price >= $free_shipping_from) {
                $return = 0;
            }
            // TODO: Implement advanced order_total calculation
        }

        return rex_extension::registerPoint(new rex_extension_point(
            'WAREHOUSE_CART_SHIPPING_COST',
            $return,
            ['cart' => $cart, 'total_weight' => $total_weight, 'total_pieces' => $total_pieces, 'total_price' => $total_price, 'free_shipping_from' => $free_shipping_from, 'shipping_fee' => $shipping_fee, 'minimum_order_value' => $minimum_order_value, 'shipping_calculation_mode' => $shipping_calculation_mode]
        ));
    }

    public static function getCostFormatted() :string {
        return Warehouse::getCurrencySign() . ' ' . number_format(self::getCost(), 2, ',', '.');
    }
    
}
