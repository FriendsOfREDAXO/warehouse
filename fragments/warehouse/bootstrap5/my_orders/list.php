<?php

/** @var rex_fragment $this */
use FriendsOfRedaxo\Warehouse\Order;
use rex_yform_manager_collection;

/** @var rex_yform_manager_collection $orders */
$orders = $this->getVar('orders');
?>
<article class="card">
	<div class="card-body">
		<header class="text-center">
			<h1 class="card-title">Meine Bestellungen</h1>
		</header>
		<section class="card-text">
			<table class="table table-striped">
				<tbody>
					<?php foreach ($this->orders as $order) :
                        /** @var Order $order */
                        ?>
					<tr>
						<td><?= $order->id ?></td>
						<td><?= $order->getCreatedate() ?></td>
						<td><?= $order->getFirstname() ?>
							<?= $order->getLastname() ?>
						</td>
						<td><a
								href="<?= rex_getUrl('', '', ['warehouse-order-id' => $order->id]) ?>">Bestellung
								ansehen</a></td>
					</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</section>
	</div>
</article>
