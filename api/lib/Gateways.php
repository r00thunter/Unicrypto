<?php 
use Omnipay\Omnipay;
class Gateways {
	public static $gateways;
	
	public static function get($type=false) {
		global $CFG;
		
		$sql = 'SELECT gateways.*, gateway_types.key AS type_key, gateway_types.name_'.$CFG->language.' AS type_name FROM gateways LEFT JOIN gateway_types ON (gateways.gateway_type = gateway_types.id) WHERE gateways.is_active = "Y" ';
		if ($type && is_string($type))
			$sql .= ' AND gateway_types.key = "'.$type.'" ';
		if ($type && is_numeric($type))
			$sql .= ' AND gateways.gateway_type = '.$type.' ';
		
		$result = db_query_array($sql);
		$return = array();
		if ($result) {
			foreach ($result as $row) {
				$return[$row['id']] = $row;
			}
		}
		
		return $return;
	}
	
	public static function getTypes() {
		global $CFG;
		
		$sql = 'SELECT * FROM gateway_types';
		$result = db_query_array($sql);
		
		return $result;
	}
	
	public static function getCards() {
		global $CFG;
	
		$sql = 'SELECT * FROM gateway_card_types ORDER BY id ASC';
		$result = db_query_array($sql);
		$return = array();
		if ($result) {
			foreach ($result as $row) {
				$return[$row['id']] = $row;
			}
		}
	
		return $return;
	}
	
