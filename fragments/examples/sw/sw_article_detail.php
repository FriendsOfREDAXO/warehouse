<script>
    $(function () {

        $('label.switch_count').click(function () {
            var for_id = $(this).attr('for');
            var cur_val = $('input#' + for_id).val();
            var new_val = eval(cur_val + $(this).data('value'));
            if (new_val < 1) {
                new_val = 1;
            }
            $('input#' + for_id).val(new_val);
        });
    });
</script>
<?php

$main_article = $this->articles[0]->getData();
$warehouse_prop = rex::getProperty('warehouse_prop');
?>

<section class="artdetails pt30 pb50 bg_grey">
    <div class="container">
        <div class="row">
            <div class="p20 bg_white b_grey">
                <div class="product-view-area nofoam">
                    <div class="product-big-image col-xs-12 col-sm-4 col-lg-4 col-md-4">
                        <div class="large-image"> <a href="/media/<?= $main_article['image'] ?>" class="cloud-zoom" id="zoom1" rel="useWrapper: false, adjustY:0, adjustX:20"> 
                                <img class="zoom-img" src="/media/<?= $main_article['image'] ?>" alt="<?= $this->article->get_name() ?>"> </a> 
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-lg-8 col-md-8 product-details-area">
                        <div class="product-name">
                            <h1><?= $main_article['name_1'] ?></h1>
                        </div>
                        <?php if (count($this->articles) > 1) :  // ==== Variantenartikel    ?>
                            <div class="variant-box">
                                <!-- div class="uk-text-small uk-margin-xsmall-bottom">Portion</div -->
                                <ul class="tm-variations wh-variants">
                                    <?php foreach ($this->articles as $k => $var) : ?>
                                        <?php if ($k == 0) : ?>
                                            <li class="label label-info"><a data-price="<?= $var->var_price ?>" data-art_id="<?= $var->get_art_id() ?>"><?= $var->var_name ?></a>
                                            </li>
                                        <?php else : ?>
                                            <li  class="label label-info"><a data-price="<?= $var->var_price ?>" data-art_id="<?= $var->get_art_id() ?>"><?= $var->var_name ?></a>
                                            </li>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>

                        <?php foreach ($this->attributes as $attr) : ?>
                            <?php if ($attr['attr']['orderable']) : // ==== Attribute ?>
                                <div class="uk-margin">
                                    <div class="uk-grid-medium uk-grid" uk-grid="">
                                        <div>
                                            <p><?= $attr['attr']['name_1'] ?></p>
                                            <?php if ($attr['attr']['multiple'] == 1) : ?>
                                            
                                                <div class="wh-attributes">
                                                    <?php foreach ($attr['data'] as $_attr) : ?>
                                                        <?php if (!$_attr->label) continue ?>
                                                        <button type="button" id="multiple_attr_<?= $_attr->value ?>" class="uk-button uk-margin-small-bottom uk-margin-small-right" uk-toggle="target: #multiple_attr_<?= $_attr->value ?>; cls: uk-card-primary" data-price="<?= $_attr->price ?>" data-attr_id="<?= $_attr->value ?>">
                                                            <?= $_attr->label ?>
                                                        </button>
                                                    <?php endforeach ?>
                                                </div>
                                            <?php else : ?>
                                                <?php if (0 && $attr['attr']['type'] == 'SELECT') :  // SELECT erstmal nicht berücksichtigen ?>
                                                    <?php foreach ($attr['data'] as $_attr) : ?>
                                                        <?php $art_attributes = $_attr->getData() ?>
                                                        <?php $all_attributes = FriendsOfRedaxo\Warehouse\Article::attr_to_array($attr['attr']['values']); ?>
                                                        <ul class="tm-variations wh-variants">
                                                            <?php foreach (explode(',',$art_attributes['value']) as $k=>$attr_val) : ?>
                                                                <li class="label label-info<?= $k ? '' : ' uk-active' ?>">
                                                                    <a data-price="<?= $main_article['price'] ?>" data-art_id="<?= $this->article->get_art_id() ?>" data-attr_id="<?= trim($art_attributes['id'].'__'.$attr_val,'_') ?>"><?= $all_attributes[$attr_val] ?></a>
                                                                </li>
                                                            <?php endforeach ?>
                                                        </ul>                                            
                                                    <?php endforeach ?>
                                                <?php elseif ($attr['attr']['type'] == 'WIDGET') : ?>
                                                    <ul class="tm-variations wh-attributes">
                                                        <?php $start = 1 ?>
                                                        <?php foreach ($attr['data'] as $k=>$_attr) : ?>
                                                            <?php if (!$_attr->available) continue ?>
                                                            <li class="label label-info<?= $start ? ' uk-active' : '' ?>">
                                                                <a data-price="<?= $_attr->price ?: '0' ?>" data-attr_id="<?= $_attr->value ?>"><?= $_attr->label ?></a>
                                                            </li>
                                                            <?php // dump($_attr); ?>
                                                            <?php $start = 0 ?>
                                                        <?php endforeach ?>
                                                    </ul>                                            
                                                <?php endif ?>                                            
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endforeach ?>



                        <div class="price-box">
                            <div class="priceleft"><div id="warehouse_art_price" class="tm-product-price" data-price="<?= $this->article->get_price() ?>"><?= $this->article->get_price(true) ?></div></div>
                            <div class="priceright"><span class="fa fa-info"></span> inkl. MwSt. zzgl. <a class="fontred" href="">Versandkosten</a></div>
                        </div>
                        <div class="p10 bg_grey">
                            <form action="/index.php" method="post" id="warehouse_form_detail">
                                <input type="hidden" name="art_id" value="<?= $this->article->get_art_id() ?>">
                                <input type="hidden" name=action value="add_to_cart">
                                <div class="cart-plus-minus">
                                    <label for="warehouse_count_<?= $this->article->get_art_id() ?>" class="switch_count" data-value="-1">
                                        <div class="inc qtybutton">
                                            <span><i class="fa fa-minus"></i></span>
                                        </div>
                                    </label>
                                    <input name="order_count" type="text" class="qty" id="warehouse_count_<?= $this->article->get_art_id() ?>" value="1">
                                    <label for="warehouse_count_<?= $this->article->get_art_id() ?>" class="switch_count" data-value="+1">
                                        <div class="inc qtybutton">
                                            <span><i class="fa fa-plus"></i></span>
                                        </div>
                                    </label>
                                </div>

                                <button type="submit" name="submit" value="1" class="button pro-add-to-cart"><span><i class="fa fa-shopping-cart"></i> {{ Bestellen }}</span></button>
                            </form>
                        </div>


                        <div class="sharebox">
                            <div class="title">
                                Artikel-Nr.: <?= $main_article['whid'] ?>
                            </div>
                        </div>
                        <div class="specbox">
                            <?php $specifications = json_decode($this->article->{'specifications_' . rex_clang::getCurrentId()}) ?>
                            <table class="table table-bordered table-striped mt20">
                                <tbody>
                                    <?php foreach ($specifications as $speci) : ?>
                                        <tr>
                                            <th class=""><?= $speci[0] ?></th>
                                            <td class=""><?= $speci[1] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="product-overview-tab">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12  p20 bg_white b_grey">
                            <div class="product-tab-inner">
                                <ul id="product-detail-tab" class="nav nav-tabs product-tabs">
                                    <li class="active"> <a href="#description" data-toggle="tab"> Beschreibung </a> </li>
                                    <li> <a href="#reviews" data-toggle="tab">Weitere Infos</a> </li>
                                </ul>
                                <div id="productTabContent" class="tab-content">
                                    <div class="tab-pane fade in active" id="description">
                                        <div class="std"><?= $main_article['longtext_1'] ?></div>
                                    </div>


                                    <div class="tab-pane fade" id="reviews">
                                        <div class="product-tabs-content-inner clearfix">
                                            <p><strong>Lorem Ipsum</strong><span>&nbsp;is
                                                    was popularised in the 1960s with the release of Letraset sheets 
                                                    containing Lorem Ipsum passages, and more recently with desktop 
                                                    publishing software like Aldus PageMaker including versions of Lorem 
                                                    Ipsum.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
