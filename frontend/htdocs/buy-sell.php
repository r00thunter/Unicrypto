
<?php
include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
	Link::redirect('userprofile.php');
elseif (User::$awaiting_token)
	Link::redirect('verify-token.php');
elseif (!User::isLoggedIn())
	Link::redirect('login.php');


$market = $_GET['trade'];
$currencies = Settings::sessionCurrency();
$buy = (!empty($_REQUEST['buy']));
$sell = (!empty($_REQUEST['sell']));
$ask_confirm = false;
$currency1 = $currencies['currency'];
$c_currency1 = $currencies['c_currency'];
list($c_currency1, $currency1 ) = explode("-",$market) ;
foreach ($CFG->currencies as $key => $currency) {
	if( strtolower($c_currency1) == strtolower( $currency['currency'] )){
		$c_currency1 = $currency['id'] ;
	}
	if( strtolower( $currency1 ) == strtolower( $currency['currency'] ) ){
		$currency1 = $currency['id'];
	}
	// $select .= '<option value="'.$currency['id'].'" '.($c_currency1 == $currency['id'] ? 'selected="selected"' : '').'>'.$currency['currency'].'</option>';
}
$currency_info = $CFG->currencies[$currency1];
$c_currency_info = $CFG->currencies[$c_currency1];
$confirmed = (!empty($_REQUEST['confirmed'])) ? $_REQUEST['confirmed'] : false;
$cancel = (!empty($_REQUEST['cancel'])) ? $_REQUEST['cancel'] : false;
$bypass = (!empty($_REQUEST['bypass'])) ? $_REQUEST['bypass'] : false;
$buy_market_price1 = 0;
$sell_market_price1 = 0;
$buy_limit = 1;
$sell_limit = 1;
if ($buy || $sell) {
	if (empty($_SESSION["buysell_uniq"]) || empty($_REQUEST['uniq']) || !in_array($_REQUEST['uniq'],$_SESSION["buysell_uniq"]))
		Errors::add('Page expired.');
}

foreach ($CFG->currencies as $key => $currency) {
	if (is_numeric($key) || $currency['is_crypto'] != 'Y')
		continue;
		
	API::add('Stats','getCurrent',array($currency['id'],$currency1));
}

API::add('FeeSchedule','getRecord',array(User::$info['fee_schedule']));
API::add('User','getAvailable');
API::add('User','hasCurrencies');
API::add('Orders','getBidAsk',array($c_currency1,$currency1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,false,false,1));
API::add('Transactions','get',array(false,false,1,$c_currency1,$currency1));
API::add('Transactions','get24hData',array(28,27));
API::add('Transactions','get24hData',array(42,27));
API::add('Transactions','get24hData',array(42,28));
if ($currency_info['is_crypto'] != 'Y')
	API::add('BankAccounts','get',array($currency_info['id']));

$query = API::send();
$total = $query['Transactions']['get']['results'][0];
$user_fee_both = $query['FeeSchedule']['getRecord']['results'][0];
$user_available = $query['User']['getAvailable']['results'][0];
$user_available_currencies = $query['User']['hasCurrencies']['results'];
// echo "<pre>"; print_r($user_available_currencies); exit;
$current_bid = $query['Orders']['getBidAsk']['results'][0]['bid'];
$current_ask =  $query['Orders']['getBidAsk']['results'][0]['ask'];
$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];
$user_fee_bid = ($buy && ((Stringz::currencyInput($_REQUEST['buy_amount']) > 0 && Stringz::currencyInput($_REQUEST['buy_price']) >= $asks[0]['btc_price']) || !empty($_REQUEST['buy_market_price']) || empty($_REQUEST['buy_amount']))) ? $query['FeeSchedule']['getRecord']['results'][0]['fee'] : $query['FeeSchedule']['getRecord']['results'][0]['fee1'];
$user_fee_ask = ($sell && ((Stringz::currencyInput($_REQUEST['sell_amount']) > 0 && Stringz::currencyInput($_REQUEST['sell_price']) <= $bids[0]['btc_price']) || !empty($_REQUEST['sell_market_price']) || empty($_REQUEST['sell_amount']))) ? $query['FeeSchedule']['getRecord']['results'][0]['fee'] : $query['FeeSchedule']['getRecord']['results'][0]['fee1'];
$transactions = $query['Transactions']['get']['results'][0];
$usd_field = 'usd_ask';
$transactions_24hrs_btc_usd = $query['Transactions']['get24hData']['results'][0] ;
$transactions_24hrs_ltc_usd = $query['Transactions']['get24hData']['results'][1] ;
$transactions_24hrs_ltc_btc = $query['Transactions']['get24hData']['results'][2] ;


$i = 0;
$stats = array();
$market_stats = array();
foreach ($CFG->currencies as $key => $currency) {
	if (is_numeric($key) || $currency['is_crypto'] != 'Y')
		continue;

	$k = $query['Stats']['getCurrent']['results'][$i]['market'];
	if ($CFG->currencies[$k]['id'] == $c_currency1)
		$stats = $query['Stats']['getCurrent']['results'][$i];
	
	$market_stats[$k] = $query['Stats']['getCurrent']['results'][$i];
	$i++;
}

if ($currency_info['is_crypto'] != 'Y')
	$bank_accounts = $query['BankAccounts']['get']['results'][0];

$buy_amount1 = (!empty($_REQUEST['buy_amount'])) ? Stringz::currencyInput($_REQUEST['buy_amount']) : 0;
$buy_price1 = (!empty($_REQUEST['buy_price'])) ? Stringz::currencyInput($_REQUEST['buy_price']) : $current_ask;
$buy_subtotal1 = $buy_amount1 * $buy_price1;
$buy_fee_amount1 = ($user_fee_bid * 0.01) * $buy_subtotal1;
$buy_total1 = round($buy_subtotal1 + $buy_fee_amount1,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP);
$buy_stop = false;
$buy_stop_price1 = false;
$buy_all1 = (!empty($_REQUEST['buy_all']));

