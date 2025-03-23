<article class="uk-card uk-card-default uk-card-body uk-article tm-ignore-container">
    <header class="uk-text-center"><h1 class="uk-article-title">Meine Bestellungen</h1></header>
    <section class="uk-article">
        <table class="uk-table-striped uk-table">
            <tbody>
                <?php foreach ($this->orders as $order) : ?>
                    <tr>
                        <td><?= $order->id ?></td>
                        <td><?= $order->get_date() ?></td>
                        <td><?= $order->firstname ?> <?= $order->lastname ?></td>
                        <td><a href="<?= rex_getUrl('', '', ['order_id' => $order->id]) ?>">{{ Bestellung ansehen }}</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</article>