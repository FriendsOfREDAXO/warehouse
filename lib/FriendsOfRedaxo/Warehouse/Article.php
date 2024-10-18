<?php
namespace FriendsOfRedaxo\Warehouse;

use rex_clang;
use rex_config;

class Article extends \rex_yform_manager_dataset {

    public static function get_query() {
        $qry = self::query();
        $qry->whereRaw('(stock_item = 0 OR (stock_item = 1 AND stock > 0))');
        return $qry;
    }

    public function get_val($key) {
        if (isset($this->{$key})) {
            return $this->{$key};
        } else {
            return 'nicht gefunden';
        }
    }

    public function get_art_id() {
        if (isset($this->var_id)) {
            return $this->id . '__' . $this->var_id;
        } else {
            return $this->id;
        }
    }

    public function get_name() {
        if (isset($this->var_id)) {
            return $this->art_name . ($this->var_name ?  ' - ' . $this->var_name : '');
        } else {
            return $this->art_name;
        }
    }

    public function get_image() {
        if (isset($this->var_id) && $this->var_image) {
            return $this->var_image;
        } else {
            return $this->image;
        }
    }

    public function get_gallery() {
        if ($this->var_id && $this->var_gallery) {
            return $this->var_gallery;
        } else {
            return $this->gallery;
        }
    }

    public function get_price($with_currency = false) {
        $price = $this->var_price ?? $this->price;
        if (isset($this->var_add_parent_price) && $this->var_add_parent_price) {
            $price = $this->price + $this->var_price;
        }
        if ($with_currency) {
            return rex_config::get('warehouse', 'currency_symbol') . '&nbsp;<span class="product_price">' . number_format($price,2) . '</span>';
        }
        return $price;
    }

    /*
    public function get_price_netto() {
        $brutto_price = $this->get_price();
        $tax = rex_config::get('warehouse', 'tax_value');
        $factor = (100 + $tax) / 100;
        return round($brutto_price / $factor, 2);
    }

    public function get_tax_value() {
        return $this->get_price() - $this->get_price_netto();
    }
    */

    /**
     * Bei Variantenartikeln: articleid__varianteid
     * @param type $article_id
     * @return type
     */
    public static function get_article($article_id = '') {
        if (strpos($article_id, '__')) {
            $art_id = explode('__', $article_id);
        } else {
            $art_id = [$article_id];
        }
//        dump($art_id); exit;
        return self::get_articles(0, $art_id, true);
    }

    /**
     * 
     * @param type $cat_id  - ausgewählt in der Moduleingabe
     * @param type $article_id
     * @param type $find_one
     * @param type $with_attributes
     * @param type $articles_only - liefert nur Artikel - keine Varianten
     * @return type
     */
    public static function get_articles($cat_id = 0, $article_id = [], $find_one = false, $with_attributes = false, $articles_only = false) {
        $clang = rex_clang::getCurrentId();
        if ($articles_only) {
            $data = self::get_query()
                ->alias('art')
                ->leftJoin('rex_warehouse_categories', 'cat', 'art.category_id', 'cat.id')
                ->select('art.name_' . $clang, 'art_name')
                ->select('cat.name_' . $clang, 'cat_name')
                ->select('cat.id', 'cat_id')
                ->select('art.description_' . $clang, 'art_description')
                ->select('art.longtext_' . $clang, 'art_longtext')
                ->orderBy('art.prio')
                ->where('art.status',1)
            ;
        } else {
            $data = self::get_query()
                ->alias('art')
                ->leftJoin('rex_warehouse_article_variants', 'var', 'art.id', 'var.parent_id')
                ->leftJoin('rex_warehouse_categories', 'cat', 'art.category_id', 'cat.id')
                ->select('art.name_' . $clang, 'art_name')
                ->select('art.description_' . $clang, 'art_description')
                ->select('art.longtext_' . $clang, 'art_longtext')
                ->select('var.name_' . $clang, 'var_name')
                ->select('var.image', 'var_image')
                ->select('var.freeprice', 'var_freeprice')
                ->select('var.add_parent_price', 'var_add_parent_price')
                ->select('var.id', 'var_id')
                ->select('var.whvarid', 'var_whvarid')
                ->select('var.weight', 'var_weight')
                ->select('var.price', 'var_price')
                ->select('var.relay_price', 'var_relay_price')
                ->select('var.gallery', 'var_gallery')
                ->select('cat.name_' . $clang, 'cat_name')
                ->select('cat.id', 'cat_id')
                ->orderBy('art.prio')
                ->orderBy('var.prio')
                ->where('art.status',1)
            ;
        }



        if ($cat_id) {
//            $data->where('art.category_id', $cat_id);
            $data->whereRaw('FIND_IN_SET(:cat_id,art.category_id)', ['cat_id'=>$cat_id]);
        }
        if (count($article_id) == 2) {
            $data->where('art.id', $article_id[0]);
            $data->where('var.id', $article_id[1]);
        } elseif (count($article_id) == 1) {
            $data->where('art.id', $article_id[0]);
        }

//       dump($data->getQuery()); exit;

        if ($find_one) {
            return $data->findOne();
        }

        $articles = $data->find();

        return $articles;
    }

    public function get_variants() {
        $clang = rex_clang::getCurrentId();
        $query = \rex_yform_manager_table::get(\rex::getTable('warehouse_article_variants'))->query();
        $query->select('name_'.$clang,'`name`');
        $query->selectRaw('CONCAT("'.$this->id.'__",id)','art_id');
        $query->where('parent_id',$this->id)->orderBy('prio');
        return $query->find();        
    }
  
    
    

}
