<?php
// session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
include '/var/www/html/frontend/lib/common.php';

$symbol=$_REQUEST['symbol'];
echo $_SESSION['LANG']=$symbol;
?>