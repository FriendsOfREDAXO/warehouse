<?php

namespace FriendsOfRedaxo\Warehouse;

use rex_addon;
use rex_config;
use rex_csrf_token;
use rex_formatter;
use rex_i18n;
use rex_response;
use rex_url;
use rex_ycom_auth;
use rex_yform;
use rex_yform_list;
use rex_yform_manager_collection;
use rex_yform_manager_dataset;
use rex_yform_manager_table;

class Order extends rex_yform_manager_dataset
{
    // Single point of truth for field names
    public const string ID = 'id';
    public const string ORDER_NO = 'order_no';
    public const string SALUTATION = 'salutation';
    public const string FIRSTNAME = 'firstname';
    public const string LASTNAME = 'lastname';
    public const string COMPANY = 'company';
    public const string ADDRESS = 'address';
    public const string ZIP = 'zip';
    public const string CITY = 'city';
    public const string COUNTRY = 'country';
    public const string EMAIL = 'email';
    public const string CREATEDATE = 'createdate';
    public const string PAYPAL_ID = 'paypal_id';
    public const string PAYMENT_ID = 'payment_id';
    public const string PAYPAL_CONFIRM_TOKEN = 'paypal_confirm_token';
    public const string PAYMENT_CONFIRM = 'payment_confirm';
    public const string ORDER_TEXT = 'order_text';
    public const string ORDER_JSON = 'order_json';
    public const string ORDER_TOTAL = 'order_total';
    public const string YCOM_USER_ID = 'ycom_user_id';
    public const string PAYMENT_TYPE = 'payment_type';
    public const string PAYED = 'payed';
    public const string IMPORTED = 'imported';
    public const string IS_READ = 'is_read';
    public const string UPDATEDATE = 'updatedate';
    public const string HASH = 'hash';
    public const string CUSTOM_ORDER_ID = 'custom_order_id';
    public const string PAYMENT_STATUS = 'payment_status';
    public const string SHIPPING_STATUS = 'shipping_status';
    public const string CART_TOTAL_TAX = 'cart_total_tax';
    public const string CURRENCY = 'currency';
    public const string NOTE = 'note';
    public const string ORDER_DATE = 'order_date';
    public const string ORDER_NUMBER = 'order_number';
    public const string PAYDATE = 'paydate';
    public const string SHIPPING_COST = 'shipping_cost';
    public const string SHIPPING_TYPE = 'shipping_type';
    public const string SUB_TOTAL = 'sub_total';
    public const string SUB_TOTAL_NETTO = 'sub_total_netto';
    public const string USER_E_NUMBER = 'user_e_number';
    public const string UST = 'ust';
    public const string VERWENDUNGSZWECK = 'verwendungszweck';
    public const string WITH_TAX = 'with_tax';
    public const string DISCOUNT = 'discount';
    public const string STATUS = 'status';
    public const string PAYMENT_TYPE_LABELS = 'payment_type_LABELS';
    public const string SHIPPMENT_TYPE = 'shippment_type'; // typo in database field

    public const array FIELD_CONFIG = [
        self::ID => [],
        self::ORDER_NO => [],
        self::SALUTATION => [],
        self::FIRSTNAME => [],
        self::LASTNAME => [],
        self::COMPANY => [],
        self::ADDRESS => [],
        self::ZIP => [],
        self::CITY => [],
        self::COUNTRY => [],
        self::EMAIL => [],
        self::CREATEDATE => [],
        self::PAYPAL_ID => [],
        self::PAYMENT_ID => [],
        self::PAYPAL_CONFIRM_TOKEN => [],
        self::PAYMENT_CONFIRM => [],
        self::ORDER_TEXT => [],
        self::ORDER_JSON => [],
        self::ORDER_TOTAL => [],
        self::YCOM_USER_ID => [],
        self::PAYMENT_TYPE => [],
        self::PAYED => [],
        self::IMPORTED => [],
        self::IS_READ => [],
        self::UPDATEDATE => [],
        self::HASH => [],
        self::CUSTOM_ORDER_ID => [],
        self::PAYMENT_STATUS => [],
        self::SHIPPING_STATUS => [],
        self::CART_TOTAL_TAX => [],
        self::CURRENCY => [],
        self::NOTE => [],
        self::ORDER_DATE => [],
        self::ORDER_NUMBER => [],
        self::PAYDATE => [],
        self::SHIPPING_COST => [],
        self::SHIPPING_TYPE => [],
        self::SUB_TOTAL => [],
        self::SUB_TOTAL_NETTO => [],
        self::USER_E_NUMBER => [],
        self::UST => [],
        self::VERWENDUNGSZWECK => [],
        self::WITH_TAX => [],
        self::DISCOUNT => [],
        self::STATUS => [],
        self::PAYMENT_TYPE_LABELS => [],
        self::SHIPPMENT_TYPE => [], // typo in database field
    ];

