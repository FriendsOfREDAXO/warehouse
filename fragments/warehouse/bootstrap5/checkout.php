<?php

use FriendsOfRedaxo\Warehouse\Customer;
use FriendsOfRedaxo\Warehouse\Warehouse;

$customer = Customer::getCurrent() ?? Customer::get(1);
$customerAddress = $customer?->getAddress();
$customer_shipping_address = $customer?->getShippingAddress();
$current_payment_types = Warehouse::getAllowedPaymentOptions();

$yform = new rex_yform();

// $yf->setObjectparams('form_action',rex_getUrl($article_id));
$yform->setObjectparams('form_wrap_class', 'yform-wrap');
// $yf->setObjectparams('debug',0);
$yform->setObjectparams('form_ytemplate','bootstrap5,bootstrap');
$yform->setObjectparams('form_class', 'rex-yform warehouse_checkout');
$yform->setObjectparams('form_anchor', 'form-checkout');

$yform->setValueField('html',['','<section>']);
$yform->setValueField('text',['firstname','Vorname*',$customer?->getFirstname(),'',['required'=>'required']]);
$yform->setValidateField('empty',['firstname','Bitte füllen Sie alle markierten Felder aus']);

$yform->setValueField('text',['lastname','Nachname*',$customer?->getLastname(),'',['required'=>'required']]);
$yform->setValueField('text',['company','Firma',$customer?->getCompany(),'']);
$yform->setValueField('text',['department','Abteilung',$customer?->getDepartment(),'']);
$yform->setValueField('text',['address','Adresse*',$customer?->getAddress(),'',['required'=>'required']]);
$yform->setValueField('text',['zip','PLZ*',$customer?->getZip(),'',['required'=>'required']]);
$yform->setValueField('text',['city','Ort*',$customer?->getCity(),'',['required'=>'required']]);

$yform->setValueField('text',['email','E-Mail*',$customer?->getEmail(),'',['required'=>'required']]);

$yform->setValidateField('empty',['email','Bitte füllen Sie alle markierten Felder aus']);
$yform->setValidateField('type',['email','email','Bitte geben Sie eine gültige E-Mail Adresse ein']);

$yform->setValueField('text',['phone','Telefon',$customer?->getPhone(),'']);

$yform->setValueField('text',['to_name','Vorname',$customer_shipping_address?->getName(),'']);
$yform->setValueField('text',['to_company','Firma',$customer_shipping_address?->getCompany(),'']);
$yform->setValueField('text',['to_address','Adresse',$customer_shipping_address?->getStreet(),'']);
$yform->setValueField('text',['to_zip','PLZ',$customer_shipping_address?->getZip(),'']);
$yform->setValueField('text',['to_city','Ort',$customer_shipping_address?->getCity(),'']);

$yform->setValueField('textarea',['note','Bemerkung','','']);

if (count($current_payment_types) > 1) {
    $yform->setValueField('choice',["payment_type", Warehouse::getLabel('payment_type'),json_encode($current_payment_types),1,0]);
    $yform->setValidateField('empty',['payment_type','Bitte füllen Sie alle markierten Felder aus']);
} else {
    $yform->setValueField('html',['',Warehouse::getLabel('payment_type')]);
    $yform->setValueField('hidden',['payment_type',array_values($current_payment_types)[0]]);
}

if (count($current_payment_types) > 1) {
    if (in_array('direct_debit',$current_payment_types)) {
            $yform->setValueField('html',['',Warehouse::getLabel('direct_debit')]);
            $yform->setValueField('text',['direct_debit_name','Ggf. abweichender Kontoinhaber','','']);
            $yform->setValueField('text',['iban','IBAN*','','']);
            $yform->setValueField('text',['bic','BIC*','','']);
    }

}
$yform->setValueField('submit_once',['send',Warehouse::getLabel('next'),'','','','button']);
$yform->setValueField('html',['','</section>']);

$yform->setActionField('callback', ['FriendsOfRedaxo\Warehouse\Warehouse::saveCustomerInSession']);
$yform->setActionField('redirect',[rex_config::get('warehouse','order_page')]);

$form = $yform->getForm();

$fragment = new rex_fragment();
$fragment->setVar('form',$form);
echo $fragment->parse('warehouse/bootstrap5/checkout/checkout_page.php');
?>

<style nonce="<?= rex_response::getNonce() ?>">
    input#accordion_switcher + .accordion {
        display: none;
    }
    input#accordion_switcher:checked + .accordion {
        display: block;
    }
    #accordion_switcher_label {
        text-decoration: underline;
        cursor: pointer;
    }
    #direct_debit_box {
        display: none;
    }
</style>
<script type="text/javascript" nonce="<?= rex_response::getNonce() ?>">
    $(function() {
        $('#payment_box input').each(function() {
            if ($(this).val() == 'direct_debit' && $(this).prop('checked')) {                
                $('#direct_debit_box').show();            
            }
        });
        $('#payment_box').on('change','input',function() {
            $('#direct_debit_box').hide();
            if ($(this).val() == 'direct_debit') {
                $('#direct_debit_box').show();                
            }
        }); 
    });

</script>
