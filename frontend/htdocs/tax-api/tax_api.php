
<?php 
include '/var/www/html/frontend/lib/common.php';
$commands['side'] = 'sell';
$commands['type'] = 'stop';
$commands['api_key'] = $_REQUEST['api_key'];
$commands['nonce']=strtotime(date('d-m-Y H:i:s'));

$site_user=$_REQUEST['site_user'];
$api_key=$_REQUEST['api_key'];
$secret_key=$_REQUEST['secret_key'];
$api_signature=hash_hmac('sha256', base64_encode(json_encode($commands)), $secret_key);

$flag=$_REQUEST['flag'];


if($flag=='wallet')
{
	if ($api_key) 
	{
			API::add('User','getAvailable_private_tax_api',array($site_user));
			API::apiKey($api_key);
			API::apiSignature($api_signature,$params_json);
			API::apiUpdateNonce();
			$query = API::send($commands['nonce']);			
			$return['wallet'] = $query['User']['getAvailable_private_tax_api']['results'][0];
			// $return['wallet'][strtolower($CFG->currencies[$main['fiat']]['currency']).'_volume'] = ($return['wallet']['usd_volume']) ? $return['wallet']['usd_volume'] : 0;
			// $return['wallet']['exchange_'.strtolower($CFG->currencies[$main['crypto']]['currency']).'_volume'] = ($return['wallet']['global_btc_volume']) ? $return['wallet']['global_btc_volume'] : 0;
			unset($return['wallet']['global_btc_volume']);
			if ($CFG->currencies[$main['fiat']]['currency'] != 'USD')
				unset($return['wallet']['usd_volume']);
	}
}

if($flag=='transactions')
{
	$limit1='';
	API::add('Transactions','get_tax',array($site_user,false,false,$limit1,'','',false,false,false,false,false,1));
	$query = API::send();	
	if ($query['Transactions']['get_tax']['results'][0]) 
	{
		foreach ($query['Transactions'] as $row) {		
		for ($s_no=0; $s_no < count($row['results'][0]); $s_no++) { 

		if ($row['results'][0][$s_no]['maker_type'] == 1) {
		$row['results'][0][$s_no]['maker_type'] = "BUY";
		}elseif ($row['results'][0][$s_no]['maker_type'] == 2) {
		$row['results'][0][$s_no]['maker_type'] = "SELL";
		}

		}
		$return['transactions']  = $row['results'][0];

		}
	}
	else
	{
		$return['transactions']['message']  = "No Data";
	}
}



if (!empty($return))
echo json_encode($return,JSON_NUMERIC_CHECK);

?>