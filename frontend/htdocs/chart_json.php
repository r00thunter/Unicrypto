<?php
$currency_id = $_GET['currency'];
$conn = new mysqli("localhost","root","xchange123","bitexchange_cash");
$sql = "SELECT A.date,A.btc_price,B.currency,A.btc_before,A.btc_after FROM `transactions` A,`currencies` B WHERE A.c_currency = B.id AND c_currency = $currency_id";
$my_query = mysqli_query($conn,$sql);

$temp = array();
while ($row = mysqli_fetch_assoc($my_query)) {
	$row['date'] = strtotime($row['date']);
	$temp[] = $row;
}

echo '{ "Data" : '.json_encode($temp).'}';

?>