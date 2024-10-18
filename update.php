<?php

rex_sql_table::get(rex::getTable('warehouse_attributes'))
  ->ensureColumn(new rex_sql_column('pricemode', 'varchar(191)', false, ''))
  ->alter();
rex_sql_table::get(rex::getTable('warehouse_orders'))
  ->ensureColumn(new rex_sql_column('paypal_confirm_token', 'text', false, ''))
  ->alter();
rex_sql_table::get(rex::getTable('ycom_user'))
  ->ensureColumn(new rex_sql_column('company', 'text', false, ''),'name')
  ->alter();
