<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Warehouse;

$domain = Domain::getCurrent();
$ycom_mode = Warehouse::getConfig('ycom_mode', 'guest_only');

// Determine back URL based on ycom_mode
if ($ycom_mode === 'choose') {
    $back_url = $domain?->getCheckoutUrl() ?? '';
    $back_label = rex_i18n::msg('warehouse.settings.label_back');
} else {
    $back_url = $domain?->getCartArtUrl() ?? '';
    $back_label = rex_i18n::msg('warehouse.settings.label_back_to_cart');
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
		<div class="row">
			<div class="col-12 col-md">
				<?= html_entity_decode($this->form); ?>
			</div>
		</div>
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