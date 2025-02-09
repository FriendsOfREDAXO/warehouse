<?php

$addon = rex_addon::get('warehouse');
echo rex_view::title($addon->i18n('warehouse.title'));

$_REQUEST['table_name'] = rex::getTablePrefix() . "wh_zones";
include \rex_path::plugin('yform', 'manager', 'pages/data_edit.php');
