#!/usr/bin/php
<?php
include 'common.php';

// WARNING
// This file is only to be run to pull price data from other exchanges before the exchange has it's own operations!
// Once the exchange is operating, it will generate it's own price data

echo date('Y-m-d H:i:s').' Beginning Historical Data processing...'.PHP_EOL;

$wallets = Wallets::get();
if (!$wallets) {
	echo 'Error: no wallets to process.'.PHP_EOL;
	exit;
}

if (!array_key_exists('BTC',$wallets)) {
	echo 'Error: Must have BTC data series to execute this function.'.PHP_EOL;
	exit;
}

$btc_data = array();
$btc_wallet = $wallets['BTC'];
unset($wallets['BTC']);
$wallets = array('BTC'=>$btc_wallet) + $wallets;
$currency = 'USD';
$exchange = 'BTCE';
// QUANDL HISTORICAL DATA
foreach ($wallets as $wallet) {
	if ($CFG->currencies[$wallet['c_currency']]['currency'] == 'BTC')
		$url = 'https://www.quandl.com/api/v3/datasets/BCHARTS/'.$exchange.$currency.'.csv?trim_start=2011-01-01';
	else
		$url = 'https://www.quandl.com/api/v3/datasets/CRYPTOCHART/'.$CFG->currencies[$wallet['c_currency']]['currency'].'.csv';
	
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
	curl_setopt($ch,CURLOPT_TIMEOUT,10);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	$data = curl_exec($ch);
	curl_close($ch);
	$data1 = explode("\n",$data);
	if ($data1 && count($data1)>5) {
		$i = 1;
		$c = count($data1);
		$rows = array();
		
		foreach ($data1 as $row) {
			$row1 = explode(',',$row);
			if ($i == 1 || ($i > 2 && (empty($row1[0]) || empty($row1[1]))) || $i==$c) {
				$i++;
				continue;
			}
			
			if ($CFG->currencies[$wallet['c_currency']]['currency'] == 'BTC') {
				$btc_data[$row1[0]] = $row1[1];
				$exchange_rate = $row1[1];
			}
			else if (!empty($btc_data[$row1[0]]))
				$exchange_rate = $row1[1] * $btc_data[$row1[0]];
			else
				continue;
			
			if ($i == 2) {
				db_update('currencies',$wallet['c_currency'],array('usd_ask'=>$exchange_rate,'usd_bid'=>$exchange_rate));
			}
			
			$rows[] = array('c_currency'=>$wallet['c_currency'],'date'=>'"'.$row1[0].'"','usd'=>$exchange_rate);
			$i++;
		}
		
		foreach ($rows as $row) {
			$rows1[] = '('.implode(',',$row).')';
		}
		$sql = 'INSERT INTO historical_data ('.implode(',',array_keys($rows[0])).') VALUES '.implode(',',$rows1).' ON DUPLICATE KEY UPDATE c_currency = VALUES(c_currency), date = VALUES(date), usd = VALUES(usd)';
		db_query($sql);
	}
}

echo 'done';
?>
