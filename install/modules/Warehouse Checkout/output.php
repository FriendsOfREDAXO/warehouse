<?php
$js = <<<EOT
<style>
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
<script type="text/javascript">
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
//            console.log($(this).val());
        }); 
    });

</script>
EOT;

rex::setProperty('js',$js.rex::getProperty('js',''));
?>

<?php /* Checkout - Adresseingabe - Output */

$userdata = warehouse::get_user_data();
$userdata = warehouse::ensure_userdata_fields($userdata);

// dump($userdata);

$article_id = "REX_ARTICLE_ID";

$current_payment_types = warehouse::get_available_payment_types();
// dump($current_payment_types);
       
$yf = new rex_yform();



$yf->setObjectparams('form_action',rex_getUrl($article_id));
// $yf->setObjectparams('form_showformafterupdate', 0);
$yf->setObjectparams('form_wrap_class', 'yform wh-form');
$yf->setObjectparams('debug',0);
$yf->setObjectparams('form_ytemplate','uikit,bootstrap,classic');
$yf->setObjectparams('form_class', 'uk-form rex-yform warehouse_checkout');
$yf->setObjectparams('form_anchor', 'rex-yform');

$yf->setValueField('html',['','<section>
    <div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid" uk-grid="margin: uk-margin-small">
    <div>
']);
$yf->setValueField('text',['firstname','Vorname*',$userdata['firstname'],'[no_db]',['required'=>'required']]);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['lastname','Nachname*',$userdata['lastname'],'[no_db]',['required'=>'required']]);
$yf->setValueField('html',['','</div><div>']);
// $yf->setValueField('text',['birthdate','Geburtsdatum*',$userdata['birthdate'],'[no_db]',['required'=>'required']]);
// $yf->setValueField('html',['','</div><div class="uk-grid-margin uk-first-column">']);
$yf->setValueField('text',['company','Firma',$userdata['company'],'[no_db]']);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['department','Abteilung',$userdata['department'],'[no_db]']);
$yf->setValueField('html',['','</div><div class="uk-grid-margin">']);
$yf->setValueField('text',['address','Adresse*',$userdata['address'],'[no_db]',['required'=>'required']]);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['zip','PLZ*',$userdata['zip'],'[no_db]',['required'=>'required']]);
$yf->setValueField('html',['','</div><div class="uk-grid-margin">']);
$yf->setValueField('text',['city','Ort*',$userdata['city'],'[no_db]',['required'=>'required']]);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('choice',['country','Land',rex_config::get('warehouse','country_codes'),0,0]);
// $yf->setValueField('text',['country','Land',$userdata['country'],'[no_db]',['required'=>'required']]);
$yf->setValueField('html',['','</div><div class="uk-grid-margin">']);
$yf->setValueField('text',['email','E-Mail*',$userdata['email'],'[no_db]',['required'=>'required']]);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['phone','Telefon',$userdata['phone'],'[no_db]']);
$yf->setValueField('html',['','</div>']); // 

$yf->setValueField('html',['','<div class="uk-grid-margin">']);
$yf->setValueField('html',['','<button uk-toggle="target: #separate_delivery_address" type="button" class="uk-button">Abweichende Lieferadresse</button>']);
$yf->setValueField('html',['','</div>']);
$yf->setValueField('html',['','</div>']);

$yf->setValueField('html',['','<div id="separate_delivery_address" class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid" uk-grid="margin: uk-margin-small" hidden>']);

$yf->setValueField('html',['','<div>']);
$yf->setValueField('text',['to_firstname','Vorname',$userdata['to_firstname'],'[no_db]']);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['to_lastname','Nachname',$userdata['to_lastname'],'[no_db]']);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['to_company','Firma',$userdata['to_company'],'[no_db]']);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['to_department','Abteilung',$userdata['to_department'],'[no_db]']);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['to_address','Adresse',$userdata['to_address'],'[no_db]']);
$yf->setValueField('html',['','</div><div>']);
$yf->setValueField('text',['to_zip','PLZ',$userdata['to_zip'],'[no_db]']);
$yf->setValueField('html',['','</div><div class="uk-grid-margin">']);
$yf->setValueField('text',['to_city','Ort',$userdata['to_city'],'[no_db]']);
$yf->setValueField('html',['','</div><div>']);
// $yf->setValueField('text',['to_country','Land',$userdata['to_country'],'[no_db]']);
$yf->setValueField('choice',['to_country','Land',rex_config::get('warehouse','country_codes'),0,0]);

