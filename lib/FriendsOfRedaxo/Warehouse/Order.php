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

    /* Bestellnummer */
    /** @api */
    public function getOrderNo(): ?string
    {
        return $this->getValue("order_no");
    }
    /** @api */
    public function setOrderNo(string $value): self
    {
        $this->setValue("order_no", $value);
        return $this;
    }

    /* Anrede */
    /** @api */
    public function getSalutation() : ?string
    {
        return $this->getValue("salutation");
    }
    /** @api */
    public function setSalutation(mixed $value) : self
    {
        $this->setValue("salutation", $value);
        return $this;
    }
    
    /* Vorname */
    /** @api */
    public function getFirstname() : mixed
    {
        return $this->getValue("firstname");
    }
    /** @api */
    public function setFirstname(mixed $value) : self
    {
        $this->setValue("firstname", $value);
        return $this;
    }
    
    /* Nachname */
    /** @api */
    public function getLastname() : mixed
    {
        return $this->getValue("lastname");
    }
    /** @api */
    public function setLastname(mixed $value) : self
    {
        $this->setValue("lastname", $value);
        return $this;
    }
    
    /* Firma */
    /** @api */
    public function getCompany() : mixed
    {
        return $this->getValue("company");
    }
    /** @api */
    public function setCompany(mixed $value) : self
    {
        $this->setValue("company", $value);
        return $this;
    }
    
    /* Adresse */
    /** @api */
    public function getAddress() : mixed
    {
        return $this->getValue("address");
    }
    /** @api */
    public function setAddress(mixed $value) : self
    {
        $this->setValue("address", $value);
        return $this;
    }
    
    /* PLZ */
    /** @api */
    public function getZip() : mixed
    {
        return $this->getValue("zip");
    }
    /** @api */
    public function setZip(mixed $value) : self
    {
        $this->setValue("zip", $value);
        return $this;
    }
    
    /* Stadt */
    /** @api */
    public function getCity() : mixed
    {
        return $this->getValue("city");
    }
    /** @api */
    public function setCity(mixed $value) : self
    {
        $this->setValue("city", $value);
        return $this;
    }
    
    /* Land */
    /** @api */
    public function getCountry() : ?string
    {
        return $this->getValue("country");
    }
    /** @api */
    public function setCountry(mixed $value) : self
    {
        $this->setValue("country", $value);
        return $this;
    }
    
    /* E-Mail */
    /** @api */
    public function getEmail() : ?string
    {
        return $this->getValue("email");
    }
    /** @api */
    public function setEmail(mixed $value) : self
    {
        $this->setValue("email", $value);
        return $this;
    }
    
    /* Erstellungsdatum */
    /** @api */
    public function getCreatedate() : ?string
    {
        return $this->getValue("createdate");
    }

    // TODO: IntldateFromatter verwenden
    /** @api */
    public function getCreatedateFormatted() : ?string
    {
        return date('d.m.Y H:i', strtotime($this->getValue("createdate")));
    }

    /** @api */
    public function setCreatedate(string $value) : self
    {
        $this->setValue("createdate", $value);
        return $this;
    }
    
    /* PayPal-ID */
    /** @api */
    public function getPaypalId() : mixed
    {
        return $this->getValue("paypal_id");
    }
    /** @api */
    public function setPaypalId(mixed $value) : self
    {
        $this->setValue("paypal_id", $value);
        return $this;
    }
    
    /* Zahlungs-ID */
    /** @api */
    public function getPaymentId() : ?string
    {
        return $this->getValue("payment_id");
    }
    /** @api */
    public function setPaymentId(mixed $value) : self
    {
        $this->setValue("payment_id", $value);
        return $this;
    }
    
    /* PayPal-Bestätigungstoken */
    /** @api */
    public function getPaypalConfirmToken() : ?string
    {
        return $this->getValue("paypal_confirm_token");
    }
    /** @api */
    public function setPaypalConfirmToken(mixed $value) : self
    {
        $this->setValue("paypal_confirm_token", $value);
        return $this;
    }
    
    /* Zahlungsbestätigung */
    /** @api */
    public function getPaymentConfirm() : ?string
    {
        return $this->getValue("payment_confirm");
    }
    /** @api */
    public function setPaymentConfirm(mixed $value) : self
    {
        $this->setValue("payment_confirm", $value);
        return $this;
    }
                
    /* Bestell-JSON */
    /** @api */
    public function getOrderJson(bool $asArray = true) : mixed
    {
        if ($asArray) {
            return json_decode($this->getValue("order_json"), true);
        }
        return $this->getValue("order_json");
    }
    /** @api */
    public function setOrderJson(string $value) : self
    {
        $this->setValue("order_json", $value);
        return $this;
    }
                
    /* Bestellsumme */
    /** @api */
    public function getOrderTotal() : ?float
    {
        return $this->getValue("order_total");
    }
    /** @api */
    public function setOrderTotal(float $value) : self
    {
        $this->setValue("order_total", $value);
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
        $this->setValue("ycom_user_id", $ycom_user_id);
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
        return (bool) $this->getValue("is_read");
    }
    /** @api */
    public function setIsRead(bool $value) : self
    {
        $this->setValue("is_read", $value ? 1 : 0);
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
                $params['data_id'] = $a['list']->getValue('id');
                $params['func'] = 'edit';

                $return = '';
                /** @var rex_yform_manager_dataset $values */
                $values = $a['list'];
                // Anrede, Name, Adresse in einer Zelle
                if ($values->getValue('company') != '') {
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
            $status_name,
            'custom',
            static function ($a) {
                /** @var rex_yform_manager_dataset $values */
                $values = $a['list'];
                $is_read = (bool) $values->getValue('is_read');
                
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
                $order_total = $list->getValue('order_total');
                $payment_confirm = $list->getValue('payment_confirm');
                $payment_type = $list->getValue('payment_type');
                $payed = $list->getValue('payed');

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
        $sum += (float) $this->getValue('shipping_cost');
        $sum -= (float) $this->getValue('discount');
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
}
