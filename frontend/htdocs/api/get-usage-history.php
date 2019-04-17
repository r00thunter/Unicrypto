<?php

include 'include-me.php';


if (isset($_POST['user_id'])) {
	UserController::getUsageHistory($_POST['user_id']);
} else {
	
	$res = array();
	$res['status'] = false;
	$res['message'] = 'user_id is required';
	return helper::toJson($res);

}