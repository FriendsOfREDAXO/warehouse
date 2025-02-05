<?php
if (!isset($this->category)) {
    return;
}
?>
<section class="my-8 my-lg-10">
    <!-- Kategorie Header -->
    <div class="bg-light py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 order-lg-2">
                    <h2><?= $this->category['name_1'] ?></h2>
                    <?= str_replace(
                        ['<p>', '<li>'],
                        ['<p class="lead opacity-75">', '<li class="opacity-75">'],
                        html_entity_decode($this->category['longtext_1'])
                    ); ?>
                </div>
                <?php if ($this->category['image']) : ?>
                    <div class="col-lg-4 order-lg-1">
                        <div class="ratio ratio-1x1">
                            <div class="bg-image" style="background-image: url(/images/product/<?= $this->category['image'] ?>)"></div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Artikel Grid -->
    <div class="container">
        <div class="row g-4">
            <?php foreach ($this->items as $item) : ?>
                <div class="col-6 col-lg-4">
                    <article class="card h-100 product-card">
                        <!-- Produkt Bild -->
                        <div class="ratio ratio-1x1">
                            <div class="card-img-top bg-image"
                                 style="background-image: url('/images/product/<?=$item->image?>')">

                                <form action="/" method="post" class="product-actions">
                                    <input type="hidden" name="art_id" value="<?= $item->get_art_id() ?>">
                                    <input type="hidden" name="action" value="add_to_cart">
                                    <input type="hidden" name="order_count" value="1">

                                    <div class="btn-group shadow">
                                        <a href="<?= rex_getUrl('', '', ['wh_art_id' => $item->id]) ?>"
                                           class="btn btn-light"
                                           data-bs-toggle="tooltip"
                                           data-bs-title="Produkt Informationen">
                                            <i class="bi bi-info-circle fs-5"></i>
                                        </a>
                                        <button type="submit"
                                                class="btn btn-light"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="In den Warenkorb">
                                            <i class="bi bi-cart-plus fs-5"></i>
                                        </button>
                                    </div>
                                </form>

                                <a href="<?= rex_getUrl('', '', ['wh_art_id' => $item->id]) ?>"
                                   class="stretched-link product-title">
                                    <span class="visually-hidden"><?= $item->name_1 ?></span>
                                </a>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h3 class="h5 mb-3">
                                <a href="<?= rex_getUrl('', '', ['wh_art_id' => $item->id]) ?>"
                                   class="text-decoration-none text-dark">
                                    <?= $item->name_1 ?>
                                </a>
                            </h3>

                            <div class="mt-auto">
                                <div class="price-block mb-2">
                                    <span class="price fs-4">
                                        <span class="price-amount">
                                            <bdi><?= $item->get_price(true) ?></bdi>
                                        </span>
                                    </span>
                                </div>
                                <small class="text-muted d-block">
                                    inkl. Ust. zzgl. <a href="#shipping_modal" data-bs-toggle="modal">Versandkosten</a>
                                </small>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<style>
    .bg-image {
        background-size: cover;
        background-position: center;
    }

    .product-card {
        border: 1px solid rgba(0,0,0,.125);
        transition: all 0.3s ease;
    }

    .product-card:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        transform: translateY(-3px);
    }

    .product-actions {
        position: absolute;
        top: 1rem;
        right: 1rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card:hover .product-actions {
        opacity: 1;
    }

    .btn-light {
        background: rgba(255,255,255,.9);
    }

    .btn-light:hover {
        background: #fff;
    }

    .product-title {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>