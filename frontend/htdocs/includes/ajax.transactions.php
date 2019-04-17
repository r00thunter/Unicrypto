<?php
chdir('..');

$ajax = true;
include '../lib/common.php';

$currency1 = (array_key_exists($_REQUEST['currency'],$CFG->currencies)) ? $_REQUEST['currency'] : false;
$c_currency1 = (array_key_exists($_REQUEST['c_currency'],$CFG->currencies)) ? $_REQUEST['c_currency'] : false;
$type1 = preg_replace("/[^0-9]/", "",$_REQUEST['type']);
$order_by1 = preg_replace("/[^a-z]/", "",$_REQUEST['order_by']);
$page1 = preg_replace("/[^0-9]/", "",$_REQUEST['page']);

API::add('Transactions','get',array(0,$page1,30,$c_currency1,$currency1,1,false,$type1,$order_by1,false));
$query = API::send();

$return = $query['Transactions']['get']['results'][0];
echo json_encode($return);