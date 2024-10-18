<?php
// dump($this->items[0]);
if (!isset ($this->category)) {
    return;
}
?>
<div class="uk-grid-margin uk-first-column">
    <div class="uk-grid-medium uk-grid" uk-grid="">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-6">
                <h2><?= $this->category['name_1'] ?></h2>
                <?php if ($this->category['image']) : ?>
                    <img src="/images/product/<?= $this->category['image'] ?>">
                <?php endif ?>
                <?= html_entity_decode($this->category['longtext_1']) ?>
            </div>
            <div class="uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-match" uk-height-match=".mh_title, .longtext, .price_info" uk-grid uk-lightbox="animation: fade; toggle: a.lightboxlink">

                <?php foreach ($this->items as $item) : ?>
                    <div class="uk-margin-large-top">
                        <div class="uk-card-title">
                            <h3 class="mh_title"><a href="<?= rex_getUrl('', '', ['warehouse_art_id' => $item->id]) ?>"><?= $item->name_1 ?></a></h3>
                        </div>
                        <div>
                            <a href="/images/lightbox/<?= $item->image ?>" data-caption="<?= $item->name_1 ?>" class="lightboxlink">
                                <img src="/images/product/<?= $item->image ?>" alt="<?= $item->name_1 ?>" class="warehouse_prod_image">
                            </a>
                        </div>
                        <div class="longtext uk-margin-top">
                            <?= \FriendsOfRedaxo\Warehouse\Helper::uk_format_text($item->description_1) ?>
                            <?php $specifications = rex_var::toArray($item->specifications_1); ?>
                            <?php if ($specifications) : ?>
                                <dl class="uk-description-list">
                                    <?php foreach ($specifications as $spec) : ?>
                                        <dt><?= $spec[0] ?></dt>
                                        <dd><?= $spec[1] ?></dd>
                                    <?php endforeach ?>
                                </dl>
                            <?php endif ?>
                        </div>
                        <p class="priceline uk-margin-remove"><?= $item->get_price(true) ?></p>
                        <p class="uk-text-small uk-margin-remove-top">inkl. MwSt. zzgl. <a href="#shipping_modal" uk-toggle>Versandkosten</a></p>                            
                        <form action="/" method="post">
                            <input type="hidden" name="art_id" value="<?= $item->get_art_id() ?>">
                            <input type="hidden" name=action value="add_to_cart">
                            <label for="warehouse_count_<?= $item->get_art_id() ?>" class="switch_count uk-button uk-button-primary uk-padding-remove" data-value="-1"><span uk-icon="icon: minus;"></span></label><input name="order_count" type="text" class="uk-input uk-inline order_count" id="warehouse_count_<?= $item->get_art_id() ?>" value="1"><label for="warehouse_count_<?= $item->get_art_id() ?>" class="uk-button uk-button-primary uk-padding-remove switch_count" data-value="+1"><span uk-icon="icon: plus;"></span></label>
                            <button type="submit" name="submit" value="1" class="uk-button order_button">{{ Bestellen }}</button>
                        </form>
                    </div>
                <?php endforeach ?>                
            </div>
        </div>
    </div>
</div>

<?php /*
 *
 Vorhandene Werte in $this->items (Beispiel):
 * 
"id" => "1"
"whid" => "pk-ts1"
"name_1" => "Tressower See - Vier Farben"
"category_id" => "2"
"image" => "postkarte_tressower_see-1.jpg"
"description_1" => ""
"attributegroup_id" => ""
"price" => "1.00"
"tax" => "tax_value_1"
"free_shipping" => "0"
"prio" => "1"
"longtext_1" => "Die Postkarte Tressower See zeigt vier Motive. Auf den Motiven dominieren die Farben Gelb, Rot, Grau und Blau"
"gallery" => ""
"specifications_1" => "[]"
"status" => "1"
"header_image" => ""
"art_name" => "Tressower See - Vier Farben"
"art_description" => ""
"art_longtext" => "Die Postkarte Tressower See zeigt vier Motive. Auf den Motiven dominieren die Farben Gelb, Rot, Grau und Blau"
"var_name" => null
"var_image" => null
"var_freeprice" => null
"var_id" => null
"var_whvarid" => null
"var_price" => null
"var_gallery" => null
"cat_name" => "Postkarten Tressower See"
"cat_id" => "2"

  
  
 *  
 */
?>