$sell_amount1 = (!empty($_REQUEST['sell_amount'])) ? Stringz::currencyInput($_REQUEST['sell_amount']) : 0;
$sell_price1 = (!empty($_REQUEST['sell_price'])) ? Stringz::currencyInput($_REQUEST['sell_price']) : $current_bid;
$sell_subtotal1 = $sell_amount1 * $sell_price1;
$sell_fee_amount1 = ($user_fee_ask * 0.01) * $sell_subtotal1;
$sell_total1 = round($sell_subtotal1 - $sell_fee_amount1,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP);
$sell_stop = false;
$sell_stop_price1 = false;

if ($CFG->trading_status == 'suspended')
	Errors::add(Lang::string('buy-trading-disabled'));

if ($buy && !is_array(Errors::$errors)) {
	$buy_market_price1 = (!empty($_REQUEST['buy_market_price']));
	$buy_price1 = ($buy_market_price1) ? $current_ask : $buy_price1;
	$buy_stop = (!empty($_REQUEST['buy_stop']));
	$buy_stop_price1 = ($buy_stop) ? Stringz::currencyInput($_REQUEST['buy_stop_price']) : false;
	$buy_limit = (!empty($_REQUEST['buy_limit']));
	$buy_limit = (!$buy_stop && !$buy_market_price1) ? 1 : $buy_limit;
	
	if (!$confirmed && !$cancel) {
		API::add('Orders','checkPreconditions',array(1,$c_currency1,$currency_info,$buy_amount1,(($buy_stop && !$buy_limit) ? $buy_stop_price1 : $buy_price1),$buy_stop_price1,$user_fee_bid,$user_available[$currency_info['currency']],$current_bid,$current_ask,$buy_market_price1,false,false,$buy_all1));
		if (!$buy_market_price1)
			API::add('Orders','checkUserOrders',array(1,$c_currency1,$currency_info,false,(($buy_stop && !$buy_limit) ? $buy_stop_price1 : $buy_price1),$buy_stop_price1,$user_fee_bid,$buy_stop));
		
		$query = API::send();
		$errors1 = $query['Orders']['checkPreconditions']['results'][0];
		if (!empty($errors1['error']))
			Errors::add($errors1['error']['message']);
		$errors2 = (!empty($query['Orders']['checkUserOrders']['results'][0])) ? $query['Orders']['checkUserOrders']['results'][0] : false;
		if (!empty($errors2['error']))
			Errors::add($errors2['error']['message']);
		
		if (!$errors1 && !$errors2)
			$ask_confirm = true;
	}
	else if (!$cancel) {
		API::add('Orders','executeOrder',array(1,(($buy_stop && !$buy_limit) ? $buy_stop_price1 : $buy_price1),$buy_amount1,$c_currency1,$currency1,$user_fee_bid,$buy_market_price1,false,false,false,$buy_stop_price1,false,false,$buy_all1));
		$query = API::send();
		$operations = $query['Orders']['executeOrder']['results'][0];
		
		if (!empty($operations['error'])) {
			Errors::add($operations['error']['message']);
		}
		else if ($operations['new_order'] > 0) {
		    $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
		    if (count($_SESSION["buysell_uniq"]) > 3) {
		    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
		    }
		    
			Link::redirect('open-orders.php',array('transactions'=>$operations['transactions'],'new_order'=>1));
			exit;
		}
		else {
		    $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
		    if (count($_SESSION["buysell_uniq"]) > 3) {
		    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
		    }
		    
			Link::redirect('transactions.php',array('transactions'=>$operations['transactions']));
			exit;
		}
	}
}

if ($sell && !is_array(Errors::$errors)) {
	$sell_market_price1 = (!empty($_REQUEST['sell_market_price']));
	$sell_price1 = ($sell_market_price1) ? $current_bid : $sell_price1;
	$sell_stop = (!empty($_REQUEST['sell_stop']));
	$sell_stop_price1 = ($sell_stop) ? Stringz::currencyInput($_REQUEST['sell_stop_price']) : false;
	$sell_limit = (!empty($_REQUEST['sell_limit']));
	$sell_limit = (!$sell_stop && !$sell_market_price1) ? 1 : $sell_limit;
	
	if (!$confirmed && !$cancel) {
		API::add('Orders','checkPreconditions',array(0,$c_currency1,$currency_info,$sell_amount1,(($sell_stop && !$sell_limit) ? $sell_stop_price1 : $sell_price1),$sell_stop_price1,$user_fee_ask,$user_available[$c_currency_info['currency']],$current_bid,$current_ask,$sell_market_price1));
		if (!$sell_market_price1)
			API::add('Orders','checkUserOrders',array(0,$c_currency1,$currency_info,false,(($sell_stop && !$sell_limit) ? $sell_stop_price1 : $sell_price1),$sell_stop_price1,$user_fee_ask,$sell_stop));
	
		$query = API::send();
		$errors1 = $query['Orders']['checkPreconditions']['results'][0];
		if (!empty($errors1['error']))
			Errors::add($errors1['error']['message']);
		$errors2 = (!empty($query['Orders']['checkUserOrders']['results'][0])) ? $query['Orders']['checkUserOrders']['results'][0] : false;
		if (!empty($errors2['error']))
			Errors::add($errors2['error']['message']);
	
		if (!$errors1 && !$errors2)
			$ask_confirm = true;
	}
	else if (!$cancel) {
		API::add('Orders','executeOrder',array(0,($sell_stop && !$sell_limit) ? $sell_stop_price1 : $sell_price1,$sell_amount1,$c_currency1,$currency1,$user_fee_ask,$sell_market_price1,false,false,false,$sell_stop_price1));
		$query = API::send();
		$operations = $query['Orders']['executeOrder']['results'][0];

		if (!empty($operations['error'])) {
			Errors::add($operations['error']['message']);
		}
		else if ($operations['new_order'] > 0) {
		    $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
		    if (count($_SESSION["buysell_uniq"]) > 3) {
		    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
		    }
		    
			Link::redirect('open-orders.php',array('transactions'=>$operations['transactions'],'new_order'=>1));
			exit;
		}
		else {
		    $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
		    if (count($_SESSION["buysell_uniq"]) > 3) {
		    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
		    }
		    
			// Link::redirect('transactions.php',array('transactions'=>$operations['transactions']));
			exit;
		}
	}
}

