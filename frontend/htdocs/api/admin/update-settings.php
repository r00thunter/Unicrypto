<?php 

include '../config/constants.php';


if (!isset($_GET['value'])) {
	return false;
}
$conn = new mysqli(HOST,DB_USER,DB_PASS,DB);

$value = $_GET['value'];
$sql = "UPDATE settings SET is_referral = $value WHERE status = 1";
$query = mysqli_query($conn,$sql);

return true;