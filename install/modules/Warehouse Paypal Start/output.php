<?php
/* -- Warehouse Paypal Start -- */
if (rex::isBackend()) {
    echo '<h2>Paypal Start</h2>';
    return;
} else {
    warehouse_paypal::create_order();
}
?>
