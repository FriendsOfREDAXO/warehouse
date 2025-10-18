<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Warehouse;

$domain = Domain::getCurrent();
$ycom_mode = Warehouse::getConfig('ycom_mode', 'guest_only');

// Determine back URL based on ycom_mode
if ($ycom_mode === 'choose') {
    $back_url = $domain?->getCheckoutUrl() ?? '';
    $back_label = Warehouse::getLabel('back');
} else {
    $back_url = $domain?->getCartArtUrl() ?? '';
    $back_label = Warehouse::getLabel('back_to_cart');
}

?>
<div class="row">
	<section class="col-12 my-3">
		<div class="d-flex justify-content-between align-items-center">
			<a class="btn btn-outline-secondary"
				href="<?= $back_url ?>">
				<i class="bi bi-arrow-left"></i>
				<?= $back_label ?>
			</a>
		</div>
	</section>
	<section class="col-12">
		<div class="row">
			<div class="col-12 col-md">
				<?= html_entity_decode($this->form); ?>
			</div>
		</div>
	</section>
	<section class="col-12 my-3">
		<div class="d-flex justify-content-between align-items-center">
			<a class="btn btn-outline-secondary"
				href="<?= $back_url ?>">
				<i class="bi bi-arrow-left"></i>
				<?= $back_label ?>
			</a>
		</div>
	</section>
</div>