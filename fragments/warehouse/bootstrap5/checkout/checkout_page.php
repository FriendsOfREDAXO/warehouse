<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Domain;
use FriendsOfRedaxo\Warehouse\Warehouse;

$domain = Domain::getCurrent();

?>
<div class="row">
	<section class="col-12 my-3">
		<a class="text-muted small"
			href="<?= $domain?->getCartArtUrl() ?? '' ?>">
			<i class="bi bi-arrow-left small"></i>
			<?= Warehouse::getLabel('back_to_cart') ?>
		</a>
	</section>
	<section class="col-12">
		<?= html_entity_decode($this->form) ?>
		<p>
			<a class="text-muted small"
				href="<?= $domain?->getCartArtUrl() ?? '' ?>">
				<?= Warehouse::getLabel('back_to_cart') ?>
			</a>
		</p>
	</section>
</div>