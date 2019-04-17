<?php
    include '../lib/common.php';
    $conn = new mysqli("localhost","root","Referral!@##@!","bitexchange_ref");
//     error_reporting(E_ERROR | E_WARNING | E_PARSE);
// ini_set('display_errors', 1);
    if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y') {
        Link::redirect('userprofile.php');
    } elseif (User::$awaiting_token) {
        Link::redirect('verify-token.php');
    } elseif (!User::isLoggedIn()) {
        Link::redirect('login.php');
    }
       
       //////////////////////////////////////////////////////


    $order_id1 = preg_replace("/[^0-9]/", "",$_REQUEST['order_id']);
$bypass = (!empty($_REQUEST['bypass']));
$buy = (!empty($_REQUEST['buy']));
$sell = (!empty($_REQUEST['sell']));
$buy = (!empty($_REQUEST['buy']));
$sell = (!empty($_REQUEST['sell']));
$buy_all1 = (!empty($_REQUEST['buy_all']));

if ($buy || $sell) {
    if (empty($_SESSION["editorder_uniq"]) || empty($_REQUEST['uniq']) || !in_array($_REQUEST['uniq'],$_SESSION["editorder_uniq"]))
        Errors::add('Page expired.');
}

API::add('Orders','getRecord',array($order_id1));
$query = API::send();
$order_info = $query['Orders']['getRecord']['results'][0];
$currency_info = $CFG->currencies[$order_info['currency']];
$c_currency_info = $CFG->currencies[$order_info['c_currency']];
$currency1 = $currency_info['id'];
$c_currency1 = $c_currency_info['id'];

if (empty($order_info['id']) || $order_info['site_user'] != $order_info['user_id']) {
    Link::redirect('openorders.php?message=order-doesnt-exist');
    exit;
}

foreach ($CFG->currencies as $key => $currency) {
    if (is_numeric($key) || $currency['is_crypto'] != 'Y')
        continue;

    API::add('Stats','getCurrent',array($currency['id'],$currency1));
}

API::add('Orders','getBidAsk',array($c_currency1,$currency1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,false,false,1));
API::add('FeeSchedule','getRecord',array(User::$info['fee_schedule']));
API::add('User','getAvailable');
API::add('Transactions','get',array(false,false,1,$c_currency1,$currency1));
$query = API::send();

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

$user_fee_both = $query['FeeSchedule']['getRecord']['results'][0];
$user_available = $query['User']['getAvailable']['results'][0];
$current_bid = $query['Orders']['getBidAsk']['results'][0]['bid'];
$current_ask = $query['Orders']['getBidAsk']['results'][0]['ask'];
$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];

$bank_accounts = $query['BankAccounts']['get']['results'][0];
$buy_market_price1 = 0;
$sell_market_price1 = 0;
$buy_limit = 1;
$sell_limit = 1;
$pre_btc_available = false;
$pre_fiat_available = false;
$buy_amount1 = false;
$sell_amount1 = false;
$buy_price1 = false;
$sell_price1 = false;
$buy_stop_price1 = false;
$sell_stop_price1 = false;
$buy_stop = false;
$sell_stop = false;
$buy_subtotal1 = false;
$sell_subtotal1 = false;
$buy_total1 = false;
$sell_total1 = false;
$user_fee_bid = false;
$user_fee_ask = false;

if ($order_info['is_bid']) {
    $buy_amount1 = (!empty($_REQUEST['buy_amount'])) ? Stringz::currencyInput($_REQUEST['buy_amount']) : $order_info['btc'];
    $buy_market_price1 = (!empty($_REQUEST['buy_market_price'])) ? $_REQUEST['buy_market_price'] : ($order_info['market_price'] == 'Y');
    $buy_price1 = (!empty($_REQUEST['buy_price'])) ? Stringz::currencyInput($_REQUEST['buy_price']) : (($order_info['btc_price'] > 0) ? $order_info['btc_price'] : ($buy_market_price1 ? $current_ask : 0));
    $buy_stop_price1 = (!empty($_REQUEST['buy_stop_price'])) ? Stringz::currencyInput($_REQUEST['buy_stop_price']) : $order_info['stop_price'];
    $user_fee_bid = ($buy_price1 >= $asks[0]['btc_price'] || $buy_market_price1) ? $user_fee_both['fee'] : $user_fee_both['fee1'];
    $buy_subtotal1 = $buy_amount1 * (($buy_price1 > 0) ? $buy_price1 : $buy_stop_price1);
    $buy_fee_amount1 = ($user_fee_bid * 0.01) * $buy_subtotal1;
    $buy_total1 = round($buy_subtotal1 + $buy_fee_amount1,2,PHP_ROUND_HALF_UP);
    $pre_fiat_available = (!empty($user_available[$currency1])) ? $user_available[$currency1] : false;
    $user_available[$currency1] += ($order_info['btc'] * $order_info['btc_price']) + (($user_fee_bid * 0.01) * ($order_info['btc'] * $order_info['btc_price']));
    $buy_stop = ($buy_stop_price1 > 0);
    $buy_limit = ($buy_price1 > 0 && !$buy_market_price1) ? 1 : !empty($_REQUEST['buy_limit']);
    $old_fiat = ($order_info['btc'] * $order_info['btc_price']) + (($order_info['btc'] * $order_info['btc_price']) * ($user_fee_bid * 0.01));
}
else {
    $sell_amount1 = (!empty($_REQUEST['sell_amount'])) ? Stringz::currencyInput($_REQUEST['sell_amount']) : $order_info['btc'];
    $sell_market_price1 = (!empty($_REQUEST['sell_market_price'])) ? $_REQUEST['sell_market_price'] : ($order_info['market_price'] == 'Y');
    $sell_price1 = (!empty($_REQUEST['sell_price'])) ? Stringz::currencyInput($_REQUEST['sell_price']) : (($order_info['btc_price'] > 0) ? $order_info['btc_price'] : ($buy_market_price1 ? $current_bid : 0));
    $sell_stop_price1 = (!empty($_REQUEST['sell_stop_price'])) ? Stringz::currencyInput($_REQUEST['sell_stop_price']) : $order_info['stop_price'];
    $user_fee_ask = (($sell_price1 <= $bids[0]['btc_price']) || $sell_market_price1) ? $user_fee_both['fee'] : $user_fee_both['fee1'];
    $sell_subtotal1 = $sell_amount1 * (($sell_price1 > 0) ? $sell_price1 : $sell_stop_price1);
    $sell_fee_amount1 = ($user_fee_ask * 0.01) * $sell_subtotal1;
    $sell_total1 = round($sell_subtotal1 - $sell_fee_amount1,2,PHP_ROUND_HALF_UP);
    $pre_btc_available = $user_available[$c_currency_info['currency']];
    $user_available[$c_currency1] += $order_info['btc'];
    $sell_stop = ($sell_stop_price1 > 0);
    $sell_limit = ($sell_price1 > 0 && !$sell_market_price1) ? 1 : !empty($_REQUEST['sell_limit']);
    $old_btc = $order_info['btc'];
}

