<?php
/* Dieses Modul wird automatisch vom Addon `warehouse` aktualisiert. Es wird empfohlen, keine Änderungen vorzunehmen, da diese nicht updatesicher sind. */

$file = "REX_VALUE[10]" ? "REX_VALUE[10]" : "fragments/warehouse/index.php";

if (rex_addon::get('warehouse')->isAvailable()) {
    $fragment = new rex_fragment();
    echo $fragment->parse($file);
    return;
} else {
    echo rex_view::error('Das Addon "Warehouse" ist nicht installiert und aktiviert.');
}
