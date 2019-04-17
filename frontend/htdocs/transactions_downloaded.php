<?php
include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
	Link::redirect('settings.php');
elseif (User::$awaiting_token)
	Link::redirect('verify-token.php');
elseif (!User::isLoggedIn())
	Link::redirect('login.php');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=transactions_'.date('Y-m-d').'.csv');

// API::add('Transactions','get',array(false,false,false,false,false,1,false,false,false,false,false,1));
// $query = API::send();

// $transactions = $query['Transactions']['get']['results'][0];

  $currencies = Settings::sessionCurrency();
        // $page_title = Lang::string('order-book');
       

        $c_currency1 = $_GET['c_currency'] ? : 28;
        $currency1 = $_GET['currency'] ? : 27;


        //commented out because of not required to take currency and c_currency from session
        /* $currency1 = $currencies['currency'];
        $c_currency1 = $currencies['c_currency']; */
       /*  if(!$currency1 || empty($currency1)){
            $currency1 = 27 ;
        }
        if(!$c_currency1 || empty($c_currency1)){
            $c_currency1 = 28 ;
        } */
        $currency_info = $CFG->currencies[$currency1];
        $c_currency_info = $CFG->currencies[$c_currency1];
        
        API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, false, false, 1));
        API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, false, false, false, false, 1));
        API::add('Transactions', 'get', array(false, false, 1, $c_currency1, $currency1));
        $query = API::send();
        
        $bids = $query['Orders']['get']['results'][0];
        $asks = $query['Orders']['get']['results'][1];
        $last_transaction = $query['Transactions']['get']['results'][0][0];
        $last_trans_currency = ($last_transaction['currency'] == $currency_info['id']) ? false : (($last_transaction['currency1'] == $currency_info['id']) ? false : ' (' . $CFG->currencies[$last_transaction['currency1']]['currency'] . ')');
        $last_trans_symbol = $currency_info['fa_symbol'];
        $last_trans_color = ($last_transaction['maker_type'] == 'sell') ? 'price-green' : 'price-red';
        
        if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']), $CFG->currencies)))
            $_SESSION['oo_c_currency'] = preg_replace("/[^0-9]/", "", $_REQUEST['c_currency']);
        else if (empty($_SESSION['oo_c_currency']) || $_REQUEST['c_currency'] == 'All')
            $_SESSION['oo_c_currency'] = false;
        
        if ((!empty($_REQUEST['currency']) && array_key_exists(strtoupper($_REQUEST['currency']), $CFG->currencies)))
            $_SESSION['oo_currency'] = preg_replace("/[^0-9]/", "", $_REQUEST['currency']);
        else if (empty($_SESSION['oo_currency']) || $_REQUEST['currency'] == 'All')
            $_SESSION['oo_currency'] = false;
        
        if ((!empty($_REQUEST['order_by'])))
            $_SESSION['oo_order_by'] = preg_replace("/[^a-z]/", "", $_REQUEST['order_by']);
        else if (empty($_SESSION['oo_order_by']))
            $_SESSION['oo_order_by'] = false;
        
        $open_currency1 = $_SESSION['oo_currency'];
        $open_c_currency1 = $_SESSION['oo_c_currency'];
        $order_by1 = $_SESSION['oo_order_by'];
        $trans_realized1 = (!empty($_REQUEST['transactions'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['transactions']) : false;
        $id1 = (!empty($_REQUEST['id'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['id']) : false;
        $bypass = (!empty($_REQUEST['bypass']));
        
        API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, 1, false, 1, $order_by1, false, 1));
        API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, 1, false, false, $order_by1, 1, 1));
        $query = API::send();
        
        $open_bids = $query['Orders']['get']['results'][0];
        $open_asks = $query['Orders']['get']['results'][1];
        $open_currency_info = ($open_currency1) ? $CFG->currencies[strtoupper($open_currency1)] : false;
        
        if (!empty($_REQUEST['new_order']) && !$trans_realized1)
            Messages::add(Lang::string('transactions-orders-new-message'));
        if (!empty($_REQUEST['edit_order']) && !$trans_realized1)
            Messages::add(Lang::string('transactions-orders-edit-message'));
        elseif (!empty($_REQUEST['new_order']) && $trans_realized1 > 0)
            Messages::add(str_replace('[transactions]', $trans_realized1, Lang::string('transactions-orders-done-message')));
        elseif (!empty($_REQUEST['edit_order']) && $trans_realized1 > 0)
            Messages::add(str_replace('[transactions]', $trans_realized1, Lang::string('transactions-orders-done-edit-message')));
        elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'order-doesnt-exist')
            Errors::add(Lang::string('orders-order-doesnt-exist'));
        elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'not-your-order')
            Errors::add(Lang::string('orders-not-yours'));
        elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'order-cancelled')
            Messages::add(Lang::string('orders-order-cancelled'));
        elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'deleteall-error')
            Errors::add(Lang::string('orders-order-cancelled-error'));
        elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'deleteall-success')
            Messages::add(Lang::string('orders-order-cancelled-all'));
        
        $_SESSION["openorders_uniq"] = md5(uniqid(mt_rand(), true));
        
        //transaction
        
        API::add('Transactions', 'get', array(1, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
        $query = API::send();
        $total = $query['Transactions']['get']['results'][0];
        
        API::add('Transactions', 'get', array(false, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
        API::add('Transactions', 'getTypes');
        $query = API::send();
        
        $transactions = $query['Transactions']['get']['results'][0];
        
if ($transactions) {
	$output = fopen('php://output', 'w');
	fputcsv($output, array(' '.Lang::string('transactions-type').' ',' '.Lang::string('transactions-time').' ',' '.Lang::string('market').' ',' '.Lang::string('currency').' ',' '.Lang::string('orders-amount').' ',' '.Lang::string('transactions-fiat').' ',' '.Lang::string('orders-price').' ',' '.Lang::string('transactions-fee').' '));
	foreach ($transactions as $transaction) {
		fputcsv($output,array(
			' '.$transaction['type'].' ',
			' '.date('M j, Y, H:i',strtotime($transaction['date']) + $CFG->timezone_offset).' UTC ',
			' '.$CFG->currencies[$transaction['c_currency']]['currency'].' ',
			' '.$CFG->currencies[$transaction['currency']]['currency'].' ',
			' '.Stringz::currency($transaction['btc'],true).' ',
			' '.Stringz::currency($transaction['btc_net'] * $transaction['fiat_price'],($transaction['is_crypto'] == 'Y')).' ',
			' '.Stringz::currency($transaction['fiat_price'],($transaction['is_crypto'] == 'Y')).' ',
			' '.Stringz::currency($transaction['fee'] * $transaction['fiat_price'],($transaction['is_crypto'] == 'Y')).' ',
		));
	}
}

