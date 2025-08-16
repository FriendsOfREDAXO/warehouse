<?php

use FriendsOfRedaxo\Warehouse\Order;

/** @var rex_fragment $this */
/** @var Order $order */
$order = $this->getVar('order');


// Kundendaten
$user_data = [
    'salutation' => $order->getSalutation(),
    'firstname' => $order->getFirstname(),
    'lastname' => $order->getLastname(),
    'company' => $order->getCompany(),
    // 'department' => $order->getDepartment(),
    'address' => $order->getAddress(),
    'zip' => $order->getZip(),
    'city' => $order->getCity(),
    'country' => $order->getCountry(),
    'email' => $order->getEmail(),
    'ust' => $order->getValue(Order::UST),
    'user_e_number' => $order->getValue(Order::USER_E_NUMBER),
    'payment_type' => $order->getValue(Order::PAYMENT_TYPE),
    'payment_type_LABELS' => $order->getValue(Order::PAYMENT_TYPE_LABELS), // This appears to be a computed field
    'note' => $order->getValue(Order::NOTE),
    // ggf. weitere Felder
];

$cart = $order->getOrderJson();
$shipping = $order->getValue(Order::SHIPPING_COST);
$total = $order->getValue(Order::ORDER_TOTAL);
$with_tax = $order->getValue(Order::WITH_TAX);
$currency = $order->getValue(Order::CURRENCY);
$sub_total_netto = $order->getValue(Order::SUB_TOTAL_NETTO);
$sub_total = $order->getValue(Order::SUB_TOTAL);
$cart_total_tax = $order->getValue(Order::CART_TOTAL_TAX);
$userNumber = $user_data['user_e_number'];
$ustId = (!empty($user_data['ust'])) ? $user_data['ust'] : '';
$payment_type = $user_data['payment_type'];
$paymantTypeLabel = $user_data['payment_type_LABELS'];
$note = $user_data['note'];
if (!$with_tax) {
    $shipping = $shipping / 119 * 100;
}
if (empty($userNumber)) {
    $userNumber = 'Gastbestellung';
}

?>
<html>

<head>
	<link rel="stylesheet"
		href="<?= rex_path::addonAssets('warehouse', 'vendor\fontawesome-free\webfonts\fa-regular-400.ttf'); ?>">
	<style>
		@page {
			margin: 0cm 0cm;
		}

		body {
			margin: 5cm 1.25cm 2cm 1.25cm;
			font-size: 12px;
			font-family: Helvetica, Arial, sans-serif;
		}

		.wrapper {
			margin: 0 auto;
			width: 100%;
			max-width: 900px;
		}

		table {
			margin: 0 auto;
			width: 100%;
			max-width: 900px;
			collapse: 0;
			padding: 0;
			border: 0;
			font-size: 12px;
		}

		footer table {
			padding: 0;
		}

		h2.invoice {
			margin-top: 1.25cm;
			margin-bottom: 1.25cm;
			font-size: 16px;
			font-weight: bold;
		}

		table th {
			text-align: left;
		}

		table.items th {
			border-bottom: 1px solid #000;
			vertical-align: bottom;
		}

		table.items td {
			padding: 0.25cm 0.05cm 0.05cm 0.05cm;
			vertical-align: top;
		}

		.text-right {
			text-align: right;
		}

		table.items th:last-child,
		table.items td:last-child,
		table.billet th:last-child,
		table.billet td:last-child {
			text-align: right;
		}

		table.billet {
			width: 50%;
			float: right;
			clear: both;
			border: 1px solid #000;
			border-right: 0;
			margin: 0.25cm 0 1.25cm 0;
		}

		table.billet td {
			border-bottom: 0;
			border-top: 0;
			border-right: 1px solid #000;
			padding: 0.05cm 0.15cm;
		}

		p {
			clear: both;
		}

		main {}

		header {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			height: 5.5cm;
			padding: 0.5cm 1.25cm 0 1.25cm;
		}

		footer {
			position: fixed;
			bottom: 0;
			left: 0;
			right: 0;
			height: 1.55cm;
			padding: 0.75cm 1.25cm 0 1.25cm;
		}

		footer .point::after {
			content: '';
			margin: 0 0.075cm;
			display: inline-block;
			position: relative;
			top: -2px;
			width: 3px;
			height: 3px;
			border-radius: 6px;
			background: #000;
		}

		header .fa {
			position: relative;
			top: 0.08cm;
			font-size: 11px;
		}
	</style>
