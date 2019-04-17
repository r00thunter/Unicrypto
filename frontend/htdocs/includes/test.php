<?php

header("content-type:application/json");
header('Access-Control-Allow-Origin: *');  

$data = array();

$data[] = array("date"=>'2015-12-24', "value" => 511.53, "volume" => 12.34);
$data[] = array("date"=>'2015-12-25', "value" => 515.53, "volume" => 14.34);
$data[] = array("date"=>'2015-12-26', "value" => 513.53, "volume" => 16.34);


echo '{ "data" : '.json_encode($data).'}';

?>