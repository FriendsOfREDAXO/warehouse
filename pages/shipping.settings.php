<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$form = rex_config_form::factory('warehouse');

// ==== Frachtrechnung

$form->addFieldset('Frachtrechnung');

$field = $form->addTextField('shipping');
$field->setLabel('Versandkosten Standard');
$field->setNotice('Kann leer bleiben, wenn Sonderfrachtberechnung definiert ist.');

$field = $form->addSelectField('shipping_mode');
$field->setLabel('Frachtberechnung');
$select = $field->getSelect();
$select->addOptions([
    0 => 'Standard (Pauschal)',
    'pieces' => 'nach Stück',
    'weight' => 'nach Gewicht',
    'order_total' => 'Betrag (brutto)',
]);

$field = $form->addTextField('shipping_parameters');
$field->setLabel('Fracht Parameter');
$field->setNotice('Paramter für die Frachtberechnung. Als JSON in der Form <code>[[">",4,10.5],[">",2,7.9],[">",0,5.9]]</code> angeben. Jede Bedingung besteht aus drei Elementen. Als Kondition sind die Angaben <code>&gt;</code>, '
        . '<code>&lt;</code>, <code>&gt;=</code>, <code>&lt;=</code> oder <code>=</code> möglich. Der zweite Wert steht für die Anzahl, der dritte für den Frachtpreis. Die erste Bedingung die erfüllt ist, wird für die Frachtberechnung verwendet. Wenn keine Bedingung erfüllt ist, wird der Standardfrachtpreis berechnet.');

$content = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

echo $content;

