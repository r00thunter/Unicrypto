<?php
class Currencies {
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
	
		$return = array('crypto'=>$main_crypto,'fiat'=>$main_fiat);	
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
}