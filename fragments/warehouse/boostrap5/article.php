<?php

/** @var rex_fragment $this */

?><h2>Artikel</h2>

<?php foreach ($this->items as $item) : ?>
    <div class="article_item">
        <form action="/" method="post">
            <input type="hidden" name="art_id" value="<?= $item->get_art_id() ?>">
            <input type="hidden" name=action value="add_to_cart">
        <p><?= $item->get_name() ?></p>
        <?php if ($item->var_freeprice) : ?>
            <label for="input_price_<?= $item->get_art_id() ?>">Preis eintragen</label>
            <input name="price" id="input_price_<?= $item->get_art_id() ?>" type="text"><br>
        <?php endif ?>
        <label for="warehouse_count_<?= $item->get_art_id() ?>" class="switch_count" data-value="-1">-</label><input name="order_count" type="text" class="order_count" id="warehouse_count_<?= $item->get_art_id() ?>"><label for="warehouse_count_<?= $item->get_art_id() ?>" class="switch_count" data-value="+1">+</label>
        <button type="submit" name="submit" value="1" class="order_button">Bestellen</button>
        </form>
    </div>
<?php endforeach ?>
