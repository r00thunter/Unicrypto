<?php 
include '../lib/common.php';

$CFG->public_api = true;
$post = ($_SERVER['REQUEST_METHOD'] == 'POST');
$params_json = file_get_contents('php://input');

// check if params sent as payload or http params
if (!empty($params_json)) {
	$decoded = json_decode($params_json,1);
	
	if (!empty($decoded) && is_array($decoded))
		$_POST = $decoded;
	
	if (!empty($_REQUEST) && is_array($_REQUEST) && !empty($_POST) && is_array($_POST))
		$_REQUEST = array_merge($_REQUEST,$_POST);
	elseif (!empty($_POST))
		$_REQUEST = $_POST;
}
else {
	$params_json = json_encode($_POST,JSON_NUMERIC_CHECK);
}

$main = Currencies::getMain();
$api_key1 = (!empty($_POST['api_key'])) ? preg_replace("/[^0-9a-zA-Z]/","",$_POST['api_key']) : false;
$api_signature1 = (!empty($_POST['signature'])) ? preg_replace("/[^0-9a-zA-Z]/","",$_POST['signature']) : false;
$nonce1 = (!empty($_POST['nonce'])) ? preg_replace("/[^0-9]/","",$_POST['nonce']) : false;
$CFG->language = (!empty($_POST['lang'])) ? preg_replace("/[^a-z]/","",$_POST['lang']) : 'en';
$currency1 = (!empty($_REQUEST['currency'])) ? preg_replace("/[^a-zA-Z0-9]/","",$_REQUEST['currency']) : false;
$c_currency1 = (!empty($_REQUEST['market'])) ? preg_replace("/[^a-zA-Z0-9]/","",$_REQUEST['market']) : false;
$currency_info = array('id'=>false);
$c_currency_info = array('id'=>false);
$endpoint = $_REQUEST['endpoint'];

$invalid_signature = false;
$invalid_currency = false;
$invalid_c_currency = false;

// check if API key/signature received
if ($api_key1 && (strlen($api_key1) != 16 || strlen($api_signature1) != 64)) {
	$return['errors'][] = array('message'=>'Invalid API key or signature.','code'=>'AUTH_INVALID_KEY');
	$invalid_signature = true;
}
elseif ($api_key1 && $api_signature1) {
	API::add('APIKeys','hasPermission',array($api_key1));
	$query = API::send($nonce1);
	$permissions = $query['APIKeys']['hasPermission']['results'][0];
}

// check if currency is supported
if ($currency1 && (strtolower($currency1) != 'all' && $endpoint == 'stats') && (!is_array($CFG->currencies[strtoupper($currency1)]))) {
	$return['errors'][] = array('message'=>'Invalid currency.','code'=>'INVALID_CURRENCY');
	$invalid_currency = true;
}
else {
	$currency_info = $CFG->currencies[strtoupper($currency1)];
}

if ($c_currency1 && (strtolower($c_currency1) != 'all' && $endpoint == 'stats') && (!is_array($CFG->currencies[strtoupper($c_currency1)]))) {
	$return['errors'][] = array('message'=>'Invalid market.','code'=>'INVALID_MARKET');
	$invalid_c_currency = true;
}
else {
	$c_currency_info = $CFG->currencies[strtoupper($c_currency1)];
}

