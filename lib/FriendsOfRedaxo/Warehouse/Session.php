<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_request;
use rex_ycom_auth;
use rex_ycom_user;

class Session extends rex_request
{
    const WAREHOUSE_SESSION_NAMESPACE = 'warehouse';

    const WAREHOUSE_CART_SESSION_KEY = 'warehouse_cart';
    const WAREHOUSE_CUSTOMER_SESSION_KEY = 'warehouse_customer';
    const WAREHOUSE_PAYMENT_SESSION_KEY = 'warehouse_payment';
    const WAREHOUSE_BILLING_ADRESS_SESSION_KEY = 'warehouse_billing_address';
    const WAREHOUSE_BILLING_ADDRESS_SESSION_KEY = 'warehouse_billing_address';
    const WAREHOUSE_SHIPPING_ADDRESS_SESSION_KEY = 'warehouse_shipping_address';

    /**
     * Returns the session namespace for the warehouse addon.
     *
     * @return string
     */
    public static function getSessionNamespace()
    {
        return self::WAREHOUSE_SESSION_NAMESPACE;
    }

    public static function initWarehouseSession()
    {
        // Initialize the session namespace if not already set
        if (!parent::session(self::WAREHOUSE_CART_SESSION_KEY)) {
            parent::setSession(self::WAREHOUSE_CART_SESSION_KEY, []);
        }
        if (!parent::session(self::WAREHOUSE_CUSTOMER_SESSION_KEY)) {
            parent::setSession(self::WAREHOUSE_CUSTOMER_SESSION_KEY, []);
        }
        if (!parent::session(self::WAREHOUSE_PAYMENT_SESSION_KEY)) {
            parent::setSession(self::WAREHOUSE_PAYMENT_SESSION_KEY, []);
        }
        if (!parent::session(self::WAREHOUSE_BILLING_ADRESS_SESSION_KEY)) {
            parent::setSession(self::WAREHOUSE_BILLING_ADRESS_SESSION_KEY, []);
        }
        if (!parent::session(self::WAREHOUSE_SHIPPING_ADRESS_SESSION_KEY)) {
            parent::setSession(self::WAREHOUSE_SHIPPING_ADRESS_SESSION_KEY, []);
        }

    }

    /**
     * Sets a session variable.
     *
     * @param string $key
     * @param mixed $value
     */
    public static function unset()
    {
        parent::unsetSession(self::WAREHOUSE_CART_SESSION_KEY);
        parent::unsetSession(self::WAREHOUSE_CUSTOMER_SESSION_KEY);
        parent::unsetSession(self::WAREHOUSE_PAYMENT_SESSION_KEY);
        parent::unsetSession(self::WAREHOUSE_BILLING_ADRESS_SESSION_KEY);
        parent::unsetSession(self::WAREHOUSE_SHIPPING_ADRESS_SESSION_KEY);
    }

    /**
     * Returns the cart from the session.
     *
     * @return array
     */
    public static function getCartData()
    {
        return self::session(self::WAREHOUSE_CART_SESSION_KEY, 'array', []);
    }

    /**
     * Sets the cart in the session.
     *
     * @param array $cart
     */
    public static function setCart(array $cart)
    {
        self::setSession(self::WAREHOUSE_CART_SESSION_KEY, $cart);
    }
    /**
     * Returns the customer data from the session.
     *
     * @return array
     */
    public static function getCustomerData()
    {
        return self::session(self::WAREHOUSE_CUSTOMER_SESSION_KEY, 'array', []);
    }
    /**
     * Sets the customer data in the session.
     *
     * @param array $customer
     */
    public static function setCustomer(array $customer)
    {
        self::setSession(self::WAREHOUSE_CUSTOMER_SESSION_KEY, $customer);
    }
    /**
     * Returns the payment data from the session.
     *
     * @return array
     */
    public static function getPaymentData()
    {
        return self::session(self::WAREHOUSE_PAYMENT_SESSION_KEY, 'array', []);
    }
    /**
     * Sets the payment data in the session.
     *
     * @param array $paymentData
     */
    public static function setPayment(array $paymentData)
    {
        self::setSession(self::WAREHOUSE_PAYMENT_SESSION_KEY, $paymentData);
    }
    /**
     * Returns the billing address from the session.
     *
     * @return array
     */
    public static function getBillingAddressData()
    {
        return self::session(self::WAREHOUSE_BILLING_ADRESS_SESSION_KEY, 'array', []);
    }
    /**
     * Sets the billing address in the session.
     *
     * @param array $billingAddress
     */
    public static function setBillingAddress(array $billingAddress)
    {
        self::setSession(self::WAREHOUSE_BILLING_ADRESS_SESSION_KEY, $billingAddress);
    }
    /**
     * Returns the shipping address from the session.
     *
     * @return array
     */
    public static function getShippingAddressData()
    {
        return self::session(self::WAREHOUSE_SHIPPING_ADRESS_SESSION_KEY, 'array', []);
    }
    /**
     * Sets the shipping address in the session.
     *
     * @param array $shippingAddress
     */
    public static function setShippingAddress(array $shippingAddress)
    {
        self::setSession(self::WAREHOUSE_SHIPPING_ADRESS_SESSION_KEY, $shippingAddress);
    }

    
    public static function saveAsOrder(string $payment_id = ''): bool
    {
        $cart = new Cart(); // Lädt den Warenkorb aus der Session
        $order = Order::create();

        $customer = self::getCustomerData();
        $billingAddress = self::getBillingAddressData();
        $shippingAddress = self::getShippingAddressData();

        $order->setOrderTotal($cart->getCartTotal());
        $order->setPaymentId($payment_id);
        $order->setPaymentConfirm($customer['payment_confirm'] ?? '');
        $order->setOrderJson(json_encode([
            'cart' => $cart->getItems(),
            'customer' => $customer,
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
        ]));

        $order->setCreateDate(date('Y-m-d H:i:s'));
        
        // Use customer data from cart or fallback to user_data
        $order->setFirstname($customer['firstname'] ?? '');
        $order->setLastname($customer['lastname'] ?? '');
        $order->setAddress($customer['address']  ?? '');
        $order->setZip($customer['zip'] ?? '');
        $order->setCity($customer['city'] ?? '');
        $order->setEmail($customer['email'] ?? '');

        if (class_exists('rex_addon') && rex_addon::get('ycom')->isAvailable()) {
            $ycom_user = rex_ycom_auth::getUser();
            if ($ycom_user) {
                $order->setValue('ycom_user_id', $ycom_user->getId());
            }
        }

        // Auto-assign order number before saving
        Document::assignOrderNo($order);

        return $order->save();
    }


    /**
     * Check if delivery address is different from billing address
     */
    public function hasSeparateDeliveryAddress(): bool
    {
        $billingAddress = self::getBillingAddressData();
        $shippingAddress = self::getShippingAddressData();

        // Check if billing and shipping addresses are set and different
        return !empty($billingAddress) && !empty($shippingAddress) &&
               ($billingAddress !== $shippingAddress);
    }
    /**
     * Auto-set customer from YCom if available
     */
    public function autoSetCustomerFromYCom(): void
    {
        if (rex_addon::get('ycom')->isAvailable()) {
            $ycom_user = rex_ycom_auth::getUser();
            if ($ycom_user instanceof rex_ycom_user) {
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
}
