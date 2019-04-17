<?php
session_start();
session_destroy();
header('Location: http://bitexchange.cash/api/admin/login.php');
?>