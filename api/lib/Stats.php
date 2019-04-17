<?php
class Stats {
	public static function getHistorical($timeframe='1mon',$c_currency,$currency,$public_api=false) {
		global $CFG;
		
		$main = Currencies::getMain();
		$currency = preg_replace("/[^0-9]/", "",$currency);
		$c_currency = preg_replace("/[^0-9]/", "",$c_currency);
		
		if (empty($CFG->currencies[strtoupper($currency)]))
			$currency_info = $CFG->currencies[$main['fiat']];
		else
			$currency_info = $CFG->currencies[strtoupper($currency)];
		
		if (empty($CFG->currencies[strtoupper($c_currency)]))
			$c_currency_info = $CFG->currencies[$main['crypto']];
		else
			$c_currency_info = $CFG->currencies[strtoupper($c_currency)];
		
		if (empty($currency_info) || empty($c_currency_info))
			return false;
		
		if ($timeframe == '1mon' || !$timeframe)
			$start = date('Y-m-d',strtotime('-1 month'));
		elseif ($timeframe == '3mon')
			$start = date('Y-m-d',strtotime('-3 month'));
		elseif ($timeframe == '6mon')
			$start = date('Y-m-d',strtotime('-6 month'));
		elseif ($timeframe == 'ytd')
			$start = date('Y').'-01-01';
		elseif ($timeframe == '1year')
			$start = date('Y-m-d',strtotime('-1 year'));
		
		if ($CFG->memcached) {
			$cached = $CFG->m->get('historical_'.$currency.'_'.$timeframe.(($public_api) ? '_api' : ''));
			if ($cached) {
				return $cached;
			}
		}
		
		$usd_ask = $currency_info['usd_ask'];
		if (!$usd_ask)
			return false;
		
		$sql = "SELECT ".((!$public_api) ? "(UNIX_TIMESTAMP(DATE(`date`)) * 1000) AS" : '')." `date`,ROUND((usd/$usd_ask),".($currency_info['is_crypto'] == 'Y' ? 8 : 2).") AS price FROM historical_data WHERE `date` >= '$start' AND c_currency = ".$c_currency_info['id']." GROUP BY `date` ORDER BY `date` ASC";
		$result = db_query_array($sql);
		if ($CFG->memcached)
			$CFG->m->set('historical_'.$currency.'_'.$timeframe.(($public_api) ? '_api' : ''),$result,3600);
		
		return $result;
	}
	
