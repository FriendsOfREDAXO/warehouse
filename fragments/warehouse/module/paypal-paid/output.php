<?php
/* Paypal Zahlung erfolgt */
// Paypal Zahlung bestätigen, Erfolg loggen, weiter leiten zur Danke-Seite
if (rex::isBackend()) {
    echo '<h2>PayPal Abschluss der Zahlung, Bestätigungsmail und Bestellmail verschicken.</h2>';
    return;
} else {
    $response = FriendsOfRedaxo\Warehouse\PayPal::ExecutePayment();
//    dump($response); exit;
    /*
    PayPalHttp\HttpResponse {#170 ▼
        +statusCode: 201
        +result: {#151 ▼
            +"id": "37G433630R1366212" // -> paypal order_id = $_GET["token"]
            +"intent": "CAPTURE"
            +"status": "COMPLETED"
            +"purchase_units": array:1 [▼
                0 => {#147 ▶}
            ]
            +"payer": {#167 ▶}
            +"create_time": "2021-08-04T10:25:05Z"
            +"update_time": "2021-08-04T10:25:15Z"
            +"links": array:1 [▼
                0 => {#169 ▶}
            ]
        }
        +headers: array:10 [▼
            "" => ""
            "Content-Type" => "application/json"
            "Content-Length" => "1653"
            "Connection" => "keep-alive"
            "Date" => "Wed, 04 Aug 2021 10"
            "Application_id" => "APP-80W284485P519543T"
            "Cache-Control" => "max-age=0, no-cache, no-store, must-revalidate"
            "Caller_acct_num" => "TYMDVHDDBLE62"
            "Paypal-Debug-Id" => "b9d0345b88d06"
            "Strict-Transport-Security" => "max-age=31536000; includeSubDomains"
        ]
    }
    */
    
    // log payment
    FriendsOfRedaxo\Warehouse\Warehouse::paypal_approved_v2($response);

    $cart = FriendsOfRedaxo\Warehouse\Warehouse::get_cart();
    $warehouse_userdata = FriendsOfRedaxo\Warehouse\Warehouse::get_user_data();
    
    $yf = new rex_yform();
    $fragment = new rex_fragment();
    $fragment->setVar('cart', $cart);
    $fragment->setVar('warehouse_userdata', $warehouse_userdata);
    
    $yf->setObjectparams('csrf_protection',false);
    $yf->setValueField('hidden', ['email', $warehouse_userdata['email']]);
    $yf->setValueField('hidden', ['firstname', $warehouse_userdata['firstname']]);
    $yf->setValueField('hidden', ['lastname', $warehouse_userdata['lastname']]);
    $yf->setValueField('hidden', ['payment_type', $warehouse_userdata['payment_type']]);
    
    foreach (explode(',', rex_config::get('warehouse', 'order_email')) as $email) {
        $yf->setActionField('tpl2email', [rex_config::get('warehouse', 'email_template_seller'), $email]);
    }
    $yf->setActionField('tpl2email', [rex_config::get('warehouse', 'email_template_customer'), 'email']);
    $yf->setActionField('callback', ['warehouse::clear_cart']);
//    $yf->setActionField('redirect', [rex_config::get('warehouse', 'thankyou_page')]);
    
    $yf->getForm();
    $yf->setObjectparams('send',1);
    $yf->executeActions();    
    
    $params = json_decode(rex_config::get('warehouse','paypal_getparams'),true);
    
//    rex_redirect(rex_config::get('warehouse','thankyou_page'));
    rex_response::sendRedirect(rex_getUrl(rex_config::get('warehouse','thankyou_page'), '', $params ?? [] , '&'));    
    // json_decode(rex_config::get('warehouse','paypal_getparams'),true)
    
}
?>
