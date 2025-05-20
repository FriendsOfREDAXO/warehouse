<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_ycom_auth;
use rex_url;
use rex_yform_manager_collection;
use rex_yform_manager_dataset;
use rex_extension_point;
use rex_i18n;
use rex_formatter;

class Order extends rex_yform_manager_dataset {
        
        /* Anrede */
        /** @api */
        public function getSalutation() : ?string {
            return $this->getValue("salutation");
        }
        /** @api */
        public function setSalutation(mixed $value) : self {
            $this->setValue("salutation", $value);
            return $this;
        }
    
        /* Vorname */
        /** @api */
        public function getFirstname() : mixed {
            return $this->getValue("firstname");
        }
        /** @api */
        public function setFirstname(mixed $value) : self {
            $this->setValue("firstname", $value);
            return $this;
        }
    
        /* Nachname */
        /** @api */
        public function getLastname() : mixed {
            return $this->getValue("lastname");
        }
        /** @api */
        public function setLastname(mixed $value) : self {
            $this->setValue("lastname", $value);
            return $this;
        }
    
        /* Firma */
        /** @api */
        public function getCompany() : mixed {
            return $this->getValue("company");
        }
        /** @api */
        public function setCompany(mixed $value) : self {
            $this->setValue("company", $value);
            return $this;
        }
    
        /* Adresse */
        /** @api */
        public function getAddress() : mixed {
            return $this->getValue("address");
        }
        /** @api */
        public function setAddress(mixed $value) : self {
            $this->setValue("address", $value);
            return $this;
        }
    
        /* PLZ */
        /** @api */
        public function getZip() : mixed {
            return $this->getValue("zip");
        }
        /** @api */
        public function setZip(mixed $value) : self {
            $this->setValue("zip", $value);
            return $this;
        }
    
        /* Stadt */
        /** @api */
        public function getCity() : mixed {
            return $this->getValue("city");
        }
        /** @api */
        public function setCity(mixed $value) : self {
            $this->setValue("city", $value);
            return $this;
        }
    
        /* Land */
        /** @api */
        public function getCountry() : ?string {
            return $this->getValue("country");
        }
        /** @api */
        public function setCountry(mixed $value) : self {
            $this->setValue("country", $value);
            return $this;
        }
    
        /* E-Mail */
        /** @api */
        public function getEmail() : ?string {
            return $this->getValue("email");
        }
        /** @api */
        public function setEmail(mixed $value) : self {
            $this->setValue("email", $value);
            return $this;
        }
    
        /* Erstellungsdatum */
        /** @api */
        public function getCreatedate() : ?string {
            return $this->getValue("createdate");
        }

        // TODO: IntldateFromatter verwenden
        /** @api */
        public function getCreatedateFormatted() : ?string {
            return date('d.m.Y H:i',strtotime($this->getValue("createdate")));
        }

        /** @api */
        public function setCreatedate(string $value) : self {
            $this->setValue("createdate", $value);
            return $this;
        }
    
        /* PayPal-ID */
        /** @api */
        public function getPaypalId() : mixed {
            return $this->getValue("paypal_id");
        }
        /** @api */
        public function setPaypalId(mixed $value) : self {
            $this->setValue("paypal_id", $value);
            return $this;
        }
    
        /* Zahlungs-ID */
        /** @api */
        public function getPaymentId() : ?string {
            return $this->getValue("payment_id");
        }
        /** @api */
        public function setPaymentId(mixed $value) : self {
            $this->setValue("payment_id", $value);
            return $this;
        }
    
        /* PayPal-Bestätigungstoken */
        /** @api */
        public function getPaypalConfirmToken() : ?string {
            return $this->getValue("paypal_confirm_token");
        }
        /** @api */
        public function setPaypalConfirmToken(mixed $value) : self {
            $this->setValue("paypal_confirm_token", $value);
            return $this;
        }
    
        /* Zahlungsbestätigung */
        /** @api */
        public function getPaymentConfirm() : ?string {
            return $this->getValue("payment_confirm");
        }
        /** @api */
        public function setPaymentConfirm(mixed $value) : self {
            $this->setValue("payment_confirm", $value);
            return $this;
        }
    
        /* Bestelltext */
        /** @api */
        public function getOrderText(bool $asPlaintext = false) : mixed {
            if($asPlaintext) {
                return strip_tags($this->getValue("order_text"));
            }
            return $this->getValue("order_text");
        }
        /** @api */
        public function setOrderText(mixed $value) : self {
            $this->setValue("order_text", $value);
            return $this;
        }
                
