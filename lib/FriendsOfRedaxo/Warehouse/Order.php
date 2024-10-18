<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_ycom_auth;

class Order extends \rex_yform_manager_dataset {

    public static function getOrdersForUser() {
        $ycom_user = rex_ycom_auth::getUser();
        $data = self::query()
                ->alias('orders')
                ->where('orders.ycom_userid',$ycom_user->id)
                ->orderBy('createdate','desc')
        ;
        return $data->find();        
        
    }
    
    public static function GetOrderForUser($order_id) {
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
