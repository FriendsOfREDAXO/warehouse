<?php

/** @var rex_fragment $this */

foreach ($this->items as $item) : ?>

<script type="application/ld+json">
{
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "<?= $item->name_1 ?>",
    "image": [
        "<?= trim(rex::getServer(),'/') ?>/media/<?= $item->image ?>"
    ],
    "description": "<?= strip_tags($item->description_1) ?>",
    "sku": "<?= $item->whid ?>",
    "offers": {
        "@type": "Offer",
        "url": "<?= trim(rex::getServer(),'/').rex_getUrl('','',['warehouse_art_id'=>$item->get_art_id()]) ?>",
        "priceCurrency": "<?= rex_config::get('warehouse','currency') ?>",
        "price": "<?= $item->get_price() ?>",
        "priceValidUntil": "<?= date('Y-m-d',strtotime('+1 year')) ?>",
        "itemCondition": "https://schema.org/NewCondition",
        "availability": "https://schema.org/InStock",
        "seller": {
            "@type": "Organization",
            "name": "Ferien am Tressower See",
            "legalName" : "Ferien am Tressower See",
            "url": "https://ferien-am-tressower-see.de",
            "foundingDate": "2004",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Am Tressower See 1",
                "addressLocality": "Tressow",
                "addressRegion": "Mecklenburg",
                "postalCode": "23966",
                "addressCountry": "DE"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "contactType": "customer support",
                "telephone": "[+49-3841-6408571]",
                "email": "info@ferien-am-tressower-see.de"
            }
        }
    }
}
</script>
<?php endforeach ?>
