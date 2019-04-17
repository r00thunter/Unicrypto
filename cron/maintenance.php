#!/usr/bin/php
<?php
include 'common.php';
echo date('Y-m-d H:i:s').' Beginning Maintenance processing...'.PHP_EOL;

$CFG->session_active = 1;
$CFG->in_cron = 1;

$main = Currencies::getMain();
$cryptos = Currencies::getCryptos();
$wallets = Wallets::get();
$btc_24h_main = 0;
$reserve_ratio = ($CFG->bitcoin_reserve_ratio) ? $CFG->bitcoin_reserve_ratio : '0';

// get 24 hour BTC volume
$now = 'DATE_ADD(NOW(), INTERVAL '.((($CFG->timezone_offset)/60)/60).' HOUR)';
$sql = "SELECT transactions.c_currency, IFNULL(SUM(btc),0) AS btc_24h, IFNULL(SUM(IF(transaction_type != {$CFG->transactions_buy_id},btc,0)),0) AS btc_24h_s, IFNULL(SUM(IF(transaction_type = {$CFG->transactions_buy_id},btc,0)),0) AS btc_24h_b, IFNULL(SUM(IF(`date` >= DATE_SUB($now, INTERVAL 1 HOUR),btc,0)),0) AS btc_1h, IFNULL(SUM(IF(transaction_type != {$CFG->transactions_buy_id} AND `date` >= DATE_SUB($now, INTERVAL 1 HOUR),btc,0)),0) AS btc_1h_s, IFNULL(SUM(IF(transaction_type = {$CFG->transactions_buy_id} AND `date` >= DATE_SUB($now, INTERVAL 1 HOUR),btc,0)),0) AS btc_1h_b FROM transactions WHERE `date` >= DATE_SUB($now, INTERVAL 1 DAY) GROUP BY transactions.c_currency ORDER BY NULL";
$result = db_query_array($sql);
$processed = array();
if ($result) {
	foreach ($result as $currency) {
		$processed[] = $currency['c_currency'];
		
		$btc_24h[$currency['c_currency']] = $currency['btc_24h'];
		$btc_24h_s[$currency['c_currency']] = $currency['btc_24h_s'];
		$btc_24h_b[$currency['c_currency']] = $currency['btc_24h_b'];
		$btc_1h[$currency['c_currency']] = $currency['btc_1h'];
		$btc_1h_s[$currency['c_currency']] = $currency['btc_1h_s'];
		$btc_1h_b[$currency['c_currency']] = $currency['btc_1h_b'];
		$btc_24h_main += ($currency['c_currency'] != $main['crypto']) ? round(($CFG->currencies[$currency['c_currency']]['usd_ask'] / $CFG->currencies[$main['crypto']]['usd_ask']) * $currency['btc_24h'],8,PHP_ROUND_HALF_UP) : $currency['btc_24h'];
		
		db_update('wallets',$wallets[$CFG->currencies[$currency['c_currency']]['currency']]['id'],array('btc_24h'=>$currency['btc_24h'],'btc_24h_s'=>$currency['btc_24h_s'],'btc_24h_b'=>$currency['btc_24h_b'],'btc_1h'=>$currency['btc_1h'],'btc_1h_s'=>$currency['btc_1h_s'],'btc_1h_b'=>$currency['btc_1h_b']));
	}
}

foreach ($CFG->currencies as $currency_id => $currency) {
	if (!is_numeric($currency_id) || $currency['is_crypto'] != 'Y' || in_array($currency_id,$processed))
		continue;
	
	$btc_24h[$currency_id] = '0';
	$btc_24h_s[$currency_id] = '0';
	$btc_24h_b[$currency_id] = '0';
	$btc_1h[$currency_id] = '0';
	$btc_1h_s[$currency_id] = '0';
	$btc_1h_b[$currency_id] = '0';
	
	db_update('wallets',$wallets[$CFG->currencies[$currency_id]['currency']]['id'],array('btc_24h'=>0,'btc_24h_s'=>0,'btc_24h_b'=>0,'btc_1h'=>0,'btc_1h_s'=>0,'btc_1h_b'=>0));
}


