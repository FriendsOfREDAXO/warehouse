<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\Warehouse\Warehouse;
use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\Domain;

/** @var Article $article */
$article = $this->getVar('article');

$output = [
    '@context' => 'https://schema.org/',
    '@type' => 'Product',
    'name' => $article->getName(),
    'image' => [Domain::getCurrentUrl() . ($article->getImageAsMedia()?->getUrl() ?? '')],
    'description' => strip_tags($article->getShortText(true) ?? ''),
    'sku' => $article->getValue('sku'),
    'offers' => [
        '@type' => 'Offer',
        'url' => Domain::getCurrentUrl() . $article->getUrl(),
        'priceCurrency' => Warehouse::getConfig('currency', 'EUR'),
        'price' => $article->getPrice(),
        'priceValidUntil' => date('Y-m-d',strtotime('+1 year')),
        'itemCondition' => 'https://schema.org/NewCondition',
        'availability' => 'https://schema.org/' . ($article->getAvailability() ?: 'InStock'),
        'seller' => [
            '@type' => 'Organization',
            'name' => Warehouse::getConfig('shop_name', 'Warehouse Shop'),
            'legalName' => Warehouse::getConfig('shop_name', 'Warehouse Shop'),
            'url' => Domain::getCurrentUrl(),
            'address' => [
                '@type' => 'PostalAddress',
                /*
                'streetAddress' => 'Am Tressower See 1',
                'addressLocality' => 'Tressow',
                'addressRegion' => 'Mecklenburg',
                'postalCode' => '23966',
                */
                'addressCountry' => 'DE',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                /*
                'telephone' => '[+49-3841-6408571]',
                'email' => 'info@ferien-am-tressower-see.de',
                */
            ],
        ],
    ],
];
?>
<script type="application/ld+json" nonce="<?= rex_response::getNonce() ?>">
<?= json_encode($output, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) ?>
</script>
