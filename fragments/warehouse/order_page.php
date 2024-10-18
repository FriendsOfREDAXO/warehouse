<?php
// dump($this->order->getData());
?>

<article class="uk-card uk-card-default uk-card-body uk-article tm-ignore-container">
    <header class="uk-text-center"><h1 class="uk-article-title">Bestellung vom <?= $this->order->get_date() ?></h1></header>
    <section class="uk-article">


<pre>
Bestelldatum: <?= $this->order->get_date() ?>


<?= $this->order->order_text ?>
</pre>
        <p><a href="<?= rex_getUrl() ?>">zur Übersicht</a></p>
    </section>
</article>