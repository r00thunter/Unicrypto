<?php

/**
* user controller 
*/
class UserController
{
	
	function __construct()
	{
		# code...
	}

	public static function query_execute($sql)
	{
		$conn = DB::connect();
		$query = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc();
		return $data;
	}

	public static function getUsageHistory($user_id)
	{
		$data = self::query_execute("SELECT * FROM use_referral WHERE user_id = '$user_id'");
		return helper::toJson(['status'=>true,'data'=>$data]);
	}

	public static function useBonus($user_id,$trans_id,$amount)
	{
		$conn = DB::connect();
		$status = ONE;
		$timestamp = config::timestamp();
		$sql = "INSERT INTO `use_referral`( `user_id`, `trans_id`, `point`, `status`, `created_at`, `updated_at`) VALUES ('$user_id','$trans_id','$amount','$status','$timestamp','$timestamp')";
		$execute = mysqli_query($conn,$sql);
		$point_value = helper::getPointValue(); 
		$amount = $amount * $point_value;
		$current_bonus = helper::getUserBonus($user_id);

		if ($current_bonus < $amount) {
			return helper::toJson(['status'=>false,'message'=>'User not have enough bonus']);
		}
		$update_bonus = (float) $current_bonus - (float) $amount;
		$update_sql = "UPDATE `users` SET `bonous_point`= '$update_bonus' WHERE username = '$user_id'";	
			$execute = mysqli_query($conn,$update_sql);
		$update_sql = "UPDATE `users` SET `bonous_point`= '$update_bonus' WHERE user_id = '$user_id'";	
			$execute = mysqli_query($conn,$update_sql);

			return helper::toJson(['status'=>true,'message'=>'Bonus debited successfully']);
	}

	public static function getUserBonus($user_id)
	{
		$conn = DB::connect();
		$sql = "SELECT * FROM users WHERE referral_code = '$user_id'";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);
		if (count($data) != 0) {
			$res = array();
			$res['status'] = true;
			$res['data'] = $data;
			helper::toJson($res);
		} else {
			$res = array();
			$res['status'] = false;
			$res['message'] = 'User not found';
			helper::toJson($res);
		}
	}

	public static function getUserBonusByName($user_id)
	{
		$conn = DB::connect();
		$sql = "SELECT * FROM users WHERE username = '$user_id'";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);

		$sql = "SELECT * FROM settings";
		$execute = mysqli_query($conn,$sql);
		$settings = mysqli_fetch_assoc($execute);

		if (count($data) != 0) {
			$res = array();
			$res['status'] = true;
			$res['data'] = $data;
			$res['settings'] = $settings;
			helper::toJson($res);
		} else {
			$res = array();
			$res['status'] = false;
			$res['message'] = 'User not found';
			$res['messages'] = $sql;
			helper::toJson($res);
		}
	}

	

	public static function getUserBonusByEmail($user_id)
	{
		$conn = DB::connect();
		$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);
		if (count($data) != 0) {
			$res = array();
			$res['status'] = true;
			$res['data'] = $data;
			helper::toJson($res);
		} else {
			self::addNewUser($user_id,0,"");
			$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);
			// $res = array();
			// $res['status'] = false;
			// $res['message'] = 'User not found';
			// helper::toJson($res);
			$res = array();
			$res['status'] = true;
			$res['data'] = $data;
			helper::toJson($res);
		}
	}

	public static function addNewUser($user_id,$referred_by,$username)
	{

		$debug = true;
		$conn = DB::connect();
		$referral_code = helper::generateReferral();

		
		// if ($referred_by) {
		// 	echo 'referred_by is present';
		// } else {
		// 	echo 'referred_by is not  present';
		// }
		// echo $referred_by; exit;

		if ($referred_by) {
			
			//helper::validate_referral($referred_by,$user_id);
			$bonus_amount = helper::getReferredBonus();
			$update_status = helper::updateBonus($referred_by,$user_id);
		}else{
			$bonus_amount = helper::getBonus();
		}
		$sql = "SELECT user_id FROM users WHERE user_id = $user_id";
		$execute = mysqli_query($conn,$sql);
		$data = mysqli_fetch_assoc($execute);

		if (count($data) != 0) { 
			$res = array();
			$res['status'] = false;
			$res['message'] = 'User already exists';
			helper::toJson($res);
		} else {			
			
			$timestamp = DB::timestamp();
			$token = config::randomString();
			$status = ONE;
			$ins_sql = "INSERT INTO `users`(`user_id`,`username`,`referral_code`, `token`, `bonous_point`,`referred_by`, `status`, `created_at`, `updated_at`) VALUES ('$user_id','$username','$referral_code','$token','$bonus_amount','$referred_by','$status','$timestamp','$timestamp')";
			try {
				$execute = mysqli_query($conn,$ins_sql);
				$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
				$ex = mysqli_query($conn,$sql);
				$data = mysqli_fetch_assoc($ex);
				//$data = json_encode($data);
				helper::toJson(['status'=>true,'message'=>'User created successfully','data'=>$data]);
			} catch (Exception $e) {
				helper::toJson(['status'=>false,'message'=>'Unable to process try again']);
			}

		}
		
		
	}
}
?>