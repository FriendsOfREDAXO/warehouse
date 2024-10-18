<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_sql;
use rex;

class SingleArticle extends \rex_yform_manager_dataset {

    static $article = false;

    public static function get_article() {
        self::set_article();
        if (!self::$article) {
            return false;
        }
//        dump($article); exit;
        $art = [];
        $art['count'] = rex_request('order_count', 'int') ?: 1;
        $art['art_id'] = rex_request('art_id');
        $art['price'] = self::$article['value3'];
        $art['name'] = self::$article['value2'];
        $art['cat_name'] = '';
        $art['cat_id'] = '';
        $art['description'] = '';
        $art['image'] = '';
        $art['var_id'] = '';
        $art['var_whvarid'] = '';
        $art['whid'] = rex_request('art_id');
        $art['tax'] = 'tax_value_1';
        $art['free_shipping'] = false;
        $art['attributes'] = [];
        return $art;
    }

    public static function set_article() {
        $sql = rex_sql::factory()->setTable(rex::getTable('article_slice'));
        $sql->setWhere('value1 = :value1 AND value20 = "warehouse_single" AND status = 1',['value1'=>rex_request('art_id')]);
        $res = $sql->select()->getArray();
        if (count($res)) {
            self::$article = $res[0];
        }
    }
}
