<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Cart;
use FriendsOfRedaxo\Warehouse\Domain;

?>
<a href="<?= Domain::getCurrent()?->getCartArtUrl() ?? '' ?>"
	title="<?= rex_i18n::msg('warehouse.settings.label_cart') ?>"
	class="btn btn-outline-secondary align-middle d-inline-flex align-items-center px-3 py-1">
	<i class="bi-icon bi-cart me-2"></i>
	<?= rex_i18n::msg('warehouse.settings.label_cart') ?>
	<span
		class="badge bg-secondary ms-2"
		data-warehouse-cart-count><?= Cart::create()->count() ?></span>
</a>
