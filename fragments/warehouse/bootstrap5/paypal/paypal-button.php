<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Api\Order;
use FriendsOfRedaxo\Warehouse\PayPal;
use FriendsOfRedaxo\Warehouse\Warehouse;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;

?>
<div
	data-warehouse-paypal-config
	data-warehouse-paypal="button-container" id="paypal-button-container"
	data-warehouse-paypal-style="<?= htmlspecialchars(PayPal::getStyleConfig(), ENT_QUOTES, 'UTF-8') ?>"
	data-warehouse-paypal-error-page-url="<?= htmlspecialchars(PayPal::getErrorPageUrl(), ENT_QUOTES, 'UTF-8') ?>"
	data-warehouse-paypal-success-page-url="<?= htmlspecialchars(PayPal::getSuccessPageUrl(), ENT_QUOTES, 'UTF-8') ?>"
	data-warehouse-paypal-error-create-order="<?= htmlspecialchars(Warehouse::getLabel('paypal.error_create_order'), ENT_QUOTES, 'UTF-8') ?>"
	data-warehouse-paypal-error-capture-order="<?= htmlspecialchars(Warehouse::getLabel('paypal.error_capture_order'), ENT_QUOTES, 'UTF-8') ?>"
	data-warehouse-paypal-error-technical-details="<?= htmlspecialchars(Warehouse::getLabel('paypal.error_technical_details'), ENT_QUOTES, 'UTF-8') ?>">
</div>
<p data-warehouse-paypal="result-message" id="paypal-result-message"></p>

<!-- PayPal Error Modal -->
<div class="modal fade" id="paypalErrorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="paypalErrorModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="paypalErrorModalLabel"><?= Warehouse::getLabel('paypal.error_modal_title') ?></h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(Warehouse::getLabel('paypal.error_modal_close'), ENT_QUOTES, 'UTF-8') ?>"></button>
			</div>
			<div class="modal-body" id="paypalErrorModalBody">
				<!-- Error message will be inserted here -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?= Warehouse::getLabel('paypal.error_modal_close') ?></button>
			</div>
		</div>
	</div>
</div>


<?php

global $client;
$PAYPAL_CLIENT_ID = PayPal::getClientId();
$PAYPAL_CLIENT_SECRET = PayPal::getClientSecret();

$client = PaypalServerSdkClientBuilder::init()
    ->clientCredentialsAuthCredentials(
        ClientCredentialsAuthCredentialsBuilder::init(
            $PAYPAL_CLIENT_ID,
            $PAYPAL_CLIENT_SECRET,
        ),
    )
    ->environment(Environment::SANDBOX) // Use Environment::live() for production
    ->build();

Order::createOrder([]); // For debugging purposes, remove in production

?>

<script nonce="<?= rex_response::getNonce() ?>"
	src="https://www.paypal.com/sdk/js?client-id=<?= PayPal::getClientId() ?>&buyer-country=DE&currency=<?= Warehouse::getCurrency() ?>&components=buttons&enable-funding=card&disable-funding=venmo,paylater"
	data-sdk-integration-source="developer-studio"></script>
<script nonce="<?= rex_response::getNonce() ?>"
	src="/assets/addons/warehouse/js/paypal-frontend.js"></script>
