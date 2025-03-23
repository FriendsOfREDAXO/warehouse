<?php
$user_data = $this->warehouse_userdata;
?>
<div class="row">
    <div class="col-lg-12">
        <div class="main">
            <div class="cart">
                <div class="page-content page-order">
                    <div class="page-title mt30">
                        <h2>{{ Bestellübersicht }}</h2>
                    </div>

                    <table class="table table-bordered table-striped order_summary" id="table_order_summary">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-right"><?= rex_config::get('warehouse', 'currency') ?></th>
                            </tr>
                        </thead>
                        <?php foreach ($this->cart as $item) : ?>
                            <tr>
                                <td class="col1">
                                    <?= html_entity_decode($item['name']) ?><br>
                                    <?= $item['count'] ?> x à <?= number_format($item['price'], 2) ?>
                                </td>
                                <td class="col2 text-right"><?= number_format($item['total'], 2) ?></td>
                            </tr>
                        <?php endforeach ?>
                        <tr class="tr_shipping">
                            <td class="col1">{{ Versandkosten }} <?= $this->warehouse_userdata['country'] ?></td>
                            <td class="col2 text-right"><?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getShippingCost(), 2) ?></td>
                        </tr>
                        <tr class="tr_total">
                            <td class="bigtext col1">{{ Total }}</td>
                            <td class="bigtext col2 text-right"><?= number_format(FriendsOfRedaxo\Warehouse\Warehouse::getCartTotal(), 2) ?></td>
                        </tr>
                    </table>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="headlineboxsmall">
                                <h2><i class="fa fa-info"></i>{{ Lieferadresse }}</h2>
                            </div>
                            <div class="summarybox bg_grey">
                                <?= $user_data['firstname'] . ' ' . $user_data['lastname'] ?><br>
                                <?= $user_data['address'] ?><br>
                                <?= $user_data['zip'] . ' ' . $user_data['city'] ?><br>
                                <?= $user_data['country'] ?>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="headlineboxsmall">
                                <h2><i class="fa fa-info"></i>{{ Rechnungsadresse }}</h2>
                            </div>

                            <?php if (isset($user_data['separate_delivery_address']) && $user_data['separate_delivery_address'] == 1) : ?>
                                <p class="">{{ Entspricht der Lieferadresse }}</p>
                            <?php else: ?>
                                <div class="summarybox bg_grey">
                                    <?= $user_data['to_firstname'] . ' ' . $user_data['to_lastname'] ?><br>
                                    <?= $user_data['to_address'] ?><br>
                                    <?= $user_data['to_zip'] . ' ' . $user_data['to_city'] ?><br>
                                    <?= $user_data['to_country'] ?>    
                                </div>
                            <?php endif ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="headlineboxsmall">
                                <h2><i class="fa fa-info"></i>{{ Zahlungsart }}</h2>
                            </div>
                            <div class="summarybox bg_grey">{{ payment_<?= $user_data['payment_type'] ?> }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
