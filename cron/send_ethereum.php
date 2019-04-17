#!/usr/bin/php
<?php
include 'common.php';
require_once 'vendor/autoload.php';
use Achse\GethJsonRpcPhpClient\JsonRpc\Client;
use Achse\GethJsonRpcPhpClient\JsonRpc\GuzzleClient;
use Achse\GethJsonRpcPhpClient\JsonRpc\GuzzleClientFactory;
use Achse\GethJsonRpcPhpClient\Utils;
echo date('Y-m-d H:i:s').' Beginning Crypto Withdrawals processing...'.PHP_EOL;

$cryptos = Currencies::getCryptos();
$sql = "SELECT requests.currency, requests.site_user, requests.amount, requests.send_address, requests.id, site_users_balances.balance, site_users_balances.id AS balance_id FROM requests LEFT JOIN site_users ON (requests.site_user = site_users.id) LEFT JOIN site_users_balances ON (site_users_balances.site_user = requests.site_user AND site_users_balances.currency = requests.currency) WHERE requests.request_status = {$CFG->request_pending_id} AND requests.currency IN (".implode(',',$cryptos).") AND requests.request_type = {$CFG->request_withdrawal_id}".(($CFG->withdrawals_btc_manual_approval == 'Y') ? " AND (requests.approved = 'Y' OR site_users.trusted = 'Y')" : '');
echo $sql."\n";
$result = db_query_array($sql);
if (!$result) {
	echo "NO RESULTS FOUND \n" ;
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
        $guzzleClt = new GuzzleClient(new GuzzleClientFactory(), $wallet['bitcoin_host'],$wallet['bitcoin_port']);
        $ethereum = new Client($guzzleClt);
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

                if(!empty($row['transaction_id'])){
                    //If transaction receipt is not null implies transaction has added to blockchain.
                    //Get the gasUsed for transaction and calculate unused fee 
                    //Transfer the unused fee to the coinbase account if sufficient amount is present (0.1 ether here)
                    $txnReceiptResult = $ethereum->callMethod('eth_getTransactionReceipt',[$row['transaction_id']]) ;
                    if(!empty($txnReceiptResult->error)){
                        echo $response->error->message."\n";
                        continue ;
                    }else{
                        $txnReceipt = $txnReceiptResult->result ;
                        if(!empty($txnReceipt) && !empty($txnReceipt['blockNumber'])){
                            $gasUsed = $txnReceipt['gasUsed'] ;
                            $coinbaseAddr = getCoinbaseAddress() ;
                            if(!$coinbaseAddr){
                                echo "Coinbase Address is not Found, cannot transfer Escrow balance to owner" ;
                                continue ;
                            }else{
                                $unUsedGasInEther = calcUnusedGas($gasUsed, $fee) ;
                                $sqlUnspent =  "SELECT * from eth_escrow_unspent_balances WHERE site_user = ".$row['site_user']." AND currency = ".$row['currency'];
                                $resultUnspent = db_query_array($sqlUnspent) ;
                                if(!$resultUnspent){
                                    $totalUnspent = $unUsedGasInEther ;
                                }else{
                                    $rowUnspent = $resultUnspent[0] ;
                                    $totalUnspent = bcadd($unUsedGasInEther, $rowUnspent['unspent_balance']) ;
                                }
                                //If unspent escrow balance is greater that 0.1 transfer to the coinbase account
                                //For this transaction we are giving a fixed blockchain fee of 0.0001
                                //TODO What will happen to the unspent fee charged ?
                                if($totalUnspent >= 0.1){
                                    if(!$coinbaseAddr)
                                        continue ;
                                    transferETH($row['site_user'],$coinbaseAddr, $totalUnspent, 0.0001 ) ;
                                    Status::updateEscrows(array($wallet['c_currency']=>($totalUnspent-0.0001)));
                                    
                                }else{
                                //If unspent escrow balance is lessthan 0.1 update the unspent escrow balances table ;
                                   if(!$resultUnspent){
                                       db_insert('eth_escrow_unspent_balances',array('unspent_balance'=>$totalUnspent, 'site_user'=>$row['site_user'], 'currency'=>$row['currency'])) ;
                                   }else{
                                        db_update('eth_escrow_unspent_balances',array('unspent_balance'=>$totalUnspent)) ;
                                   }
                                }
                                $updates = array() ;
                                $updates['request_status'] = $CFG->request_completed_id ;							
                                db_update('requests',$row['id'],$updates);
                                User::updateBalances($row['site_user'],array($wallet['c_currency']=>(-1 * $row['amount'])),true);							
                                Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>(0 - $row['amount'] + $unUsedGasInEther ),'total_btc'=>(0 - $row['amount'] + $unUsedGasInEther)));	
                            }
                        }
                    }
                }else{
                    // check if user sending to himself
                    $addr_info = BitcoinAddresses::getAddress($row['send_address'],$wallet['c_currency']);
                    if (!empty($addr_info['site_user']) && $addr_info['site_user'] == $row['site_user']) {
                        db_update('requests',$row['id'],array('request_status'=>$CFG->request_completed_id));
                        continue;
                    }

                    echo "IN SEND BITCOIN" ;
                    // check if hot wallet has enough to send
                    $pending += $row['amount'];
                    echo "AVAILABLE = ".$available."\n" ;
                    echo "AMOUNT = ".$row['amount']."\n" ;			
                    if ($row['amount'] > $available)
                        continue;
                    echo bcsub($row['amount'],$wallet['bitcoin_sending_fee'],8)."\n" ;
                    echo $row['currency']."\n" ;
                    if (bcsub($row['amount'],$wallet['bitcoin_sending_fee'],8) > 0) {
                        if($row['currency'] == 45){
                                echo "IN ETHEREUM LOOP ELSE 2 \n" ;
                                $txnId = transferETH($row['site_user'], $sendAddress, $row['amoount'], $wallet['bitcoin_sending_fee']) ; 
                                if (!$txnId){
                                    echo "Erron in Withdrawing Ethereum \n" ;
                                    continue ;
                                }
                                else {
                                    echo "REQUEST ID = ".$row["id"]."\n" ;
                                    echo "Status = ".$CFG->request_completed_id."\n";
                                //	db_update('requests',$row['id'],array('request_status'=>$CFG->request_completed_id));
                                    echo $CFG->currencies[$wallet['c_currency']]['currency'].' Transactions sent: '.$txnId;
                                    $updates = array() ;
                                    $updates['transaction_id'] = $txnId ;
                                    $updates['request_status'] = $CFG->request_pending_id ;							
                                    db_update('requests',$row['id'],$updates);							
                                //   $transaction = $ethereum->callMethod('eth_getTransaction',[$response->result]);
                                //  User::updateBalances($row['site_user'],array($wallet['c_currency']=>(-1 * $row['amount'])),true);							
                                // Wallets::sumFields($wallet['id'],array('hot_wallet_btc'=>(0 - $row['amount']),'total_btc'=>(0 - $row['amount'])));
                                }
                            $available = bcsub($available,$row['amount'],8);
                        }
                    }
                }
                
                
                
                
                $requests[] = $row['id'];
               
            }
          //TODO : What if withdrawal amount is greater the hot wallet balance
        }
    }
	
	
	