$notice = '';
if ($ask_confirm && $sell) {
	if (!$bank_accounts && $currency_info['is_crypto'] != 'Y')
		$notice .= '<div class="message-box-wrap">'.str_replace('[currency]',$currency_info['currency'],Lang::string('buy-errors-no-bank-account')).'</div>';
	
	if (($buy_limit && $buy_stop) || ($sell_limit && $sell_stop))
		$notice .= '<div class="message-box-wrap">'.Lang::string('buy-notify-two-orders').'</div>';
}

$select = "" ;
foreach ($CFG->currencies as $key => $currency) {
	if (is_numeric($key) || $currency['is_crypto'] != 'Y')
		continue;
	if($c_currency1 == $currency['id'])
		$select = $currency['currency'] ;
	// $select .= '<option value="'.$currency['id'].'" '.($c_currency1 == $currency['id'] ? 'selected="selected"' : '').'>'.$currency['currency'].'</option>';
}


$page_title = Lang::string('buy-sell');
if (!$bypass) {
	$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
	if (count($_SESSION["buysell_uniq"]) > 3) {
		unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
	}
	
	include 'includes/head.php';	
?>
<style>
.content_right{
	max-width: initial;
    position: static;
    float: right;
    width: 68%;
}
.nav-left{
	display: inline-block;
    width: 30%;
    padding: 50px 0px 0px 0px;
    overflow-x: auto;
}
/*.nav-left .table-list, .nav-left .table-list tr,
.nav-left .table-list td, .nav-left .trades th{
	background-color: #161616 !important;
    border: none;
    color: #fff !important;
}
.nav-left .trades th{
	font-weight: 400;
    font-size: 14px;
}
.nav-left .table-list td{
	max-width: 50px;
    overflow: hidden;
    text-overflow: ellipsis;
}
.nav-left .table-list td:focus{
	border:1px dashed #fff;
}
.nav-left .table-list.trades{
	padding:5px;
}*/
@media only screen and (max-width: 999px)
{
.container {
    width: 91%;
}
}
@media only screen and (max-width: 880px){
	.content_right,.nav-left{
		width: 100%;
	}
}
.highlgt{
	font-weight: 800 !important;
}
.highlgt .box{
	 background:#4a4c4c !important;
}
.nav-left .trades td a{
	display: block;
	color: #fff;
	min-height: 22px;
}
</style>
<div class="page_title">
	<div class="container">
		<div class="title"><h1><?= $page_title ?></h1></div>
        <div class="pagenation">&nbsp;<a href="index.php"><?= Lang::string('home') ?></a> <i>/</i> <a href="account.php"><?= Lang::string('account') ?></a> <i>/</i> <a href="buy-sell.php?trade=<?= $market ?>"><?= $page_title ?></a></div>
	</div>
</div>
<div class="container">
	<div class="content_right">
		<? Errors::display(); ?>
		<?= ($notice) ? '<div class="notice">'.$notice.'</div>' : '' ?>
		<div class="testimonials-4">
			<? if (!$ask_confirm) { ?>
			<input type="hidden" id="is_crypto" value="<?= $currency_info['is_crypto'] ?>" />
			<input type="hidden" id="user_fee" value="<?= $user_fee_both['fee'] ?>" />
			<input type="hidden" id="user_fee1" value="<?= $user_fee_both['fee1'] ?>" />
			<? include 'includes/graph.php'; ?>
			<div class="one_half">
				<div class="content">
					<h3 class="section_label">
						<input id="c_currency" type="text" value="<?= $c_currency1 ?>" style="display:none;">
						<span class="right"><?= str_replace('[c_currency]',$select,Lang::string('buy-bitcoins')) ?></span>
					</h3>
					<div class="clear"></div>
					<form id="buy_form" action="buy-sell.php?trade=<?= $market ?>" method="POST">
						<div class="buyform">
							<div class="spacer"></div>
							<div class="calc dotted">
								<div class="label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-fiat-available')) ?></div>
								<div class="value"><span class="buy_currency_char"><?= $currency_info['fa_symbol'] ?></span><a id="buy_user_available" href="#" title="<?= Lang::string('orders-click-full-buy') ?>"><?= ((!empty($user_available[strtoupper($currency_info['currency'])])) ? Stringz::currency($user_available[strtoupper($currency_info['currency'])],($currency_info['is_crypto'] == 'Y')) : '0.00') ?></a></div>
								<div class="clear"></div>
							</div>
							<div class="spacer"></div>
							<div class="param">
								<label for="buy_amount"><?= Lang::string('buy-amount') ?></label>
								<input name="buy_amount" id="buy_amount" type="text" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
								<div class="qualify"><?= $c_currency_info['currency'] ?></div>
								<div class="clear"></div>
							</div>
							<div class="param">
								<label for="buy_currency"><?= Lang::string('buy-with-currency') ?></label>
								<select id="buy_currency" name="currency">
								<?
								if ($CFG->currencies) {
									foreach ($CFG->currencies as $key => $currency) {

										if (is_numeric($key) || $key == $c_currency_info['currency'])
											continue;
										
										echo '<option '.(($currency['id'] == $currency1) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
									}
								}	
								?>
								</select>
								<div class="clear"></div>
							</div>
							<div class="param lessbottom">
								<input class="checkbox" name="buy_market_price" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1 && !$buy_stop) ? 'checked="checked"' : '' ?> <?= (!$asks) ? 'readonly="readonly"' : '' ?> />
								<label for="buy_market_price"><?= Lang::string('buy-market-price') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href=""><i class="fa fa-question-circle"></i></a></label>
								<div class="clear"></div>
							</div>
							<div class="param lessbottom">
								<input class="checkbox" name="buy_limit" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="buy_limit"><?= Lang::string('buy-limit') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href=""><i class="fa fa-question-circle"></i></a></label>
								<div class="clear"></div>
							</div>
							<div class="param lessbottom">
								<input class="checkbox" name="buy_stop" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="buy_stop"><?= Lang::string('buy-stop') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href="help.php?url=support/solutions/articles/8000018720-hva%C3%B0-er-tilbo%C3%B0-%C3%A1-l%C3%A1gmarksver%C3%B0i- "><i class="fa fa-question-circle"></i></a></label>
								<div class="clear"></div>
							</div>
							<div id="buy_price_container" class="param" <?= (!$buy_limit && !$buy_market_price1) ? 'style="display:none;"' : '' ?>>
								<label for="buy_price"><span id="buy_price_limit_label" <?= (!$buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="buy_price_market_label" <?= ($buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></label>
								<input name="buy_price" id="buy_price" type="text" value="<?= Stringz::currencyOutput($buy_price1) ?>" <?= ($buy_market_price1) ? 'readonly="readonly"' : '' ?> />
								<div class="qualify"><span class="buy_currency_label"><?= $currency_info['currency'] ?></span></div>
								<div class="clear"></div>
							</div>
							<div id="buy_stop_container" class="param" <?= (!$buy_stop) ? 'style="display:none;"' : '' ?>>
								<label for="buy_stop_price"><?= Lang::string('buy-stop-price') ?></label>
								<input name="buy_stop_price" id="buy_stop_price" type="text" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>" />
								<div class="qualify"><span class="buy_currency_label"><?= $currency_info['currency'] ?></span></div>
								<div class="clear"></div>
							</div>
							<div class="spacer"></div>
							<div class="calc">
								<div class="label"><?= Lang::string('buy-subtotal') ?></div>
								<div class="value"><span class="buy_currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="buy_subtotal"><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></div>
								<div class="clear"></div>
							</div>
							<div class="calc">
								<div class="label"><?= Lang::string('buy-fee') ?> <a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule.php"><i class="fa fa-question-circle"></i></a></div>
								<div class="value"><span id="buy_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</div>
								<div class="clear"></div>
							</div>
							<div class="calc bigger">
								<div class="label">
									<span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
									<span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
								</div>
								<div class="value"><span class="buy_currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></div>
								<div class="clear"></div>
							</div>
							<input type="hidden" name="buy" value="1" />
							<input type="hidden" name="buy_all" id="buy_all" value="<?= $buy_all1 ?>" />
							<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
							<input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('buy-bitcoins')) ?>" class="but_user" />
						</div>
					</form>
				</div>
			</div>
			<div class="one_half last">
				<div class="content">
					<h3 class="section_label">
						<span class="right"><?= str_replace('[c_currency]',$select,Lang::string('sell-bitcoins')) ?></span>
					</h3>
					<div class="clear"></div>
					<form id="sell_form" action="buy-sell.php?trade=<?= $market ?>" method="POST">
						<div class="buyform">
							<div class="spacer"></div>
							<div class="calc dotted">
								<div class="label"><?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-btc-available')) ?></div>
								<div class="value"><a id="sell_user_available"  href="#" title="<?= Lang::string('orders-click-full-sell') ?>"><?= Stringz::currency($user_available[strtoupper($c_currency_info['currency'])],true) ?></a> <?= $c_currency_info['currency']?></div>
								<div class="clear"></div>
							</div>
							<div class="spacer"></div>
							<div class="param">
								<label for="sell_amount"><?= Lang::string('sell-amount') ?></label>
								<input name="sell_amount" id="sell_amount" type="text" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
								<div class="qualify"><?= $c_currency_info['currency'] ?></div>
								<div class="clear"></div>
							</div>
							<div class="param">
								<label for="sell_currency"><?= Lang::string('buy-with-currency') ?></label>
								<select id="sell_currency" name="currency">
								<?
								if ($CFG->currencies) {
									foreach ($CFG->currencies as $key => $currency) {
										if (is_numeric($key) || $key == $c_currency_info['currency'])
											continue;
										
										echo '<option '.(($currency['id'] == $currency1) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
									}
								}	
								?>
								</select>
								<div class="clear"></div>
							</div>
							<div class="param lessbottom">
								<input class="checkbox" name="sell_market_price" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1 && !$sell_stop) ? 'checked="checked"' : '' ?> <?= (!$bids) ? 'readonly="readonly"' : '' ?> />
								<label for="sell_market_price"><?= Lang::string('sell-market-price') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href=""><i class="fa fa-question-circle"></i></a></label>
								<div class="clear"></div>
							</div>
							<div class="param lessbottom">
								<input class="checkbox" name="sell_limit" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="sell_stop"><?= Lang::string('buy-limit') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href=""><i class="fa fa-question-circle"></i></a></label>
								<div class="clear"></div>
							</div>
							<div class="param lessbottom">
								<input class="checkbox" name="sell_stop" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="sell_stop"><?= Lang::string('buy-stop') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href="help.php?url=support/solutions/articles/8000018720-hva%C3%B0-er-tilbo%C3%B0-%C3%A1-l%C3%A1gmarksver%C3%B0i- "><i class="fa fa-question-circle"></i></a></label>
								<div class="clear"></div>
							</div>
							<div id="sell_price_container" class="param" <?= (!$sell_limit && !$sell_market_price1) ? 'style="display:none;"' : '' ?>>
								<label for="sell_price"><span id="sell_price_limit_label" <?= (!$sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="sell_price_market_label" <?= ($sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></label>
								<input name="sell_price" id="sell_price" type="text" value="<?= Stringz::currencyOutput($sell_price1) ?>" <?= ($sell_market_price1) ? 'readonly="readonly"' : '' ?> />
								<div class="qualify"><span class="sell_currency_label"><?= $currency_info['currency'] ?></span></div>
								<div class="clear"></div>
							</div>
							<div id="sell_stop_container" class="param" <?= (!$sell_stop) ? 'style="display:none;"' : '' ?>>
								<label for="sell_stop_price"><?= Lang::string('buy-stop-price') ?></label>
								<input name="sell_stop_price" id="sell_stop_price" type="text" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>" />
								<div class="qualify"><span class="sell_currency_label"><?= $currency_info['currency'] ?></span></div>
								<div class="clear"></div>
							</div>
							<div class="spacer"></div>
							<div class="calc">
								<div class="label"><?= Lang::string('buy-subtotal') ?></div>
								<div class="value"><span class="sell_currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="sell_subtotal"><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></div>
								<div class="clear"></div>
							</div>
							<div class="calc">
								<div class="label"><?= Lang::string('buy-fee') ?> <a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule.php"><i class="fa fa-question-circle"></i></a></div>
								<div class="value"><span id="sell_user_fee"><?= Stringz::currency($user_fee_ask) ?></span>%</div>
								<div class="clear"></div>
							</div>
							<div class="calc bigger">
								<div class="label">
									<span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
									<span id="sell_total_label" style="display:none;"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total')) ?></span>
								</div>
								<div class="value"><span class="sell_currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></div>
								<div class="clear"></div>
							</div>
							<input type="hidden" name="sell" value="1" />
							<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
							<input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-bitcoins')) ?>" class="but_user" />
						</div>
					</form>
				</div>
			</div>
			<? } else { ?>
			<div class="one_half last">
				<div class="content">
					<h3 class="section_label">
						<span class="left"><i class="fa fa-exclamation fa-2x"></i></span>
						<span class="right"><?= Lang::string('confirm-transaction') ?></span>
						<div class="clear"></div>
					</h3>
					<div class="clear"></div>
					<form id="confirm_form" action="buy-sell.php?trade=<?= $market ?>" method="POST">
						<input type="hidden" name="confirmed" value="1" />
						<input type="hidden" id="buy_all" name="buy_all" value="<?= $buy_all1 ?>" />
						<input type="hidden" id="cancel" name="cancel" value="" />
						<? if ($buy) { ?>
						<div class="balances" style="margin-left:0;">
							<div class="label"><?= Lang::string('buy-amount') ?></div>
							<div class="amount"><?= Stringz::currency($buy_amount1,true) ?></div>
							<input type="hidden" name="buy_amount" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
							<div class="label"><?= Lang::string('buy-with-currency') ?></div>
							<div class="amount"><?= $currency_info['currency'] ?></div>
							<input type="hidden" name="buy_currency" value="<?= $currency1 ?>" />
							<? if ($buy_limit || $buy_market_price1) { ?>
							<div class="label"><?= ($buy_market_price1) ? Lang::string('buy-price') : Lang::string('buy-limit-price') ?></div>
							<div class="amount"><?= Stringz::currency($buy_price1,($currency_info['is_crypto'] == 'Y')) ?></div>
							<input type="hidden" name="buy_price" value="<?= Stringz::currencyOutput($buy_price1) ?>" />
							<? } ?>
							<? if ($buy_stop) { ?>
							<div class="label"><?= Lang::string('buy-stop-price') ?></div>
							<div class="amount"><?= Stringz::currency($buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></div>
							<input type="hidden" name="buy_stop_price" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>" />
							<? } ?>
						</div>
						<div class="buyform">
							<? if ($buy_market_price1) { ?>
							<div class="mar_top1"></div>
							<div class="param lessbottom">
								<input disabled="disabled" class="checkbox" name="dummy" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="buy_market_price"><?= Lang::string('buy-market-price') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href="help.php?url=support/solutions/articles/8000018719-hva%C3%B0-er-maka%C3%B0stilbo%C3%B0-e%C3%B0a-vi%C3%B0skipti-%C3%A1-marka%C3%B0sver%C3%B0i-"><i class="fa fa-question-circle"></i></a></label>
								<input type="hidden" name="buy_market_price" value="<?= $buy_market_price1 ?>" />
								<div class="clear"></div>
							</div>
							<? } ?>
							<? if ($buy_limit) { ?>
							<div class="mar_top1"></div>
							<div class="param lessbottom">
								<input disabled="disabled" class="checkbox" name="dummy" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="buy_limit"><?= Lang::string('buy-limit') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href="help.php?url=support/solutions/articles/8000018721-hva%C3%B0-er-fast-ver%C3%B0tilbo%C3%B0-"><i class="fa fa-question-circle"></i></a></label>
								<input type="hidden" name="buy_limit" value="<?= $buy_limit ?>" />
								<div class="clear"></div>
							</div>
							<? } ?>
							<? if ($buy_stop) { ?>
							<div class="mar_top1"></div>
							<div class="param lessbottom">
								<input disabled="disabled" class="checkbox" name="dummy" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="buy_stop"><?= Lang::string('buy-stop') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href="help.php?url=support/solutions/articles/8000018720-hva%C3%B0-er-tilbo%C3%B0-%C3%A1-l%C3%A1gmarksver%C3%B0i-"><i class="fa fa-question-circle"></i></a></label>
								<input type="hidden" name="buy_stop" value="<?= $buy_stop ?>" />
								<div class="clear"></div>
							</div>
							<? } ?>
							<div class="spacer"></div>
							<div class="calc">
								<div class="label"><?= Lang::string('buy-subtotal') ?></div>
								<div class="value"><span class="sell_currency_char"><?= $currency_info['fa_symbol'] ?></span><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></div>
								<div class="clear"></div>
							</div>
							<div class="calc">
								<div class="label"><?= Lang::string('buy-fee') ?> <a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule.php"><i class="fa fa-question-circle"></i></a></div>
								<div class="value"><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</div>
								<div class="clear"></div>
							</div>
							<div class="calc bigger">
								<div class="label">
									<span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
									<span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
								</div>
								<div class="value"><span class="buy_currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></div>
								<div class="clear"></div>
							</div>
							<input type="hidden" name="buy" value="1" />
							<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
						</div>
						<ul class="list_empty">
							<li style="margin-bottom:0;"><input type="submit" name="submit" value="<?= Lang::string('confirm-buy') ?>" class="but_user" /></li>
							<li style="margin-bottom:0;"><input id="cancel_transaction" type="submit" name="dont" value="<?= Lang::string('confirm-back') ?>" class="but_user grey" /></li>
						</ul>
						<div class="clear"></div>
						<? } else { ?>
						<div class="balances" style="margin-left:0;">
							<div class="label"><?= Lang::string('sell-amount') ?></div>
							<div class="amount"><?= Stringz::currency($sell_amount1,true) ?></div>
							<input type="hidden" name="sell_amount" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
							<div class="label"><?= Lang::string('buy-with-currency') ?></div>
							<div class="amount"><?= $currency_info['currency'] ?></div>
							<input type="hidden" name="sell_currency" value="<?= $currency1 ?>" />
							<? if ($sell_limit || $sell_market_price1) { ?>
							<div class="label"><?= ($sell_market_price1) ? Lang::string('buy-price') : Lang::string('buy-limit-price') ?></div>
							<div class="amount"><?= Stringz::currency($sell_price1,($currency_info['is_crypto'] == 'Y')) ?></div>
							<input type="hidden" name="sell_price" value="<?= Stringz::currencyOutput($sell_price1) ?>" />
							<? } ?>
							<? if ($sell_stop) { ?>
							<div class="label"><?= Lang::string('buy-stop-price') ?></div>
							<div class="amount"><?= Stringz::currency($sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></div>
							<input type="hidden" name="sell_stop_price" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>" />
							<? } ?>
						</div>
						<div class="buyform">
							<? if ($sell_market_price1) { ?>
							<div class="mar_top1"></div>
							<div class="param lessbottom">
								<input disabled="disabled" class="checkbox" name="dummy" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="sell_market_price"><?= Lang::string('sell-market-price') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href="help.php?url=support/solutions/articles/8000018719-hva%C3%B0-er-maka%C3%B0stilbo%C3%B0-e%C3%B0a-vi%C3%B0skipti-%C3%A1-marka%C3%B0sver%C3%B0i-"><i class="fa fa-question-circle"></i></a></label>
								<input type="hidden" name="sell_market_price" value="<?= $sell_market_price1 ?>" />
								<div class="clear"></div>
							</div>
							<? } ?>
							<? if ($sell_limit) { ?>
							<div class="mar_top1"></div>
							<div class="param lessbottom">
								<input disabled="disabled" class="checkbox" name="dummy" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="sell_limit"><?= Lang::string('buy-limit') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href="help.php?url=support/solutions/articles/8000018721-hva%C3%B0-er-fast-ver%C3%B0tilbo%C3%B0-"><i class="fa fa-question-circle"></i></a></label>
								<input type="hidden" name="sell_limit" value="<?= $sell_limit ?>" />
								<div class="clear"></div>
							</div>
							<? } ?>
							<? if ($sell_stop) { ?>
							<div class="mar_top1"></div>
							<div class="param lessbottom">
								<input disabled="disabled" class="checkbox" name="dummy" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
								<label for="sell_stop"><?= Lang::string('buy-stop') ?> <a title="<?= Lang::string('buy-market-rates-info') ?>" href="help.php?url=support/solutions/articles/8000018720-hva%C3%B0-er-tilbo%C3%B0-%C3%A1-l%C3%A1gmarksver%C3%B0i- "><i class="fa fa-question-circle"></i></a></label>
								<input type="hidden" name="sell_stop" value="<?= $sell_stop ?>" />
								<div class="clear"></div>
							</div>
							<? } ?>
							<div class="spacer"></div>
							<div class="calc">
								<div class="label"><?= Lang::string('buy-subtotal') ?></div>
								<div class="value"><span class="sell_currency_char"><?= $currency_info['fa_symbol'] ?></span><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></div>
								<div class="clear"></div>
							</div>
							<div class="calc">
								<div class="label"><?= Lang::string('buy-fee') ?> <a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule.php"><i class="fa fa-question-circle"></i></a></div>
								<div class="value"><span id="sell_user_fee"><?= Stringz::currency($user_fee_ask) ?></span>%</div>
								<div class="clear"></div>
							</div>
							<div class="calc bigger">
								<div class="label">
									<span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
									<span id="sell_total_label" style="display:none;"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total')) ?></span>
								</div>
								<div class="value"><span class="sell_currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></div>
								<div class="clear"></div>
							</div>
							<input type="hidden" name="sell" value="1" />
							<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
						</div>
						<ul class="list_empty">
							<li style="margin-bottom:0;"><input type="submit" name="submit" value="<?= Lang::string('confirm-sale') ?>" class="but_user" /></li>
							<li style="margin-bottom:0;"><input id="cancel_transaction" type="submit" name="dont" value="<?= Lang::string('confirm-back') ?>" class="but_user grey" /></li>
						</ul>
						<div class="clear"></div>
						<? } ?>
					</form>
				</div>
			</div>
			<? } ?>
		</div>
		<div class="mar_top3"></div>
		<div class="clear"></div>
		<div id="filters_area">
<? } ?>
			<? if (!$ask_confirm) { ?>
			<div class="one_half">
				<h3><?= Lang::string('orders-bid-top-10') ?></h3>
	        	<div class="table-style">
	        		<table class="table-list trades" id="bids_list">
	        			<tr>
	        				<th><?= Lang::string('orders-price') ?></th>
	        				<th><?= Lang::string('orders-amount') ?></th>
	        				<th><?= Lang::string('orders-value') ?></th>
	        			</tr>
	        			<? 
	        			if ($bids) {
							foreach ($bids as $bid) {
								$mine = (!empty(User::$info['user']) && $bid['user_id'] == User::$info['user'] && $bid['btc_price'] == $bid['fiat_price']) ? '<a class="fa fa-user" href="open-orders.php?id='.$bid['id'].'" title="'.Lang::string('home-your-order').'"></a>' : '';
								echo '
						<tr id="bid_'.$bid['id'].'" class="bid_tr">
							<td>'.$mine.'<span class="buy_currency_char">'.$currency_info['fa_symbol'].'</span><a class="order_price click" title="'.Lang::string('orders-click-price-sell').'" href="#">'.Stringz::currency($bid['btc_price'],($currency_info['is_crypto'] == 'Y')).'</a> '.(($bid['btc_price'] != $bid['fiat_price']) ? '<a title="'.str_replace('[currency]',$CFG->currencies[$bid['currency']]['currency'],Lang::string('orders-converted-from')).'" class="fa fa-exchange" href="" onclick="return false;"></a>' : '').'</td>
							<td><a class="order_amount click" title="'.Lang::string('orders-click-amount-sell').'" href="#">'.Stringz::currency($bid['btc'],true).'</a></td>
							<td><span class="buy_currency_char">'.$currency_info['fa_symbol'].'</span><span class="order_value">'.Stringz::currency(($bid['btc_price'] * $bid['btc']),($currency_info['is_crypto'] == 'Y')).'</span></td>
						</tr>';
							}
						}
						echo '<tr id="no_bids" style="'.(is_array($bids) && count($bids) > 0 ? 'display:none;' : '').'"><td colspan="4">'.Lang::string('orders-no-bid').'</td></tr>';
	        			?>
	        		</table>
				</div>
			</div>
			<div class="one_half last">
				<h3><?= Lang::string('orders-ask-top-10') ?></h3>
				<div class="table-style">
					<table class="table-list trades" id="asks_list">
						<tr>
							<th><?= Lang::string('orders-price') ?></th>
	        				<th><?= Lang::string('orders-amount') ?></th>
	        				<th><?= Lang::string('orders-value') ?></th>
						</tr>
	        			<? 
	        			if ($asks) {
							foreach ($asks as $ask) {
								$mine = (!empty(User::$info['user']) && $ask['user_id'] == User::$info['user'] && $ask['btc_price'] == $ask['fiat_price']) ? '<a class="fa fa-user" href="open-orders.php?id='.$ask['id'].'" title="'.Lang::string('home-your-order').'"></a>' : '';
								echo '
						<tr id="ask_'.$ask['id'].'" class="ask_tr">
							<td>'.$mine.'<span class="buy_currency_char">'.$currency_info['fa_symbol'].'</span><a class="order_price click" title="'.Lang::string('orders-click-price-buy').'" href="#">'.Stringz::currency($ask['btc_price'],($currency_info['is_crypto'] == 'Y')).'</a> '.(($ask['btc_price'] != $ask['fiat_price']) ? '<a title="'.str_replace('[currency]',$CFG->currencies[$ask['currency']]['currency'],Lang::string('orders-converted-from')).'" class="fa fa-exchange" href="" onclick="return false;"></a>' : '').'</td>
							<td><a class="order_amount click" title="'.Lang::string('orders-click-amount-buy').'" href="#">'.Stringz::currency($ask['btc'],true).'</a></td>
							<td><span class="buy_currency_char">'.$currency_info['fa_symbol'].'</span><span class="order_value">'.Stringz::currency(($ask['btc_price'] * $ask['btc']),($currency_info['is_crypto'] == 'Y')).'</span></td>
						</tr>';
							}
						}
						echo '<tr id="no_asks" style="'.(is_array($asks) && count($asks) > 0 ? 'display:none;' : '').'"><td colspan="4">'.Lang::string('orders-no-ask').'</td></tr>';
	        			?>
					</table>
				</div>
				<div class="clear"></div>
			</div>
			<? } ?>
<? if (!$bypass) { ?>
		</div>
		<div class="mar_top5"></div>
	</div>
	<div class="nav-left">
	<div class="sidebar_title"><h3>Markets</h3></div>
		
		<div class="boxer">
		  <div class="box-row">
		  	<div class="box">Market</div>
		    <div class="box">Last Price</div>
		    <div class="box">24hrs Volume</div>
		    <div class="box">24hrs Change</div>
		  </div>
  
		  <div class="box-row hg <? if($market == "BTC-USD") echo 'highlgt'; ?>" >
		    <div class="box"><a href="buy-sell.php?trade=BTC-USD">BTC/USD</a></div>
		    <div class="box"><a href="buy-sell.php?trade=BTC-USD"><? echo $transactions_24hrs_btc_usd['lastPrice'] ?></a></div>
		    <div class="box"><a href="buy-sell.php?trade=BTC-USD"><? echo $transactions_24hrs_btc_usd['transactions_24hrs'] ?></a></div>
		    <div class="box"><a href="buy-sell.php?trade=BTC-USD"><? echo $transactions_24hrs_btc_usd['change_24hrs'] ?></a></div>
		  </div>

		  <div class="box-row hg <? if($market == "LTC-USD") echo 'highlgt'; ?>">
		    <div class="box"><a href="buy-sell.php?trade=LTC-USD">LTC/USD</a></div>
		    <div class="box"><a href="buy-sell.php?trade=LTC-USD"><? echo $transactions_24hrs_ltc_usd['lastPrice'] ?></a></div>
		    <div class="box"><a href="buy-sell.php?trade=LTC-USD"><? echo $transactions_24hrs_ltc_usd['transactions_24hrs'] ?></a></div>
		    <div class="box"><a href="buy-sell.php?trade=LTC-USD"><? echo $transactions_24hrs_ltc_usd['change_24hrs'] ?></a></div>
		  </div>
		  <div class="box-row hg <? if($market == "LTC-BTC") echo 'highlgt'; ?>">
		    <div class="box"><a href="buy-sell.php?trade=LTC-BTC">LTC/BTC</a></div>
		    <div class="box"><a href="buy-sell.php?trade=LTC-BTC"><? echo $transactions_24hrs_ltc_btc['lastPrice'] ?></a></div>
		    <div class="box"><a href="buy-sell.php?trade=LTC-BTC"><? echo $transactions_24hrs_ltc_btc['transactions_24hrs'] ?></a></div>
		    <div class="box"><a href="buy-sell.php?trade=LTC-BTC"><? echo $transactions_24hrs_ltc_btc['change_24hrs'] ?></a></div>
		  </div>
		 
		</div>

		<!-- <table class="table-list trades">
			<tbody>
			<tr id="table_first">
				<th></th>
				<th>Price</th>
				<th>Volume</th>
				<th>24th Price</th>
			</tr>
			<tr class="one">
				<td><a href="buy-sell.php?trade=BTC-USD">BTC/USD</a></td>
				<td><a href="buy-sell.php?trade=BTC-USD">1</a></td>
				<td><a href="buy-sell.php?trade=BTC-USD">2</a></td>
				<td><a href="buy-sell.php?trade=BTC-USD">3</a></td>
			</tr>
			<a>
			<tr class="two">
				<td><a href="buy-sell.php?trade=LTC-USD">LTC/USD</a></td>
				<td><a href="buy-sell.php?trade=LTC-USD"></a></td>
				<td><a href="buy-sell.php?trade=LTC-USD"></a></td>
				<td><a href="buy-sell.php?trade=LTC-USD"></a></td>
			</tr>  
			<tr class="three">
				<td><a href="buy-sell.php?trade=LTC-BTC">LTC/BTC</a></td>
				<td><a href="buy-sell.php?trade=LTC-BTC"></a></td>
				<td><a href="buy-sell.php?trade=LTC-BTC"></a></td>
				<td><a href="buy-sell.php?trade=LTC-BTC"></a></td>
			</tr>      		
			</tbody>
		</table> -->
		
		<!-- <div class="one_fourth">&nbsp;</div>
		<div class="one_fourth">Price</div>
		<div class="one_fourth">Volume</div>
		<div class="one_fourth">24h Price</div>
		<ul class="">
		
			<li><a href="buy-sell.php?trade=BTC-USD>
				<div class="one_fourth">BTC/USD</div>
				<div class="one_fourth">&nbsp;</div>
				<div class="one_fourth">&nbsp;</div>
				<div class="one_fourth">&nbsp;</div>
			</a></li>
			<li><a href="buy-sell.php?trade=LTC-USD">
				<div class="one_fourth">LTC/USD</div>
				<div class="one_fourth">&nbsp;</div>
				<div class="one_fourth">&nbsp;</div>
				<div class="one_fourth">&nbsp;</div>
			</a></li>
			
		</ul> -->
	</div>
	<div class="mar_top8"></div>
	<div class="clear"></div>
</div>
<style>
	.boxer {
	  display: table;
	  border-collapse: collapse;
	  -webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2);
	  box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2);
	  margin-bottom: 1em;
	      width: 99%;
    margin: auto;
	}
	.boxer .box-row a{
		color:#fff;
	}
	.boxer .box-row {
	  display: table-row;
	  border:1px solid #6b5b5b;
	}
	.boxer .box-row:first-child {
	  font-weight:bold;
	}
	.boxer .box {
	  display: table-cell;
	  vertical-align: top;
	  padding: 15px 10px;
	  background-color: #161616;
	  color: #fff;
	  font-size: 14px;
	}

	.center {
	  text-align:center;
	}
	.right {
	  float:right;
	}
	.nav-left::-webkit-scrollbar {
	    width: 8px;
	    height: 8px
	}

	.nav-left::-webkit-scrollbar-thumb {
	    background: #666;
	    -webkit-border-radius: 0;
	    -moz-border-radius: 0;
	    -ms-border-radius: 0;
	    border-radius: 0
	}

	.nav-left::-webkit-scrollbar-track {
	    background: #ddd;
	    -webkit-border-radius: 0;
	    -moz-border-radius: 0;
	    -ms-border-radius: 0;
	    border-radius: 0;
	    background-clip: content-box
	}

</style>
<? include 'includes/foot.php'; ?>
<? } ?>
<script>
$(".box-row.hg").click(function(){
$(".box-row.hg").removeClass("highlgt");
    $(this).addClass("highlgt");
});

</script>