// determine users' monthly volume
$sql = 'SELECT id, global_btc, from_usd, to_usd FROM fee_schedule ORDER BY global_btc ASC, from_usd ASC';
$result = db_query_array($sql);
if ($result && count($result) > 1) {
	$sql = 'SELECT ROUND(SUM(IF(transactions.id IS NOT NULL,transactions.btc * transactions.btc_price * currencies.usd_ask,transactions1.btc * transactions1.btc_price * currencies1.usd_ask)),2) AS volume, site_users.id AS user_id
			FROM site_users
			LEFT JOIN transactions ON (transactions.site_user = site_users.id AND transactions.date >= DATE_SUB(CURDATE(),INTERVAL 1 MONTH))
			LEFT JOIN transactions transactions1 ON (transactions1.site_user1 = site_users.id AND transactions1.date >= DATE_SUB(CURDATE(),INTERVAL 1 MONTH))
			LEFT JOIN currencies ON (currencies.id = transactions.currency)
			LEFT JOIN currencies currencies1 ON (currencies1.id = transactions1.currency)
			WHERE transactions.id IS NOT NULL OR transactions1.id IS NOT NULL
			GROUP BY site_users.id';
	$volumes = db_query_array($sql);
	
	$global_fc_id = false;
	$fee_schedule = false;
	if ($volumes) {
		foreach ($volumes as $volume) {
			foreach ($result as $row) {
				$global_fc_id = ($row['global_btc'] <= $btc_24h_main && $row['global_btc'] > 0) ? $row['id'] : $global_fc_id;
				$fee_schedule = ($row['from_usd'] <= ($volume['volume'] / $CFG->currencies[$main['fiat']]['usd_ask'])) ? $row['id'] : $fee_schedule;
			}
			
			$fee_schedule = ($fee_schedule >= $global_fc_id) ? $fee_schedule : $global_fc_id;
			$sql = 'UPDATE site_users SET site_users.fee_schedule = '.$fee_schedule.' WHERE site_users.id = '.$volume['user_id'];
			db_query($sql);
		}
	}
}

// expire settings change request
$sql = 'DELETE FROM change_settings WHERE `date` <= ("'.date('Y-m-d H:i:s').'" - INTERVAL 1 DAY)';
$result = db_query($sql);

// expire unautharized requests
$sql = 'UPDATE requests SET request_status = '.$CFG->request_cancelled_id.' WHERE request_status = '.$CFG->request_awaiting_id.' AND `date` <= (NOW() - INTERVAL 1 DAY)';
$result = db_query($sql);

// 30 day token don't ask
//$sql = 'UPDATE site_users SET dont_ask_30_days = "N" WHERE dont_ask_date <= (NOW() - INTERVAL 1 MONTH) AND dont_ask_30_days = "Y" ';
//$result = db_query($sql);

// delete old sessions
$sql = "DELETE FROM sessions WHERE session_time < ('".date('Y-m-d H:i:s')."' - INTERVAL 15 MINUTE) ";
db_query($sql);

// delete old chats
$sql = 'SELECT COUNT(id) AS total FROM chat';
$result = db_query_array($sql);
if ($result && $result[0]['total'] > 0) {
	$c = $result[0]['total'] - 30;
	if ($c > 0)
		db_query('DELETE FROM chat ORDER BY id ASC LIMIT '.$c);
}

// delete ip access log
$timeframe = (!empty($CFG->cloudflare_blacklist_timeframe)) ? $CFG->cloudflare_blacklist_timeframe : 15;
$sql = "DELETE FROM ip_access_log WHERE `timestamp` < ('".date('Y-m-d H:i:s')."' - INTERVAL $timeframe MINUTE) ";
db_query($sql);

// set market price orders at market price
$sql = "SELECT id,btc,currency,c_currency,order_type,site_user FROM orders WHERE orders.market_price = 'Y' ORDER BY orders.id ASC";
$result = db_query_array($sql);
if ($result) {
	foreach ($result as $row) {
		$buy = ($row['order_type'] == $CFG->order_type_bid);
		$operations = Orders::executeOrder($buy,false,$row['btc'],$row['c_currency'],$row['currency'],false,1,$row['id'],$row['site_user'],true);
	}
}