        /* Bestell-JSON */
        /** @api */
        public function getOrderJson(bool $asArray = true) : mixed {
            if($asArray) {
                return json_decode($this->getValue("order_json"), true);
            }
            return $this->getValue("order_json");
        }
        /** @api */
        public function setOrderJson(string $value) : self {
            $this->setValue("order_json", $value);
            return $this;
        }
                
        /* Bestellsumme */
        /** @api */
        public function getOrderTotal() : ?float {
            return $this->getValue("order_total");
        }
        /** @api */
        public function setOrderTotal(float $value) : self {
            $this->setValue("order_total", $value);
            return $this;
        }
                
        /* YCom-Benutzer-ID */
        /** @api */
        public function getYcomUser() : ?rex_yform_manager_dataset {
            return $this->getRelatedDataset("ycom_user_id");
        }

        public function setYComUser(int $ycom_user_id) : self {
            $this->setValue("ycom_user_id", $ycom_user_id);
            return $this;
        }
    
        /* Zahlungsart */
        /** @api */
        public function getPaymentType() : ?string {
            return $this->getValue("payment_type");
        }
        /** @api */
        public function setPaymentType(mixed $value) : self {
            $this->setValue("payment_type", $value);
            return $this;
        }
    
        /* Bezahlt */
        /** @api */
        public function getPayed() : ?bool {
            return $this->getValue("payed");
        }
        /** @api */
        public function setPayed(bool $value) : self {
            $this->setValue("payed", $value);
            return $this;
        }
    
        /* Importiert */
        /** @api */
        public function getImported() : bool {
            return $this->getValue("imported");
        }
        /** @api */
        public function setImported(bool $value) : self {
            $this->setValue("imported", $value);
            return $this;
        }

    public static function findByYComUserId(int $ycom_user_id = null) : ?rex_yform_manager_collection {
        if($ycom_user_id === null) {
            $ycom_user = rex_ycom_auth::getUser();
            $ycom_user_id = $ycom_user->getId();
        }
        $data = self::query()
                ->alias('orders')
                ->where('orders.ycom_user_id',$ycom_user_id)
                ->orderBy('createdate','desc')
        ;
        return $data->find();
    }
    
    public static function findByUuid($uuid) : ?self {
        return self::query()->where('uuid',$uuid)->findOne();
       
    }

