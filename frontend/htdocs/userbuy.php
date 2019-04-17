<?php

    include '../lib/common.php';
    require_once ("cfg.php");
    // $conn = new mysqli("localhost","root","xchange123","bitexchange_cash");
//     error_reporting(E_ERROR | E_WARNING | E_PARSE);
// ini_set('display_errors', 1);
    if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y') {
        Link::redirect('userprofile.php');
    } elseif (User::$awaiting_token) {
        Link::redirect('verify-token.php');
    } elseif (!User::isLoggedIn()) {
        Link::redirect('login.php');
    }
      
    // $cur_sql = "SELECT * FROM currencies";
    // $currency_query = mysqli_query($conn,$cur_sql); 
    $currency_id = $_REQUEST['currency'];
    $currency_id1 = $_REQUEST['currency'];
    $c_currency_id = $_REQUEST['c_currency'];
    if (!$currency_id) {
       $currency_id = 28;
       $_REQUEST['currency'] = 28;
    }

    

    
    //$currencies = mysqli_fetch_assoc($currency_query);

    // CHECKING REFErral status 
    // $ch = curl_init("http://18.220.172.39/api/get-settings.php"); 
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);      
    curl_close($ch);
    $ref_response = json_decode($output);
    if ($ref_response->is_referral == 1) {
        $GLOBALS['REFERRAL'] = true;
        // $GLOBALS['REFERRAL_BASE_URL'] = "http://18.220.172.39/api/";
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
    API::add('Transactions','get24hData',array(45,27));
    API::add('Transactions','get24hData',array(45,28));
    API::add('Transactions','get24hData',array(42,45));
    API::add('Transactions','get24hData',array(43,45));
    API::add('Transactions','get24hData',array(44,27));
    API::add('Transactions','get24hData',array(44,28));
    API::add('Transactions','get24hData',array(43,27));
    API::add('Transactions','get24hData',array(43,28));
    API::add('Transactions','get24hData',array($c_currency1, $currency1));
    API::add('Transactions','get24hData',array(28,42)); //btc-ltc
    API::add('Transactions','get24hData',array(45,42)); //eth-ltc
    API::add('Transactions','get24hData',array(43,42)); //XLM-ltc
    API::add('Transactions','get24hData',array(44,42)); //XRP-ltc
    
    API::add('Transactions','get24hData',array(28,44)); //btc-bch
    API::add('Transactions','get24hData',array(45,44)); //btc-eth
    API::add('Transactions','get24hData',array(43,44)); //btc-zec
    API::add('Transactions','get24hData',array(42,44)); //btc-ltc
    
    API::add('Transactions','get24hData',array(28,43)); //btc-zec
    API::add('Transactions','get24hData',array(42,43)); //ltc-zec
    API::add('Transactions','get24hData',array(45,43)); //eth-zec
       API::add('Transactions','get24hData',array(44,43)); //XRP-zec
       
       API::add('Transactions','get24hData',array(28,45)); //btc-eth
    API::add('Transactions','get24hData',array(42,45)); //ltc-eth
    API::add('Transactions','get24hData',array(44,45)); //XRP-eth
       API::add('Transactions','get24hData',array(43,45)); //XLM-eth
       
    
       //my transactions 
       API::add('Transactions', 'get', array(false, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
       API::add('Transactions', 'getTypes');
    
    
    if ($currency_info['is_crypto'] != 'Y') {
        API::add('BankAccounts','get',array($currency_info['id']));
    }
        
       //echo $_REQUEST['buy_market_price']; exit;         
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

        //echo $_REQUEST['buy_market_price']; exit;
        $buy_market_price1 = (!empty($_REQUEST['buy_market_price']));
        $buy_market_price1 = $_REQUEST['buy_market_price']?$_REQUEST['buy_market_price']:"";
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
            if($bonous_point==''){
                $bonous_point = 0;
            }
            if($one_point_value==''){
                $one_point_value = 0;
            }

            if ($to_currency == 'USD') {
                if($bonous_point != 0 && $one_point_value !=0){
                    $bonus_amount = (float) $bonous_point / (float) $one_point_value;
                }else{
                    $bonus_amount = 0;
                }
                $cur_code = '$';
            }else{
                $one_point_values = $response->settings->$to_currency;
                if($one_point_values != '' && $bonous_point != 0){                    
                $bonus_amount = (float) $bonous_point / (float) $one_point_values;
                }else{
                    $bonus_amount = 0;
                }
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
        <?php
        $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='simple trade'");
        while($pagerow=mysqli_fetch_array($page_sql))
        {
            $page_id=$pagerow['id'];
        }

        $page_sql1=mysqli_query($conn_l, "select page_content_key,page_content from trans_page_value where page_id=".$page_id);

            $symbol = $_SESSION[LANG];
        while($pagerow1=mysqli_fetch_array($page_sql1))
        {
           
            $page_content = $pagerow1[0];
            // echo $page_content."<br>";
            $page_content1 = json_decode($pagerow1[1],true);
            // print_r($page_content1[$symbol][$page_content]);
            $pgcont[$page_content]=$page_content1[$symbol][$page_content];
            // print_r($pgcont);
           
        }

        // $pg_cont_sql=mysqli_query($conn_l, "select page_content_key, ".$_SESSION[LANG]."_page_content from trans_page_value where page_content_status=1 and page_id='".$page_id."'");
        // while($pagecontrow=mysqli_fetch_array($pg_cont_sql))
        // {
        //     $pgcont[$pagecontrow['page_content_key']]=$pagecontrow[$_SESSION[LANG].'_page_content'];
        // }       
        ?>

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
                            <p class="small gray-color"><?php echo isset($pgcont['simple_trade_last_price_key']) ? $pgcont['simple_trade_last_price_key'] : 'Last Price'; ?></p>
                            <p class="amount"><strong><span class="green-color"><?= number_format($currentPair['lastPrice'], 8) ?></span></strong></p>
                        </div>
                        <div class="head-splits">
                            <p class="small gray-color"><?php echo isset($pgcont['simple_trade_24change_key']) ? $pgcont['simple_trade_24change_key'] : '24h change'; ?></p>
                            <p class="amount"><strong><span class="red-color"><?= number_format($currentPair['change_24hrs'], 8) ?></span></strong></p>
                        </div>
                       
                        <div class="head-splits">
                            <p class="small gray-color"><?php echo isset($pgcont['simple_trade_24volume_key']) ? $pgcont['simple_trade_24volume_key'] : '24h Volume'; ?></p>
                            <p class="amount"><strong><span class="gray-color"><?= number_format($currentPair['transactions_24hrs'], 8) ?></span> <?= $c_currency_info['currency'] ?></strong></p>
                        </div>
                        </div>
                        </div>
                        <div class="left-side-widget">
                            <div class="bg-white">
                                <ul>
                                    <li><?php echo isset($pgcont['simple_trade_li_content1_key']) ? $pgcont['simple_trade_li_content1_key'] : 'The Simple Trade page lets you Buy / Sell cryptocurrencies on this exchange.'; ?></li>
                                    <li><?php echo isset($pgcont['simple_trade_li_content2_key']) ? $pgcont['simple_trade_li_content2_key'] : 'You can use a fiat currency or any cryptocurrency available on the Currency Pair section to buy / sell cryptocurrencies.'; ?></li>
                                    <li><?php echo isset($pgcont['simple_trade_li_content3_key']) ? $pgcont['simple_trade_li_content3_key'] : 'Check the market rate or the open order on the \'Exchange Open Orders\' section before you trade'; ?></li>
                            
                                    <li><?php echo isset($pgcont['simple_trade_li_content4_key']) ? $pgcont['simple_trade_li_content4_key'] : 'Deposit fiat currency in your Fiat Wallet, if you want to purchase your first or any Cryptocurrency.'; ?> <b>
                                    <a href="deposit"><?php echo isset($pgcont['simple_trade_li_content4_deposite_key']) ? $pgcont['simple_trade_li_content4_deposite_key'] : 'Add a Deposit'; ?></a></b></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-5 col-sm-12 col-xs-12">
                        <h6 class="right-side-widget-title"><strong class="heading-one"><?php echo isset($pgcont['simple_trade_open_market_key']) ? $pgcont['simple_trade_open_market_key'] : 'Open Orders on Market'; ?></strong>
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
                                    <a class="nav-link active" id="buy-tab" data-toggle="tab" href="#buy" role="tab" aria-controls="buy" aria-selected="true"><img src="sonance/img/icons/buy.jpg"><span style="font-size: 10px;margin-left: 5px;font-weight: 600;"><?php echo isset($pgcont['simple_trade_open_market_tab_buy_key']) ? $pgcont['simple_trade_open_market_tab_buy_key'] : 'Buy Orders'; ?></span></a>
                                </li>
                                <li class="nav-item" title="Sell Orders">
                                    <a class="nav-link" id="sell-tab" data-toggle="tab" href="#sell" role="tab" aria-controls="sell" aria-selected="false"><img src="sonance/img/icons/sell.jpg"> <span style="font-size: 10px;margin-left: 5px;font-weight: 600;"><?php echo isset($pgcont['simple_trade_open_market_tab_sell_key']) ? $pgcont['simple_trade_open_market_tab_sell_key'] : 'Sell Orders'; ?></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="myTab">
                                <div class="tab-pane fade show active" id="buy" role="tabpanel" aria-labelledby="buy-tab">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><?php echo isset($pgcont['simple_trade_open_market_table_price_key']) ? $pgcont['simple_trade_open_market_table_price_key'] : 'Price'; ?>(<?= $currency_info['currency'] ?>)</th>
                                                <th><?php echo isset($pgcont['simple_trade_open_market_table_amount_key']) ? $pgcont['simple_trade_open_market_table_amount_key'] : 'Amount'; ?>(<?= $c_currency_info['currency'] ?>)</th>
                                                <th><?php echo isset($pgcont['simple_trade_open_market_table_total_key']) ? $pgcont['simple_trade_open_market_table_total_key'] : 'Total'; ?>(<?= $currency_info['currency'] ?>)</th>
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
                                                <td colspan="3"><?php echo isset($pgcont['simple_trade_open_market_buy_no_data_key']) ? $pgcont['simple_trade_open_market_buy_no_data_key'] : 'No Buy Orders'; ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="sell" role="tabpanel" aria-labelledby="sell-tab">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><?php echo isset($pgcont['simple_trade_open_market_table_price_key']) ? $pgcont['simple_trade_open_market_table_price_key'] : 'Price'; ?>(<?= $currency_info['currency'] ?>)</th>
                                                <th><?php echo isset($pgcont['simple_trade_open_market_table_amount_key']) ? $pgcont['simple_trade_open_market_table_amount_key'] : 'Amount'; ?>(<?= $c_currency_info['currency'] ?>)</th>
                                                <th><?php echo isset($pgcont['simple_trade_open_market_table_total_key']) ? $pgcont['simple_trade_open_market_table_total_key'] : 'Total'; ?>(<?= $currency_info['currency'] ?>)</th>
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
                                                <td colspan="3"><?php echo isset($pgcont['simple_trade_open_market_sell_no_data_key']) ? $pgcont['simple_trade_open_market_sell_no_data_key'] : 'No Sell Orders'; ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                                     <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12">
                                <h6 class="right-side-widget-title"><strong class="heading-two"><?php echo isset($pgcont['simple_trade_currency_pair_key']) ? $pgcont['simple_trade_currency_pair_key'] : 'Currency Pairs'; ?></strong>
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
                                <?php
                                    $active_currency_id = $_REQUEST['currency'];
                                    $active_currency = $_REQUEST['c_currency'];
                                    $class_active = "";
                                ?>
                                <div class="right-side-widget">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <?php
                                                if($active_currency_id == 27)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";

                                                 // echo  $active_currency_id; 
                                            ?>
                                            <a class="nav-link <?php echo $class_active;  ?>" id="r-usd-tab" data-toggle="tab" href="#r-usd" role="tab" aria-controls="r-usd" aria-selected="true">USD</a>
                                        </li>
                                        <li class="nav-item">
                                            <?php
                                                if($active_currency_id == 28)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                            ?>
                                            <a class="nav-link <?php echo $class_active; ?>" id="r-btc-tab" data-toggle="tab" href="#r-btc" role="tab" aria-controls="r-btc" aria-selected="true">BTC</a>
                                        </li>
                                        <li class="nav-item">
                                            <?php
                                                if($active_currency_id == 42)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                            ?>
                                            <a class="nav-link <?php echo $class_active;  ?>" id="r-ltc-tab" data-toggle="tab" href="#r-ltc" role="tab" aria-controls="r-ltc" aria-selected="false">LTC</a>
                                        </li>
                                        <li class="nav-item">
                                            <?php
                                                if($active_currency_id == 46)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                            ?>
                                            <a class="nav-link <?php echo $class_active;  ?>" id="r-bch-tab" data-toggle="tab" href="#r-bch" role="tab" aria-controls="r-bch" aria-selected="false">XRP</a>
                                        </li>
                                        <li class="nav-item">
                                            <?php
                                                if($active_currency_id == 47)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                            ?>
                                            <a class="nav-link <?php echo $class_active;  ?>" id="r-zec-tab" data-toggle="tab" href="#r-zec" role="tab" aria-controls="r-zec" aria-selected="false">XLM</a>
                                        </li>
                                        <li class="nav-item">
                                            <?php
                                                if($active_currency_id == 45)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                            ?>
                                            <a class="nav-link <?php echo $class_active;  ?>" id="r-eth-tab" data-toggle="tab" href="#r-eth" role="tab" aria-controls="r-eth" aria-selected="false">ETH</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <?php
                                                if($active_currency_id == 27)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                                ?>
                                        <div class="tab-pane fade <?php echo $class_active;  ?>" id="r-usd" role="tabpanel" aria-labelledby="r-usd-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120"><?php echo isset($pgcont['simple_trade_currency_pair_table_pair_key']) ? $pgcont['simple_trade_currency_pair_table_pair_key'] : 'Pair'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_price_key']) ? $pgcont['simple_trade_currency_pair_table_price_key'] : 'Price'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_change_key']) ? $pgcont['simple_trade_currency_pair_table_change_key'] : 'Change'; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTC' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTC-USD&c_currency=28&currency=27">
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
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-USD&c_currency=42&currency=27">
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
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-USD&c_currency=45&currency=27">
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
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'XRP' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=XRP-USD&c_currency=46&currency=27">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                XRP/USD
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_xrp_usd['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_xrp_usd['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'XLM' && $currency_info['currency'] == 'USD') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=XLM-USD&c_currency=47&currency=27">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                XLM/USD
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_xlm_usd['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_xlm_usd['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                                if($active_currency_id == 28)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                                ?>
                                        <div class="tab-pane fade <?php echo $class_active;  ?>" id="r-btc" role="tabpanel" aria-labelledby="r-btc-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120"><?php echo isset($pgcont['simple_trade_currency_pair_table_pair_key']) ? $pgcont['simple_trade_currency_pair_table_pair_key'] : 'Pair'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_price_key']) ? $pgcont['simple_trade_currency_pair_table_price_key'] : 'Price'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_change_key']) ? $pgcont['simple_trade_currency_pair_table_change_key'] : 'Change'; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'BTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-BTC&c_currency=42&currency=28">
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
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'BTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-BTC&c_currency=45&currency=28">
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
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'XRP' && $currency_info['currency'] == 'BTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=XRP-BTC&c_currency=46&currency=28">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                XRP/BTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_xrp_btc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_xrp_btc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'XLM' && $currency_info['currency'] == 'BTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=XLM-BTC&c_currency=47&currency=28">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                XLM/BTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_xlm_btc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_xlm_btc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                                if($active_currency_id == 42)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                                ?>
                                        <div class="tab-pane fade <?php echo $class_active;  ?>" id="r-ltc" role="tabpanel" aria-labelledby="r-ltc-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120"><?php echo isset($pgcont['simple_trade_currency_pair_table_pair_key']) ? $pgcont['simple_trade_currency_pair_table_pair_key'] : 'Pair'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_price_key']) ? $pgcont['simple_trade_currency_pair_table_price_key'] : 'Price'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_change_key']) ? $pgcont['simple_trade_currency_pair_table_change_key'] : 'Change'; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTC' && $currency_info['currency'] == 'LTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTC-LTC&c_currency=28&currency=42">
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
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'LTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-LTC&c_currency=45&currency=42">
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
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'XLM' && $currency_info['currency'] == 'LTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=XLM-LTC&c_currency=47&currency=42">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                XLM/LTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_xlm_ltc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_xlm_ltc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'XRP' && $currency_info['currency'] == 'LTC') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=XRP-LTC&c_currency=46&currency=42">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                XRP/LTC
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_xrp_ltc['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_xrp_ltc['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                                if($active_currency_id == 44)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                                ?>
                                        <div class="tab-pane fade <?php echo $class_active;  ?>" id="r-bch" role="tabpanel" aria-labelledby="r-bch-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120"><?php echo isset($pgcont['simple_trade_currency_pair_table_pair_key']) ? $pgcont['simple_trade_currency_pair_table_pair_key'] : 'Pair'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_price_key']) ? $pgcont['simple_trade_currency_pair_table_price_key'] : 'Price'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_change_key']) ? $pgcont['simple_trade_currency_pair_table_change_key'] : 'Change'; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTC' && $currency_info['currency'] == 'XRP') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTH-XRP&c_currency=28&currency=46">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BTC/XRP
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_btc_xrp['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_btc_xrp['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'XRP') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-XRP&c_currency=45&currency=46">
                                                        <td>
                                                            <div class="star-inner text-left" style="font-size:9px">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ETH/XRP
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_eth_xrp['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_eth_xrp['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ZEC' && $currency_info['currency'] == 'XRP') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ZEC-XRP&c_currency=43&currency=46">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ZEC/XRP
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_zec_xrp['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_zec_xrp['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'XRP') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-XRP&c_currency=42&currency=46">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                LTC/XRP
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_ltc_xrp['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_ltc_xrp['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                                if($active_currency_id == 43)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                                ?>
                                        <div class="tab-pane fade <?php echo $class_active;  ?>" id="r-zec" role="tabpanel" aria-labelledby="r-zec-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120"><?php echo isset($pgcont['simple_trade_currency_pair_table_pair_key']) ? $pgcont['simple_trade_currency_pair_table_pair_key'] : 'Pair'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_price_key']) ? $pgcont['simple_trade_currency_pair_table_price_key'] : 'Price'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_change_key']) ? $pgcont['simple_trade_currency_pair_table_change_key'] : 'Change'; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTC' && $currency_info['currency'] == 'XLM') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTC-XLM&c_currency=28&currency=47">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BTC/XLM
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_btc_xlm['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_btc_xlm['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'XLM') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-XLM&c_currency=42&currency=47">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                LTC/XLM
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_ltc_xlm['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_ltc_xlm['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'ETH' && $currency_info['currency'] == 'XLM') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=ETH-XLM&c_currency=45&currency=47">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                ETH/XLM
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_eth_xlm['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_eth_xlm['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'XRP' && $currency_info['currency'] == 'XLM') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BCH-XLM&c_currency=44&currency=47">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                BCH/XLM
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_bch_xlm['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_bch_xlm['change_24hrs'] ?></span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                                if($active_currency_id == 45)
                                                    $class_active = "active show";
                                                else
                                                    $class_active = "";
                                                ?>
                                        <div class="tab-pane fade <?php echo $class_active;  ?>" id="r-eth" role="tabpanel" aria-labelledby="r-eth-tab">
                                            <table class="table row-border right-data-table table-hover" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="120"><?php echo isset($pgcont['simple_trade_currency_pair_table_pair_key']) ? $pgcont['simple_trade_currency_pair_table_pair_key'] : 'Pair'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_price_key']) ? $pgcont['simple_trade_currency_pair_table_price_key'] : 'Price'; ?></th>
                                                        <th><?php echo isset($pgcont['simple_trade_currency_pair_table_change_key']) ? $pgcont['simple_trade_currency_pair_table_change_key'] : 'Change'; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'BTH' && $currency_info['currency'] == 'ETH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=BTC-ETH&c_currency=28&currency=45">
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
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'LTC' && $currency_info['currency'] == 'ETH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=LTC-ETH&c_currency=42&currency=45">
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
                                                    <tr class="clickable-row" data-href="userbuy?trade=XRP-ETH&c_currency=46&currency=45">
                                                        <td>
                                                            <div class="star-inner text-left" style="font-size:9px">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                XRP/ETH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_xrp_eth['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_xrp_eth['change_24hrs'] ?></span></td>
                                                    </tr>
                                                    <tr class="clickable-row <?= ($c_currency_info['currency'] == 'XLM' && $currency_info['currency'] == 'ETH') ? 'userbuy-active' : "" ?>" data-href="userbuy?trade=XLM-ETH&c_currency=47&currency=45">
                                                        <td>
                                                            <div class="star-inner text-left">
                                                                <input id="star1" type="checkbox" name="time" />
                                                                <label for="star1"></label>
                                                                XLM/ETH
                                                            </div>
                                                        </td>
                                                        <td><span class="green-color"><?= $transactions_24hrs_xlm_eth['lastPrice'] ?></span> </td>
                                                        <td><span class="red-color"><?= $transactions_24hrs_xlm_eth['change_24hrs'] ?></span></td>
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
                      <!--   <form name="chat_filter" method="get" action="<?php echo $url; ?>">
                            <input type="hidden" name="trade" value="<?php echo $_REQUEST['trade']; ?>">
                        <div class="row">
                            <div class="col-md-2 col-sm-6 col-xs-6">
                                <select class="form-group form-control" name="currency">
                                 <?php while($currency = mysqli_fetch_assoc($currency_query)) { ?>
                                        <option 
                                        <?php if($_REQUEST['currency'] == $currency['id']) { $g_name = $currency['currency'];?> 
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
                        </form> -->
                        
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
                                    <a class="nav-link active" id="limit-tab" data-toggle="tab" href="#limit" role="tab" aria-controls="limit" aria-selected="true" style="display: inline-block;"><?php echo isset($pgcont['simple_trade_buy_sell_key']) ? $pgcont['simple_trade_buy_sell_key'] : 'Buy-or-Sell'; ?></a>
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
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <form id="buy_form" action="userbuy.php?trade=<?= $market ?>" method="POST">
                                                <h6 class="title"><strong><?php echo isset($pgcont['simple_trade_buy_crypto_key']) ? $pgcont['simple_trade_buy_crypto_key'] : 'Buy Cryptocurrency'; ?></strong></h6>
                                                <div class="form-group">
                                                    <label for=""><?php echo isset($pgcont['simple_trade_available_key']) ? $pgcont['simple_trade_available_key'] : 'Available Balance'; ?>(<span class="sell_currency_label"><?= $currency_info['currency'] ?></span>)</label>
                                                    <span class="form-control center-widget" style="margin-top: 0px;"><span class="buy_currency_char"><?= $currency_info['fa_symbol'] ?></span>
                                                    <span id="buy_user_available" style="color: #2f8afd;"><?= ((!empty($user_available[strtoupper($currency_info['currency'])])) ? Stringz::currency($user_available[strtoupper($currency_info['currency'])],($currency_info['is_crypto'] == 'Y')) : '0.00') ?></span></span>
                                                </div>
                                                <div class="Flex__Flex-fVJVYW gkSoIH">
                                                    <div class="form-group">
                                                        <label><?php echo isset($pgcont['simple_trade_buy_confirm_amount_key']) ? $pgcont['simple_trade_buy_confirm_amount_key'] : Lang::string('buy-amount'); ?></label>
                                                        <input name="buy_amount" id="buy_amount" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
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
                                                                <label class="position-relative"><?php echo isset($pgcont['simple_trade_currency_use_key']) ? $pgcont['simple_trade_currency_use_key'] : Lang::string('buy-with-currency'); ?></label>
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
                                                    <input style="vertical-align:middle" class="checkbox" name="buy_market_price" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1 && !$buy_stop) ? 'checked="checked"' : '' ?> <?= (!$asks) ? 'readonly="readonly"' : '' ?> />
                                                    <?php echo isset($pgcont['simple_trade_buy_market_key']) ? $pgcont['simple_trade_buy_market_key'] : Lang::string('buy-market-price'); ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <label class="cont">
                                                    <input class="checkbox" name="buy_limit" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
                                                    <?php echo isset($pgcont['simple_trade_limit_key']) ? $pgcont['simple_trade_limit_key'] : Lang::string('buy-limit'); ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <label class="cont">
                                                    <input class="checkbox" name="buy_stop" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
                                                    <?php echo isset($pgcont['simple_trade_stop_key']) ? $pgcont['simple_trade_stop_key'] : Lang::string('buy-stop'); ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" id="buy_price_container" <?= (!$buy_limit && !$buy_market_price1) ? 'style="display:none;"' : '' ?>>
                                                    <!-- <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span id="buy_price_limit_label" <?= (!$buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="buy_price_market_label" <?= ($buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></h4>
                                                    </div> -->
                                                    <div>
                                                        <div class="Flex__Flex-fVJVYW gkSoIH">
                                                            <div class="form-group">
                                                                <label><span id="buy_price_limit_label" <?= (!$buy_limit) ? 'style="display:none;"' : '' ?>><?php echo isset($pgcont['simple_trade_limit_key']) ? $pgcont['simple_trade_limit_key'] : Lang::string('buy-limit-price'); ?></span><span id="buy_price_market_label" <?= ($buy_limit) ? 'style="display:none;"' : '' ?>><?php echo isset($pgcont['simple_trade_history_table_price_key']) ? $pgcont['simple_trade_history_table_price_key'] : Lang::string('buy-price'); ?></span></label>
                                                                <input name="buy_price" id="buy_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_price1) ?>" <?= ($buy_market_price1) ? 'readonly="readonly"' : '' ?> />
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
                                                                    <label><span><?php echo isset($pgcont['simple_trade_stop_key']) ? $pgcont['simple_trade_stop_key'] : Lang::string('buy-stop-price'); ?></span><span style="display:none;"><?php echo isset($pgcont['simple_trade_history_table_price_key']) ? $pgcont['simple_trade_history_table_price_key'] : 'Price'; ?></span></label>
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
                                                        <?php echo isset($pgcont['simple_trade_subtotal_key']) ? $pgcont['simple_trade_subtotal_key'] : Lang::string('buy-subtotal'); ?>
                                                        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_subtotal"><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
                                                    </p>
                                                </div>
                                                <div class="current-otr">
                                                    <p>
                                                        <?php echo isset($pgcont['simple_trade_fee_key']) ? $pgcont['simple_trade_fee_key'] : Lang::string('buy-fee'); ?>
                                                        <span class="pull-right"><span id="buy_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</span>
                                                    </p>
                                                </div>

                                                <?php if($REFERRAL == true){ ?>
                                                <input type="hidden" name="ref_status" id="ref_status" value="1">
                                                <input type="hidden" name="bonus_amount" id="bonus_amount" value="<? echo $bonus_amount; ?>">
                                                <label class="cont" style="color: brown;font-style:  italic;">
                                                    <input 
                                                    class="checkbox" 
                                                    name="is_referral" 
                                                    id="is_referral" 
                                                    onclick="calculateBuyPrice()"
                                                    type="checkbox" value="1"
                                                    <?if($bonous_point == 0){ echo 'disabled'; } ?>
                                                     />
                                                    <?php echo isset($pgcont['simple_trade_referral_bonus_key']) ? $pgcont['simple_trade_referral_bonus_key'] : 'Use your Referral Bonus'; ?>

                                                    <span style="float: right;">    
                                                        <? echo $cur_code; ?> <? echo $bonus_amount; ?>
                                                    </span>

                                                    <span class="checkmark"></span>
                                                </label>
                                                <?php } ?>

                                                <div class="current-otr m-b-15">
                                                    <p>
                                                        <span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',(isset($pgcont['simple_trade_approx_key']) ? $pgcont['simple_trade_approx_key'].'. '.$c_currency_info['currency'].' '.$pgcont['simple_trade_to_spend_key'] : Lang::string('buy-total-approx'))) ?></span>
                                                        <span id="buy_total_label" style="display:none;"><?php echo isset($pgcont['simple_trade_to_receive_key']) ? $c_currency_info['currency'].' '.$pgcont['simple_trade_to_receive_key'] : Lang::string('buy-total'); ?></span>
                                                        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
                                                    </p>
                                                </div>
                                                <input type="hidden" name="buy" value="1" />
                                                <input type="hidden" name="buy_all" id="buy_all" value="<?= $buy_all1 ?>" />
                                                <input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
                                                <input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],(isset($pgcont['simple_trade_buy_button_key']) ? $pgcont['simple_trade_buy_button_key'].' '.$c_currency_info['currency'] : Lang::string('buy-bitcoins'))) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary"/>
                                            </form>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <form id="sell_form" action="userbuy.php?trade=<?= $market ?>" method="POST">
                                                <h6 class="title"><strong><?php echo isset($pgcont['simple_trade_sell_crypto_key']) ? $pgcont['simple_trade_sell_crypto_key'] : 'Sell Cryptocurrency'; ?></strong></h6>
                                                <div class="form-group">
                                                    <label for=""><?php echo isset($pgcont['simple_trade_available_key']) ? $pgcont['simple_trade_available_key'] : 'Available Balance'; ?>(<?= $c_currency_info['currency'] ?>)</label>
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
                                                                <label><?php echo isset($pgcont['simple_trade_sell_amount_key']) ? $pgcont['simple_trade_sell_amount_key'] : Lang::string('sell-amount'); ?></label>
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
                                                                <label><?php echo isset($pgcont['simple_trade_currency_use_key']) ? $pgcont['simple_trade_currency_use_key'] : Lang::string('buy-with-currency'); ?></label>
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
                                                    <?php echo isset($pgcont['simple_trade_sell_market_key']) ? $pgcont['simple_trade_sell_market_key'] : Lang::string('sell-market-price'); ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <label class="cont">
                                                    <input class="checkbox" name="sell_limit" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
                                                    <?php echo isset($pgcont['simple_trade_limit_key']) ? $pgcont['simple_trade_limit_key'] : Lang::string('buy-limit'); ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <label class="cont">
                                                    <input class="checkbox" name="sell_stop" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
                                                    <?php echo isset($pgcont['simple_trade_stop_key']) ? $pgcont['simple_trade_stop_key'] : Lang::string('buy-stop'); ?>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <div id="sell_price_container" class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" <?= (!$sell_limit && !$sell_market_price1) ? 'style="display:none;"' : '' ?>>
                                                    <!-- <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><span id="sell_price_limit_label" <?= (!$sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="sell_price_market_label" <?= ($sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></h4>
                                                    </div> -->
                                                    <div>
                                                        <div class="Flex__Flex-fVJVYW gkSoIH">
                                                            <div class="form-group">
                                                                <label><span id="sell_price_limit_label" <?= (!$sell_limit) ? 'style="display:none;"' : '' ?>><?php echo isset($pgcont['simple_trade_limit_price_key']) ? $pgcont['simple_trade_limit_price_key'] : Lang::string('buy-limit-price'); ?></span><span id="sell_price_market_label" <?= ($sell_limit) ? 'style="display:none;"' : '' ?>><?php echo isset($pgcont['simple_trade_history_table_price_key']) ? $pgcont['simple_trade_history_table_price_key'] : Lang::string('buy-price'); ?></span></label>
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
                                                                    <label><span><?php echo isset($pgcont['simple_trade_stop_key']) ? $pgcont['simple_trade_stop_key'] : Lang::string('buy-stop-price'); ?></span><span style="display:none;"><?php echo isset($pgcont['simple_trade_history_table_price_key']) ? $pgcont['simple_trade_history_table_price_key'] : 'Price'; ?></span></label>
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
                                                        <?php echo isset($pgcont['simple_trade_subtotal_key']) ? $pgcont['simple_trade_subtotal_key'] : Lang::string('buy-subtotal'); ?>
                                                        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_subtotal"><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
                                                    </p>
                                                </div>
                                                <div class="current-otr">
                                                    <p>
                                                        <?php echo isset($pgcont['simple_trade_fee_key']) ? $pgcont['simple_trade_fee_key'] : Lang::string('buy-fee'); ?>
                                                        <span class="pull-right"><span id="sell_user_fee"><?= Stringz::currency($user_fee_ask) ?></span>%</span>
                                                    </p>
                                                </div>

                                                <?php if($REFERRAL == true){ ?>
                                                <input type="hidden" name="bonus_amount" id="bonus_amount" value="<? echo $bonus_amount; ?>">
                                                <label class="cont" style="color: brown;font-style:  italic;">
                                                    <input 
                                                    class="checkbox" 
                                                    name="is_referral"  
                                                    id="is_referral_sell" 
                                                    onclick="calculateBuyPrice()"
                                                    type="checkbox" value="1" 
                                                    <? if($bonous_point == 0){ echo 'disabled'; } ?>
                                                    />
                                                    <?php echo isset($pgcont['simple_trade_referral_bonus_key']) ? $pgcont['simple_trade_referral_bonus_key'] : 'Use your Referral Bonus'; ?>

                                                    <span style="float: right;">    
                                                        <? echo $cur_code; ?> <? echo $bonus_amount; ?>
                                                    </span>

                                                    <span class="checkmark"></span>
                                                </label>
                                                <?php } ?>
                                                
                                                <div class="current-otr m-b-15">
                                                    <p>
                                                        <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',(isset($pgcont['simple_trade_approx_key']) ? $pgcont['simple_trade_approx_key'].'. '.$c_currency_info['currency'].' '.$pgcont['simple_trade_to_receive_key'] : Lang::string('sell-total-approx'))) ?></span>
                                                        <span id="sell_total_label" style="display:none;"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',(isset($pgcont['simple_trade_to_receive_key']) ? $pgcont['simple_trade_to_receive_key'] : Lang::string('sell-total'))) ?></span>
                                                        <span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
                                                    </p>
                                                </div>
                                                <input type="hidden" name="sell" value="1" />
                                                <input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
                                                <input type="submit" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],(isset($pgcont['simple_trade_sell_button_key']) ? $pgcont['simple_trade_sell_button_key'].' '.$c_currency_info['currency'] : Lang::string('sell-bitcoins'))) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary"/>
                                                <!-- <button class="Button__Container-hQftQV kZBVvC" disabled="">
                                                    <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                                        <div class="Flex__Flex-fVJVYW ghkoKS">Sell Bitcoin Instantly</div>
                                                    </div>
                                                    </button> -->
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <? else: ?>
                                <div class="TradeFormTabContainer__Container-cUyfJR eMNjQO Panel__Container-hCUKEb gmOPIV conform-screen" style="max-width: 700px;margin: auto;width:100%;">
                                    <div class="Flex__Flex-fVJVYW iDqRrV">
                                        <div class="TradeFormTabContainer__Tab-caAlbq keHVTX Flex__Flex-fVJVYW iDqRrV" style="border-right: none; text-align:center">
                                            <a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf">
                                            <input id="c_currency" type="text" value="28" style="display:none;">
                                            <span class="right" style="font-size: 1.2em;margin-top:1em;"><?php echo isset($pgcont['simple_trade_confirm_key']) ? $pgcont['simple_trade_confirm_key'] : Lang::string('confirm-transaction'); ?></span>
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
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_buy_confirm_amount_key']) ? $pgcont['simple_trade_buy_confirm_amount_key'] : Lang::string('buy-amount'); ?></p>
                                                        <h4><b><?= Stringz::currency($buy_amount1,true) ?></b></h4>
                                                        <input type="hidden" name="buy_amount" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
                                                    </div>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_currency_use_key']) ? $pgcont['simple_trade_currency_use_key'] : Lang::string('buy-with-currency'); ?></p>
                                                        <h4><b><?= $currency_info['currency'] ?></b></h4>
                                                        <input type="hidden" name="buy_currency" value="<?= $currency1 ?>" />
                                                    </div>
                                                    <? if ($buy_limit || $buy_market_price1) { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= ($buy_market_price1) ? (isset($pgcont['simple_trade_history_table_price_key']) ? $pgcont['simple_trade_history_table_price_key'] : Lang::string('buy-price')) : (isset($pgcont['simple_trade_buy_confirm_amount_key']) ? $pgcont['simple_trade_buy_confirm_amount_key'] : Lang::string('buy-limit-price')) ?></p>
                                                        <h4><b><?= Stringz::currency($buy_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                        <input type="hidden" name="buy_price" value="<?= Stringz::currencyOutput($buy_price1) ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <? if ($buy_stop) { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_stop_key']) ? $pgcont['simple_trade_stop_key'] : Lang::string('buy-stop-price'); ?></p>
                                                        <h4><b><?= Stringz::currency($buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                        <input type="hidden" name="buy_stop_price" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <? if ($buy_market_price1) { ?>
                                                    <label class="cont"><?php echo isset($pgcont['simple_trade_buy_market_key']) ? $pgcont['simple_trade_buy_market_key'] : Lang::string('buy-market-price'); ?>   <input disabled="disabled" class="checkbox" name="dummy" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="buy_market_price" value="<?= $buy_market_price1 ?>"/>
                                                    <?php } ?>
                                                    <? if ($buy_limit) { ?>
                                                    <label class="cont"><?php echo isset($pgcont['simple_trade_limit_key']) ? $pgcont['simple_trade_limit_key'] : Lang::string('buy-limit'); ?>   <input disabled="disabled" class="checkbox" name="dummy" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="buy_limit" value="<?= $buy_limit ?>"/>
                                                    <?php } ?>
                                                    <? if ($buy_stop) { ?>
                                                    <label class="cont" style="padding-left:2em;"><?php echo isset($pgcont['simple_trade_stop_key']) ? $pgcont['simple_trade_stop_key'] : Lang::string('buy-stop'); ?>   
                                                    <input disabled="disabled" class="checkbox" name="dummy" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="buy_stop" value="<?= $buy_stop ?>" />
                                                    <?php } ?>
                                                    <span class="checkmark"></span>
                                                    </label>
                                                    <? if ($buy_stop) { ?>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_subtotal_key']) ? $pgcont['simple_trade_subtotal_key'] : Lang::string('buy-subtotal'); ?></p>
                                                        <h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($buy_amount1 * $buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                    </div>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px;">
                                                            <?php echo isset($pgcont['simple_trade_fee_key']) ? $pgcont['simple_trade_fee_key'] : Lang::string('buy-fee'); ?>
                                                            <h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
                                                        </p>
                                                    </div>
                                                    <div class="current-otr m-b-15">
                                                        <p style="margin-bottom:0px;">
                                                            <span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',(isset($pgcont['simple_trade_approx_key']) ? $pgcont['simple_trade_approx_key'].'. '.$c_currency_info['currency'].' '.$pgcont['simple_trade_to_spend_key'] : Lang::string('buy-total-approx'))) ?></span>
                                                        </p>
                                                        <h4>
                                                            <span id="buy_total_label" style="display:none;"><?php echo isset($pgcont['simple_trade_to_receive_key']) ? $pgcont['simple_trade_to_receive_key'] : Lang::string('buy-total'); ?></span>
                                                            <b><span id="buy_total"><?= Stringz::currency(round($buy_amount1 * $buy_stop_price1 - ($user_fee_ask * 0.01) * $buy_amount1 * $buy_stop_price1 ,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP),($currency_info['is_crypto'] == 'Y')) ?></span></b>
                                                        </h4>
                                                    </div>
                                                    <? } else { ?>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_subtotal_key']) ? $pgcont['simple_trade_subtotal_key'] : Lang::string('buy-subtotal'); ?></p>
                                                        <h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                    </div>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_fee_key']) ? $pgcont['simple_trade_fee_key'] : Lang::string('buy-fee'); ?></p>
                                                        <h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
                                                    </div>
                                                    <div class="current-otr m-b-15">
                                                        <p style="margin-bottom:0px;">
                                                            <span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',(isset($pgcont['simple_trade_approx_key']) ? $pgcont['simple_trade_approx_key'].'. '.$c_currency_info['currency'].' '.$pgcont['simple_trade_to_spend_key'] : Lang::string('buy-total-approx'))) ?></span>
                                                        </p>
                                                        <h4>
                                                            <span id="buy_total_label" style="display:none;"><?php echo isset($pgcont['simple_trade_to_receive_key']) ? $pgcont['simple_trade_to_receive_key'] : Lang::string('buy-total'); ?></span>
                                                            <b><?= $currency_info['fa_symbol'] ?><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></b>
                                                        </h4>
                                                    </div>
                                                    <? } ?>
                                                    <input type="hidden" name="buy" value="1" />
                                                    <input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
                                                    <div class="btn-otr">
                                                        <span>
                                                        <input type="submit" name="submit" value="<?php echo isset($pgcont['simple_trade_buy_confirm_button_content_key']) ? $pgcont['simple_trade_buy_confirm_button_content_key'] : Lang::string('confirm-buy'); ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" style="width: auto;display: inline-block;" />
                                                        </span>
                                                        <span>
                                                            <!-- <input id="cancel_transaction" type="submit" name="dont" value="<?= Lang::string('confirm-back') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: right;padding: 12px 30px;" /> -->
                                                            <input id="cancel_transaction" type="submit" name="dont" value="<?php echo isset($pgcont['simple_trade_buy_confirm_button_back_key']) ? $pgcont['simple_trade_buy_confirm_button_back_key'] :  'Back'; ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn btn-primary" style="width: auto;display: inline-block;float: right;">
                                                        </span>
                                                        <p class="m-t-10"> <?php echo isset($pgcont['simple_trade_buy_confirm_button_key']) ? $pgcont['simple_trade_buy_confirm_button_key'] : 'By clicking CONFIRM button an order request will be created.'; ?></p>
                                                    </div>
                                                    <? } else { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_sell_amount_key']) ? $pgcont['simple_trade_sell_amount_key'] : Lang::string('sell-amount'); ?></p>
                                                        <h4><b><?= Stringz::currency($sell_amount1,true) ?></b></h4>
                                                        <input type="hidden" name="sell_amount" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
                                                    </div>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_currency_use_key']) ? $pgcont['simple_trade_currency_use_key'] : Lang::string('buy-with-currency'); ?></p>
                                                        <h4><b><?= $currency_info['currency'] ?></b></h4>
                                                        <input type="hidden" name="sell_currency" value="<?= $currency1 ?>" />
                                                    </div>
                                                    <? if ($sell_limit || $sell_market_price1) { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?= ($sell_market_price1) ? (isset($pgcont['simple_trade_history_table_price_key']) ? $pgcont['simple_trade_history_table_price_key'] : Lang::string('buy-price')) : (isset($pgcont['simple_trade_buy_confirm_amount_key']) ? $pgcont['simple_trade_buy_confirm_amount_key'] : Lang::string('buy-limit-price')) ?></p>
                                                        <h4><b><?= Stringz::currency($sell_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                        <input type="hidden" name="sell_price" value="<?= Stringz::currencyOutput($sell_price1) ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <? if ($sell_stop) { ?>
                                                    <div class="bskbTZ">
                                                        <p style="margin-bottom:0px;"><?php echo isset($pgcont['simple_trade_stop_key']) ? $pgcont['simple_trade_stop_key'] : Lang::string('buy-stop-price'); ?></p>
                                                        <h4><b><?= Stringz::currency($sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                        <input type="hidden" name="sell_stop_price" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <? if ($sell_market_price1) { ?>
                                                    <label class="cont"><?php echo isset($pgcont['simple_trade_sell_market_key']) ? $pgcont['simple_trade_sell_market_key'] : Lang::string('sell-market-price'); ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="sell_market_price" value="<?= $sell_market_price1 ?>" />
                                                    <?php } ?>
                                                    <? if ($sell_limit) { ?>
                                                    <label class="cont"><?php echo isset($pgcont['simple_trade_limit_key']) ? $pgcont['simple_trade_limit_key'] : Lang::string('buy-limit'); ?>  <input disabled="disabled" class="checkbox" name="dummy" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="sell_limit" value="<?= $sell_limit ?>" />
                                                    <?php } ?>
                                                    <? if ($sell_stop) { ?>
                                                    <label class="cont"><?php echo isset($pgcont['simple_trade_stop_key']) ? $pgcont['simple_trade_stop_key'] : Lang::string('buy-stop'); ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
                                                    <input type="hidden" name="sell_stop" value="<?= $sell_stop ?>" />
                                                    <?php } ?>
                                                    <span class="checkmark"></span>
                                                    </label>
                                                    <? if ($sell_stop) { ?>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px"><?php echo isset($pgcont['simple_trade_subtotal_key']) ? $pgcont['simple_trade_subtotal_key'] : Lang::string('buy-subtotal'); ?></p>
                                                        <h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($sell_amount1 * $sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                    </div>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px">
                                                            <?php echo isset($pgcont['simple_trade_fee_key']) ? $pgcont['simple_trade_fee_key'] :  Lang::string('buy-fee'); ?> 
                                                        </p>
                                                        <h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
                                                    </div>
                                                    <div class="current-otr m-b-15">
                                                        <p style="margin-bottom:0px">
                                                            <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',(isset($pgcont['simple_trade_approx_key']) ? $pgcont['simple_trade_approx_key'].'. '.$c_currency_info['currency'].' '.$pgcont['simple_trade_to_receive_key'] : Lang::string('sell-total-approx'))) ?></span>
                                                        </p>
                                                        <h4>
                                                            <span id="sell_total_label" style="display:none;"><?php echo isset($pgcont['simple_trade_to_receive_key']) ? $pgcont['simple_trade_to_receive_key'] :  Lang::string('sell-total'); ?></span>
                                                            <b><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency(round($sell_amount1 * $sell_stop_price1 - ($user_fee_ask * 0.01) * $sell_amount1 * $sell_stop_price1 ,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP),($currency_info['is_crypto'] == 'Y')) ?></span></b>
                                                        </h4>
                                                    </div>
                                                    <? } else { ?>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px"><?php echo isset($pgcont['simple_trade_subtotal_key']) ? $pgcont['simple_trade_subtotal_key'] : Lang::string('buy-subtotal'); ?></p>
                                                        <h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
                                                    </div>
                                                    <div class="current-otr">
                                                        <p style="margin-bottom:0px"><?php echo isset($pgcont['simple_trade_fee_key']) ? $pgcont['simple_trade_fee_key'] :  Lang::string('buy-fee'); ?></p>
                                                        <h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
                                                    </div>
                                                    <div class="current-otr m-b-15">
                                                        <p style="margin-bottom:0px">
                                                            <span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',(isset($pgcont['simple_trade_approx_key']) ? $pgcont['simple_trade_approx_key'].'. '.$c_currency_info['currency'].' '.$pgcont['simple_trade_to_receive_key'] : Lang::string('sell-total-approx'))) ?></span>
                                                        </p>
                                                        <h4>
                                                            <span id="sell_total_label" style="display:none;"><?php echo isset($pgcont['simple_trade_to_receive_key']) ? $pgcont['simple_trade_to_receive_key'] :  Lang::string('sell-total'); ?></span>
                                                            <b><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></b>
                                                        </h4>
                                                    </div>
                                                    <? } ?>
                                                    <input type="hidden" name="sell" value="1" />
                                                    <input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
                                                    <div class="btn-otr">
                                                        <span>
                                                        <input type="submit" name="submit" value="<?php echo isset($pgcont['simple_trade_sell_confirm_button_key']) ? $pgcont['simple_trade_sell_confirm_button_key'] :  Lang::string('confirm-sale'); ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" style="width: auto;display: inline-block;padding: 12px 30px;" />
                                                        </span>
                                                        <span>
                                                            <!-- <input id="cancel_transaction" type="submit" name="dont" value="<?= Lang::string('confirm-back') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc" style="width: auto;display: inline-block;float: right;padding: 12px 30px;" /> -->
                                                            <input type="submit" name="dont" value="<?php echo isset($pgcont['simple_trade_buy_confirm_button_back_key']) ? $pgcont['simple_trade_buy_confirm_button_back_key'] :  'Back'; ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" style="width: auto;display: inline-block;float: right;padding: 12px 30px;">
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
                                <h6 class="right-side-widget-title"><strong class="heading-three"><?php echo isset($pgcont['simple_trade_history_key']) ? $pgcont['simple_trade_history_key'] :  'Trade History'; ?> ( <?= $c_currency_info['currency']."  - ".$currency_info['currency']; ?>)</strong>
                                </h6>
                                <div class="right-side-widget history" style="background: #f4f4f4;height: auto;padding-bottom: 0;">
                                <div class="trade-history-view">
                                <div>
                                    <table class="table row-border table-hover no-header" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th><?php echo isset($pgcont['simple_trade_history_table_pair_key']) ? $pgcont['simple_trade_history_table_pair_key'] :  'Pair'; ?></th>
                                                <th><?php echo isset($pgcont['simple_trade_history_table_amount_key']) ? $pgcont['simple_trade_history_table_amount_key'] :  'Amount'; ?></th>
                                                <th><?php echo isset($pgcont['simple_trade_history_table_price_key']) ? $pgcont['simple_trade_history_table_price_key'] :  'Price'; ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!count($my_transactions)): ?>
                                            <tr>
                                                <td col-span="3"><?php echo isset($pgcont['simple_trade_history_table_no_data_key']) ? $pgcont['simple_trade_history_table_no_data_key'] :  'No Trades'; ?></td>
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

        
            <!-- Modal-1-->
<div class="modal fade" id="openmarket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['simple_trade_open_market_key']) ? $pgcont['simple_trade_open_market_key'] :  'Open Orders on Market'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['simple_trade_open_market_content_key']) ? $pgcont['simple_trade_open_market_content_key'] :  'List of all the Open Buy / Sell orders in this exchange. You can decide to create buy / sell orders based on the value / price shown below'; ?></p>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>
<!--modal-2-->
<div class="modal fade" id="cpair" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['simple_trade_currency_pair_key']) ? $pgcont['simple_trade_currency_pair_key'] :  'Currency Pairs'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['simple_trade_currency_pair_modal_content_key']) ? $pgcont['simple_trade_currency_pair_modal_content_key'] :  'Choose a currency pair you would like to trade with.'; ?></p>
        <ul>
            <li><?php echo isset($pgcont['simple_trade_currency_pair_modal_li1_key']) ? $pgcont['simple_trade_currency_pair_modal_li1_key'] :  'For example, if you want to use (US Dollar a.k.a Fiat Currency) USD to buy a Bitcoin (BTC) choose USD/BTC currency pair'; ?></li>
            <li><?php echo isset($pgcont['simple_trade_currency_pair_modal_li2_key']) ? $pgcont['simple_trade_currency_pair_modal_li2_key'] :  'If you want to use an Litecoin (LTC) to buy Bitcoin (BTC), use LTC/BTC'; ?></li>
            <li><?php echo isset($pgcont['simple_trade_currency_pair_modal_li3_key']) ? $pgcont['simple_trade_currency_pair_modal_li3_key'] :  'Once you choose the respective currency pair, it will be reflected in the Buy or Sell Box, When you can use the respective currencies to purchase a Cryptocurrency.'; ?></li>
            <li><?php echo isset($pgcont['simple_trade_currency_pair_modal_li4_key']) ? $pgcont['simple_trade_currency_pair_modal_li4_key'] :  'Only the cryptocurrencies shown in the Currency Pair box is available to trade with on this Exchange.'; ?></li>
        </ul>
      </div>
     <!--  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>
<!--modal-2-->
<div class="modal fade" id="buysell" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['simple_trade_buy_sell_key']) ? $pgcont['simple_trade_buy_sell_key'] :  'Buy-or-Sell'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['simple_trade_buy_sell_modal_content_key']) ? $pgcont['simple_trade_buy_sell_modal_content_key'] :  'Here you can buy or sell the cryptocurrencies supported by this exchange.'; ?> </p>
        <ol>
            <li><?php echo isset($pgcont['simple_trade_buy_sell_modal_li1_key']) ? $pgcont['simple_trade_buy_sell_modal_li1_key'] :  'Make sure you choose the currency pair you wish to trade with at the CURRENCY PAIR section.'; ?> </li>
            <li><?php echo isset($pgcont['simple_trade_buy_sell_modal_li2_key']) ? $pgcont['simple_trade_buy_sell_modal_li2_key'] :  'If you are trading at the Current Market Price, make sure you have seen the current market rate ON THIS EXCHANGE before you make a purchase or sell.'; ?></li>
            <li><?php echo isset($pgcont['simple_trade_buy_sell_modal_li3_key']) ? $pgcont['simple_trade_buy_sell_modal_li3_key'] :  'If you are using STOP Limit, make sure you set the ideal price you wish the buy or sell at the Stop limit value box. Once the price crosses the amount set in the Stop limit box, the order will get executed.'; ?></li>
            <li><?php echo isset($pgcont['simple_trade_buy_sell_modal_li4_key']) ? $pgcont['simple_trade_buy_sell_modal_li4_key'] :  'If you are choosing limit price, when the price matches the amount set, the order gets executed.'; ?></li>
            <li><?php echo isset($pgcont['simple_trade_buy_sell_modal_li5_key']) ? $pgcont['simple_trade_buy_sell_modal_li5_key'] :  'Once you have successfully created the order request. You can check the status of it in the respective order history / open order section / trade history section.'; ?></li>
        </ol>
      </div>
     <!--  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div> -->
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


$sql = "SELECT A.date,A.btc_price,B.currency FROM `transactions` A,`currencies` B WHERE A.c_currency = B.id AND A.c_currency = $c_currency_id  AND A.currency = $currency_id1";
$my_query = mysqli_query($conn,$sql);
// echo "hello";
// var_dump(mysqli_fetch_assoc($my_query));

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
        data.addColumn('number', '<?php echo $g_name; ?>');
        data.addColumn('string', 'title1');
        data.addColumn('string', 'text1');
        data.addRows([
            <?php while ($value = mysqli_fetch_assoc($my_query)) {

                $d = date("d",strtotime($value['date']));
                $y = date("Y",strtotime($value['date']));
                $m = date("m",strtotime($value['date']));

                ?>
           [new Date(<?php echo $y; ?>, <?php echo $m-1; ?> ,<?php echo $d; ?>), <?php echo $value['btc_price']; ?>, undefined, undefined] ,
        <?php } if(!mysqli_fetch_assoc($my_query)){
           
            for($i=1;$i<=2;$i++)
            {          
            $btc_p=0;           
            if($i==1) {
                $year = date("Y");
                $month = date("m");
                $date = date("d");
            }
            else {
                $year = date('Y',(strtotime ( '-1 day' , strtotime ( date('Y-m-d')) ) ));
                $month = date('m',(strtotime ( '-1 day' , strtotime ( date('Y-m-d')) ) ));
                $date = date('d',(strtotime ( '-1 day' , strtotime ( date('Y-m-d')) ) ));
            }

            ?>
            [new Date(<?php echo $year; ?>, <?php echo $month-1; ?> ,<?php echo $date; ?>), <?php echo $btc_p; ?>, undefined, undefined] ,           
           
            <?php } } ?>
        ]);

        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
        chart.draw(data, {displayAnnotations: false});
      }
    

    //load_chart();
    </script>

    <script type="text/javascript">
    $(document).ready(function () {

        if(document.getElementById('is_referral').checked) {
            calculateBuyPrice();
        }else{
            calculateBuyPrice();
        }

        if(document.getElementById('is_referral_sell').checked) {
            calculateBuyPrice();
        }else{
            calculateBuyPrice();
        }
        $(window).scrollTop(0);
        return false;

    });
</script>
<script type="text/javascript" src="js/script.js"></script>

    </html>