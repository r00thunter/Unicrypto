<?php

error_reporting(0);
date_default_timezone_set("Asia/Kolkata");
header('Content-Type: application/json');
include 'constants.php';

/**
* 
*/
class DB
{
	
	function __construct()
	{
		# code...
	}

	public static function connect()
	{
		return new mysqli(HOST,DB_USER,DB_PASS,DB);
	}

	public static function timestamp()
	{
		return date('Y-m-d H:i:s');
	}
}


/**
* 
*/
class config extends DB
{
	
	function __construct()
	{
		# code...
	}

	public static function timestamp()
	{
		return date('Y-m-d H:i:s');
	}

	public static function randomString()
	{
		$length = 16;
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;

	}
}

?>
