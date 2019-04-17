<?php
include 'include-me.php';

$conn = DB::connect();

if (isset($_GET['referral_code'])) {
	$referral_code = $_GET['referral_code'];
	$sql = "SELECT * FROM users WHERE referral_code = '$referral_code'";
	$query = mysqli_query($conn,$sql);
	$data = mysqli_fetch_assoc($query);

	if ($data) {
		helper::toJson(['status'=>true,'message'=>'valid referral code']);
	}else{
		helper::toJson(['status'=>false]);
	}
}else{
	helper::toJson(['status'=>false,'message'=>'Please provide valid referral code']);
}