if ($CFG->trading_status == 'suspended')
    Errors::add(Lang::string('buy-trading-disabled'));

if ($buy && !is_array(Errors::$errors)) {
    $buy_market_price1 = (!empty($_REQUEST['buy_market_price']));
    $buy_stop = (!empty($_REQUEST['buy_stop']));
    $buy_stop_price1 = ($buy_stop) ? $buy_stop_price1 : false;
    $buy_limit = (!empty($_REQUEST['buy_limit']));
    $buy_limit = (!$buy_stop && !$buy_market_price1) ? 1 : $buy_limit;
    $buy_price1 = ($buy_market_price1) ? $current_ask : $buy_price1;

    API::add('Orders','executeOrder',array(1,(($buy_stop && !$buy_limit) ? $buy_stop_price1 : $buy_price1),$buy_amount1,$c_currency1,$currency1,$user_fee_bid,$buy_market_price1,$order_info['id'],false,false,$buy_stop_price1,false,false,$buy_all1));
    $query = API::send();
    $operations = $query['Orders']['executeOrder']['results'][0];
    
    if (!empty($operations['error'])) {
        Errors::add($operations['error']['message']);
    }
    else if ($operations['edit_order'] > 0) {
        $uniq_time = time();
        $_SESSION["editorder_uniq"][$uniq_time] = md5(uniqid(mt_rand(),true));
        if (count($_SESSION["editorder_uniq"]) > 3) {
            unset($_SESSION["editorder_uniq"][min(array_keys($_SESSION["editorder_uniq"]))]);
        }
        
        Link::redirect('openorders.php',array('transactions'=>$operations['transactions'],'edit_order'=>1));
        exit;
    }
    else {
        $uniq_time = time();
        $_SESSION["editorder_uniq"][$uniq_time] = md5(uniqid(mt_rand(),true));
        if (count($_SESSION["editorder_uniq"]) > 3) {
            unset($_SESSION["editorder_uniq"][min(array_keys($_SESSION["editorder_uniq"]))]);
        }
        
        Link::redirect('transactions.php',array('transactions'=>$operations['transactions']));
        exit;
    }
}

if ($sell && !is_array(Errors::$errors)) {
    $sell_market_price1 = (!empty($_REQUEST['sell_market_price']));
    $sell_stop = (!empty($_REQUEST['sell_stop']));
    $sell_stop_price1 = ($sell_stop) ? $sell_stop_price1 : false;
    $sell_limit = (!empty($_REQUEST['sell_limit']));
    $sell_limit = (!$sell_stop && !$sell_market_price1) ? 1 : $sell_limit;
    $sell_price1 = ($sell_market_price1) ? $current_bid : $sell_price1;
    
    API::add('Orders','executeOrder',array(0,(($sell_stop && !$sell_limit) ? $sell_stop_price1 : $sell_price1),$sell_amount1,$c_currency1,$currency1,$user_fee_ask,$sell_market_price1,$order_info['id'],false,false,$sell_stop_price1));
    $query = API::send();
    $operations = $query['Orders']['executeOrder']['results'][0];
    
    if (!empty($operations['error'])) {
        Errors::add($operations['error']['message']);
    }
    else if ($operations['edit_order'] > 0) {
        $uniq_time = time();
        $_SESSION["editorder_uniq"][$uniq_time] = md5(uniqid(mt_rand(),true));
        if (count($_SESSION["editorder_uniq"]) > 3) {
            unset($_SESSION["editorder_uniq"][min(array_keys($_SESSION["editorder_uniq"]))]);
        }
        
        Link::redirect('openorders.php',array('transactions'=>$operations['transactions'],'edit_order'=>1));
        exit;
    }
    else {
        $uniq_time = time();
        $_SESSION["editorder_uniq"][$uniq_time] = md5(uniqid(mt_rand(),true));
        if (count($_SESSION["editorder_uniq"]) > 3) {
            unset($_SESSION["editorder_uniq"][min(array_keys($_SESSION["editorder_uniq"]))]);
        }
        
        Link::redirect('transactions.php',array('transactions'=>$operations['transactions']));
        exit;
    }
}

$user_available[$currency1] = $pre_fiat_available;
$user_available[$c_currency_info['currency']] = $pre_btc_available;

