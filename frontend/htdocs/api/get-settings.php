<?php
include 'include-me.php';

$conn = DB::connect();

$sql = "SELECT * FROM settings WHERE status = 1";
$query = mysqli_query($conn,$sql);
$data = mysqli_fetch_assoc($query);

helper::toJson($data);

?>