	public static function getCurrent($c_currency_id,$currency_id) {
		global $CFG;
		
		if (strtolower($c_currency_id) == 'all') {
			$all = array();
			foreach ($CFG->currencies as $key => $currency1) {
				if ($currency1['is_crypto'] != 'Y' || is_numeric($key))
					continue;
				
				if (strtolower($currency_id) == 'all') {
					foreach ($CFG->currencies as $key => $currency2) {
						if ($currency1['id'] == $currency2['id'] || is_numeric($key))
							continue;
						
						$all[$currency1['currency'].'-'.$currency2['currency']] = self::getCurrent($currency1['id'],$currency2['id']);
					}
				}
				else {
					$all[$currency1['currency']] = self::getCurrent($currency1['id'],$currency_id);
				}
			}
			
			$all['all'] = true;
			return $all;
		}
		
		$usd_info = $CFG->currencies['USD'];
		$main = Currencies::getMain();
		$usd_field = 'usd_ask';
		$currency_id = ($currency_id > 0) ? preg_replace("/[^0-9]/", "",$currency_id) : $CFG->currencies[$main['fiat']]['id'];
		$c_currency_id = ($c_currency_id > 0) ? preg_replace("/[^0-9]/", "",$c_currency_id) : $CFG->currencies[$main['crypto']]['id'];
		$currency_info = $CFG->currencies[$currency_id];
		$c_currency_info = $CFG->currencies[$c_currency_id];
		
		if (empty($currency_info) || empty($c_currency_info))
			return false;
		
		$wallet = Wallets::getWallet($c_currency_info['id']);
		if ($CFG->memcached) {
			$cached = $CFG->m->get('stats_'.$c_currency_info['currency'].'_'.$currency_info['currency']);
			if ($cached) {
				return $cached;
			}
		}
		
		$bid_ask = Orders::getBidAsk($c_currency_id,$currency_id);
		$bid = $bid_ask['bid'];
		$ask = $bid_ask['ask'];
		
		$price_str = '(CASE WHEN transactions.currency = '.$currency_info['id'].' THEN transactions.btc_price WHEN transactions.currency1 = '.$currency_info['id'].' THEN transactions.orig_btc_price ELSE (transactions.orig_btc_price * (CASE transactions.currency1 ';
		foreach ($CFG->currencies as $curr_id => $currency1) {
			if (is_numeric($curr_id) || $currency1['id'] == $c_currency_id || $currency1['id'] == $currency_id)
				continue;
		
			$conversion = (empty($currency_info) || $currency_info['currency'] == 'USD') ? $currency1[$usd_field] : $currency1[$usd_field] / $currency_info[$usd_field];
			$price_str .= ' WHEN '.$currency1['id'].' THEN '.$conversion.' ';
		}
		$price_str .= ' END)) END)';
		
		$sql = 'SELECT r2.btc_price AS btc_price2, r3.btc_price AS btc_price3, r2.last_transaction_type AS last_transaction_type2, r2.last_transaction_currency AS last_transaction_currency2, r3.last_transaction_currency AS last_transaction_currency3, r5.max, r5.min, wallets.btc_24h, wallets.btc_24h_s, wallets.btc_24h_b, wallets.btc_1h, wallets.btc_1h_s, wallets.btc_1h_b, wallets.global_btc, wallets.market_cap, wallets.trade_volume FROM wallets ';

		$sql_arr[] = "LEFT JOIN (SELECT IF(transactions.currency = $currency_id,transactions.btc_price,transactions.orig_btc_price) AS btc_price, IF(transactions.transaction_type = {$CFG->transactions_buy_id},'BUY','SELL') AS last_transaction_type, IF(transactions.currency != $currency_id AND transactions.currency1 != $currency_id,transactions.currency1,$currency_id) AS last_transaction_currency FROM transactions WHERE c_currency = ".$c_currency_info['id']." ".((!$CFG->cross_currency_trades) ? "AND transactions.currency = $currency_id" : '')." ORDER BY transactions.id DESC LIMIT 0,1) AS r2 ON (1)";
		$sql_arr[] = "LEFT JOIN (SELECT IF(transactions.currency = $currency_id,transactions.btc_price,transactions.orig_btc_price) AS btc_price, IF(transactions.currency != $currency_id AND transactions.currency1 != $currency_id,transactions.currency1,$currency_id) AS last_transaction_currency FROM transactions WHERE c_currency = ".$c_currency_info['id']." AND transactions.date < DATE_SUB(DATE_ADD(NOW(), INTERVAL ".((($CFG->timezone_offset)/60)/60)." HOUR), INTERVAL 1 DAY) ".((!$CFG->cross_currency_trades) ? "AND transactions.currency = $currency_id" : '')." ORDER BY transactions.id DESC LIMIT 0,1) AS r3  ON (1)";
		$sql_arr[] = "LEFT JOIN (SELECT MAX(".(($CFG->cross_currency_trades) ? "ROUND($price_str,8)" : 'transactions.btc_price').") AS `max`, MIN(".(($CFG->cross_currency_trades) ? "ROUND($price_str,8)" : 'transactions.btc_price').") AS `min` FROM transactions WHERE c_currency = ".$c_currency_info['id']." AND transactions.date >= CURDATE() ".((!$CFG->cross_currency_trades) ? "AND transactions.currency = $currency_id" : '')." LIMIT 0,1) AS r5 ON (1)";
		
		$sql .= implode(' ',$sql_arr).' WHERE wallets.c_currency = '.$c_currency_info['id'];
		$result = db_query_array($sql);
		
		if ($result[0]['btc_price2'])
			$result[0]['btc_price2'] = number_format(round($result[0]['btc_price2'] * (($currency_info['currency'] == 'USD') ? $CFG->currencies[$result[0]['last_transaction_currency2']][$usd_field] : $CFG->currencies[$result[0]['last_transaction_currency2']][$usd_field] / $currency_info[$usd_field]),8,PHP_ROUND_HALF_UP),8,'.','');
		if ($result[0]['btc_price3'])
			$result[0]['btc_price3'] = number_format(round($result[0]['btc_price3'] * (($currency_info['currency'] == 'USD') ? $CFG->currencies[$result[0]['last_transaction_currency3']][$usd_field] : $CFG->currencies[$result[0]['last_transaction_currency3']][$usd_field] / $currency_info[$usd_field]),8,PHP_ROUND_HALF_UP),8,'.','');
		
		$stats['market'] = $c_currency_info['currency'];
		$stats['request_currency'] = $currency_info['currency'];
		$stats['bid'] = $bid;
		$stats['ask'] = $ask;
		$stats['last_price'] = ($result[0]['btc_price2']) ? $result[0]['btc_price2'] : $ask;
		$stats['last_transaction_type'] = $result[0]['last_transaction_type2'];
		$stats['last_transaction_currency'] = !empty($CFG->currencies[$result[0]['last_transaction_currency2']]) ? $CFG->currencies[$result[0]['last_transaction_currency2']]['currency'] : null;
		$stats['daily_change'] = ($result[0]['btc_price3'] > 0 && $result[0]['btc_price2'] > 0) ? number_format(round($result[0]['btc_price2'] - $result[0]['btc_price3'],8,PHP_ROUND_HALF_UP),8,'.','') : '0';
		$stats['daily_change_percent'] = ($stats['last_price'] > 0) ? round(($stats['daily_change']/$stats['last_price']) * 100,2,PHP_ROUND_HALF_UP) : 0;
		$stats['max'] = ($result[0]['max'] > 0) ? $result[0]['max'] : $result[0]['btc_price2'];
		$stats['min'] = ($result[0]['min'] > 0) ? $result[0]['min'] : $result[0]['btc_price2'];
		$stats['open'] = ($result[0]['btc_price3'] > 0) ? $result[0]['btc_price3'] : $result[0]['btc_price2'];
		$stats['total_btc_traded'] = $result[0]['btc_24h'];
		$stats['total_btc'] = $result[0]['global_btc'];
		$stats['global_btc'] = $result[0]['global_btc'];
		$stats['market_cap'] = ($result[0]['market_cap'] * $CFG->currencies[$main['fiat']]['usd_ask'])/$currency_info['usd_ask'];
		$stats['trade_volume'] = number_format(($result[0]['trade_volume'] * $CFG->currencies[$main['fiat']]['usd_ask'])/$currency_info['usd_ask'],8,'.','');
		$stats['trade_volume'] = ($stats['trade_volume'] < $result[0]['btc_24h']) ? number_format(($result[0]['btc_24h']  * $c_currency_info['usd_ask'])/$currency_info['usd_ask'],8,'.','') : $stats['trade_volume'];
		$stats['btc_24h'] = number_format(($result[0]['btc_24h']  * $c_currency_info['usd_ask'])/$currency_info['usd_ask'],8,'.','');
		$stats['btc_24h_buy'] = number_format(($result[0]['btc_24h_b'] * $c_currency_info['usd_ask'])/$currency_info['usd_ask'],8,'.','');
		$stats['btc_24h_sell'] = number_format(($result[0]['btc_24h_s'] * $c_currency_info['usd_ask'])/$currency_info['usd_ask'],8,'.','');
		$stats['btc_1h'] = number_format(($result[0]['btc_1h'] * $c_currency_info['usd_ask'])/$currency_info['usd_ask'],8,'.','');
		$stats['btc_1h_buy'] = number_format(($result[0]['btc_1h_b'] * $c_currency_info['usd_ask'])/$currency_info['usd_ask'],8,'.','');
		$stats['btc_1h_sell'] = number_format(($result[0]['btc_1h_s'] * $c_currency_info['usd_ask'])/$currency_info['usd_ask'],8,'.','');

		if ($CFG->memcached) {
			$key = 'stats_'.$c_currency_info['currency'].'_'.$currency_info['currency'];
			$set[$key] = $stats;
			memcached_safe_set($set,300);
		}

		return $stats;
	}
	
	public static function getBTCTraded($c_currency_id) {
		global $CFG;
				
		$c_currency_info = $CFG->currencies[$c_currency_id];
		if (empty($c_currency_info)) {
			$main = Currencies::getMain();
			$c_currency_info = $CFG->currencies[$main['crypto']];
			$c_currency_id = $c_currency_info['id'];
		}

		if ($CFG->memcached && empty($CFG->m_skip)) {
			$cached = $CFG->m->get('btc_traded_'.$c_currency_id);
			if ($cached) {
				return $cached;
			}
		}
		
		$sql = 'SELECT btc_24h AS total_btc_traded FROM wallets WHERE c_currency = '.$c_currency_id.' LIMIT 0,1';
		$result = db_query_array($sql);
		
		if ($CFG->memcached)
			$CFG->m->set('btc_traded_'.$c_currency_id,$result,120);
		
		return $result;
	}
}