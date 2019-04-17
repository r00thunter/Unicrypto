<?php
chdir('..');

$ajax = true;
include '../lib/common.php';

$currency1 = (!empty($CFG->currencies[$_REQUEST['currency']])) ? $_REQUEST['currency'] : false;
$c_currency1 = (!empty($CFG->currencies[$_REQUEST['c_currency']])) ? $_REQUEST['c_currency'] : false;

foreach ($CFG->currencies as $key => $currency) {
	if (is_numeric($key) || $currency['is_crypto'] != 'Y')
		continue;

	API::add('Stats','getCurrent',array($currency['id'],$currency1));
}

API::add('Orders','getBidAsk',array($c_currency1,$currency1));
API::add('Stats','getCurrent',array($c_currency1,$currency1));
API::add('User','getAvailable');
$query = API::send();

$current_bid = $query['Orders']['getBidAsk']['results'][0]['bid'];
$current_ask = $query['Orders']['getBidAsk']['results'][0]['ask'];
$user_available = $query['User']['getAvailable']['results'][0];

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

$return['currency_info'] = $CFG->currencies[strtoupper($currency1)];
$return['current_bid'] = $current_bid;
$return['current_ask'] = $current_ask;
$return['available_btc'] = (!empty($user_available[$CFG->currencies[$c_currency1]['currency']])) ? Stringz::currency($user_available[$CFG->currencies[$c_currency1]['currency']],true) : 0;
$return['available_fiat'] = (!empty($user_available[$CFG->currencies[$currency1]['currency']])) ? Stringz::currency($user_available[$CFG->currencies[$currency1]['currency']],($CFG->currencies[$currency1]['is_crypto'] == 'Y')) : 0;
$return['stats'] = $stats;
$return['market_stats'] = $market_stats;

echo json_encode($return);
