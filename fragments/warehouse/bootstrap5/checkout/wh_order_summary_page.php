<?php
$user_data = $this->wh_userdata;
//dump($user_data);
$with_tax = warehouse::with_tax();

if ($with_tax) {
    $shipping = warehouse::get_shipping_cost();
} else {
    $shipping = (warehouse::get_shipping_cost() - warehouse::get_shipping_tax());
}

//dump(warehouse::get_order_text());

//dump($this->cart);

?>

<h2>{{ Bestellübersicht }}</h2>
<table class="uk-table uk-table-striped uk-width-1-1 uk-table-small" id="table_order_summary">
    <thead>
        <tr>
            <th>Artikel</th>
            <th></th>
            <?php if ($with_tax): ?><th class="uk-text-right">{{ tax }}</th><?php endif; ?>
            <th class="uk-text-right"><?= rex_config::get('warehouse', 'currency') ?></th>
        </tr>
    </thead>
    <?php foreach ($this->cart as $item) : ?>
        <tr>
            <td>
                <div class="uk-text-meta"><?= ((isset($item['var_whvarid']) && !empty($item['var_whvarid'])) ? $item['var_whvarid'] : $item['whid'])  ?></div>
                <?= trim(html_entity_decode($item['article_name']),' -') ?><br>
                <?php $attr_text = []; ?>
                <?php if (isset($item['attributes']) && is_array($item['attributes']) && sizeof($item['attributes']) > 0): foreach ($item['attributes'] as $attr) : ?>
                    <?php $attr_text[] = $attr['value'] ?>
                <?php endforeach; endif; ?>
                <?= implode(' - ',$attr_text). ($attr_text ? '<br>' : '') ?>
                <?= $item['count'] ?> x à <?= warehouse::number_format(warehouse::get_item_price($item), 2) ?>
            </td>
            <td><?=((isset($item['var_bezeichnung'])&&!empty($item['var_bezeichnung']))?'<div class="uk-text-meta">&nbsp;</div>'.$item['var_bezeichnung']:'')?></td>
            <?php if ($with_tax): ?><td class="uk-text-right"><?=$item['taxpercent']?>%</td><?php endif; ?>
            <td class="uk-text-right"><?= warehouse::number_format(warehouse::get_item_price_total($item), 2) ?></td>
        </tr>
    <?php endforeach ?>
    <tr>
        <td colspan="2">{{ Shipping }} <?= $this->wh_userdata['country'] ?></td>
        <td colspan="2" class="uk-text-right"><?= warehouse::number_format($shipping, 2) ?></td>
    </tr>
    <?php if ($with_tax):
        $taxItems = warehouse::get_card_tax_by_percent();
        foreach ($taxItems as $tax => $taxValue) :
    ?>
    <tr>
        <td colspan="2">{{ Card_Mwst_Total }} (<?=$tax?>%)</td>
        <td colspan="2" class="uk-text-right"><?= warehouse::number_format($taxValue, 2) ?></td>
    </tr>
    <?php endforeach; endif; ?>
    <tr>
        <td colspan="2"><?php if($with_tax) { ?>{{ Total_Mwst }}<?php } else { ?>{{ Total }}<?php } ?></td>
        <td colspan="2" class="uk-text-right"><?= warehouse::number_format(warehouse::get_cart_total(), 2) ?></td>
    </tr>
</table>
<p>{{ Lieferadresse }}:</p>
<p>
    <?php

$firma = (isset($user_data['company']) && !empty($user_data['company'])) ? $user_data['company'] . ' ' . $user_data['department'] .'<br>': '';
$ust = (isset($user_data['ust']) && !empty($user_data['ust'])) ? 'Ust. Identnummer: ' . $user_data['ust'] .'<br>': '';
$title = (isset($user_data['title']) && !empty($user_data['title'])) ? ' ' . $user_data['title'] .' ': ' ';
echo "
    {$user_data['salutation']}$title{$user_data['firstname']} {$user_data['lastname']}<br>
    {$firma}{$ust}
    {$user_data['address']} {$user_data['housenumber']} <br>
    {$user_data['zip']}   {$user_data['city']}<br>
    {$user_data['country']}
"
    ?>
</p>

<p>{{ Payment Type }}: {{ payment_<?= $user_data['payment_type'] ?> }}</p>
