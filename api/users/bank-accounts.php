<?php 
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
include '../lib/common.php';

$session = User::checkAuth();

$account = User::getInfo($session[0]['session_id']);
$info = User::setInfo($account);
$CFG->session_active = true;

$account = BankAccounts::get();
if($account==false){
    echo json_encode(array('message'=>'No account found','status'=>200));
    exit;
}

echo json_encode($account);
?>