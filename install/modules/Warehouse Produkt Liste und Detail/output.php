<?php /* wh05 . Katalog - Liste und Detailansicht Shop - Output  */ 
$warehouse_prop = rex::getProperty('warehouse_prop');

if (rex::isBackend()) {
    echo '<h2>Warehouse Kategorie- und Detailansicht</h2>';
} else {
    $manager = Url\Url::resolveCurrent();
    if ($manager) {
        $profile = $manager->getProfile();
        $data_id = (int) $manager->getDatasetId();
        if ($profile->getTableName() == rex::getTable('warehouse_articles')) {
            // Detailanzeige
            if ($var_id = rex_get('var_id','int')) {
                $article = warehouse_articles::get_articles(0,[$data_id,$var_id],true);
            } else {
                $article = warehouse_articles::get_articles(0,[$data_id],false,true);
            }
            
//            dump($article[0]->getData());
            
            $attributes = warehouse_articles::get_attributes_for_article($article[0]);
            $fragment = new rex_fragment();
            $fragment->setVar('article',$article[0]);
            $fragment->setVar('articles',$article);
            $fragment->setVar('attributes',$attributes);
            echo $fragment->parse('warehouse_article_detail.php');
        } elseif ($profile->getTableName() == rex::getTable('warehouse_categories')) {
            $fragment = new rex_fragment();
            
            // Listenanzeige Unterkategorie
            $categories = warehouse_categories::get_children($data_id);
            if ($categories) {
                $fragment->setVar('tree',$categories);
                $fragment->setVar('path',$warehouse_prop['path']);
                echo $fragment->parse('warehouse_catalog.php');
            }
            
            // Nur Artikel - keine Varianten
            $articles = warehouse_articles::get_articles($data_id,[]);
            $category = warehouse_categories::get($data_id)->getData();
            if (isset($articles[0])) {
                $fragment->setVar('items',$articles);
                $fragment->setVar('category',$category);
                $fragment->setVar('path',$warehouse_prop['path']);
                echo $fragment->parse('warehouse_article_with_variants.php');
                echo $fragment->parse('warehouse_scheme_article_with_variants.php');
            }
        }
    } else {
        // Katalog
        $fragment = new rex_fragment();
        $fragment->setVar('tree',$warehouse_prop['tree']);
        echo $fragment->parse('warehouse_catalog.php');
    }
    
}

?>
