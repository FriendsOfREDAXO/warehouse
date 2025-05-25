<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_extension;
use rex_extension_point;
use rex_i18n;
use rex_ycom_auth;

class Cart
{
    public $cart = [
        'items' => [], 
        'address' => [],
        'last_update' => 0
    ];

    // initialisieren des Warenkorbs
    public function __construct()
    {
        self::init();
    }

    public function init()
    {
        if(Warehouse::isDemoMode()) {
            // Setze Demo-Warenkorb
            $this->setDemoCart();
            return;
        }
        if (rex_session('warehouse_cart', 'array', null) === null) {
            rex_set_session('warehouse_cart', []);
        }
        $this->cart = rex_session('warehouse_cart', 'array');
    }


    public static function get(): Cart
    {
        return new self();
    }

    public function getItems() :array {
        return $this->cart['items'] ?? [];
    }

    public function isEmpty(): bool
    {
        return empty($this->getItems());
    }

    public function count(): int
    {
        $totalPieces = 0;
        foreach ($this->getItems() as $uuid => $item) {
            $totalPieces += (int) $item['amount'];
        }
        return $totalPieces;
    }

    // Wiege das Gewicht aller Artikel im Warenkorb
    public static function totalWeight()
    {
        $weight = 0;
        if (!Warehouse::isWeightEnabled()) {
            return $weight;
        }

        $cart = Cart::get();
        foreach ($cart->getItems() as $uuid => $item) {
            $article = Article::getByUuid($uuid);
            if ($article) {
                $weight += $article->getWeight() * $item['amount'];
            }
        }

        return $weight;
    }

    public function totalShippingCosts()
    {
        $shipping = Shipping::getCost();
        if ($shipping > 0) {
            return $shipping;
        }
        return 0;
    }

    // Berechne Gesamtsumme des Warenkorbs
    public static function getTotal()
    {
        $cart = self::get();
        $total = 0;
        foreach ($cart->getItems() as $item) {
            $total += $item['price'] * $item['amount'];
        }
        return $total;
    }

    /**
     * Warenkorb aktualisieren (Preise, Steuern, Gesamtsumme)
     * @return void
     */
    public function update($items): void
    {
        $this->cart['items'] = $items;
        $this->cart['last_update'] = time();
        rex_set_session('warehouse_cart', $this->cart);
    }


    public function modify(int $article_id, int $article_variant_id, int|false $quantity, string $mode = '='): void
    {
        $items = $this->getItems();
        if ($quantity === false) {
            unset($items[$article_id]);
        }
        // mode = "=" => "set", "+" => "add", "-" => "remove"
        if (($mode == '=' || $mode == 'set') && $quantity > 0) {
            $items[$article_id][$article_variant_id]['amount'] = $quantity;
        } elseif ($mode == '+' || $mode == 'add') {
            $items[$article_id][$article_variant_id]['amount'] += $quantity;
        } elseif ($mode == '-' || $mode == 'remove') {
            $items[$article_id][$article_variant_id]['amount'] -= $quantity;
        }
        // Check if quantity is valid, no empty quantity - remove article from cart
        if ($items[$article_id][$article_variant_id]['amount'] <= 0) {
            unset($items[$article_id][$article_variant_id]);
        }
        rex_set_session('warehouse_cart', $items);
        self::update($items);
    }

    public function remove(int $article_id, int $variant_id): void
    {
        self::modify($article_id, $variant_id, false);
    }
    public function add(int $article_id, int $article_variant_id = null, int $quantity = 1): bool
    {
        $added = false;
        if ($article_variant_id > 0) {
            $article_variant = ArticleVariant::get($article_variant_id);
            $article = $article_variant->getArticle();
        } else {
            $article = Article::get($article_id);
        }

        $items = $this->getItems();
        if ($quantity >= 1) {
            $items[$article->getId()]['amount'] += $quantity;
            $added = true;
        }

        rex_set_session('warehouse_cart', $items);
        self::update($items);

        // TODO: Aktion nach hinzufügen zum Warenkorb - entweder direkt zum Checkout, zum Warekorb oder auf der Artikelseite bleiben
        /*
        if (rex_request('art_type', 'string') == 'warehouse_single' || (rex_config::get('warehouse', 'cart_mode') == 'page' && rex_request('article_id', 'int'))) {
            rex_redirect(rex_request('article_id'), '', ['showcart' => 1]);
        } else {
            self::redirect_from_cart($added, 1);
        }
        */
        return $added;
    }
    /**
     * Total (Warenkorb mit Shipping)
     * @return float
     */
    public static function getCartTotal(): float
    {
        $sum = (float) self::getSubTotal();
        $sum += (float) Shipping::getCost();
        $sum -= (float) self::getDiscountValue();
        return $sum;
    }


