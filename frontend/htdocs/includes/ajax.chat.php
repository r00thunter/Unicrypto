<?php
chdir('..');
$ajax = true;
include '../lib/common.php';

$action = $_REQUEST['action'];
$last_id = (!empty($_REQUEST['last_id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['last_id']) : false;
if ($action == 'read') {
	API::add('Chat','get',array($last_id));
	$query = API::send();
	echo json_encode($query['Chat']['get']['results'][0]);
}
else if ($action == 'new') {
	$message = preg_replace('/[^\pL 0-9a-zA-Z!@#$%&*?\.\-\_\,]/u','',$_REQUEST['message']);
	API::add('Chat','newMessage',array($message));
	$query = API::send();
	echo json_encode($query['Chat']['newMessage']['results'][0]);
}
?>