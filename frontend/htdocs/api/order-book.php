<?php

$uri = $_SERVER['REQUEST_URI'];
$host = explode('api/',$uri);

$endpoint = $host[1];

// error_reporting(E_ALL); ini_set('display_errors', 1);
    include '/var/www/html/frontend/lib/common.php';

    include '/var/www/html/frontend/htdocs/api/index.php';

?>