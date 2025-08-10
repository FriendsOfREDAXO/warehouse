<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_config;
use rex_extension;
use rex_extension_point;
use rex_i18n;
use rex_ycom_auth;

class Cart
{
    public $cart = [
        'items' => [],
        'customer' => null,
        'billing_address' => null,
        'delivery_address' => null,
        'last_update' => 0
    ];

    // initialisieren des Warenkorbs
    public function __construct()
    {
        $this->init();
        
        // Auto-detect customer from YCom if available and not already set
        if ((!isset($this->cart['customer']) || !$this->cart['customer']) && 
            (!isset($this->cart['customer_data']) || empty($this->cart['customer_data']))) {
            $this->autoSetCustomerFromYCom();
        }
    }

    public function init()
    {
        if (Warehouse::isDemoMode()) {
            // Setze Demo-Warenkorb
            $this->setDemoCart();
            return;
        }
        
        $session_cart = rex_session('warehouse_cart', 'array', null);
        if ($session_cart === null) {
            rex_set_session('warehouse_cart', $this->cart);
        } else {
            // Merge with default structure to ensure all fields exist
            $this->cart = array_merge($this->cart, $session_cart);
        }
    }

    public static function loadCartFromSession(): self
    {
        $cart = new self();
        $session_cart = rex_session('warehouse_cart', 'array', []);
        if (empty($session_cart)) {
            // Wenn der Warenkorb leer ist, initialisiere ihn
            $cart->init();
        } else {
            // Merge with default structure
            $cart->cart = array_merge($cart->cart, $session_cart);
        }
        return $cart;
    }
    public function saveCartToSession(): void
    {
        rex_set_session('warehouse_cart', $this->cart);
    }

    public static function get(): Cart
    {
        return new self();
    }

