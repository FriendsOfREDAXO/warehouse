<?php /* Warehouse Giropay Notify [11] */ ?>
 

<?php
if (rex::isBackend()) {
    echo '<h2>Giropay Notify</h2>';
    return;
} else {
    $giropay = new warehouse_giropay();
    $giropay->check_response();
}



?>
