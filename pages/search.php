<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$table_name = 'rex_warehouse_order';

use FriendsOfRedaxo\Warehouse\Article;
use FriendsOfRedaxo\Warehouse\ArticleVariant;
use FriendsOfRedaxo\Warehouse\Category;
use FriendsOfRedaxo\Warehouse\Order;
use FriendsOfRedaxo\Warehouse\Search;

$results = Search::query(rex_request('query', 'string', ''));
?>
<div class="rex-page-section">
    <?= Search::getForm() ?>
</div>
<?php if (empty($results)): ?>
    <div class="alert alert-info">
        <?= $this->i18n('warehouse.search.no_results') ?>
    </div>
<?php
endif; ?>
<?php if (!empty($results)): ?>
    <div class="alert alert-success">
        <?= $this->i18n('warehouse.search.results', count($results)) ?>
    </div>

<?php
endif; ?>

<table class="table">
    <thead>
        <tr>
            <th>Quelle</th>
            <th>Name</th>
            <th>Details</th>
            <th>UUID</th>
            <th>Aktion</th>
        </tr>
    </thead>
    <tbody>
<?php 
foreach ($results as $result) {
    $url = '';
    switch ($result['source']) {
        case 'article':
            $url = Article::get($result['id'])->getBackendUrl();
            break;
        case 'article_variant':
            $url = ArticleVariant::get($result['id'])->getBackendUrl();
            break;
        case 'order':
            $url = Order::get($result['id'])->getBackendUrl();
            break;
    }
    $source_emoji = '';
    switch ($result['source']) {
        case 'article':
            $source_emoji = Article::getBackendIcon(true);
            break;
        case 'article_variant':
            $source_emoji = ArticleVariant::getBackendIcon(true);
            break;
        case 'category':
            $source_emoji = Category::getBackendIcon(true);
            break;
        case 'order':
            $source_emoji =  Order::getBackendIcon(true);
            break;
    }
    echo '<tr>';
    echo '<td>' . $source_emoji . '</td>';
    echo '<td>' . htmlspecialchars($result['name'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($result['details'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($result['uuid'] ?? '') . '</td>';
    echo '<td><a href="' . $url . '">Details</a></td>';
    echo '</tr>';
}
?>
    </tbody>
</table>