    public function getItems() :array
    {
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
        foreach ($cart->getItems() as $item) {
            if ($item['type'] === 'variant' && $item['variant_id']) {
                $variant = ArticleVariant::get($item['variant_id']);
                if ($variant) {
                    $article = $variant->getArticle();
                    if ($article) {
                        $weight += $article->getWeight() * $item['amount'];
                    }
                }
            } else {
                $article = Article::get($item['article_id']);
                if ($article) {
                    $weight += $article->getWeight() * $item['amount'];
                }
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
            $total += $item['total'];
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

    /**
     * Set customer data for the cart
     */
    public function setCustomer(?Customer $customer): void
    {
        $this->cart['customer'] = $customer ? $customer->getId() : null;
        rex_set_session('warehouse_cart', $this->cart);
    }

    /**
     * Get customer from cart
     */
    public function getCustomer(): ?Customer
    {
        if (!$this->cart['customer']) {
            return null;
        }
        return Customer::get($this->cart['customer']);
    }

    /**
     * Set billing address for the cart
     */
    public function setBillingAddress(?CustomerAddress $address): void
    {
        $this->cart['billing_address'] = $address ? $address->getId() : null;
        rex_set_session('warehouse_cart', $this->cart);
    }

    /**
     * Get billing address from cart
     */
    public function getBillingAddress(): ?CustomerAddress
    {
        if (!$this->cart['billing_address']) {
            return null;
        }
        return CustomerAddress::get($this->cart['billing_address']);
    }

    /**
     * Set delivery address for the cart (if different from billing)
     */
    public function setDeliveryAddress(?CustomerAddress $address): void
    {
        $this->cart['delivery_address'] = $address ? $address->getId() : null;
        rex_set_session('warehouse_cart', $this->cart);
    }

    /**
     * Get delivery address from cart
     */
    public function getDeliveryAddress(): ?CustomerAddress
    {
        if (!$this->cart['delivery_address']) {
            return $this->getBillingAddress(); // Fall back to billing address
        }
        return CustomerAddress::get($this->cart['delivery_address']);
    }

    /**
     * Check if delivery address is different from billing address
     */
    public function hasSeperateDeliveryAddress(): bool
    {
        return $this->cart['delivery_address'] !== null && 
               $this->cart['delivery_address'] !== $this->cart['billing_address'];
    }

    /**
     * Set customer data from array (for guest checkout)
     */
    public function setCustomerData(array $customer_data): void
    {
        $this->cart['customer_data'] = $customer_data;
        rex_set_session('warehouse_cart', $this->cart);
    }

    /**
     * Get customer data array (for guest checkout)
     */
    public function getCustomerData(): array
    {
        return $this->cart['customer_data'] ?? [];
    }

    /**
     * Auto-set customer from YCom if available
     */
    public function autoSetCustomerFromYCom(): void
    {
        if (class_exists('rex_addon') && rex_addon::get('ycom')->isAvailable()) {
            $ycom_user = rex_ycom_auth::getUser();
            if ($ycom_user) {
                // Try to find corresponding customer
                $customer = Customer::query()->where('email', $ycom_user->getValue('email'))->findOne();
                if ($customer) {
                    $this->setCustomer($customer);
                    
                    // Also set billing address if available
                    $billing_address = CustomerAddress::query()
                        ->where('ycom_user_id', $ycom_user->getId())
                        ->where('type', 'billing')
                        ->findOne();
                    if ($billing_address) {
                        $this->setBillingAddress($billing_address);
                    }
                }
            }
        }
    }

    /**
     * Clear customer and address data
     */
    public function clearCustomerData(): void
    {
        $this->cart['customer'] = null;
        $this->cart['customer_data'] = [];
        $this->cart['billing_address'] = null;
        $this->cart['delivery_address'] = null;
        rex_set_session('warehouse_cart', $this->cart);
    }


    public function modify(int $article_id, int $article_variant_id = null, int|false $quantity = false, string $mode = '='): void
    {
        $item_key = $article_id . ($article_variant_id ? '_' . $article_variant_id : '');
        $items = $this->getItems();
        
        if (!isset($items[$item_key])) {
            return; // Item not in cart
        }

        if ($quantity === false) {
            // Remove item completely
            unset($items[$item_key]);
        } else {
            // mode = "=" => "set", "+" => "add", "-" => "remove"
            if ($mode === '=' || $mode === 'set') {
                $items[$item_key]['amount'] = $quantity;
            } elseif ($mode === '+' || $mode === 'add') {
                $items[$item_key]['amount'] += $quantity;
            } elseif ($mode === '-' || $mode === 'remove') {
                $items[$item_key]['amount'] -= $quantity;
            }
            
            // Check if quantity is valid, no empty quantity - remove article from cart
            if ($items[$item_key]['amount'] <= 0) {
                unset($items[$item_key]);
            } else {
                // Recalculate total
                $items[$item_key]['total'] = $items[$item_key]['price'] * $items[$item_key]['amount'];
            }
        }
        
        $this->update($items);
    }

    public function remove(int $article_id, int $variant_id = null): void
    {
        $this->modify($article_id, $variant_id, false);
    }
    public function add(int $article_id, int $article_variant_id = null, int $quantity = 1): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $article = Article::get($article_id);
        if (!$article) {
            return false;
        }

        $article_variant = null;
        if ($article_variant_id > 0) {
            $article_variant = ArticleVariant::get($article_variant_id);
            if (!$article_variant || $article_variant->getArticle()->getId() !== $article_id) {
                return false;
            }
        }

        // Create unique key for this cart item
        $item_key = $article_id . ($article_variant_id ? '_' . $article_variant_id : '');
        
        $items = $this->getItems();
        
        // Check if item already exists in cart
        if (isset($items[$item_key])) {
            // Update quantity
            $items[$item_key]['amount'] += $quantity;
        } else {
            // Add new item with current price
            $price = $article_variant ? $article_variant->getPrice() : $article->getPrice();
            $name = $article_variant ? $article_variant->getName() : $article->getName();
            
            $items[$item_key] = [
                'type' => $article_variant ? 'variant' : 'article',
                'article_id' => $article_id,
                'variant_id' => $article_variant_id,
                'name' => $name,
                'price' => $price,
                'amount' => $quantity,
                'total' => $price * $quantity,
                'added_at' => time()
            ];
        }

        // Recalculate total for existing items
        $items[$item_key]['total'] = $items[$item_key]['price'] * $items[$item_key]['amount'];

        $this->update($items);
        return true;
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

    public static function getCartTotalFormatted(): string
    {
        return Warehouse::getCurrencySign() . ' ' . number_format(self::getCartTotal(), 2, ',', '');
    }


    /*
    Warenkorbrabatt
     */
    public static function getDiscountValue()
    {
        return 0;
    }

    public static function getDiscountValueFormatted(): string
    {
        $discount = self::getDiscountValue();
        if ($discount > 0) {
            return Warehouse::getCurrencySign() . ' ' . number_format($discount, 2, ',', '');
        }
        return '';
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

    public static function getSubTotalFormatted(): string
    {
        return Warehouse::getCurrencySign() . ' ' . number_format(self::getSubTotal(), 2, ',', '');
    }

    public static function getSubTotalNetto()
    {
        return self::getSubTotalByMode('net');
    }

    public static function getTaxTotal()
    {
        return self::getTaxTotalByMode();
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
        $user_data = rex_session('user_data', 'array', []);

        // Get customer data from cart
        $customer_data = $cart->getCustomerData();
        $customer = $cart->getCustomer();
        $billing_address = $cart->getBillingAddress();
        $delivery_address = $cart->getDeliveryAddress();

        $order->setOrderTotal(self::getCartTotal());
        $order->setPaymentId($payment_id);
        $order->setPaymentType($user_data['payment_type'] ?? '');
        $order->setPaymentConfirm($user_data['payment_confirm'] ?? '');
        $order->setOrderJson(json_encode([
            'cart' => $cart->cart,
            'user_data' => $user_data,
            'customer_data' => $customer_data,
            'billing_address' => $billing_address?->getData(),
            'delivery_address' => $delivery_address?->getData()
        ]));

        $order->setCreateDate(date('Y-m-d H:i:s'));
        $order->setOrderText(Warehouse::getOrderAsText());
        
        // Use customer data from cart or fallback to user_data
        $order->setFirstname($customer_data['firstname'] ?? $user_data['firstname'] ?? '');
        $order->setLastname($customer_data['lastname'] ?? $user_data['lastname'] ?? '');
        $order->setAddress($customer_data['address'] ?? $user_data['address'] ?? '');
        $order->setZip($customer_data['zip'] ?? $user_data['zip'] ?? '');
        $order->setCity($customer_data['city'] ?? $user_data['city'] ?? '');
        $order->setEmail($customer_data['email'] ?? $user_data['email'] ?? '');

        // Set customer reference if available
        if ($customer) {
            $order->setValue('customer_id', $customer->getId());
        }

        if (class_exists('rex_addon') && rex_addon::get('ycom')->isAvailable()) {
            $ycom_user = rex_ycom_auth::getUser();
            if ($ycom_user) {
                $order->setValue('ycom_user_id', $ycom_user->getId());
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
        $cart = self::get();
        
        // Überprüfe, ob Warenkorb leer ist
        if ($cart->isEmpty()) {
            return rex_i18n::msg('warehouse.cart_empty');
        }
        
        // Überprüfe, ob Mindestbestellwert erreicht ist
        $minimum_order_value = (float) Warehouse::getConfig('minimum_order_value');
        if (self::getTotal() < $minimum_order_value) {
            return rex_i18n::msg('warehouse.cart_minimum_order_value', $minimum_order_value);
        }
        
        // Überprüfe, ob alle Artikel noch bestellbar sind
        $items = $cart->getItems();
        foreach ($items as $item_key => $item) {
            if ($item['type'] === 'variant' && $item['variant_id']) {
                $variant = ArticleVariant::get($item['variant_id']);
                if (!$variant) {
                    return rex_i18n::msg('warehouse.cart_item_not_available', $item['name']);
                }
                
                $article = $variant->getArticle();
                if (!$article) {
                    return rex_i18n::msg('warehouse.cart_item_not_available', $item['name']);
                }
                
                // Check if variant is still available
                if (!in_array($variant->getValue('availability'), ArticleVariant::AVAILABLE)) {
                    return rex_i18n::msg('warehouse.cart_item_no_longer_available', $item['name']);
                }
            } else {
                $article = Article::get($item['article_id']);
                if (!$article) {
                    return rex_i18n::msg('warehouse.cart_item_not_available', $item['name']);
                }
                
                // Check if article is still available
                if (!in_array($article->getValue('availability'), Article::AVAILABLE)) {
                    return rex_i18n::msg('warehouse.cart_item_no_longer_available', $item['name']);
                }
            }
        }

        // Extension Point für weitere Validierungen
        $validation_result = rex_extension::registerPoint(new rex_extension_point('WAREHOUSE_CART_VALIDATE', true, [
            'cart' => $cart,
            'items' => $items
        ]));

        if (is_string($validation_result)) {
            // Wenn ein String zurückgegeben wird, ist eine Fehlermeldung vorhanden
            return $validation_result;
        }
        
        // Wenn keine Validierungsfehler gefunden wurden, gib true zurück
        return true;
    }

    public function setDemoCart()
    {
        $demo_items = [
            '123' => [
                'type' => 'article',
                'article_id' => 123,
                'variant_id' => null,
                'name' => 'Artikelname',
                'price' => 19.99,
                'amount' => 2,
                'total' => 39.98,
                'added_at' => time()
            ],
            '456_1' => [
                'type' => 'variant',
                'article_id' => 456,
                'variant_id' => 1,
                'name' => 'Anderer Artikel - Variante 1',
                'price' => 29.99,
                'amount' => 1,
                'total' => 29.99,
                'added_at' => time()
            ],
            '456_2' => [
                'type' => 'variant',
                'article_id' => 456,
                'variant_id' => 2,
                'name' => 'Anderer Artikel - Variante 2',
                'price' => 34.99,
                'amount' => 1,
                'total' => 34.99,
                'added_at' => time()
            ]
        ];
        $this->update($demo_items);
    }

    /**
     * Gibt die Zwischensumme (Summe aller Artikel) im gewünschten Modus zurück.
     * @param string|null $mode 'net' oder 'gross' (optional, sonst globaler Modus)
     * @return float
     */
    public static function getSubTotalByMode(string $mode = null): float
    {
        $cart = self::get();
        $items = $cart->getItems();
        $sum = 0;
        
        foreach ($items as $item) {
            if ($item['type'] === 'variant' && $item['variant_id']) {
                $variant = ArticleVariant::get($item['variant_id']);
                if ($variant) {
                    $price = $variant->getPrice($mode);
                    $sum += (float)$price * (int)$item['amount'];
                }
            } else {
                $article = Article::get($item['article_id']);
                if ($article) {
                    $price = $article->getPrice($mode);
                    $sum += (float)$price * (int)$item['amount'];
                }
            }
        }
        return $sum;
    }

    /**
     * Gibt die Zwischensumme formatiert zurück.
     */
    public static function getSubTotalByModeFormatted(string $mode = null): string
    {
        return Warehouse::getCurrencySign() . ' ' . number_format(self::getSubTotalByMode($mode), 2, ',', '');
    }

    /**
     * Gibt die Gesamtsumme (inkl. Versand, Rabatt) im gewünschten Modus zurück.
     */
    public static function getCartTotalByMode(?string $mode = null): float
    {
        $sum = (float) self::getSubTotalByMode($mode);
        $shippingCost = (float) Shipping::getCost();
        $discount = (float) self::getDiscountValue();
        
        // Ensure values are non-negative
        $sum += max(0, $shippingCost);
        $sum -= max(0, $discount);
        
        return $sum;
    }

    public static function getCartTotalByModeFormatted(string $mode = null): string
    {
        return Warehouse::getCurrencySign() . ' ' . number_format(self::getCartTotalByMode($mode), 2, ',', '');
    }

    /**
     * Gibt die gesamte Steuer im Warenkorb zurück (Summe aller Einzelsteuern).
     * @return float
     */
    public static function getTaxTotalByMode(): float
    {
        $cart = self::get();
        $items = $cart->getItems();
        $sum = 0;
        
        foreach ($items as $item) {
            if (!isset($item['article_id'], $item['amount'])) {
                continue;
            }
            
            if ($item['type'] === 'variant' && $item['variant_id']) {
                $variant = ArticleVariant::get($item['variant_id']);
                if (!$variant) {
                    continue;
                }
                $net = $variant->getPrice('net');
                $gross = $variant->getPrice('gross');
                $sum += (($gross - $net) * (int)$item['amount']);
            } else {
                $article = Article::get($item['article_id']);
                if (!$article) {
                    continue;
                }
                $net = $article->getPrice('net');
                $gross = $article->getPrice('gross');
                $sum += (($gross - $net) * (int)$item['amount']);
            }
        }
        return round($sum, 2);
    }
}