// notify pending withdrawals
if ($CFG->email_notify_fiat_withdrawals == 'Y') {
	$sql = 'SELECT 1 FROM requests WHERE notified = 0 AND request_type = '.$CFG->request_widthdrawal_id.' AND request_status = '.$CFG->request_pending_id.' AND `date` < DATE_SUB(DATE_ADD(NOW(), INTERVAL '.((($CFG->timezone_offset)/60)/60).' HOUR), INTERVAL 5 MINUTE) AND done != \'Y\' LIMIT 0,1';
	$result = db_query_array($sql);
	
	$sql = 'SELECT c_currency, (hot_wallet_btc - ((total_btc * '.$reserve_ratio.') + pending_withdrawals) - bitcoin_sending_fee) AS deficit FROM wallets WHERE hot_wallet_notified = "N" AND (hot_wallet_btc - ((total_btc * '.$reserve_ratio.') + pending_withdrawals) - bitcoin_sending_fee) LIMIT 0,1';
	$result1 = db_query_array($sql);
	
	if ($result || $result1) {
		$info = array();
		
		$sql = 'SELECT ROUND(SUM(requests.amount),2) AS amount, LOWER(currencies.currency) AS currency FROM requests LEFT JOIN currencies ON (currencies.id = requests.currency) WHERE requests.request_type = '.$CFG->request_widthdrawal_id.' AND requests.request_status = '.$CFG->request_pending_id.' AND requests.done != \'Y\' GROUP BY requests.currency';
		$result = db_query_array($sql);
		
		if ($result) {
			foreach ($result as $row) {
				if (empty($info['pending_withdrawals']))
					$info['pending_withdrawals'] = '';
				
				$info['pending_withdrawals'] .= strtoupper($row['currency']).': '.$row['amount'].'<br/>';
			}
		}
		
		if ($result1) {
			foreach ($result1 as $row) {
				if (empty($info['pending_withdrawals']))
					$info['pending_withdrawals'] = '';
				
				$info['pending_withdrawals'] .= 'Hot Wallet Deficit ('.$CFG->currencies[$row['c_currency']]['currency'].'): '.abs($row['deficit']).'<br/>';
			}
		}
		
		if (count($info) > 0) {
			$CFG->language = 'en';
			$email = SiteEmail::getRecord('pending-withdrawals');
			Email::send($CFG->form_email,$CFG->contact_email,$email['title'],$CFG->form_email_from,false,$email['content'],$info);
				
			$sql = 'UPDATE requests SET notified = 1 WHERE notified = 0';
			db_query($sql);
				
			$sql = 'UPDATE wallets SET hot_wallet_notified = "Y"';
			db_query($sql);
		}
	}
}

// subtract withdrawals
$sql = 'SELECT site_users_balances.balance AS balance, site_users_balances.id AS balance_id, requests.id AS request_id, requests.site_user AS site_user, requests.currency AS currency, ROUND(requests.amount,2) AS amount FROM requests LEFT JOIN site_users_balances ON (site_users_balances.site_user = requests.site_user AND site_users_balances.currency = requests.currency) WHERE requests.request_type = '.$CFG->request_widthdrawal_id.' AND requests.currency NOT IN ('.implode(',',$cryptos).') AND requests.request_status = '.$CFG->request_pending_id.' AND requests.done = \'Y\'';
$result = db_query_array($sql);
if ($result) {
	foreach ($result as $row) {
		if (empty($old_balance[$row['site_user']][$row['currency']]))
			$old_balance[$row['site_user']][$row['currency']] = $row['balance'];
		
		$sql = 'UPDATE site_users_balances SET balance = balance - '.number_format($row['amount'],8,'.','').' WHERE id = '.$row['balance_id'];
		db_query($sql);
		
		$sql = 'UPDATE history SET balance_before = '.number_format($old_balance[$row['site_user']][$row['currency']],8,'.','').', balance_after = '.number_format(($old_balance[$row['site_user']][$row['currency']] - $row['amount']),8,'.','').' WHERE request_id = '.$row['request_id'];
		db_query($sql);
		
		$old_balance[$row['site_user']][$row['currency']] = $old_balance[$row['site_user']][$row['currency']] - $row['amount'];
	}
	$sql = 'UPDATE requests SET requests.request_status = '.$CFG->request_completed_id.' WHERE requests.request_type = '.$CFG->request_widthdrawal_id.' AND requests.currency NOT IN ('.implode(',',$cryptos).') AND requests.request_status = '.$CFG->request_pending_id.' AND requests.done = "Y" ';
	db_query($sql);
}

