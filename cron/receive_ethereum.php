#!/usr/bin/php
<?php
include 'common.php';
require_once 'vendor/autoload.php';
use Achse\GethJsonRpcPhpClient\JsonRpc\Client;
use Achse\GethJsonRpcPhpClient\JsonRpc\GuzzleClient;
use Achse\GethJsonRpcPhpClient\JsonRpc\GuzzleClientFactory;
use Achse\GethJsonRpcPhpClient\Utils;
echo date('Y-m-d H:i:s').' Beginning Crypto Deposits processing...'.PHP_EOL;

$CFG->session_active = true;
$sqlBlockNumber = "SELECT * FROM block_tracker where currency = 45";
$resultBN = db_query_array($sqlBlockNumber) ;
if(!$resultBN){
    echo 'done'.PHP_EOL;
    exit ;
}
$currentBlockNumber = $resultBN[0]['blockNumber'] ;
//$highestBlockInTrxnSql = "SELECT max('blockNumber') FROM ethereum_log" ;
//$highestBlockInTrxn = db_query($highestBlockInTrxnSql) ;
$ethTransactionsSql = "SELECT * FROM ethereum_txn";
$transactions = db_query_array($ethTransactionsSql) ;
if (!$transactions) {
    echo "NO TRANSACTIONS FOUND \n" ;
	echo 'done'.PHP_EOL;
	exit;
}

$wallets = Wallets::get();
if (!$wallets) {
	echo 'Error: no wallets to process.'.PHP_EOL;
	exit;
}