    /* Bestellnummer */
    /** @api */
    public function getOrderNo(): ?string
    {
        return $this->getValue(self::ORDER_NO);
    }
    /** @api */
    public function setOrderNo(string $value): self
    {
        $this->setValue(self::ORDER_NO, $value);
        return $this;
    }

    /* Anrede */
    /** @api */
    public function getSalutation() : ?string
    {
        return $this->getValue(self::SALUTATION);
    }
    /** @api */
    public function setSalutation(mixed $value) : self
    {
        $this->setValue(self::SALUTATION, $value);
        return $this;
    }
    
    /* Vorname */
    /** @api */
    public function getFirstname() : mixed
    {
        return $this->getValue(self::FIRSTNAME);
    }
    /** @api */
    public function setFirstname(mixed $value) : self
    {
        $this->setValue(self::FIRSTNAME, $value);
        return $this;
    }
    
    /* Nachname */
    /** @api */
    public function getLastname() : mixed
    {
        return $this->getValue(self::LASTNAME);
    }
    /** @api */
    public function setLastname(mixed $value) : self
    {
        $this->setValue(self::LASTNAME, $value);
        return $this;
    }
    
    /* Firma */
    /** @api */
    public function getCompany() : mixed
    {
        return $this->getValue(self::COMPANY);
    }
    /** @api */
    public function setCompany(mixed $value) : self
    {
        $this->setValue(self::COMPANY, $value);
        return $this;
    }
    
    /* Adresse */
    /** @api */
    public function getAddress() : mixed
    {
        return $this->getValue(self::ADDRESS);
    }
    /** @api */
    public function setAddress(mixed $value) : self
    {
        $this->setValue(self::ADDRESS, $value);
        return $this;
    }
    
    /* PLZ */
    /** @api */
    public function getZip() : mixed
    {
        return $this->getValue(self::ZIP);
    }
    /** @api */
    public function setZip(mixed $value) : self
    {
        $this->setValue(self::ZIP, $value);
        return $this;
    }
    
    /* Stadt */
    /** @api */
    public function getCity() : mixed
    {
        return $this->getValue(self::CITY);
    }
    /** @api */
    public function setCity(mixed $value) : self
    {
        $this->setValue(self::CITY, $value);
        return $this;
    }
    
    /* Land */
    /** @api */
    public function getCountry() : ?string
    {
        return $this->getValue(self::COUNTRY);
    }
    /** @api */
    public function setCountry(mixed $value) : self
    {
        $this->setValue(self::COUNTRY, $value);
        return $this;
    }
    
    /* E-Mail */
    /** @api */
    public function getEmail() : ?string
    {
        return $this->getValue(self::EMAIL);
    }
    /** @api */
    public function setEmail(mixed $value) : self
    {
        $this->setValue(self::EMAIL, $value);
        return $this;
    }
    
    /* Erstellungsdatum */
    /** @api */
    public function getCreatedate() : ?string
    {
        return $this->getValue(self::CREATEDATE);
    }

    // TODO: IntldateFromatter verwenden
    /** @api */
    public function getCreatedateFormatted() : ?string
    {
        return date('d.m.Y H:i', strtotime($this->getValue(self::CREATEDATE)));
    }

    /** @api */
    public function setCreatedate(string $value) : self
    {
        $this->setValue(self::CREATEDATE, $value);
        return $this;
    }
    
    /* PayPal-ID */
    /** @api */
    public function getPaypalId() : mixed
    {
        return $this->getValue(self::PAYPAL_ID);
    }
    /** @api */
    public function setPaypalId(mixed $value) : self
    {
        $this->setValue(self::PAYPAL_ID, $value);
        return $this;
    }
    
