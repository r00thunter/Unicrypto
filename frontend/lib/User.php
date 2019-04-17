<?php
class User {
	private static $logged_in;
	public static $awaiting_token, $info, $attempts, $timeout , $ekyc_data, $ekyc;
	
	static function logIn($user=false,$pass=false,$email_authcode=false,$email_authcode_request=false) {
		global $CFG;
		
		$ip = API::getUserIp();
		
		$ch = curl_init($CFG->auth_login_url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,array('user'=>$user,'pass'=>$pass,'ip'=>$ip,'email_authcode'=>$email_authcode,'email_authcode_request'=>$email_authcode_request));
		curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
		
		$result1 = curl_exec($ch);
		$result = json_decode($result1,true);
		curl_close($ch);
		
		if (!empty($result['attempts'])) {
			self::$attempts = $result['attempts'];
			$_SESSION['attempts'] = $result['attempts'];
		}
		
		if (!empty($result['timeout'])) {
			self::$timeout = $result['timeout'];
			$_SESSION['timeout'] = $result['timeout'];
		}
		
		if (empty($result) || !empty($result['error'])) {
			return false;
		}
		elseif (!empty($result['message'])) {
			$_SESSION['session_id'] = $result['session_id'];
			$_SESSION['session_key'] = $result['session_key'];
			$_SESSION['nonce'] = $result['nonce'];
			unset($_SESSION['attempts']);
			unset($_SESSION['timeout']);
			return $result;
		}
	}
	
	static function verifyLogin($query) {
		global $CFG;
		
		if (isset($query['User']['verifyLogin']['results'][0]))
			$result = $query['User']['verifyLogin']['results'][0];

		if (!empty($result['attempts']) && (empty($_SESSION['attempts']) || $result['attempts'] > $_SESSION['attempts']))
			self::$attempts = $result['attempts'];
		else if (!empty($_SESSION['attempts']))
			self::$attempts = $_SESSION['attempts'];
		
		if (empty($_SESSION['session_id']))
			return false;

		if (!empty($result['error']) || !empty($query['error']) || !isset($result)) {
			$session_id = session_id();
			if (!empty($session_id)) {
				session_destroy();
				$_SESSION = array();
			}
			return false;
		}
		
		if (!empty($result['message']) && $result['message'] == 'awaiting-token') {
			self::$awaiting_token = true;
			return true;
		}
		else {
			self::$info = $result['info'];
			self::$logged_in = true;
			//self::updateNonce();
			return true;
		}
	}
	
	static function verifyToken($token,$dont_ask=false) {
		global $CFG;
		
		if (!self::$awaiting_token)
			return false;
	
		$commands['session_id'] = $_SESSION['session_id'];
		$commands['nonce'] = $_SESSION['nonce'];
		$commands['token'] = $token;
		$commands['dont_ask'] = $dont_ask;
		$commands['commands'] = json_encode($commands);
		
		openssl_sign($commands['commands'],$signature,$_SESSION['session_key']);
		$commands['signature'] = bin2hex($signature);
	
		$ch = curl_init($CFG->auth_verify_token_url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$commands);
		curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
	
		$result1 = curl_exec($ch);
		$result = json_decode($result1,true);
		curl_close($ch);

		if (!empty($result['authy-errors']['message'])) {
			Errors::add(Lang::string('security-incorrect-token'));
			return false;
		}
		elseif (!empty($result['error'])) {
			Errors::add(Lang::string('security-incorrect-token'));
			return false;
		}
		elseif (empty($result)) {
			Errors::add(Lang::string('security-com-error'));
			return false;
		}
		
		if (!empty($result['message']) && $result['message'] == 'OK') {
			self::$logged_in = true;
			self::updateNonce();
			return true;
		}
	}
	
	static function isLoggedIn() {
		return self::$logged_in;
	}
	
	static function logOut($logout) {
		if ($logout && $_REQUEST['uniq'] == $_SESSION["logout_uniq"]) {
			API::add('User','logOut',array($_SESSION['session_id']));
			API::send();
			
			$lang = $_SESSION['language'];
			unset($_SESSION);
			session_destroy();
			if ($_SERVER['SERVER_NAME'] == 'localhost') {
session_save_path("C:/home/frontend_sessions") ;
} else {
session_save_path("/home/frontend_sessions") ;
}  
			session_start();
			$_SESSION['language'] = $lang;
			
			self::$logged_in = false;
			self::$info = false;
		}
	}
	
	static function updateNonce() {
		if (!self::$logged_in)
			return false;
		
		$_SESSION['nonce']++;
		return true;
	}
	
	static function sendSMS($authy_id=false) {
		global $CFG;
		
		API::add('User','sendSMS',array($authy_id));
		$query = API::send();
		$response = $query['User']['sendSMS']['results'][0];
		
		if (!$response || !is_array($response))
			Errors::add(Lang::string('security-com-error'));
		elseif ($response['success'] === false)
			Errors::merge($response['errors']);
		else {
			return true;
		}
	}
	//Verify eKYC
	static function verfieKYC($query){
		API::add('User','geteKYC');
		API::add('User', 'eKycAuth');
		$query = API::send();
		if(!empty($query['User']['geteKYC']['results']) && !empty($query['User']['geteKYC']['results'][0])){
			self::$ekyc = $query['User']['geteKYC']['results'][0];
			
			$Auth = $query['User']['eKycAuth']['results'][0];
			$data = [
				"customerId" => $query['User']['geteKYC']['results'][0]['customer_id'],
				"merchantId"=> $query['User']['geteKYC']['results'][0]['merchant_id']
			];
			
			
			$data_string = json_encode($data);
		
			$curl = curl_init();
		
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://simple-individual-onboarding.signzy.tech/api/pullmerchants",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30000,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $data_string,
				CURLOPT_HTTPHEADER => array(
					"accept: */*",
					"accept-language: en-US,en;q=0.8",
					"authorization:".$Auth['id'],
					"content-type: application/json"
				),
			));
		
			$response = json_decode(curl_exec($curl));
			$err = curl_error($curl);
		
			curl_close($curl);
			self::$ekyc_data = $response->result;
			return true;
		}else{
			return false ;
		}
	}
}
?>