if ($endpoint == 'stats') {
	if (!$invalid_currency && !$invalid_c_currency) {
		API::add('Stats','getCurrent',array((strtolower($c_currency1) == 'all' ? 'all' : $c_currency_info['id']),(strtolower($currency1) == 'all' ? 'all' : $currency_info['id'])));
		$query = API::send();
		
		$all = false;
		if (empty($query['Stats']['getCurrent']['results'][0]['all'])) {
			$result[] = $query['Stats']['getCurrent']['results'][0];
			$all = false;
		}
		else { 
			unset($query['Stats']['getCurrent']['results'][0]['all']);
			$result = $query['Stats']['getCurrent']['results'][0];
			$all = true;
		}
		
		if (is_array($result)) {
			foreach ($result as $key => $stats) {
				if (!is_array($stats))
					continue;
				
				$result[$key]['market_cap'] = (!empty($stats['market_cap'])) ? $stats['market_cap'] : 0;
				$result[$key]['global_units'] = (!empty($stats['total_btc'])) ? $stats['total_btc'] : 0;
				$result[$key]['global_volume'] = (!empty($stats['trade_volume'])) ? $stats['trade_volume'] : 0;
				$result[$key]['24h_volume'] = (!empty($stats['btc_24h'])) ? $stats['btc_24h'] : 0;
				$result[$key]['24h_volume_buy'] = (!empty($stats['btc_24h_buy'])) ? $stats['btc_24h_buy'] : 0;
				$result[$key]['24h_volume_sell'] = (!empty($stats['btc_24h_sell'])) ? $stats['btc_24h_sell'] : 0;
				$result[$key]['1h_volume'] = (!empty($stats['btc_1h'])) ? $stats['btc_1h'] : 0;
				$result[$key]['1h_volume_buy'] = (!empty($stats['btc_1h_buy'])) ? $stats['btc_1h_buy'] : 0;
				$result[$key]['1h_volume_sell'] = (!empty($stats['btc_1h_sell'])) ? $stats['btc_1h_sell'] : 0;
				$result[$key]['currency'] = $stats['request_currency'];
				unset($result[$key]['total_btc_traded']);
				unset($result[$key]['total_btc']);
				unset($result[$key]['global_btc']);
				unset($result[$key]['trade_volume']);
				unset($result[$key]['btc_24h']);
				unset($result[$key]['btc_24h_buy']);
				unset($result[$key]['btc_24h_sell']);
				unset($result[$key]['btc_1h']);
				unset($result[$key]['btc_1h_buy']);
				unset($result[$key]['btc_1h_sell']);
				unset($result[$key]['request_currency']);
			}
			
			if ($all)
				$return['stats'] = $result;
			else
				$return['stats'] = $result[0];
		}
		else
			$return['stats'] = array();
	}
}
elseif ($endpoint == 'historical-prices') {
	if (!$invalid_currency && !$invalid_c_currency) {
		// timeframe values: 1mon, 3mon, 6mon, ytd, 1year
		$timeframe_values = array('1mon','3mon','6mon','ytd','1year');
		$timeframe1 = preg_replace("/[^0-9a-zA-Z]/","",$_REQUEST['timeframe']);
		$timeframe1 = (!$timeframe1 || !in_array($timeframe1,$timeframe_values)) ? '1mon' : $timeframe1;
		
		API::add('Stats','getHistorical',array(strtolower($timeframe1),$c_currency_info['id'],$currency_info['id'],1));
		$query = API::send();
		
		if (is_array($query['Stats']['getHistorical']['results'][0])) {
			$return['historical-prices']['market'] = ($c_currency_info['id']) ? $c_currency_info['currency'] : $CFG->currencies[$main['crypto']]['currency'];
			$return['historical-prices']['currency'] = ($currency_info['id']) ? $currency_info['currency'] : $CFG->currencies[$main['fiat']]['currency'];
			$return['historical-prices']['data'] = $query['Stats']['getHistorical']['results'][0];
		}
		else
			$return['historical-prices'] = array();
	}
}
elseif ($endpoint == 'order-book') {
	if (!$invalid_currency && !$invalid_c_currency) {
		$limit1 = (!empty($_REQUEST['limit'])) ? preg_replace("/[^0-9]/","",$_REQUEST['limit']) : 50;
		$limit1 = ($limit1 > 100) ? 100 : $limit1;
		
		API::add('Orders','get',array(false,false,$limit1,$c_currency_info['id'],$currency_info['id'],false,false,1,false,false,false,false,1));
		API::add('Orders','get',array(false,false,$limit1,$c_currency_info['id'],$currency_info['id'],false,false,false,false,1,false,false,1));
		$query = API::send();
		
		$return['order-book']['market'] = ($c_currency_info['id']) ? $c_currency_info['currency'] : $CFG->currencies[$main['crypto']]['currency'];
		$return['order-book']['currency'] = ($currency_info['id']) ? $currency_info['currency'] : $CFG->currencies[$main['fiat']]['currency'];
		$return['order-book']['bid'] = ($query['Orders']['get']['results'][0]) ? $query['Orders']['get']['results'][0] : array();
		$return['order-book']['ask'] = ($query['Orders']['get']['results'][1]) ? $query['Orders']['get']['results'][1] : array();
	}
}
elseif ($endpoint == 'transactions') {
	// currency filters transactions involving that particular currency
	if (!$invalid_currency && !$invalid_c_currency) {
		$limit1 = (!empty($_REQUEST['limit'])) ? preg_replace("/[^0-9]/","",$_REQUEST['limit']) : false;
		$limit1 = (!$limit1) ? 10 : $limit1;
		
		API::add('Transactions','get',array(false,false,$limit1,$c_currency_info['id'],$currency_info['id'],false,false,false,false,false,1));
		$query = API::send();
		$return['transactions']['market'] = ($c_currency_info['id']) ? $c_currency_info['currency'] : 'ALL';
		$return['transactions']['currency'] = ($currency_info['id']) ? $currency_info['currency'] : 'ORIGINAL';
		$return['transactions']['data'] = ($query['Transactions']['get']['results'][0]) ? $query['Transactions']['get']['results'][0] : array();
	}
}
elseif ($endpoint == 'balances-and-info') {
	if ($post) {
		if (!$invalid_signature && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_view'] == 'Y') {
				API::add('User','getBalancesAndInfo');
				API::apiKey($api_key1);
				API::apiSignature($api_signature1,$params_json);
				API::apiUpdateNonce();
				$query = API::send($nonce1);
				

				if (empty($query['error'])) {
					$return['balances-and-info'] = $query['User']['getBalancesAndInfo']['results'][0];
					$return['balances-and-info'][strtolower($CFG->currencies[$main['fiat']]['currency']).'_volume'] = ($return['balances-and-info']['usd_volume']) ? $return['balances-and-info']['usd_volume'] : 0;
					$return['balances-and-info']['exchange_'.strtolower($CFG->currencies[$main['crypto']]['currency']).'_volume'] = ($return['balances-and-info']['global_btc_volume']) ? $return['balances-and-info']['global_btc_volume'] : 0;
					unset($return['balances-and-info']['global_btc_volume']);
					if ($CFG->currencies[$main['fiat']]['currency'] != 'USD')
						unset($return['balances-and-info']['usd_volume']);
				}
				else
					$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
	else
		$return['errors'][] = array('message'=>'Invalid HTTP method.','code'=>'AUTH_INVALID_HTTP_METHOD');
}
elseif ($endpoint == 'open-orders') {
	if ($post) {
		if (!$invalid_signature && !$invalid_currency && !$invalid_c_currency && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_view'] == 'Y') {
				// currency filters by native currency
				API::add('Orders','get',array(false,false,false,$c_currency_info['id'],$currency_info['id'],1,false,1,false,false,1,1));
				API::add('Orders','get',array(false,false,false,$c_currency_info['id'],$currency_info['id'],1,false,false,false,1,1,1));
				API::apiKey($api_key1);
				API::apiSignature($api_signature1,$params_json);
				API::apiUpdateNonce();
				$query = API::send($nonce1);
				
				if (empty($query['error'])) {
					$return['open-orders']['market'] = ($c_currency_info['id']) ? $c_currency_info['currency'] : 'ALL';
					$return['open-orders']['currency'] = ($currency_info['id']) ? $currency_info['currency'] : 'ORIGINAL';
					$return['open-orders']['bid'] = ($query['Orders']['get']['results'][0]) ? $query['Orders']['get']['results'][0] : array();
					$return['open-orders']['ask'] = ($query['Orders']['get']['results'][1]) ? $query['Orders']['get']['results'][1] : array();
				}
				else
					$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature && !$invalid_currency)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
	else
		$return['errors'][] = array('message'=>'Invalid HTTP method.','code'=>'AUTH_INVALID_HTTP_METHOD');
}
elseif ($endpoint == 'user-transactions') {
	if ($post) {
		if (!$invalid_signature && !$invalid_currency && !$invalid_c_currency && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_view'] == 'Y') {
				// currency filters by currency
				// type can be 'buy' or 'sell'
				$limit1 = (!empty($_REQUEST['limit'])) ? preg_replace("/[^0-9]/","",$_REQUEST['limit']) : false;
				$limit1 = (!$limit1) ? 10 : $limit1;
				$type1 = (!empty($_REQUEST['side'])) ? preg_replace("/[^a-zA-Z]/","",$_REQUEST['side']) : false;
				
				API::add('Transactions','get',array(false,false,$limit1,$c_currency1,$currency1,1,false,strtolower($type1),false,false,1));
				API::apiKey($api_key1);
				API::apiSignature($api_signature1,$params_json);
				API::apiUpdateNonce();
				$query = API::send($nonce1);
				
				if (empty($query['error'])) {
					$return['user-transactions']['market'] = ($c_currency_info['id']) ? $c_currency_info['currency'] : 'ALL';
					$return['user-transactions']['currency'] = ($currency_info['id']) ? $currency_info['currency'] : 'ALL';
					$return['user-transactions']['data'] = ($query['Transactions']['get']['results'][0]) ? $query['Transactions']['get']['results'][0] : array();
				}
				else
					$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature && !$invalid_currency)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
	else
		$return['errors'][] = array('message'=>'Invalid HTTP method.','code'=>'AUTH_INVALID_HTTP_METHOD');
}
elseif ($endpoint == 'btc-deposit-address/get' || $endpoint == 'crypto-deposit-address/get') {
	if ($post) {
		if (!$invalid_signature && $api_key1 && $nonce1 > 0) {
			if ($c_currency_info['id'] > 0 || $invalid_c_currency) {
				if ($permissions['p_view'] == 'Y') {
					$limit1 = (!empty($_REQUEST['limit'])) ? preg_replace("/[^0-9]/","",$_REQUEST['limit']) : false;
					$limit1 = (!$limit1) ? 10 : $limit1;
					
					API::add('BitcoinAddresses','get',array(false,false,$c_currency_info['id'],$limit1,false,false,false,1));
					API::apiKey($api_key1);
					API::apiSignature($api_signature1,$params_json);
					API::apiUpdateNonce();
					$query = API::send($nonce1);
					
					if (empty($query['error']))
						$return['btc-deposit-address-get'] = ($query['BitcoinAddresses']['get']['results'][0]) ? $query['BitcoinAddresses']['get']['results'][0] : array();
					else
						$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
				}
				else
					$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
			}
			else
				$return['errors'][] = array('message'=>'A valid cryptocurrency is required for the [market] parameter.','code'=>'CRYPTO_ADDRESS_INVALID_CRYPTOCURRENCY');
		}
		elseif (!$invalid_signature)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
}
elseif ($endpoint == 'btc-deposit-address/new' || $endpoint == 'crypto-deposit-address/new') {
	if ($post) {
		if ($c_currency_info['id'] > 0 || $invalid_c_currency) {
			if (!$invalid_signature && $api_key1 && $nonce1 > 0) {
				if ($permissions['p_view'] == 'Y') {
					API::add('BitcoinAddresses','get',array(false,$c_currency_info['id'],false,1,1));
					API::apiKey($api_key1);
					API::apiSignature($api_signature1,$params_json);
					$query = API::send($nonce1);
					$bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
		
					if (strtotime($bitcoin_addresses[0]['date']) >= strtotime('-1 day')) {
						$return['errors'][] = array('message'=>str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('bitcoin-addresses-too-soon')),'code'=>'BTC_ADDRESS_TOO_SOON');
						$error = true;
					}
					
					if (empty($error)) {
						API::add('BitcoinAddresses','getNew',array($c_currency_info['id'],1));
						API::apiKey($api_key1);
						API::apiSignature($api_signature1,$params_json);
						API::apiUpdateNonce();
						$query = API::send($nonce1);
						
						if (empty($query['error']))
							$return['crypto-deposit-address-new']['address'] = $query['BitcoinAddresses']['getNew']['results'][0];
						else
							$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
					}
				}
				else
					$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
			}
			elseif (!$invalid_signature)
				$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
		}
		else {
			$return['errors'][] = array('message'=>'A valid cryptocurrency is required for the [market] parameter.','code'=>'BTC_ADDRESS_INVALID_CRYPTOCURRENCY');
		}
	}
	else
		$return['errors'][] = array('message'=>'Invalid HTTP method.','code'=>'AUTH_INVALID_HTTP_METHOD');
}
elseif ($endpoint == 'deposits/get') {
	if ($post) {
		if (!$invalid_signature && !$invalid_currency && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_view'] == 'Y') {
				// status can be 'pending' or 'completed'
				$limit1 = (!empty($_REQUEST['limit'])) ? preg_replace("/[^0-9]/","",$_REQUEST['limit']) : false;
				$limit1 = (!$limit1) ? 10 : $limit1;
				$status1 = (!empty($_REQUEST['status'])) ? strtolower(preg_replace("/[^a-zA-Z]/","",$_REQUEST['status'])) : false;
				
				if ($status1 && $status1 != 'pending' && $status1 != 'completed' && $status1 != 'cancelled') {
					$return['errors'][] = array('message'=>'Invalid status.','code'=>'DEPOSIT_INVALID_STATUS');
					$error = true;
				}
				
				if (empty($error)) {
					API::add('Requests','get',array(false,false,$limit1,false,$currency_info['id'],$status1,1));
					API::apiKey($api_key1);
					API::apiSignature($api_signature1,$params_json);
					API::apiUpdateNonce();
					$query = API::send($nonce1);
					
					if (empty($query['error']))
						$return['deposits'] = ($query['Requests']['get']['results'][0]) ? $query['Requests']['get']['results'][0] : array();
					else
						$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
				}
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature && !$invalid_currency)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
}
elseif ($endpoint == 'withdrawals/get') {
	if ($post) {
		if (!$invalid_signature && !$invalid_currency && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_view'] == 'Y') {
				// status can be 'pending' or 'completed'
				$limit1 = (!empty($_REQUEST['limit'])) ? preg_replace("/[^0-9]/","",$_REQUEST['limit']) : false;
				$limit1 = (!$limit1) ? 10 : $limit1;
				$status1 = (!empty($_REQUEST['status'])) ? strtolower(preg_replace("/[^a-zA-Z]/","",$_REQUEST['status'])) : false;
				
				if ($status1 && ($status1 != 'pending' && $status1 != 'completed' && $status1 != 'cancelled')) {
					$return['errors'][] = array('message'=>'Invalid status.','code'=>'DEPOSIT_INVALID_STATUS');
					$error = true;
				}
				
				API::add('Requests','get',array(false,false,$limit1,1,$currency_info['id'],$status1,1));
				API::apiKey($api_key1);
				API::apiSignature($api_signature1,$params_json);
				API::apiUpdateNonce();
				$query = API::send($nonce1);
					
				if (empty($query['error']))
					$return['withdrawals'] = ($query['Requests']['get']['results'][0]) ? $query['Requests']['get']['results'][0] : array();
				else
					$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature && !$invalid_currency)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
	else
		$return['errors'][] = array('message'=>'Invalid HTTP method.','code'=>'AUTH_INVALID_HTTP_METHOD');
}
elseif ($endpoint == 'orders/new') {
	if ($post) {
		if (!$invalid_signature && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_orders'] == 'Y') {
				// new orders can be many or just one, can be in json or regular array (use http_build_query on all commands)
				// params: side(buy/sell), type(market,limit,stop), limit_price, stop_price, amount, currency
	
				$json = (!empty($_POST['orders'])) ? json_decode($_POST['orders'],1) : false;
				if (!empty($_POST['orders']) && is_array($_POST['orders']))
					$orders = $_POST['orders'];
				elseif (is_array($json))
					$orders = $json;
				else
					$orders[] = array('side'=>((!empty($_POST['side'])) ? $_POST['side'] : false),'type'=>((!empty($_POST['type'])) ? $_POST['type'] : false),'market'=>$c_currency_info['id'],'currency'=>$currency_info['id'],'limit_price'=>((!empty($_POST['limit_price'])) ? $_POST['limit_price'] : false),'stop_price'=>((!empty($_POST['stop_price'])) ? $_POST['stop_price'] : false),'amount'=>((!empty($_POST['amount'])) ? $_POST['amount'] : false));
				
				if (is_array($orders)) {
					$i = 1;
					foreach ($orders as $order) {
						$order['side'] = (!empty($order['side'])) ? strtolower(preg_replace("/[^a-zA-Z]/","",$order['side'])) : false;
						$order['type'] = (!empty($order['type'])) ? strtolower(preg_replace("/[^a-zA-Z]/","",$order['type'])) : false;
						$order['market'] = (!empty($order['market'])) ? $CFG->currencies[strtoupper(preg_replace("/[^a-zA-Z0-9]/","",$order['market']))]['id'] : false;
						$order['currency'] = (!empty($order['currency'])) ? $CFG->currencies[strtoupper(preg_replace("/[^a-zA-Z0-9]/","",$order['currency']))]['id'] : false;
						$order['limit_price'] = (!empty($order['limit_price']) && $order['limit_price'] > 0) ? number_format(preg_replace("/[^0-9.]/", "",$order['limit_price']),8,'.','') : false;
						$order['stop_price'] = (!empty($order['type']) && $order['type'] == 'stop' && $order['stop_price'] > 0) ? number_format(preg_replace("/[^0-9.]/", "",$order['stop_price']),8,'.','') : false;
						$order['amount'] = (!empty($order['amount']) && $order['amount'] > 0) ? number_format(preg_replace("/[^0-9.]/", "",$order['amount']),8,'.','') : false;
						
						// preliminary validation
						if ($CFG->trading_status == 'suspended') {
							$return['errors'][] = array('message'=>Lang::string('buy-trading-disabled'),'code'=>'TRADING_SUSPENDED');
							break;
						}
						elseif ($order['side'] != 'buy' && $order['side'] != 'sell') {
							$return['errors'][] = array('message'=>'Invalid order side (must be buy or sell).','code'=>'ORDER_INVALID_SIDE');
							continue;
						}
						elseif ($order['type'] != 'market' && $order['type'] != 'limit' && $order['type'] != 'stop') {
							$return['errors'][] = array('message'=>'Invalid order type (must be market, limit or stop).','code'=>'ORDER_INVALID_TYPE');
							continue;
						}
						elseif (!$CFG->currencies[$order['market']]) {
							$return['errors'][] = array('message'=>'Invalid market.','code'=>'INVALID_MARKET');
							continue;
						}
						elseif (!$CFG->currencies[$order['currency']]) {
							$return['errors'][] = array('message'=>'Invalid currency.','code'=>'INVALID_CURRENCY');
							continue;
						}
						elseif (!($order['amount'] > 0)) {
							$return['errors'][] = array('message'=>'Amount to '.($order['side'] == 'buy' ? 'buy' : 'sell').' must be greater than zero.','code'=>'ORDER_INVALID_AMOUNT');
							continue;
						}
						elseif ($order['type'] == 'limit' && !($order['limit_price'] > 0)) {
							$return['errors'][] = array('message'=>'No limit price provided.','code'=>'ORDER_INVALID_LIMIT_PRICE');
							continue;
						}
						elseif ($order['type'] == 'stop' && !($order['stop_price'] > 0)) {
							$return['errors'][] = array('message'=>Lang::string('buy-errors-no-stop'),'code'=>'ORDER_INVALID_STOP_PRICE');
							continue;
						}
						
						API::add('Orders','executeOrder',array(($order['side'] == 'buy'),$order['limit_price'],$order['amount'],$order['market'],$order['currency'],false,($order['type'] == 'market'),false,false,false,$order['stop_price'],false,1));
						API::apiKey($api_key1);
						API::apiSignature($api_signature1,$params_json);
						
						if (count($orders) == $i)
							API::apiUpdateNonce();
						
						$query = API::send($nonce1);
						$result = $query['Orders']['executeOrder']['results'][0];
						
						if ($result && empty($query['error'])) {
							unset($result['order_info']['comp_orig_prices']);
							unset($result['order_info']['replaced']);
							unset($result['edit_order']);
							unset($result['executed']);
							
							if ($order['limit_price'] > 0 && $order['stop_price'] > 0 && $order['type'] == 'stop')
								$result['order_info']['oco'] = true;
							
							$return['orders-new'][] = ($result) ? $result : array();
						}
						else
							$return['errors'][] = $query['error'];
						
						$i++;
					}
				}
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature && !$invalid_currency)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
	else
		$return['errors'][] = array('message'=>'Invalid HTTP method.','code'=>'AUTH_INVALID_HTTP_METHOD');
}
elseif ($endpoint == 'orders/edit') {
	// will return "replaced" as the id of the order that was replaced
	if ($post) {
		if (!$invalid_signature && !$invalid_currency && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_orders'] == 'Y') {
				$json = (!empty($_POST['orders'])) ? json_decode($_POST['orders'],1) : false;
				if (!empty($_POST['orders']) && is_array($_POST['orders']))
					$orders = $_POST['orders'];
				elseif (is_array($json))
					$orders = $json;
				else
					$orders[] = array('id'=>((!empty($_POST['id'])) ? $_POST['id'] : false),'type'=>((!empty($_POST['type'])) ? $_POST['type'] : false),'limit_price'=>((!empty($_POST['limit_price'])) ? $_POST['limit_price'] : false),'stop_price'=>((!empty($_POST['stop_price'])) ? $_POST['stop_price'] : false),'amount'=>((!empty($_POST['amount'])) ? $_POST['amount'] : false));
					
				if (is_array($orders)) {
					$i = 1;
					foreach ($orders as $order) {
						$order['id'] = (!empty($order['id'])) ? preg_replace("/[^0-9]/", "",$order['id']) : false;
						$order['type'] = (!empty($order['type'])) ? strtolower(preg_replace("/[^a-zA-Z]/","",$order['type'])) : false;
						$order['limit_price'] = (!empty($order['limit_price']) && $order['limit_price'] > 0) ? number_format(preg_replace("/[^0-9.]/", "",$order['limit_price']),8,'.','') : false;
						$order['stop_price'] = (!empty($order['stop_price']) && $order['stop_price'] > 0) ? number_format(preg_replace("/[^0-9.]/", "",$order['stop_price']),8,'.','') : false;
						$order['amount'] = (!empty($order['amount']) && $order['amount'] > 0) ? number_format(preg_replace("/[^0-9.]/", "",$order['amount']),8,'.','') : false;
						
						// preliminary validation
						if ($CFG->trading_status == 'suspended') {
							$return['errors'][] = array('message'=>Lang::string('buy-trading-disabled'),'code'=>'TRADING_SUSPENDED');
							break;
						}
						elseif (empty($order['id']) || !($order['id'] > 0)) {
							$return['errors'][] = array('message'=>'Invalid order id.','code'=>'ORDER_INVALID_ID');
							continue;
						}
						elseif ($order['type'] != 'market' && $order['type'] != 'limit' && $order['type'] != 'stop') {
							$return['errors'][] = array('message'=>'Invalid order type (must be market, limit or stop).','code'=>'ORDER_INVALID_TYPE');
							continue;
						}
						elseif (!($order['amount'] > 0)) {
							$return['errors'][] = array('message'=>'Amount to '.($order['side'] == 'buy' ? 'buy' : 'sell').' must be greater than zero.','code'=>'ORDER_INVALID_AMOUNT');
							continue;
						}
						elseif ($order['type'] == 'limit' && !($order['limit_price'] > 0)) {
							$return['errors'][] = array('message'=>'No limit price provided.','code'=>'ORDER_INVALID_LIMIT_PRICE');
							continue;
						}
						elseif ($order['type'] == 'stop' && !($order['stop_price'] > 0)) {
							$return['errors'][] = array('message'=>Lang::string('buy-errors-no-stop'),'code'=>'ORDER_INVALID_STOP_PRICE');
							continue;
						}
							
						API::add('Orders','executeOrder',array(false,$order['limit_price'],$order['amount'],false,false,false,($order['type'] == 'market'),$order['id'],false,false,$order['stop_price'],false,1));
						API::apiKey($api_key1);
						API::apiSignature($api_signature1,$params_json);
						
						if (count($orders) == $i)
							API::apiUpdateNonce();
						
						$query = API::send($nonce1);
						$result = $query['Orders']['executeOrder']['results'][0];
							
						if ($result && empty($query['error'])) {
							unset($result['order_info']['comp_orig_prices']);
							unset($result['new_order']);
							unset($result['executed']);
							
							if ($order['limit_price'] > 0 && $order['stop_price'] > 0 && $order['type'] == 'stop')
								$result['order_info']['oco'] = true;
							
							$return['orders-edit'][] = ($result) ? $result : array();
						}
						else
							$return['errors'][] = $query['error'];
							
						$i++;
					}
				}
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature && !$invalid_currency)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
	else
		$return['errors'][] = array('message'=>'Invalid HTTP method.','code'=>'AUTH_INVALID_HTTP_METHOD');
}
elseif ($endpoint == 'orders/cancel') {
	if ($post) {
		if (!$invalid_signature && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_orders'] == 'Y') {
				if (empty($_POST['all'])) {
					$json = (!empty($_POST['orders'])) ? json_decode($_POST['orders'],1) : false;
					if (!empty($_POST['orders']) && is_array($_POST['orders']))
						$orders = $_POST['orders'];
					elseif (is_array($json))
						$orders = $json;
					else
						$orders[] = array('id'=>((!empty($_POST['id'])) ? $_POST['id'] : false));
			
					if (is_array($orders)) {
						$i = 1;
						foreach ($orders as $order) {
							if ($CFG->trading_status == 'suspended') {
								$return['errors'][] = array('message'=>Lang::string('buy-trading-disabled'),'code'=>'TRADING_SUSPENDED');
								break;
							}
							
							$order['id'] = (!empty($order['id'])) ? preg_replace("/[^0-9]/", "",$order['id']) : false;
							API::add('Orders','delete',array(false,$order['id']));
							API::apiKey($api_key1);
							API::apiSignature($api_signature1,$params_json);
							
							if (count($orders) == $i)
								API::apiUpdateNonce();
							
							$query = API::send($nonce1);
							$result = $query['Orders']['delete']['results'][0];
							
							if (empty($result)) {
								$return['errors'][] = array('message'=>'Order not found.','code'=>'ORDER_NOT_FOUND');
								continue;
							}
							
							$return['orders-cancel'][] = ($result) ? $result : array();
							$i++;
						}
					}
				}
				else {
					API::add('Orders','deleteAll');
					API::apiKey($api_key1);
					API::apiSignature($api_signature1,$params_json);
					API::apiUpdateNonce();
					$query = API::send($nonce1);
					$return['orders-cancel'] = $query['Orders']['deleteAll']['results'][0];
				}
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
	else
		$return['errors'][] = array('message'=>'Invalid HTTP method.','code'=>'AUTH_INVALID_HTTP_METHOD');
}
elseif ($endpoint == 'orders/status') {
	if ($post) {
		if (!$invalid_signature && !$invalid_currency && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_view'] == 'Y') {
				$json = (!empty($_POST['orders'])) ? json_decode($_POST['orders'],1) : false;
				if (!empty($_POST['orders']) && is_array($_POST['orders']))
					$orders = $_POST['orders'];
				elseif (is_array($json))
					$orders = $json;
				else
					$orders[] = array('id'=>((!empty($_POST['id'])) ? $_POST['id'] : false));
				
				if (is_array($orders)) {
					$i = 1;
					foreach ($orders as $order) {
						$order['id'] = (!empty($order['id'])) ? preg_replace("/[^0-9]/", "",$order['id']) : false;
						
						API::add('Orders','getStatus',array($order['id'],1));
						API::apiKey($api_key1);
						API::apiSignature($api_signature1,$params_json);
						
						if (count($orders) == $i)
							API::apiUpdateNonce();
						
						$query = API::send($nonce1);
						$result = $query['Orders']['getStatus']['results'][0];
						
						if (!empty($query['error'])) {
							$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
							break;
						}
						
						if (!$result) {
							$return['errors'][] = array('message'=>'Order not found.','code'=>'ORDER_NOT_FOUND');
							continue;
						}
						
						$return['orders-status'][] = ($result) ? $result : array();
						$i++;
					}
				}
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature && !$invalid_currency)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
}
elseif ($endpoint == 'withdrawals/new') {
	if ($post) {
		if (!$invalid_signature && !$invalid_currency && $api_key1 && $nonce1 > 0) {
			if ($permissions['p_withdraw'] == 'Y') {
				$amount1 = (!empty($_POST['amount'])) ? preg_replace("/[^0-9.]/", "",$_POST['amount']) : 0;
				$address1 = (!empty($_POST['address'])) ? preg_replace("/[^\da-z]/i", "",$_POST['address']) : false;
				$account1 = (!empty($_POST['account_number'])) ? preg_replace("/[^0-9]/", "",$_POST['account_number']) : false;
				$amount1 = number_format($amount1,8,'.','');
				$error = false;
				
				if (!($currency_info['id'] > 0))
					$return['errors'][] = array('message'=>'A valid [currency] is required.','code'=>'WITHDRAW_INVALID_CURRENCY');
				elseif ($currency_info['is_crypto'] != 'Y' && $amount1 < 1)
					$return['errors'][] = array('message'=>Lang::string('withdraw-amount-one'),'code'=>'WITHDRAW_INVALID_AMOUNT');
				elseif ($currency_info['is_crypto'] == 'Y' && !$address1)
					$return['errors'][] = array('message'=>Lang::string('withdraw-address-invalid'),'code'=>'WITHDRAW_INVALID_ADDRESS');
				elseif ($currency_info['is_crypto'] != 'Y' && !$account1)
					$return['errors'][] = array('message'=>Lang::string('withdraw-no-account'),'code'=>'WITHDRAW_INVALID_ACCOUNT');
				else {				
					if ($currency_info['is_crypto'] == 'Y') {
						API::add('User','getAvailable');
						API::add('BitcoinAddresses','validateAddress',array($address1));
						API::add('Wallets','get',array($currency_info['id']));
						API::apiKey($api_key1);
						API::apiSignature($api_signature1,$params_json);
						$query = API::send($nonce1);
						$user_available = $query['User']['getAvailable']['results'][0];
						$wallet = $query['Wallet']['get']['results'][0];
						
						if ($CFG->withdrawals_status == 'suspended') {
							$return['errors'][] = array('message'=>Lang::string('withdrawal-suspended'),'code'=>'WITHDRAWALS_SUSPENDED');
							$error = true;
						}
						elseif ($amount1 < $wallet['bitcoin_sending_fee']) {
							$return['errors'][] = array('message'=>Lang::string('withdraw-amount-zero'),'code'=>'WITHDRAW_INVALID_AMOUNT');
						}
						elseif (!empty($query['error'])) {
							$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
							$error = true;
						}
						elseif ($amount1 > $user_available[$currency_info['currency']]) {
							$return['errors'][] = array('message'=>Lang::string('withdraw-too-much'),'code'=>'WITHDRAW_BALANCE_TOO_LOW');
							$error = true;
						}
						elseif (empty($query['BitcoinAddresses']['validateAddress']['results'][0])) {
							$return['errors'][] = array('message'=>Lang::string('withdraw-address-invalid'),'code'=>'WITHDRAW_INVALID_ADDRESS');
							$error = true;
						}
					}
					else {
						API::add('BankAccounts','getRecord',array(false,$account1));
						API::add('BankAccounts','get',array($currency_info['id']));
						API::add('User','getAvailable');
						API::apiKey($api_key1);
						API::apiSignature($api_signature1,$params_json);
						$query = API::send($nonce1);
						$bank_account = $query['BankAccounts']['getRecord']['results'][0];
						$bank_accounts = $query['BankAccounts']['get']['results'][0];
						$user_available = $query['User']['getAvailable']['results'][0];
						
						if ($CFG->withdrawals_status == 'suspended') {
							$return['errors'][] = array('message'=>Lang::string('withdrawal-suspended'),'code'=>'WITHDRAWALS_SUSPENDED');
							$error = true;
						}
						elseif (!empty($query['error'])) {
							$return['errors'][] = array('message'=>'Invalid authentication.','code'=>$query['error']);
							$error = true;
						}
						elseif (!is_array($bank_account)) {
							$return['errors'][] = array('message'=>Lang::string('withdraw-account-not-found'),'code'=>'WITHDRAW_INVALID_ACCOUNT');
							$error = true;
						}
						elseif (empty($bank_accounts[$bank_account['account_number']])) {
							$return['errors'][] = array('message'=>Lang::string('withdraw-account-not-found'),'code'=>'WITHDRAW_INVALID_ACCOUNT');
							$error = true;
						}
						elseif ($amount1 > $user_available[strtoupper($currency1)]) {
							$return['errors'][] = array('message'=>Lang::string('withdraw-too-much'),'code'=>'WITHDRAW_BALANCE_TOO_LOW');
							$error = true;
						}
					}
					
					if (!$error) {
						API::add('Requests','insert',array($currency_info['id'],$amount1,$address1,$account1));
						API::apiKey($api_key1);
						API::apiSignature($api_signature1,$params_json);
						API::apiUpdateNonce();
						$query = API::send($nonce1);
	
						$return['withdraw'] = ($query['Requests']['insert']['results'][0]) ? $query['Requests']['insert']['results'][0] : array();
					}
				}
			}
			else
				$return['errors'][] = array('message'=>'Not authorized.','code'=>'AUTH_NOT_AUTHORIZED');
		}
		elseif (!$invalid_signature && !$invalid_currency)
			$return['errors'][] = array('message'=>'Invalid authentication.','code'=>'AUTH_ERROR');
	}
}

if (!empty($return))
	echo json_encode($return,JSON_NUMERIC_CHECK);


?>