$page_title = Lang::string('edit-order');
if (!$bypass) {
    $uniq_time = time();
    $_SESSION["editorder_uniq"][$uniq_time] = md5(uniqid(mt_rand(),true));
    if (count($_SESSION["editorder_uniq"]) > 3) {
        unset($_SESSION["editorder_uniq"][min(array_keys($_SESSION["editorder_uniq"]))]);
    }

    

    //////////////////////////////////////////////////////////

    if ($_REQUEST['order_id']) {
        $get_order_id = $_REQUEST['order_id'];
        $SQL = "SELECT * FROM orders A, currencies B WHERE A.currency = B.id AND A.id = $get_order_id";
        $QUERY = mysqli_query($conn,$SQL);
        $order_data = mysqli_fetch_assoc($QUERY);
        $fee = ($order_data['fiat'] * 0.25 ) / 100;
        $f_amount = $order_data['fiat'] + $fee;
        
        //var_dump($order_data);exit;
    }
    //
    $currency_id = $_REQUEST['currency'];
    if (!$currency_id) {
       $currency_id = 28;
       $_REQUEST['currency'] = 28;
    }

    $conn = new mysqli("localhost","root","Referral!@##@!","bitexchange_ref");

    $cur_sql = "SELECT * FROM currencies";
    $currency_query = mysqli_query($conn,$cur_sql);
    //$currencies = mysqli_fetch_assoc($currency_query);

    // CHECKING REFErral status 
    $ch = curl_init("http://167.99.204.119/api/get-settings.php"); 
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);      
    curl_close($ch);
    $ref_response = json_decode($output);
    if ($ref_response->is_referral == 1) {
        $GLOBALS['REFERRAL'] = true;
        $GLOBALS['REFERRAL_BASE_URL'] = "http://167.99.204.119/api/";
        //$GLOBALS['REFERRAL_BASE_URL'] = $ref_response->base_url;
    }else{
       $GLOBALS['REFERRAL'] = false; 
    }

    // end of checking referral status

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

    $from_currency = $c_currency_info['currency'];
    $to_currency = $currency_info['currency'];

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

    // referral bonus starts
    if ($_REQUEST['is_referral']) {
        //echo 'yes is_referral true';//bonus_amount
        if ($_REQUEST['bonus_amount']) {
            $buy_fee_amount1 = $buy_fee_amount1 - $_REQUEST['bonus_amount'];
        }
    }
    // end of referral bonus

    $buy_total1 = round($buy_subtotal1 + $buy_fee_amount1,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP);
    $buy_stop = false;
    $buy_stop_price1 = false;
    $buy_all1 = (!empty($_REQUEST['buy_all']));
    
    $sell_amount1 = (!empty($_REQUEST['sell_amount'])) ? Stringz::currencyInput($_REQUEST['sell_amount']) : 0;
    $sell_price1 = (!empty($_REQUEST['sell_price'])) ? Stringz::currencyInput($_REQUEST['sell_price']) : $current_bid;
    $sell_subtotal1 = $sell_amount1 * $sell_price1;
    $sell_fee_amount1 = ($user_fee_ask * 0.01) * $sell_subtotal1;

    //
    // referral bonus starts
    if ($_REQUEST['is_referral']) {
        //echo 'yes is_referral true';//bonus_amount
        if ($_REQUEST['bonus_amount']) {
            $sell_fee_amount1 = $sell_fee_amount1 - $_REQUEST['bonus_amount'];
        }
    }
    //
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
        
        // updating referral bonus
        $name = User::$info['first_name'].' '.User::$info['last_name'];
            $url = $REFERRAL_BASE_URL."use-bonus.php?name=1";
            $fields = array(
                'user_id' => urlencode($name),
                'trans_id' => urlencode($name),
                'points' => urlencode($bonous_point),
                'name' => urlencode($name)
            );
            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            $response = json_decode($result);
            curl_close($ch);

        Link::redirect('orderhistory.php',array('transactions'=>$operations['transactions'],'new_order'=>1));

        exit;
        }
        else {
        $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
        if (count($_SESSION["buysell_uniq"]) > 3) {
        unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
        }
        
        // updating referral bonus
        $name = User::$info['first_name'].' '.User::$info['last_name'];
            $url = $REFERRAL_BASE_URL."use-bonus.php?name=1";
            $fields = array(
                'user_id' => urlencode($name),
                'trans_id' => urlencode($name),
                'points' => urlencode($bonous_point),
                'name' => urlencode($name)
            );
            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            $response = json_decode($result);
            curl_close($ch);

        Link::redirect('orderhistory.php',array('transactions'=>$operations['transactions']));

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
        

        Link::redirect('orderhistory.php',array('transactions'=>$operations['transactions'],'new_order'=>1));

        exit;
        }
        else {
        $_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
        if (count($_SESSION["buysell_uniq"]) > 3) {
        unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
        }

        // updating referral bonus
        $name = User::$info['first_name'].' '.User::$info['last_name'];
            $url = $REFERRAL_BASE_URL."use-bonus.php?name=1";
            $fields = array(
                'user_id' => urlencode($name),
                'trans_id' => urlencode($name),
                'points' => urlencode($bonous_point),
                'name' => urlencode($name)
            );
            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            $response = json_decode($result);
            curl_close($ch);

        Link::redirect('orderhistory.php',array('transactions'=>$operations['transactions'])); //newly added 
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

    // start of referral 
        if ($REFERRAL == true) {
            
            $name = User::$info['first_name'].' '.User::$info['last_name'];
            
            $url = $REFERRAL_BASE_URL."get-user-bonus.php?name=1";

            $fields = array(
                'user_id' => urlencode($name),
                'name' => urlencode($name)
            );
            //print_r($fields);
            //url-ify the data for the POST
            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
           //open connection
            $ch = curl_init();
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //execute post
            $result = curl_exec($ch);
            $response = json_decode($result);
            //close connection
            curl_close($ch);
            $one_point_value = $response->settings->one_point_value;
         
            $referral_code = $response->data->referral_code; 
            $bonous_point = $response->data->bonous_point;
            
            if ($to_currency == 'USD') {
                $bonus_amount = (float) $bonous_point / (float) $one_point_value;
                $cur_code = '$';
            }else{
                $one_point_values = $response->settings->$to_currency;
                $bonus_amount = (float) $bonous_point / (float) $one_point_values;
                $cur_code = $to_currency;
            }
            

            //
            $his_url = $REFERRAL_BASE_URL."get-usage-history.php";
            $ch = curl_init();
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //execute post
            $result = curl_exec($ch);
            $response = json_decode($result);
            //var_dump($response);
            //close connection
            curl_close($ch);
        }
        
    // end of referral
                
    ?>
    <!DOCTYPE html>
<html lang="en">
    <?php include "includes/sonance_header.php";  ?>

  <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
    




  
    <style>

    #chartdiv {
      width: 100%;
      height: 450px;
    }
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
        .btn.btn-primary.btn-change {
            width: auto;
            padding-left: 30px;
            padding-right: 30px;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        #chart_div {
            margin-left: 0px;
            padding-left: 0px;
            margin-bottom: 20px;
        }
    </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>  

        <div class="page-container">
            <div class="container" style="margin-bottom: 15%">
                   <div class="row">
                    <div class="col-md-12">
                        <div class="left-side-widget">
                        <div class="bg-white">
                        <div class="head-splits name">
                            <h4 class="title"><?= $c_currency_info['currency'] ?><span class="gray-color">/<?= $currency_info['currency'] ?></span></h4>
                            <p><?= $c_currency_info['currency'] ?></p>
                        </div>
                        <div class="head-splits">
                            <p class="small gray-color">Last Price</p>
                            <p class="amount"><strong><span class="green-color"><?= number_format($currentPair['lastPrice'], 8) ?></span></strong></p>
                        </div>
                        <div class="head-splits">
                            <p class="small gray-color">24h change</p>
                            <p class="amount"><strong><span class="red-color"><?= number_format($currentPair['change_24hrs'], 8) ?></span></strong></p>
                        </div>
                       
                        <div class="head-splits">
                            <p class="small gray-color">24h Volume</p>
                            <p class="amount"><strong><span class="gray-color"><?= number_format($currentPair['transactions_24hrs'], 8) ?></span> <?= $c_currency_info['currency'] ?></strong></p>
                        </div>
                        </div>
                        </div>
                        <div class="left-side-widget">
                            <div class="bg-white">
                                <ul>
                                    <li>The Simple Trade page lets you Buy / Sell cryptocurrencies on this exchange.</li>
                                    <li>You can use a fiat currency or any cryptocurrency available on the Currency Pair section to buy / sell cryptocurrencies.</li>
                                    <li>Check the market rate or the open order on the 'Exchange Open Orders' section before you trade</li>
                            
                                    <li>Deposit fiat currency in your Fiat Wallet, if you want to purchase your first or any Cryptocurrency. <b>
                                    <a href="depositnew">Add a Deposit</a></b></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12">
                        <h6 class="right-side-widget-title"><strong class="heading-one">Open Orders on Market</strong>
                                <a href="#openmarket" data-toggle="modal" class="float-right">
                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" xml:space="preserve">
                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"></circle>
                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"></line>
                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                        C26.355,23.457,25,26.261,25,29.158V32"></path><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                </a>
                                </h6>
                        <div class="left-side-widget">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" title="Buy Orders">
                                    <a class="nav-link active" id="buy-tab" data-toggle="tab" href="#buy" role="tab" aria-controls="buy" aria-selected="true"><img src="sonance/img/icons/buy.jpg"><span style="font-size: 10px;margin-left: 5px;font-weight: 600;">Buy Orders</span></a>
                                </li>
                                <li class="nav-item" title="Sell Orders">
                                    <a class="nav-link" id="sell-tab" data-toggle="tab" href="#sell" role="tab" aria-controls="sell" aria-selected="false"><img src="sonance/img/icons/sell.jpg"> <span style="font-size: 10px;margin-left: 5px;font-weight: 600;">Sell Orders</span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="myTab">
                                <div class="tab-pane fade show active" id="buy" role="tabpanel" aria-labelledby="buy-tab">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Price(<?= $currency_info['currency'] ?>)</th>
                                                <th>Amount(<?= $c_currency_info['currency'] ?>)</th>
                                                <th>Total(<?= $currency_info['currency'] ?>)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($bids): ?>
                                            <?php foreach($bids as $bid): ?>
                                            <tr>
                                                <td><span class="green-color"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($bid['btc_price'],($currency_info['is_crypto'] == 'Y')) ?></span></td>
                                                <td><span class="gray-color"><?= Stringz::currency($bid['btc'],true) ?></span></td>
                                                <td><span class="gray-color"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency(($bid['btc_price'] * $bid['btc']),($currency_info['is_crypto'] == 'Y')) ?></span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <tr>
                                                <td colspan="3">No Buy Orders</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="sell" role="tabpanel" aria-labelledby="sell-tab">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Price(<?= $currency_info['currency'] ?>)</th>
                                                <th>Amount(<?= $c_currency_info['currency'] ?>)</th>
                                                <th>Total(<?= $currency_info['currency'] ?>)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($asks): ?>
                                            <?php foreach($asks as $ask): ?>
                                            <tr>
                                                <td><span class="green-color"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($ask['btc_price'],($currency_info['is_crypto'] == 'Y')) ?></span></td>
                                                <td><span class="gray-color"><?= Stringz::currency($ask['btc'],true) ?></span></td>
                                                <td><span class="gray-color"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency(($ask['btc_price'] * $ask['btc']),($currency_info['is_crypto'] == 'Y')) ?></span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <tr>
                                                <td colspan="3">No Sell Orders</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                                     <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12">
                                <h6 class="right-side-widget-title"><strong class="heading-two">Currency Pairs</strong>
                                <a href="#cpair" data-toggle="modal" class="float-right">
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
                                </h6>
                                <div class="right-side-widget">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="r-usd-tab" data-toggle="tab" href="#r-usd" role="tab" aria-controls="r-usd" aria-selected="true">USD</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="r-btc-tab" data-toggle="tab" href="#r-btc" role="tab" aria-controls="r-btc" aria-selected="true">BTC</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="r-ltc-tab" data-toggle="tab" href="#r-ltc" role="tab" aria-controls="r-ltc" aria-selected="false">LTC</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="r-bch-tab" data-toggle="tab" href="#r-bch" role="tab" aria-controls="r-bch" aria-selected="false">BCH</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="r-zec-tab" data-toggle="tab" href="#r-zec" role="tab" aria-controls="r-zec" aria-selected="false">ZEC</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="r-eth-tab" data-toggle="tab" href="#r-eth" role="tab" aria-controls="r-eth" aria-selected="false">ETH</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">

                                        <div class="tab-pane fade show active" id="r-usd" role="tabpanel" aria-labelledby="r-usd-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120">Pair</th>
                                                        <th>Price</th>
                                                        <th>Change</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTC' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTC-USD">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BTC/USD
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_btc_usd['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_btc_usd['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-USD">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                LTC/USD
                                                            </div>
                                                        </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_ltc_usd['lastPrice'] ?></span> </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_ltc_usd['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-USD">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ETH/USD
                                                            </div>
                                                        </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_eth_usd['lastPrice'] ?></span> </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_eth_usd['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BCH' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BCH-USD">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BCH/USD
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_bch_usd['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_bch_usd['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ZEC' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ZEC-USD">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ZEC/USD
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_zec_usd['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_bch_usd['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="r-btc" role="tabpanel" aria-labelledby="r-btc-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120">Pair</th>
                                                        <th>Price</th>
                                                        <th>Change</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'BTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-BTC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                LTC/BTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_ltc_btc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_ltc_btc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'BTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-BTC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ETH/BTC
                                                            </div>
                                                        </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_eth_btc['lastPrice'] ?></span> </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_eth_btc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BCH' && $currency_info['currency'] == 'BTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BCH-BTC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BCH/BTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_bch_btc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_bch_btc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ZEC' && $currency_info['currency'] == 'BTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ZEC-BTC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ZEC/BTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_zec_btc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_bch_btc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="r-ltc" role="tabpanel" aria-labelledby="r-ltc-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120">Pair</th>
                                                        <th>Price</th>
                                                        <th>Change</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTC' && $currency_info['currency'] == 'LTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTC-LTC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BTC/LTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_btc_ltc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_btc_ltc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'LTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-LTC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ETH/LTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_eth_ltc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_eth_ltc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ZEC' && $currency_info['currency'] == 'LTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ZEC-LTC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ZEC/LTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_zec_ltc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_zec_ltc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BCH' && $currency_info['currency'] == 'LTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BCH-LTC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BCH/LTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_bch_ltc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_bch_ltc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="r-bch" role="tabpanel" aria-labelledby="r-bch-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120">Pair</th>
                                                        <th>Price</th>
                                                        <th>Change</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTC' && $currency_info['currency'] == 'BCH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTH-BCH">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BTC/BCH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_btc_bch['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_btc_bch['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'BCH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-BCH">
                                                        <td>
                                                            <div class="star-inner text-left" style="font-size:9px">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ETH/BCH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_eth_bch['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_eth_bch['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ZEC' && $currency_info['currency'] == 'BCH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ZEC-BCH">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ZEC/BCH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_zec_bch['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_zec_bch['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'BCH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-BCH">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                LTC/BCH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_ltc_bch['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_ltc_bch['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="r-zec" role="tabpanel" aria-labelledby="r-zec-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120">Pair</th>
                                                        <th>Price</th>
                                                        <th>Change</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTC' && $currency_info['currency'] == 'ZEC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTC-ZEC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BTC/ZEC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_btc_zec['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_btc_zec['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'ZEC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-ZEC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                LTC/ZEC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_ltc_zec['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_ltc_zec['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETC' && $currency_info['currency'] == 'ZEC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-ZEC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ETH/ZEC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_eth_zec['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_eth_zec['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BCH' && $currency_info['currency'] == 'ZEC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BCH-ZEC">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BCH/ZEC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_bch_zec['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_bch_zec['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="r-eth" role="tabpanel" aria-labelledby="r-eth-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120">Pair</th>
                                                        <th>Price</th>
                                                        <th>Change</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTH' && $currency_info['currency'] == 'ETH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTC-ETH">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BTC/ETH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_btc_eth['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_btc_eth['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'ETH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-ETH">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                LTC/ETH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_ltc_eth['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_ltc_eth['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row" data-href="userbuy?trade=BCH-ETH">
                                                        <td>
                                                            <div class="star-inner text-left" style="font-size:9px">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BCH/ETH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_bch_eth['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_bch_eth['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ZEC' && $currency_info['currency'] == 'ETH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ZEC-ETH">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ZEC/ETH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_zec_eth['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_zec_eth['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>


                    </div>

                  <?php 
                  $base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
                    $url = $base_url . $_SERVER["REQUEST_URI"]; 
                  ?>
                    <div class="col-lg-9 col-md-7 col-sm-12 col-xs-12" style="margin-top: 50px;margin-bottom: 20px;">
                        <form name="chat_filter" method="get" action="<?php echo $url; ?>">
                            <input type="hidden" name="trade" value="<?php echo $_REQUEST['trade']; ?>">
                        <div class="row">
                            <div class="col-md-2 col-sm-6 col-xs-6">
                                <select class="form-group form-control" name="currency">
                                 <?php while($currency = mysqli_fetch_assoc($currency_query)) { ?>
                                        <option 
                                        <?php if($_REQUEST['currency'] == $currency['id']) { ?> 
                                        selected
                                        <?php } ?>
                                        value="<?php echo $currency['id']; ?>">
                                            <?php echo $currency['currency']; ?>
                                        </option>
                                    <?php } ?>
                                    
                                </select>
                            </div>
                            <div class="col-md-8 col-sm-6 col-xs-6">
                                <button style="width: 10%;padding-left: 13px;" class="btn btn-primary btn-change">GO</button>
                            </div>
                        </div>
                        </form>
                        
                        <div id="chart_div" style="width: 96%; height: 30%;"></div>  
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">

                                <input type="hidden" id="is_crypto" value="<?= $currency_info['is_crypto'] ?>" />
                        <input type="hidden" id="user_fee" value="<?= $user_fee_both['fee'] ?>" />
                        <input type="hidden" id="user_fee1" value="<?= $user_fee_both['fee1'] ?>" />
                        <input type="hidden" id="c_currency" value="<?= $c_currency1 ?>">
                        <div class="center-widget">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" style="width: 100%;">
                                    <a class="nav-link active" id="limit-tab" data-toggle="tab" href="#limit" role="tab" aria-controls="limit" aria-selected="true" style="display: inline-block;">Buy-or-Sell</a>
                                    <a href="#buysell" data-toggle="modal" class="float-right">
                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" xml:space="preserve">
                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"></circle>
                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"></line>
                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                        C26.355,23.457,25,26.261,25,29.158V32"></path><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                </a>
                                </li>
                              
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="row">
                                    <div class="col-md-12" style="margin: auto;width: 100%;padding: 0;">
                                        <? Errors::display(); ?>
                                        <style>
                                            .errors, .notice {
                                                   color: #e23535;
                                                    list-style: none;
                                                    background: #ff000036;
                                                    padding: 10px;
                                                    position: relative;
                                                    margin: 0 auto;
                                                    font-size: 1em;
                                                    text-align: center;
                                                    max-width: 90%;
                                                    left: 1px;
                                            }
                                        </style>
                                        <?= ($notice) ? '<div class="notice">'.$notice.'</div>' : '' ?>
                                    </div>
                                </div>
                                <? if(!$ask_confirm) : ?>
                                <div class="tab-pane fade show active" id="limit" role="tabpanel" aria-labelledby="limit-tab">
                                    <div class="row">
                                        <?php if($order_data['order_type'] == 1){ ?>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <form id="buy_form" action="userbuy.php?trade=<?= $market ?>" method="POST">
                                                <h6 class="title"><strong>Buy Cryptocurrency</strong></h6>
                                                <div class="form-group">
                                                    <label for="">Available Balance(<span class="sell_currency_label"><?= $currency_info['currency'] ?></span>)</label>
                                                    <span class="form-control center-widget" style="margin-top: 0px;"><span class="buy_currency_char"><?= $currency_info['fa_symbol'] ?></span>
                                                    <span id="buy_user_available" style="color: #2f8afd;"><?= ((!empty($user_available[strtoupper($currency_info['currency'])])) ? Stringz::currency($user_available[strtoupper($currency_info['currency'])],($currency_info['is_crypto'] == 'Y')) : '0.00') ?></span></span>
                                                </div>
                                                <div class="Flex__Flex-fVJVYW gkSoIH">
                                                    <div class="form-group">
                                                        <label><?= Lang::string('buy-amount') ?></label>
                                                        <input name="buy_amount" id="buy_amount" type="text" class="form-control" 
                                                        value="<?php echo $order_data['btc']; ?>" />
                                                        <div class="input-caption"><?= $c_currency_info['currency'] ?></div>
                                                    </div>
                                                </div>
                                                <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
                                                    <!-- <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('buy-with-currency') ?></h4>
                                                        </div> -->
                                                    <div>
                                                        <div class="Flex__Flex-fVJVYW gkSoIH">
                                                            <div class="form-group">
                                                                <label class="position-relative"><?= Lang::string('buy-with-currency') ?></label>
                                                                <span id="buy_currency" class="pull-right position-absolute" style="right:15px;"><?= $currency_info['currency'] ?></span>
                                                                <!-- <select id="buy_currency" name="currency" class="form-control custom-select">
                                                                <?
                                                                    // if ($CFG->currencies) {
                                                                    //  foreach ($CFG->currencies as $key => $currency) {
                                                                    
                                                                    //  if (is_numeric($key) || $key == $c_currency_info['currency'])
                                                                    //  continue;
                                                                        
                                                                    //  echo '<option '.(($currency['id'] == $currency1) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
                                                                    //  }
                                                                    // }    
                                                                    ?>
                                                                </select> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="cont">
                                                    <input style="vertical-align:middle" class="checkbox" name="buy_market_price" id="buy_market_price" type="checkbox" value="1" 
                                                    <?php if($order_data['market_price'] == 'Y'){ ?>
                                                    checked="checked"
                                                    <?php } ?>
                                                    <?= ($buy_market_price1 && !$buy_stop) ? 'checked="checked"' : '' ?> <?= (!$asks) ? 'readonly="readonly"' : '' ?> />
                                                    <?= Lang::string('buy-market-price') ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <label class="cont">
                                                    <input class="checkbox" name="buy_limit" id="buy_limit" type="checkbox" value="1" 
                                                    <?php if($order_data['market_price'] == 'N' && $order_data['stop_price'] == 0){ ?>
                                                    checked="checked"
                                                    <?php } ?>
                                                    <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
                                                    <?= Lang::string('buy-limit') ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <label class="cont">
                                                    <input class="checkbox" name="buy_stop" id="buy_stop" type="checkbox" value="1" 
                                                    <?php if($order_data['market_price'] == 'N' && $order_data['stop_price'] > 0){ ?>
                                                    checked="checked"
                                                    <?php } ?>
                                                    <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
                                                    <?= Lang::string('buy-stop') ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" id="buy_price_container" <?= (!$buy_limit && !$buy_market_price1) ? 'style="display:none;"' : '' ?>>
                                                    <!-- <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span id="buy_price_limit_label" <?= (!$buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="buy_price_market_label" <?= ($buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></h4>
                                                    </div> -->
                                                    <div>
                                                        <div class="Flex__Flex-fVJVYW gkSoIH">
                                                            <div class="form-group">
                                                                <label><span id="buy_price_limit_label" <?= (!$buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="buy_price_market_label" <?= ($buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></label>
                                                                <input name="buy_price" id="buy_price" type="text" class="form-control" 
                                                                value="<?php echo $order_data['btc_price']; ?>" <?= ($buy_market_price1) ? 'readonly="readonly"' : '' ?> />
                                                                <div class="input-caption"><?= $currency_info['currency'] ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="buy_stop_container" class="param" <?= (!$buy_stop) ? 'style="display:none;"' : '' ?>>
                                                    <div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" >
                                                        <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                            <!-- <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></h4> -->
                                                        </div>
                                                        <div>
                                                            <div class="Flex__Flex-fVJVYW gkSoIH">
                                                                <div class="form-group">
                                                                    <label><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></label>
                                                                    <input name="buy_stop_price" id="buy_stop_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>">
                                                                    <div class="input-caption"><?= $currency_info['currency'] ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="current-otr">
                                                    <p>
                                                        <?= Lang::string('buy-subtotal') ?> 
                                                        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_subtotal">
                                                            
                                                                <?php echo $order_data['fiat']; ?>
                                                            </span></span>
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
                                                        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_total">


                                                            <?php echo $f_amount; ?>
                                                            <?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?>
                                                                
                                                            </span></span>
                                                    </p>
                                                </div>
                                                <input type="hidden" name="buy" value="1" />
                                                <input type="hidden" name="buy_all" id="buy_all" value="<?= $buy_all1 ?>" />
                                                <input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
                                                <input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('buy-bitcoins')) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary"/>
                                            </form>
                                        </div>
                                        <?php } ?>
                                        <?php if($order_data['order_type'] == 2){ ?>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <form id="sell_form" action="userbuy.php?trade=<?= $market ?>" method="POST">
                                                <h6 class="title"><strong>Sell Cryptocurrency</strong></h6>
                                                <div class="form-group">
                                                    <label for="">Available Balance(<?= $c_currency_info['currency'] ?>)</label>
                                                    <span class="form-control center-widget" style="margin-top: 0px;">
                                                        <span id="sell_user_available" style="color: #2f8afd;"  ><?= Stringz::currency($user_available[strtoupper($c_currency_info['currency'])],true) ?></span> <?= $c_currency_info['currency']?></span>
                                                </div>
                                                <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
                                                    <!-- <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('sell-amount') ?></h4>
                                                    </div> -->
                                                    <div>
                                                        <div class="Flex__Flex-fVJVYW gkSoIH">
                                                            <div class="form-group">
                                                                <label><?= Lang::string('sell-amount') ?></label>
                                                                <input name="sell_amount" id="sell_amount" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
                                                                <div class="input-caption"><?= $c_currency_info['currency'] ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
                                                    <!-- <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('buy-with-currency') ?></h4>
                                                    </div> -->
                                                    <div>
                                                        <div class="Flex__Flex-fVJVYW gkSoIH">
                                                            <div class="form-group">
                                                                <label><?= Lang::string('buy-with-currency') ?></label>
                                                                <span id="buy_currency" class="pull-right position-absolute" style="right:15px;"><?= $currency_info['currency'] ?></span>
                                                                <!-- <select id="sell_currency" name="currency" class="form-control custom-select">
                                                                <?
                                                                    // if ($CFG->currencies) {
                                                                    //  foreach ($CFG->currencies as $key => $currency) {
                                                                    //  if (is_numeric($key) || $key == $c_currency_info['currency'])
                                                                    //  continue;
                                                                        
                                                                    //  echo '<option '.(($currency['id'] == $currency1) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
                                                                    //  }
                                                                    // }    
                                                                    ?>
                                                                </select> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="cont">
                                                    <input class="checkbox" name="sell_market_price" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1 && !$sell_stop) ? 'checked="checked"' : '' ?> <?= (!$bids) ? 'readonly="readonly"' : '' ?> />
                                                    <?= Lang::string('sell-market-price') ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <label class="cont">
                                                    <input class="checkbox" name="sell_limit" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
                                                    <?= Lang::string('buy-limit') ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <label class="cont">
                                                    <input class="checkbox" name="sell_stop" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
                                                    <?= Lang::string('buy-stop') ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <div id="sell_price_container" class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" <?= (!$sell_limit && !$sell_market_price1) ? 'style="display:none;"' : '' ?>>
                                                    <!-- <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span id="sell_price_limit_label" <?= (!$sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="sell_price_market_label" <?= ($sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></h4>
                                                    </div> -->
                                                    <div>
                                                        <div class="Flex__Flex-fVJVYW gkSoIH">
                                                            <div class="form-group">
                                                                <label><span id="sell_price_limit_label" <?= (!$sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="sell_price_market_label" <?= ($sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></label>
                                                                <input name="sell_price" id="sell_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_price1) ?>" <?= ($sell_market_price1) ? 'readonly="readonly"' : '' ?> />
                                                                <div class="input-caption"><?= $currency_info['currency'] ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="sell_stop_container" class="param" <?= (!$sell_stop) ? 'style="display:none;"' : '' ?>>
                                                    <div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" >
                                                        <!-- <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></h4>
                                                        </div> -->
                                                        <div>
                                                            <div class="Flex__Flex-fVJVYW gkSoIH">
                                                                <div class="form-group">
                                                                    <label><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></label>
                                                                    <input name="sell_stop_price" id="sell_stop_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>">
                                                                    <div class="input-caption"><?= $currency_info['currency'] ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
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
                                                <input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-bitcoins')) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary"/>
                                                <!-- <button class="Button__Container-hQftQV kZBVvC" disabled="">
                                                    <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                                        <div class="Flex__Flex-fVJVYW ghkoKS">Sell Bitcoin Instantly</div>
                                                    </div>
                                                    </button> -->
                                            </form>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <? else: ?>
                                <div class="TradeFormTabContainer__Container-cUyfJR eMNjQO Panel__Container-hCUKEb gmOPIV conform-screen" style="max-width: 700px;margin: auto;width:100%;">
                                    <div class="Flex__Flex-fVJVYW iDqRrV">
                                        <div class="TradeFormTabContainer__Tab-caAlbq keHVTX Flex__Flex-fVJVYW iDqRrV" style="border-right: none; text-align:center">
                                            <a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf">
                                            <input id="c_currency" type="text" value="28" style="display:none;">
                                            <span class="right" style="font-size: 1.2em;margin-top:1em;"><?= Lang::string('confirm-transaction') ?></span>
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
                                                        <p style="margin-bottom:0px;"><?= Lang::string('buy-amount') ?></p>
                                                        <h4><b><?= Stringz::currency($buy_amount1,true) ?></b></h4>
                                                        <input type="hidden" name="buy_amount" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
                                                    </div>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= Lang::string('buy-with-currency') ?></p>
                                                        <h4><b><?= $currency_info['currency'] ?></b></h4>
                                                        <input type="hidden" name="buy_currency" value="<?= $currency1 ?>" />
                                                    </div>
                                                    <? if ($buy_limit || $buy_market_price1) { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= ($buy_market_price1) ? Lang::string('buy-price') : Lang::string('buy-limit-price') ?></p>
                                                        <h4><b><?= Stringz::currency($buy_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                        <input type="hidden" name="buy_price" value="<?= Stringz::currencyOutput($buy_price1) ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <? if ($buy_stop) { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= Lang::string('buy-stop-price') ?></p>
                                                        <h4><b><?= Stringz::currency($buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                        <input type="hidden" name="buy_stop_price" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <? if ($buy_market_price1) { ?>
                                                    <label class="cont"><?= Lang::string('buy-market-price') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="buy_market_price" value="<?= $buy_market_price1 ?>"/>
                                                    <?php } ?>
                                                    <? if ($buy_limit) { ?>
                                                    <label class="cont"><?= Lang::string('buy-limit') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="buy_limit" value="<?= $buy_limit ?>"/>
                                                    <?php } ?>
                                                    <? if ($buy_stop) { ?>
                                                    <label class="cont" style="padding-left:2em;"><?= Lang::string('buy-stop') ?>   
                                                    <input disabled="disabled" class="checkbox" name="dummy" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="buy_stop" value="<?= $buy_stop ?>" />
                                                    <?php } ?>
                                                    <span class="checkmark"></span>
                                                    </label>
                                                    <? if ($buy_stop) { ?>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px;"><?= Lang::string('buy-subtotal') ?></p>
                                                        <h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($buy_amount1 * $buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                    </div>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px;">
                                                            <?= Lang::string('buy-fee') ?>
                                                            <h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
                                                        </p>
                                                    </div>
                                                    <div class="current-otr m-b-15">
                                                        <p style="margin-bottom:0px;">
                                                            <span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
                                                        </p>
                                                        <h4>
                                                            <span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
                                                            <b><span id="buy_total"><?= Stringz::currency(round($buy_amount1 * $buy_stop_price1 - ($user_fee_ask * 0.01) * $buy_amount1 * $buy_stop_price1 ,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP),($currency_info['is_crypto'] == 'Y')) ?></span></b>
                                                        </h4>
                                                    </div>
                                                    <? } else { ?>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px;"><?= Lang::string('buy-subtotal') ?></p>
                                                        <h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                    </div>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px;"><?= Lang::string('buy-fee') ?></p>
                                                        <h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
                                                    </div>
                                                    <div class="current-otr m-b-15">
                                                        <p style="margin-bottom:0px;">
                                                            <span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
                                                        </p>
                                                        <h4>
                                                            <span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
                                                            <b><?= $currency_info['fa_symbol'] ?><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></b>
                                                        </h4>
                                                    </div>
                                                    <? } ?>
                                                    <input type="hidden" name="buy" value="1" />
                                                    <input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
                                                    <div class="btn-otr">
                                                        <span>
                                                        <input type="submit" name="submit" value="<?= Lang::string('confirm-buy') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" style="width: auto;display: inline-block;" />
                                                        </span>
                                                        <span>
                                                            <!-- <input id="cancel_transaction" type="submit" name="dont" value="<?= Lang::string('confirm-back') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: right;padding: 12px 30px;" /> -->
                                                            <input id="cancel_transaction" type="submit" name="dont" value="Back" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn btn-primary" style="width: auto;display: inline-block;float: right;">
                                                        </span>
                                                        <p class="m-t-10"> By clicking CONFIRM button an order request will be created.</p>
                                                    </div>
                                                    <? } else { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= Lang::string('sell-amount') ?></p>
                                                        <h4><b><?= Stringz::currency($sell_amount1,true) ?></b></h4>
                                                        <input type="hidden" name="sell_amount" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
                                                    </div>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= Lang::string('buy-with-currency') ?></p>
                                                        <h4><b><?= $currency_info['currency'] ?></b></h4>
                                                        <input type="hidden" name="sell_currency" value="<?= $currency1 ?>" />
                                                    </div>
                                                    <? if ($sell_limit || $sell_market_price1) { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= ($sell_market_price1) ? Lang::string('buy-price') : Lang::string('buy-limit-price') ?></p>
                                                        <h4><b><?= Stringz::currency($sell_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                        <input type="hidden" name="sell_price" value="<?= Stringz::currencyOutput($sell_price1) ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <? if ($sell_stop) { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= Lang::string('buy-stop-price') ?></p>
                                                        <h4><b><?= Stringz::currency($sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                        <input type="hidden" name="sell_stop_price" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <? if ($sell_market_price1) { ?>
                                                    <label class="cont"><?= Lang::string('sell-market-price') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="sell_market_price" value="<?= $sell_market_price1 ?>" />
                                                    <?php } ?>
                                                    <? if ($sell_limit) { ?>
                                                    <label class="cont"><?= Lang::string('buy-limit') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="sell_limit" value="<?= $sell_limit ?>" />
                                                    <?php } ?>
                                                    <? if ($sell_stop) { ?>
                                                    <label class="cont"><?= Lang::string('buy-stop') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="sell_stop" value="<?= $sell_stop ?>" />
                                                    <?php } ?>
                                                    <span class="checkmark"></span>
                                                    </label>
                                                    <? if ($sell_stop) { ?>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px"><?= Lang::string('buy-subtotal') ?> </p>
                                                        <h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($sell_amount1 * $sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                    </div>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px">
                                                            <?= Lang::string('buy-fee') ?>
                                                        </p>
                                                        <h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
                                                    </div>
                                                    <div class="current-otr m-b-15">
                                                        <p style="margin-bottom:0px">
                                                            <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
                                                        </p>
                                                        <h4>
                                                            <span id="sell_total_label" style="display:none;"><?= Lang::string('sell-total') ?></span>
                                                            <b><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency(round($sell_amount1 * $sell_stop_price1 - ($user_fee_ask * 0.01) * $sell_amount1 * $sell_stop_price1 ,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP),($currency_info['is_crypto'] == 'Y')) ?></span></b>
                                                        </h4>
                                                    </div>
                                                    <? } else { ?>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px"><?= Lang::string('buy-subtotal') ?></p>
                                                        <h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                    </div>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px"><?= Lang::string('buy-fee') ?></p>
                                                        <h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
                                                    </div>
                                                    <div class="current-otr m-b-15">
                                                        <p style="margin-bottom:0px">
                                                            <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
                                                        </p>
                                                        <h4>
                                                            <span id="sell_total_label" style="display:none;"><?= Lang::string('sell-total') ?></span>
                                                            <b><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></b>
                                                        </h4>
                                                    </div>
                                                    <? } ?>
                                                    <input type="hidden" name="sell" value="1" />
                                                    <input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
                                                    <div class="btn-otr">
                                                        <span>
                                                        <input type="submit" name="submit" value="<?= Lang::string('confirm-sale') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" style="width: auto;display: inline-block;padding: 12px 30px;" />
                                                        </span>
                                                        <span>
                                                            <!-- <input id="cancel_transaction" type="submit" name="dont" value="<?= Lang::string('confirm-back') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: right;padding: 12px 30px;" /> -->
                                                            <input type="submit" name="dont" value="Back" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" style="width: auto;display: inline-block;float: right;padding: 12px 30px;">
                                                        </span>
                                                    </div>
                                                    <?php } ?>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <? endif; ?>
                       
                            </div>
                        </div>
                    </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <h6 class="right-side-widget-title"><strong class="heading-three">Trade History ( <?= $c_currency_info['currency']."  - ".$currency_info['currency']; ?>)</strong>
                                </h6>
                                <div class="right-side-widget history" style="background: #f4f4f4;height: auto;padding-bottom: 0;">
                                <div class="trade-history-view">
                                <div>
                                    <table class="table row-border table-hover no-header" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Pair</th>
                                                <th>Amount</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!count($my_transactions)): ?>
                                            <tr>
                                                <td col-span="3">No Trades</td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach($my_transactions as $transaction): ?>
                                            <tr class="clickable-row" data-href="">
                                                <td>
                                                    <div class="star-inner text-left">
                                                        <?= $c_currency_info['currency'] ?>/<?= $currency_info['currency'] ?>
                                                    </div>
                                                </td>
                                                <td><span class="<?= $transaction['type'] == 'Buy' ? 'green-color' : 'red-color' ?>"><?= Stringz::currency($transaction['btc'], true) . ' ' . $CFG->currencies[$transaction['c_currency']]['fa_symbol']  ?></span> </td>
                                                <td><span class="<?= $transaction['type'] == 'Buy' ? 'green-color' : 'red-color' ?>"><?= $CFG->currencies[$transaction['currency']]['fa_symbol'] ?><?= Stringz::currency($transaction['fiat_price'], ($transaction['is_crypto'] == 'Y')) ?></span></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
         <?php include "includes/sonance_footer.php"; ?>
    </body>

    
         <!-- EnjoyHint JS and CSS files -->

    
    <script>

        var sell_price = 0;
        var buy_price = 0;

        $(document).ready(function(){
        
        $(".Header__DropdownButton-dItiAm").click(function(){
        $(".DropdownMenu__Wrapper-ieiZya.kwMMmE").toggleClass("show-menu");
        });
        });
        
        sell_price = $("#sell_price").val();
        buy_price = $("#buy_price").val();

        $("#sell_market_price").on('change', function(){
            $("#sell_price").val(sell_price).change();
        })

        $("#buy_market_price").on('change', function(){
            $("#buy_price").val(buy_price).change();
        })


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
    <script type="text/javascript" src="js/ops.js?v=20160210"></script>
    <script>
    $(document).ready(function(){

        $('.clickable-row').on('click', function(){
            var href = $(this).data('href');
            window.location.href = href;
        })

    });
    
    var interval = setInterval(function(){
        
        if($(".tradingview-widget-container").html() != "") {
            $(".tradingview-widget-container").show();
            clearInterval(interval);
        }
    }, 100)
    </script>

<?php


$sql = "SELECT A.date,A.btc_price,B.currency FROM `transactions` A,`currencies` B WHERE A.c_currency = B.id AND c_currency = $currency_id";
$my_query = mysqli_query($conn,$sql);


// $timeframe1 = (!empty($_REQUEST['timeframe'])) ? preg_replace("/[^0-9a-zA-Z]/", "",$_REQUEST['timeframe']) : false;
// $timeframe2 = (!empty($_REQUEST['timeframe1'])) ? preg_replace("/[^0-9a-zA-Z]/", "",$_REQUEST['timeframe1']) : false;
// $currency1 = (!empty($CFG->currencies[$_REQUEST['currency']])) ? $_REQUEST['currency'] : false;
// $c_currency1 = (!empty($CFG->currencies[$_REQUEST['c_currency']])) ? $_REQUEST['c_currency'] : false;
// $first = (!empty($_REQUEST['first'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['first']) : false;
// $last = (!empty($_REQUEST['last'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['last']) : false;
// $_SESSION['timeframe'] = $timeframe1;

// if ($action != 'more')
//     API::add('Stats','getHistorical',array($timeframe2,$c_currency1,$currency1));

// API::add('Transactions','candlesticks',array($timeframe1,$c_currency1,$currency1,false,$first,$last));
// $query = API::send();

// if ($action != 'more') {
//     $stats = $query['Stats']['getHistorical']['results'][0];
//     $vars = array();
//     if ($stats) {
//         foreach ($stats as $row) {
//             $d = $row['date'];
//             $temp_date = date("d",strtotime($d));
//             $vars[] = '['.$temp_date.','.$row['price'].']';
//         }
//     }
//     $hist = '['.implode(',', $vars).']';
// }
// else {
//     $hist = '[]';
// }

// //var_dump($stats);

// foreach ($stats as $key => $value) {

//     $d = date("d",strtotime($value['date']));
//     $y = date("Y",strtotime($value['date']));
//     $m = date("m",strtotime($value['date']));
//     $value['day'] = $d;
//     $value['year'] = $y;
//     $value['month'] = $m;

// }
//var_dump($stats);

?>

<!--     <script type="text/javascript">

    var datas;
    // $.getJSON("includes/ajax.graph.php?timeframe=1d&timeframe1=6mon&currency=27&c_currency=28", function (json_data) {
    //         datas = json_data.history;
    //         alert("Ajax loaded");
    //         load_chart();
    //     });

    

    </script> -->

    <script type='text/javascript'>


        google.charts.load('current', {'packages':['annotatedtimeline']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', 'BTC');
        data.addColumn('string', 'title1');
        data.addColumn('string', 'text1');
        data.addRows([
            <?php while ($value = mysqli_fetch_assoc($my_query)) { 

                $d = date("d",strtotime($value['date']));
                $y = date("Y",strtotime($value['date']));
                $m = date("m",strtotime($value['date']));

                ?>
           [new Date(<?php echo $y; ?>, <?php echo $m-1; ?> ,<?php echo $d; ?>), <?php echo $value['btc_price']; ?>, undefined, undefined] ,
        <?php } ?>
        ]);

        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
        chart.draw(data, {displayAnnotations: false});
      }
    

    //load_chart();
    </script>

    <script type="text/javascript">
    $(document).ready(function () {

        $(window).scrollTop(0);
        return false;

    });
</script>

    </html>