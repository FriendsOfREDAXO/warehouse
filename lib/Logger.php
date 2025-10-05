<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_log_file;
use rex_path;

class Logger
{
    public const EVENT_ADD_CART = 'add_cart'; /** @api */
    public const EVENT_REMOVE_CART = 'remove_cart'; /** @api */
    public const EVENT_EMPTY_CART = 'empty_cart'; /** @api */
    public const EVENT_UPDATE_CART = 'update_cart'; /** @api */
    public const EVENT_CHECKOUT = 'checkout'; /** @api */
    public const EVENT_START_PAYMENT = 'start_payment'; /** @api */
    public const EVENT_PAYMENT_SUCCESS = 'payment_success'; /** @api */
    public const EVENT_PAYMENT_FAILED = 'payment_failed'; /** @api */
    public const EVENT_ORDER_PLACED = 'order_placed'; /** @api */
    public const EVENT_ORDER_CANCELLED = 'order_cancelled'; /** @api */
    public const EVENT_ORDER_SHIPPED = 'order_shipped'; /** @api */
    public const EVENT_ORDER_RETURNED = 'order_returned'; /** @api */
    public const EVENT_LOGIN = 'login'; /** @api */
    public const EVENT_LOGOUT = 'logout'; /** @api */
    public const EVENT_REGISTER = 'register'; /** @api */

    public const EVENTS = [self::EVENT_ADD_CART, self::EVENT_REMOVE_CART, self::EVENT_EMPTY_CART, self::EVENT_UPDATE_CART, self::EVENT_CHECKOUT, self::EVENT_START_PAYMENT, self::EVENT_PAYMENT_SUCCESS, self::EVENT_PAYMENT_FAILED, self::EVENT_ORDER_PLACED, self::EVENT_ORDER_CANCELLED, self::EVENT_ORDER_SHIPPED, self::EVENT_ORDER_RETURNED, self::EVENT_LOGIN, self::EVENT_LOGOUT, self::EVENT_REGISTER];

    /** @var bool|null */
    private static $active;
    private static int $maxFileSize = 20000000; // 20 Mb Default

    /**
     * @param string $event
     * @param array<string|int, string|array<string, mixed>> $params
     */
    public static function log(string $event = '', string $message, null|int $order_id = null, null|int $article_id = null, null|int $article_variant_id = null, array $params = []): void
    {
        if (!self::isActive()) {
            return;
        }

        $log = rex_log_file::factory(self::logFile(), self::$maxFileSize);
        $data = [
            $event,
            $message,
            $order_id ?? '',
            $article_id ?? '',
            $article_variant_id ?? '',
            (string) json_encode($params),
        ];
        $log->add($data);
    }
    
    public static function activate(): void
    {
        $addon = rex_addon::get('warehouse');
        if ($addon->isAvailable()) {
            $addon->setConfig('log', 1);
            self::$active = true;
        }
    }

    public static function deactivate(): void
    {
        $addon = rex_addon::get('warehouse');
        if ($addon->isAvailable()) {
            $addon->setConfig('log', 0);
            self::$active = false;
        }
    }

    public static function isActive(): bool
    {
        if (null === self::$active) {
            $addon = rex_addon::get('warehouse');
            if ($addon->isAvailable()) {
                self::$active = (1 === $addon->getConfig('log')) ? true : false;
            } else {
                self::$active = false;
            }
        }
        return (self::$active) ? true : false;
    }

    public static function logFolder(): string
    {
        return rex_path::addonData('warehouse');
    }

    public static function logFile(): string
    {
        return rex_path::log('warehouse.log');
    }

    public static function delete(): bool
    {
        return rex_log_file::delete(self::logFile());
    }

}
