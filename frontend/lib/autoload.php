<?php 
function classLoader($class_name) {
	require_once ('/var/www/html/frontend/lib/'.$class_name.'.php');
}
spl_autoload_register('classLoader');