//	if (empty($pending)) db_update('wallets',$wallet['id'],array('deficit_btc'=>'0'));
}

function getCoinbaseAddress(){
    $addrResult = $ethereum->callMethod('eth_coinbase',[]) ;
    if(!empty($addrResult->error)){
        echo $addrResult->error->message ;
        return false ;
    }else{
        return $addrResult->result ;
    }
}

//Returns transactionId if transaction is succesfull, else returns false
function transferETH($userId, $sendAddress, $totalAmount, $fees_charged){
    echo "IN ETHEREUM LOOP ELSE \n" ;
    $fromAddresses = BitcoinAddresses::get(false,45,false,false,$userId) ;
    echo "IN ETHEREUM LOOP ELSE 2 \n" ;
    $fromAddress = $fromAddresses[0]['address'] ;
    $txn->from = $fromAddress ;
    $txn->to = $sendAddress ;
    $amount = bcsub(totalAmount,$fees_charged,8);
    $etherValue = '0x'.base_convert(bcmul($amount,'1000000000000000000'),10,16);
    $txn->value = $etherValue;


    echo "IN TRANSCTION ELSE \n" ;

    //Set gas price
    $gasPriceResult = $ethereum->callMethod("eth_gasPrice",[]) ;
    $gasPrice = $gasPriceResult->result ;
    $txn->gas = '0x'.base_convert(bcdiv(bcmul($fees_charged,'1000000000000000000'),Utils::bigHexToBigDec($gasPrice)),10,16 );
    echo "txnGas = ".$txn->gas."    \n" ;

    $response= $ethereum->callMethod('personal_sendTransaction',[$txn,$fromAddresses[0]['address_key']]);
    
    if (!empty($response->error)){
        echo $response->error->message."\n";
        return false ;
    }
    else {
        echo "REQUEST ID = ".$row["id"]."\n" ;
        echo "Status = ".$CFG->request_completed_id."\n";
        echo $CFG->currencies[$wallet['c_currency']]['currency'].' Transactions sent: '.$response->result;
        $request_id = db_insert('requests',array('date'=>date('Y-m-d H:i:s'),'site_user'=>User::$info['id'],'currency'=>$currency,'amount'=>$amount,'description'=>$CFG->withdraw_btc_desc,'request_status'=>$status,'request_type'=>$CFG->request_withdrawal_id,'send_address'=>$btc_address,'fee'=>$wallet['bitcoin_sending_fee'],'net_amount'=>($amount - $wallet['bitcoin_sending_fee'])));
        db_insert('ethereum_log', array('transaction_id'=> $response->result, 'amount'=>$amount,'date'=> date('Y-m-d H:i:s'), 'fromAddress'=>$fromAddress,'toAddress'=>$sendAddress)) ;
        return $response->result ;
    }
}

function calcUnusedGas($gasUsed, $fees_charged){
    //$gasUsed is in gas units
    //$fees_charged is in ether units
    $gasPriceResult = $ethereum->callMethod("eth_gasPrice",[]) ;
    $gasPriceHex = $gasPriceResult->result ;
    $gasPriceDec = Utils::bigHexToBigDec($gasPriceHex) ;
    $gasUsedDec = Utils::bigHexToBigDec($gasUsed) ;

    $feeSpentInWei = bcmul($gasUsedDec,$gasPriceDec) ;
    $feeSpentInEther = bcdiv($feeSpentInWei,'1000000000000000000',9) ;
    $unSpentEther = bcsub($fees_charged,$feeSpentInEther,9) ;
    return $unSpentEther ;  
}
db_update('status',1,array('cron_send_bitcoin'=>date('Y-m-d H:i:s')));

echo 'done'.PHP_EOL;