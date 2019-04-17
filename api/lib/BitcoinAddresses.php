<?php
require_once '../vendor/autoload.php';
use Achse\GethJsonRpcPhpClient\JsonRpc\Client;
use Achse\GethJsonRpcPhpClient\JsonRpc\GuzzleClient;
use Achse\GethJsonRpcPhpClient\JsonRpc\GuzzleClientFactory;
class BitcoinAddresses{
	static $bitcoin;
	
	public static function erc20_config()
	{
		$sql = "SELECT * FROM erc20_configuration";
		$result = db_query_array($sql);
		return $result;
	}

	public static function getCurrentUser($currency)
	{
		# code...
		$id = User::$info['id'];
		// $id = 563214;
		$sql = "SELECT * FROM bitcoin_addresses WHERE site_user = $id AND c_currency = $currency ORDER BY id DESC LIMIT 0 , 1";
		$result = db_query_array($sql);
		return $result;
	}

	public static function getBitcoinAddress($c_currency)
	{ 
		$id = User::$info['id'];
		 $sql = "SELECT * FROM bitcoin_addresses WHERE site_user = '".$id."' AND c_currency ='".$c_currency."' ";
		$result = db_query_array($sql);
		return $result;
	}

	public static function getTRXBitcoinAddress($address)
	{ 
		$id = User::$info['id'];
		 $sql = "SELECT * FROM bitcoin_addresses WHERE address ='".$address."' ";
		$result = db_query_array($sql);
		if ($result) {
			 return $result;
		}
		return false;
		
	} 
	
	
	public static function get($count=false,$c_currency=false,$page=false,$per_page=false,$user=false,$unassigned=false,$system=false,$public_api=false) {
		global $CFG;
		
		if (!$CFG->session_active || !(User::$info['id'] > 0))
			return false;
		
		$page = preg_replace("/[^0-9]/", "",$page);
		$per_page = preg_replace("/[^0-9]/", "",$per_page);
		$c_currency = preg_replace("/[^0-9]/", "",$c_currency);
		
		if (empty($CFG->currencies[strtoupper($c_currency)]))
			$c_currency = $CFG->currencies[$main['crypto']]['id'];
		else
			$c_currency = $CFG->currencies[strtoupper($c_currency)]['id'];
		
		$page = ($page > 0) ? $page - 1 : 0;
		$r1 = $page * $per_page;
		$user = User::$info['id'];
		
		if (!$count && !$public_api)
			$sql = "SELECT * FROM bitcoin_addresses WHERE 1 ";
		elseif (!$count && $public_api)
			$sql = "SELECT address,`date` FROM bitcoin_addresses WHERE 1 ";
		else
			$sql = "SELECT COUNT(id) AS total FROM bitcoin_addresses WHERE 1  ";
		
		if ($user > 0)
			$sql .= " AND site_user = $user ";
		
		if ($unassigned)
			$sql .= " AND site_user = 0 ";
		
		if ($system)
			$sql .= " AND system_address = 'Y' ";
		else
			$sql .= " AND system_address != 'Y' ";
		
		if ($c_currency)
			$sql .= ' AND c_currency = '.$c_currency.' ';
		
		if ($per_page > 0 && !$count)
			$sql .= " ORDER BY bitcoin_addresses.date DESC LIMIT $r1,$per_page ";
		
		$result = db_query_array($sql);
		if (!$count)
			return $result;
		else
			return $result[0]['total'];
	}
	
	public static function getNew($c_currency=false,$return_address=false) {
		global $CFG;
		
		if (!$CFG->session_active)
			return false;
		
		$c_currency = preg_replace("/[^0-9]/", "",$c_currency);
		if (!array_key_exists($c_currency,$CFG->currencies))
			return false;
			$wallet = Wallets::getWallet($c_currency);
			if($c_currency == 45){

				$ethAddressExisting= self::get($count=false,45) ;
				if(!empty($ethAddressExisting && count($ethAddressExisting)> 0)){
					$new_address = $ethAddressExisting[0]['address'] ;
				}else{
					$new_addressResult = self::getNewETHAccount($wallet['bitcoin_host'],$wallet['bitcoin_port']) ;
					$new_address = $new_addressResult->result ;
					$addressKey = $new_addressResult->addressKey ;
				}		
			}else{
				require_once('../lib/easybitcoin.php');
				$bitcoin = new Bitcoin($wallet['bitcoin_username'],$wallet['bitcoin_passphrase'],$wallet['bitcoin_host'],$wallet['bitcoin_port'],$wallet['bitcoin_protocol']);
				
				$new_address = $bitcoin->getnewaddress($wallet['bitcoin_accountname']);
			}
			if(!$addressKey)
				$new_id = db_insert('bitcoin_addresses',array('c_currency'=>$c_currency,'address'=>$new_address,'site_user'=>User::$info['id'],'date'=>date('Y-m-d H:i:s')));
			else
				$new_id = db_insert('bitcoin_addresses',array('c_currency'=>$c_currency,'address'=>$new_address,'site_user'=>User::$info['id'],'date'=>date('Y-m-d H:i:s'),'address_key'=>$addressKey));
			    
		
		return ($return_address) ? $new_address : $new_id;
	}
	public static function getNewETHAccount($host, $port){
		global $CFG ;
		// if (!$CFG->session_active)
		// 	return false;	
		$httpClient = new GuzzleClient(new GuzzleClientFactory(), $host, $port);
		$client = new Client($httpClient);
		$addressKey = hash('md5',User::$info['email']) ;
		$result = $client->callMethod('personal_newAccount',[$addressKey]);
		echo 'Address = '.$result->result;
		$result->addressKey = $addressKey ;
		return $result;
		// Run operation (all are described here: https://github.com/ethereum/wiki/wiki/JSON-RPC#json-rpc-methods)	
	}
	
