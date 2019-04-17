<?php
class Deposit{
	public static function get($currency_id=false) {
		global $CFG;
		
		if (!$CFG->session_active)
			return false;
		
		$currency_id = preg_replace("/[^0-9]/", "",$currency_id);
		
		$sql = "SELECT deposits.*, currencies.currency AS currency FROM deposits  JOIN currencies ON (deposits.currency = currencies.id) WHERE  site_user = ".User::$info['id'];

		if ($currency_id > 0)
			$sql .= " AND deposits.currency = $currency_id ";

		$result = db_query_array($sql);
		
		if ($result) {
			foreach ($result as $row) {
				$return[$row['transaction_id']] = $row;
			}
			return $return;
		}
		return false;
	}
	
	public static function getRecord($id=false,$account_number=false) {
		global $CFG;
		
		$id = preg_replace("/[^0-9]/", "",$id);
		$account_number = preg_replace("/[^0-9]/", "",$account_number);
		
		if (!$CFG->session_active || !($id > 0 || $account_number > 0))
			return false;
		
		$sql = 'SELECT * FROM deposits WHERE '.(($id > 0) ? " deposit_id = $id " : " transaction_id = $account_number ").' AND site_user = '.User::$info['id'];
		$result = db_query_array($sql);
		
		if ($result)
			return $result[0];
		else
			return false;
	}
	
	public static function find($transaction_id) {
		global $CFG;
		
		if (!$CFG->session_active || !$account_number)
			return false;
		
		$transaction_id = preg_replace("/[^0-9]/", "",$transaction_id);
		
		$sql = "SELECT * FROM deposits WHERE transaction_id = $transaction_id";
		$result = db_query_array($sql);
		
		if ($result)
			return $result[0];
	}
	
	public static function insert($account,$currency,$description=false, $bank_name, $pan_no, $amount) {
		global $CFG;
		
		if (!$CFG->session_active)
			return false;
		
		$transaction = preg_replace("/[^0-9]/", "",$account);
		$currency = preg_replace("/[^0-9]/", "",$currency);
        $description = preg_replace("/[^0-9a-zA-Z!@#$%&*?\.\-\_ ]/",'',$description);
		db_insert('deposits',array('transaction_id'=>$account ,'currency'=>$currency,'description'=>$description,'site_user'=>User::$info['id'], 'bank_name' => $bank_name , 'pan_no'=> $pan_no, 'amount'=>$amount));
	}
	
	public static function delete($remove_id) {
		global $CFG;
		
		if (!$CFG->session_active)
			return false;
		
		$sql = 'SELECT deposit_id FROM deposits WHERE id = '.$remove_id.' AND site_user = '.User::$info['id'];
		$result = db_query_array($sql);
		if (!$result)
			return false;
		
		$remove_id = preg_replace("/[^0-9]/", "",$remove_id);
		
		db_delete('deposits',$remove_id);
	}
}