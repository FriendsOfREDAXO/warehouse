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
				href="<?= htmlspecialchars($back_url, ENT_QUOTES, 'UTF-8') ?>">
				<i class="bi bi-arrow-left"></i>
				<?= htmlspecialchars($back_label, ENT_QUOTES, 'UTF-8') ?>
			</a>
		</div>
	</section>
	<section class="col-12">
		<?= html_entity_decode($this->form); ?>
	</section>
	<section class="col-12 my-3">
		<div class="d-flex justify-content-between align-items-center">
			<a class="btn btn-outline-secondary"
				href="<?= htmlspecialchars($back_url, ENT_QUOTES, 'UTF-8') ?>">
				<i class="bi bi-arrow-left"></i>
				<?= htmlspecialchars($back_label, ENT_QUOTES, 'UTF-8') ?>
			</a>
		</div>
	</section>
</div>