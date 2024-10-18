<p>Versandkosten innerhalb Deutschlands:</p>
<?php $shipping_parameters = json_decode(rex_config::get('warehouse','shipping_parameters')); ?>
<ul class="uk-list uk-list-striped">
    <?php foreach (array_reverse($shipping_parameters) as $sp) : ?>
        <?php if ($sp[1] == 0) : ?>
            <li>Standard - <?= number_format($sp[2],2) ?> <?= rex_config::get('warehouse','currency_symbol') ?></li>
        <?php elseif ($sp[2] == 0) : ?>
            <li>ab einem Warenwert von <?= number_format($sp[1],2) ?> <?= rex_config::get('warehouse','currency_symbol') ?> - Versandkostenfrei</li>
        <?php else : ?>
            <li>ab einem Warenwert von <?= number_format($sp[1],2) ?> <?= rex_config::get('warehouse','currency_symbol') ?> - <?= number_format($sp[2],2) ?> <?= rex_config::get('warehouse','currency_symbol') ?></li>
        <?php endif ?>
    <?php endforeach ?>
</ul>