// currency ledger
if ((date('H') == 7 || date('H') == 16) && (date('i') >= 0 && date('i') < 5)) {
	// check total currency needed for withdrawals
	$sql = "SELECT currency, SUM(amount) AS amount FROM requests WHERE requests.request_status = {$CFG->request_pending_id} AND currency NOT IN ('.implode(',',$cryptos).') AND request_type = {$CFG->request_withdrawal_id} GROUP BY currency";
	$result = db_query_array($sql);
	if ($result) {
		foreach ($result as $row) {
			$withdrawals[$row['currency']] = $row['amount'];
		}
	}
	
	// get fiat escrow balances from Bank
	//////////////////////////////////////////
	
	// get crypto escrow balances
	//$wallets = Wallets::get();
	
	// get current currency ledger
	$ledger = array();
	$sql = 'SELECT * FROM conversions WHERE is_active != "Y"';
	$result = db_query_array($sql);
	if ($result) {
		foreach ($result as $row) {
			$ledger[$row['currency']] = $row['amount'];
		}
	}
	
	// factor new imbalances into ledger balances
	$sql = 'SELECT 
			IF(transactions.transaction_type = '.$CFG->transactions_buy_id.', currency, currency1) AS currency, 
			IF(transactions.transaction_type = '.$CFG->transactions_buy_id.', currency1, currency) AS currency1, 
			SUM(IF(transactions.transaction_type = '.$CFG->transactions_buy_id.', transactions.btc_price * transactions.btc, transactions.orig_btc_price * transactions.btc )) AS amount, 
			SUM(IF(transactions.transaction_type = '.$CFG->transactions_buy_id.', transactions.orig_btc_price * transactions.btc, transactions.btc_price * transactions.btc)) AS amount_needed 
			FROM transactions WHERE factored != "Y" AND conversion = "Y" 
			GROUP BY CONCAT(IF(transactions.transaction_type = '.$CFG->transactions_buy_id.', currency1, currency) , "-", IF(transactions.transaction_type = '.$CFG->transactions_buy_id.', currency, currency1))';
	$result = db_query_array($sql);
	if ($result) {
		foreach ($result as $row) {
			$ledger[$row['currency']] = (!empty($ledger[$row['currency']])) ? $ledger[$row['currency']] + $row['amount'] : $row['amount'];
			$ledger[$row['currency1']] = (!empty($ledger[$row['currency1']])) ? $ledger[$row['currency1']] - $row['amount_needed'] : ($row['amount_needed'] * -1);
		}
	}
	
	if ($ledger) {
		foreach ($ledger as $currency => $amount) {
			/*
			 if ($withdrawals[$currency] > $amount) {
			// consolidate that particular currency to satisfy withdrawals
			/////////////////////////////////////////////
			}
			*/
	
			$sql = 'SELECT id FROM conversions WHERE currency = '.$currency.' AND is_active != "Y" LIMIT 0,1';
			$result = db_query_array($sql);
			if ($result)
				db_update('conversions',$result[0]['id'],array('amount'=>$amount,'total_withdrawals'=>((!empty($withdrawals[$currency])) ? $withdrawals[$currency] : 0),'date1'=>date('Y-m-d H:i:s')));
			else
				db_insert('conversions',array('amount'=>$amount,'total_withdrawals'=>((!empty($withdrawals[$currency])) ? $withdrawals[$currency] : 0),'date'=>date('Y-m-d H:i:s'),'date1'=>date('Y-m-d H:i:s'),'currency'=>$currency,'is_active'=>'N','factored'=>'N'));
		}
	}
	
	$sql = 'UPDATE transactions SET factored = "Y" WHERE factored != "Y"';
	db_query($sql);
}

db_update('status',1,array('cron_maintenance'=>date('Y-m-d H:i:s')));

echo 'done'.PHP_EOL;
