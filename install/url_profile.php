<?php

/**
 * URL-Addon-Profil für Entry.
 *
 * @var rex_sql $sql
 */
$sql->setTable(rex::getTable('url_generator_profile'));
$sql->setValue('namespace', 'category_id');
$sql->setValue('article_id', rex_article::getSiteStartArticleId());
$sql->setValue('clang_id', 1);
$sql->setValue('ep_pre_save_called', 0);
$sql->setValue('table_name', '1_xxx_rex_warehouse_category');
$sql->setValue('table_parameters', json_encode([
    'column_id' => 'id',
    'column_clang_id' => '',
    'restriction_1_column' => '',
    'restriction_1_comparison_operator' => '=',
    'restriction_1_value' => '',
    'restriction_2_logical_operator' => '',
    'restriction_2_column' => '',
    'restriction_2_comparison_operator' => '=',
    'restriction_2_value' => '',
    'restriction_3_logical_operator' => '',
    'restriction_3_column' => '',
    'restriction_3_comparison_operator' => '=',
    'restriction_3_value' => '',
    'column_segment_part_1' => 'name_1',
    'column_segment_part_2_separator' => '/',
    'column_segment_part_2' => '',
    'column_segment_part_3_separator' => '/',
    'column_segment_part_3' => '',
    'relation_1_column' => '',
    'relation_1_position' => 'BEFORE',
    'relation_2_column' => '',
    'relation_2_position' => 'BEFORE',
    'relation_3_column' => '',
    'relation_3_position' => 'BEFORE',
    'append_user_paths' => '',
    'append_structure_categories' => '0',
    'column_seo_title' => '',
    'column_seo_description' => '',
    'column_seo_image' => '',
    'sitemap_add' => '1',
    'sitemap_frequency' => 'always',
    'sitemap_priority' => '1.0',
    'column_sitemap_lastmod' => 'updatedate',
]));
$sql->setValue('relation_1_table_name', '');
$sql->setValue('relation_1_table_parameters', '[]');
$sql->setValue('relation_2_table_name', '');
$sql->setValue('relation_2_table_parameters', '[]');
$sql->setValue('relation_3_table_name', '');
$sql->setValue('relation_3_table_parameters', '[]');
$sql->addGlobalCreateFields('warehouse');
$sql->addGlobalUpdateFields('warehouse');
$sql->insert();

$sql->setTable(rex::getTable('url_generator_profile'));
$sql->setValue('namespace', 'warehouse_art_id');
$sql->setValue('article_id', rex_article::getSiteStartArticleId());
$sql->setValue('clang_id', 1);
$sql->setValue('ep_pre_save_called', 0);
$sql->setValue('table_name', '1_xxx_rex_warehouse_article');
$sql->setValue('table_parameters', json_encode([
    'column_id' => 'id',
    'column_clang_id' => '',
    'restriction_1_column' => '',
    'restriction_1_comparison_operator' => '=',
    'restriction_1_value' => '',
    'restriction_2_logical_operator' => '',
    'restriction_2_column' => '',
    'restriction_2_comparison_operator' => '=',
    'restriction_2_value' => '',
    'restriction_3_logical_operator' => '',
    'restriction_3_column' => '',
    'restriction_3_comparison_operator' => '=',
    'restriction_3_value' => '',
    'column_segment_part_1' => 'name_1',
    'column_segment_part_2_separator' => '/',
    'column_segment_part_2' => 'id',
    'column_segment_part_3_separator' => '/',
    'column_segment_part_3' => '',
    'relation_1_column' => '',
    'relation_1_position' => 'BEFORE',
    'relation_2_column' => '',
    'relation_2_position' => 'BEFORE',
    'relation_3_column' => '',
    'relation_3_position' => 'BEFORE',
    'append_user_paths' => '',
    'append_structure_categories' => '0',
    'column_seo_title' => 'name_1',
    'column_seo_description' => 'description_1',
    'column_seo_image' => 'image',
    'sitemap_add' => '1',
    'sitemap_frequency' => 'always',
    'sitemap_priority' => '1.0',
    'column_sitemap_lastmod' => 'updatedate',
]));
$sql->setValue('relation_1_table_name', '');
$sql->setValue('relation_1_table_parameters', '[]');
$sql->setValue('relation_2_table_name', '');
$sql->setValue('relation_2_table_parameters', '[]');
$sql->setValue('relation_3_table_name', '');
$sql->setValue('relation_3_table_parameters', '[]');
$sql->addGlobalCreateFields('warehouse');
$sql->addGlobalUpdateFields('warehouse');
$sql->insert();
