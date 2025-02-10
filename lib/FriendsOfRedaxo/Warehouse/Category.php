<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_clang;
use rex_yform_value_select_sql_tree;

class Category extends \rex_yform_manager_dataset
{

    public static function get_query()
    {
        $clang = rex_clang::getCurrentId();
        return self::query()
            ->select('longtext_'.$clang, '`longtext`')
            ->select('name_'.$clang, '`name`')
        ;
    }

    public static function get_category($id = 0)
    {
        $query = self::get_query();
        $query->where('id', $id);
        return $query->findOne();
    }

    public static function get_children($cat, $depth = 1, $level = 0)
    {
        $clang = rex_clang::getCurrentId();
        $tree_select = new rex_yform_value_select_sql_tree();
        $tree_select->set_query('SELECT id, image, name_'.$clang.' name FROM rex_warehouse_category WHERE status = 1 AND parent_id = |parent_id| ORDER BY prio');
        $data = $tree_select->sqlTree($cat, $level, $depth);
        return $data;
    }
    
    public static function get_all()
    {
        $clang = rex_clang::getCurrentId();
        $data = self::query()
                ->alias('cat')
                ->select('cat.name_' . $clang, 'cat_name')
                ->select('cat.longtext_' . $clang, 'cat_longtext')
                ->orderBy('cat.prio')
                ->where('cat.status', 1)
        ;
                

        return $data->find();
    }
    

}