    /* Zahlungs-ID */
    /** @api */
    public function getPaymentId() : ?string
    {
        return $this->getValue(self::PAYMENT_ID);
    }
    /** @api */
    public function setPaymentId(mixed $value) : self
    {
        $this->setValue(self::PAYMENT_ID, $value);
        return $this;
    }
    
    /* PayPal-Bestätigungstoken */
    /** @api */
    public function getPaypalConfirmToken() : ?string
    {
        return $this->getValue(self::PAYPAL_CONFIRM_TOKEN);
    }
    /** @api */
    public function setPaypalConfirmToken(mixed $value) : self
    {
        $this->setValue(self::PAYPAL_CONFIRM_TOKEN, $value);
        return $this;
    }
    
    /* Zahlungsbestätigung */
    /** @api */
    public function getPaymentConfirm() : ?string
    {
        return $this->getValue(self::PAYMENT_CONFIRM);
    }
    /** @api */
    public function setPaymentConfirm(mixed $value) : self
    {
        $this->setValue(self::PAYMENT_CONFIRM, $value);
        return $this;
    }
                
    /* Bestell-JSON */
    /** @api */
    public function getOrderJson(bool $asArray = true) : mixed
    {
        if ($asArray) {
            return json_decode($this->getValue(self::ORDER_JSON), true);
        }
        return $this->getValue(self::ORDER_JSON);
    }
    /** @api */
    public function setOrderJson(string $value) : self
    {
        $this->setValue(self::ORDER_JSON, $value);
        return $this;
    }
                
    /* Bestellsumme */
    /** @api */
    public function getOrderTotal() : ?float
    {
        return $this->getValue(self::ORDER_TOTAL);
    }
    /** @api */
    public function setOrderTotal(float $value) : self
    {
        $this->setValue(self::ORDER_TOTAL, $value);
        return $this;
    }
                
    /* YCom-Benutzer-ID */
    /** @api */
    public function getYcomUser() : ?rex_yform_manager_dataset
    {
        return $this->getRelatedDataset("ycom_user_id");
    }

    public function setYComUser(int $ycom_user_id) : self
    {
        $this->setValue(self::YCOM_USER_ID, $ycom_user_id);
        return $this;
    }
    
    /**
     * @return rex_yform_manager_collection<self>|null
     */
    public static function findByYComUserId(int $ycom_user_id = null) : ?rex_yform_manager_collection
    {
        if ($ycom_user_id === null) {
            $ycom_user = rex_ycom_auth::getUser();
            $ycom_user_id = $ycom_user?->getId();
        }
        $data = self::query()
                ->alias('orders')
                ->where('orders.ycom_user_id', $ycom_user_id)
                ->orderBy('createdate', 'desc')
        ;
        return $data->find();
    }
    
    /* Gelesen-Status */
    /** @api */
    public function getIsRead() : bool
    {
        return (bool) $this->getValue(self::IS_READ);
    }
    /** @api */
    public function setIsRead(bool $value) : self
    {
        $this->setValue(self::IS_READ, $value ? 1 : 0);
        return $this;
    }
    
    public static function findByUuid(string $uuid) : ?self
    {
        return self::query()->where('uuid', $uuid)->findOne();
       
    }

    /**
     * @param rex_extension_point<mixed> $ep
     * @return void
     */
    public static function epYformDataList(rex_extension_point $ep): void
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
        
        // Status-Spalte für Gelesen/Ungelesen
        $status_name = rex_i18n::msg('warehouse_order.is_read');
        $list->addColumn($status_name, '', 1);

        $list->setColumnFormat(
            $name,
            'custom',
            static function ($a) {
                $_csrf_key = self::table()->getCSRFKey();
                $token = \rex_csrf_token::factory($_csrf_key)->getUrlParams();

                $params = [];
                $params['table_name'] = self::table()->getTableName();
                $params['rex_yform_manager_popup'] = '0';
                $params['_csrf_token'] = $token['_csrf_token'] ?? '';
                $params['data_id'] = $a['list']->getValue(self::ID);
                $params['func'] = 'edit';

                $return = '';
                /** @var rex_yform_manager_dataset $values */
                $values = $a['list'];
                // Anrede, Name, Adresse in einer Zelle
                if ($values->getValue(self::COMPANY) != '') {
                    $return .= $values->getValue(self::COMPANY) . '<br>';
                }
                $return .= $values->getValue(self::SALUTATION) . ' ' . $values->getValue(self::FIRSTNAME) . ' ' . $values->getValue(self::LASTNAME) . '<br>';
                $return .= $values->getValue(self::ADDRESS) . '<br>';
                $return .= $values->getValue(self::ZIP) . ' ' . $values->getValue(self::CITY) . '<br>';
                $return .= $values->getValue(self::COUNTRY) . '<br>';

                return '<div class="text-nowrap">' . $return . '</div>';

            },
        );

