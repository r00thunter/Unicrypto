<?php
include '../lib/common.php';
$conn = new mysqli("localhost","root","xchange123","bitexchange_cash");        

$currency_id = $_REQUEST['c_currency'];
if (!$currency_id) {
$currency_id = 28;
$_REQUEST['currency'] = 28;
}

$c_currency_id = $_REQUEST['currency'];

$t = $_REQUEST['trade'];

// CHECKING REFErral status 
$ch = curl_init("http://18.222.151.3/api/get-settings.php"); 
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);      
curl_close($ch);
$ref_response = json_decode($output);
if ($ref_response->is_referral == 1) {
$GLOBALS['REFERRAL'] = true;
$GLOBALS['REFERRAL_BASE_URL'] = "http://18.222.151.3/api/";
}else{
$GLOBALS['REFERRAL'] = false; 
}
// end of checking referral status

$market = $_REQUEST['trade'];
$currencies = Settings::sessionCurrency();

$buy = (!empty($_REQUEST['buy']));
$sell = (!empty($_REQUEST['sell']));
$ask_confirm = false;
$currency1 = $currencies['currency'];
$c_currency1 = $currencies['c_currency'];
list($c_currency1, $currency1 ) = explode("-",$market) ;
foreach ($CFG->currencies as $key => $currency) {
if( strtolower($c_currency1) == strtolower( $currency['currency'] )){
$c_currency1 = $currency['id'] ;
}
if( strtolower( $currency1 ) == strtolower( $currency['currency'] ) ){
$currency1 = $currency['id'];
}
}
$currency_info = $CFG->currencies[$currency1];
$c_currency_info = $CFG->currencies[$c_currency1];

$from_currency = $c_currency_info['currency'];
$to_currency = $currency_info['currency'];

$confirmed = (!empty($_REQUEST['confirmed'])) ? $_REQUEST['confirmed'] : false;
$cancel = (!empty($_REQUEST['cancel'])) ? $_REQUEST['cancel'] : false;
$bypass = (!empty($_REQUEST['bypass'])) ? $_REQUEST['bypass'] : false;
$buy_market_price1 = 0;
$sell_market_price1 = 0;
$buy_limit = 1;
$sell_limit = 1;
if ($buy || $sell) {
if (empty($_SESSION["buysell_uniq"]) || empty($_REQUEST['uniq']) || !in_array($_REQUEST['uniq'],$_SESSION["buysell_uniq"]))
Errors::add('Page expired.');
}

foreach ($CFG->currencies as $key => $currency) {
if (is_numeric($key) || $currency['is_crypto'] != 'Y')
continue;

API::add('Stats','getCurrent',array($currency['id'],$currency1));
}

API::add('User','hasCurrencies');
API::add('Orders','getBidAsk',array($c_currency1,$currency1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,false,false,1));
API::add('Transactions','get',array(false,false,1,$c_currency1,$currency1));
API::add('Transactions','get24hData',array(28,27));
API::add('Transactions','get24hData',array(42,27));
API::add('Transactions','get24hData',array(42,28));
API::add('Transactions','get24hData',array(43,27));
API::add('Transactions','get24hData',array(43,28));
API::add('Transactions','get24hData',array(43,27));
API::add('Transactions','get24hData',array(43,28));
API::add('Transactions','get24hData',array(45,27));
API::add('Transactions','get24hData',array(45,28));
API::add('Transactions','get24hData',array(42,45));
API::add('Transactions','get24hData',array(43,45));
API::add('Transactions','get24hData',array(44,27));
API::add('Transactions','get24hData',array(44,28));
API::add('Transactions','get24hData',array($c_currency1, $currency1));
API::add('Transactions','get24hData',array(28,42)); //btc-ltc
API::add('Transactions','get24hData',array(45,42)); //eth-ltc
API::add('Transactions','get24hData',array(43,42)); //zec-ltc
API::add('Transactions','get24hData',array(44,42)); //bch-ltc

API::add('Transactions','get24hData',array(28,44)); //btc-bch
API::add('Transactions','get24hData',array(45,44)); //btc-eth
API::add('Transactions','get24hData',array(43,44)); //btc-zec
API::add('Transactions','get24hData',array(42,44)); //btc-ltc

API::add('Transactions','get24hData',array(28,43)); //btc-zec
API::add('Transactions','get24hData',array(42,43)); //ltc-zec
API::add('Transactions','get24hData',array(45,43)); //eth-zec
API::add('Transactions','get24hData',array(44,43)); //bch-zec

