<?php

$_REQUEST['table_name'] = rex::getTablePrefix() . "wh_countries";
include \rex_path::plugin('yform','manager','pages/data_edit.php');
