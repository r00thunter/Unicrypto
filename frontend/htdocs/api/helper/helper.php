<?php

class helper 
{
	
	function __construct()
	{
		# code...
	}

	public static function generateReferral() {
		$length = 10;
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    $randomString = strtoupper($randomString);
	    return $randomString;
	}

	public static function getPointValue()
	{
		$conn = DB::connect();
		$sql = "SELECT one_point_value FROM settings WHERE status = 1";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);
		return $data['one_point_value'];
	}

	public static function updateBonus($from,$to)
	{
		$conn = DB::connect();
		try {
			// insert referral history
			$status = ONE;
			$timestamp = config::timestamp();
			$sql = "INSERT INTO `referral_history`
					(`user_id`, `referral_code`, `status`, `created_at`, `updated_at`) 
					VALUES ('$to','$from','$status','$timestamp','$timestamp')";
			$execute = mysqli_query($conn,$sql);
			// end of referral history
			$current_bonus = self::getUserBonus($from);
			$ref_bonus = self::getReferrarEarn();
			$update_bonus = (float) $current_bonus + (float) $ref_bonus;

			$update_sql = "UPDATE `users` SET `bonous_point`= '$update_bonus' WHERE referral_code = '$from'";
			$execute = mysqli_query($conn,$update_sql);
			return true;
		} catch (Exception $e) {
			return false;
		}

	}

	public static function getReferrarEarn()
	{
		# code... referrar_earn
		$conn = DB::connect();
		$sql = "SELECT referrar_earn FROM settings WHERE status = 1";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);
		return $data['referrar_earn'];
	}


	public static function getBonus()
	{
		$conn = DB::connect();
		$sql = "SELECT default_bonus FROM settings WHERE status = 1";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);
		return $data['default_bonus'];
	}

	public function validate_referral($referred_by,$user_id)
	{
		$check_sql = "SELECT * FROM users WHERE referral_code = '$referred_by' AND user_id != '$user_id'"; 
			$execute = mysqli_query($conn,$check_sql);
			$data = mysqli_fetch_assoc($execute);
			if (count($data) == 0) {
				helper::toJson(['status'=>false,'message'=>'Please provide valid referral code']);
				exit();
			}else{
				return true;
			}
	}

	public static function getReferredBonus()
	{
		$conn = DB::connect();
		$sql = "SELECT referred_bonus,default_bonus FROM settings WHERE status = 1";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);
		$bonus = $data['referred_bonus'] + $data['default_bonus'];
		return $bonus;
	}

	public static function getUserBonus($user_id)
	{
		$conn = DB::connect();
		$sql = "SELECT bonous_point FROM users WHERE referral_code = '$user_id'";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);
		return $data['bonous_point'];
	}

	public static function toJson($arr)
	{
		echo json_encode($arr);exit;
	}


}
?>