API::add('Transactions','get24hData',array(28,45)); //btc-eth
API::add('Transactions','get24hData',array(42,45)); //ltc-eth
API::add('Transactions','get24hData',array(44,45)); //bch-eth
API::add('Transactions','get24hData',array(43,45)); //zec-eth

API::add('Transactions', 'get', array(false, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
API::add('Transactions', 'getTypes');


if ($currency_info['is_crypto'] != 'Y') {
API::add('BankAccounts','get',array($currency_info['id']));
}


$query = API::send();

$currentPair = $query['Transactions']['get24hData']['results'][13];
$total = $query['Transactions']['get']['results'][0];
$user_available_currencies = $query['User']['hasCurrencies']['results'];
$current_bid = $query['Orders']['getBidAsk']['results'][0]['bid'];
$current_ask =  $query['Orders']['getBidAsk']['results'][0]['ask'];
$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];

API::add('FeeSchedule','getRecord',array(User::$info['fee_schedule']));
API::add('User','getAvailable');
$feequery = API::send();
$user_fee_both = $feequery['FeeSchedule']['getRecord']['results'][0];
$user_available = $feequery['User']['getAvailable']['results'][0];

$user_fee_bid = ($buy && ((Stringz::currencyInput($_REQUEST['buy_amount']) > 0 && Stringz::currencyInput($_REQUEST['buy_price']) >= $asks[0]['btc_price']) || !empty($_REQUEST['buy_market_price']) || empty($_REQUEST['buy_amount']))) ? $feequery['FeeSchedule']['getRecord']['results'][0]['fee'] : $feequery['FeeSchedule']['getRecord']['results'][0]['fee1'];
$user_fee_ask = ($sell && ((Stringz::currencyInput($_REQUEST['sell_amount']) > 0 && Stringz::currencyInput($_REQUEST['sell_price']) <= $bids[0]['btc_price']) || !empty($_REQUEST['sell_market_price']) || empty($_REQUEST['sell_amount']))) ? $feequery['FeeSchedule']['getRecord']['results'][0]['fee'] : $feequery['FeeSchedule']['getRecord']['results'][0]['fee1'];
$transactions = $query['Transactions']['get']['results'][0];
$my_transactions = $query['Transactions']['get']['results'][1];
$usd_field = 'usd_ask';
$transactions_24hrs_btc_usd = $query['Transactions']['get24hData']['results'][0] ;
$transactions_24hrs_ltc_usd = $query['Transactions']['get24hData']['results'][1] ;
$transactions_24hrs_ltc_btc = $query['Transactions']['get24hData']['results'][2] ;
$transactions_24hrs_zec_usd = $query['Transactions']['get24hData']['results'][3] ;
$transactions_24hrs_zec_btc = $query['Transactions']['get24hData']['results'][4] ;
$transactions_24hrs_eth_usd = $query['Transactions']['get24hData']['results'][5] ;
$transactions_24hrs_eth_btc = $query['Transactions']['get24hData']['results'][6] ;
$transactions_24hrs_ltc_eth = $query['Transactions']['get24hData']['results'][7] ;
$transactions_24hrs_zec_eth = $query['Transactions']['get24hData']['results'][8] ;
$transactions_24hrs_bch_usd = $query['Transactions']['get24hData']['results'][9] ;
$transactions_24hrs_bch_btc = $query['Transactions']['get24hData']['results'][10] ;

$transactions_24hrs_btc_ltc = $query['Transactions']['get24hData']['results'][14] ;
$transactions_24hrs_eth_ltc = $query['Transactions']['get24hData']['results'][15] ;
$transactions_24hrs_zec_ltc = $query['Transactions']['get24hData']['results'][16] ;
$transactions_24hrs_bch_ltc = $query['Transactions']['get24hData']['results'][17] ;

$transactions_24hrs_btc_bch = $query['Transactions']['get24hData']['results'][18] ;
$transactions_24hrs_eth_bch = $query['Transactions']['get24hData']['results'][19] ;
$transactions_24hrs_zec_bch = $query['Transactions']['get24hData']['results'][20] ;
$transactions_24hrs_ltc_bch = $query['Transactions']['get24hData']['results'][21] ;

$transactions_24hrs_btc_zec = $query['Transactions']['get24hData']['results'][22] ;
$transactions_24hrs_ltc_zec = $query['Transactions']['get24hData']['results'][23] ;
$transactions_24hrs_eth_zec = $query['Transactions']['get24hData']['results'][24] ;
$transactions_24hrs_bch_zec = $query['Transactions']['get24hData']['results'][25] ;

$transactions_24hrs_btc_eth = $query['Transactions']['get24hData']['results'][26] ;
$transactions_24hrs_ltc_eth = $query['Transactions']['get24hData']['results'][27] ;
$transactions_24hrs_bch_eth = $query['Transactions']['get24hData']['results'][28] ;
$transactions_24hrs_zec_eth = $query['Transactions']['get24hData']['results'][29] ;

$i = 0;
$stats = array();
$market_stats = array();
foreach ($CFG->currencies as $key => $currency) {
if (is_numeric($key) || $currency['is_crypto'] != 'Y')
continue;

$k = $query['Stats']['getCurrent']['results'][$i]['market'];
if ($CFG->currencies[$k]['id'] == $c_currency1)
$stats = $query['Stats']['getCurrent']['results'][$i];

$market_stats[$k] = $query['Stats']['getCurrent']['results'][$i];
$i++;
}

if ($currency_info['is_crypto'] != 'Y')
$bank_accounts = $query['BankAccounts']['get']['results'][0];

$buy_amount1 = (!empty($_REQUEST['buy_amount'])) ? Stringz::currencyInput($_REQUEST['buy_amount']) : 0;
$buy_price1 = (!empty($_REQUEST['buy_price'])) ? Stringz::currencyInput($_REQUEST['buy_price']) : $current_ask;
$buy_subtotal1 = $buy_amount1 * $buy_price1;
$buy_fee_amount1 = ($user_fee_bid * 0.01) * $buy_subtotal1;

// referral bonus starts
if ($_REQUEST['is_referral']) {
//echo 'yes is_referral true';//bonus_amount
if ($_REQUEST['bonus_amount']) {
$buy_fee_amount1 = $buy_fee_amount1 - $_REQUEST['bonus_amount'];
}
}
// end of referral bonus

$buy_total1 = round($buy_subtotal1 + $buy_fee_amount1,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP);
$buy_stop = false;
$buy_stop_price1 = false;
$buy_all1 = (!empty($_REQUEST['buy_all']));

$sell_amount1 = (!empty($_REQUEST['sell_amount'])) ? Stringz::currencyInput($_REQUEST['sell_amount']) : 0;
$sell_price1 = (!empty($_REQUEST['sell_price'])) ? Stringz::currencyInput($_REQUEST['sell_price']) : $current_bid;
$sell_subtotal1 = $sell_amount1 * $sell_price1;
$sell_fee_amount1 = ($user_fee_ask * 0.01) * $sell_subtotal1;

if ($_REQUEST['is_referral']) {
if ($_REQUEST['bonus_amount']) {
$sell_fee_amount1 = $sell_fee_amount1 - $_REQUEST['bonus_amount'];
}
}
//
$sell_total1 = round($sell_subtotal1 - $sell_fee_amount1,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP);
$sell_stop = false;
$sell_stop_price1 = false;

if ($CFG->trading_status == 'suspended')
Errors::add(Lang::string('buy-trading-disabled'));

if ($buy && !is_array(Errors::$errors)) {
$buy_market_price1 = (!empty($_REQUEST['buy_market_price']));
$buy_price1 = ($buy_market_price1) ? $current_ask : $buy_price1;
$buy_stop = (!empty($_REQUEST['buy_stop']));
$buy_stop_price1 = ($buy_stop) ? Stringz::currencyInput($_REQUEST['buy_stop_price']) : false;
$buy_limit = (!empty($_REQUEST['buy_limit']));
$buy_limit = (!$buy_stop && !$buy_market_price1) ? 1 : $buy_limit;

if (!$confirmed && !$cancel) {
API::add('Orders','checkPreconditions',array(1,$c_currency1,$currency_info,$buy_amount1,(($buy_stop && !$buy_limit) ? $buy_stop_price1 : $buy_price1),$buy_stop_price1,$user_fee_bid,$user_available[$currency_info['currency']],$current_bid,$current_ask,$buy_market_price1,false,false,$buy_all1));
if (!$buy_market_price1)
API::add('Orders','checkUserOrders',array(1,$c_currency1,$currency_info,false,(($buy_stop && !$buy_limit) ? $buy_stop_price1 : $buy_price1),$buy_stop_price1,$user_fee_bid,$buy_stop));

$query = API::send();
// var_dump($query);
$errors1 = $query['Orders']['checkPreconditions']['results'][0];
if (!empty($errors1['error']))
Errors::add($errors1['error']['message']);
$errors2 = (!empty($query['Orders']['checkUserOrders']['results'][0])) ? $query['Orders']['checkUserOrders']['results'][0] : false;
if (!empty($errors2['error']))
Errors::add($errors2['error']['message']);

if (!$errors1 && !$errors2)
$ask_confirm = true;
}
else if (!$cancel) {
API::add('Orders','executeOrder',array(1,(($buy_stop && !$buy_limit) ? $buy_stop_price1 : $buy_price1),$buy_amount1,$c_currency1,$currency1,$user_fee_bid,$buy_market_price1,false,false,false,$buy_stop_price1,false,false,$buy_all1));
$query = API::send();
$operations = $query['Orders']['executeOrder']['results'][0];
if (!empty($operations['error'])) {
Errors::add($operations['error']['message']);
}
else if ($operations['new_order'] > 0) {
$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
if (count($_SESSION["buysell_uniq"]) > 3) {
unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
}

// updating referral bonus
$name = User::$info['first_name'].' '.User::$info['last_name'];
$url = $REFERRAL_BASE_URL."use-bonus.php?name=1";
$fields = array(
'user_id' => urlencode($name),
'trans_id' => urlencode($name),
'points' => urlencode($bonous_point),
'name' => urlencode($name)
);
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
$response = json_decode($result);
curl_close($ch);

Link::redirect("advanced-trade.php?trade=".$t."&c_currency=".$currency_id."&currency=".$c_currency_id);
exit;
}
else {
$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
if (count($_SESSION["buysell_uniq"]) > 3) {
unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
}

// updating referral bonus
$name = User::$info['first_name'].' '.User::$info['last_name'];
$url = $REFERRAL_BASE_URL."use-bonus.php?name=1";
$fields = array(
'user_id' => urlencode($name),
'trans_id' => urlencode($name),
'points' => urlencode($bonous_point),
'name' => urlencode($name)
);
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
$response = json_decode($result);
curl_close($ch);
Link::redirect("advanced-trade.php?trade=".$t."&c_currency=".$currency_id."&currency=".$c_currency_id);
exit;
}
}
}

if ($sell && !is_array(Errors::$errors)) {
$sell_market_price1 = (!empty($_REQUEST['sell_market_price']));
$sell_price1 = ($sell_market_price1) ? $current_bid : $sell_price1;
$sell_stop = (!empty($_REQUEST['sell_stop']));
$sell_stop_price1 = ($sell_stop) ? Stringz::currencyInput($_REQUEST['sell_stop_price']) : false;
$sell_limit = (!empty($_REQUEST['sell_limit']));
$sell_limit = (!$sell_stop && !$sell_market_price1) ? 1 : $sell_limit;

if (!$confirmed && !$cancel) {
API::add('Orders','checkPreconditions',array(0,$c_currency1,$currency_info,$sell_amount1,(($sell_stop && !$sell_limit) ? $sell_stop_price1 : $sell_price1),$sell_stop_price1,$user_fee_ask,$user_available[$c_currency_info['currency']],$current_bid,$current_ask,$sell_market_price1));
if (!$sell_market_price1)
API::add('Orders','checkUserOrders',array(0,$c_currency1,$currency_info,false,(($sell_stop && !$sell_limit) ? $sell_stop_price1 : $sell_price1),$sell_stop_price1,$user_fee_ask,$sell_stop));

$query = API::send();
$errors1 = $query['Orders']['checkPreconditions']['results'][0];
if (!empty($errors1['error']))
Errors::add($errors1['error']['message']);
$errors2 = (!empty($query['Orders']['checkUserOrders']['results'][0])) ? $query['Orders']['checkUserOrders']['results'][0] : false;
if (!empty($errors2['error']))
Errors::add($errors2['error']['message']);

if (!$errors1 && !$errors2)
$ask_confirm = true;
}
else if (!$cancel) {
API::add('Orders','executeOrder',array(0,($sell_stop && !$sell_limit) ? $sell_stop_price1 : $sell_price1,$sell_amount1,$c_currency1,$currency1,$user_fee_ask,$sell_market_price1,false,false,false,$sell_stop_price1));
$query = API::send();
$operations = $query['Orders']['executeOrder']['results'][0];

if (!empty($operations['error'])) {
Errors::add($operations['error']['message']);
}
else if ($operations['new_order'] > 0) {
$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
if (count($_SESSION["buysell_uniq"]) > 3) {
unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
}

Link::redirect("advanced-trade.php?trade=".$t."&c_currency=".$currency_id."&currency=".$c_currency_id);

exit;
}
else {
$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
if (count($_SESSION["buysell_uniq"]) > 3) {
unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
}

// updating referral bonus
$name = User::$info['first_name'].' '.User::$info['last_name'];
$url = $REFERRAL_BASE_URL."use-bonus.php?name=1";
$fields = array(
'user_id' => urlencode($name),
'trans_id' => urlencode($name),
'points' => urlencode($bonous_point),
'name' => urlencode($name)
);
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
$response = json_decode($result);
curl_close($ch);
Link::redirect("advanced-trade.php?trade=".$t."&c_currency=".$currency_id."&currency=".$c_currency_id);
exit;
}
}
}

$notice = '';
if ($ask_confirm && $sell) {
if (!$bank_accounts && $currency_info['is_crypto'] != 'Y')
$notice .= '<div class="message-box-wrap">'.str_replace('[currency]',$currency_info['currency'],Lang::string('buy-errors-no-bank-account')).'</div>';

if (($buy_limit && $buy_stop) || ($sell_limit && $sell_stop))
$notice .= '<div class="message-box-wrap">'.Lang::string('buy-notify-two-orders').'</div>';
}

$select = "" ;
foreach ($CFG->currencies as $key => $currency) {
if (is_numeric($key) || $currency['is_crypto'] != 'Y')
continue;
if($c_currency1 == $currency['id'])
$select = $currency['currency'] ;
}


$page_title = Lang::string('buy-sell');
$_SESSION["buysell_uniq"][time()] = md5(uniqid(mt_rand(),true));
if (count($_SESSION["buysell_uniq"]) > 3) {
unset($_SESSION["buysell_uniq"][min(array_keys($_SESSION["buysell_uniq"]))]);
}

// start of referral 
if ($REFERRAL == true) {

$name = User::$info['first_name'].' '.User::$info['last_name'];

$url = $REFERRAL_BASE_URL."get-user-bonus.php?name=1";

$fields = array(
'user_id' => urlencode($name),
'name' => urlencode($name)
);
//print_r($fields);
//url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
//open connection
$ch = curl_init();
//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//execute post
$result = curl_exec($ch);
$response = json_decode($result);
//close connection
curl_close($ch);
$one_point_value = $response->settings->one_point_value;

$referral_code = $response->data->referral_code; 
$bonous_point = $response->data->bonous_point;

if ($to_currency == 'USD') {
$bonus_amount = (float) $bonous_point / (float) $one_point_value;
$cur_code = '$';
}else{
$one_point_values = $response->settings->$to_currency;
$bonus_amount = (float) $bonous_point / (float) $one_point_values;
$cur_code = $to_currency;
}

$his_url = $REFERRAL_BASE_URL."get-usage-history.php";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result = curl_exec($ch);
$response = json_decode($result);
curl_close($ch);
}
// end of referral
?>

<!-- Buy form Confirm -->

<div class="TradeFormTabContainer__Container-cUyfJR eMNjQO Panel__Container-hCUKEb gmOPIV conform-screen" style="max-width: 700px;margin: auto;width:100%;" id="form_confirm">
<div class="Flex__Flex-fVJVYW iDqRrV">
<div class="TradeFormTabContainer__Tab-caAlbq keHVTX Flex__Flex-fVJVYW iDqRrV" style="border-right: none; text-align:center">
<a class="TradeFormTabContainer__TabLink-bIVxHh kIXsIf">
<input id="c_currency" type="text" value="28" style="display:none;">
<span class="right" style="font-size: 1.2em;margin-top:1em;"><?= Lang::string('confirm-transaction') ?></span>
</a>
</div>
</div>
<div class="TradeFormTabContainer__Content-bTJPSU TMIzi Flex__Flex-fVJVYW gkSoIH" style="min-height:auto;">
<div></div>
<div>
<div class="Flex__Flex-fVJVYW bHipRv">
<form id="confirm_form" action="" method="POST">
<input type="hidden" name="confirmed" id="confirmed" value="1" />
<input type="hidden" id="buy_all" name="buy_all" value="<?= $buy_all1 ?>" />
<input type="hidden" id="cancel" name="cancel" value="" />

<!-- Buy Confirm -->

<? if ($buy) { ?>
<div class="bskbTZ">
<p style="margin-bottom:0px;"><?= Lang::string('buy-amount') ?></p>
<h4><b><?= Stringz::currency($buy_amount1,true) ?></b></h4>
<input type="hidden" name="buy_amount" id="buy_amount" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
</div>
<div class="bskbTZ">
<p style="margin-bottom:0px;"><?= Lang::string('buy-with-currency') ?></p>
<h4><b><?= $currency_info['currency'] ?></b></h4>
<input type="hidden" name="buy_currency" value="<?= $currency1 ?>" />
</div>
<? if ($buy_limit || $buy_market_price1) { ?>
<div class="bskbTZ">
<p style="margin-bottom:0px;"><?= ($buy_market_price1) ? Lang::string('buy-price') : Lang::string('buy-limit-price') ?></p>
<h4><b><?= Stringz::currency($buy_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
<input type="hidden" name="buy_price" id="buy_price" value="<?= Stringz::currencyOutput($buy_price1) ?>" />
</div>
<?php } ?>
<? if ($buy_stop) { ?>
<div class="bskbTZ">
<p style="margin-bottom:0px;"><?= Lang::string('buy-stop-price') ?></p>
<h4><b><?= Stringz::currency($buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
<input type="hidden" name="buy_stop_price" id="buy_stop_price" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>" />
</div>
<?php } ?>
<? if ($buy_market_price1) { ?>
<label class="cont"><?= Lang::string('buy-market-price') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
<input type="hidden" name="buy_market_price" id="buy_market_price_new" value="<?= $buy_market_price1 ?>"/>
<?php } ?>
<? if ($buy_limit) { ?>
<label class="cont"><?= Lang::string('buy-limit') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
<input type="hidden" name="buy_limit" value="<?= $buy_limit ?>"/>
<?php } ?>
<? if ($buy_stop) { ?>
<label class="cont" style="padding-left:2em;"><?= Lang::string('buy-stop') ?>   
<input disabled="disabled" class="checkbox" name="dummy" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
<input type="hidden" name="buy_stop" value="<?= $buy_stop ?>" />
<?php } ?>
<span class="checkmark"></span>
</label>
<? if ($buy_stop) { ?>
<div class="current-otr">
<p style="margin-bottom:0px;"><?= Lang::string('buy-subtotal') ?></p>
<h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($buy_amount1 * $buy_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
</div>
<div class="current-otr">
<p style="margin-bottom:0px;">
<?= Lang::string('buy-fee') ?>
<h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
</p>
</div>
<div class="current-otr m-b-15">
<p style="margin-bottom:0px;">
<span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
</p>
<h4>
<span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
<b><span id="buy_total"><?= Stringz::currency(round($buy_amount1 * $buy_stop_price1 - ($user_fee_ask * 0.01) * $buy_amount1 * $buy_stop_price1 ,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP),($currency_info['is_crypto'] == 'Y')) ?></span></b>
</h4>
</div>
<? } else { ?>
<div class="current-otr">
<p style="margin-bottom:0px;"><?= Lang::string('buy-subtotal') ?></p>
<h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
</div>
<div class="current-otr">
<p style="margin-bottom:0px;"><?= Lang::string('buy-fee') ?></p>
<h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
</div>
<div class="current-otr m-b-15">
<p style="margin-bottom:0px;">
<span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
</p>
<h4>
<span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
<b><?= $currency_info['fa_symbol'] ?><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></b>
</h4>
</div>
<? } ?>
<input type="hidden" name="buy" value="1" />
<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
<div class="btn-otr">
<span>
<div class="buy_loader_confirm" style="text-align: center;background-color: rgb(245, 215, 156);border-radius: 4px;cursor: not-allowed;width: 19%;margin: 0px;padding: 0px;height: 28px;display: none;">
<img src="images/loader1.gif" style="width: 35%;"/>
</div>
<input type="button" id="confirm_button_buy" name="submit" value="<?= Lang::string('confirm-buy') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" style="width: auto;display: inline-block;" onclick="buy_order_ajax_confirm();"/>
</span>
<span>
<button type="button" value="Back" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn btn-primary buy_loader_cancel" style="width: 60px;display: none;float: right;background-color: #f5d79c !important;"><img src="images/loader1.gif" style="width: 40%;"/></button>

<input id="cancel_transaction" type="button" name="dont" value="Back" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn btn-primary" style="width: auto;display: inline-block;float: right;" onclick="javascript:buy_order_back();">
</span>
<p class="m-t-10"> By clicking CONFIRM button an order request will be created.</p>
</div>

<!-- End of Buy Confirm -->

<!-- Sell Confirm -->

<? } else { ?>
<div class="bskbTZ">
<p style="margin-bottom:0px;"><?= Lang::string('sell-amount') ?></p>
<h4><b><?= Stringz::currency($sell_amount1,true) ?></b></h4>
<input type="hidden" name="sell_amount" id="sell_amount" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
</div>
<div class="bskbTZ">
<p style="margin-bottom:0px;"><?= Lang::string('buy-with-currency') ?></p>
<h4><b><?= $currency_info['currency'] ?></b></h4>
<input type="hidden" name="sell_currency" value="<?= $currency1 ?>" />
</div>
<? if ($sell_limit || $sell_market_price1) { ?>
<div class="bskbTZ">
<p style="margin-bottom:0px;"><?= ($sell_market_price1) ? Lang::string('buy-price') : Lang::string('buy-limit-price') ?></p>
<h4><b><?= Stringz::currency($sell_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
<input type="hidden" name="sell_price" value="<?= Stringz::currencyOutput($sell_price1) ?>" />
</div>
<?php } ?>
<? if ($sell_stop) { ?>
<div class="bskbTZ">
<p style="margin-bottom:0px;"><?= Lang::string('buy-stop-price') ?></p>
<h4><b><?= Stringz::currency($sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
<input type="hidden" name="sell_stop_price" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>" />
</div>
<?php } ?>
<? if ($sell_market_price1) { ?>
<label class="cont"><?= Lang::string('sell-market-price') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
<input type="hidden" name="sell_market_price" value="<?= $sell_market_price1 ?>" />
<?php } ?>
<? if ($sell_limit) { ?>
<label class="cont"><?= Lang::string('buy-limit') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
<input type="hidden" name="sell_limit" value="<?= $sell_limit ?>" />
<?php } ?>
<? if ($sell_stop) { ?>
<label class="cont"><?= Lang::string('buy-stop') ?>   <input disabled="disabled" class="checkbox" name="dummy" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> style="vertical-align: middle;margin-left: 5px;width: 20px;height: 20px;"/>
<input type="hidden" name="sell_stop" value="<?= $sell_stop ?>" />
<?php } ?>
<span class="checkmark"></span>
</label>
<? if ($sell_stop) { ?>
<div class="current-otr">
<p style="margin-bottom:0px"><?= Lang::string('buy-subtotal') ?> </p>
<h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($sell_amount1 * $sell_stop_price1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
</div>
<div class="current-otr">
<p style="margin-bottom:0px">
<?= Lang::string('buy-fee') ?>
</p>
<h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
</div>
<div class="current-otr m-b-15">
<p style="margin-bottom:0px">
<span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
</p>
<h4>
<span id="sell_total_label" style="display:none;"><?= Lang::string('sell-total') ?></span>
<b><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency(round($sell_amount1 * $sell_stop_price1 - ($user_fee_ask * 0.01) * $sell_amount1 * $sell_stop_price1 ,($currency_info['is_crypto'] == 'Y' ? 8 : 2),PHP_ROUND_HALF_UP),($currency_info['is_crypto'] == 'Y')) ?></span></b>
</h4>
</div>
<? } else { ?>
<div class="current-otr">
<p style="margin-bottom:0px"><?= Lang::string('buy-subtotal') ?></p>
<h4><b><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></b></h4>
</div>
<div class="current-otr">
<p style="margin-bottom:0px"><?= Lang::string('buy-fee') ?></p>
<h4><b><span id="sell_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</b></h4>
</div>
<div class="current-otr m-b-15">
<p style="margin-bottom:0px">
<span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
</p>
<h4>
<span id="sell_total_label" style="display:none;"><?= Lang::string('sell-total') ?></span>
<b><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></b>
</h4>
</div>
<? } ?>
<input type="hidden" name="sell" value="1" />
<input type="hidden" name="uniq" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
<div class="btn-otr">


<span>
<div class="sell_loader_confirm" style="text-align: center;background-color: rgb(245, 215, 156);border-radius: 4px;cursor: not-allowed;width: 32%;margin: 0px;padding: 0px;height: 40px;display: none;">
<img src="images/loader1.gif" style="width: 28%;"/>
</div>
<input type="button" id="confirm_button_sell" name="submit" value="<?= Lang::string('confirm-sale') ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" style="width: auto;display: inline-block;padding: 12px 30px;" onclick="sell_order_ajax_confirm();"/>
</span>

<span>
<button type="button" value="Back" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn btn-primary sell_loader_cancel" style="width: 80px;display: none;float: right;background-color: #f5d79c !important;height: 42px;"><img src="images/loader1.gif" style="width: 40%;"/></button>

<input type="button" name="dont" value="Back" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn btn-primary" style="width: auto;display: inline-block;float: right;padding: 12px 30px;" id="sell_button_back" onclick="javascript:sell_order_back();">
</span>

</div>
<?php } ?>

<!-- End of Sell Confirm -->

</form>
</div>
</div>
</div>
</div>

<!-- End of Buy form Confirm -->

<script>
var sell_price = 0;
var buy_price = 0;

$(document).ready(function(){

$(".Header__DropdownButton-dItiAm").click(function(){
$(".DropdownMenu__Wrapper-ieiZya.kwMMmE").toggleClass("show-menu");
});
});

sell_price = $("#sell_price").val();
buy_price = $("#buy_price").val();

$("#sell_market_price").on('change', function(){
$("#sell_price").val(sell_price).change();
})

$("#buy_market_price").on('change', function(){
$("#buy_price").val(buy_price).change();
})


$("#buy_stop").click(function() {
var ischecked= $('#buy_limit').is(':checked');
if(ischecked)
$('#buy_limit').prop('checked', false);
}); 

$("#buy_limit").click(function() {
var ischecked= $('#buy_stop').is(':checked');
if(ischecked)
$('#buy_stop').prop('checked', false);
}); 

$("#sell_stop").click(function() {
var ischecked= $('#sell_limit').is(':checked');
if(ischecked)
$('#sell_limit').prop('checked', false);
}); 

$("#sell_limit").click(function() {
var ischecked= $('#sell_stop').is(':checked');
if(ischecked)
$('#sell_stop').prop('checked', false);
}); 
</script>
<script type="text/javascript" src="js/ops.js?v=20160210"></script>

<script>
$(document).ready(function(){

$('.clickable-row').on('click', function(){
var href = $(this).data('href');
window.location.href = href;
})

});

var interval = setInterval(function(){

if($(".tradingview-widget-container").html() != "") {
$(".tradingview-widget-container").show();
clearInterval(interval);
}
}, 100)
</script>
<?php


$sql = "SELECT A.date,A.btc_price,B.currency FROM `transactions` A,`currencies` B WHERE A.c_currency = B.id AND c_currency = $currency_id";
$my_query = mysqli_query($conn,$sql);
?>

<script type='text/javascript'>
function drawChart() {
var data = new google.visualization.DataTable();
data.addColumn('date', 'Date');
data.addColumn('number', 'BTC');
data.addColumn('string', 'title1');
data.addColumn('string', 'text1');
data.addRows([
<?php while ($value = mysqli_fetch_assoc($my_query)) { 

$d = date("d",strtotime($value['date']));
$y = date("Y",strtotime($value['date']));
$m = date("m",strtotime($value['date']));
?>

[new Date(<?php echo $y; ?>, <?php echo $m-1; ?> ,<?php echo $d; ?>), <?php echo $value['btc_price']; ?>, undefined, undefined] ,
<?php } if(!mysqli_fetch_assoc($my_query)){ 

$year = date("Y");
$month = date("m");
$date = date("d"); ?>
[new Date(<?php echo $year; ?>, <?php echo $month-1; ?> ,<?php echo $date; ?>), 0, undefined, undefined] 

<?php }?>
]);

var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
chart.draw(data, {displayAnnotations: false});
}

$(document).ready(function () {
$(window).scrollTop(0);
return false;
});

function redirectBasedOnCurrencies(c_currency, currency)
{
var url = window.location.origin+window.location.pathname+"?trade=BTC-USD&c_currency="+c_currency+"&currency="+currency;
console.log(url);
window.location.href = url;
}

$(document).ready(function(){
$("#c_currency_select").on('change', function(){
redirectBasedOnCurrencies($(this).val(), $('#currency_select').find('option:selected').val());
});
$("#currency_select").on('change', function(){
redirectBasedOnCurrencies($("#c_currency_select").find('option:selected').val(), $(this).val());
});

$("#1c_currency_select").on('change', function(){
redirectBasedOnCurrencies($(this).val(), $('#1currency_select').find('option:selected').val());
});
$("#1currency_select").on('change', function(){
redirectBasedOnCurrencies($("#1c_currency_select").find('option:selected').val(), $(this).val());
});

$("#2c_currency_select").on('change', function(){
redirectBasedOnCurrencies($(this).val(), $('#2currency_select').find('option:selected').val());
});
$("#2currency_select").on('change', function(){
redirectBasedOnCurrencies($("#2c_currency_select").find('option:selected').val(), $(this).val());
});
$("#3c_currency_select").on('change', function(){
redirectBasedOnCurrencies($(this).val(), $('#3currency_select').find('option:selected').val());
});
$("#3currency_select").on('change', function(){
redirectBasedOnCurrencies($("#3c_currency_select").find('option:selected').val(), $(this).val());
});
});
</script>

</html>