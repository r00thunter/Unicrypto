<!doctype html>
<html>
<head>
<title><?= $CFG->exchange_name; ?> Buy/Sell</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/new-style.css" rel="stylesheet" />
<link href="css/buy.css" rel="stylesheet" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1.0" data-react-helmet="true">

</head>

<body class="app signed-in static_application index" data-controller-name="static_application" data-action-name="index" data-view-name="Coinbase.Views.StaticApplication.Index" data-account-id="">
<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
	Link::redirect('userprofile.php');
elseif (User::$awaiting_token)
	Link::redirect('verify-token.php');
elseif (!User::isLoggedIn())
	Link::redirect('login.php');

// 	if(empty(User::$ekyc_data) || User::$ekyc_data[0]->status != 'accepted')
// {
//     Link::redirect('ekyc.php');
// }

$market = $_GET['trade'];
$currencies = Settings::sessionCurrency();
// print_r($currencies); exit;
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
}
$currency_info = $CFG->currencies[$currency1];
$c_currency_info = $CFG->currencies[$c_currency1];
// echo "<pre>"; print_r($c_currency_info); exit;
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
// echo "<pre>". print_r(User::$info); exit;

API::add('User','hasCurrencies');
API::add('Orders','getBidAsk',array($c_currency1,$currency1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,false,false,1));
API::add('Transactions','get',array(false,false,1,$c_currency1,$currency1));
API::add('Transactions','get24hData',array(28,27));
API::add('Transactions','get24hData',array(42,27));
API::add('Transactions','get24hData',array(42,28));
API::add('Transactions','get24hData',array(43,27));
API::add('Transactions','get24hData',array(43,28));
API::add('Transactions','get24hData',array(43,27));
API::add('Transactions','get24hData',array(43,28));
API::add('Transactions','get24hData',array(45,27));
API::add('Transactions','get24hData',array(45,28));
API::add('Transactions','get24hData',array(42,45));
API::add('Transactions','get24hData',array(43,45));
API::add('Transactions','get24hData',array(44,27));
API::add('Transactions','get24hData',array(44,28));

if ($currency_info['is_crypto'] != 'Y')
	API::add('BankAccounts','get',array($currency_info['id']));

$query = API::send();
$total = $query['Transactions']['get']['results'][0];
$user_available_currencies = $query['User']['hasCurrencies']['results'];
$current_bid = $query['Orders']['getBidAsk']['results'][0]['bid'];
$current_ask =  $query['Orders']['getBidAsk']['results'][0]['ask'];
$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];

API::add('FeeSchedule','getRecord',array(User::$info['fee_schedule']));
API::add('User','getAvailable');
$feequery = API::send();
$user_fee_both = $feequery['FeeSchedule']['getRecord']['results'][0];
$user_available = $feequery['User']['getAvailable']['results'][0];
// echo "<pre>"; print_r($user_available); exit;
// echo "<pre>"; print_r($user_fee_both); exit;

$user_fee_bid = ($buy && ((Stringz::currencyInput($_REQUEST['buy_amount']) > 0 && Stringz::currencyInput($_REQUEST['buy_price']) >= $asks[0]['btc_price']) || !empty($_REQUEST['buy_market_price']) || empty($_REQUEST['buy_amount']))) ? $feequery['FeeSchedule']['getRecord']['results'][0]['fee'] : $feequery['FeeSchedule']['getRecord']['results'][0]['fee1'];
$user_fee_ask = ($sell && ((Stringz::currencyInput($_REQUEST['sell_amount']) > 0 && Stringz::currencyInput($_REQUEST['sell_price']) <= $bids[0]['btc_price']) || !empty($_REQUEST['sell_market_price']) || empty($_REQUEST['sell_amount']))) ? $feequery['FeeSchedule']['getRecord']['results'][0]['fee'] : $feequery['FeeSchedule']['getRecord']['results'][0]['fee1'];
$transactions = $query['Transactions']['get']['results'][0];
$usd_field = 'usd_ask';
$transactions_24hrs_btc_usd = $query['Transactions']['get24hData']['results'][0] ;
$transactions_24hrs_ltc_usd = $query['Transactions']['get24hData']['results'][1] ;
$transactions_24hrs_ltc_btc = $query['Transactions']['get24hData']['results'][2] ;
$transactions_24hrs_zec_usd = $query['Transactions']['get24hData']['results'][3] ;
$transactions_24hrs_zec_btc = $query['Transactions']['get24hData']['results'][4] ;
$transactions_24hrs_eth_usd = $query['Transactions']['get24hData']['results'][5] ;
$transactions_24hrs_eth_btc = $query['Transactions']['get24hData']['results'][6] ;
$transactions_24hrs_ltc_eth = $query['Transactions']['get24hData']['results'][7] ;
$transactions_24hrs_zec_eth = $query['Transactions']['get24hData']['results'][8] ;
$transactions_24hrs_bch_usd = $query['Transactions']['get24hData']['results'][9] ;
$transactions_24hrs_bch_btc = $query['Transactions']['get24hData']['results'][10] ;

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
// echo "string ".$buy_price1; exit;
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
		    
			Link::redirect('userexchange.php',array('transactions'=>$operations['transactions'],'new_order'=>1));
			exit;
		}
		else {
		    $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
		    if (count($_SESSION["buysell_uniq"]) > 3) {
		    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
		    }
		    
			Link::redirect('userexchange.php',array('transactions'=>$operations['transactions']));
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
		    
			Link::redirect('userexchange.php',array('transactions'=>$operations['transactions'],'new_order'=>1));
			exit;
		}
		else {
		    $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
		    if (count($_SESSION["buysell_uniq"]) > 3) {
		    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
		    }
		    
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
}


