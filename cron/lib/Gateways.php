<?php 
class Gateways {
	public static $gateways;
	
	public static function get() {
		if (is_array(self::$gateways))
			return self::$gateways;
			
		$sql = 'SELECT * FROM gateways';
		$result = db_query_array($sql);
		if (!$result)
			return false;

		$return = array();
		foreach ($result as $gateway) {
			$return[$gateway['key']] = $gateway;
		}
		self::$gateways = $return;
		return $return;
	}
	
	public static function getEscrows() {
		$sql = 'SELECT * FROM gateway_escrows';
		return db_query_array($sql);
	}
	
	public static function updateEscrows($currencies_balances,$sum=false) {
		global $CFG;
	
		if (empty($currencies_balances) || !is_array($currencies_balances))
			return false;
	
		$currencies_str = '(CASE currency ';
		$currency_ids = array();
		foreach ($currencies_balances as $curr_abbr => $balance) {
			$curr_info = $CFG->currencies[strtoupper($curr_abbr)];
			$currencies_str .= ' WHEN '.$curr_info['id'].' THEN '.(($sum) ? 'balance + ' : '').' ('.$balance.') ';
			$currency_ids[] = $curr_info['id'];
		}
		$currencies_str .= ' END)';
	
		$sql = 'UPDATE gateway_escrows SET balance = '.$currencies_str.', last_update = "'.date('Y-m-d H:i:s').'" WHERE currency IN ('.implode(',',$currency_ids).')';
		$result = db_query($sql);
	
		if (!$result || $result < count($currencies_balances)) {
			$sql = 'SELECT currency FROM gateway_escrows WHERE currency IN ('.implode(',',$currency_ids).')';
			$result = db_query_array($sql);
			$existing = array();
			if ($result) {
				foreach ($result as $row) {
					$existing[] = $row['currency'];
				}
			}
				
			foreach ($currencies_balances as $curr_abbr => $balance) {
				$curr_info = $CFG->currencies[strtoupper($curr_abbr)];
				if (in_array($curr_info['id'],$existing))
					continue;
	
				$sql = 'INSERT INTO gateway_escrows (balance,currency,last_update) VALUES ('.$balance.','.$curr_info['id'].',"'.date('Y-m-d H:i:s').'") ';
				$result = db_query($sql);
			}
		}
		return $result;
	}
	
	public static function processDeposits() {
		global $CFG;
		
		$ignore = array('crypto-capital');
		
		$gateways = self::get();
		if (!$gateways)
			return false;
		
		foreach ($gateways as $key => $gateway) {
			if (in_array($key,$ignore) || $gateway['is_active'] != 'Y')
				continue;
			
			if ($key == 'astropay') {
				
			}
		}
	}
	
	public static function processWithdrawals() {
	
	}
	
	public static function findEscrows() {
	
	}
}
?>