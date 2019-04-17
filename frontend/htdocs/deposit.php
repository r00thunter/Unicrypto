<?php
    include '../lib/common.php';
    
    if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y') {
    	Link::redirect('userprofile.php');
    } elseif (User::$awaiting_token) {
    	Link::redirect('verify-token.php');
    } elseif (!User::isLoggedIn()) {
    	Link::redirect('login.php');
    }

    // deposit details get by sivabharathy

    $transaction_id = $_REQUEST['transaction_id'];

$currency1 = (!empty($_REQUEST['currency'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['currency']) : false;
//$amount = (!empty($_REQUEST['amount'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['amount']) : false;
$amount = (!empty($_REQUEST['amount'])) ? $_REQUEST['amount'] : false;
$description1 = (!empty($_REQUEST['description'])) ? preg_replace("/[^\pL 0-9a-zA-Z!@#$%&*?\.\-\_ ]/u", "", $_REQUEST['description']) : false;
$bank_name = (!empty($_REQUEST['bank_name'])) ? preg_replace("/[^\pL 0-9a-zA-Z!@#$%&*?\.\-\_ ]/u", "", $_REQUEST['bank_name']) : false;
$pan_no = (!empty($_REQUEST['pan_no'])) ? preg_replace("/[^\pL 0-9a-zA-Z!@#$%&*?\.\-\_ ]/u", "", $_REQUEST['pan_no']) : false;
// $remove_id1 = (!empty($_REQUEST['remove_id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['remove_id']) : false;

$status = 1;

if ($_REQUEST['action']) {

    if ((empty($transaction_id))) {
        Errors::add('Transaction id is required');
    }

    if ((empty($bank_name))) {
        Errors::add('Bank name is required');
    }

    if ((empty($amount))) {
        Errors::add('Amount is required');
    }

    if ($amount == 0) {
        $status = 0;
        Errors::add('Amount should be greater than zero');
    }
    if (!preg_match('/^[0-9]*$/', $_REQUEST['amount'])) {
        Errors::add('Amount is only numbers');
    }

}
// if ($account1 > 0) {
//     if (empty($_REQUEST['uniq'])) {
//         Errors::add('Page expired.');
//     }

// }
if($_GET['deposit']=='add'){
  Messages::add("You've successfully submitted the deposit details. 
The amount will be available in your fiat wallet once it is verified");
}

if($_GET['deposit']=='add_failed'){
  Messages::add('Unable to add deposit');
}


$page1 = (!empty($_REQUEST['page'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['page']) : false;
$currencies = Settings::sessionCurrency();
API::add('Requests', 'get', array(1, false, false, false, 27));
API::add('Requests', 'get', array(false, $page1, 15, false, 27));
API::add('BankAccounts', 'get');
API::add('Content', 'getRecord', array('deposit-bank-instructions'));
API::add('Content', 'getRecord', array('deposit-no-bank'));
API::add('User', 'getAvailable');

$query = API::send();
$user_available = $query['User']['getAvailable']['results'][0];

$bank_accounts = $query['BankAccounts']['get']['results'][0];
// echo "<pre>"; print_r($bank_accounts); exit;
$total = $query['Requests']['get']['results'][0];
$requests = $query['Requests']['get']['results'][1];
$bank_instructions = $query['Content']['getRecord']['results'][0];
$pagination = $pagination = Content::pagination('deposit.php', $page1, $total, 15, 5, false);
$page_title = Lang::string('deposit');
if (!empty($transaction_id) && $status == 1) {
    $_REQUEST['action'] = false;
    $amount = (float) $amount;
    API::add('Requests', 'insertDeposit', array($currency1, $amount, $bank_name, $transaction_id));
    API::add('Requests', 'get', array(1, false, false, false, 27));
    API::add('Requests', 'get', array(false, $page1, 15, false, 27));
    $query = API::send();
    Messages::add('Successfully added deposit tranasaction');
    $requests = $query['Deposit']['get']['results'][0];
    $total = $query['Requests']['get']['results'][0];
    $requests = $query['Requests']['get']['results'][1];
    $_REQUEST['transaction_id'] = false;
    $_REQUEST['amount'] = false;
    Link::redirect('deposit.php?deposit=add');
}
if (empty($_REQUEST['bypass'])) {
}

    // end of deposit details get by sivabharathy
       
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
    API::add('Transactions','get24hData',array($c_currency1, $currency1));
    API::add('Transactions','get24hData',array(28,42)); //btc-ltc
    API::add('Transactions','get24hData',array(45,42)); //eth-ltc
    API::add('Transactions','get24hData',array(43,42)); //zec-ltc
    API::add('Transactions','get24hData',array(44,42)); //bch-ltc
    
    API::add('Transactions','get24hData',array(28,44)); //btc-bch
    API::add('Transactions','get24hData',array(45,44)); //btc-eth
    API::add('Transactions','get24hData',array(43,44)); //btc-zec
    API::add('Transactions','get24hData',array(42,44)); //btc-ltc
    
    API::add('Transactions','get24hData',array(28,43)); //btc-zec
    API::add('Transactions','get24hData',array(42,43)); //ltc-zec
    API::add('Transactions','get24hData',array(45,43)); //eth-zec
       API::add('Transactions','get24hData',array(44,43)); //bch-zec
       
       API::add('Transactions','get24hData',array(28,45)); //btc-eth
    API::add('Transactions','get24hData',array(42,45)); //ltc-eth
    API::add('Transactions','get24hData',array(44,45)); //bch-eth
       API::add('Transactions','get24hData',array(43,45)); //zec-eth
       
    
       //my transactions
       API::add('Transactions', 'get', array(false, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
       API::add('Transactions', 'getTypes');
    
    
    if ($currency_info['is_crypto'] != 'Y') {
    	API::add('BankAccounts','get',array($currency_info['id']));
    }
    	
               
    $query = API::send();
    
    $currentPair = $query['Transactions']['get24hData']['results'][13];
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
       $my_transactions = $query['Transactions']['get']['results'][1];
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
    
    $transactions_24hrs_btc_ltc = $query['Transactions']['get24hData']['results'][14] ;
    $transactions_24hrs_eth_ltc = $query['Transactions']['get24hData']['results'][15] ;
    $transactions_24hrs_zec_ltc = $query['Transactions']['get24hData']['results'][16] ;
    $transactions_24hrs_bch_ltc = $query['Transactions']['get24hData']['results'][17] ;
    
    $transactions_24hrs_btc_bch = $query['Transactions']['get24hData']['results'][18] ;
    $transactions_24hrs_eth_bch = $query['Transactions']['get24hData']['results'][19] ;
    $transactions_24hrs_zec_bch = $query['Transactions']['get24hData']['results'][20] ;
    $transactions_24hrs_ltc_bch = $query['Transactions']['get24hData']['results'][21] ;
    
    $transactions_24hrs_btc_zec = $query['Transactions']['get24hData']['results'][22] ;
    $transactions_24hrs_ltc_zec = $query['Transactions']['get24hData']['results'][23] ;
    $transactions_24hrs_eth_zec = $query['Transactions']['get24hData']['results'][24] ;
       $transactions_24hrs_bch_zec = $query['Transactions']['get24hData']['results'][25] ;
       
       $transactions_24hrs_btc_eth = $query['Transactions']['get24hData']['results'][26] ;
    $transactions_24hrs_ltc_eth = $query['Transactions']['get24hData']['results'][27] ;
    $transactions_24hrs_bch_eth = $query['Transactions']['get24hData']['results'][28] ;
    $transactions_24hrs_zec_eth = $query['Transactions']['get24hData']['results'][29] ;
    
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
        // echo "string<pre>"; print_r($operations); exit;
    	if (!empty($operations['error'])) {
    	Errors::add($operations['error']['message']);
    	}
    	else if ($operations['new_order'] > 0) {
    	$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
    	if (count($_SESSION["buysell_uniq"]) > 3) {
    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
    	}
    	
    	Link::redirect('openorders.php',array('transactions'=>$operations['transactions'],'new_order'=>1));
    	exit;
    	}
    	else {
    	$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
    	if (count($_SESSION["buysell_uniq"]) > 3) {
    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
    	}
    	
    	Link::redirect('openorders.php',array('transactions'=>$operations['transactions']));
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
    	
    	Link::redirect('openorders.php',array('transactions'=>$operations['transactions'],'new_order'=>1));
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
    $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
    if (count($_SESSION["buysell_uniq"]) > 3) {
    	unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
    }
               	
    ?>
<!DOCTYPE html>
<html lang="en">
    <style>
        .input-caption {
        position: relative;
        float: right;
        top: -28px;
        right: 6px;
        height: 28px;
        padding-top: 5px;
        }
        .custom-select
        {
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 2px;
        height: 28px !important;
        }
        label.cont
        {
            width:100%;
        }
        .pull-right 
        {
            float:right;
        }

        .current-otr p 
        {
            margin: 5px 0;
        }
        .left-side-widget .nav-link:hover,
        .left-side-widget .nav-link:focus,
        .left-side-widget .nav-link:visited,
        .left-side-widget .nav-link:active{
            color:#000 !important;
        }
    </style>
   <!DOCTYPE html>
<html lang="en">
    <?php include "includes/sonance_header.php";  ?>
    <body id="wrapper">
   <?php include "includes/sonance_navbar.php"; ?>
   <?php
    $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='deposit'");
    while($pagerow=mysqli_fetch_array($page_sql))
    {
        $page_id=$pagerow['id'];
    }
    $pg_cont_sql=mysqli_query($conn_l, "select page_content_key, ".$_SESSION[LANG]."_page_content from trans_page_value where page_content_status=1 and page_id='".$page_id."'");
    while($pagecontrow=mysqli_fetch_array($pg_cont_sql))
    {
        $pgcont[$pagecontrow['page_content_key']]=$pagecontrow[$_SESSION[LANG].'_page_content'];
    }       
    ?>
   <header>
            <div class="banner row">
                <div class="container content">
                    <h1><?php echo isset($pgcont['deposit_heading_key']) ? $pgcont['deposit_heading_key'] : 'Fiat Wallet'; ?></h1>
                    <p class="text-white text-center"><?php echo isset($pgcont['deposit_sub_heading_key']) ? $pgcont['deposit_sub_heading_key'] : 'Make a Deposit, view your wallet account balance and transactions.'; ?></p>
                    <!-- <p class="text-white text-center">Please note all deposits and withdraws are converted from local currency to USA Dollars</p> -->
                    <div class="text-center">
                      <a href="manageaccounts.php" class="btn" style="background: #007bff !important;"><?php echo isset($pgcont['deposit_heading_button_key']) ? $pgcont['deposit_heading_button_key'] : 'Manage Bank Account'; ?></a> 
                    </div>
                </div>
            </div>
        </header>
    <div class="page-container">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">

                    <?Errors::display();?>

                    <? Messages::display(); ?>

                    <div class="pro card">
                        <div class="card-header">
                            <h6>
                                <strong><?php echo isset($pgcont['deposit_fiat_currency_trans_key']) ? $pgcont['deposit_fiat_currency_trans_key'] : 'Your Fiat Currency Transactions'; ?></strong>
                                <span class="float-right">
                                    <a href="#fiatcurrency" data-toggle="modal">
                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" >
                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"/>
                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"/>
                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                        C26.355,23.457,25,26.261,25,29.158V32"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                </a>
                                </span>
                            </h6>
                        </div>

                        

                        <div class="card-body">

                            <div class="deposite-otr">
                                
                                <form id="add_deposit" action="deposit.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="currency" value="27">
                                    <div class="form-group">
                                         <label><?php echo isset($pgcont['deposit_transaction_id_key']) ? $pgcont['deposit_transaction_id_key'] : 'Transaction Id'; ?></label>
                                       <input type="text" class="form-control" name="transaction_id" value="<?php echo $_REQUEST['transaction_id']; ?>" >
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo isset($pgcont['deposit_bank_name_key']) ? $pgcont['deposit_bank_name_key'] : 'Bank Name'; ?></label>
                                        <select id="bank_name" name="bank_name" class="form-control">
                            <?
                            $i = 1;
                            if ($bank_accounts) {
                                foreach ($bank_accounts as $account) {
                                    echo '<option ' . (($i == 1) ? 'selected="selected"' : '') . ' value="' . $account['account_number'] . '">' . $account['account_number'] . ' - (' . $account['currency'] . ')</option>';
                                    ++$i;
                                }
                            }
                            ?>
                                </select>
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo isset($pgcont['deposit_amount_key']) ? $pgcont['deposit_amount_key'] : 'Amount'; ?></label>
                                        <input type="text" class="form-control" name="amount"  value="<?php echo $_REQUEST['amount']; ?>" >
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn"><?php echo isset($pgcont['deposit_add_key']) ? $pgcont['deposit_add_key'] : 'Add'; ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">

                    <div class="pro card"> 
                        <div class="card-header">
                            <h6><strong><?php echo isset($pgcont['deposit_fiat_wallet_key']) ? $pgcont['deposit_fiat_wallet_key'] : 'Fiat Wallet'; ?></strong>
                                
                            </h6>
                        </div>
                        
                        <div class="card-body">
                            <div class="media">
                          <img class="mr-3" src="images/dollar.png" alt="" width="40" height="40">
                          <div class="media-body">
                            <p class="mb-0"><b>USD <?php echo isset($pgcont['deposit_wallet_key']) ? $pgcont['deposit_wallet_key'] : 'Wallet'; ?></b></p>
                            <span>$ <?= Stringz::currency($user_available['USD']) ?></span>
                             <div class="">
                                <a href="manageaccounts.php"  class="btn text-black"><?php echo isset($pgcont['deposit_button_manage_account_key']) ? $pgcont['deposit_button_manage_account_key'] : 'Manage Accounts'; ?></a>
                                <a href="withdraw.php" class="btn" style="background: #007bff !important;"><?php echo isset($pgcont['deposit_button_withdraw_key']) ? $pgcont['deposit_button_withdraw_key'] : 'Withdraw'; ?></a> 
                            </div>
                          </div>
                        </div>
                        </div>
                    </div>

                    <!-- exchagne admin bank details -->
                    <div class="pro card" style="display: none;"> 
                        <div class="card-header">
                            <h6><strong>How to deposit fiat currency ? <a href="#" data-toggle="collapse" data-target="#deposite-process">Click Here</a></strong>
                                
                            </h6>
                        </div>
                        
                        <div class="card-body collapse" id="deposite-process">
                    
                          
                            <?echo $bank_instructions['content']; ?>
                         
                        </div>
                        
                    </div>
                    <!-- exchagne admin bank details -->


                    <div class="pro card">
                        <div class="card-header">
                            <h6><strong><?php echo isset($pgcont['deposit_table_history_head_key']) ? $pgcont['deposit_table_history_head_key'] : 'History'; ?></strong>
                                 <span class="float-right">
                                    <a href="#dephistory" data-toggle="modal">
                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" >
                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"/>
                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"/>
                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                        C26.355,23.457,25,26.261,25,29.158V32"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                </a>
                                </span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="">
                                <table class="table table-border">
                                    <thead>
                                        <tr>
                                             <th scope="col"><?php echo isset($pgcont['deposit_table_history_table_th_date_key']) ? $pgcont['deposit_table_history_table_th_date_key'] : 'Date'; ?></th>
                                            <th scope="col"><?php echo isset($pgcont['deposit_table_history_table_th_type_key']) ? $pgcont['deposit_table_history_table_th_type_key'] : 'Type'; ?></th>
                                            <th scope="col"><?php echo isset($pgcont['deposit_table_history_table_th_coin_key']) ? $pgcont['deposit_table_history_table_th_coin_key'] : 'Coin'; ?></th>
                                            <th scope="col"><?php echo isset($pgcont['deposit_amount_key']) ? $pgcont['deposit_amount_key'] : 'Amount'; ?></th>
                                            <th scope="col"><?php echo isset($pgcont['deposit_table_history_table_th_status_key']) ? $pgcont['deposit_table_history_table_th_status_key'] : 'Status'; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 

                                        if ($requests) {
    foreach ($requests as $request) {
        ?>

        <?php $d = date_create($request['date']);?>
                                        <tr>
                                            <td scope="row">
                                                <?php echo $request['date'];//date_format($d, "d"); ?>
                                            </td>
                                            <td>Deposit</td>
                                            <td>USD</td>
                                            <td>
                                                <?php echo (($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency($request['amount'], true) . ' ' . $request['fa_symbol'] : $request['fa_symbol'] . Stringz::currency($request['amount'])) ?>
                                            </td>
                                            <td><?=$request['status']; ?></td>
                                        </tr>

                                        <?
    }
} else {
    echo '<div style="padding:20% 35%;">' . Lang::string('deposit-no') . '</div>';
}
?>
                                        
                                    </tbody>
                                </table><?php
                                echo $pagination; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal-1-->
<div class="modal fade" id="fiatcurrency" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['deposit_fiat_currency_trans_modal_head_key']) ? $pgcont['deposit_fiat_currency_trans_modal_head_key'] : 'Deposits'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['deposit_fiat_currency_trans_modal_here_key']) ? $pgcont['deposit_fiat_currency_trans_modal_here_key'] : 'Here you can:'; ?></p>
        <ul>
            <li><?php echo isset($pgcont['deposit_fiat_currency_trans_modal_li1_key']) ? $pgcont['deposit_fiat_currency_trans_modal_li1_key'] : 'Add the transaction details after you have made the fiat currency deposit.'; ?></li>
            <li><?php echo isset($pgcont['deposit_fiat_currency_trans_modal_li2_key']) ? $pgcont['deposit_fiat_currency_trans_modal_li2_key'] : 'View the transaction details of the Fiat currency Deposit transactions.'; ?></li>
            <li><?php echo isset($pgcont['deposit_fiat_currency_trans_modal_li3_key']) ? $pgcont['deposit_fiat_currency_trans_modal_li3_key'] : 'Place a Withdraw request.'; ?></li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="dephistory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['deposit_fiat_currency_trans_modal_head_key']) ? $pgcont['deposit_fiat_currency_trans_modal_head_key'] : 'Deposits'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['deposit_table_history_modal_content_key']) ? $pgcont['deposit_table_history_modal_content_key'] : 'Here you can view the transaction details of the deposits you have made.'; ?></p>
        
      </div>
    </div>
  </div>
</div>
      <?php include "includes/sonance_footer.php"; ?>
    <script type="text/javascript">
    $(document).ready(function() {
        //$('.selectpicker').select2();

        /**
            if no bank accounts added remove depoisite add button
         */
        if(!$('select#bank_name option').length) {
            $("#add_deposit").find("button[type='submit']").attr('disabled', 'disabled').html('Add bank account first')
        }


    });
    </script>
</body>

</html>