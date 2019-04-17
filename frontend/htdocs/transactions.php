<?php
include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
	Link::redirect('userprofile.php');
elseif (User::$awaiting_token)
	Link::redirect('verify-token.php');
elseif (!User::isLoggedIn())
	Link::redirect('login.php');

if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']),$CFG->currencies)))
	$_SESSION['tr_c_currency'] = preg_replace("/[^0-9]/", "",$_REQUEST['c_currency']);
else if (empty($_SESSION['tr_c_currency']) || $_REQUEST['c_currency'] == 'All')
	$_SESSION['tr_c_currency'] = false;

if ((!empty($_REQUEST['currency']) && array_key_exists(strtoupper($_REQUEST['currency']),$CFG->currencies)))
	$_SESSION['tr_currency'] = preg_replace("/[^0-9]/", "",$_REQUEST['currency']);
else if (empty($_SESSION['tr_currency']) || $_REQUEST['currency'] == 'All')
	$_SESSION['tr_currency'] = false;

if ((!empty($_REQUEST['order_by'])))
	$_SESSION['tr_order_by'] = preg_replace("/[^a-z]/", "",$_REQUEST['order_by']);
else if (empty($_SESSION['tr_order_by']))
	$_SESSION['tr_order_by'] = false;

$currency1 = $_SESSION['tr_currency'];
$c_currency1 = $_SESSION['tr_c_currency'];
$order_by1 = $_SESSION['tr_order_by'];
$start_date1 = false;
$type1 = (!empty($_REQUEST['type'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['type']) : false;
$page1 = (!empty($_REQUEST['page'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['page']) : false;
$trans_realized1 = (!empty($_REQUEST['transactions'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['transactions']) : false;
$bypass = !empty($_REQUEST['bypass']);

API::add('Transactions','get',array(1,$page1,30,$c_currency1,$currency1,1,$start_date1,$type1,$order_by1));
$query = API::send();
$total = $query['Transactions']['get']['results'][0];

API::add('Transactions','get',array(false,$page1,30,$c_currency1,$currency1,1,$start_date1,$type1,$order_by1));
API::add('Transactions','getTypes');
$query = API::send();

$transactions = $query['Transactions']['get']['results'][0];
$transaction_types = $query['Transactions']['getTypes']['results'][0];
$pagination = Content::pagination('transactions.php',$page1,$total,30,5,false);

$currency_info = ($currency1) ? $CFG->currencies[strtoupper($currency1)] : array();

if ($trans_realized1 > 0)
	Messages::add(str_replace('[transactions]',$trans_realized1,Lang::string('transactions-done-message')));

$page_title = Lang::string('transactions');

if (!$bypass) {
	include 'includes/head.php';
	
?>
<div class="page_title">
	<div class="container">
		<div class="title"><h1><?= $page_title ?></h1></div>
        <div class="pagenation">&nbsp;<a href="index.php"><?= Lang::string('home') ?></a> <i>/</i> <a href="account.php"><?= Lang::string('account') ?></a> <i>/</i> <a href="transactions.php"><?= $page_title ?></a></div>
	</div>
</div>
<div class="container">
	<div class="content_right">
		<? Messages::display(); ?>
		<div class="filters">
			<input type="hidden" id="transactions_user" value="1" />
			<form id="filters" method="GET" action="transactions.php">
				<ul class="list_empty">
					<li>
						<label for="c_currency1"><?= Lang::string('market') ?></label>
						<select name="c_currency" id="c_currency1">
							<option value="All"><?= Lang::string('all-currencies') ?></option>
							<?
							if ($CFG->currencies) {
								foreach ($CFG->currencies as $key => $currency) {
									if (is_numeric($key) || $currency['is_crypto'] != 'Y')
										continue;
									
									echo '<option '.(($currency['id'] == $c_currency1) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
								}
							}	
							?>
						</select>
					</li>
					<li>
						<label for="graph_orders_currency"><?= Lang::string('orders-filter-currency') ?></label>
						<select id="graph_orders_currency" name="currency">
							<option value="All"><?= Lang::string('transactions-any') ?></option>
							<? 
							if ($CFG->currencies) {
								foreach ($CFG->currencies as $key => $currency) {
									if (is_numeric($key))
										continue;
									
									echo '<option '.((strtolower($currency['currency']) == $currency1) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
								}
							}
							?>
						</select>
					</li>
					<li>
						<label for="order_by"><?= Lang::string('orders-order-by') ?></label>
						<select id="order_by" name="order_by">
							<option value="date" <?= (!$order_by1 || $order_by1 == 'date') ? 'selected="selected"' : ''?>><?= Lang::string('transactions-time') ?></option>
							<option value="btcprice" <?= ($order_by1 == 'btcprice') ? 'selected="selected"' : ''?>><?= Lang::string('orders-order-by-btc-price') ?></option>
							<option value="fiat" <?= ($order_by1 == 'fiat') ? 'selected="selected"' : ''?>><?= Lang::string('transactions-fiat') ?></option>
						</select>
					</li>
					<li>
						<label for="type"><?= Lang::string('transactions-type') ?></label>
						<select id="type" name="type">
							<option value=""><?= Lang::string('transactions-any') ?></option>
							<?
							if ($transaction_types) {
								foreach ($transaction_types as $type) {
									echo '<option '.((strtolower($type['id']) == $type1) ? 'selected="selected"' : '').' value="'.$type['id'].'">'.$type['name_'.$CFG->language].'</option>';
								}
							}
							?>
						</select>
					</li>
					<li>
						<a class="download" href="transactions_download.php"><i class="fa fa-download"></i> <?= Lang::string('transactions-download') ?></a>
					</li>
				</ul>
			</form>
		</div>
		<div class="clear"></div>
		<div id="filters_area">
<? } ?>
        	<div class="table-style">
        		<input type="hidden" id="refresh_transactions" value="1" />
        		<input type="hidden" id="page" value="<?= $page1 ?>" />
        		<table class="table-list trades" id="transactions_list">
        			<tr id="table_first">
        				<th><?= Lang::string('transactions-type') ?></th>
        				<th><?= Lang::string('transactions-time') ?></th>
        				<th><?= Lang::string('orders-amount') ?></th>
        				<th><?= Lang::string('transactions-fiat') ?></th>
        				<th><?= Lang::string('orders-price') ?></th>
        				<th><?= Lang::string('transactions-fee') ?></th>
        			</tr>
        			<? 
        			if ($transactions) {
						foreach ($transactions as $transaction) {
							$trans_symbol = $CFG->currencies[$transaction['currency']]['fa_symbol'];
							echo '
					<tr id="transaction_'.$transaction['id'].'">
						<input type="hidden" class="is_crypto" value="'.$transaction['is_crypto'].'" />
						<td>'.$transaction['type'].'</td>
						<td><input type="hidden" class="localdate" value="'.(strtotime($transaction['date'])).'" /></td>
						<td>'.Stringz::currency($transaction['btc'],true).' '.$CFG->currencies[$transaction['c_currency']]['fa_symbol'].'</td>
						<td><span class="currency_char">'.$trans_symbol.'</span><span>'.Stringz::currency($transaction['btc_net'] * $transaction['fiat_price'],($transaction['is_crypto'] == 'Y')).'</span></td>
						<td><span class="currency_char">'.$trans_symbol.'</span><span>'.Stringz::currency($transaction['fiat_price'],($transaction['is_crypto'] == 'Y')).'</span></td>
						<td><span class="currency_char">'.$trans_symbol.'</span><span>'.Stringz::currency($transaction['fee'] * $transaction['fiat_price'],($transaction['is_crypto'] == 'Y')).'</span></td>
					</tr>';
						}
					}
					echo '<tr id="no_transactions" style="'.(is_array($transactions) ? 'display:none;' : '').'"><td colspan="6">'.Lang::string('transactions-no').'</td></tr>';
        			?>
        		</table>
        		<?= $pagination ?>
			</div>
			<div class="clear"></div>
		</div>
<? if (!$bypass) { ?>
		<div class="mar_top5"></div>
	</div>
	<? include 'includes/sidebar_account.php'; ?>
</div>
<? include 'includes/foot.php'; ?>
<? } ?>