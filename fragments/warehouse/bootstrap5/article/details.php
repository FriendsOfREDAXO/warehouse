<?php

/** @var rex_fragment $this */

$main_article = $this->articles[0];
$warehouse_prop = rex::getProperty('warehouse_prop');

?>
<div class="row">
    <div class="col-12">
        <h3><?= $main_article->getName() ?></h3>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="row g-0">
                <div class="col-12 col-md-8">
                        <div class="col-12">
                            <div class="card-body p-0">
                                <?php if ($main_article->getImageAsMedia()) : ?>
                                    <img src="<?= $main_article->getImageAsMedia()->getUrl() ?>" class="warehouse_prod_image">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
                <div class="col-12 col-md-4 tm-product-info">
                    <?php if (count($this->articles) > 1) :  // ==== Variantenartikel   
                    ?>
                    <div class="mb-3">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <?php foreach ($this->articles as $k => $var) : ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?= $k == 0 ? 'active' : '' ?>" id="pills-<?= $var->getId() ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-<?= $var->getId() ?>" type="button" role="tab" aria-controls="pills-<?= $var->getId() ?>" aria-selected="<?= $k == 0 ? 'true' : 'false' ?>" data-price="<?= $var->getPrice() ?>" data-art_id="<?= $var->getId() ?>"><?= $var->getName() ?></button>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php endif ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <?= html_entity_decode($main_article->getText()) ?>

                            <?php $specifications = json_decode($this->article->{'specifications_' . rex_clang::getCurrentId()}) ?>
                            <table class="table table-bordered table-responsive">
                                <tbody>
                                    <?php if (is_array($specifications)) foreach ($specifications as $speci) : ?>
                                        <tr>
                                            <th class=""><?= $speci[0] ?></th>
                                            <td class=""><?= $speci[1] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>


                            <div id="warehouse_art_price" class="tm-product-price" data-price="<?= $this->article->getPrice() ?>"><?= $this->article->getPriceFormatted() ?></div>
                        </div>
                        <div class="col-12">
                            <form action="/" method="post" id="warehouse_form_detail">
                                <input type="hidden" name="art_id" value="<?= $this->article->getId() ?>">
                                <input type="hidden" name=action value="add_to_cart">
                                <p class="text-small mb-0">inkl. MwSt. zzgl. <a href="#shipping_modal" data-bs-toggle="modal">Versandkosten</a></p>
                                <div class="input-group mb-3">
                                    <button class="btn btn-outline-primary switch_count" type="button" data-value="-1"></button>
                                    <input name="order_count" type="text" class="form-control order_count" id="warehouse_count_<?= $this->article->getId() ?>" value="1">
                                    <button class="btn btn-outline-primary switch_count" type="button" data-value="+1"></button>
                                    <button type="submit" name="submit" value="1" class="btn btn-primary">{{ Bestellen }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