</head>

<body>

	<header>
		<table cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td style="width:60%;font-size:10px;">
						<?php
                        //sprogcard('pdf_header_info_text')
?>
					</td>
					<td style="width:40%">
						<img width="90%" src="<?php
                        $type = pathinfo(rex_path::media('logo_shop.png'), PATHINFO_EXTENSION);
$data = file_get_contents(rex_path::media('logo_shop.png'));
echo 'data:image/' . $type . ';base64,' . base64_encode($data);
?>" alt=""><br><br>
						<p><i style="display:inline-block;position:relative;width:0.42cm"><img
									style="position:absolute;width:0.40cm;top:-0.31cm;" src="<?php
                                                                                                        $type = pathinfo(rex_path::media('phone.png'), PATHINFO_EXTENSION);
$data = file_get_contents(rex_path::media('phone.png'));
echo 'data:image/' . $type . ';base64,' . base64_encode($data);
?>" alt=""></i>
							<?= sprogcard('pdf_phone') ?><br>
							<i style="display:inline-block;position:relative;width:0.42cm"><img
									style="position:absolute;width:0.40cm;top:-0.28cm;" src="<?php
    $type = pathinfo(rex_path::media('www.png'), PATHINFO_EXTENSION);
$data = file_get_contents(rex_path::media('www.png'));
echo 'data:image/' . $type . ';base64,' . base64_encode($data);
?>" alt=""></i>
							<?= sprogcard('pdf_shop_url') ?><br>
							<i style="display:inline-block;position:relative;width:0.42cm"><img
									style="position:absolute;width:0.40cm;top:-0.28cm;" src="<?php
$type = pathinfo(rex_path::media('mail.png'), PATHINFO_EXTENSION);
$data = file_get_contents(rex_path::media('mail.png'));
echo 'data:image/' . $type . ';base64,' . base64_encode($data);
?>" alt=""></i>
							<?= sprogcard('pdf_email') ?><br>
							<i style="display:inline-block;position:relative;width:0.42cm"><img
									style="position:absolute;width:0.40cm;top:-0.28cm;" src="<?php
$type = pathinfo(rex_path::media('mail.png'), PATHINFO_EXTENSION);
$data = file_get_contents(rex_path::media('mail.png'));
echo 'data:image/' . $type . ';base64,' . base64_encode($data);
?>" alt=""></i> USt.-ID: 146 788 484<br>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</header>

	<footer>
		<table cellspacing="0">
			<tbody>
				<tr>
					<td style="font-weight:bold;font-size:10px">
						<?= sprogcard('pdf_footer') ?>
					</td>
				</tr>
			</tbody>
		</table>
	</footer>

	<main>
		<div class="wrapper">
			<table cellspacing="0">
				<tbody>
					<tr>
						<td>
							<span
								style="font-size:9px;display:inline-block;border-bottom:1px solid #000;margin-bottom:0.15cm;"><?= sprogcard('pdf_address_header') ?>&nbsp;</span><br>
							<?php
                            $firma = (isset($user_data['company']) && !empty($user_data['company'])) ? $user_data['company'] . ' ' . $user_data['department'] : '';
$title = (isset($user_data['title']) && !empty($user_data['title'])) ? ' ' . $user_data['title'] . ' ' : ' ';

