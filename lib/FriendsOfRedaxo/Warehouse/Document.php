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
        return (int)\rex_extension::registerPoint(new \rex_extension_point('WAREHOUSE_INVOICE_NUMBER', $number));
    }

    /**
     * Generiert und gibt die nächste Bestellnummer zurück (per Extension Point modifizierbar).
     * Standardformat: YYYY-MM-#### mit monatlicher Zurücksetzung
     *
     * @return string
     */
    public static function generateOrderNo(): string
    {
        // Get current date for default format
        $year = date('Y');
        $month = date('m');
        $currentPeriod = $year . '-' . $month;
        
        // Get the last period and number from config
        $lastPeriod = Warehouse::getConfig('order_no_last_period', '');
        $orderNoCounter = (int)Warehouse::getConfig('order_no_counter', 1);
        
        // Reset counter if it's a new month
        if ($lastPeriod !== $currentPeriod) {
            $orderNoCounter = 1;
            Warehouse::setConfig('order_no_last_period', $currentPeriod);
        }
        
        // Generate default order number format
        $orderNo = $currentPeriod . '-' . sprintf('%04d', $orderNoCounter);
        
        // Allow modification via extension point
        $orderNo = \rex_extension::registerPoint(new \rex_extension_point('WAREHOUSE_ORDER_NO_GENERATE', $orderNo, [
            'year' => $year,
            'month' => $month,
            'counter' => $orderNoCounter,
            'period' => $currentPeriod
        ]));
        
        // Increment and save counter for next order
        Warehouse::setConfig('order_no_counter', $orderNoCounter + 1);
        
        return (string)$orderNo;
    }

    /**
     * Assigns an order number to an order if it doesn't already have one.
     *
     * @param Order $order
     * @return void
     */
    public static function assignOrderNo(Order $order): void
    {
        if (empty($order->getOrderNo())) {
            $orderNo = self::generateOrderNo();
            $order->setOrderNo($orderNo);
        }
    }
}
