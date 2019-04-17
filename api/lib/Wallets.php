<?php 
class Wallets {
	public static function getWallet($c_currency=false) {
		global $CFG;
		
		if (!array_key_exists($c_currency,$CFG->currencies))
			return false;
		
		$sql = 'SELECT * FROM wallets WHERE c_currency = '.$c_currency.' LIMIT 0,1';
		$result = db_query_array($sql);
		if ($result)
			return $result[0];
		else
			return false;
	}
	
	public static function sumFields($wallet_id,$fields) {
		global $CFG;
	
		if (!is_array($fields) || empty($fields) || empty($wallet_id))
			return false;
	
		$set = array();
		foreach ($fields as $field => $sum_amount) {
			if (!is_numeric($sum_amount))
				continue;
	
			$set[] = $field.' = '.$field.' + ('.$sum_amount.')';
		}
	
		$sql = 'UPDATE wallets SET '.implode(',',$set).' WHERE id = '.$wallet_id;
		return db_query($sql);
	}
}
?>