echo '<strong>' . $firma ? $firma . '</strong><br>' : '';
echo "{$user_data['salutation']}$title{$user_data['firstname']} {$user_data['lastname']}" . '<br>';
echo $user_data['department'] ? $user_data['department'] . '<br>' : '';
echo $user_data['address'] ? $user_data['address'] . ((!empty($user_data['housenumber'])) ? ' ' . $user_data['housenumber'] : '') . '<br>' : '';
echo $user_data['country'] . ' ' . $user_data['zip'] . ' ' . $user_data['city'] . '<br>';
if ($user_data['email']) {
    echo sprintf(sprogcard('tracking_at'), $user_data['email']);
}
?>
						</td>
						<td width="40%" style="width:40%">
							<table style="position:relative;margin-top:-1cm;border:1px solid #000;">
								<tbody>
									<tr>
										<th><?= sprogcard('pdf_order_date') ?>
										</th>
										<td><?= $order->getValue(Order::ORDER_DATE) ?>
										</td>
									</tr>
									<tr>
										<th><?= sprogcard('pdf_user_nr') ?>
										</th>
										<td><?= $userNumber ?></td>
									</tr>
									<tr>
										<th><?= sprogcard('pdf_shipping_type') ?>
										</th>
										<td><?= $order->getValue(Order::SHIPPING_TYPE) ?>
										</td>
									</tr>
									<tr>
										<th><?= sprogcard('pdf_payment_type') ?>
										</th>
										<td><?= $paymantTypeLabel ?>
										</td>
									</tr>
									<?php if (!empty($ustId)): ?>
									<tr>
										<th><?= sprogcard('pdf_payment_ustid') ?>
										</th>
										<td><?= $ustId ?></td>
									</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>

			<h2 class="invoice">
				<?= sprintf(sprogcard('pdf_invoice_headline'), $order->getValue(Order::ORDER_NUMBER)); ?>
			</h2>

			<?php
            if (isset($user_data['to_address']) && $user_data['to_address'] != $user_data['address']) {
                echo '<p>' . sprogcard('pdf_delivery_address') . '</p><p>';
                echo $user_data['to_company'] ? '<strong>' . $user_data['to_company'] . '</strong><br>' : '';
                //            echo (!empty($ustId)) ? sprogcard('pdf_payment_ustid') . ' ' . $ustId . '<br>' : '';
                echo (isset($user_data['to_firstname']) || isset($user_data['to_lastname'])) ? $user_data['to_firstname'] . ' ' . $user_data['to_lastname'] . '<br>' : '';
                echo $user_data['to_department'] ? $user_data['to_department'] . '<br>' : '';
                echo $user_data['to_address'] ? $user_data['to_address'] . '<br>' : '';
                echo $user_data['to_country'] . ' ' . $user_data['to_zip'] . ' ' . $user_data['to_city'];
                echo '</p>';
            }
?>

			<table class="items" cellspacing="0">
				<tbody>
					<tr>
						<th width="5%">
							<?= sprogcard('pdf_invoice_th_position') ?>
						</th>
						<th width="20%">
							<?= sprogcard('pdf_invoice_th_article_number') ?>
						</th>
						<th width="35%">
							<?= sprogcard('pdf_invoice_th_article_name') ?>
						</th>
						<th width="10%" class="text-right">
							<?= sprogcard('pdf_invoice_th_count') ?>
						</th>
						<?php if ($with_tax) : ?>
						<th width="10%" class="text-right">
							<?= sprogcard('pdf_invoice_th_tax') ?>
						</th><?php else: ?>
						<th></th><?php endif; ?>
						<th width="10%" class="text-right">
							<?= sprogcard('pdf_invoice_th_e_price') ?>
						</th>
						<th width="10%" class="text-right">
							<?= sprogcard('pdf_invoice_th_g_price') ?>
						</th>
					</tr>
					<?php
        // Access cart items from order JSON structure
        $cart_data = is_array($cart) ? $cart : [];
        $cart_items = $cart_data['cart'] ?? $cart_data; // Support both new and old structure
        
        if (is_array($cart_items) && count($cart_items) > 0) {
            $count = 0;
            foreach ($cart_items as $pos) {
                $count++;
                $attr = '';

                // Note: attributes are not part of standardized cart structure
                // This is kept for backward compatibility with old orders
                if (isset($pos['attributes']) && is_array($pos['attributes']) && sizeof($pos['attributes']) > 0) {
                    foreach ($pos['attributes'] as $attr_item) {
                        $attr .= html_entity_decode($attr_item['value'] . '  ' . $attr_item['at_name'] . ': ' . $attr_item['label']);
                    }
                }

                echo '<tr>';
                echo "<td>$count</td>";
                // Use SKU if available, otherwise fallback to generated pattern
                $article_sku = $pos['sku'] ?? ($pos['article_id'] . ($pos['variant_id'] ? '-' . $pos['variant_id'] : ''));
                echo '<td>' . $article_sku . '</td>';
                $variant_indicator = ($pos['type'] === 'variant') ? ' (Variante)' : '';
                echo '<td class="text-left">' . html_entity_decode($pos['name']) . $variant_indicator . $attr . '</td>';
                echo '<td class="text-right">' . $pos['amount'] . '</td>';
                if ($with_tax) {
                    $tax_rate = isset($pos['tax_rate']) ? $pos['tax_rate'] : '-';
                    echo '<td class="text-right">' . htmlspecialchars($tax_rate) . '</td>';
                    echo '<td class="text-right">' . number_format($pos['price'], 2, ',', '.') . '</td>';
                    echo '<td class="text-right">' . number_format($pos['total'], 2, ',', '.') . '</td>';
                } else {
                    echo '<td></td>';
                    echo '<td class="text-right">' . number_format($pos['price'], 2, ',', '.') . '</td>';
                    echo '<td class="text-right">' . number_format($pos['total'], 2, ',', '.') . '</td>';
                }
                echo '</tr>';
            }
        }
