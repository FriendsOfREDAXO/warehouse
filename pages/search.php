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
            <th style="width: 20%;">Name</th>
            <th style="width: auto">Details</th>
            <th style="width: 10%;">UUID</th>
            <th style="white-space: nowrap; min-width: 180px">Aktion</th>
        </tr>
    </thead>
    <tbody>
<?php 
foreach ($results as $result) {
    $backend_url = '';
    switch ($result['source']) {
        case 'article':
            $backend_url = Article::get($result['id'])->getBackendUrl();
            break;
        case 'article_variant':
            $backend_url = ArticleVariant::get($result['id'])->getBackendUrl();
            break;
        case 'order':
            $backend_url = Order::get($result['id'])->getBackendUrl();
            break;
        case 'category':
            $backend_url = Category::get($result['id'])->getBackendUrl();
            break;
    }

    $frontend_url = '';
    switch ($result['source']) {
        case 'article':
            $frontend_url = Article::get($result['id'])->getUrl();
            break;
        case 'article_variant':
            $frontend_url = ArticleVariant::get($result['id'])->getUrl();
            break;
        case 'order':
            $frontend_url = Order::get($result['id'])->getUrl();
            break;
        case 'category':
            $frontend_url = Category::get($result['id'])->getUrl();
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
    echo '<td><div style="white-space: nowrap;">' . $source_emoji . '</div></td>';
    echo '<td>' . htmlspecialchars($result['name'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($result['details'] ?? '') . '</td>';
    echo '<td><small>' . htmlspecialchars($result['uuid'] ?? '') . '</small></td>';
    echo '<td style="white-space: nowrap;"><div class="btn-group" style="white-space: nowrap;"><a class="btn btn-primary btn-sm" href="' . $backend_url . '">Bearbeiten</a><a class="btn btn-success btn-sm" href="' . $frontend_url . '">Anzeigen</a></div></td>';

    echo '</tr>';
}
?>
    </tbody>
</table>
