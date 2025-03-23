<?php

/** @var rex_fragment $this */

?>
<form action="/" method="post">
<input type="hidden" name="action" value="add_group_to_cart">
<input type="hidden" name="current_article" value="<?= rex_article::getCurrentId() ?>">
<?php $check = 0 ?>
<?php foreach ($this->items as $k=>$item) : ?>
<?php if ($item->id != $check) : ?>
    <?= $item->art_description ?>
    <?php $check = $item->id ?>
<?php endif ?>
<div class="article_item">
        <input type="hidden" name="group[<?= $k ?>][art_id]" value="<?= $item->get_art_id() ?>">
    <h4 class="warehouse_article_name"><?= $item->get_name() ?></h4>
    <?php if ($item->var_freeprice) : ?>
        <label for="input_price_<?= $item->get_art_id() ?>">Preis eintragen</label>
        <input name="group[<?= $k ?>][price]" id="input_price_<?= $item->get_art_id() ?>" type="text" placeholder="0.00"><br>
    <?php endif ?>
    {{ Anzahl }} 
    <label for="warehouse_count_<?= $item->get_art_id() ?>" class="switch_count" data-value="-1"><span  uk-icon="icon: minus-circle"></span></label>
    <input name="group[<?= $k ?>][order_count]" type="text" class="order_count" id="warehouse_count_<?= $item->get_art_id() ?>" value="0">
    <label for="warehouse_count_<?= $item->get_art_id() ?>" class="switch_count" data-value="+1"><span  uk-icon="icon: plus-circle"></span></label>
</div>
<?php endforeach ?>
<button type="submit" name="submit" value="1" class="order_button uk-button">{{ In den Warenkorb }}</button>
</form>