// $yf->setValueField('html',['','</div>']);
$yf->setValueField('html',['','</div>']);
$yf->setValueField('html',['','</div>']); // #separate_delivery_address

$yf->setValueField('html',['','<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid" uk-grid="margin: uk-margin-small">']);
$yf->setValueField('html',['','<div>']);
$yf->setValueField('textarea',['note','Bemerkung',$userdata['note'],'[no_db]']);
$yf->setValueField('html',['','</div>']);
$yf->setValueField('html',['','</div>']);

$yf->setValueField('html',['','<div class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid" uk-grid="margin: uk-margin-small">']);
$yf->setValueField('html',['','<div id="payment_box">']);

if (count($current_payment_types) > 1) {
    $yf->setValueField('choice',["payment_type","{{ Payment Type }}",json_encode($current_payment_types),1,0,$userdata['payment_type']]);
} else {
    $yf->setValueField('html',['','{{ Payment Type }}: '.array_keys($current_payment_types)[0]]);
    $yf->setValueField('hidden',['payment_type',array_values($current_payment_types)[0]]);
}


// $yf->setValueField('choice',["payment_type","{{ Payment Type }}",'{"Vorauskasse":"prepayment","Paypal":"paypal"}',1,0]);
$yf->setValueField('html',['','</div>']);
$yf->setValueField('html',['','</div>']);

if (count($current_payment_types) > 1) {
    if (in_array('direct_debit',$current_payment_types)) {
        $yf->setValueField('html',['','<div id="direct_debit_box" class="uk-child-width-1-1 uk-child-width-1-2@s uk-grid" uk-grid="margin: uk-margin-small">']);
            $yf->setValueField('html',['','<div>{{ sepa_lastschrift_text }}</div>']);
            $yf->setValueField('html',['','<div>']);
            $yf->setValueField('text',['iban','IBAN*',$userdata['iban'],'[no_db]']);
            $yf->setValueField('html',['','</div><div>']);
            $yf->setValueField('text',['bic','BIC*',$userdata['bic'],'[no_db]']);
            $yf->setValueField('html',['','</div><div>']);
            $yf->setValueField('text',['direct_debit_name','Ggf. abweichender Kontoinhaber',$userdata['direct_debit_name'],'[no_db]']);
            $yf->setValueField('html',['','</div>']);
        $yf->setValueField('html',['','</div>']);
        $yf->setValidateField('customfunction',['iban','warehouse_helper::validate_sub_values',['payment_type','direct_debit'],'Bitte füllen Sie alle markierten Felder aus.']);
        $yf->setValidateField('customfunction',['bic','warehouse_helper::validate_sub_values',['payment_type','direct_debit'],'Bitte füllen Sie alle markierten Felder aus.']);
    }

} else {
}

$yf->setValueField('html',['','</div>']);

$yf->setValueField('submit',['send','Weiter ...','','[no_db]','','button']);
$yf->setValueField('html',['','</div></div>']);
$yf->setValueField('html',['','</section>']);

$yf->setValidateField('empty',['payment_type','Bitte füllen Sie alle markierten Felder aus']);
$yf->setValidateField('empty',['firstname','Bitte füllen Sie alle markierten Felder aus']);
// $yf->setValidateField('empty',['birthdate','Bitte füllen Sie alle markierten Felder aus']);
// $yf->setValidateField('size_range',['birthdate',8,12,'Bitte geben Sie das Datum in der Form dd.mm.YYYY an.']);
$yf->setValidateField('empty',['email','Bitte füllen Sie alle markierten Felder aus']);
$yf->setValidateField('email',['email','Bitte geben Sie eine gültige E-Mail Adresse ein']);

$yf->setActionField('callback', ['warehouse::save_userdata_in_session']);
$yf->setActionField('redirect',[rex_config::get('warehouse','order_page')]);

$form = $yf->getForm();

$fragment = new rex_fragment();
$fragment->setVar('form',$form);
echo $fragment->parse('warehouse_checkout_page.php');

?>
