<?php

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use PaypalServerSdkLib\Models\OrderRequest;
use PaypalServerSdkLib\Models\PurchaseUnitRequest;
use PaypalServerSdkLib\Models\AmountWithBreakdown;
use PaypalServerSdkLib\Models\Money;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\ApplicationContext;
use PaypalServerSdkLib\Models\ShippingDetail;
use PaypalServerSdkLib\Models\Address;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Name;
use PaypalServerSdkLib\Models\PaymentSource;
use PaypalServerSdkLib\Models\OrdersPayPal;
use PaypalServerSdkLib\Models\ExperienceContext;
use PaypalServerSdkLib\Models\Payee;
use PaypalServerSdkLib\Models\PayeeBase;
use PaypalServerSdkLib\Models\PurchaseUnit;
use Psr\Log\LogLevel;

class PayPalPayment
{
}
