<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_extension;
use rex_extension_point;
use rex_i18n;

class Payment
{

    public const PAYMENT_OPTIONS = [
        'prepayment' => 'warehouse.payment_options.prepayment',
        'invoice' => 'warehouse.payment_options.invoice',
        'paypal' => 'warehouse.payment_options.paypal',
        'direct_debit' => 'warehouse.payment_options.direct_debit'
    ];

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_COMPLETED = 'completed';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_CANCELLED = 'cancelled';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    public const PAYMENT_STATUS_OPTIONS = [
        self::PAYMENT_STATUS_PENDING => 'warehouse.payment_status.pending',
        self::PAYMENT_STATUS_COMPLETED => 'warehouse.payment_status.completed',
        self::PAYMENT_STATUS_FAILED => 'warehouse.payment_status.failed',
        self::PAYMENT_STATUS_CANCELLED => 'warehouse.payment_status.cancelled',
        self::PAYMENT_STATUS_REFUNDED => 'warehouse.payment_status.refunded'
    ];

    // In Properties soll sowohl die gewählte Zahlungsart als auch die Parameter für die Zahlungsart gespeichert werden
    public $payment_type = '';
    public $payment_direct_debit = [
        'iban' => '',
        'bic' => '',
        'direct_debit_name' => ''
    ];

    public $payment_paypal = [
        'paypal_email' => ''
    ];

    // Getter und Setter für payment_type
    public function getPaymentType(): string
    {
        return $this->payment_type;
    }

    public function setPaymentType(string $payment_type): void
    {
        $this->payment_type = $payment_type;
    }

    // Getter und Setter für payment_direct_debit
    public function getPaymentDirectDebit(): array
    {
        return $this->payment_direct_debit;
    }

    public function setPaymentDirectDebit(array $direct_debit): void
    {
        $this->payment_direct_debit = array_merge($this->payment_direct_debit, $direct_debit);
    }

    // Getter und Setter für payment_paypal
    public function getPaymentPaypal(): array
    {
        return $this->payment_paypal;
    }

    public function setPaymentPaypal(array $paypal): void
    {
        $this->payment_paypal = array_merge($this->payment_paypal, $paypal);
    }

    public static function getPaymentStatusOptions(): array
    {
        $payment_status_options = self::PAYMENT_STATUS_OPTIONS;
        foreach ($payment_status_options as $key => $label) {
            $payment_status_options[$key] = rex_i18n::msg($label);
        }
        return $payment_status_options;
    }

    public static function getPaymentDetailsByType(string $payment_type): array
    {
        $payment_details = [];
        switch ($payment_type) {
            case 'prepayment':
                $payment_details = [
                    'type' => 'prepayment',
                    'details' => []
                ];
                break;
            case 'invoice':
                $payment_details = [
                    'type' => 'invoice',
                    'details' => []
                ];
                break;
            case 'paypal':
                $payment_details = [
                    'type' => 'paypal',
                    'details' => self::PAYMENT_OPTIONS['paypal']
                ];
                break;
            case 'direct_debit':
                $payment_details = [
                    'type' => 'direct_debit',
                    'details' => self::PAYMENT_OPTIONS['direct_debit']
                ];
                break;
        }
        return $payment_details;
    }


    public static function getPaymentOptions() :array
    {
        $payment_options = self::PAYMENT_OPTIONS;
        // Via Extension Point eigene Zahlungsarten hinzufügen
        $payment_options = rex_extension::registerPoint(new rex_extension_point('WAREHOUSE_PAYMENT_OPTIONS', $payment_options));
        return $payment_options;
    }

    public static function getPaymentOptionsChoice() {
        $payment_options = self::getPaymentOptions();
        $options = [];
        foreach ($payment_options as $key => $label) {
            $options[$key] = rex_i18n::msg($payment_options[$key]);
        }
        return $options;
    }

    public static function getAllowedPaymentOptions() :array
    {
        $payment_options = self::getPaymentOptions();
        $allowed_payment_options = Warehouse::getConfig('allowed_payment_options', '|prepayment|invoice|direct_debit|');
        // Nur die Optionen zurückgeben, die in der Konfiguration aktiviert sind
        $available_options = [];
        foreach ($payment_options as $key => $label) {
            if (strpos($allowed_payment_options, '|' . $key . '|') !== false) {
                $available_options[$key] = $label;
            }
        }
        return $available_options;
    }

    public static function loadPaymentFromSession(): array
    {
        $payment = rex_session('warehouse_payment', 'array', []);
        if (empty($payment)) {
            return [];
        }
        return $payment;
    }

    public function savePaymentToSession(): void
    {
        rex_set_session('warehouse_payment', $this);
    }

}
