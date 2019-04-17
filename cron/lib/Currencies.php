<?php
class Currencies {
	public static function get() {
		$sql = "SELECT * FROM currencies WHERE is_active = 'Y' ORDER BY currency ASC";
		$result = db_query_array($sql);
		
		if ($result) {
			foreach ($result as $row) {
				$currencies[$row['currency']] = $row;
				$currencies[$row['id']] = $row;
			}
		}
		return $currencies;
	}
	
	public static function getRecord($currency_abbr=false,$currency_id=false) {
		if (!$currency_abbr && !$currency_id)
			return false;

		if ($currency_abbr)
			return DB::getRecord('currencies',false,$currency_abbr,0,'currency');
		elseif ($currency_id > 0)
			return DB::getRecord('currencies',$currency_id,false,1);
	}
	
	public static function getMain() {
		global $CFG;
		
		$main_crypto = 0;
		$main_fiat = 0;
		
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
		
		return array('crypto'=>$main_crypto,'fiat'=>$main_fiat);
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
}