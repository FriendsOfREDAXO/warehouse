<?php
/* Dieses Modul wird automatisch vom Addon `warehouse` aktualisiert. Es wird empfohlen, keine Änderungen vorzunehmen, da diese nicht updatesicher sind. */

use FriendsOfRedaxo\Warehouse\Warehouse;

$title = "REX_VALUE[1]";
$description = "REX_VALUE[2 output=html]";

/* Passendes Fragment ausgeben, Parameter des Dateinamens in REX_VALUE[10] */
$file = "REX_VALUE[10]" ? "REX_VALUE[10]" : "index.php";

if (rex_addon::get('warehouse')->isAvailable()) {
    echo Warehouse::parse($file, ['title' => $title, 'description' => $description]);
    return;
} else {
    echo rex_view::error('Das Add-on "Warehouse" ist nicht installiert oder aktiviert.');
}