	public static function depositPreconditions($gateway_type1,$gateway_currency1,$gateway_amount1,$gateway_id1,$card_type1,$card_name1,$card_number1,$card_expiration_month1,$card_expiration_year1,$card_cvv1,$card_email1,$card_phone1,$card_address11,$card_address21,$card_city1,$card_state1,$card_country1,$card_zip1,$gateway_user1,$gateway_pass1,$gateway_bank_account1,$gateway_bank_iban1,$gateway_bank_swift1,$gateway_bank_name1,$gateway_bank_city1,$gateway_bank_country1) {
		global $CFG;
		
		if (!$CFG->session_active)
			return false;
		
		$gateway_type1 = preg_replace("/[^a-z_]/","",$gateway_type1);
		$gateway_currency1 = preg_replace("/[^0-9]/","",$gateway_currency1);
		$gateway_amount1 = Stringz::currencyInput($gateway_amount1);
		$card_type1 = preg_replace("/[^0-9]/","",$card_type1);
		$card_name1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u", "",$card_name1);
		$card_number1 = preg_replace("/[^0-9]/", "",$card_number1);
		$card_expiration_month1 = preg_replace("/[^0-9]/","",$card_expiration_month1);
		$card_expiration_year1 = preg_replace("/[^0-9]/","",$card_expiration_year1);
		$card_cvv1 = preg_replace("/[^0-9]/", "",$card_cvv1);
		$card_email1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_email1);
		$card_phone1 = preg_replace("/[^0-9]/","",$card_phone1);
		$card_address11 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_address11);
		$card_address21 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_address21);
		$card_city1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_city1);
		$card_state1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_state1);
		$card_country1 = preg_replace("/[^0-9]/","",$card_country1);
		$card_zip1 = preg_replace("/[^0-9]/","",$card_zip1);
		$gateway_id1 = preg_replace("/[^0-9]/","",$gateway_id1);
		$gateway_user1 = preg_replace($CFG->pass_regex,"",$gateway_user1);
		$gateway_pass1 = preg_replace($CFG->pass_regex,"",$gateway_pass1);
		$gateway_bank_account1 = preg_replace("/[^0-9]/","",$gateway_bank_account1);
		$gateway_bank_iban1 = preg_replace("/[^0-9]/","",$gateway_bank_iban1);
		$gateway_bank_swift1 = preg_replace("/[^0-9]/","",$gateway_bank_swift1);
		$gateway_bank_name1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$gateway_bank_name1);
		$gateway_bank_city1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$gateway_bank_city1);
		$gateway_bank_country1 = preg_replace("/[^0-9]/","",$gateway_bank_country1);
		
		if (!$gateway_type1)
			return array('error'=>array('message'=>Lang::string('gateway-invalid-type'),'code'=>'GATEWAY_INVALID_TYPE'));
		
		$result = self::get($gateway_type1);
		if (!$result)
			return array('error'=>array('message'=>Lang::string('gateway-invalid-type'),'code'=>'GATEWAY_INVALID_TYPE'));
		
		if (empty($CFG->currencies[$gateway_currency1]))
			return array('error'=>array('message'=>Lang::string('gateway-invalid-currency'),'code'=>'GATEWAY_INVALID_CURRENCY'));
		
		if ($gateway_amount1 < 1)
			return array('error'=>array('message'=>Lang::string('gateway-invalid-amount'),'code'=>'GATEWAY_INVALID_AMOUNT'));
		
		if ($gateway_type1 == 'credit_card') {
			if (!$card_type1)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-type'),'code'=>'GATEWAY_INVALID_CARD_TYPE'));
			
			$card_type = DB::getRecord('gateway_card_types',$card_type1,0,1);
			if (!$card_type || $card_type['is_active'] != 'Y')
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-type'),'code'=>'GATEWAY_INVALID_CARD_TYPE'));
			
			if (!$card_name1 || strlen($card_name1) < 5)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-name'),'code'=>'GATEWAY_INVALID_CARD_NAME'));
			
			if (!$card_number1 || strlen($card_number1) < 13)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-number'),'code'=>'GATEWAY_INVALID_CARD_NUMBER'));
		
			if (!$card_expiration_month1 || !$card_expiration_year1 || $card_expiration_month1 < 1 || $card_expiration_month1 > 12 || strtotime($card_expiration_year1.'-'.$card_expiration_month1.'-01 00:00:00') < time())
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-expiration'),'code'=>'GATEWAY_INVALID_CARD_EXPIRATION'));
			
			if (!$card_email1 || strlen($card_email1) < 5)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-email'),'code'=>'GATEWAY_INVALID_CARD_EMAIL'));

			if (!$card_phone1 || strlen($card_phone1) < 5)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-phone'),'code'=>'GATEWAY_INVALID_CARD_PHONE'));
			
			if (!$card_address11 || strlen($card_address11) < 5)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-address'),'code'=>'GATEWAY_INVALID_CARD_ADDRESS'));
			
			if (!$card_city1 || strlen($card_city1) < 3)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-city'),'code'=>'GATEWAY_INVALID_CARD_CITY'));

			if (!$card_state1 || strlen($card_state1) < 1)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-state'),'code'=>'GATEWAY_INVALID_CARD_STATE'));
			
			if (!$card_country1 || !($card_country1 > 0))
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-country'),'code'=>'GATEWAY_INVALID_CARD_COUNTRY'));
			
			if (!$card_zip1 || strlen($card_zip1) < 1)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-card-zip'),'code'=>'GATEWAY_INVALID_CARD_ZIP'));
			
			$gateway = DB::getRecord('gateways',$card_type['gateway'],0,1);
			if (!$gateway)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-gateway'),'code'=>'GATEWAY_INVALID_GATEWAY'));
		}
		else if ($gateway_type1 == 'gateway') {
			$gateway = DB::getRecord('gateways',$gateway_id1,0,1);
			if (!$gateway)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-gateway'),'code'=>'GATEWAY_INVALID_GATEWAY'));
			
			if (!$gateway_user || strlen($gateway_user1) < 5)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-user'),'code'=>'GATEWAY_INVALID_USER'));
			
			if (!$gateway_pass || strlen($gateway_pass1) < 3)
				return array('error'=>array('message'=>Lang::string('gateway-invalid-pass'),'code'=>'GATEWAY_INVALID_PASSWORD'));
		}
		return false;
	}
	
	public static function processDeposit($gateway_type1,$gateway_currency1,$gateway_amount1,$gateway_id1,$card_type1,$card_name1,$card_number1,$card_expiration_month1,$card_expiration_year1,$card_cvv1,$card_email1,$card_phone1,$card_address11,$card_address21,$card_city1,$card_state1,$card_country1,$card_zip1,$gateway_user1,$gateway_pass1,$gateway_bank_account1,$gateway_bank_iban1,$gateway_bank_swift1,$gateway_bank_name1,$gateway_bank_city1,$gateway_bank_country1) {
		global $CFG;
		
		if (!$CFG->session_active)
			return false;
		
		$error = self::depositPreconditions($gateway_type1,$gateway_currency1,$gateway_amount1,$gateway_id1,$card_type1,$card_name1,$card_number1,$card_expiration_month1,$card_expiration_year1,$card_cvv1,$card_email1,$card_phone1,$card_address11,$card_address21,$card_city1,$card_state1,$card_country1,$card_zip1,$gateway_user1,$gateway_pass1,$gateway_bank_account1,$gateway_bank_iban1,$gateway_bank_swift1,$gateway_bank_name1,$gateway_bank_city1,$gateway_bank_country1);
		if ($error)
			return $error;
		
		$gateway_type1 = preg_replace("/[^a-z_]/","",$gateway_type1);
		$gateway_currency1 = preg_replace("/[^0-9]/","",$gateway_currency1);
		$gateway_amount1 = Stringz::currencyInput($gateway_amount1);
		$card_type1 = preg_replace("/[^0-9]/","",$card_type1);
		$card_name1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u", "",$card_name1);
		$card_number1 = preg_replace("/[^0-9]/", "",$card_number1);
		$card_expiration_month1 = preg_replace("/[^0-9]/","",$card_expiration_month1);
		$card_expiration_year1 = preg_replace("/[^0-9]/","",$card_expiration_year1);
		$card_cvv1 = preg_replace("/[^0-9]/", "",$card_cvv1);
		$card_email1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_email1);
		$card_phone1 = preg_replace("/[^0-9]/","",$card_phone1);
		$card_address11 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_address11);
		$card_address21 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_address21);
		$card_city1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_city1);
		$card_state1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$card_state1);
		$card_country1 = preg_replace("/[^0-9]/","",$card_country1);
		$card_zip1 = preg_replace("/[^0-9]/","",$card_zip1);
		$gateway_id1 = preg_replace("/[^0-9]/","",$gateway_id1);
		$gateway_user1 = preg_replace($CFG->pass_regex,"",$gateway_user1);
		$gateway_pass1 = preg_replace($CFG->pass_regex,"",$gateway_pass1);
		$gateway_bank_account1 = preg_replace("/[^0-9]/","",$gateway_bank_account1);
		$gateway_bank_iban1 = preg_replace("/[^0-9]/","",$gateway_bank_iban1);
		$gateway_bank_swift1 = preg_replace("/[^0-9]/","",$gateway_bank_swift1);
		$gateway_bank_name1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$gateway_bank_name1);
		$gateway_bank_city1 = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u","",$gateway_bank_city1);
		$gateway_bank_country1 = preg_replace("/[^0-9]/","",$gateway_bank_country1);
		
		if ($gateway_type1 == 'credit_card') {
			$card_type = DB::getRecord('gateway_card_types',$card_type1,0,1);
			$gateway_info = DB::getRecord('gateways',$card_type['gateway'],0,1);
			
			if ($gateway_info['key'] == 'astropay') {
				
			}
			else {
				$gateway = Omnipay::create($gateway_info['key']);
				$gateway->setTestMode(($gateway_info['sandbox_mode'] == 'Y'));

				if ($gateway_info['key'] == 'skrill') {
					$gateway->setEmail($gateway_info['api_key']);
					$gateway->setReturUrl($CFG->frontend_baseurl.'deposit.php');
				}
				
				$params = array(
					'firstName'=>substr($card_name1,0,strrpos($card_name1, ' ')),
					'lastName'=>strrchr($card_name1,' '),
					'number'=>$card_number1,
					'expiryMonth'=>$card_expiration_month1,
					'expiryYear'=>$card_expiration_year1,
					'cvv'=>$card_cvv1,
					'billingAddress1'=>$card_address11,
					'billingAddress2'=>$card_address21,
					'billingCity'=>$card_city1,
					'billingPostcode'=>$card_zip1,
					'billingState'=>$card_state1,
					'billingCountry'=>$card_country1,
					'billingPhone'=>$card_phone1,
					'email'=>$card_email1
				);
				
				$response = $gateway->purchase($params);
				
				// WTF
			}
		}
		else if ($gateway_type1 == 'gateway') {
			$gateway_info = DB::getRecord('gateways',$gateway_id1,0,1);
			if ($gateway_info['key'] == 'Skrill') {
				$gateway->setEmail($gateway_info['api_key']);
				$gateway->setReturUrl($CFG->frontend_baseurl.'deposit.php');
			}
			else if ($gateway_info['key'] == 'Ecopayz') {
				$gateway->setEmail($gateway_info['api_key']);
				$gateway->setReturUrl($CFG->frontend_baseurl.'deposit.php');
			}
		}
		else if ($gateway_type1 == 'bank_account') {
				
		}
	}
}