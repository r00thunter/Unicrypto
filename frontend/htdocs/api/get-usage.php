<?php
include 'include-me.php';

$conn = DB::connect();


if (!isset($_GET['email'])) {
	return helper::toJson(['status'=>false,'message'=>'Unable to process']);
} else {
	$email = $_GET['email'];
	$user_sql = "SELECT id FROM users WHERE user_id = '$email'";
	$user_query = mysqli_query($conn,$user_sql);
	$data = mysqli_fetch_assoc($user_query);
	$user_id = $data['id'];
	$s_sql = "SELECT * FROM use_referral WHERE user_id = $user_id";
	$s_query = mysqli_query($conn,$s_sql);
	$res_data = mysqli_fetch_assoc($s_query);
	$res = array();
	while ($data = mysqli_fetch_assoc($s_query)) {
		$res[] = $data;
	}
	echo json_encode($res);

}