	public static function validateAddress($c_currency=false,$btc_address) {
		global $CFG;
		if($c_currency == 45){
			// $httpClient = new GuzzleClient(new GuzzleClientFactory(), 'localhost', 8555);
			// $client = new Client($httpClient);
			// $result = $client->callMethod('web3_isAddress',[$btc_address]);
			// return $result->result ;
			return true ;
		}else{
			$btc_address = preg_replace("/[^0-9a-zA-Z]/",'',$btc_address);
			$c_currency = preg_replace("/[^0-9]/", "",$c_currency);
			$wallet = Wallets::getWallet($c_currency);
			
			if (!$btc_address || !$c_currency)
				return false;
		
			require_once('../lib/easybitcoin.php');
			$bitcoin = new Bitcoin($wallet['bitcoin_username'],$wallet['bitcoin_passphrase'],$wallet['bitcoin_host'],$wallet['bitcoin_port'],$wallet['bitcoin_protocol']);
			
			$response = $bitcoin->validateaddress($btc_address);
		
			if (!$response['isvalid'] || !is_array($response))
				return false;
			else
				return true;
		}
	}

	// Private API

	public static function get_private_api($count=false,$c_currency=false,$page=false,$per_page=false,$user=false,$unassigned=false,$system=false,$public_api=false,$session_user=false) {
		global $CFG;
		$getin=User::getInfo($session_user);
		$setin=User::setInfo($getin);
				
		// !$CFG->session_active || 
		if (!(User::$info['id'] > 0))
			return false;
		
		$page = preg_replace("/[^0-9]/", "",$page);
		$per_page = preg_replace("/[^0-9]/", "",$per_page);
		$c_currency = preg_replace("/[^0-9]/", "",$c_currency);
		if (empty($CFG->currencies[strtoupper($c_currency)]))
			$c_currency = $CFG->currencies[$main['crypto']]['id'];
		else
			$c_currency = $CFG->currencies[strtoupper($c_currency)]['id'];
		
		$page = ($page > 0) ? $page - 1 : 0;
		$r1 = $page * $per_page;
		$user = User::$info['id'];
		
		if (!$count && !$public_api)
			$sql = "SELECT * FROM bitcoin_addresses WHERE 1 ";
		elseif (!$count && $public_api)
			$sql = "SELECT address,`date` FROM bitcoin_addresses WHERE 1 ";
		else
			$sql = "SELECT COUNT(id) AS total FROM bitcoin_addresses WHERE 1  ";
		
		if ($user > 0)
			$sql .= " AND site_user = $user ";
		
		if ($unassigned)
			$sql .= " AND site_user = 0 ";
		
		if ($system)
			$sql .= " AND system_address = 'Y' ";
		else
			$sql .= " AND system_address != 'Y' ";
		
		if ($c_currency)
			$sql .= ' AND c_currency = '.$c_currency.' ';
		
		if ($per_page > 0 && !$count)
			$sql .= " ORDER BY bitcoin_addresses.date DESC LIMIT $r1,$per_page ";				
		
		$result = db_query_array($sql);
		if (!$count)
			return $result;
		else
			return $result[0]['total'];
	}

	public static function getNew_private_api($c_currency=false,$return_address=false,$session_user=false) {
		global $CFG;		
		$getin=User::getInfo($session_user);
		$setin=User::setInfo($getin);

		$c_currency = preg_replace("/[^0-9]/", "",$c_currency);
		if (!array_key_exists($c_currency,$CFG->currencies))
			return false;
			$wallet = Wallets::getWallet($c_currency);
			if($c_currency == 45){

				$ethAddressExisting= self::get_private_api($count=false,45) ;
				if(!empty($ethAddressExisting && count($ethAddressExisting)> 0)){
					$new_address = $ethAddressExisting[0]['address'] ;
				}else{

					$new_addressResult = self::getNewETHAccount($wallet['bitcoin_host'],$wallet['bitcoin_port']) ;
					$new_address = $new_addressResult->result ;
					$addressKey = $new_addressResult->addressKey ;
					
				}		
			}else{
				require_once('../lib/easybitcoin.php');
				$bitcoin = new Bitcoin($wallet['bitcoin_username'],$wallet['bitcoin_passphrase'],$wallet['bitcoin_host'],$wallet['bitcoin_port'],$wallet['bitcoin_protocol']);
				
				$new_address = $bitcoin->getnewaddress($wallet['bitcoin_accountname']);
			}
			if(!$addressKey)
				$new_id = db_insert('bitcoin_addresses',array('c_currency'=>$c_currency,'address'=>$new_address,'site_user'=>User::$info['id'],'date'=>date('Y-m-d H:i:s')));
			else
				$new_id = db_insert('bitcoin_addresses',array('c_currency'=>$c_currency,'address'=>$new_address,'site_user'=>User::$info['id'],'date'=>date('Y-m-d H:i:s'),'address_key'=>$addressKey));
			    
		
		return ($return_address) ? $new_address : $new_id;
	}

	// End of Private API


	//  new methode for creating address eth and erc20 token
	public static function getNewMethode($c_currency=false,$address=false,$address_key=false) {
			// return array('c_currency'=>$c_currency,'address'=>$address,'site_user'=>User::$info['id'],'date'=>date('Y-m-d H:i:s'),'address_key'=>$address_key);
			// if ($c_currency && $address && $address_key) {
				$new_id = db_insert('bitcoin_addresses',array('c_currency'=>$c_currency,'address'=>$address,'site_user'=>User::$info['id'],'date'=>date('Y-m-d H:i:s'),'address_key'=>$address_key));
				if ($new_id) {
						return true;
				}
				
			// }
			return false;

	}

}