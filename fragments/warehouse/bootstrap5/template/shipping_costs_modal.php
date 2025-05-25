<?php
use FriendsOfRedaxo\Warehouse\Warehouse;
?>
<!-- template/shipping_costs_modal.php -->
<div class="modal fade" id="warehouseShippingCostModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="warehouseShippingCostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="warehouseShippingCostModalLabel"><?= Warehouse::getLabel('shipping_costs') ?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?= Warehouse::getConfig('shipping_conditions_text') ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Okay</button>
      </div>
    </div>
  </div>
</div>
<!-- /template/shipping_costs_modal.php -->
