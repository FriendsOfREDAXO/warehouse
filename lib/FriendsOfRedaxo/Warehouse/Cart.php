<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_config;
use rex_extension;
use rex_extension_point;
use rex_i18n;
use rex_ycom_auth;
use rex_ycom_user;

class Cart
{
    /**
     * Warenkorb-Datenstruktur
     * @var array<string, mixed>
     */
    public $cart = [
        'items' => [],
        'last_update' => 0
    ];

    // initialisieren des Warenkorbs
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {        
        $sessionCart = Session::getCartData();
        if ($sessionCart === null) {
            Session::setCart($this->cart);
        } else {
            // Merge with default structure to ensure all fields exist
            $this->cart = array_merge($this->cart, $sessionCart);
        }
    }

    /**
     * Speichert den Warenkorb in der Session
     */
    public function saveToSession(): void
    {
        Session::setCart($this->cart);
    }

    /**
     * Erzeugt einen neuen Warenkorb
     *
     * @return Cart
     */
    public static function create(): Cart
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

        $cart = Cart::create();
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
        $cart = self::create();
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
        Session::setCart($this->cart);
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
                // Recalculate total and tax amount
                $items[$item_key]['total'] = $items[$item_key]['price'] * $items[$item_key]['amount'];
                if (isset($items[$item_key]['gross_price'], $items[$item_key]['net_price'])) {
                    $items[$item_key]['tax_amount'] = ($items[$item_key]['gross_price'] - $items[$item_key]['net_price']) * $items[$item_key]['amount'];
                }
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
            // Get the item object (article or variant)
            $item_object = $article_variant ?: $article;
            
            // Calculate prices and tax
            $net_price = $item_object->getPrice('net');
            $gross_price = $item_object->getPrice('gross');
            $tax_rate = $article_variant ? $article_variant->getTax() : $article->getTax();
            $tax_rate = $tax_rate !== null ? (float)$tax_rate : 0.0;
            
            // Use current global price mode to determine display price
            $current_mode = Warehouse::getPriceInputMode();
            $price = $current_mode === 'net' ? $net_price : $gross_price;
            
            $name = $article_variant ? $article_variant->getName() : $article->getName();
            $sku = $article_variant ? $article_variant->getSku() : $article->getSku();
            
            // Get image filename for this item (variant takes precedence over article)
            $image_filename = null;
            if ($article_variant && $article_variant->getImage()) {
                $image_filename = $article_variant->getImage();
            } elseif ($article && $article->getImage()) {
                $image_filename = $article->getImage();
            }
            
            // Generate image URL if image exists
            $image_url = null;
            if ($image_filename) {
                $domain_url = Domain::getCurrentUrl();
                if ($domain_url) {
                    $image_url = rtrim($domain_url, '/') . '/media/rex_media_small/' . $image_filename;
                }
            }
            
            $items[$item_key] = [
                'type' => $article_variant ? 'variant' : 'article',
                'article_id' => $article_id,
                'variant_id' => $article_variant_id,
                'name' => $name,
                'sku' => $sku,
                'price' => $price,
                'net_price' => $net_price,
                'gross_price' => $gross_price,
                'tax_rate' => $tax_rate,
                'tax_amount' => ($gross_price - $net_price) * $quantity,
                'amount' => $quantity,
                'total' => $price * $quantity,
                'added_at' => time(),
                'image' => $image_url
            ];
        }

        // Recalculate totals for existing items
        $items[$item_key]['total'] = $items[$item_key]['price'] * $items[$item_key]['amount'];
        $gross_price = $items[$item_key]['gross_price'] ?? 0.0;
        $net_price = $items[$item_key]['net_price'] ?? 0.0;
        $items[$item_key]['tax_amount'] = ($gross_price - $net_price) * $items[$item_key]['amount'];

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
        return Warehouse::formatCurrency(self::getCartTotal());
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
            return Warehouse::formatCurrency($discount);
        }
        return '';
    }

    /**
     * Sub Total (Warenkorb ohne Versandkosten)
     * @return float
     */
    public static function getSubTotal(): float
    {
        $cart = self::create();
        $items = $cart->getItems();
        $sum = 0;
        foreach ($items as $item) {
            $sum += (float) $item['total'];
        }
        return $sum;
    }

    public static function getSubTotalFormatted(): string
    {
        return Warehouse::formatCurrency(self::getSubTotal());
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


    public static function getTax()
    {
        return self::getTaxTotalByMode();
    }

    public static function validateCart(): bool|string
    {
        $cart = self::create();
        
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

    /**
     * Gibt die Zwischensumme (Summe aller Artikel) im gewünschten Modus zurück.
     * @param string|null $mode 'net' oder 'gross' (optional, sonst globaler Modus)
     * @return float
     */
    public static function getSubTotalByMode(string $mode = null): float
    {
        $cart = self::create();
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
        return Warehouse::formatCurrency(self::getSubTotalByMode($mode));
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
        return Warehouse::formatCurrency(self::getCartTotalByMode($mode));
    }

    /**
     * Gibt die gesamte Steuer im Warenkorb zurück (Summe aller Einzelsteuern).
     * @return float
     */
    public static function getTaxTotalByMode(): float
    {
        $cart = self::create();
        $items = $cart->getItems();
        $sum = 0;
        
        foreach ($items as $item) {
            if (!isset($item['amount'])) {
                continue;
            }
            
            // Use stored tax amount if available, otherwise calculate dynamically
            if (isset($item['tax_amount'])) {
                $sum += (float)$item['tax_amount'];
            } else {
                // Fallback for backward compatibility - calculate dynamically
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
        }
        return round($sum, 2);
    }
}
