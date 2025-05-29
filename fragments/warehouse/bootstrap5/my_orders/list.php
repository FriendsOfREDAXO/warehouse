<?php

/** @var rex_fragment $this */

?>
<article class="card">
    <div class="card-body">
        <header class="text-center"><h1 class="card-title">Meine Bestellungen</h1></header>
        <section class="card-text">
            <table class="table table-striped">
                <tbody>
                    <?php foreach ($this->orders as $order) : ?>
                        <tr>
                            <td><?= $order->id ?></td>
                            <td><?= $order->get_date() ?></td>
                            <td><?= $order->firstname ?> <?= $order->lastname ?></td>
                            <td><a href="<?= rex_getUrl('', '', ['warehouse-order-id' => $order->id]) ?>">Bestellung ansehen</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</article>
