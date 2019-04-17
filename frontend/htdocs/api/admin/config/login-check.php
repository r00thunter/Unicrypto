<?php
session_start();
include 'constants.php';
if(!isset($_SESSION['user_id']) && !isset($_SESSION['token'])) {
	$_SESSION['error'] = 'Please login to access';
	$login_page = BASE_URL . 'api/admin/login.php';
   	header("Location: $login_page");
	die();
}

?>