    /*
    Warenkorbrabatt
     */
    public static function getDiscountValue()
    {
        return 0;
    }

    /**
     * Sub Total (Warenkorb ohne Versandkosten)
     * @return float
     */
    public static function getSubTotal(): float
    {
        $cart = self::get();
        $items = $cart->getItems();
        $sum = 0;
        foreach ($items as $item) {
            $sum += (float) $item['total'];
        }
        return $sum;
    }

    public static function getSubTotalNetto()
    {
        $cart = self::get();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['price_netto'] * $item['amount'];
        }
        return round($sum, 2);
    }

    public static function getTaxTotal()
    {
        $cart = self::get();
        $sum = 0;
        foreach ($cart as $item) {
            $sum += $item['taxval'];
        }
        return round($sum, 2);
    }


    public static function getCartNetto()
    {
        return self::getSubTotalNetto();
    }
    public static function saveAsOrder(string $payment_id = ''): bool
    {
        $order = Order::create();
        $cart = self::get();

        $shipping = Shipping::getCost();
        $user_data = rex_session('user_data', 'array');

        $order->setOrderTotal(self::getTotal());
        $order->setPaymentId($payment_id);
        $order->setPaymentType($user_data['payment_type']);
        $order->setPaymentConfirm($user_data['payment_confirm']);
        $order->setOrderJson(json_encode([
            'cart' => $cart,
            'user_data' => $user_data
        ]));

        $order->setCreateDate(date('Y-m-d H:i:s'));
        $order->setOrderText(Warehouse::getOrderAsText());
        $order->setFirstname($user_data['firstname']);
        $order->setLastname($user_data['lastname']);
        $order->setAddress($user_data['address'] ?? '');
        $order->setZip($user_data['zip'] ?? '');
        $order->setCity($user_data['city'] ?? '');
        $order->setEmail($user_data['email']);

        if (rex_addon::get('ycom')->isAvailable()) {
            $ycom_user = rex_ycom_auth::getUser();
            if ($ycom_user) {
                $values['ycom_user_id'] = $ycom_user->getId();
            }
        }

        return $order->save();
    }


    public static function empty()
    {
        rex_unset_session('warehouse_cart');
    }

    public static function getTax()
    {
        // TODO: Tax pro Warenkorb-Inhalt berechnen
        return 0;
    }

    public static function validateCart(): bool|string
    {
        // Überprüfe, ob Mindestbestellwert erreicht ist
        $cart = self::get();
        $minimum_order_value = (float) Warehouse::getConfig('minimum_order_value');
        if (self::getTotal() < $minimum_order_value) {
            return rex_i18n::msg('warehouse.cart_minimum_order_value', $minimum_order_value);
        }
        // Überprüfe, ob alle Artikel noch bestellbar sind

        // Extension Point für weitere Validierungen
        $cart = rex_extension::registerPoint(new rex_extension_point('WAREHOUSE_CART_VALIDATE', '', [
            'cart' => $cart
        ]));

        if (is_string($cart)) {
            // Wenn ein String zurückgegeben wird, ist eine Fehlermeldung vorhanden
            return $cart;
        }
        // Wenn keine Validierungsfehler gefunden wurden, gib true zurück
        return true;
    }

    public function setDemoCart()
    {

        $demo_items = [
            'uuid1' => [
                'type' => 'article',
                'id' => '123',
                'name' => 'Artikelname',
                'price' => 19.99,
                'amount' => 2,
                'total' => 39.98,
                'image' => 'image.jpg',
                'cat_name' => 'Kategorie'
            ],
            'uuid2' => [
                'type' => 'article',
                'id' => '456',
                'name' => 'Anderer Artikel mit Varianten',
                'total' => 64.98,
                'price' => 19.99,
                'amount' => 2,
                'image' => 'image2.jpg',
                'cat_name' => 'Andere Kategorie',
                'variants' => [
                    'variant1' => [
                        'type' => 'variant',
                        'id' => '456-1',
                        'name' => 'Variante 1',
                        'price' => 29.99,
                        'amount' => 1,
                        'total' => 29.99,
                        'image' => 'variant1.jpg',
                    ],
                    'variant2' => [
                        'type' => 'variant',
                        'id' => '456-2',
                        'name' => 'Variante 2',
                        'price' => 34.99,
                        'amount' => 1,
                        'total' => 34.99,
                        'image' => 'variant2.jpg',
                    ]
                ]
            ],
        ];
        self::update($demo_items);
    }
}
