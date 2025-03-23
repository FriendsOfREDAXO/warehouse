<?php

/** @var rex_fragment $this */

?>
<div class="row">
    <section class="col-12 text-center">
        <a class="text-muted small" href="<?= rex_getUrl(rex_config::get('warehouse','cart_page')) ?>">
            <i class="bi bi-arrow-left small"></i>
            {{ return to cart }}
        </a>
        <h1 class="mt-sm-2 mb-0">{{ checkout }}</h1>
    </section>
    <section class="col-12">
        <div class="row">
            <div class="col-12 col-md">
                <?= html_entity_decode($this->form); ?>
            </div>            
        </div>
        <p>
            <a class="text-muted small" href="<?= rex_getUrl(rex_config::get('warehouse', 'cart_page')) ?>">
                {{ return to cart }}
            </a>
        </p>
    </section>
</div>
