#!/usr/bin/php
<?php
include 'common.php';
echo date('Y-m-d H:i:s').' Beginning Crypto Withdrawals processing...'.PHP_EOL;

$cryptos = Currencies::getCryptos();
$sql = "SELECT requests.currency, requests.site_user, requests.amount, requests.send_address, requests.id, site_users_balances.balance, site_users_balances.id AS balance_id FROM requests LEFT JOIN site_users ON (requests.site_user = site_users.id) LEFT JOIN site_users_balances ON (site_users_balances.site_user = requests.site_user AND site_users_balances.currency = requests.currency) WHERE requests.request_status = {$CFG->request_pending_id} AND requests.currency IN (".implode(',',$cryptos).") AND requests.request_type = {$CFG->request_withdrawal_id}".(($CFG->withdrawals_btc_manual_approval == 'Y') ? " AND (requests.approved = 'Y' OR site_users.trusted = 'Y')" : '');
$result = db_query_array($sql);
if (!$result) {
	echo 'done'.PHP_EOL;
	exit;
}

$wallets = Wallets::get();
if (!$wallets) {
	echo 'Error: no wallets to process.'.PHP_EOL;
	exit;
}

foreach ($wallets as $wallet) {
	if($wallet['c_currency'] != 45 && $wallet['c_currency'] != 47 && $wallet['c_currency'] != 44){
	$bitcoin = new Bitcoin($wallet['bitcoin_username'],$wallet['bitcoin_passphrase'],$wallet['bitcoin_host'],$wallet['bitcoin_port'],$wallet['bitcoin_protocol']);
	$bitcoin->settxfee($wallet['bitcoin_sending_fee']);
	$available = $wallet['hot_wallet_btc'];
	$deficit = $wallet['deficit_btc'];
	$users = array();
	$transactions = array();
	$user_balances = array();
	$addresses = array();
	
	if ($result) {
		$pending = 0;
		
		foreach ($result as $row) {
			if ($row['currency'] != $wallet['c_currency'])
				continue;
			
			// check if user sending to himself
			$addr_info = BitcoinAddresses::getAddress($row['send_address'],$wallet['c_currency']);
			if (!empty($addr_info['site_user']) && $addr_info['site_user'] == $row['site_user']) {
				db_update('requests',$row['id'],array('request_status'=>$CFG->request_completed_id));
				continue;
			}
			
			// check if sending to another user
			if (!empty($addr_info['site_user'])) {
				if (empty($user_balances[$addr_info['site_user']])) {
					$bal_info = User::getBalance($addr_info['site_user'],$wallet['c_currency'],true);
					$user_balances[$addr_info['site_user']] = $bal_info['balance'];
				}
				
				User::updateBalances($row['site_user'],array($wallet['c_currency']=>(-1 * $row['amount'])),true);
				User::updateBalances($addr_info['site_user'],array($wallet['c_currency']=>($row['amount'])),true);
				db_update('requests',$row['id'],array('request_status'=>$CFG->request_completed_id));
				
				$rid = db_insert('requests',array('date'=>date('Y-m-d H:i:s'),'site_user'=>$addr_info['site_user'],'currency'=>$wallet['c_currency'],'amount'=>$row['amount'],'description'=>$CFG->deposit_bitcoin_desc,'request_status'=>$CFG->request_completed_id,'request_type'=>$CFG->request_deposit_id));
				if ($rid)
					db_insert('history',array('date'=>date('Y-m-d H:i:s'),'history_action'=>$CFG->history_deposit_id,'site_user'=>$addr_info['site_user'],'request_id'=>$rid,'balance_before'=>$user_balances[$addr_info['site_user']],'balance_after'=>($user_balances[$addr_info['site_user']] + $row['amount']),'bitcoin_address'=>$row['send_address']));
				
				$user_balances[$addr_info['site_user']] = $user_balances[$addr_info['site_user']] + $row['amount'];
				continue;
			}

			// check if hot wallet has enough to send
			$pending += $row['amount'];
			if ($row['amount'] > $available)
				continue;
			if (bcsub($row['amount'],$wallet['bitcoin_sending_fee'],8) > 0) {
					$transactions[$row['send_address']] = (!empty($transactions[$row['send_address']])) ? bcadd($row['amount'],$transactions[$row['send_address']],8) : $row['amount'];
			}
			
			$users[$row['site_user']] = (!empty($users[$row['site_user']])) ? bcadd($row['amount'],$users[$row['site_user']],8) : $row['amount'];
			$requests[] = $row['id'];
			$available = bcsub($available,$row['amount'],8);
		}
	
		if ($pending > $available) {
			db_update('wallets',$wallet['id'],array('deficit_btc'=>($pending - $available),'pending_withdrawals'=>$pending));
			echo $CFG->currencies[$wallet['c_currency']]['currency'].' Deficit: '.($pending - $available).PHP_EOL;
		}
	}
	
	if (!empty($transactions)) {
			if (count($transactions) > 1) {
				$bitcoin->walletpassphrase($wallet['bitcoin_passphrase'],3);
				$json_arr = array();
				$fees_charged = 0;
				foreach ($transactions as $address => $amount) {
					$json_arr[$address] = ($amount - $wallet['bitcoin_sending_fee']);
					$fees_charged += $wallet['bitcoin_sending_fee'];
				}
				$response = $bitcoin->sendmany($wallet['bitcoin_accountname'],json_decode(json_encode($json_arr)));
				
				if (!empty($bitcoin->error))
					echo $bitcoin->error.PHP_EOL;
			}
			elseif (count($transactions) == 1) {
				$bitcoin->walletpassphrase($wallet['bitcoin_passphrase'],3);
				$fees_charged = 0;
				foreach ($transactions as $address => $amount) {
					$response = $bitcoin->sendfrom($wallet['bitcoin_accountname'],$address,(float)bcsub($amount,$wallet['bitcoin_sending_fee'],8));
					$fees_charged += $wallet['bitcoin_sending_fee'];
					
					if (!empty($bitcoin->error))
						echo $bitcoin->error.PHP_EOL;
				}
			}
			if (!empty($response) && $users && !$bitcoin->error) {
				echo $CFG->currencies[$wallet['c_currency']]['currency'].' Transactions sent: '.$response.PHP_EOL;
				
				$total = 0;
				$transaction = $bitcoin->gettransaction($response);
				$actual_fee_difference = $fees_charged - abs($transaction['fee']);
			
				foreach ($users as $site_user => $amount) {
					$total += $amount;
					User::updateBalances($site_user,array($wallet['c_currency']=>(-1 * $amount)),true);
				}
				
				foreach ($requests as $request_id) {
					db_update('requests',$request_id,array('request_status'=>$CFG->request_completed_id));
				}
				
				if ($total > 0) {
					Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>(0 - $total + $actual_fee_difference),'total_btc'=>(0 - $total + $actual_fee_difference)));
					Status::updateEscrows(array($wallet['c_currency']=>$actual_fee_difference));
					db_update('wallets',$wallet['id'],array('pending_withdrawals'=>($pending - $total)));
				}
			}
	}
	
	
	
	if (empty($pending)) db_update('wallets',$wallet['id'],array('deficit_btc'=>'0'));
 }
}

db_update('status',1,array('cron_send_bitcoin'=>date('Y-m-d H:i:s')));

echo 'done'.PHP_EOL;