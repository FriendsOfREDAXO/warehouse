<?php /* Warehouse Cart Page - Output */ 

$cart = FriendsOfRedaxo\Warehouse\Warehouse::getCart();
$fragment = new rex_fragment();
$fragment->setVar('cart',$cart);
$fragment->setVar('mode','modul');
echo $fragment->parse('warehouse_cart_page.php');

?>
