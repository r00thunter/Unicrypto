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
$ch = curl_init("http://18.220.172.39/api/get-settings.php"); 
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);      
curl_close($ch);
$ref_response = json_decode($output);
if ($ref_response->is_referral == 1) {
$GLOBALS['REFERRAL'] = true;
$GLOBALS['REFERRAL_BASE_URL'] = "http://18.220.172.39/api/";
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

if ($buy) {
$buy_market_price1 = (!empty($_REQUEST['buy_market_price']));
$buy_price1 = ($buy_market_price1) ? $current_ask : $buy_price1;
$buy_stop = (!empty($_REQUEST['buy_stop']));
$buy_stop_price1 = ($buy_stop) ? Stringz::currencyInput($_REQUEST['buy_stop_price']) : false;
$buy_limit = (!empty($_REQUEST['buy_limit']));
$buy_limit = (!$buy_stop && !$buy_market_price1) ? 1 : $buy_limit;

if (!$confirmed) {
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

}

if ($sell) {
$sell_market_price1 = (!empty($_REQUEST['sell_market_price']));
$sell_price1 = ($sell_market_price1) ? $current_bid : $sell_price1;
$sell_stop = (!empty($_REQUEST['sell_stop']));
$sell_stop_price1 = ($sell_stop) ? Stringz::currencyInput($_REQUEST['sell_stop_price']) : false;
$sell_limit = (!empty($_REQUEST['sell_limit']));
$sell_limit = (!$sell_stop && !$sell_market_price1) ? 1 : $sell_limit;

if (!$confirmed) {
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
if ($_REQUEST['referral_check'] == true) {

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

<!-- Buy form initial -->

<div class="tab-pane fade show active" id="limit" role="tabpanel" aria-labelledby="limit-tab" id="form_initial">
<div class="row">

<!-- Buy form Started -->

<div class="col-md-6 col-sm-6 col-xs-12" style="/*min-width: 400px;*/" id="buy_form_initial">
<form id="buy_form" action="" method="POST">
<h6 class="title"><strong>Buy Cryptocurrency</strong></h6>
<div class="form-group">
<label for="">Available Balance(<span class="sell_currency_label"><?= $currency_info['currency'] ?></span>)</label>
<span class="form-control center-widget" style="margin-top: 0px;"><span class="buy_currency_char"><?= $currency_info['fa_symbol'] ?></span>
<span id="buy_user_available" style="color: #2f8afd;"><?= ((!empty($user_available[strtoupper($currency_info['currency'])])) ? Stringz::currency($user_available[strtoupper($currency_info['currency'])],($currency_info['is_crypto'] == 'Y')) : '0.00') ?></span></span>
</div>
<div class="Flex__Flex-fVJVYW gkSoIH">
<div class="form-group">
<label><?= Lang::string('buy-amount') ?></label>
<input name="buy_amount" id="buy_amount" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_amount1) ?>" />
<div class="input-caption"><?= $c_currency_info['currency'] ?></div>
</div>
</div>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div>
<div class="Flex__Flex-fVJVYW gkSoIH">
<div class="form-group">
<label class="position-relative"><?= Lang::string('buy-with-currency') ?></label>
<span id="buy_currency" class="pull-right position-absolute" style="right:15px;"><?= $currency_info['currency'] ?></span>

</div>
</div>
</div>
</div>
<label class="cont">
<input style="vertical-align:middle" class="checkbox" name="buy_market_price" id="buy_market_price" type="checkbox" value="1" <?= ($buy_market_price1 && !$buy_stop) ? 'checked="checked"' : '' ?> <?= (!$asks) ? 'readonly="readonly"' : '' ?> />
<?= Lang::string('buy-market-price') ?>
<span class="checkmark"></span>
</label>
<label class="cont">
<input class="checkbox" name="buy_limit" id="buy_limit" type="checkbox" value="1" <?= ($buy_limit && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
<?= Lang::string('buy-limit') ?>
<span class="checkmark"></span>
</label>
<label class="cont">
<input class="checkbox" name="buy_stop" id="buy_stop" type="checkbox" value="1" <?= ($buy_stop && !$buy_market_price1) ? 'checked="checked"' : '' ?> />
<?= Lang::string('buy-stop') ?>
<span class="checkmark"></span>
</label>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" id="buy_price_container" <?= (!$buy_limit && !$buy_market_price1) ? 'style="display:none;"' : '' ?>>
<div>
<div class="Flex__Flex-fVJVYW gkSoIH">
<div class="form-group">
<label><span id="buy_price_limit_label" <?= (!$buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="buy_price_market_label" <?= ($buy_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></label>
<input name="buy_price" id="buy_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_price1) ?>" <?= ($buy_market_price1) ? 'readonly="readonly"' : '' ?> />
<div class="input-caption"><?= $currency_info['currency'] ?></div>
</div>
</div>
</div>
</div>
<div id="buy_stop_container" class="param" <?= (!$buy_stop) ? 'style="display:none;"' : '' ?>>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" >
<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">

</div>
<div>
<div class="Flex__Flex-fVJVYW gkSoIH">
<div class="form-group">
<label><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></label>
<input name="buy_stop_price" id="buy_stop_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($buy_stop_price1) ?>">
<div class="input-caption"><?= $currency_info['currency'] ?></div>
</div>
</div>
</div>
</div>
</div>
<br>
<div class="current-otr">
<p>
<?= Lang::string('buy-subtotal') ?> 
<span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_subtotal"><?= Stringz::currency($buy_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
</p>
</div>
<div class="current-otr">
<p>
<?= Lang::string('buy-fee') ?> 
<span class="pull-right"><span id="buy_user_fee"><?= Stringz::currency($user_fee_bid) ?></span>%</span>
</p>
</div>



<?php if($_REQUEST['referral_check'] == true){ ?>
<input type="hidden" name="ref_status" id="ref_status" value="1">
<input type="hidden" name="bonus_amount" id="bonus_amount" value="<? echo $bonus_amount; ?>">
<label class="cont" style="color: brown;font-style:  italic;">
<input 
class="checkbox" 
name="is_referral" 
id="is_referral" 
onclick="calculateBuyPrice()"
type="checkbox" value="1"
<? if($bonous_point == 0){ echo 'disabled'; } ?>
/>
Use your Referral Bonus

<span style="float: right;">    
<? echo $cur_code; ?> <? echo $bonus_amount; ?>
</span>

<span class="checkmark"></span>
</label>
<?php } ?>



<div class="current-otr m-b-15">
<p>
<span id="buy_total_approx_label"><?= str_replace('[currency]','<span class="buy_currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-total-approx')) ?></span>
<span id="buy_total_label" style="display:none;"><?= Lang::string('buy-total') ?></span>
<span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="buy_total"><?= Stringz::currency($buy_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
</p>
</div>
<input type="hidden" name="buy" id="buy_flag" value="1" />
<input type="hidden" name="buy_all" id="buy_all" value="<?= $buy_all1 ?>" />
<input type="hidden" name="uniq" id="uniq_buy" value="<?= end($_SESSION["buysell_uniq"]) ?>" />
<div class="stop_buy_loader" style="text-align: center;background-color: #f5d79c;border-radius: 4px;cursor: not-allowed;position: absolute;width: 87%;height: 6%;margin-top: 15px;display: none;">
<img src="images/loader1.gif" style="width: 16%;"/>
</div>
<input type="button" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('buy-bitcoins')) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" id="buy_button" onclick="buy_order_ajax();"/>
</form>
</div>

<!-- Buy form End -->


<!-- Sell form started -->

<div class="col-md-6 col-sm-6 col-xs-12" id="sell_form_initial">
<form id="sell_form" action="" method="POST">
<h6 class="title"><strong>Sell Cryptocurrency</strong></h6>
<div class="form-group">
<label for="">Available Balance(<?= $c_currency_info['currency'] ?>)</label>
<span class="form-control center-widget" style="margin-top: 0px;">
<span id="sell_user_available" style="color: #2f8afd;"  ><?= Stringz::currency($user_available[strtoupper($c_currency_info['currency'])],true) ?></span> <?= $c_currency_info['currency']?></span>
</div>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div>
<div class="Flex__Flex-fVJVYW gkSoIH">
<div class="form-group">
<label><?= Lang::string('sell-amount') ?></label>
<input name="sell_amount" id="sell_amount" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_amount1) ?>" />
<div class="input-caption"><?= $c_currency_info['currency'] ?></div>
</div>
</div>
</div>
</div>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
<div>
<div class="Flex__Flex-fVJVYW gkSoIH">
<div class="form-group">
<label><?= Lang::string('buy-with-currency') ?></label>
<span id="buy_currency" class="pull-right position-absolute" style="right:15px;"><?= $currency_info['currency'] ?></span>

</div>
</div>
</div>
</div>
<label class="cont">
<input class="checkbox" name="sell_market_price" id="sell_market_price" type="checkbox" value="1" <?= ($sell_market_price1 && !$sell_stop) ? 'checked="checked"' : '' ?> <?= (!$bids) ? 'readonly="readonly"' : '' ?> />
<?= Lang::string('sell-market-price') ?>
<span class="checkmark"></span>
</label>
<label class="cont">
<input class="checkbox" name="sell_limit" id="sell_limit" type="checkbox" value="1" <?= ($sell_limit && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
<?= Lang::string('buy-limit') ?>
<span class="checkmark"></span>
</label>
<label class="cont">
<input class="checkbox" name="sell_stop" id="sell_stop" type="checkbox" value="1" <?= ($sell_stop && !$sell_market_price1) ? 'checked="checked"' : '' ?> />
<?= Lang::string('buy-stop') ?>
<span class="checkmark"></span>
</label>
<div id="sell_price_container" class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" <?= (!$sell_limit && !$sell_market_price1) ? 'style="display:none;"' : '' ?>>
<div>
<div class="Flex__Flex-fVJVYW gkSoIH">
<div class="form-group">
<label><span id="sell_price_limit_label" <?= (!$sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-limit-price') ?></span><span id="sell_price_market_label" <?= ($sell_limit) ? 'style="display:none;"' : '' ?>><?= Lang::string('buy-price') ?></span></label>
<input name="sell_price" id="sell_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_price1) ?>" <?= ($sell_market_price1) ? 'readonly="readonly"' : '' ?> />
<div class="input-caption"><?= $currency_info['currency'] ?></div>
</div>
</div>
</div>
</div>
<div id="sell_stop_container" class="param" <?= (!$sell_stop) ? 'style="display:none;"' : '' ?>>
<div class="TradeSection__Wrapper-jIpuvx bskbTZ m-t-15" >
<div>
<div class="Flex__Flex-fVJVYW gkSoIH">
<div class="form-group">
<label><span><?= Lang::string('buy-stop-price') ?></span><span style="display:none;">Price</span></label>
<input name="sell_stop_price" id="sell_stop_price" type="text" class="form-control" value="<?= Stringz::currencyOutput($sell_stop_price1) ?>">
<div class="input-caption"><?= $currency_info['currency'] ?></div>
</div>
</div>
</div>
</div>
</div>
<br>
<div class="current-otr">
<p>
<?= Lang::string('buy-subtotal') ?>
<span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_subtotal"><?= Stringz::currency($sell_subtotal1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
</p>
</div>
<div class="current-otr">
<p>
<?= Lang::string('buy-fee') ?> 
<span class="pull-right"><span id="sell_user_fee"><?= Stringz::currency($user_fee_ask) ?></span>%</span>
</p>
</div>


<?php if($_REQUEST['referral_check'] == true){ ?>
<input type="hidden" name="bonus_amount" id="bonus_amount" value="<? echo $bonus_amount; ?>">
<label class="cont" style="color: brown;font-style:  italic;">
<input 
class="checkbox" 
name="is_referral"  
id="is_referral_sell" 
onclick="calculateBuyPrice()"
type="checkbox" value="1" 
<? if($bonous_point == 0){ echo 'disabled'; } ?>
/>
Use your Referral Bonus

<span style="float: right;">    
<? echo $cur_code; ?> <? echo $bonus_amount; ?>
</span>

<span class="checkmark"></span>
</label>
<?php } ?>




<div class="current-otr m-b-15">
<p>
<span id="sell_total_approx_label"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total-approx')) ?></span>
<span id="sell_total_label" style="display:none;"><?= str_replace('[currency]','<span class="sell_currency_label">'.$currency_info['currency'].'</span>',Lang::string('sell-total')) ?></span>
<span class="pull-right"><?= $currency_info['fa_symbol'] ?><span id="sell_total"><?= Stringz::currency($sell_total1,($currency_info['is_crypto'] == 'Y')) ?></span></span>
</p>
</div>
<input type="hidden" name="sell" id="sell_flag" value="1" />
<input type="hidden" name="uniq" id="uniq_sell" value="<?= end($_SESSION["buysell_uniq"]) ?>" />

<div class="stop_sell_loader" style="text-align: center;background-color: #f5d79c;border-radius: 4px;cursor: not-allowed;position: absolute;width: 87%;height: 6%;margin-top: 15px;display: none;">
<img src="images/loader1.gif" style="width: 16%;"/>
</div>

<input type="button" name="submit" value="<?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-bitcoins')) ?>" class="Flex__Flex-fVJVYW ghkoKS buy-btc btn btn-primary" id="sell_button" onclick="sell_order_ajax();"/>
</form>
</div>

<!-- Sell form End -->


</div>


<!-- End of Buy form initial -->

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