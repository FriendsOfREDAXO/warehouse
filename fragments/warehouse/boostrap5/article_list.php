<?php

/** @var rex_fragment $this */

?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="row row-cols-1">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-3 g-4">
                                <?php foreach ($this->articles as $item) : ?>
                                    <?php $link = isset($item->var_id) ? rex_getUrl('', '', ['warehouse_art_id' => $item->id, 'var_id' => $item->var_id]) : rex_getUrl('', '', ['warehouse_art_id' => $item->id]); ?>
                                    <div class="col">
                                        <div class="card h-100">
                                            <a href="<?= $link; ?>">
                                                <?php if ($item->get_image()) : ?>
                                                    <img src="/images/whlist/<?= $item->get_image() ?>" class="card-img-top" alt="<?= $item->get_name() ?>">
                                                <?php else : ?>
                                                    <svg class="bd-placeholder-img card-img-top" width="100%" height="180" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Image" preserveAspectRatio="xMidYMid slice" focusable="false">
                                                        <title>Placeholder</title>
                                                        <rect width="100%" height="100%" fill="#868e96"></rect><text x="50%" y="50%" fill="#dee2e6" dy=".3em">Image</text>
                                                    </svg>
                                                <?php endif ?>
                                            </a>
                                            <div class="card-body">
                                                <h5 class="card-title"><a href="<?= $link ?>"><?= $item->get_name() ?></a></h5>
                                                <p class="card-text"><?= $item->art_description ?></p>
                                                <p class="card-text"><small class="text-muted"><?= $item->cat_name ?></small></p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="btn-group">
                                                        <a href="<?= rex_getUrl('', '', ['art_id' => $item->get_art_id(), 'action' => 'add_to_cart', 'order_count' => 1]) ?>" class="btn btn-sm btn-outline-secondary">Add to cart</a>
                                                    </div>
                                                    <small class="text-muted"><?= $item->get_price(true) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
