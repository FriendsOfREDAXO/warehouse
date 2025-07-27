<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_config;
use rex_extension;
use rex_extension_point;

class Payment
{

    public const PAYMENT_OPTIONS = [
        'prepayment' => 'warehouse.payment_options.prepayment',
        'invoice' => 'warehouse.payment_options.invoice',
        'paypal' => 'warehouse.payment_options.paypal',
        'direct_debit' => 'warehouse.payment_options.direct_debit'
    ];
    
    public static function getPaymentOptions() :array
    {
        $payment_options = self::PAYMENT_OPTIONS;
        // Via Extension Point eigene Zahlungsarten hinzufügen
        $payment_options = rex_extension::registerPoint(new rex_extension_point('WAREHOUSE_PAYMENT_OPTIONS', $payment_options));
        return $payment_options;
    }

    public static function getAllowedPaymentOptions() :array
    {
        $payment_options = self::getPaymentOptions();
        $allowed_payment_options = Warehouse::getConfig('warehouse', 'allowed_payment_options', '|prepayment|invoice|direct_debit|');
        // Nur die Optionen zurückgeben, die in der Konfiguration aktiviert sind
        $available_options = [];
        foreach ($payment_options as $key => $label) {
            if (strpos($allowed_payment_options, '|' . $key . '|') !== false) {
                $available_options[$key] = $label;
            }
        }
        return $available_options;
    }

}
