<?php

include 'include-me.php';


if (isset($_POST['user_id'])) {
	if (isset($_POST['name'])) {
		UserController::getUserBonusByName($_POST['user_id']);
	} else {
		UserController::getUserBonusByEmail($_POST['user_id']);
	}

} else {
	
	$res = array();
	$res['status'] = false;
	$res['message'] = 'user_id is required';
	return helper::toJson($res);

}