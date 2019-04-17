<?php
chdir('..');

if (empty($_REQUEST['action']) || ($_REQUEST['action'] != 'indicators' && $_REQUEST['action'] != 'chat_setting'))
	$ajax = true;

include '../lib/common.php';

$action = $_REQUEST['action'];
if ($action == 'indicators') {
	if (isset($_REQUEST['sma']) && $_REQUEST['sma'] == 'true')
		$_SESSION['sma'] = 1;
	else if (isset($_REQUEST['sma']) && $_REQUEST['sma'] != 'true')
		$_SESSION['sma'] = false;
	if (!empty($_REQUEST['sma1']))
		$_SESSION['sma1'] = preg_replace("/[^0-9]/", "",$_SESSION['sma1']);
	if (!empty($_REQUEST['sma2']))
		$_SESSION['sma2'] = preg_replace("/[^0-9]/", "",$_SESSION['sma2']);
	if (isset($_REQUEST['ema']) && $_REQUEST['ema'] == 'true')
		$_SESSION['ema'] = true;
	else if (isset($_REQUEST['ema']) && $_REQUEST['ema'] != 'true')
		$_SESSION['ema'] = false;
	if (!empty($_REQUEST['ema1']))
		$_SESSION['ema1'] = preg_replace("/[^0-9]/", "",$_SESSION['ema1']);
	if (!empty($_REQUEST['ema2']))
		$_SESSION['ema2'] = preg_replace("/[^0-9]/", "",$_SESSION['ema2']);

	exit;
}
else if ($action == 'chat_setting') {
	$_SESSION['chat_height'] = $_REQUEST['height'];
}
else if ($action == 'order_book') {
	$currency1 = (!empty($CFG->currencies[strtoupper($_REQUEST['currency'])])) ? strtoupper($_REQUEST['currency']) : false;
	$c_currency1 = (!empty($CFG->currencies[strtoupper($_REQUEST['c_currency'])])) ? strtoupper($_REQUEST['c_currency']) : false;
	
	API::add('Orders','get',array(false,false,false,$c_currency1,$currency1,false,false,1));
	API::add('Orders','get',array(false,false,false,$c_currency1,$currency1,false,false,false,false,1));
	$query = API::send();
	$bids = $query['Orders']['get']['results'][0];
	$asks = $query['Orders']['get']['results'][1];
	
	$bid_range = $max_bid - $min_bid;
	$ask_range = $max_ask - $min_ask;
	$c_bids = count($bids);
	$c_asks = count($asks);
	$lower_range = ($bid_range < $ask_range) ? $bid_range : $ask_range;
	$vars = array('bids'=>array(),'asks'=>array());
	
	if ($bids) {
		$cum_btc = 0;
		foreach ($bids as $bid) {
			if ($max_bid && $c_asks > 1 && (($max_bid - $bid['btc_price']) >  $lower_range))
				continue;
	
			$cum_btc += $bid['btc'];
			$vars['bids'][] = array($bid['btc_price'],$cum_btc);
		}
	
		if ($max_bid && $c_asks > 1)
			$vars['bids'][] = array(($max_bid - $lower_range),$cum_btc);
	
	}
	if ($asks) {
		$cum_btc = 0;
		foreach ($asks as $ask) {
			if ($min_ask && $c_bids > 1 && (($ask['btc_price'] - $min_ask) >  $lower_range))
				continue;
	
			$cum_btc += $ask['btc'];
			$vars['asks'][] = array($ask['btc_price'],$cum_btc);
		}
	
		if ($min_ask && $c_bids > 1)
			$vars['asks'][] = array(($min_ask + $lower_range),$cum_btc);
	}
	
	echo json_encode($vars);
	exit;
}

$timeframe1 = (!empty($_REQUEST['timeframe'])) ? preg_replace("/[^0-9a-zA-Z]/", "",$_REQUEST['timeframe']) : false;
$timeframe2 = (!empty($_REQUEST['timeframe1'])) ? preg_replace("/[^0-9a-zA-Z]/", "",$_REQUEST['timeframe1']) : false;
$currency1 = (!empty($CFG->currencies[$_REQUEST['currency']])) ? $_REQUEST['currency'] : false;
$c_currency1 = (!empty($CFG->currencies[$_REQUEST['c_currency']])) ? $_REQUEST['c_currency'] : false;
$first = (!empty($_REQUEST['first'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['first']) : false;
$last = (!empty($_REQUEST['last'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['last']) : false;
$_SESSION['timeframe'] = $timeframe1;

if ($action != 'more')
	API::add('Stats','getHistorical',array($timeframe2,$c_currency1,$currency1));

API::add('Transactions','candlesticks',array($timeframe1,$c_currency1,$currency1,false,$first,$last));
$query = API::send();

if ($action != 'more') {
	$stats = $query['Stats']['getHistorical']['results'][0];
	$vars = array();
	if ($stats) {
		foreach ($stats as $row) {
			$d = $row['date'];
			$temp_date = date("d",strtotime($d));
			$vars[] = '['.$temp_date.','.$row['price'].']';
		}
	}
	$hist = '['.implode(',', $vars).']';
}
else {
	$hist = '[]';
}

$first_id = 0;
$last_id = 0;

$data = $query['Transactions']['candlesticks']['results'][0];
$vars = array();
$datas = array();
if ($data) {
	$c = count($data) - 1;
	$first_id = ($data[0]['first_id']) ? $data[0]['first_id'] : $data[0]['id'];
	$last_id = ($data[0]['last_id']) ? $data[0]['last_id'] : $data[$c]['id'];
	
	foreach ($data as $key => $row) {
		if (!($row['t'] > 0) || $key == 's_final' || $key == 'e_final')
			continue;
		$d = date("Y-m-d",strtotime($row['t']));
		$vars[] = '['.$d.','.$row['price'].','.$row['vol'].','.$row['id'].']';
		$datas[] = '['.$d.','.$row['price'].']';
	}
}
$candles = '['.implode(',', $vars).']';
$data = '['.implode(',', $datas).']';

$first_id = (!$first_id) ? '0' : $first_id;
$last_id = (!$last_id) ? '0' : $last_id;

$re = array("data"=>$hist);
echo json_encode($re); exit;
echo '{"history":'.$hist.',"candles":'.$candles.',"data":'.$data.',"first_id":'.$first_id.',"last_id":'.$last_id.'}';