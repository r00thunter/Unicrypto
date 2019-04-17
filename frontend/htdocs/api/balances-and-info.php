<?php
if($_POST['api_key']!='')
{
$uri = $_SERVER['REQUEST_URI'];
$host = explode('api/',$uri);

$endpoint = $host[1];

// error_reporting(E_ALL); ini_set('display_errors', 1);
    include '/var/www/html/frontend/lib/common.php';

    include '/var/www/html/frontend/htdocs/api/index.php';
}
else
{
	include '/var/www/html/frontend/lib/common.php';
	API::add('User','getInfo',array($_SESSION['session_id']));
	$query1 = API::send();
	$userInfo = $query1['User']['getInfo']['results'][0];
	$_SESSION['api_info_user_id']=$userInfo['id'];
	echo '<form action="" method="post" id="balc_info_frm" name="balc_info_frm">';
	echo '<table style="padding:10px;border: solid 2px #4f5050;visibility:visible;">';
	echo '<tr>';
	echo '<td style="padding:10px;">API KEY</td>';
	echo '<td style="padding:10px;">:</td>';
	echo '<td style="padding:10px;" colspan="2"><input style="width: 500px;" type="text" value="" name="api_key" id="api_key"></td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td style="padding:10px;">API SECRET</td>';
	echo '<td style="padding:10px;">:</td>';
	echo '<td style="padding:10px;"><input style="width: 250px;" type="text" value="" name="api_secret" id="api_secret"></td>';
	echo '<td style="padding:10px;"><input type="button" style="width: 100%;" value="Generate Signature" name="get_sign" onclick="javascript:get_signature();"></td>';
	echo '</tr>';

	echo '<tr>';
	$commands['side'] = 'sell';
	$commands['type'] = 'stop';
	$commands['api_key'] = '';
	$_SESSION['api_key'] = '';
	// $commands['nonce'] = strtotime(date('d-m-Y H:i:s'));
	$commands['nonce']=$_SESSION['nonce'];
	$api_secret='';
	// create the signature
	$signature = hash_hmac('sha256', base64_encode(json_encode($commands)), $api_secret);
	// add signature to request parameters
	$commands['signature'] = $signature;
	echo '<td style="padding:10px;">SIGNATURE</td>';
	echo '<td style="padding:10px;">:</td>';
	echo '<td style="padding:10px;" colspan="2"><input style="width: 500px;" type="text" value="" name="signature" id="signature"></td>';
	echo '</tr>';
	echo '<tr style="display:none;">';
	echo '<td style="padding:10px;">NONCE</td>';
	echo '<td style="padding:10px;">:</td>';
	echo '<td style="padding:10px;" colspan="2"><input style="width: 500px;" type="text" value="'.$commands['nonce'].'" name="nonce" id="nonce"></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td style="padding:10px;text-align:center;" colspan="3"><input type="submit" value="submit"></td>';	
	echo '</tr>';
	echo '</table>';
	echo '</form>';
	?>
	<script type="text/javascript">
	function get_signature() 
	{
		var api_key=document.getElementById('api_key').value;		
		var api_secret=document.getElementById('api_secret').value;
		var side = 'sell';
		var type = 'stop';
		var nonce=document.getElementById('nonce').value;
		var signature="<?php echo hash_hmac('sha256', base64_encode(json_encode($commands)), $api_secret); ?>";
		document.getElementById('signature').value=signature;		
	}
	</script>
	<?php
}
?>
<script type="text/javascript">
// document.getElementById("balc_info_frm").submit();
</script>