foreach ($wallets as $wallet) {
    if($wallet['c_currency'] == 45){
        echo "ETHEREUM WALLET \n" ;
        $total_received = 0;
        // $guzzleClt = new GuzzleClient(new GuzzleClientFactory(), $wallet['bitcoin_host'], $wallet['bitcoin_port']);
        $guzzleClt = new GuzzleClient(new GuzzleClientFactory(),$wallet['bitcoin_host'],$wallet['bitcoin_port']);        
        $ethereum = new Client($guzzleClt);
        echo "ETHEREUM WALLET2 \n" ;        
        $addresses = array();
        $user_balances = array();
        $email = SiteEmail::getRecord('new-deposit');
        $sql = "SELECT transaction_id, id FROM requests WHERE request_status != {$CFG->request_completed_id} AND currency = {$wallet['c_currency']} AND request_type = {$CFG->request_deposit_id} ";
        $result = db_query_array($sql);
        if ($result) {
            foreach ($result as $row) {
                $requests[$row['transaction_id']] = $row['id'];
            }
        }

        $latestBlockHex = $ethereum->callMethod('eth_blockNumber',[]) ;
        if (!empty($latestBlockHex->error)){
            echo ''.$latestBlockHex->error->message.'   ';
            continue ;
        }
        $latestBlock = Utils::bigHexToBigDec($latestBlockHex->result) ;
        echo "LATEST BLOCK = ".$latestBlock."\n" ;
        $ethTxnDeleteSql = "DELETE FROM ethereum_txn WHERE id = " ;
        foreach($transactions as $transaction){
            //Delete from ethereum_txn table and add to ethereum_log table 
            db_query($ethTxnDeleteSql.$transaction['id']) ;
            db_insert('ethereum_log',array('transaction_id'=>$transaction['transaction_id'], 'amount'=>$transaction['amount'],'date'=>$transaction['date'],'fromAddress'=>$transaction['fromAddress'],'toAddress'=>$transaction['toAddress'],'blockNumber'=>$transaction['blockNumber']));
            
            //Calculate number of confirmations
            $transaction['confirmations'] = bcsub($latestBlock,$transaction['blockNumber']) ;
            if (empty($addresses[$transaction['toAddress']])) {
                $addr_info = BitcoinAddresses::getAddress($transaction['toAddress'],$wallet['c_currency']);
                if (!$addr_info)
                    continue;
                
                $addresses[$transaction['toAddress']] = $addr_info;
            }
            
            $user_id = $addresses[$transaction['toAddress']]['site_user'];
            $request_id = (!empty($requests[$transaction['transaction_id']])) ? $requests[$transaction['transaction_id']] : false;
            
            // check for hot wallet recharge
            if ($addresses[$transaction['toAddress']]['hot_wallet'] == 'Y') {
                
                if ($transaction['confirmations'] > 0) {
                    $hot_wallet_in = $transaction['amount'] ;
                }
                continue;
            }
            elseif ($addresses[$transaction['toAddress']]['system_address'] == 'Y') {
                //TODO Research into system_address
                continue ;
            }
            
            // get user balance... no need to lock
            if (empty($user_balances[$user_id])) {
                $bal_info = User::getBalance($user_id,$wallet['c_currency']);
                $user_balances[$user_id] = $bal_info['balance'];
            } 

            // if not confirmed enough
            if (($addresses[$transaction['toAddress']]['trusted'] == 'Y' && $transaction['confirmations'] < 1) || ($addresses[$transaction['toAddress']]['trusted'] != 'Y' && $transaction['confirmations'] < 3)) {
                //Create Pending Request and History
                if (!($request_id > 0)) {
                    $rid = db_insert('requests',array('date'=>date('Y-m-d H:i:s'),'site_user'=>$user_id,'currency'=>$wallet['c_currency'],'amount'=>$transaction['amount'],'description'=>$CFG->deposit_bitcoin_desc,'request_status'=>$CFG->request_pending_id,'request_type'=>$CFG->request_deposit_id,'transaction_id'=>$transaction['transaction_id'],'send_address'=>$transaction['toAddress']));
                    db_insert('history',array('date'=>date('Y-m-d H:i:s'),'history_action'=>$CFG->history_deposit_id,'site_user'=>$user_id,'request_id'=>$rid,'balance_before'=>$user_balances[$user_id],'balance_after'=>($user_balances[$user_id] + $transaction['amount']),'bitcoin_address'=>$transaction['toAddress']));
                }
                echo $CFG->currencies[$wallet['c_currency']]['currency'].' transaction pending.'.PHP_EOL;
                $pending = true;
            }else{
                if (!($request_id > 0)) {
                    $updated = db_insert('requests',array('date'=>date('Y-m-d H:i:s'),'site_user'=>$user_id,'currency'=>$wallet['c_currency'],'amount'=>$transaction['amount'],'description'=>$CFG->deposit_bitcoin_desc,'request_status'=>$CFG->request_completed_id,'request_type'=>$CFG->request_deposit_id,'transaction_id'=>$transaction['transaction_id'],'send_address'=>$transaction['toAddress']));
                    db_insert('history',array('date'=>date('Y-m-d H:i:s'),'history_action'=>$CFG->history_deposit_id,'site_user'=>$user_id,'request_id'=>$rid,'balance_before'=>$user_balances[$user_id],'balance_after'=>($user_balances[$user_id] + $transaction['amount']),'bitcoin_address'=>$transaction['toAddress']));
                }else{
						$updated = db_update('requests',$request_id,array('request_status'=>$CFG->request_completed_id));
                    
                }

                if ($updated > 0) {
                    User::updateBalances($user_id,array($wallet['c_currency']=>($transaction['amount'])),true);
                    
                    $total_received += $transaction['amount'];
                    $user_balances[$user_id] = $user_balances[$user_id] + $transaction['amount'];
                    
                    
                    $info = $addresses[$transaction['toAddress']];
                    if ($info['notify_deposit_btc'] == 'Y') {
                        $info['amount'] = $transaction['amount'];
                        $info['currency'] = $CFG->currencies[$wallet['c_currency']]['currency'];
                        $info['id'] = (!empty($request_id)) ? $request_id : $updated;
                        $CFG->language = ($info['last_lang']) ? $info['last_lang'] : 'en';
                        Email::send($CFG->form_email,$info['email'],str_replace('[amount]',$transaction['amount'],str_replace('[currency]',$info['currency'],$email['title'])),$CFG->form_email_from,false,$email['content'],$info);
                    }
                    echo $CFG->currencies[$wallet['c_currency']]['currency'].' transaction credited successfully.'.PHP_EOL;
                }
            }

        }

        //Check for pending Requests and update them
        $pendingRequestsSql = "SELECT transaction_id, id,send_address, amount FROM requests WHERE request_status != {$CFG->request_completed_id} AND currency = {$wallet['c_currency']} AND request_type = {$CFG->request_deposit_id}";
        $pendingRequests = db_query_array($pendingRequestsSql) ;
        foreach($pendingRequests as $pendRequest){
            $transactionId = pendRequest['transaction_id'] ;
            $transactionCall = $ethereum->callMethod('eth_getTransaction',[$transactionId]) ;
            if (empty($addresses[$transaction['send_address']])) {
                $addr_info = BitcoinAddresses::getAddress($transaction['send_address'],$wallet['c_currency']);
                if (!$addr_info)
                    continue;
                    
                $addresses[$transaction['send_address']] = $addr_info;
            }
            $user_id = $addresses[$transaction['send_address']]['site_user'];
            if (!empty($transactionCall->error)){
                echo ''.$transactionCall->error->message.'   ';
                continue ;
            }
            $transaction = $transactionCall->result ;
            $transaction['confirmations'] = bcsub($latestBlock,$transaction['blockNumber']) ;
            if ($transaction['confirmations'] < 3) {
                $pending = true;
                continue ;
                
            }else{
               
				$updated = db_update('requests',$pendRequest['id'],array('request_status'=>$CFG->request_completed_id));
                    

                if ($updated > 0) {
                    User::updateBalances($user_id,array($wallet['c_currency']=>($transaction['amount'])),true);
                    
                    $total_received += $transaction['amount'];
                    $user_balances[$user_id] = $user_balances[$user_id] + $transaction['amount'];
                    
                    
                    $info = $addresses[$transaction['send_address']];
                    if ($info['notify_deposit_btc'] == 'Y') {
                        $info['amount'] = $transaction['amount'];
                        $info['currency'] = $CFG->currencies[$wallet['c_currency']]['currency'];
                        $info['id'] = (!empty($transaction['id'])) ? $transaction['id'] : $updated;
                        $CFG->language = ($info['last_lang']) ? $info['last_lang'] : 'en';
                        Email::send($CFG->form_email,$info['email'],str_replace('[amount]',$transaction['amount'],str_replace('[currency]',$info['currency'],$email['title'])),$CFG->form_email_from,false,$email['content'],$info);
                    }
                    echo $CFG->currencies[$wallet['c_currency']]['currency'].' transaction credited successfully.'.PHP_EOL;
                }
            
            }
        }
	
    if ($hot_wallet_in > 0) {
        $updated = Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>$hot_wallet_in,'warm_wallet_btc'=>(-1 * ($hot_wallet_in + $wallet['bitcoin_sending_fee'])),'total_btc'=>(-1 * $wallet['bitcoin_sending_fee'])));
        echo 'Hot wallet received '.$hot_wallet_in.' '.$CFG->currencies[$wallet['c_currency']]['currency'].PHP_EOL;
        if ($updated) {
            db_update('wallets',$wallet['id'],array('hot_wallet_notified'=>'N'));
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
	
	// if ($total_received > 0) {
	// 	echo 'Total '.$CFG->currencies[$wallet['c_currency']]['currency'].' received: '.$total_received.PHP_EOL;
	// 	$update = Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>$total_received,'total_btc'=>$total_received));
		
	// 	if ($warm_wallet && $reserve_surplus > $CFG->bitcoin_reserve_min) {
	// 		$bitcoin->walletpassphrase($wallet['bitcoin_passphrase'],3);
	// 		$response = $bitcoin->sendfrom($wallet['bitcoin_accountname'],$warm_wallet,floatval($reserve_surplus));
	// 		$transferred = 0;
	// 		echo $bitcoin->error;
			
	// 		if ($response && !$bitcoin->error) {
	// 			$transferred = $reserve_surplus;
	// 			$transfer_fees = 0;
	// 			$transaction = $bitcoin->gettransaction($response);
	// 			foreach ($transaction['details'] as $detail) {
	// 				if ($detail['category'] == 'send') {
	// 					$detail['fee'] = round(abs($detail['fee']),8,PHP_ROUND_HALF_UP);
	// 					if ($detail['fee'] > 0) {
	// 						$transfer_fees += $detail['fee'];
	// 						db_insert('fees',array('fee'=>$detail['fee'],'date'=>date('Y-m-d H:i:s')));
	// 					}
	// 				}
	// 			}
				
	// 			Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>(0 - $transferred - $transfer_fees),'warm_wallet_btc'=>$transferred - $transfer_fees,'total_btc'=>(0 - $transfer_fees)));
	// 			echo 'Transferred '.$reserve_surplus.' '.$CFG->currencies[$wallet['c_currency']]['currency'].' to warm wallet. TX: '.$response.PHP_EOL;
	// 		}
	// 	}
	// }
	// elseif ($warm_wallet && $reserve_surplus > $CFG->bitcoin_reserve_min) {
	// 	$bitcoin->walletpassphrase($wallet['bitcoin_passphrase'],3);
	// 	$response = $bitcoin->sendfrom($wallet['bitcoin_accountname'],$warm_wallet,floatval($reserve_surplus));
	// 	$transferred = 0;
	// 	echo $bitcoin->error;
		
	// 	if ($response && !$bitcoin->error) {
	// 		$transferred = $reserve_surplus;
	// 		$transfer_fees = 0;
	// 		$transaction = $bitcoin->gettransaction($response);
			
	// 		foreach ($transaction['details'] as $detail) {
	// 			if ($detail['category'] == 'send') {
	// 				$detail['fee'] = round(abs($detail['fee']),8,PHP_ROUND_HALF_UP);
	// 				if ($detail['fee'] > 0) {
	// 					$transfer_fees += $detail['fee'];
	// 					db_insert('fees',array('fee'=>$detail['fee'],'date'=>date('Y-m-d H:i:s')));
	// 				}
	// 			}
	// 		}
			
	// 		Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>(0 - $transferred - $transfer_fees),'warm_wallet_btc'=>$transferred - $transfer_fees,'total_btc'=>(0 - $transfer_fees)));
	// 		echo 'Transferred '.$reserve_surplus.' '.$CFG->currencies[$wallet['c_currency']]['currency'].' to warm wallet. TX: '.$response.PHP_EOL;
	// 	}
    // }
}
}

db_update('status',1,array('cron_receive_ethereum'=>date('Y-m-d H:i:s')));

echo 'done'.PHP_EOL;
