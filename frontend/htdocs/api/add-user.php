<?php

include 'include-me.php';

$conn = DB::connect();

if (isset($_POST['user_id'])) {

	if (isset($_POST['referral_id'])) {
		UserController::addNewUser($_POST['user_id'],$_POST['referral_id'],$_POST['username']);
	} else {
		UserController::addNewUser($_POST['user_id'],ZERO,$_POST['username']);
	}
	
	
} else {
	
	$res = array();
	$res['status'] = false;
	$res['message'] = 'user_id is required';
	return helper::toJson($res);

}

