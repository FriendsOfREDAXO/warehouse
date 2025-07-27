<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_extension;
use rex_extension_point;

/**
 * Document class for handling warehouse documents such as invoice, delivery note, order confirmation, etc.
 * @package FriendsOfRedaxo\Warehouse
 */
class Document
{
    /**
     * Gibt die aktuelle Bestellnummer zurück (per Extension Point modifizierbar).
     *
     * @return int
     */
    public static function getOrderNumber(): int
    {
        $number = (int)Warehouse::getConfig('order_number', 1);
        return (int)\rex_extension::registerPoint(new \rex_extension_point('WAREHOUSE_ORDER_NUMBER', $number));
    }
    
    /**
     * Gibt die aktuelle Lieferscheinnummer zurück (per Extension Point modifizierbar).
     *
     * @return int
     */
    public static function getDeliveryNoteNumber(): int
    {
        $number = (int)Warehouse::getConfig('delivery_note_number', 1);
        return (int) rex_extension::registerPoint(new rex_extension_point('WAREHOUSE_DELIVERY_NOTE_NUMBER', $number));
    }

    /**
     * Gibt die aktuelle Rechnungsnummer zurück (per Extension Point modifizierbar).
     *
     * @return int
     */
    public static function getInvoiceNumber(): int
    {
        $number = (int)Warehouse::getConfig('invoice_number', 1);
        return (int)\rex_extension::registerPoint(new rex_extension_point('WAREHOUSE_INVOICE_NUMBER', $number));
    }
}
