<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

$_REQUEST['table_name'] = rex::getTablePrefix() . "wh_zones";
include \rex_path::plugin('yform','manager','pages/data_edit.php');
