<?php

class warehouse_orders extends \rex_yform_manager_dataset {

    public static function get_orders_for_user() {
        $ycom_user = rex_ycom_auth::getUser();
        $data = self::query()
                ->alias('orders')
                ->where('orders.ycom_userid',$ycom_user->id)
                ->orderBy('createdate','desc')
        ;
        return $data->find();        
        
    }
    
    public static function get_order_for_user($order_id) {
        $ycom_user = rex_ycom_auth::getUser();
        $data = self::query()
                ->alias('orders')
                ->where('orders.ycom_userid',$ycom_user->id)
                ->where('orders.id',$order_id)
                ->orderBy('createdate','desc')
        ;
        return $data->findOne();
        
    }
    
    
    public function get_date() {
        $date = strtotime($this->createdate);
        return date('d.m.Y',$date);
    }

}
