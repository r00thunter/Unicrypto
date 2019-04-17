<?php
class Currencies {
	public static function get() {
		global $CFG;
		
		if ($CFG->memcached) {
			$cached = $CFG->m->get('currencies');
			if ($cached) {
				return $cached;
			}
		}

		$sql = "SELECT * FROM currencies WHERE is_active = 'Y'";
		$result = db_query_array($sql);
		if ($result) {
			foreach ($result as $row) {
				$currencies[$row['currency']] = $row;
				$currencies[(string)$row['id']] = $row;
			}
			
			ksort($currencies);
			if ($CFG->memcached)
				$CFG->m->set('currencies',$currencies,60);
		}
		return $currencies;
	}
	
	public static function getRecord($currency_abbr=false,$currency_id=false) {
		if (!$currency_abbr && !$currency_id)
			return false;
		
		$currency_id1 = preg_replace("/[^0-9]/", "",$currency_id);
		$currency_abbr1 = preg_replace("/[^a-zA-Z]/", "",$currency_abbr);

		if ($currency_abbr1)
			return DB::getRecord('currencies',false,$currency_abbr1,0,'currency');
		elseif ($currency_id1 > 0)
			return DB::getRecord('currencies',$currency_id1,false,1);
	}
	
	public static function getMain() {
		global $CFG;
	
		$main_crypto = 0;
		$main_fiat = 0;
		
		if ($CFG->memcached) {
			$cached = $CFG->m->get('currencies_main');
			if ($cached) {
				return $cached;
			}
		}
	
		foreach ($CFG->currencies as $currency_id => $currency) {
			if (!is_numeric($currency_id) || $currency['is_main'] != 'Y')
				continue;
				
			if ($currency['is_crypto'] == 'Y')
				$main_crypto = $currency_id;
			else
				$main_fiat = $currency_id;
		}
	
		if (!$main_crypto)
			$main_crypto = $CFG->currencies['BTC']['id'];
		if (!$main_fiat)
			$main_fiat = $CFG->currencies['USD']['id'];
	
		$return = array('crypto'=>$main_crypto,'fiat'=>$main_fiat);
		if ($CFG->memcached)
			$CFG->m->set('currencies_main',$return,300);
		
		return $return;
	}
	
	public static function getCryptos() {
		global $CFG;
	
		$cryptos = array();
		foreach ($CFG->currencies as $currency_id => $currency) {
			if (!is_numeric($currency_id) || $currency['is_crypto'] != 'Y')
				continue;
	
			$cryptos[] = $currency_id;
		}
		return $cryptos;
	}
	
	public static function getNotConvertible() {
		global $CFG;
	
		$not = array();
		foreach ($CFG->currencies as $currency_id => $currency) {
			if (!is_numeric($currency_id) || $currency['not_convertible'] != 'Y')
				continue;
	
			$not[] = $currency_id;
		}
		return $not;
	}

	public static function getUserBalance($c_currency)
	{ 
		// return "hello";
		$id = User::$info['id'];
		// $id = 2365;
		 $sql = "SELECT balance FROM site_users_balances WHERE site_user = '".$id."' AND currency ='".$c_currency."' ";
		$result = db_query_array($sql);

		if ($result) {
				return $result;
		}
		return false;
	}

	public static function getCurrencies()
	{ 
		// return "hello";
		$id = User::$info['id'];
		// $id = 2365;
		 $sql = "SELECT * FROM `currencies` WHERE `is_active` = 'Y' AND `is_crypto` = 'Y'";
		$result = db_query_array($sql);

		if ($result) {
				return $result;
		}
		return false;
	}
}