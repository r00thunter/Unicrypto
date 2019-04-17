<?php
chdir('..');

$ajax = true;
include '../lib/common.php';

$currency1 = (array_key_exists($_REQUEST['currency'],$CFG->currencies)) ? $_REQUEST['currency'] : false;
$c_currency1 = (array_key_exists($_REQUEST['c_currency'],$CFG->currencies)) ? $_REQUEST['c_currency'] : false;
$notrades = (!empty($_REQUEST['notrades']));
$limit = (!empty($_REQUEST['get10'])) ? 10 : 5;
$user = (!empty($_REQUEST['user']));
$currency_info = $CFG->currencies[$currency1];
$c_currency_info = $CFG->currencies[$c_currency1];
$usd_field = 'usd_ask';


if (!$notrades) {
	API::add('Transactions','get',array(false,false,5,$c_currency1,$currency1));
	API::add('Stats','getBTCTraded',array($c_currency1));
}
elseif (empty($_REQUEST['get10'])) {
	$limit = (!$user) ? 30 : false;
}

if (!empty($_REQUEST['last_price']) && $notrades) {
	API::add('Transactions','get',array(false,false,1,$c_currency1,$currency1));
	API::add('Stats','getCurrent',array($c_currency1,$currency1));
	if ($currency1)
		API::add('User','getAvailable');
}
// $user_available = $query['User']['getAvailable']['results'][0];
// echo "<pre>"; print_r($user_available); exit;
API::add('Orders','get',array(false,false,$limit,$c_currency1,$currency1,$user,false,1,false,false,$user));
API::add('Orders','get',array(false,false,$limit,$c_currency1,$currency1,$user,false,false,false,1,$user));
$query = API::send();

$return['asks'][] = $query['Orders']['get']['results'][1];
$return['bids'][] = $query['Orders']['get']['results'][0];

if (!$notrades) {
	$return['transactions'][] = $query['Transactions']['get']['results'][0];
	$return['btc_traded'] = $query['Stats']['getBTCTraded']['results'][0][0]['total_btc_traded'];
}

if (!empty($_REQUEST['last_price'])) {
	$return['last_price'] = $query['Transactions']['get']['results'][0][0]['btc_price'];
	$return['last_price_curr'] = ($query['Transactions']['get']['results'][0][0]['currency'] == $currency_info['id']) ? '' : (($query['Transactions']['get']['results'][0][0]['currency1'] == $currency_info['id']) ? '' : ' ('.$CFG->currencies[$query['Transactions']['get']['results'][0][0]['currency1']]['currency'].')');
	$return['fa_symbol'] = $currency_info['fa_symbol'];
	$return['last_trans_color'] = ($query['Transactions']['get']['results'][0][0]['maker_type'] == 'sell') ? 'price-green' : 'price-red';
	
	if ($currency1) {
		$return['available_fiat'] = (!empty($query['User']['getAvailable']['results'][0][$currency_info['currency']])) ? Stringz::currency($query['User']['getAvailable']['results'][0][$currency_info['currency']],($currency_info['is_crypto'] == 'Y')) : '0';
		$return['available_btc'] = (!empty($query['User']['getAvailable']['results'][0][$c_currency_info['currency']])) ? Stringz::currency($query['User']['getAvailable']['results'][0][$c_currency_info['currency']],true) : '0';
	}
	
	if ($CFG->currencies) {
		foreach ($CFG->currencies as $key => $currency) {
			if (is_numeric($key))
				continue;
	
			$last_price = Stringz::currency($return['last_price'] * ((empty($currency_info) || $currency_info['currency'] == 'USD') ? 1/$currency[$usd_field] : $currency_info[$usd_field] / $currency[$usd_field]),($currency_info['is_crypto'] == 'Y'));
			$return['last_price_cnv'][$currency['currency']] = $last_price;
		}
	}
}

echo json_encode($return);