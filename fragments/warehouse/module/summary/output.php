<?php /* UK . 420 . Bestellung Zusammenfassung - Output - Id: 15 */ ?>

<?php

if (rex::isBackend()) {
    echo '<h2>Bestellung Zusammenfassung</h2>';
    return;
} else {
    $warehouse_userdata = FriendsOfRedaxo\Warehouse\Warehouse::get_user_data();
    $cart = FriendsOfRedaxo\Warehouse\Warehouse::getCart();
//    dump($cart);
    $fragment = new rex_fragment();
    $fragment->setVar('cart', $cart);
    $fragment->setVar('warehouse_userdata', $warehouse_userdata);
    $warehouse_cart_text = $fragment->parse('warehouse/order_summary_page.php');

    $yf = new rex_yform();
    $yf->setObjectparams('form_action', rex_getUrl());
    $yf->setObjectparams('form_class', 'rex-yform wh-form summary');
    $yf->setObjectparams('form_anchor', 'formular');
    $yf->setObjectparams('form_ytemplate', 'uikit,bootstrap,classic');
    $yf->setObjectparams('error_class', 'uk-form-danger');

    $yf->setValueField('hidden', ['email', $warehouse_userdata['email']]);
    $yf->setValueField('hidden', ['firstname', $warehouse_userdata['firstname']]);
    $yf->setValueField('hidden', ['lastname', $warehouse_userdata['lastname']]);
    $yf->setValueField('hidden', ['iban', $warehouse_userdata['iban']]);
    $yf->setValueField('hidden', ['bic', $warehouse_userdata['bic']]);
    $yf->setValueField('hidden', ['direct_debit_name', $warehouse_userdata['direct_debit_name']]);
    $yf->setValueField('hidden', ['payment_type', $warehouse_userdata['payment_type']]);

    /*
      $yf->setHiddenField('email',$warehouse_userdata['email']);
      $yf->setHiddenField('firstname',$warehouse_userdata['firstname']);
      $yf->setHiddenField('lastname',$warehouse_userdata['lastname']);
     */


    $yf->setValueField('html', ['', $warehouse_cart_text]);

    $yf->setValueField('checkbox', ['agb_ok', '{{ agb_dsgvo_label|format(' . rex_getUrl(14) . ',' . rex_getUrl(15) . ') }}', '0,1', '0']);
//    $yf->setValueField('checkbox', ['dsgvo_ok', '{{ Ich habe die Datenschutzbestimmungen gelesen.|format(' . rex_getUrl(4) . ') }}', '0,1', '0']);

    $yf->setValueField('html', ['', '</div><div class="col-4_md-12 right-col relative align-center">']); // col | col

    $yf->setValueField('submit', ['send', '{{ Jetzt kaufen }}', '', '[no_db]', '', 'uk-button uk-button-primary uk-margin-top']);

    $yf->setValidateField('empty', ['agb_ok', '{{ Sie müssen die AGBs akzeptieren. }}']);
//    $yf->setValidateField('empty', ['dsgvo_ok', '{{ Sie müssen die Datenschutzbestimmungen akzeptieren. }}']);



    if (in_array($warehouse_userdata['payment_type'],['invoice','prepayment','direct_debit'])) {
        $yf->setActionField('callback', ['FriendsOfRedaxo\Warehouse\Warehouse::save_order']);
        foreach (explode(',', rex_config::get('warehouse', 'order_email')) as $email) {
            $yf->setActionField('tpl2email', [rex_config::get('warehouse', 'email_template_seller'), $email]);
        }
        $yf->setActionField('tpl2email', [rex_config::get('warehouse', 'email_template_customer'), 'email']);
        $yf->setActionField('callback', ['FriendsOfRedaxo\Warehouse\Warehouse::clear_cart']);
        $yf->setActionField('redirect', [rex_config::get('warehouse', 'thankyou_page')]);
    } elseif ($warehouse_userdata['payment_type'] == 'paypal') {
        $yf->setActionField('redirect', [rex_config::get('warehouse', 'paypal_page_start')]);
    }

}

echo $yf->getForm();
