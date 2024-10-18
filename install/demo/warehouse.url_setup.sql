## Redaxo Database Dump Version 5
## Prefix rex_
## charset utf-8

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `rex_url_generator_profile`;
CREATE TABLE `rex_url_generator_profile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `namespace` varchar(255) NOT NULL,
  `article_id` int(11) NOT NULL,
  `clang_id` int(11) NOT NULL DEFAULT '1',
  `table_name` varchar(255) NOT NULL,
  `table_parameters` text,
  `relation_1_table_name` varchar(255) NOT NULL,
  `relation_1_table_parameters` text,
  `relation_2_table_name` varchar(255) NOT NULL,
  `relation_2_table_parameters` text,
  `relation_3_table_name` varchar(255) NOT NULL,
  `relation_3_table_parameters` text,
  `createdate` datetime NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updatedate` datetime NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`,`article_id`,`clang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_url_generator_profile` WRITE;
/*!40000 ALTER TABLE `rex_url_generator_profile` DISABLE KEYS */;
INSERT INTO `rex_url_generator_profile` VALUES 
  (1,'category_id',1,1,'1_xxx_rex_warehouse_categories','{\"column_id\":\"id\",\"column_clang_id\":\"\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name_1\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"\",\"column_seo_description\":\"\",\"column_seo_image\":\"\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}','','[]','','[]','','[]','2019-07-15 08:16:48','wolfgang','2019-08-12 07:33:48','wolfgang'),
  (2,'warehouse_art_id',1,1,'1_xxx_rex_warehouse_articles','{\"column_id\":\"id\",\"column_clang_id\":\"\",\"restriction_1_column\":\"\",\"restriction_1_comparison_operator\":\"=\",\"restriction_1_value\":\"\",\"restriction_2_logical_operator\":\"\",\"restriction_2_column\":\"\",\"restriction_2_comparison_operator\":\"=\",\"restriction_2_value\":\"\",\"restriction_3_logical_operator\":\"\",\"restriction_3_column\":\"\",\"restriction_3_comparison_operator\":\"=\",\"restriction_3_value\":\"\",\"column_segment_part_1\":\"name_1\",\"column_segment_part_2_separator\":\"\\/\",\"column_segment_part_2\":\"id\",\"column_segment_part_3_separator\":\"\\/\",\"column_segment_part_3\":\"\",\"relation_1_column\":\"\",\"relation_1_position\":\"BEFORE\",\"relation_2_column\":\"\",\"relation_2_position\":\"BEFORE\",\"relation_3_column\":\"\",\"relation_3_position\":\"BEFORE\",\"append_user_paths\":\"\",\"append_structure_categories\":\"0\",\"column_seo_title\":\"name_1\",\"column_seo_description\":\"description_1\",\"column_seo_image\":\"image\",\"sitemap_add\":\"1\",\"sitemap_frequency\":\"always\",\"sitemap_priority\":\"1.0\",\"column_sitemap_lastmod\":\"updatedate\"}','','[]','','[]','','[]','2019-07-15 10:19:01','wolfgang','2019-08-12 07:34:01','wolfgang');
/*!40000 ALTER TABLE `rex_url_generator_profile` ENABLE KEYS */;
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS = 1;
