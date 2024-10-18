<?php /* Warehouse Giropay Start [10] */ ?>
 

<?php
if (rex::isBackend()) {
    echo '<h2>Giropay Start</h2>';
    return;
} else {
    $giropay = new warehouse_giropay();
    $giropay->init_giropay();
}



?>
