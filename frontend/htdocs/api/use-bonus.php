<?php
include 'include-me.php';

if (isset($_POST['user_id'])) {

	if (isset($_POST['trans_id'])) {

		$conn = DB::connect();

		$user_id = $_POST['user_id'];
		$trans_id = $_POST['trans_id'];
		$core_points = $_POST['points'];
		$current_timestamp = date("Y-m-d H:i:s");
		$s_sql = "SELECT id,bonous_point FROM users WHERE username = '$user_id'";
		$q = mysqli_query($conn,$s_sql);
		$user_data = mysqli_fetch_assoc($q);

		$u_id = $user_data['id'];

		

		$s_sql = "SELECT one_point_value FROM settings WHERE status = 1";
		$settings = mysqli_query($conn,$s_sql);
		$settings_data = mysqli_fetch_assoc($settings);

		$points = $_POST['points'] / $settings_data['one_point_value'];

		$sql = "INSERT INTO use_referral (`user_id`,`trans_id`,`point`,`status`,`created_at`,`updated_at`) VALUES ('$u_id','$trans_id',$core_points,1,'$current_timestamp','$current_timestamp')";
		$query = mysqli_query($conn,$sql);

		$old_bonus = $user_data['bonous_point'];
		$new_bonus = $old_bonus - $core_points;
$s_sql = "SELECT id,bonous_point FROM users WHERE username = '$user_id'";
$update_sql = "UPDATE users SET bonous_point = $new_bonus WHERE username = '$user_id'";
$update_query = mysqli_query($conn,$update_sql);

	return helper::toJson(['status'=>true,'message'=>'Bonus point used successfully']);



		UserController::useBonus($_POST['user_id'],$_POST['trans_id'],$_POST['points']);

	} else {
		$res = array();
		$res['status'] = false;
		$res['message'] = 'transcation id is required';
		return helper::toJson($res);
	}
	
	
} else {
	
	$res = array();
	$res['status'] = false;
	$res['message'] = 'user_id is required';
	return helper::toJson($res);

}
?>