    public static function epYformDataList(rex_extension_point $ep)
    {
        /** @var rex_yform_manager_table $table */
        $table = $ep->getParam('table');
        if ($table->getTableName() !== self::table()->getTableName()) {
            return;
        }

        /** @var rex_yform_list $list */
        $list = $ep->getSubject();

        $list->removeColumn('id');

        $list->removeColumn('company');

        $list->removeColumn('salutation');
        $list->removeColumn('firstname');
        $list->removeColumn('lastname');

        $list->removeColumn('address');

        $list->removeColumn('zip');
        $list->removeColumn('city');

        $name = rex_i18n::msg('warehouse_order.buyer');
        $list->addColumn($name, '', 2);

        $list->setColumnFormat(
            $name,
            'custom',
            static function ($a) {
                $_csrf_key = self::table()->getCSRFKey();
                $token = \rex_csrf_token::factory($_csrf_key)->getUrlParams();

                $params = [];
                $params['table_name'] = self::table()->getTableName();
                $params['rex_yform_manager_popup'] = '0';
                $params['_csrf_token'] = $token['_csrf_token'];
                $params['data_id'] = $a['list']->getValue('id');
                $params['func'] = 'edit';

                $return = '';
                $values = $a['list'];
                // Anrede, Name, Adresse in einer Zelle
                if($values->getValue('company') != '') {
                    $return .= $values->getValue('company') . '<br>';
                }
                $return .= $values->getValue('salutation') . ' ' . $values->getValue('firstname') . ' ' . $values->getValue('lastname') . '<br>';
                $return .= $values->getValue('address') . '<br>';
                $return .= $values->getValue('zip') . ' ' . $values->getValue('city') . '<br>';
                $return .= $values->getValue('country') . '<br>';

                return '<div class="text-nowrap">' . $return . '</div>';

            },
        );

        $list->setColumnFormat(
            'email',
            'custom',
            static function ($a) {
                if($a !== '') {
                    return '<a href="mailto:' . $a['value'] . '">' . $a['value'] . '</a>';
                }
            }
        );

        $list->setColumnFormat(
            'payment_id',
            'custom',
            static function ($a) {
                // Kürze auf 10 Zeichen
                $payment_id = $a['value'];
                if (strlen($payment_id) > 10) {
                    $payment_id = substr($payment_id, 0, 10) . '…';
                }
                return $payment_id;
            },
        );
        
        $list->setColumnFormat(
            'createdate',
            'custom',
            static function ($a) {
                return rex_formatter::intlDate($a['value']);
            },
        );

        $list->setColumnPosition('ycom_userid', 2);
        $list->setColumnLabel('ycom_userid', '<i class=\'rex-icon rex-icon-user\'></i>');

        $list->setColumnFormat(
            'ycom_userid',
            'custom',
            static function ($a) {
                if($a['value'] > 0 && rex_addon::get('ycom')->isAvailable()) {
                    $user = \rex_ycom_user::get($a['value']);
                    $user_status = $user->getValue('status');

                    $user_status_class = '';
                    if($user_status == 0) {
                        $user_status_class = 'text-info';
                    } elseif($user_status < 0) {
                        $user_status_class = 'text-danger';
                    } elseif($user_status > 0) {
                        $user_status_class = 'text-success';
                    }
                    if($user === null && $a['value'] > 0) {
                        return '<i class="rex-icon rex-icon-user text-warning"></i>';
                    }
                    if($user) {
                        // index.php?page=yform/manager/data_edit&table_name=rex_ycom_user&list=45e18d03&sort=&sorttype=&start=0&_csrf_token=Qk3DRM8nOTKy8pFY9H7jA8qL7PQAORVL0hYGfUmEtw8&rex_yform_manager_popup=0&data_id=1&func=edit&45e18d03_start=0
                        return '<a href="' . rex_url::backendController(['page' => 'yform/manager/data_edit', 'table_name' => 'rex_ycom_user', '_csrf_token' => \rex_csrf_token::factory('ycom_user')->getUrlParams()['_csrf_token'], 'rex_yform_manager_popup' => 0, 'data_id' => $user->getId(), 'func' => 'edit']) . '"><i class="rex-icon rex-icon-user '.$user_status_class.'"></i></a>';
                    }
                }
                return '<i class="rex-icon rex-icon-user text-muted"></i>';
            }
        );

        $list->removeColumn('payment_confirm');
        $list->removeColumn('payment_type');
        $list->removeColumn('payed');

        $list->setColumnFormat(
            'order_total',
            'custom',
            static function ($a) {
                $order_total = $a['list']->getValue('order_total');
                $payment_confirm = $a['list']->getValue('payment_confirm');
                $payment_type = $a['list']->getValue('payment_type');
                $payed = $a['list']->getValue('payed');

                $return = '';

                if($order_total > 0) {
                    $return .= '<span class="">' . rex_formatter::number($order_total) . '</span><br>';
                } else {
                    $return .= '<span class="text-danger">' . rex_formatter::number($order_total) . '</span><br>';
                }

                if($payment_confirm != '') {            
                    $return .= $payment_confirm . '<br>';           
                }
                if ($payment_type != '') {
                    $return .= '<span class="badge badge-info">' . $payment_type . '</span><br>';
                }
                if ($payed) {
                    $return .= '<span class="badge badge-success">' . rex_i18n::msg('warehouse_order.payed') . '</span>';
                } else {
                    $return .= '<span class="badge badge-danger">' . rex_i18n::msg('warehouse_order.not_payed') . '</span>';
                }

                return $return;
            }
        );
    }
    public static function epYformDataListActionButtons(rex_extension_point $ep)
    {
        /** @var rex_yform_manager_table $table */
        $table = $ep->getParam('table');
        if ($table->getTableName() !== self::table()->getTableName()) {
            return;
        }

        $buttons = $ep->getSubject();

        $params = $ep->getParam('link_vars');

        unset($buttons['clone']);
        unset($buttons['edit']);
        $buttons['details'] = [
            'params' => array_merge($params, [
                'page' => 'warehouse/order/details'
            ]),
            'content' => '<i class="rex-icon rex-icon-info"></i> Details',
            'attributes' => null
        ];
        return $buttons;
    }
}
