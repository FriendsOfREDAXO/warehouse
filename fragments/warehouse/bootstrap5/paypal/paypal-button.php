<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Api\Cart;
use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Api\Order;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;

?>
<div
	data-warehouse-paypal-config
	data-warehouse-paypal="button-container" id="paypal-button-container"
	data-warehouse-paypal-style="<?= htmlspecialchars(PayPal::getStyleConfig(), ENT_QUOTES, 'UTF-8') ?>" 
	data-warehouse-paypal-error-page-url="<?= htmlspecialchars(PayPal::getErrorPageUrl(), ENT_QUOTES, 'UTF-8') ?>" 
	data-warehouse-paypal-success-page-url="<?= htmlspecialchars(PayPal::getSuccessPageUrl(), ENT_QUOTES, 'UTF-8') ?>">
</div>
<p data-warehouse-paypal="result-message" id="paypal-result-message"></p>


<?php


global $client;
$PAYPAL_CLIENT_ID = PayPal::getClientId();
$PAYPAL_CLIENT_SECRET = PayPal::getClientSecret();

$client = PaypalServerSdkClientBuilder::init()
	->clientCredentialsAuthCredentials(
		ClientCredentialsAuthCredentialsBuilder::init(
			$PAYPAL_CLIENT_ID,
			$PAYPAL_CLIENT_SECRET
		)
	)
	->environment(Environment::SANDBOX) // Use Environment::live() for production
	->build();


Order::createOrder([]); // For debugging purposes, remove in production

?>

<script nonce="<?= rex_response::getNonce() ?>"
	src="https://www.paypal.com/sdk/js?client-id=<?= PayPal::getClientId() ?>&buyer-country=DE&currency=EUR&components=buttons&enable-funding=card&disable-funding=venmo,paylater"
	data-sdk-integration-source="developer-studio"></script>
<script nonce="<?= rex_response::getNonce() ?>"
	src="/assets/addons/warehouse/js/paypal-frontend.js"></script>