$page_title = Lang::string('buy-sell');
// if (!$bypass) {
	$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
	if (count($_SESSION["buysell_uniq"]) > 3) {
		unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
	}
	
	// echo "bl ".$buy_limit." sl ".$sell_limit; exit;
	// echo "USD ".$user_available[strtoupper($currency_info['currency'])]; exit;
?>

<div id="root">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Toasts__Container-kTLjCb jeFCaz"></div>
<div class="Layout__Container-jkalbK gCVQUv Flex__Flex-fVJVYW bHipRv">
<div class="LayoutDesktop__AppWrapper-cPGAqn WhXLX Flex__Flex-fVJVYW bHipRv">

<? include 'includes/topheader.php'; ?>
<div class="LayoutDesktop__ContentContainer-cdKOaO cpwUZB Flex__Flex-fVJVYW bHipRv">

<? include 'includes/menubar.php'; ?>
<div class="banner">
    <div class="container content">
        <h1>Simple Trade</h1>
    </div>
</div>
<div class="LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">

<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">
	<? Errors::display(); ?>
<?= ($notice) ? '<div class="notice">'.$notice.'</div>' : '' ?>
<div class="Trade__TradeView-eeHZtW jkvMB Flex__Flex-fVJVYW iDqRrV">
<div class="Trade__TradeFormContainer-lhJJLd hNLWXE Flex__Flex-fVJVYW iDqRrV">
	<? if (!$ask_confirm) { ?>
<div class="">
<div class="">
	<div class="table-otr" style="background-color:#fff;width: 96%;margin: 0 auto 1em;height: auto;">
		<table>
	  <tbody><tr>
	    <th>Market</th>
	    <th>Last Price</th>
	    <th>24hrs Volume</th>
		<th>24hrs Change</th>
	  </tr>
	  
		<tr <? if($market == "BTC-USD") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=BTC-USD">BTC/USD</a></td>
		    <td><a href="userbuy.php?trade=BTC-USD"><? echo $transactions_24hrs_btc_usd['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=BTC-USD"><? echo $transactions_24hrs_btc_usd['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=BTC-USD"><? echo $transactions_24hrs_btc_usd['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "LTC-USD") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=LTC-USD">LTC/USD</a></td>
		    <td><a href="userbuy.php?trade=LTC-USD"><? echo $transactions_24hrs_ltc_usd['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=LTC-USD"><? echo $transactions_24hrs_ltc_usd['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=LTC-USD"><? echo $transactions_24hrs_ltc_usd['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "LTC-BTC") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=LTC-BTC">LTC/BTC</a></td>
		    <td><a href="userbuy.php?trade=LTC-BTC"><? echo $transactions_24hrs_ltc_btc['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=LTC-BTC"><? echo $transactions_24hrs_ltc_btc['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=LTC-BTC"><? echo $transactions_24hrs_ltc_btc['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "LTC-ETH") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=LTC-ETH">LTC/ETH</a></td>
		    <td><a href="userbuy.php?trade=LTC-ETH"><? echo $transactions_24hrs_ltc_eth['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=LTC-ETH"><? echo $transactions_24hrs_ltc_eth['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=LTC-ETH"><? echo $transactions_24hrs_ltc_eth['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "ZEC-USD") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=ZEC-USD">ZEC/USD</a></td>
		    <td><a href="userbuy.php?trade=ZEC-USD"><? echo $transactions_24hrs_zec_usd['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=ZEC-USD"><? echo $transactions_24hrs_zec_usd['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=ZEC-USD"><? echo $transactions_24hrs_zec_usd['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "ZEC-BTC") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=ZEC-BTC">ZEC/BTC</a></td>
		    <td><a href="userbuy.php?trade=ZEC-BTC"><? echo $transactions_24hrs_zec_btc['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=ZEC-BTC"><? echo $transactions_24hrs_zec_btc['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=ZEC-BTC"><? echo $transactions_24hrs_zec_btc['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "ZEC-ETH") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=ZEC-ETH">ZEC/ETH</a></td>
		    <td><a href="userbuy.php?trade=ZEC-ETH"><? echo $transactions_24hrs_zec_eth['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=ZEC-ETH"><? echo $transactions_24hrs_zec_eth['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=ZEC-ETH"><? echo $transactions_24hrs_zec_eth['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "ETH-USD") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=ETH-USD">ETH/USD</a></td>
		    <td><a href="userbuy.php?trade=ETH-USD"><? echo $transactions_24hrs_eth_usd['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=ETH-USD"><? echo $transactions_24hrs_eth_usd['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=ETH-USD"><? echo $transactions_24hrs_eth_usd['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "ETH-BTC") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=ETH-BTC">ETH/BTC</a></td>
		    <td><a href="userbuy.php?trade=ETH-BTC"><? echo $transactions_24hrs_eth_btc['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=ETH-BTC"><? echo $transactions_24hrs_eth_btc['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=ETH-BTC"><? echo $transactions_24hrs_eth_btc['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "BCH-USD") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=BCH-USD">BCH/USD</a></td>
		    <td><a href="userbuy.php?trade=BCH-USD"><? echo $transactions_24hrs_bch_usd['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=BCH-USD"><? echo $transactions_24hrs_bch_usd['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=BCH-USD"><? echo $transactions_24hrs_bch_usd['change_24hrs'] ?></a></td>
		</tr>
		<tr <? if($market == "BCH-BTC") echo 'class="market-active"'; ?>>
			<td><a href="userbuy.php?trade=BCH-BTC">BCH/BTC</a></td>
		    <td><a href="userbuy.php?trade=BCH-BTC"><? echo $transactions_24hrs_bch_btc['lastPrice'] ?></a></td>
		    <td><a href="userbuy.php?trade=BCH-BTC"><? echo $transactions_24hrs_bch_btc['transactions_24hrs'] ?></a></td>
		    <td><a href="userbuy.php?trade=BCH-BTC"><? echo $transactions_24hrs_bch_btc['change_24hrs'] ?></a></td>
		</tr>
	</tbody></table>
	</div>
</div>

</div>
<?= ($notice) ? '<div class="notice">'.$notice.'</div>' : '' ?>
</div>

<!-- Buy Section Starts Here -->
<input type="hidden" id="is_crypto" value="<?= $currency_info['is_crypto'] ?>" />
<input type="hidden" id="user_fee" value="<?= $user_fee_both['fee'] ?>" />
<input type="hidden" id="user_fee1" value="<?= $user_fee_both['fee1'] ?>" />
<input type="hidden" id="c_currency" value="<?= $c_currency1 ?>">
<div class="TradeFormTabContainer__Container-cUyfJR eMNjQO Panel__Container-hCUKEb gmOPIV">
<!-- <div class="Flex__Flex-fVJVYW iDqRrV">
<div class="TradeFormTabContainer__Tab-caAlbq keHVTX Flex__Flex-fVJVYW iDqRrV" style="border-right: none;">
<a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf" >
	<input id="c_currency" type="text" value="<?= $c_currency1 ?>" style="display:none;">
	<span class="right"><?= str_replace('[c_currency]',$select,Lang::string('buy-bitcoins')) ?></span>
</a>

</div>
<input type="hidden" id="is_crypto" value="<?= $currency_info['is_crypto'] ?>" />
<input type="hidden" id="user_fee" value="<?= $user_fee_both['fee'] ?>" />
<input type="hidden" id="user_fee1" value="<?= $user_fee_both['fee1'] ?>" /> -->
<!-- <div class="TradeFormTabContainer__Tab-caAlbq hMgXGE Flex__Flex-fVJVYW iDqRrV">
<a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf" href="cryptokartsell.php">
<span>Sell</span>
</a>
</div> -->
<!-- </div> -->
<div class="Flex__Flex-fVJVYW iDqRrV">
	<div class="TradeFormTabContainer__Tab-caAlbq keHVTX Flex__Flex-fVJVYW iDqRrV buy-main">
		<a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf" href="#" id="buy"><span>Buy</span></a>
	</div>
	<div class="TradeFormTabContainer__Tab-caAlbq hMgXGE Flex__Flex-fVJVYW iDqRrV sell-main">
	<a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf" href="#" id="sell"><span>Sell</span></a>
	</div>
</div>
<div class="TradeFormTabContainer__Content-bTJPSU TMIzi Flex__Flex-fVJVYW gkSoIH buy_outer">
<div></div>
<div>
<div class="Flex__Flex-fVJVYW bHipRv">
<form id="buy_form" action="userbuy.php?trade=<?= $market ?>" method="POST">
<div class="calc dotted">
    <p>
        <?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-fiat-available')) ?>
        <span class="pull-right"><span class="buy_currency_char"><?= $currency_info['fa_symbol'] ?></span><a id="buy_user_available" href="#" title="<?= Lang::string('orders-click-full-buy') ?>"><?= ((!empty($user_available[strtoupper($currency_info['currency'])])) ? Stringz::currency($user_available[strtoupper($currency_info['currency'])],($currency_info['is_crypto'] == 'Y')) : '0.00') ?></a></span>
    </p>
</div>
<!-- <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= str_replace('[c_currency]',$select,Lang::string('buy-bitcoins')) ?></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        <select class="form-control">
            <option>BTC</option>
            <option>LTC</option>
        </select>
        </div>
    </div>
</div>
</div> -->
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('buy-amount') ?></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        <input name="buy_amount" id="buy_amount" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
        <div class="input-caption"><?= $c_currency_info['currency'] ?></div>
        </div>
    </div>
</div>
</div>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('buy-with-currency') ?></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        	<select id="buy_currency" name="currency" class="form-control">
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
        </div>
    </div>
</div>
</div>
<label class="cont"><?= Lang::string('buy-market-price') ?>
  <input class="checkbox" name="buy_market_price" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1 && !$buy_stop) ? 'checked="checked"' : '' ?> <?= (!$asks) ? 'readonly="readonly"' : '' ?> />
  <span class="checkmark"></span>
</label>
<label class="cont"><?= Lang::string('buy-limit') ?>
  <input class="checkbox" name="buy_limit" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
  <span class="checkmark"></span>
</label>
<label class="cont"><?= Lang::string('buy-stop') ?>
  <input class="checkbox" name="buy_stop" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
  <span class="checkmark"></span>
</label>

<div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" id="buy_price_container" <?= (!$buy_limit && !$buy_market_price1) ? 'style="display:none;"' : '' ?>>
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span id="buy_price_limit_label" <?= (!$buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="buy_price_market_label" <?= ($buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        	<input name="buy_price" id="buy_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_price1) ?>" <?= ($buy_market_price1) ? 'readonly="readonly"' : '' ?> />
        <div class="input-caption"><?= $currency_info['currency'] ?></div>
        </div>
    </div>
</div>
</div>
<div id="buy_stop_container" class="param" <?= (!$buy_stop) ? 'style="display:none;"' : '' ?>>
	<div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" >
	<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
	    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></h4>
	</div>
		<div>
		    <div class="Flex__Flex-fVJVYW gkSoIH">
		        <div class="form-group">
		        	<input name="buy_stop_price" id="buy_stop_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>">
		        <div class="input-caption"><?= $currency_info['currency'] ?></div>
		        </div>
		    </div>
		</div>
	</div>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-subtotal') ?> 
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_subtotal"><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-fee') ?> 
        <span class="pull-right"><span id="buy_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</span>
    </p>
</div>
<div class="current-otr m-b-15">
    <p>
        <span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
        <span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
<input type="hidden" name="buy" value="1" />
<input type="hidden" name="buy_all" id="buy_all" value="<?= $buy_all1 ?>" />
<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
<input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('buy-bitcoins')) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="padding: 15px 35px !important;" />

</form>
</div>
</div>
</div><!--buy-outer-->
<div class="TradeFormTabContainer__Content-bTJPSU TMIzi Flex__Flex-fVJVYW gkSoIH sell_outer" style="display: none;">
<div></div>
<div>
<div class="Flex__Flex-fVJVYW bHipRv">
<form id="sell_form" action="userbuy.php?trade=<?= $market ?>" method="POST">
<div class="calc dotted">
    <p>
        <?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-btc-available')) ?>
        <span class="pull-right"><a id="sell_user_available"  href="#" title="<?= Lang::string('orders-click-full-sell') ?>"><?= Stringz::currency($user_available[strtoupper($c_currency_info['currency'])],true) ?></a> <?= $c_currency_info['currency']?></span>
    </p>
</div>
<!-- <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">Sell</h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        <select class="form-control">
            <option>BTC</option>
            <option>LTC</option>
        </select>
        </div>
    </div>
</div>
</div> -->
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('sell-amount') ?></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        	<input name="sell_amount" id="sell_amount" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
        <div class="input-caption"><?= $c_currency_info['currency'] ?></div>
        </div>
    </div>
</div>
</div>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('buy-with-currency') ?></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        <select id="sell_currency" name="currency" class="form-control">
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
        </div>
    </div>
</div>
</div>
<label class="cont"><?= Lang::string('sell-market-price') ?>
	<input class="checkbox" name="sell_market_price" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1 && !$sell_stop) ? 'checked="checked"' : '' ?> <?= (!$bids) ? 'readonly="readonly"' : '' ?> />
  <span class="checkmark"></span>
</label>
<label class="cont"><?= Lang::string('buy-limit') ?>
  <input class="checkbox" name="sell_limit" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
  <span class="checkmark"></span>
</label>
<label class="cont"><?= Lang::string('buy-stop') ?>
  <input class="checkbox" name="sell_stop" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
  <span class="checkmark"></span>
</label>

<div id="sell_price_container" class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" <?= (!$sell_limit && !$sell_market_price1) ? 'style="display:none;"' : '' ?>>
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span id="sell_price_limit_label" <?= (!$sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="sell_price_market_label" <?= ($sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        	<input name="sell_price" id="sell_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_price1) ?>" <?= ($sell_market_price1) ? 'readonly="readonly"' : '' ?> />
        <div class="input-caption"><?= $currency_info['currency'] ?></div>
        </div>
    </div>
</div>
</div>
<div id="sell_stop_container" class="param" <?= (!$sell_stop) ? 'style="display:none;"' : '' ?>>
	<div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" >
	<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
	    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></h4>
	</div>
		<div>
		    <div class="Flex__Flex-fVJVYW gkSoIH">
		        <div class="form-group">
		        	<input name="sell_stop_price" id="sell_stop_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>">
		        <div class="input-caption"><?= $currency_info['currency'] ?></div>
		        </div>
		    </div>
		</div>
	</div>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-subtotal') ?>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_subtotal"><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-fee') ?> 
        <span class="pull-right"><span id="sell_user_fee"><?= Stringz::currency($user_fee_ask) ?></span>%</span>
    </p>
</div>
<div class="current-otr m-b-15">
    <p>
        <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
        <span id="sell_total_label" style="display:none;"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total')) ?></span>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
	<input type="hidden" name="sell" value="1" />
	<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
	<input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-bitcoins')) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="padding: 15px 35px !important;"/>
    <!-- <button class="Button__Container-hQftQV kZBVvC" disabled="">
        <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
            <div class="Flex__Flex-fVJVYW ghkoKS">Sell Bitcoin Instantly</div>
        </div>
    </button> -->
</form>
</div>
</div>
</div><!--sell-outer-->	
</div>

<!-- Buy Section Ends Here -->

<!-- Sell Section Starts Here -->
<div class="Trade__Preview-ftIHSO kkSOOR Flex__Flex-fVJVYW iDqRrV" style="display: none;">
<div class="Flex__Flex-fVJVYW aApUU">
<div class="TradeFormTabContainer__Container-cUyfJR eMNjQO Panel__Container-hCUKEb gmOPIV">
<div class="Flex__Flex-fVJVYW iDqRrV">

<div class="TradeFormTabContainer__Tab-caAlbq hMgXGE Flex__Flex-fVJVYW iDqRrV">
<a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf" >
<span><?= str_replace('[c_currency]',$select,Lang::string('sell-bitcoins')) ?></span>
</a>
</div>
</div>
<div class="TradeFormTabContainer__Content-bTJPSU TMIzi Flex__Flex-fVJVYW gkSoIH ">
<div></div>
<div>
<div class="Flex__Flex-fVJVYW bHipRv">
<!-- <form id="sell_form" action="userbuy.php?trade=<?= $market ?>" method="POST">
<div class="calc dotted">
    <p>
        <?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-btc-available')) ?>
        <span class="pull-right"><a id="sell_user_available"  href="#" title="<?= Lang::string('orders-click-full-sell') ?>"><?= Stringz::currency($user_available[strtoupper($c_currency_info['currency'])],true) ?></a> <?= $c_currency_info['currency']?></span>
    </p>
</div>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('sell-amount') ?></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        	<input name="sell_amount" id="sell_amount" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
        <div class="input-caption"><?= $c_currency_info['currency'] ?></div>
        </div>
    </div>
</div>
</div>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('buy-with-currency') ?></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        <select id="sell_currency" name="currency" class="form-control">
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
        </div>
    </div>
</div>
</div>
<label class="cont"><?= Lang::string('sell-market-price') ?>
	<input class="checkbox" name="sell_market_price" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1 && !$sell_stop) ? 'checked="checked"' : '' ?> <?= (!$bids) ? 'readonly="readonly"' : '' ?> />
  <span class="checkmark"></span>
</label>
<label class="cont"><?= Lang::string('buy-limit') ?>
  <input class="checkbox" name="sell_limit" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
  <span class="checkmark"></span>
</label>
<label class="cont"><?= Lang::string('buy-stop') ?>
  <input class="checkbox" name="sell_stop" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
  <span class="checkmark"></span>
</label>

<div id="sell_price_container" class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" <?= (!$sell_limit && !$sell_market_price1) ? 'style="display:none;"' : '' ?>>
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span id="sell_price_limit_label" <?= (!$sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="sell_price_market_label" <?= ($sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></h4>
</div>
<div>
    <div class="Flex__Flex-fVJVYW gkSoIH">
        <div class="form-group">
        	<input name="sell_price" id="sell_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_price1) ?>" <?= ($sell_market_price1) ? 'readonly="readonly"' : '' ?> />
        <div class="input-caption"><?= $currency_info['currency'] ?></div>
        </div>
    </div>
</div>
</div>
<div id="sell_stop_container" class="param" <?= (!$sell_stop) ? 'style="display:none;"' : '' ?>>
	<div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" >
	<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
	    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></h4>
	</div>
		<div>
		    <div class="Flex__Flex-fVJVYW gkSoIH">
		        <div class="form-group">
		        	<input name="sell_stop_price" id="sell_stop_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>">
		        <div class="input-caption"><?= $currency_info['currency'] ?></div>
		        </div>
		    </div>
		</div>
	</div>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-subtotal') ?>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_subtotal"><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-fee') ?> 
        <span class="pull-right"><span id="sell_user_fee"><?= Stringz::currency($user_fee_ask) ?></span>%</span>
    </p>
</div>
<div class="current-otr m-b-15">
    <p>
        <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
        <span id="sell_total_label" style="display:none;"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total')) ?></span>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
	<input type="hidden" name="sell" value="1" />
	<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
	<input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-bitcoins')) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="padding: 15px 35px !important;"/>
</form> -->
</div>
</div>
</div>
</div>
</div>
</div>

<!-- Sell Section Ends Here -->
</div>
</div>
<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">

<div class="Flex__Flex-fVJVYW gsOGkq">


<div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; margin-bottom:0px;">
<div class="Flex__Flex-fVJVYW bHipRv">
    <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">
            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('orders-bid-top-10') ?></h4>
        </div>
    </div>

    <div class="Flex__Flex-fVJVYW iJJJTg">
        <div class="Flex__Flex-fVJVYW bHipRv">
          <div class="table-otr">
            <table id="bids_list">
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
    </div>
</div>
</div>


<div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; width:599px; margin-bottom:0px;">
<div class="Flex__Flex-fVJVYW bHipRv">
    <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">

            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">
                <?= Lang::string('orders-ask-top-10') ?>
            </h4>
        </div>
    </div>
    <div class="Flex__Flex-fVJVYW iJJJTg">
         <div class="table-otr">
          <table id="asks_list">
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
    </div>
</div>
</div>
</div>

</div>
<? } else { ?>
<!-- Confirm Box Starts Here -->
<div class="TradeFormTabContainer__Container-cUyfJR eMNjQO Panel__Container-hCUKEb gmOPIV" style="max-width: 700px;margin: auto;width:100%;">
<div class="Flex__Flex-fVJVYW iDqRrV">
<div class="TradeFormTabContainer__Tab-caAlbq keHVTX Flex__Flex-fVJVYW iDqRrV" style="border-right: none;">
<a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf">
	<input id="c_currency" type="text" value="28" style="display:none;">
	<span class="right" style="font-size: 25px;margin-top:1em;"><?= Lang::string('confirm-transaction') ?></span>
</a>

</div>

</div>

<div class="TradeFormTabContainer__Content-bTJPSU TMIzi Flex__Flex-fVJVYW gkSoIH" style="min-height:auto;">
<div></div>
<div>
<div class="Flex__Flex-fVJVYW bHipRv">
	<form id="confirm_form" action="userbuy.php?trade=<?= $market ?>" method="POST">
	<input type="hidden" name="confirmed" value="1" />
	<input type="hidden" id="buy_all" name="buy_all" value="<?= $buy_all1 ?>" />
	<input type="hidden" id="cancel" name="cancel" value="" />

<? if ($buy) { ?>
<div class="bskbTZ">
	<p><?= Lang::string('buy-amount') ?></p>
	<p><b><?= Stringz::currency($buy_amount1,true) ?></b></p>
	<input type="hidden" name="buy_amount" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
</div>
<div class="bskbTZ">
	<p><?= Lang::string('buy-with-currency') ?></p>
	<p><b><?= $currency_info['currency'] ?></b></p>
	<input type="hidden" name="buy_currency" value="<?= $currency1 ?>" />
</div>
<? if ($buy_limit || $buy_market_price1) { ?>
<div class="bskbTZ">
	<p><?= ($buy_market_price1) ? Lang::string('buy-price') : Lang::string('buy-limit-price') ?></p>
	<p><b><?= Stringz::currency($buy_price1,($currency_info['is_crypto'] == 'Y')) ?></b></p>
<input type="hidden" name="buy_price" value="<?= Stringz::currencyOutput($buy_price1) ?>" />
</div>
<?php } ?>
<? if ($buy_stop) { ?>
<div class="bskbTZ">
	<p><?= Lang::string('buy-stop-price') ?></p>
	<p><b><?= Stringz::currency($buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></p>
<input type="hidden" name="buy_stop_price" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>" />
</div>
<?php } ?>

<? if ($buy_market_price1) { ?>
<label class="cont"><?= Lang::string('buy-market-price') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1) ? 'checked="checked"' : '' ?> />
	<input type="hidden" name="buy_market_price" value="<?= $buy_market_price1 ?>" />
<?php } ?>

<? if ($buy_limit) { ?>
<label class="cont"><?= Lang::string('buy-limit') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
	<input type="hidden" name="buy_limit" value="<?= $buy_limit ?>" />
<?php } ?>

<? if ($buy_stop) { ?>
<label class="cont" style="padding-left:2em;"><?= Lang::string('buy-stop') ?>   
	<input disabled="disabled" class="checkbox" name="dummy" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
	<input type="hidden" name="buy_stop" value="<?= $buy_stop ?>" />
<?php } ?>

  <span class="checkmark"></span>
</label>
<? if ($buy_stop) { ?>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-subtotal') ?> <span class="pull-right"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($buy_amount1 * $buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></span>
    </p> 
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-fee') ?>
        <span class="pull-right"><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</span>
    </p>
</div>
<div class="current-otr m-b-15">
    <p>
        <span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
        <span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_total"><?= Stringz::currency(round($buy_amount1 * $buy_stop_price1 - ($user_fee_ask * 0.01) * $buy_amount1 * $buy_stop_price1 ,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP),($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
<? } else { ?>
	<div class="current-otr">
    <p>
        <?= Lang::string('buy-subtotal') ?> <span class="pull-right"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span>
    </p>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-fee') ?>
        <span class="pull-right"><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</span>
    </p>
</div>
<div class="current-otr m-b-15">
    <p>
        <span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
        <span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
<? } ?>
<input type="hidden" name="buy" value="1" />
<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />

<div class="btn-otr">
	<span>
		<input type="submit" name="submit" value="<?= Lang::string('confirm-buy') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: left;padding: 12px 30px;" />
	</span>
	<span>
		<!-- <input id="cancel_transaction" type="submit" name="dont" value="<?= Lang::string('confirm-back') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: right;padding: 12px 30px;" /> -->
		<input id="cancel_transaction" type="submit" name="dont" value="Back" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: right;padding: 12px 30px;">
	</span>
</div>
<? } else { ?>

<div class="bskbTZ">
	<p><?= Lang::string('sell-amount') ?></p>
	<p><b><?= Stringz::currency($sell_amount1,true) ?></b></p>
	<input type="hidden" name="sell_amount" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
</div>
<div class="bskbTZ">
	<p><?= Lang::string('buy-with-currency') ?></p>
	<p><b><?= $currency_info['currency'] ?></b></p>
	<input type="hidden" name="sell_currency" value="<?= $currency1 ?>" />
</div>
<? if ($sell_limit || $sell_market_price1) { ?>
<div class="bskbTZ">
	<p><?= ($sell_market_price1) ? Lang::string('buy-price') : Lang::string('buy-limit-price') ?></p>
	<p><b><?= Stringz::currency($sell_price1,($currency_info['is_crypto'] == 'Y')) ?></b></p>
<input type="hidden" name="sell_price" value="<?= Stringz::currencyOutput($sell_price1) ?>" />
</div>
<?php } ?>
<? if ($sell_stop) { ?>
<div class="bskbTZ">
	<p><?= Lang::string('buy-stop-price') ?></p>
	<p><b><?= Stringz::currency($sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></p>
<input type="hidden" name="sell_stop_price" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>" />
</div>
<?php } ?>

<? if ($sell_market_price1) { ?>
<label class="cont"><?= Lang::string('sell-market-price') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1) ? 'checked="checked"' : '' ?> />
	<input type="hidden" name="sell_market_price" value="<?= $sell_market_price1 ?>" />
<?php } ?>

<? if ($sell_limit) { ?>
<label class="cont"><?= Lang::string('buy-limit') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
	<input type="hidden" name="sell_limit" value="<?= $sell_limit ?>" />
<?php } ?>

<? if ($sell_stop) { ?>
<label class="cont"><?= Lang::string('buy-stop') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
	<input type="hidden" name="sell_stop" value="<?= $sell_stop ?>" />
<?php } ?>

  <span class="checkmark"></span>
</label>
<? if ($sell_stop) { ?>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-subtotal') ?> <span class="pull-right"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($sell_amount1 * $sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></span>
    </p>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-fee') ?>
        <span class="pull-right"><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</span>
    </p>
</div>
<div class="current-otr m-b-15">
    <p>
        <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
        <span id="sell_total_label" style="display:none;"><?= Lang::string('sell-total') ?></span>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency(round($sell_amount1 * $sell_stop_price1 - ($user_fee_ask * 0.01) * $sell_amount1 * $sell_stop_price1 ,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP),($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
<? } else { ?>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-subtotal') ?> <span class="pull-right"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span>
    </p>
</div>
<div class="current-otr">
    <p>
        <?= Lang::string('buy-fee') ?>
        <span class="pull-right"><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</span>
    </p>
</div>
<div class="current-otr m-b-15">
    <p>
        <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
        <span id="sell_total_label" style="display:none;"><?= Lang::string('sell-total') ?></span>
        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
    </p>
</div>
<? } ?>
<input type="hidden" name="sell" value="1" />
<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />

<div class="btn-otr">
	<span>
		<input type="submit" name="submit" value="<?= Lang::string('confirm-sale') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: left;padding: 12px 30px;" />

	</span>
	<span>
		<!-- <input id="cancel_transaction" type="submit" name="dont" value="<?= Lang::string('confirm-back') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: right;padding: 12px 30px;" /> -->
		<input type="submit" name="dont" value="Back" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: right;padding: 12px 30px;">

	</span>
</div>

<?php } ?>

</form>
</div>
</div>
</div>
</div>
<!-- Confirm Box Ends Here -->
<?php } ?>
</div>
</div>

<?php include "includes/footer.php"; ?>
<div class="Backdrop__LayoutBackdrop-eRYGPr cdNVJh"></div>
</div>
</div>
</div>
</div>
<div>


</div>
</div>
</div>
<script>
$(document).ready(function(){

$(".Header__DropdownButton-dItiAm").click(function(){
$(".DropdownMenu__Wrapper-ieiZya.kwMMmE").toggleClass("show-menu");
});
});

$("#buy_stop").click(function() {
    var ischecked= $('#buy_limit').is(':checked');
    if(ischecked)
    $('#buy_limit').prop('checked', false);
}); 

$("#buy_limit").click(function() {
    var ischecked= $('#buy_stop').is(':checked');
    if(ischecked)
    $('#buy_stop').prop('checked', false);
}); 

$("#sell_stop").click(function() {
    var ischecked= $('#sell_limit').is(':checked');
    if(ischecked)
    $('#sell_limit').prop('checked', false);
}); 

$("#sell_limit").click(function() {
    var ischecked= $('#sell_stop').is(':checked');
    if(ischecked)
    $('#sell_stop').prop('checked', false);
}); 
</script>
 <script>
	var modal = document.getElementById('myModal');
	var btn = document.getElementById("myBtn");
	var span = document.getElementsByClassName("close")[0];
	btn.onclick = function() {
	modal.style.display = "block";
	}

	span.onclick = function() {
	modal.style.display = "none";
	}

	window.onclick = function(event) {
	if (event.target == modal) {
	modal.style.display = "none";
	}
	}

	</script>
	<script>
		$(document).ready(function(){
			$("#sell").click(function(){
				$(".buy_outer").hide();
				$(".sell_outer").show();
				$(".buy-main").addClass("hMgXGE");
				$(".buy-main").removeClass("keHVTX");
				$(".sell-main").addClass("keHVTX");
				$(".sell-main").removeClass("hMgXGE");
			})
			$("#buy").click(function(){
				$(".sell_outer").hide();
				$(".buy_outer").show();
				$(".sell-main").addClass("hMgXGE");
				$(".sell-main").removeClass("keHVTX");
				$(".buy-main").addClass("keHVTX");
				$(".buy-main").removeClass("hMgXGE");
			})
		});
	</script>
<!-- main js -->
<script type="text/javascript" src="js/ops.js?v=20160210"></script>
</body>

</html>