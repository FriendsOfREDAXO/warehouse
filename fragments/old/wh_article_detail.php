<?php /* 
https://chekromul.github.io/uikit-ecommerce-template
 *   */

// dump($this->articles[0]->getData())


?>
<div>
    <div class="uk-grid-medium" uk-grid>
        <div class="uk-width-expand">
            <div>
                <div class="uk-grid-medium uk-child-width-1-1" uk-grid>
                    <div>
                        <div class="uk-card uk-card-default uk-card-small tm-ignore-container">
                            <div class="uk-grid-small uk-grid-collapse uk-grid-match" uk-grid>
                                <div class="uk-width-1-1 uk-width-expand@m">
                                    <?php if ($this->article->get_gallery()) : ?>
                                        <div class="uk-grid-collapse uk-child-width-1-1" uk-slideshow="finite: true; ratio: 4:3;" uk-grid>
                                            <div>
                                                <ul class="uk-slideshow-items" uk-lightbox>
                                                    <?php foreach (explode(',', $this->article->get_gallery()) as $img) : ?>
                                                        <li>
                                                            <a class="uk-card-body tm-media-box tm-media-box-zoom" href="/images/full/<?= $img ?>">
                                                                <figure class="tm-media-box-wrap"><img src="/images/products/<?= $img ?>" alt="<?= $this->article->get_name() ?>"></figure>
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
                                                                    <ul class="tm-slider-items uk-slider-items uk-child-width-1-4 uk-grid uk-grid-small">
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
                                    <?php endif ?>
                                </div>
                                <div class="uk-width-1-1 uk-width-1-3@m tm-product-info">
                                    <div class="uk-card-body">
                                        <?php if (count($this->articles) > 1) :  // ==== Variantenartikel ?>
                                            <div class="uk-margin">
                                                <div class="uk-grid-medium uk-grid" uk-grid="">
                                                    <div>
                                                        <!-- div class="uk-text-small uk-margin-xsmall-bottom">Portion</div -->
                                                        <ul class="uk-subnav uk-subnav-pill tm-variations wh-variants" uk-switcher="">
                                                            <?php foreach ($this->articles as $k=>$var) : ?>
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
                                                            <?php if ($attr['attr']['multiple'] == 1) : ?>
                                                                <div class="wh-attributes">
                                                                <?php foreach ($attr['data'] as $_attr) : ?>
                                                                    <?php if (!$_attr->label) continue ?>
                                                                    <button type="button" id="multiple_attr_<?= $_attr->value ?>" class="uk-button uk-margin-small-bottom uk-margin-small-right" uk-toggle="target: #multiple_attr_<?= $_attr->value ?>; cls: uk-card-primary" data-price="<?= $_attr->price ?>" data-attr_id="<?= $_attr->value ?>">
                                                                        <?= $_attr->label ?>
                                                                    </button>
                                                                    <!-- li class="uk-margin-small-bottom" uk-toggle><label><input type="checkbox" data-price="<?= $_attr->price ?>" data-attr_id="<?= $_attr->value ?>"><?= $_attr->label ?></label></li -->
                                                                <?php endforeach ?>
                                                                </div>
                                                            <?php else : ?>
                                                                <ul class="uk-subnav uk-subnav-pill tm-variations wh-attributes" uk-switcher="">
                                                                <?php foreach ($attr['data'] as $_attr) : ?>
                                                                    <?php if (!$_attr->label) continue ?>
                                                                    <li aria-expanded="false" class="uk-margin-small-bottom"><a data-price="<?= $_attr->price ?>" data-attr_id="<?= $_attr->value ?>"><?= $_attr->label ?></a></li>
                                                                <?php endforeach ?>
                                                                </ul>
                                                            <?php endif ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                        
                                        
                                        <div class="uk-margin">
                                            <div class="uk-padding-small uk-background-primary-lighten uk-border-rounded">
                                                <div class="uk-grid-small uk-child-width-1-1" uk-grid>
                                                    <div>
                                                        <div id="warehouse_art_price" class="tm-product-price" data-price="<?= $this->article->get_price() ?>"><?= $this->article->get_price(true) ?></div>
                                                    </div>
                                                    <div>
                                                        <form action="/index.php" method="post" id="warehouse_form_detail">
                                                            <input type="hidden" name="art_id" value="<?= $this->article->get_art_id() ?>">
                                                            <input type="hidden" name="action" value="add_to_cart">

                                                            <div class="uk-grid-small" uk-grid>
                                                                <div><a onclick="increment(-1, 'product-1')" uk-icon="icon: minus; ratio: .75"></a><input class="uk-input tm-quantity-input" id="product-1" type="text" maxlength="3" value="1" name="order_count" /><a onclick="increment(+1, 'product-1')" uk-icon="icon: plus; ratio: .75"></a></div>
                                                                <div><button class="uk-button uk-button-primary tm-shine" value="1" name="submit" type="submit">{{ add to cart }}</button></div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin">
                                            <div class="uk-padding-small uk-background-muted uk-border-rounded">
                                                <div class="uk-grid-small uk-child-width-1-1 uk-text-small" uk-grid>
                                                    <div>
                                                        <div class="uk-grid-collapse" uk-grid>
                                                            <span class="uk-margin-xsmall-right" uk-icon="cart"></span>
                                                            <div>
                                                                <div class="uk-text-bolder">Lieferung</div>
                                                                <div class="uk-text-xsmall uk-text-muted">auf Lager, sofort lieferbar</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="uk-grid-collapse" uk-grid>
                                                            <span class="uk-margin-xsmall-right" uk-icon="location"></span>
                                                            <div>
                                                                <div class="uk-text-bolder">Abholung</div>
                                                                <div class="uk-text-xsmall uk-text-muted">auf Lager, Abholung möglich</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin">
                                            <ul class="uk-list uk-text-small uk-margin-remove">
                                                <?php foreach ($this->attributes as $attr) : ?>
                                                    <?php if (!$attr['attr']['orderable']) : ?>
                                                        <li><span class="uk-text-muted"><?= $attr['data'][0]->at_name ?>: </span><span><?= str_replace(',',' / ',$attr['data'][0]->value) ?> <?= $attr['data'][0]->at_unit ?></span></li>
                                                    <?php endif ?>
                                                <?php endforeach ?>
                                            </ul>
                                            <div class="uk-margin-small-top"><a class="uk-link-heading js-scroll-to-description" href="#description" onclick="UIkit.switcher('.js-product-switcher').show(1);"><span class="tm-pseudo">{{ Detailed specifications }}</span><span class="uk-margin-xsmall-left" uk-icon="icon: chevron-down; ratio: .75;"></span></a></div>
                                        </div>
                                    </div>
                                </div>


                                <!-- #description -->

                                <div class="uk-width-1-1 tm-product-description uk-grid-margin uk-first-column" id="description">
                                    <?php if (strlen($this->article->{'specifications_'.rex_clang::getCurrentId()}) > 10) : ?>
                                    <header>
                                        <nav class="tm-product-nav uk-sticky" uk-sticky="offset: 60; bottom: #description; cls-active: tm-product-nav-fixed;" style="">
                                            <ul class="uk-subnav uk-subnav-pill js-product-switcher" uk-switcher="connect: .js-tabs">
                                                <li aria-expanded="true" class="uk-active"><a class="js-scroll-to-description" href="#description">{{ Overview }}</a></li>
                                                <li aria-expanded="false"><a class="js-scroll-to-description" href="#description">{{ Specifications }}</a></li>
                                            </ul>
                                        </nav>
                                        <div class="uk-sticky-placeholder" style="height: 71px; margin: 0px;" hidden=""></div>
                                    </header>
                                    <?php endif ?>
                                    <div class="uk-card-body">
                                        <div class="uk-switcher js-product-switcher js-tabs">
                                            <section class="uk-active">
                                                <article class="uk-article">
                                                    <div class="uk-article-body">
                                                        <?= $this->article->art_longtext ?>
                                                    </div>
                                                </article>
                                            </section>
                                            <?php if (strlen($this->article->{'specifications_'.rex_clang::getCurrentId()}) > 10) : ?>
                                            <section>
                                                <article class="uk-article">
                                                    <div class="uk-article-body">
                                                        <h2>Spezifikationen</h2>
                                                        <?php $specifications = json_decode($this->article->{'specifications_'.rex_clang::getCurrentId()}) ?>
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
                                                    </div>
                                                </article>
                                            </section>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>                                          
                                <!-- /#description -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
