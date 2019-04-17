<?php

/**
 * 
 */
class TrezorWallet 
{

	public static function getInfo($id=1) {
		global $CFG;


		
		// if (!($session_id > 0) || !$CFG->session_active)
	
		$result = db_query_array('SELECT * FROM trazor_wallets WHERE id = '.$id);
		return $result[0];
	}
}