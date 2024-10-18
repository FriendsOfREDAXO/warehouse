<?php
// dump($this->items[0]);
?>

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



<section class="pt30 pb50 bg_grey">
    <div class="container">
        <div class="row">

            <div class="col-lg-12">
                <div class="catheadlinebox p20 mb30 bg_white">
                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-sm-10">
                            <h2 class="mb10"><?= $this->category['name_1'] ?></h2>
                            <?= html_entity_decode($this->category['longtext_1']) ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-2">
                            <?php if ($this->category['image']) : ?>
                                <img class="img-responsive" src="/media/<?= $this->category['image'] ?>">
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <?php foreach ($this->items as $item) : ?>
                <div class="col-lg-4 col-md-4 col-sm-6 mb30">
                    <div class="ab bg_white pt20 b_grey ">
                        <h3 class=""><a class="fontred" href="<?= rex_getUrl('', '', ['warehouse_art_id' => $item->id]) ?>"><?= $item->name_1 ?></a></h3>
                        <a href="<?= rex_getUrl('', '', ['warehouse_art_id' => $item->id]) ?>"  class="">
                            <img src="/media/<?= $item->image ?>" alt="<?= $item->name_1 ?>" class="img-responsive mt20">
                        </a>
                        <?= warehouse_helper::uk_format_text($item->description_1) ?>
                        <div class="row price mt20">
                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 pricecol">
                                <p><?= $item->get_price(true) ?></p>
                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 pricedesc">
                                <p class="price_desc">inkl. 19% MwSt. <br>zzgl. <a href="#shipping_modal">Versandkosten</a></p>
                            </div>
                        </div>
                        <div class="row pricebut">
                            <div class="col-lg-12">
                                <a class="btn btn-lg btn-sw btn-block checkout-btn mt20" href="<?= rex_getUrl('', '', ['warehouse_art_id' => $item->id]) ?>"><i class="fa fa-arrow-right"></i> Zum Artikel</a>
                            </div>
                        </div>                                                  
                    </div>
                </div>
            <?php endforeach ?>                
        </div>
    </div>
</section>
