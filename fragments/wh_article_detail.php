<?php
/*
  https://chekromul.github.io/uikit-ecommerce-template
 *   */

// dump($this->articles[0]->getData());
// dump($this->articles[0]);

$main_article = $this->articles[0]->getData();
$warehouse_prop = rex::getProperty('warehouse_prop');


?>
<div class="uk-grid-medium" uk-grid>
    <div>
        <h3><?= $main_article['name_1'] ?></h3>
    </div>
    <div>
        <div class="uk-card uk-card-small">
            <div class="uk-grid" uk-grid>
                <div class="uk-width-1-1 uk-width-expand@m">
                    <?php if ($this->article->get_gallery()) : ?>
                        <div class="uk-grid-collapse uk-child-width-1-1" uk-slideshow="finite: true; ratio: 4:3;" uk-grid>
                            <div>
                                <ul class="uk-slideshow-items" uk-lightbox>
                                    <?php foreach (explode(',', $this->article->get_gallery()) as $img) : ?>
                                        <li>
                                            <a href="/images/full/<?= $img ?>">
                                                <figure class="tm-media-box-wrap"><img src="/images/products/<?= $img ?>" alt="<?= $this->article->get_name() ?>" class="warehouse_prod_image"></figure>
                                            </a>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                            <div>
                                <div class="uk-card-body uk-flex uk-flex-center">
                                    <div class="uk-width-1-2 uk-visible@s">
                                        <div uk-slider="finite: true">
                                            <div class="uk-position-relative">
                                                <div class="uk-slider-container">
                                                    <ul class="uk-slider-items uk-child-width-1-4 uk-grid uk-grid-small">
                                                        <?php foreach (explode(',', $this->article->get_gallery()) as $k => $img) : ?>
                                                            <li uk-slideshow-item="<?= $k ?>">
                                                                <div class="tm-ratio tm-ratio-1-1">
                                                                    <a class="tm-media-box tm-media-box-frame">
                                                                        <figure class="tm-media-box-wrap"><img src="/images/thumb/<?= $img ?>" alt="<?= $this->article->get_name() ?>"></figure>
                                                                    </a>
                                                                </div>
                                                            </li>
                                                        <?php endforeach ?>
                                                    </ul>
                                                    <div><a class="uk-position-center-left-out uk-position-small" href="#" uk-slider-item="previous" uk-slidenav-previous></a><a class="uk-position-center-right-out uk-position-small" href="#" uk-slider-item="next" uk-slidenav-next></a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="uk-slideshow-nav uk-dotnav uk-hidden@s"></ul>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>                                    
                        <div class="uk-width-1-1">
                            <div class="uk-card-body uk-padding-remove">
                                <img src="/images/products/<?= $main_article['image'] ?>" class="warehouse_prod_image">
                            </div>
                        </div>
                    <?php endif ?>
                </div>
                <div class="uk-width-1-1 uk-width-1-3@m tm-product-info">
                    <?php if (count($this->articles) > 1) :  // ==== Variantenartikel   ?>
                        <div class="uk-margin">
                            <div class="uk-grid-medium uk-grid" uk-grid="">
                                <div>
                                    <!-- div class="uk-text-small uk-margin-xsmall-bottom">Portion</div -->
                                    <ul class="uk-subnav uk-subnav-pill tm-variations wh-variants" uk-switcher="">
                                        <?php foreach ($this->articles as $k => $var) : ?>
                                            <?php if ($k == 0) : ?>
                                                <li aria-expanded="true" class="uk-margin-small"><a data-price="<?= $var->var_price ?>" data-art_id="<?= $var->get_art_id() ?>"><?= $var->var_name ?></a></li>
                                            <?php else : ?>
                                                <li aria-expanded="false" class=""><a data-price="<?= $var->var_price ?>" data-art_id="<?= $var->get_art_id() ?>"><?= $var->var_name ?></a></li>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </ul>
                                </div>
                            </div>
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
                                                <?php if ($attr['attr']['type'] == 'SELECT') :  // SELECT erstmal nicht berücksichtigen ?>
                                                    <?php foreach ($attr['data'] as $_attr) : ?>
                                                        <?php $art_attributes = $_attr->getData() ?>
                                                        <?php $all_attributes = warehouse_articles::attr_to_array($attr['attr']['values']); ?>
                                                        <?php // dump($_attr['attr']['values']); ?>
                                                        <?php // dump($all_attributes); ?>
                                                        <?php // dump($art_attributes); ?>
                                                        <ul class="tm-variations wh-attributes uk-list">
                                                            <?php foreach (explode(',',$art_attributes['value']) as $k=>$attr_val) : ?>
                                                                <li class="uk-display-inline-block label label-info<?= $k ? '' : ' uk-active' ?>">
                                                                    <a class="uk-button uk-button-primary" data-price="0" data-art_id="<?= $this->article->get_art_id() ?>" data-attr_id="<?= trim($attr['attr']['id'].'~~'.$attr_val,'~') ?>"><?= $all_attributes[$attr_val] ?></a>
                                                                </li>
                                                            <?php endforeach ?>
                                                        </ul>                                            
                                                    <?php endforeach ?>
                                                <?php elseif ($attr['attr']['type'] == 'WIDGET') : ?>
                                                    <ul class="tm-variations wh-attributes uk-list">
                                                        <?php $start = 1 ?>
                                                        <?php foreach ($attr['data'] as $k=>$_attr) : ?>
                                                            <?php if (!$_attr->available) continue ?>
                                                            <li class="uk-display-inline-block label label-info<?= $start ? ' uk-active' : '' ?>">
                                                                <a class="uk-button uk-button-primary" data-pricemode="<?= $attr['attr']['pricemode'] ?>" data-price="<?= $_attr->price ?: '0' ?>" data-attr_id="<?= $_attr->value ?>"><?= $_attr->label ?></a>
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

                    <div class="uk-grid-small uk-child-width-1-1" uk-grid>
                        <div>
                            <?= $main_article['longtext_1'] ?>

                            <?php $specifications = json_decode($this->article->{'specifications_' . rex_clang::getCurrentId()}) ?>
                            <table class="uk-table uk-table-divider uk-table-justify uk-table-responsive">
                                <tbody>
                                    <?php foreach ($specifications as $speci) : ?>
                                        <tr>
                                            <th class="uk-width-medium"><?= $speci[0] ?></th>
                                            <td class="uk-table-expand"><?= $speci[1] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>                               


                            <div id="warehouse_art_price" class="tm-product-price" data-price="<?= $this->article->get_price() ?>"><?= $this->article->get_price(true) ?></div>
                        </div>
                        <div>
                            <form action="/" method="post" id="warehouse_form_detail">
                                <input type="hidden" name="art_id" value="<?= $this->article->get_art_id() ?>">
                                <input type="hidden" name=action value="add_to_cart">
                                <p class="uk-text-small uk-margin-remove-top">inkl. MwSt. zzgl. <a href="#shipping_modal" uk-toggle>Versandkosten</a></p>
                                <label for="warehouse_count_<?= $this->article->get_art_id() ?>" class="switch_count uk-button uk-button-primary uk-padding-remove uk-margin-bottom" data-value="-1">
                                    <span uk-icon="icon: minus;"></span>
                                </label>
                                <input name="order_count" type="text" class="uk-input uk-inline order_count uk-margin-bottom" id="warehouse_count_<?= $this->article->get_art_id() ?>" value="1">
                                <label for="warehouse_count_<?= $this->article->get_art_id() ?>" class="uk-button uk-button-primary uk-padding-remove switch_count uk-margin-bottom" data-value="+1">
                                    <span uk-icon="icon: plus;"></span>
                                </label>
                                <button type="submit" name="submit" value="1" class="uk-button order_button uk-margin-bottom">{{ Bestellen }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
