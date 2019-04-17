<?php 
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
include '../lib/common.php';

$session = User::checkAuth();

$no = $_REQUEST['account_number'];
$desc = !$_REQUEST['description'] ? "Back Account" : $_REQUEST['description'];
$cur = $_REQUEST['currency'];
if(!$no){
    echo json_encode(array('error'=>'Account no is required','status'=>500,));
    exit;
}
if(!$cur){
    echo json_encode(array('error'=>'Currency is required','status'=>500));
    exit;
}
$account = User::getInfo($session[0]['session_id']);
$info = User::setInfo($account);
$CFG->session_active = true;
$already = BankAccounts::find($no);
if($already){
    echo json_encode(array('message'=>'This account no already exist.','status'=>200));
    exit;
}
$account = BankAccounts::insert($no, $cur, $desc);
echo json_encode(array('message'=>'Successfully added your account number','status'=>200));

?>