<?php
$user_data = $this->warehouse_userdata;
?>

<h2>{{ Bestellübersicht }}</h2>
<table class="uk-table uk-table-striped uk-width-1-1 uk-table-small" id="table_order_summary">
    <thead>
        <tr>
            <th></th>
            <th class="uk-text-right"><?= rex_config::get('warehouse', 'currency') ?></th>
        </tr>
    </thead>
    <?php foreach ($this->cart as $item) : ?>
        <tr>
            <td>
                <?= trim(html_entity_decode($item['name']),' -') ?><br>
                <?php $attr_text = []; ?>
                <?php foreach ($item['attributes'] as $attr) : ?>
                    <?php $attr_text[] = $attr['value'] ?>
                <?php endforeach ?>
                <?= implode(' - ',$attr_text). ($attr_text ? '<br>' : '') ?>
                
                
                
                <?= $item['count'] ?> x à <?= number_format($item['price'], 2) ?>
            </td>
            <td class="uk-text-right"><?= number_format($item['total'], 2) ?></td>
        </tr>
    <?php endforeach ?>
    <tr>
        <td>{{ Shipping }} <?= $this->warehouse_userdata['country'] ?></td>
        <td class="uk-text-right"><?= number_format((float) FriendsOfRedaxo\Warehouse\Warehouse::get_shipping_cost(), 2) ?></td>
    </tr>
    <tr>
        <td>{{ Total }}</td>
        <td class="uk-text-right"><?= number_format((float) FriendsOfRedaxo\Warehouse\Warehouse::get_cart_total(), 2) ?></td>
    </tr>
</table>

<p>{{ Lieferadresse }}:</p>
<p>
    <?= $user_data['firstname'] . ' ' . $user_data['lastname'] ?><br>
    <?= $user_data['address'] ?><br>
    <?= $user_data['zip'] . ' ' . $user_data['city'] ?><br>
    <?= $user_data['country'] ?>
</p>

<p>{{ Rechnungsadresse }}:</p>

<?php if (isset($user_data['separate_delivery_address']) && $user_data['separate_delivery_address'] == 1) : ?>
    <p>{{ Entspricht der Lieferadresse }}</p>
<?php else: ?>
    <p>
        <?= $user_data['to_firstname'] . ' ' . $user_data['to_lastname'] ?><br>
        <?= $user_data['to_address'] ?><br>
        <?= $user_data['to_zip'] . ' ' . $user_data['to_city'] ?><br>
        <?= $user_data['to_country'] ?>    
    </p>
<?php endif ?>

<p>{{ Payment Type }}: {{ payment_<?= $user_data['payment_type'] ?> }}</p>