?>
					<tr>
						<td colspan="7">
							<table class="billet" cellspacing="0">
								<tbody>
									<tr>
										<td><?= sprogcard('pdf_full') ?>
										</td>
										<?php if ($with_tax): ?>
										<td><?= number_format($order->getValue(Order::SUB_TOTAL), 2, ',', '.') . ' ' . $currency ?>
										</td>
										<?php else: ?>
										<td><?= number_format($order->getValue(Order::SUB_TOTAL_NETTO), 2, ',', '.') . ' ' . $currency ?>
										</td>
										<?php endif; ?>
									</tr>
									<tr>
										<td><?= sprogcard('pdf_shipping_costs') ?>
										</td>
										<td><?= number_format($shipping, 2, ',', '.') . ' ' . $currency ?>
										</td>
									</tr>
									<?php if ($with_tax):
									    $taxItems = [
									        19 => $order->getValue(Order::CART_TOTAL_TAX) * 0.19,
									        7 => $order->getValue(Order::CART_TOTAL_TAX) * 0.07
									    ];
									    foreach ($taxItems as $tax => $taxValue) :
									        ?>
									<tr>
										<td><?= sprintf(sprogcard('pdf_ust'), $tax . '%') ?>
										</td>
										<td><?= number_format($taxValue, 2, ',', '.') . ' ' . $currency ?>
										</td>
									</tr>
									<?php endforeach;
									endif; ?>
									<tr>
										<td><strong><?= sprogcard('pdf_g_price') ?></strong>
										</td>
										<td><strong><?= number_format($total, 2, ',', '.') . ' ' . $currency ?></strong>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<?php if ($user_data['country'] == 'DE' && $user_data['ust'] != ""):  ?>
			<p>Steuerfreie innergemeinschaftliche Lieferung.</p>
			<?php endif; ?>
			<?php if (!is_null($note)): ?>
			<p>Bemerkungen: <?php echo $note; ?></p>
			<?php endif; ?>
			<?php if ($payment_type == 'paypal' && !is_null($order->getValue(Order::PAYDATE))): ?>
			<p><?= sprintf(sprogcard('pdf_order_msg_paypal'), $order->getValue(Order::PAYDATE)); ?>
			</p>
			<?php elseif ($payment_type == 'prepayment' && !is_null($order->getValue(Order::PAYDATE))): ?>
			<p style="font-size: 14px;">
				<?= sprintf(sprogcard('pdf_order_msg_prepayment'), number_format($total, 2, ',', '.') . ' ' . $currency, $order->getValue(Order::VERWENDUNGSZWECK)); ?>
			</p>
			<?php endif; ?>
		</div>
	</main>
</body>

</html>