        $list->setColumnFormat(
            $status_name,
            'custom',
            static function ($a) {
                /** @var rex_yform_manager_dataset $values */
                $values = $a['list'];
                $is_read = (bool) $values->getValue(self::IS_READ);
                
                // Zeige schwarzen Punkt für ungelesene Bestellungen
                if (!$is_read) {
                    return '<span style="font-size: 1.2em; color: #000;">•</span>';
                }
                return '';
            }
        );

        $list->setColumnFormat(
            'email',
            'custom',
            static function ($a) {
                if ($a !== '') {
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
            'order_no',
            'custom',
            static function ($a) {
                $order_no = $a['value'];
                if (!empty($order_no)) {
                    return '<code>' . htmlspecialchars($order_no) . '</code>';
                }
                return '<em class="text-muted">—</em>';
            },
        );
        
        $list->setColumnFormat(
            'createdate',
            'custom',
            static function ($a) {
                return rex_formatter::intlDate($a['value']);
            },
        );

        // Set label for order number column
        $list->setColumnLabel('order_no', rex_i18n::msg('warehouse_order.order_no'));

        if (rex_addon::get('ycom')->isAvailable()) {
            $list->setColumnLabel('ycom_user_id', rex_i18n::msg('warehouse_order.ycom_user'));
            $list->setColumnSortable('ycom_user_id', true);
                
            $list->setColumnFormat(
                'ycom_user_id',
                'custom',
                static function ($a) {
                    if ($a['value'] > 0 && rex_addon::get('ycom')->isAvailable()) {
                        $user = \rex_ycom_user::get($a['value']);
                        
                        if ($user === null && $a['value'] > 0) {
                            return '<i class="rex-icon rex-icon-user text-warning"></i>';
                        }

                        if ($user === null) {
                            return '<i class="rex-icon rex-icon-user text-muted" style="opacity: 0.3"></i>';
                        }

                        $user_status = $user->getValue('status');

                        $user_status_class = '';
                        if ($user_status == 0) {
                            $user_status_class = 'text-info';
                        } elseif ($user_status < 0) {
                            $user_status_class = 'text-danger';
                        } elseif ($user_status > 0) {
                            $user_status_class = 'text-success';
                        }

                        // index.php?page=yform/manager/data_edit&table_name=rex_ycom_user&list=45e18d03&sort=&sorttype=&start=0&_csrf_token=Qk3DRM8nOTKy8pFY9H7jA8qL7PQAORVL0hYGfUmEtw8&rex_yform_manager_popup=0&data_id=1&func=edit&45e18d03_start=0
                        return '<a href="' . rex_url::backendController(['page' => 'yform/manager/data_edit', 'table_name' => 'rex_ycom_user', '_csrf_token' => \rex_csrf_token::factory('ycom_user')->getUrlParams()['_csrf_token'] ?? '', 'rex_yform_manager_popup' => 0, 'data_id' => $user->getId(), 'func' => 'edit']) . '"><i class="rex-icon rex-icon-user '.$user_status_class.'"></i></a>';
                    }
                    return '<i class="rex-icon rex-icon-user text-muted" style="opacity: 0.3"></i>';
                }
            );
        } else {
            $list->removeColumn('ycom_user_id');
        }

        $list->removeColumn('payment_confirm');
        $list->removeColumn('payment_type');

        $list->setColumnFormat(
            'order_total',
            'custom',
            static function ($a) {
                /** @var rex_yform_manager_dataset $list */
                $list = $a['list'];
                $order_total = $list->getValue(self::ORDER_TOTAL);
                $payment_confirm = $list->getValue(self::PAYMENT_CONFIRM);
                $payment_type = $list->getValue(self::PAYMENT_TYPE);
                $payed = $list->getValue(self::PAYED);

                $return = '';

                if ($order_total > 0) {
                    $return .= '<span class="">' . Warehouse::formatCurrency($order_total) .  '</span><br>';
                } else {
                    $return .= '<span class="text-danger">' . Warehouse::formatCurrency($order_total) . '</span><br>';
                }

                if ($payment_confirm != '') {
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
    /**
     * @param rex_extension_point<mixed> $ep
     * @return array<string, array<string, mixed>>
     */
    public static function epYformDataListActionButtons(rex_extension_point $ep): array
    {
        /** @var rex_yform_manager_table $table */
        $table = $ep->getParam('table');
        if ($table->getTableName() !== self::table()->getTableName()) {
            return $ep->getSubject();
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

    public function getUrl(string $profile = 'order-id'): string
    {
        return rex_getUrl(null, null, [$profile => $this->getId()]);
    }

    public function getBackendUrl() :string
    {
        $params = [];
        $params['table_name'] = self::table()->getTableName();
        $params['rex_yform_manager_popup'] = '0';
        $params['_csrf_token'] = rex_csrf_token::factory(self::table()->getCSRFKey())->getUrlParams()['_csrf_token'] ?? '';
        $params['data_id'] = $this->getId();
        $params['func'] = 'edit';

        return rex_url::backendPage('warehouse/order/list', $params);
    }

    public function getBackendDetailsUrl() :string
    {
        $params = [];
        $params['table_name'] = self::table()->getTableName();
        $params['rex_yform_manager_popup'] = '0';
        $params['_csrf_token'] = rex_csrf_token::factory(self::table()->getCSRFKey())->getUrlParams()['_csrf_token'] ?? '';
        $params['data_id'] = $this->getId();
        $params['func'] = 'details';

        return rex_url::backendPage('warehouse/order/details', $params);
    }

    public static function getBackendIcon(bool $label = false) :string
    {
        if ($label) {
            return '<i class="rex-icon fa-shopping-cart"></i> ' . rex_i18n::msg('warehouse_order.icon_label');
        }
        return '<i class="rex-icon fa-shopping-cart"></i>';
    }
    
    public function sendEmails(bool $send_redirect = true) :void
    {
        $yform = new rex_yform();

        $yform->setObjectparams('csrf_protection', false);
        $yform->setValueField('hidden', ['order_id', $this->getId()]);

        foreach (explode(',', Warehouse::getConfig('order_email')) as $email) {
            $yform->setActionField('tpl2email', [Warehouse::getConfig('email_template_seller'), $email]);
        }
        $yform->setActionField('tpl2email', [Warehouse::getConfig('email_template_customer'), 'email']);
        $yform->setActionField('callback', ['Cart::empty']);

        $yform->getForm();
        $yform->setObjectparams('send', 1);
        $yform->executeActions();

        if ($send_redirect) {
            rex_response::sendRedirect(rex_getUrl(Warehouse::getConfig('thankyou_page'), '', json_decode(rex_config::get('warehouse', 'paypal_getparams'), true), '&'));
        }
    }

    /**
     * Gibt die Zwischensumme (Summe aller Artikel) im gewünschten Modus zurück.
     * @param 'net'|'gross'|null $mode 'net' oder 'gross' (optional, sonst globaler Modus)
     * @return float
     */
    public function getOrderSubTotal(?string $mode = null): float
    {
        $items = json_decode($this->getOrderJson(), true)['cart'] ?? [];
        $sum = 0;
        foreach ($items as $item) {
            $article = Article::get($item['article_id']);
            $variant = isset($item['variant_id']) && $item['variant_id'] ? ArticleVariant::get($item['variant_id']) : null;
            if ($variant) {
                $price = $variant->getPrice($mode);
            } else {
                $price = $article ? $article->getPrice($mode) : 0;
            }
            $sum += (float)$price * (int)$item['amount'];
        }
        return $sum;
    }

    /**
     * Gibt die Steuer für die Order zurück (Summe aller Einzelsteuern).
     * @return float
     */
    /**
     * Gibt die Steuer für die Order zurück (Summe aller Einzelsteuern).
     * @return float
     */
    public function getOrderTaxTotal(): float
    {
        return $this->getOrderTaxTotalByMode();
    }
    
    /**
     * Gibt die Zwischensumme (Summe aller Artikel) im gewünschten Modus zurück.
     * @param 'net'|'gross'|null $mode 'net' oder 'gross' (optional, sonst globaler Modus)
     * @return float
     */
    public function getOrderSubTotalByMode(?string $mode = null): float
    {
        $items = json_decode($this->getOrderJson(), true)['cart'] ?? [];
        $sum = 0;
        foreach ($items as $item) {
            $article = Article::get($item['article_id']);
            $variant = isset($item['variant_id']) && $item['variant_id'] ? ArticleVariant::get($item['variant_id']) : null;
            if ($variant) {
                $price = $variant->getPrice($mode);
            } else {
                $price = $article ? $article->getPrice($mode) : 0;
            }
            $sum += (float)$price * (int)$item['amount'];
        }
        return $sum;
    }

    /**
     * Gibt die Steuer für die Order zurück (Summe aller Einzelsteuern).
     * @return float
     */
    public function getOrderTaxTotalByMode(): float
    {
        $items = json_decode($this->getOrderJson(), true)['cart'] ?? [];
        $sum = 0;
        foreach ($items as $item) {
            $article = Article::get($item['article_id']);
            $variant = isset($item['variant_id']) && $item['variant_id'] ? ArticleVariant::get($item['variant_id']) : null;
            $net = $variant ? $variant->getPrice('net') : ($article ? $article->getPrice('net') : 0);
            $gross = $variant ? $variant->getPrice('gross') : ($article ? $article->getPrice('gross') : 0);
            $sum += (($gross - $net) * (int)$item['amount']);
        }
        return round($sum, 2);
    }

    /**
     * Gibt die Gesamtsumme (inkl. Versand, Rabatt) im gewünschten Modus zurück.
     * @param 'net'|'gross'|null $mode
     */
    public function getOrderTotalByMode(?string $mode = null): float
    {
        $sum = (float) $this->getOrderSubTotalByMode($mode);
        $sum += (float) $this->getValue(self::SHIPPING_COST);
        $sum -= (float) $this->getValue(self::DISCOUNT);
        return $sum;
    }

    /**
     * Erstellt eine PDF-Rechnung zur Bestellung und legt sie im Addon-Data-Ordner ab.
     * Gibt den Pfad zur erzeugten PDF-Datei zurück oder null bei Fehler.
     */
    public function createInvoicePdf(): ?string
    {
        if (!\rex_addon::get('pdfout')->isAvailable()) {
            return null;
        }
        $addonDataPath = \rex_path::addonData('warehouse', 'invoice_' . $this->getId() . '.pdf');
        $fragment = new \rex_fragment(['order' => $this]);
        $html = $fragment->parse('warehouse/backend/invoice.php');
        $pdf = new \FriendsOfRedaxo\PdfOut\PdfOut();
        $pdf->setName('Rechnung_' . $this->getId())
            ->setHtml($html)
            ->setSaveToPath($addonDataPath)
            ->setAttachment(false)
            ->setSaveAndSend(true)
            ->run();
        return $addonDataPath;
    }

    /**
     * Erstellt einen PDF-Lieferschein zur Bestellung und legt ihn im Addon-Data-Ordner ab.
     * Gibt den Pfad zur erzeugten PDF-Datei zurück oder null bei Fehler.
     */
    public function createDeliveryNotePdf(): ?string
    {
        if (!\rex_addon::get('pdfout')->isAvailable()) {
            return null;
        }
        $addonDataPath = \rex_path::addonData('warehouse', 'delivery_note_' . $this->getId() . '.pdf');
        $fragment = new \rex_fragment(['order' => $this]);
        $html = $fragment->parse('warehouse/backend/delivery_note.php');
        $pdf = new \FriendsOfRedaxo\PdfOut\PdfOut();
        $pdf->setName('Lieferschein_' . $this->getId())
            ->setHtml($html)
            ->setSaveToPath($addonDataPath)
            ->setAttachment(false)
            ->setSaveAndSend(true)
            ->run();
        return $addonDataPath;
    }



    /**
     * @return array<string, string>
     */
    public static function getPaymentStatusOptions(): array
    {
        $options = [];
        foreach (Payment::getPaymentStatusOptions() as $key => $label) {
            $options[$key] = rex_i18n::msg($label);
        }
        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function getShippingStatusOptions(): array
    {
        $options = [];
        foreach (Shipping::getShippingStatusOptions() as $key => $label) {
            $options[$key] = rex_i18n::msg($label);
        }
        return $options;
    }
}
