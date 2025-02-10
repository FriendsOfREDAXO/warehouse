<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_yform_value_select_sql_tree;

class Db
{
    
    public static function get_categories_tree()
    {
        $tree_select = new rex_yform_value_select_sql_tree();
        $tree_select->set_query('SELECT id, name_1 name FROM rex_warehouse_category WHERE parent_id = |parent_id|');
        return $tree_select->sqlTree(0, 0);
    }
    

}
