#!/usr/bin/php
<?php
include 'common.php';
echo date('Y-m-d H:i:s').' Beginning Crypto Deposits processing...'.PHP_EOL;

$CFG->session_active = true;
$transactions_dir = $CFG->dirroot.'transactions/';

$transactions = scandir($transactions_dir);
if (!$transactions) {
	echo 'done'.PHP_EOL;
	exit;
}

$wallets = Wallets::get();
if (!$wallets) {
	echo 'Error: no wallets to process.'.PHP_EOL;
	exit;
}

$sql = "SELECT id, transaction_id FROM bitcoind_log ORDER BY `date` DESC LIMIT 0,100 ";
$result = db_query_array($sql);
if ($result) {
	foreach ($result as $row) {
		$transaction_log[$row['transaction_id']] = $row['id'];
	}
}

foreach ($wallets as $wallet) {
if($wallet['c_currency'] != 45 && $wallet['c_currency'] != 47 && $wallet['c_currency'] != 44){
	$total_received = 0;
	$bitcoin = new Bitcoin($wallet['bitcoin_username'],$wallet['bitcoin_passphrase'],$wallet['bitcoin_host'],$wallet['bitcoin_port'],$wallet['bitcoin_protocol']);
	$bitcoin->settxfee($wallet['bitcoin_sending_fee']);
	
	$email = SiteEmail::getRecord('new-deposit');
	$sql = "SELECT transaction_id, id FROM requests WHERE request_status != {$CFG->request_completed_id} AND currency = {$wallet['c_currency']} AND request_type = {$CFG->request_deposit_id} ";
	$result = db_query_array($sql);
	if ($result) {
		foreach ($result as $row) {
			$requests[$row['transaction_id']] = $row['id'];
		}
	}
	
	$addresses = array();
	$user_balances = array();
	
	foreach ($transactions as $t_id) {
		if (!$t_id || $t_id == '.' || $t_id == '..' || $t_id == '.gitignore')
			continue;
		
		$transaction = $bitcoin->gettransaction($t_id);
		if (!empty($transaction_log[$t_id])) {
			unlink($transactions_dir.$t_id);
			continue;
		}
		if (empty($transaction['details']))
			continue;
		
		//$raw = $bitcoin->decoderawtransaction($bitcoin->getrawtransaction($t_id));
		//$sender_address = $raw['vout'][1]['scriptPubKey']['addresses'][0];
		
		$send = false;
		$pending = false;
		$hot_wallet_in = 0;
		
		foreach ($transaction['details'] as $detail) {
			if ($detail['category'] == 'receive') {
				// identify the user and request id
				if (empty($addresses[$detail['address']])) {
					$addr_info = BitcoinAddresses::getAddress($detail['address'],$wallet['c_currency']);
					if (!$addr_info)
						continue;
					
					$addresses[$detail['address']] = $addr_info;
				}
				
				$user_id = $addresses[$detail['address']]['site_user'];
				$request_id = (!empty($requests[$transaction['txid']])) ? $requests[$transaction['txid']] : false;
				
				// check for hot wallet recharge
				if ($addresses[$detail['address']]['hot_wallet'] == 'Y') {
					if ($transaction['confirmations'] > 0) {
						$hot_wallet_in = $detail['amount'];
					}
					continue;
				}
				elseif ($addresses[$detail['address']]['system_address'] == 'Y') {
					unlink($transactions_dir.$t_id);
					break;
				}
				
				// get user balance... no need to lock
				if (empty($user_balances[$user_id])) {
					$bal_info = User::getBalance($user_id,$wallet['c_currency']);
					$user_balances[$user_id] = $bal_info['balance'];
				}
				
				// if not confirmed enough
				if (($addresses[$detail['address']]['trusted'] == 'Y' && $transaction['confirmations'] < 1) || ($addresses[$detail['address']]['trusted'] != 'Y' && $transaction['confirmations'] < 3)) {
					if (!($request_id > 0)) {
						$rid = db_insert('requests',array('date'=>date('Y-m-d H:i:s'),'site_user'=>$user_id,'currency'=>$wallet['c_currency'],'amount'=>$detail['amount'],'description'=>$CFG->deposit_bitcoin_desc,'request_status'=>$CFG->request_pending_id,'request_type'=>$CFG->request_deposit_id,'transaction_id'=>$transaction['txid'],'send_address'=>$detail['address']));
						db_insert('history',array('date'=>date('Y-m-d H:i:s'),'history_action'=>$CFG->history_deposit_id,'site_user'=>$user_id,'request_id'=>$rid,'balance_before'=>$user_balances[$user_id],'balance_after'=>($user_balances[$user_id] + $detail['amount']),'bitcoin_address'=>$detail['address']));
					}
					
					echo $CFG->currencies[$wallet['c_currency']]['currency'].' transaction pending.'.PHP_EOL;
					$pending = true;
				}
				else {
					// if confirmation sufficient
					if (!($request_id > 0)) {
						$updated = db_insert('requests',array('date'=>date('Y-m-d H:i:s'),'site_user'=>$user_id,'currency'=>$wallet['c_currency'],'amount'=>$detail['amount'],'description'=>$CFG->deposit_bitcoin_desc,'request_status'=>$CFG->request_completed_id,'request_type'=>$CFG->request_deposit_id,'transaction_id'=>$transaction['txid'],'send_address'=>$detail['address']));
						db_insert('history',array('date'=>date('Y-m-d H:i:s'),'history_action'=>$CFG->history_deposit_id,'site_user'=>$user_id,'request_id'=>$updated,'balance_before'=>$user_balances[$user_id],'balance_after'=>($user_balances[$user_id] + $detail['amount']),'bitcoin_address'=>$detail['address']));
					}
					else
						$updated = db_update('requests',$request_id,array('request_status'=>$CFG->request_completed_id));
					
					if ($updated > 0) {
						User::updateBalances($user_id,array($wallet['c_currency']=>($detail['amount'])),true);
						db_insert('bitcoind_log',array('transaction_id'=>$transaction['txid'],'amount'=>$detail['amount'],'date'=>date('Y-m-d H:i:s')));
						
						$unlink = unlink($transactions_dir.$t_id);
						$total_received += $detail['amount'];
						$user_balances[$user_id] = $user_balances[$user_id] + $detail['amount'];
						
						if (!$unlink && file_exists($unlink)) {
							$unlink = unlink($transactions_dir.$t_id);
						}
						
						$info = $addresses[$detail['address']];
						if ($info['notify_deposit_btc'] == 'Y') {
						    $info['amount'] = $detail['amount'];
						    $info['currency'] = $CFG->currencies[$wallet['c_currency']]['currency'];
						    $info['id'] = (!empty($request_id)) ? $request_id : $updated;
						    $CFG->language = ($info['last_lang']) ? $info['last_lang'] : 'en';
						    Email::send($CFG->form_email,$info['email'],str_replace('[amount]',$detail['amount'],str_replace('[currency]',$info['currency'],$email['title'])),$CFG->form_email_from,false,$email['content'],$info);
						}
						
						if (!$unlink)
							echo 'Error: Could not delete transaction file.'.PHP_EOL;
						else
							echo $CFG->currencies[$wallet['c_currency']]['currency'].' transaction credited successfully.'.PHP_EOL;
					}
				}
			}
			elseif ($detail['category'] == 'send') {
				if ($addresses[$detail['address']]['system_address']) {
					unlink($transactions_dir.$t_id);
					break;
				} 
				else {
					$send = true;
				}
			}
		}
		
		if ($send && !$pending && !($hot_wallet_in > 0))
			unlink($transactions_dir.$t_id);
		elseif (!$send && ($hot_wallet_in > 0)) {
			$updated = Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>$hot_wallet_in,'warm_wallet_btc'=>(-1 * ($hot_wallet_in + $wallet['bitcoin_sending_fee'])),'total_btc'=>(-1 * $wallet['bitcoin_sending_fee'])));
			echo 'Hot wallet received '.$hot_wallet_in.' '.$CFG->currencies[$wallet['c_currency']]['currency'].PHP_EOL;
			if ($updated) {
				$unlink = unlink($transactions_dir.$t_id);
				if (!$unlink && file_exists($unlink)) {
					$unlink = unlink($transactions_dir.$t_id);
				}
				
				db_insert('bitcoind_log',array('transaction_id'=>$transaction['txid'],'amount'=>$hot_wallet_in,'date'=>date('Y-m-d H:i:s')));
				db_update('wallets',$wallet['id'],array('hot_wallet_notified'=>'N'));
			}
		}
	}
	
	$warm_wallet = $wallet['bitcoin_warm_wallet_address'];
	$reserve = Wallets::getReserveSurplus($wallet['id']);
	$reserve_surplus = round($reserve['surplus'],8,PHP_ROUND_HALF_UP) + $total_received;
	echo 'Reserve surplus: '.sprintf("%.8f", $reserve_surplus).' '.$CFG->currencies[$wallet['c_currency']]['currency'].PHP_EOL;
	
	/*
	if ($total_received > 0 || $reserve_surplus > $CFG->bitcoin_reserve_min) {
		$ch = curl_init($CFG->hv_addr);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,array('key'=>$CFG->hv_key));
		curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
		$result1 = curl_exec($ch);
		$result = json_decode($result1,true);
		curl_close($ch);
		$warm_wallet = $result['address'];
	}
	*/
	
	if ($total_received > 0) {
		echo 'Total '.$CFG->currencies[$wallet['c_currency']]['currency'].' received: '.$total_received.PHP_EOL;
		$update = Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>$total_received,'total_btc'=>$total_received));
		
		if ($warm_wallet && $reserve_surplus > $CFG->bitcoin_reserve_min) {
			$bitcoin->walletpassphrase($wallet['bitcoin_passphrase'],3);
			$response = $bitcoin->sendfrom($wallet['bitcoin_accountname'],$warm_wallet,floatval($reserve_surplus));
			$transferred = 0;
			echo $bitcoin->error;
			
			if ($response && !$bitcoin->error) {
				$transferred = $reserve_surplus;
				$transfer_fees = 0;
				$transaction = $bitcoin->gettransaction($response);
				foreach ($transaction['details'] as $detail) {
					if ($detail['category'] == 'send') {
						$detail['fee'] = round(abs($detail['fee']),8,PHP_ROUND_HALF_UP);
						if ($detail['fee'] > 0) {
							$transfer_fees += $detail['fee'];
							db_insert('fees',array('fee'=>$detail['fee'],'date'=>date('Y-m-d H:i:s')));
						}
					}
				}
				
				Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>(0 - $transferred - $transfer_fees),'warm_wallet_btc'=>$transferred - $transfer_fees,'total_btc'=>(0 - $transfer_fees)));
				echo 'Transferred '.$reserve_surplus.' '.$CFG->currencies[$wallet['c_currency']]['currency'].' to warm wallet. TX: '.$response.PHP_EOL;
			}
		}
	}
	elseif ($warm_wallet && $reserve_surplus > $CFG->bitcoin_reserve_min) {
		$bitcoin->walletpassphrase($wallet['bitcoin_passphrase'],3);
		$response = $bitcoin->sendfrom($wallet['bitcoin_accountname'],$warm_wallet,floatval($reserve_surplus));
		$transferred = 0;
		echo $bitcoin->error;
		
		if ($response && !$bitcoin->error) {
			$transferred = $reserve_surplus;
			$transfer_fees = 0;
			$transaction = $bitcoin->gettransaction($response);
			
			foreach ($transaction['details'] as $detail) {
				if ($detail['category'] == 'send') {
					$detail['fee'] = round(abs($detail['fee']),8,PHP_ROUND_HALF_UP);
					if ($detail['fee'] > 0) {
						$transfer_fees += $detail['fee'];
						db_insert('fees',array('fee'=>$detail['fee'],'date'=>date('Y-m-d H:i:s')));
					}
				}
			}
			
			Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>(0 - $transferred - $transfer_fees),'warm_wallet_btc'=>$transferred - $transfer_fees,'total_btc'=>(0 - $transfer_fees)));
			echo 'Transferred '.$reserve_surplus.' '.$CFG->currencies[$wallet['c_currency']]['currency'].' to warm wallet. TX: '.$response.PHP_EOL;
		}
	}
}
}

db_update('status',1,array('cron_receive_bitcoin'=>date('Y-m-d H:i:s')));

echo 'done'.PHP_EOL;
