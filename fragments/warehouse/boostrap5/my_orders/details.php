<?php

/** @var rex_fragment $this */

?>
<article class="card">
    <div class="card-body">
        <header class="text-center"><h1 class="card-title">Bestellung vom <?= $this->order->get_date() ?></h1></header>
        <section class="card-text">


<pre>
Bestelldatum: <?= $this->order->get_date() ?>
<?= $this->order->order_text ?>
</pre>
            <p><a href="<?= rex_getUrl() ?>">zur Übersicht</a></p>
        </section>
    </div>
</article>
