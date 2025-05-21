<?php

use FriendsOfRedaxo\Warehouse\Search;
use FriendsOfRedaxo\Warehouse\Warehouse;

class rex_api_warehouse_search extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        $search = rex_request('q', 'string', '');
        $result = Search::query($search, 10);
        $results = [];
        foreach ($result as $item) {
            $item['url'] = '';
            $item['icon'] = '';
            if($item['source'] == 'article') {
                $item['url'] = '<a href="'. rex_url::backendPage('warehouse/article/edit', ['id' => $item['id']]) . '" class="rex-link" target="_blank"> '.$item['name'].' <i class="rex-icon fa-arrow-right"></i></a>';
                $item['icon'] = '<i class="rex-icon fa-cube"></i>';
            } elseif($item['source'] == 'article_variant') {
                $item['url'] = '<a href="'. rex_url::backendPage('warehouse/article_variant/edit', ['id' => $item['id']]) . '" class="rex-link" target="_blank"> '.$item['name'].' <i class="rex-icon fa-arrow-right"></i></a>';
                $item['icon'] = '<i class="rex-icon fa-cubes"></i>';
            } elseif($item['source'] == 'order') {
                $item['url'] = '<a href="'. rex_url::backendPage('warehouse/order/edit', ['id' => $item['id']]) . '" class="rex-link" target="_blank"> '.$item['name'].' <i class="rex-icon fa-arrow-right"></i></a>';
                $item['icon'] = '<i class="rex-icon fa-shopping-cart"></i>';
            }
            $results[$item['id']] = $item;
        }
        rex_response::cleanOutputBuffers();
        rex_response::sendJson($results);
        exit;
    }
}
