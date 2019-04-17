
<?php 
include '/var/www/html/frontend/lib/common.php';

$email=$_GET['email'];
API::add('User','get_siteuser_id',array($email));
$query = API::send();
$site_user_id=$query['User']['get_siteuser_id']['results'][0];
if($site_user_id!='')
{
	echo $site_user_id;
}
else
{
	echo 0;
}
?>