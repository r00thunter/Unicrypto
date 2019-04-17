<?php 
include '../lib/common.php';       

$buysell = $_REQUEST['buysell'];

$c_currencyy1 = $_REQUEST['c_currency'];
$currencyy1 = $_REQUEST['currency'];

$currencies = Settings::sessionCurrency();
$currency1 = $currencies['currency'];
$c_currency1 = $currencies['c_currency'];

$currency_info = $CFG->currencies[$currency1];
$c_currency_info = $CFG->currencies[$c_currency1];

$delete_id1 = (!empty($_REQUEST['delete_id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['delete_id']) : false;
if ($delete_id1 > 0) {
API::add('Orders','getRecord',array($delete_id1));
$query = API::send();
$del_order = $query['Orders']['getRecord']['results'][0];

if (!$del_order) {
echo 0;
echo '~';
echo 'Order does not Exist!';
}
elseif ($del_order['site_user'] != $del_order['user_id'] || !($del_order['id'] > 0)) {
echo 0;
echo '~';
echo 'This Order is not yours!';
}
else {
API::add('Orders','delete',array($delete_id1));
$query = API::send();
echo 1;
echo '~';
echo 'Order Cancelled Successfully!';
}
}

if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']),$CFG->currencies)))
$_SESSION['oo_c_currency'] = preg_replace("/[^0-9]/", "",$_REQUEST['c_currency']);
else if (empty($_SESSION['oo_c_currency']) || $_REQUEST['c_currency'] == 'All')
$_SESSION['oo_c_currency'] = false;

if ((!empty($_REQUEST['currency']) && array_key_exists(strtoupper($_REQUEST['currency']),$CFG->currencies)))
$_SESSION['oo_currency'] = preg_replace("/[^0-9]/", "",$_REQUEST['currency']);
else if (empty($_SESSION['oo_currency']) || $_REQUEST['currency'] == 'All')
$_SESSION['oo_currency'] = false;

if ((!empty($_REQUEST['order_by'])))
$_SESSION['oo_order_by'] = preg_replace("/[^a-z]/", "",$_REQUEST['order_by']);
else if (empty($_SESSION['oo_order_by']))
$_SESSION['oo_order_by'] = false;

$c_currency1 = $_REQUEST['c_currency'] ? : 28;
$currency1 = $_REQUEST['currency'] ? : 27;
$order_by1 = $_SESSION['oo_order_by'];
$trans_realized1 = (!empty($_REQUEST['transactions'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['transactions']) : false;
$id1 = (!empty($_REQUEST['id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['id']) : false;
$bypass = (!empty($_REQUEST['bypass']));

API::add('Orders','get',array(false,false,false,$c_currency1,$currency1,1,false,1,$order_by1,false,1));
API::add('Orders','get',array(false,false,false,$c_currency1,$currency1,1,false,false,$order_by1,1,1));
$query = API::send();

$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];
$currency_info = ($currency1) ? $CFG->currencies[strtoupper($currency1)] : false;
$_SESSION["openorders_uniq"] = md5(uniqid(mt_rand(),true));


echo '~';

if($buysell=='buy') {
?>


<table class="table">
<thead>
<tr>
<th>Type</th>
<th><?= Lang::string('orders-price') ?></th>
<th><?= Lang::string('orders-amount') ?></th>
<th><?= Lang::string('orders-value') ?></th>
<th>Action</th>
</tr>
</thead>
<tbody>
<? 
if ($bids) {
foreach ($bids as $bid) {
$blink = ($bid['id'] == $id1) ? 'blink' : '';
$double = 0;
if ($bid['market_price'] == 'Y')
$type = '<div class="identify market_order" style="background-color:#EFE62F;text-align:center;color:white;">M</div>';
elseif ($bid['fiat_price'] > 0 && !($bid['stop_price'] > 0))
$type = '<div class="identify limit_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
elseif ($bid['stop_price'] > 0 && !($bid['fiat_price'] > 0))
$type = '<div class="identify stop_order" style="background-color:#DB82FF;text-align:center;color:white;">S</div>';
elseif ($bid['stop_price'] > 0 && $bid['fiat_price'] > 0) {
$type = '<div class="identify limit_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
$double = 1;
}

echo '
<tr id="bid_'.$bid['id'].'" class="bid_tr '.$blink.'">
<input type="hidden" class="usd_price" value="'.Stringz::currency(((empty($bid['usd_price'])) ? $bid['usd_price'] : $bid['btc_price']),($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'" />
<input type="hidden" class="order_date" value="'.$bid['date'].'" />
<input type="hidden" class="is_crypto" value="'.$bid['is_crypto'].'" />
<td>'.$type.'</td>
<td><span class="currency_char">'.$CFG->currencies[$bid['currency']]['fa_symbol'].'</span><span class="order_price">'.Stringz::currency(($bid['fiat_price'] > 0) ? $bid['fiat_price'] : $bid['stop_price'],($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="order_amount">'.Stringz::currency($bid['btc'],true).'</span> '.$CFG->currencies[$bid['c_currency']]['currency'].'</td>
<td><span class="currency_char">'.$CFG->currencies[$bid['currency']]['fa_symbol'].'</span><span class="order_value">'.Stringz::currency($bid['btc'] * (($bid['fiat_price'] > 0) ? $bid['fiat_price'] : $bid['stop_price']),($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'</span></td>
<td style="width:10%;">';

echo '<a class="buy_open_order_loader'.$bid['id'].'" style="display:none;"><img src="images/loader.gif" style="width:31%;"/></a>';

echo '<a onclick="buy_cancel_order(\''.$bid['id'].'\',\''.$_SESSION["openorders_uniq"].'\',\''.$c_currencyy1.'\',\''.$currencyy1.'\',\'buy_open_orders_table\');" title="'.Lang::string('orders-delete').'" class="remove_icon'.$bid['id'].'"><i class="fa fa-times"></i></a>';

echo '</td>';
echo '</tr>';
if ($double) {
echo '
<tr id="bid_'.$bid['id'].'" class="bid_tr double">
<input type="hidden" class="is_crypto" value="'.$bid['is_crypto'].'" />
<td><div class="identify stop_order">S</div></td>
<td><span class="currency_char">'.$CFG->currencies[$bid['currency']]['fa_symbol'].'</span><span class="order_price">'.Stringz::currency($bid['stop_price'],($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="order_amount">'.Stringz::currency($bid['btc'],true).'</span> '.$CFG->currencies[$bid['c_currency']]['currency'].'</td>
<td><span class="currency_char">'.$CFG->currencies[$bid['currency']]['fa_symbol'].'</span><span class="order_value">'.Stringz::currency($bid['btc']*$bid['stop_price'],($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td>
</tr>';
}
}
}
echo '<tr id="no_bids" style="'.(is_array($bids) && count($bids) > 0 ? 'display:none;' : '').'"><td colspan="5">'.Lang::string('orders-no-bid').'</td></tr>';
?>
</tbody>
</table>

<?php } else if($buysell=='sell') { ?>



<table class="table">
<thead>
<tr>
<th>Type</th>
<th><?= Lang::string('orders-price') ?></th>
<th><?= Lang::string('orders-amount') ?></th>
<th><?= Lang::string('orders-value') ?></th>
<th>Action</th>
</tr>
</thead>
<tbody>
<? 
if ($asks) {
foreach ($asks as $ask) {
$blink = ($ask['id'] == $id1) ? 'blink' : '';
$double = 0;
if ($ask['market_price'] == 'Y')
$type = '<div class="identify market_order" style="background-color:#EFE62F;text-align:center;color:white;">M</div>';
elseif ($ask['fiat_price'] > 0 && !($ask['stop_price'] > 0))
$type = '<div class="identify limit_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
elseif ($ask['stop_price'] > 0 && !($ask['fiat_price'] > 0))
$type = '<div class="identify stop_order" style="background-color:#DB82FF;text-align:center;color:white;">S</div>';
elseif ($ask['stop_price'] > 0 && $ask['fiat_price'] > 0) {
$type = '<div class="identify limit_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
$double = 1;
}

echo '
<tr id="ask_'.$ask['id'].'" class="ask_tr '.$blink.'">
<input type="hidden" class="usd_price" value="'.Stringz::currency(((empty($ask['usd_price'])) ? $ask['usd_price'] : $ask['btc_price']),($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'" />
<input type="hidden" class="order_date" value="'.$ask['date'].'" />
<input type="hidden" class="is_crypto" value="'.$ask['is_crypto'].'" />
<td>'.$type.'</td>
<td><span class="currency_char">'.$CFG->currencies[$ask['currency']]['fa_symbol'].'</span><span class="order_price">'.Stringz::currency(($ask['fiat_price'] > 0) ? $ask['fiat_price'] : $ask['stop_price'],($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="order_amount">'.Stringz::currency($ask['btc'],true).'</span> '.$CFG->currencies[$ask['c_currency']]['currency'].'</td>
<td><span class="currency_char">'.$CFG->currencies[$ask['currency']]['fa_symbol'].'</span><span class="order_value">'.Stringz::currency($ask['btc'] * (($ask['fiat_price'] > 0) ? $ask['fiat_price'] : $ask['stop_price']),($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'</span></td>
<td style="width:10%;">'; 
echo '<a class="sell_open_order_loader'.$ask['id'].'" style="display:none;"><img src="images/loader.gif" style="width:31%;"/></a>';
echo '<a onclick="sell_cancel_order(\''.$ask['id'].'\',\''.$_SESSION["openorders_uniq"].'\',\''.$c_currencyy1.'\',\''.$currencyy1.'\',\'sell_open_orders_table\');" title="'.Lang::string('orders-delete').'" class="remove_icon_sell'.$ask['id'].'"><i class="fa fa-times"></i></a>';

echo '</td>';
echo '</tr>';
if ($double) {
echo '
<tr id="ask_'.$ask['id'].'" class="ask_tr double">
<input type="hidden" class="is_crypto" value="'.$ask['is_crypto'].'" />
<td><div class="identify stop_order">S</div></td>
<td><span class="currency_char">'.$CFG->currencies[$ask['currency']]['fa_symbol'].'</span><span class="order_price">'.Stringz::currency($ask['stop_price'],($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="order_amount">'.Stringz::currency($ask['btc'],true).'</span> '.$CFG->currencies[$ask['c_currency']]['currency'].'</td>
<td><span class="currency_char">'.$CFG->currencies[$ask['currency']]['fa_symbol'].'</span><span class="order_value">'.Stringz::currency($ask['stop_price']*$ask['btc'],($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td>
</tr>';
}
}
}
echo '<tr id="no_asks" style="'.(is_array($asks) && count($asks) > 0 ? 'display:none;' : '').'"><td colspan="5">'.Lang::string('orders-no-ask').'</td></tr>';
?>
</tbody>
</table>

<?php } ?>


<!-- Buysell forms with balance -->

<?php

echo '~';

API::add('User','getAvailable');
$feequery = API::send();
$user_available = $feequery['User']['getAvailable']['results'][0];

?>

<?= ((!empty($user_available[strtoupper($currency_info['currency'])])) ? Stringz::currency($user_available[strtoupper($currency_info['currency'])],($currency_info['is_crypto'] == 'Y')) : '0.00') ?>

<?php

echo '~';

?>

<?= Stringz::currency($user_available[strtoupper($c_currency_info['currency'])],true) ?>

<!-- End of Buysell forms with balance -->


<!-- Header block -->

<?php 
echo '~'; 

API::add('Transactions','get24hData',array($c_currency1, $currency1));
$query = API::send();
$currentPair = $query['Transactions']['get24hData']['results'][0];

?>

<li class="nav-item">
<a class="nav-link" href="#">
Last Price<br/> 
<span class="text-success"><?= number_format($currentPair['lastPrice'], 8) ?></span>
</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#">
24th Change<br/>
<span class="text-danger"><?= number_format($currentPair['change_24hrs'], 8) ?> %</span>
</a>
</li>
<li class="nav-item">
<a class="nav-link" href="#">
24th Volume<br/>
<span class="green-text"><?= number_format($currentPair['transactions_24hrs'], 8) ?></span> <?= $c_currency_info['currency'] ?></span>
</a>
</li>
<li class="nav-item dropdown">
<?php
$mystring = $_SERVER['REQUEST_URI'];
$first = strtok($mystring, '?');
if ($first == "/advanced-trade") {
$url_parame = "/advanced-trade" ;
}
if ($first == "/advanced-trade-new") {
$url_parame = "/advanced-trade-new" ;
}
?>
<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="line-height : 44px;"><?php echo str_replace("-", '/', $_REQUEST['trade']);  ?></a>
<div class="dropdown-menu" aria-labelledby="navbarDropdown" style="overflow-y: scroll;height: 330px;">

<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BTC-USD&c_currency=28&currency=27">BTC/USD</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=LTC-USD&c_currency=42&currency=27">LTC/USD</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ETH-USD&c_currency=45&currency=27">ETH/USD</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BCH-USD&c_currency=44&currency=27">BCH/USD</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ZEC-USD&c_currency=43&currency=27">ZEC/USD</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=LTC-BTC&c_currency=42&currency=28">LTC/BTC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ETH-BTC&c_currency=45&currency=28">ETH/BTC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BCH-BTC&c_currency=44&currency=28">BCH/BTC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ZEC-BTC&c_currency=43&currency=28">ZEC/BTC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BTC-LTC&c_currency=28&currency=42">BTC/LTC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ETH-LTC&c_currency=45&currency=42">ETH/LTC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ZEC-LTC&c_currency=43&currency=42">ZEC/LTC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BCH-LTC&c_currency=44&currency=42">BCH/LTC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BTC-BCH&c_currency=28&currency=44">BTC/BCH</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ETH-BCH&c_currency=45&currency=44">ETH/BCH</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ZEC-BCH&c_currency=43&currency=44">ZEC/BCH</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=LTC-BCH&c_currency=42&currency=44">LTC/BCH</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BTC-ZEC&c_currency=28&currency=43">BTC/ZEC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=LTC-ZEC&c_currency=42&currency=43">LTC/ZEC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ETH-ZEC&c_currency=45&currency=43">ETH/ZEC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BCH-ZEC&c_currency=44&currency=43">BCH/ZEC</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BTC-ETH&c_currency=28&currency=45">BTC/ETH</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=LTC-ETH&c_currency=42&currency=45">LTC/ETH</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=BCH-ETH&c_currency=44&currency=45">BCH/ETH</a>
<a class="dropdown-item" href="<?php echo $url_parame;?>?trade=ZEC-ETH&c_currency=43&currency=45">ZEC/ETH</a>
</div>
</li>

<!-- End of Header block -->


<!-- Buysell Block -->

<?php 
echo '~'; 

API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,1));
API::add('Orders','get',array(false,false,10,$c_currency1,$currency1,false,false,false,false,1));
$query = API::send();
$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];
?>

<div class="left-side-widget" style="overflow: auto;">
<ul class="nav nav-tabs" id="myTab" role="tablist">
<li class="nav-item" title="Buy Orders">
<a class="nav-link active" id="buy-tab" data-toggle="tab" href="#buy" role="tab" aria-controls="buy" aria-selected="true"><img src="sonance/img/icons/buy.jpg"><span style="font-size: 10px;margin-left: 5px;font-weight: 600;">Buy Orders</span></a>
</li>
<li class="nav-item" title="Sell Orders">
<a class="nav-link" id="sell-tab" data-toggle="tab" href="#sell" role="tab" aria-controls="sell" aria-selected="false"><img src="sonance/img/icons/sell.jpg"> <span style="font-size: 10px;margin-left: 5px;font-weight: 600;">Sell Orders</span></a>
</li>

</ul>
<div class="tab-content" id="myTab">
<div class="tab-pane fade show active" id="buy" role="tabpanel" aria-labelledby="buy-tab">
<table class="table">
<thead>
<tr>
<th>Price(<?= $currency_info['currency'] ?>)</th>
<th>Amount(<?= $c_currency_info['currency'] ?>)</th>
<th>Total(<?= $currency_info['currency'] ?>)</th>
</tr>
</thead>
<tbody>
<?php if($bids): ?>
<?php foreach($bids as $bid): ?>
<tr>
<td><span class="green-color"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($bid['btc_price'],($currency_info['is_crypto'] == 'Y')) ?></span></td>
<td><span class="gray-color"><?= Stringz::currency($bid['btc'],true) ?></span></td>
<td><span class="gray-color"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency(($bid['btc_price'] * $bid['btc']),($currency_info['is_crypto'] == 'Y')) ?></span></td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
<td colspan="3">No Buy Orders</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
<div class="tab-pane fade" id="sell" role="tabpanel" aria-labelledby="sell-tab">
<table class="table">
<thead>
<tr>
<th>Price(<?= $currency_info['currency'] ?>)</th>
<th>Amount(<?= $c_currency_info['currency'] ?>)</th>
<th>Total(<?= $currency_info['currency'] ?>)</th>
</tr>
</thead>
<tbody>
<?php if($asks): ?>
<?php foreach($asks as $ask): ?>
<tr>
<td><span class="green-color"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency($ask['btc_price'],($currency_info['is_crypto'] == 'Y')) ?></span></td>
<td><span class="gray-color"><?= Stringz::currency($ask['btc'],true) ?></span></td>
<td><span class="gray-color"><?= $currency_info['fa_symbol'] ?><?= Stringz::currency(($ask['btc_price'] * $ask['btc']),($currency_info['is_crypto'] == 'Y')) ?></span></td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
<td colspan="3">No Sell Orders</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>


<!-- End of Buysell Block -->



<!-- Trade History Block -->


<?php 
echo '~'; 

API::add('Transactions','get',array(false,false,1,$c_currency1,$currency1));
API::add('Transactions', 'get', array(false, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
$query = API::send();
$my_transactions = $query['Transactions']['get']['results'][1];

?>

<h6 class="right-side-widget-title" style="margin-left: 10px;margin-top: 12px;"><strong class="heading-three">Trade History ( <?= $c_currency_info['currency']."  - ".$currency_info['currency']; ?>)</strong>
</h6>
<div class="right-side-widget history" style="background: #f4f4f4;padding-bottom: 0;height: 225px;">
<div class="trade-history-view">
<div>
<table class="table row-border table-hover no-header" cellspacing="0" width="100%">
<thead>
<tr>
<th>Pair</th>
<th>Amount</th>
<th>Price</th>
</tr>
</thead>
<tbody>
<?php if(!count($my_transactions)): ?>
<tr>
<td col-span="3">No Trades</td>
</tr>
<?php else: ?>
<?php foreach($my_transactions as $transaction): ?>
<tr class="clickable-row" data-href="">
<td>
<div class="star-inner text-left">
<?= $c_currency_info['currency'] ?>/<?= $currency_info['currency'] ?>
</div>
</td>
<td><span class="<?= $transaction['type'] == 'Buy' ? 'green-color' : 'red-color' ?>"><?= Stringz::currency($transaction['btc'], true) . ' ' . $CFG->currencies[$transaction['c_currency']]['fa_symbol']  ?></span> </td>
<td><span class="<?= $transaction['type'] == 'Buy' ? 'green-color' : 'red-color' ?>"><?= $CFG->currencies[$transaction['currency']]['fa_symbol'] ?><?= Stringz::currency($transaction['fiat_price'], ($transaction['is_crypto'] == 'Y')) ?></span></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>


<!-- End of Trade History Block -->


<!-- Order history block -->

<?php 
echo '~'; 

?>

<div class="order-widget">
<ul class="nav nav-tabs" id="myTab" role="tablist">
<li class="nav-item">
<a class="nav-link active" id="order-history-tab" data-toggle="tab" href="#order-history" role="tab" aria-controls="order-history" aria-selected="true">Order History</a>
</li>
<li class="nav-item">
<a class="nav-link" id="open-order-tab" data-toggle="tab" href="#open-order" role="tab" aria-controls="open-order"  aria-selected="false">
Trade History</a>
</li>
<li class="nav-item">
<a class="nav-link" id="trade-history-tab" data-toggle="tab" href="#trade-history" role="tab" aria-controls="trade-history" aria-selected="false">Open Orders</a>
</li>
</ul>
<div class="tab-content" id="myTabContent" style="height: 283px;
overflow: auto;">
<?php


$c_currency1111 = $_REQUEST['c_currency'] ? : 28;
$currency1111 = $_REQUEST['currency'] ? : 27;
/* $currency1 = $_SESSION['oo_currency'];
$c_currency1 = $_SESSION['oo_c_currency']; */
$order_by11 = $_SESSION['oo_order_by'];
$trans_realized11 = (!empty($_REQUEST['transactions'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['transactions']) : false;
$id11 = (!empty($_REQUEST['id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['id']) : false;
$bypass1 = (!empty($_REQUEST['bypass']));

API::add('Orders','get',array(false,false,false,$c_currency1111,$currency1111,1,false,1,$order_by11,false,1));
API::add('Orders','get',array(false,false,false,$c_currency1111,$currency1111,1,false,false,$order_by11,1,1));
$query1111 = API::send();

$bids11 = $query1111['Orders']['get']['results'][0];
$asks11 = $query1111['Orders']['get']['results'][1];
$currency_info11 = ($currency1111) ? $CFG->currencies[strtoupper($currency1111)] : false;

$currencies = Settings::sessionCurrency();
// $page_title = Lang::string('order-book');


$c_currency1 = $_REQUEST['c_currency'] ? : 28;
$currency1 = $_REQUEST['currency'] ? : 27;

$currency_info = $CFG->currencies[$currency1];
$c_currency_info = $CFG->currencies[$c_currency1];

API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, false, false, 1));
API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, false, false, false, false, 1));
API::add('Transactions', 'get', array(false, false, 1, $c_currency1, $currency1));
$query = API::send();

$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];
$last_transaction = $query['Transactions']['get']['results'][0][0];
$last_trans_currency = ($last_transaction['currency'] == $currency_info['id']) ? false : (($last_transaction['currency1'] == $currency_info['id']) ? false : ' (' . $CFG->currencies[$last_transaction['currency1']]['currency'] . ')');
$last_trans_symbol = $currency_info['fa_symbol'];
$last_trans_color = ($last_transaction['maker_type'] == 'sell') ? 'price-green' : 'price-red';

if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']), $CFG->currencies)))
$_SESSION['oo_c_currency'] = preg_replace("/[^0-9]/", "", $_REQUEST['c_currency']);
else if (empty($_SESSION['oo_c_currency']) || $_REQUEST['c_currency'] == 'All')
$_SESSION['oo_c_currency'] = false;

if ((!empty($_REQUEST['currency']) && array_key_exists(strtoupper($_REQUEST['currency']), $CFG->currencies)))
$_SESSION['oo_currency'] = preg_replace("/[^0-9]/", "", $_REQUEST['currency']);
else if (empty($_SESSION['oo_currency']) || $_REQUEST['currency'] == 'All')
$_SESSION['oo_currency'] = false;

if ((!empty($_REQUEST['order_by'])))
$_SESSION['oo_order_by'] = preg_replace("/[^a-z]/", "", $_REQUEST['order_by']);
else if (empty($_SESSION['oo_order_by']))
$_SESSION['oo_order_by'] = false;

$open_currency1 = $_SESSION['oo_currency'];
$open_c_currency1 = $_SESSION['oo_c_currency'];
$order_by1 = $_SESSION['oo_order_by'];
$trans_realized1 = (!empty($_REQUEST['transactions'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['transactions']) : false;
$id1 = (!empty($_REQUEST['id'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['id']) : false;
$bypass = (!empty($_REQUEST['bypass']));

API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, 1, false, 1, $order_by1, false, 1));
API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, 1, false, false, $order_by1, 1, 1));
$query = API::send();

$open_bids = $query['Orders']['get']['results'][0];
$open_asks = $query['Orders']['get']['results'][1];
$open_currency_info = ($open_currency1) ? $CFG->currencies[strtoupper($open_currency1)] : false;

if (!empty($_REQUEST['new_order']) && !$trans_realized1)
Messages::add(Lang::string('transactions-orders-new-message'));
if (!empty($_REQUEST['edit_order']) && !$trans_realized1)
Messages::add(Lang::string('transactions-orders-edit-message'));
elseif (!empty($_REQUEST['new_order']) && $trans_realized1 > 0)
Messages::add(str_replace('[transactions]', $trans_realized1, Lang::string('transactions-orders-done-message')));
elseif (!empty($_REQUEST['edit_order']) && $trans_realized1 > 0)
Messages::add(str_replace('[transactions]', $trans_realized1, Lang::string('transactions-orders-done-edit-message')));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'order-doesnt-exist')
Errors::add(Lang::string('orders-order-doesnt-exist'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'not-your-order')
Errors::add(Lang::string('orders-not-yours'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'order-cancelled')
Messages::add(Lang::string('orders-order-cancelled'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'deleteall-error')
Errors::add(Lang::string('orders-order-cancelled-error'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'deleteall-success')
Messages::add(Lang::string('orders-order-cancelled-all'));

$_SESSION["openorders_uniq"] = md5(uniqid(mt_rand(), true));

//transaction

API::add('Transactions', 'get', array(1, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
$query = API::send();
$total = $query['Transactions']['get']['results'][0];

API::add('Transactions', 'get', array(false, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
API::add('Transactions', 'getTypes');
$query = API::send();

$transactions = $query['Transactions']['get']['results'][0];

?>
<div class="tab-pane fade" id="open-order" role="tabpanel" aria-labelledby="open-order-tab">
<div class="row">

<div class="col-md-12">
<? Messages::display(); ?>
<? Errors::display(); ?>
<form action="" class="form-inline" style="padding: 20px;background: white;margin-top: 20px;">
<div class="form-group">
<label for="sel1" style="font-size: 12px;">Currency Pair &nbsp;</label>
</div>
<div class="form-group">
<select class="form-control custom-select c_currency_select" id="1c_currency_select" style="width:100px;">
<? if ($CFG->currencies): ?>
<? foreach ($CFG->currencies as $key => $currency): ?>
<? if (is_numeric($key) || $currency['is_crypto'] != 'Y') continue; ?>
<option <?= $currency['id'] == $c_currency1111 ? 'selected="selected"' : '' ?>  value="<?=$currency['id']?>">
<?=$currency['currency'] ?>
</option>
<? endforeach; ?>
<? endif; ?>
</select>
</div>
<div class="form-group">
<select class="form-control custom-select currency_select" id="1currency_select" style="margin-left:5px;width:100px;">
<? if ($CFG->currencies): ?>
<? foreach ($CFG->currencies as $key => $currency): ?>
<? if (is_numeric($key) || $currency['id'] == $c_currency1111) continue; ?>
<option <?= $currency['id'] == $currency1111 ? 'selected="selected"' : '' ?>  value="<?=$currency['id']?>">
<?=$currency['currency'] ?>
</option>
<? endforeach; ?>
<? endif; ?>
</select>
</div>
</form>
<span class="float-right">
<a href="#openorders" data-toggle="modal" style="    position: relative;top: -3em;right: 1em;">
<svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" xml:space="preserve">
<circle style="fill:#47a0dc" cx="25" cy="25" r="25"></circle>
<line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"></line>
<path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
C26.355,23.457,25,26.261,25,29.158V32"></path><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
<g></g><g></g><g></g><g></g><g></g><g></g><g></g>
</svg>
</a>
</span>
</div>
</div>
<div class="row">
<table class="table">
<thead>
<tr>
<th><?= Lang::string('transactions-type') ?>s</th>
<th><?= Lang::string('transactions-time') ?></th>
<th><?= Lang::string('orders-amount') ?></th>
<th><?= Lang::string('transactions-fiat') ?></th>
<th><?= Lang::string('orders-price') ?></th>
<th><?= Lang::string('transactions-fee') ?></th>
</tr>
</thead>
<tbody>
<?php

if ($transactions) {
foreach ($transactions as $transaction) {
$trans_symbol = $CFG->currencies[$transaction['currency']]['fa_symbol'];
echo '
<tr id="transaction_' . $transaction['id'] . '">
<input type="hidden" class="is_crypto" value="' . $transaction['is_crypto'] . '" />
<td>' . $transaction['type'] . '</td>
<td><input type="hidden" class="localdate" value="' . (strtotime($transaction['date'])) . '" /></td>
<td>' . Stringz::currency($transaction['btc'], true) . ' ' . $CFG->currencies[$transaction['c_currency']]['fa_symbol'] . '</td>
<td><span class="currency_char">' . $trans_symbol . '</span><span>' . Stringz::currency($transaction['btc_net'] * $transaction['fiat_price'], ($transaction['is_crypto'] == 'Y')) . '</span></td>
<td><span class="currency_char">' . $trans_symbol . '</span><span>' . Stringz::currency($transaction['fiat_price'], ($transaction['is_crypto'] == 'Y')) . '</span></td>
<td><span class="currency_char">' . $trans_symbol . '</span><span>' . Stringz::currency($transaction['fee'] * $transaction['fiat_price'], ($transaction['is_crypto'] == 'Y')) . '</span></td>
</tr>';
}
}
echo '<tr id="no_transactions" style="' . (is_array($transactions) ? 'display:none;' : '') . '"><td colspan="6" style="padding: 0;"><div class="" style="background: #f4f6f8; text-align:  center;
"><img src="images/no-results.gif" style="width: 300px;height: auto;    float: none;" ></div></td></tr>';
?>
</tbody>
</table>
</div>
</div>
<div class="tab-pane fade" id="order-history" role="tabpanel" aria-labelledby="order-history-tab">
<?php 
$currencies1 = Settings::sessionCurrency(); 


$c_currency11 = $_REQUEST['c_currency'] ? : 28;
$currency11 = $_REQUEST['currency'] ? : 27;

$currency_info1 = $CFG->currencies[$currency1];
$c_currency_info1 = $CFG->currencies[$c_currency1];

API::add('Orders', 'get', array(false, false, false, $c_currency11, $currency11, false, false, 1));
API::add('Orders', 'get', array(false, false, false, $c_currency11, $currency11, false, false, false, false, 1));
API::add('Transactions', 'get', array(false, false, 1, $c_currency11, $currency11));
$query1 = API::send();

$bids1 = $query1['Orders']['get']['results'][0];
$asks1 = $query1['Orders']['get']['results'][1];
//var_dump($asks); exit;
$last_transaction = $query1['Transactions']['get']['results'][0][0];
$last_trans_currency = ($last_transaction['currency'] == $currency_info1['id']) ? false : (($last_transaction['currency1'] == $currency_info1['id']) ? false : ' (' . $CFG->currencies[$last_transaction['currency1']]['currency'] . ')');
$last_trans_symbol = $currency_info1['fa_symbol'];
$last_trans_color = ($last_transaction['maker_type'] == 'sell') ? 'price-green' : 'price-red';
?>


<div class="row">
<? Messages::display(); ?>
<? Errors::display(); ?>
<div class="col-md-12">
<form action="" class="form-inline" style="padding: 20px;background: white;margin-top: 20px;">
<div class="form-group">
<label for="sel1" style="font-size: 12px;">Currency Pair &nbsp;</label>
</div>
<div class="form-group">
<select class="form-control custom-select c_currency_select" id="c_currency_select" style="width:100px;">
<? if ($CFG->currencies): ?>
<? foreach ($CFG->currencies as $key => $currency): ?>
<? if (is_numeric($key) || $currency['is_crypto'] != 'Y') continue; ?>
<option <?= $currency['id'] == $c_currency1 ? 'selected="selected"' : '' ?>  value="<?=$currency['id']?>">
<?=$currency['currency'] ?>
</option>
<? endforeach; ?>
<? endif; ?>
</select>
</div>
<div class="form-group">
<select class="form-control custom-select currency_select" id="currency_select" style="margin-left:5px;width:100px;">
<? if ($CFG->currencies): ?>
<? foreach ($CFG->currencies as $key => $currency): ?>
<? if (is_numeric($key) || $currency['id'] == $c_currency1) continue; ?>
<option <?= $currency['id'] == $currency1 ? 'selected="selected"' : '' ?>  value="<?=$currency['id']?>">
<?=$currency['currency'] ?>
</option>
<? endforeach; ?>
<? endif; ?>
</select>
</div>
</form>
<span class="float-right">
<a href="#openordershist" data-toggle="modal" style="    position: relative;top: -3em;right: 1em;">
<svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" xml:space="preserve">
<circle style="fill:#47a0dc" cx="25" cy="25" r="25"></circle>
<line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"></line>
<path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
C26.355,23.457,25,26.261,25,29.158V32"></path><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
<g></g><g></g><g></g><g></g><g></g><g></g><g></g>
</svg>
</a>
</span>
</div>
</div>

<?php
$c_currency1 = $_REQUEST['c_currency'] ? : 28;
$currency1 = $_REQUEST['currency'] ? : 27;

$currency_info = $CFG->currencies[$currency1];
$c_currency_info = $CFG->currencies[$c_currency1];

API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, false, false, 1));
API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, false, false, false, false, 1));
API::add('Transactions', 'get', array(false, false, 1, $c_currency1, $currency1));
$query = API::send();

$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];
//var_dump($asks); exit;
$last_transaction = $query['Transactions']['get']['results'][0][0];
$last_trans_currency = ($last_transaction['currency'] == $currency_info['id']) ? false : (($last_transaction['currency1'] == $currency_info['id']) ? false : ' (' . $CFG->currencies[$last_transaction['currency1']]['currency'] . ')');
$last_trans_symbol = $currency_info['fa_symbol'];
$last_trans_color = ($last_transaction['maker_type'] == 'sell') ? 'price-green' : 'price-red';

if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']), $CFG->currencies)))
$_SESSION['oo_c_currency'] = preg_replace("/[^0-9]/", "", $_REQUEST['c_currency']);
else if (empty($_SESSION['oo_c_currency']) || $_REQUEST['c_currency'] == 'All')
$_SESSION['oo_c_currency'] = false;

if ((!empty($_REQUEST['currency']) && array_key_exists(strtoupper($_REQUEST['currency']), $CFG->currencies)))
$_SESSION['oo_currency'] = preg_replace("/[^0-9]/", "", $_REQUEST['currency']);
else if (empty($_SESSION['oo_currency']) || $_REQUEST['currency'] == 'All')
$_SESSION['oo_currency'] = false;

if ((!empty($_REQUEST['order_by'])))
$_SESSION['oo_order_by'] = preg_replace("/[^a-z]/", "", $_REQUEST['order_by']);
else if (empty($_SESSION['oo_order_by']))
$_SESSION['oo_order_by'] = false;

$open_currency1 = $_SESSION['oo_currency'];
$open_c_currency1 = $_SESSION['oo_c_currency'];
$order_by1 = $_SESSION['oo_order_by'];
$trans_realized1 = (!empty($_REQUEST['transactions'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['transactions']) : false;
$id1 = (!empty($_REQUEST['id'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['id']) : false;
$bypass = (!empty($_REQUEST['bypass']));

API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, 1, false, 1, $order_by1, false, 1));
API::add('Orders', 'get', array(false, false, false, $c_currency1, $currency1, 1, false, false, $order_by1, 1, 1));
$query = API::send();

$open_bids = $query['Orders']['get']['results'][0];
$open_asks = $query['Orders']['get']['results'][1];
$open_currency_info = ($open_currency1) ? $CFG->currencies[strtoupper($open_currency1)] : false;

if (!empty($_REQUEST['new_order']) && !$trans_realized1)
Messages::add(Lang::string('transactions-orders-new-message'));
if (!empty($_REQUEST['edit_order']) && !$trans_realized1)
Messages::add(Lang::string('transactions-orders-edit-message'));
elseif (!empty($_REQUEST['new_order']) && $trans_realized1 > 0)
Messages::add(str_replace('[transactions]', $trans_realized1, Lang::string('transactions-orders-done-message')));
elseif (!empty($_REQUEST['edit_order']) && $trans_realized1 > 0)
Messages::add(str_replace('[transactions]', $trans_realized1, Lang::string('transactions-orders-done-edit-message')));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'order-doesnt-exist')
Errors::add(Lang::string('orders-order-doesnt-exist'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'not-your-order')
Errors::add(Lang::string('orders-not-yours'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'order-cancelled')
Messages::add(Lang::string('orders-order-cancelled'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'deleteall-error')
Errors::add(Lang::string('orders-order-cancelled-error'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'deleteall-success')
Messages::add(Lang::string('orders-order-cancelled-all'));

$_SESSION["openorders_uniq"] = md5(uniqid(mt_rand(), true));

//transaction

API::add('Transactions', 'get', array(1, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
$query = API::send();
$total = $query['Transactions']['get']['results'][0];

API::add('Transactions', 'get', array(false, $page1, 30, $c_currency1, $currency1, 1, $start_date1, $type1, $order_by1));
API::add('Transactions', 'getTypes');
$query = API::send();

$transactions = $query['Transactions']['get']['results'][0];
$transaction_types = $query['Transactions']['getTypes']['results'][0];
$pagination = Content::pagination('transactions.php', $page1, $total, 30, 5, false);

$currency_info = ($currency1) ? $CFG->currencies[strtoupper($currency1)] : array();
?>
<div>
<!-- first table starts -->
<h3 style="margin-left: 10px;">Buy order</h3>
<table class="table">
<thead>
<tr>
<th>Type</th>
<th><?= Lang::string('orders-price') ?></th>
<th><?= Lang::string('orders-amount') ?></th>
<th><?= Lang::string('orders-value') ?></th>
</tr>
</thead>
<tbody>
<?
if ($bids) {
$i = 0;
foreach ($bids as $bid) {


$min_bid = (empty($min_bid) || $bid['btc_price'] < $min_bid) ? $bid['btc_price'] : $min_bid;
$max_bid = (empty($max_bid) || $bid['btc_price'] > $max_bid) ? $bid['btc_price'] : $max_bid;
$mine = (!empty(User::$info['user']) && $bid['user_id'] == User::$info['user'] && $bid['btc_price'] == $bid['fiat_price']) ? '<a class="fa fa-user" href="open-orders.php?id=' . $bid['id'] . '" title="' . Lang::string('home-your-order') . '"></a>' : '';
if ($bid['market_price'] == 'N' && $bid['stop_price'] > 0) {
$type = '<div class="identify stop_order" style="background-color:#DB82FF;text-align:center;color:white;">S</div>';
} elseif ($bid['market_price']) {
$type = '<div class="identify market_order" style="background-color:#EFE62F;text-align:center;color:white;">M</div>';
} {
$type = '<div class="identify market_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
}

echo '
<tr id="bid_' . $bid['id'] . '" class="bid_tr">
<td>'.$type.'</td>
<td>' . $mine . $currency_info['fa_symbol'] . '<span class="order_price">' . Stringz::currency($bid['btc_price']) . '</span> ' . (($bid['btc_price'] != $bid['fiat_price']) ? '<a title="' . str_replace('[currency]', $CFG->currencies[$bid['currency']]['currency'], Lang::string('orders-converted-from')) . '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') . '</td>
<td><span class="order_amount">' . Stringz::currency($bid['btc'], true) . '</span> ' . $c_currency_info['currency'] . '</td>
<td>' . $currency_info['fa_symbol'] . '<span class="order_value">' . Stringz::currency(($bid['btc_price'] * $bid['btc'])) . '</span></td>
</tr>';
$i++;
}
}
echo '<tr id="no_bids" style="' . ((is_array($bids) && count($bids) > 0) ? 'display:none;' : '') . '"><td colspan="4" style="padding: 0;"> <div class="" style="background: #f4f6f8; text-align:  center;
"><img src="images/no-results.gif" style="width: 300px;height: auto; float: none;" ></div></td></tr>';
?>
</tbody>
</table>

<h3 style="margin-left: 10px;">Sell order</h3>
<table class="table">
<thead>
<tr>
<th>Type</th>
<th><?= Lang::string('orders-price') ?></th>
<th><?= Lang::string('orders-amount') ?></th>
<th><?= Lang::string('orders-value') ?></th>
</tr>
</thead>
<tbody>
<?
if ($asks) {
$i = 0;
foreach ($asks as $ask) {



$min_ask = (empty($min_ask) || $ask['btc_price'] < $min_ask) ? $ask['btc_price'] : $min_ask;
$max_ask = (empty($max_ask) || $ask['btc_price'] > $max_ask) ? $ask['btc_price'] : $max_ask;
$mine = (!empty(User::$info['user']) && $ask['user_id'] == User::$info['user'] && $ask['btc_price'] == $ask['fiat_price']) ? '<a class="fa fa-user" href="open-orders.php?id=' . $ask['id'] . '" title="' . Lang::string('home-your-order') . '"></a>' : '';

if ($ask['market_price'] == 'N') {
$type = '<div class="identify stop_order" style="background-color:#DB82FF;text-align:center;color:white;">S</div>';
} else {
$type =  '<div class="identify market_order" style="background-color:#EFE62F;text-align:center;color:white;">M</div>';
}


echo '
<tr id="ask_' . $ask['id'] . '" class="ask_tr">
<td>'.$type.'</td>
<td>' . $mine . $currency_info['fa_symbol'] . '<span class="order_price">' . Stringz::currency($ask['btc_price']) . '</span> ' . (($ask['btc_price'] != $ask['fiat_price']) ? '<a title="' . str_replace('[currency]', $CFG->currencies[$ask['currency']]['currency'], Lang::string('orders-converted-from')) . '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') . '</td>
<td><span class="order_amount">' . Stringz::currency($ask['btc'], true) . '</span> ' . $c_currency_info['currency'] . '</td>
<td>' . $currency_info['fa_symbol'] . '<span class="order_value">' . Stringz::currency(($ask['btc_price'] * $ask['btc'])) . '</span></td>
</tr>';
$i++;
}
}
echo '<tr id="no_asks" style="' . ((is_array($asks) && count($asks) > 0) ? 'display:none;' : '') . '"><td colspan="4" style="padding:0;">  <div class="" style="text-align: center;display: inline-block;width: 100%;margin: auto;background: #f4f5f8;;
"><img src="images/no-results.gif" style="width: 300px;height: auto;float: none;" ></div></td></tr>';
?>
</tbody>
</table>
</div>
</div>
<?php

$currencies11 = Settings::sessionCurrency();


$c_currency111 = $_REQUEST['c_currency'] ? : 28;
$currency111 = $_REQUEST['currency'] ? : 27;


$currency_info11 = $CFG->currencies[$currency1];
$c_currency_info11 = $CFG->currencies[$c_currency1];

API::add('Orders', 'get', array(false, false, false, $c_currency111, $currency111, false, false, 1));
API::add('Orders', 'get', array(false, false, false, $c_currency111, $currency111, false, false, false, false, 1));
API::add('Transactions', 'get', array(false, false, 1, $c_currency111, $currency111));
$query111 = API::send();

$bids1 = $quer111y['Orders']['get']['results'][0];
$asks1 = $query111['Orders']['get']['results'][1];
$last_transaction1 = $query111['Transactions']['get']['results'][0][0];
$last_trans_currency1 = ($last_transaction1['currency'] == $currency_info11['id']) ? false : (($last_transaction1['currency1'] == $currency_info11['id']) ? false : ' (' . $CFG->currencies11[$last_transaction1['currency1']]['currency'] . ')');
$last_trans_symbol1 = $currency_info11['fa_symbol'];
$last_trans_color1 = ($last_transaction1['maker_type'] == 'sell') ? 'price-green' : 'price-red';



API::add('Transactions', 'get', array(1, $page1, 30, $c_currency1, $currency111, 1, $start_date1, $type1, $order_by1));
$query = API::send();
$total = $query['Transactions']['get']['results'][0];

API::add('Transactions', 'get', array(false, $page1, 30, $c_currency1, $currency111, 1, $start_date1, $type1, $order_by1));
API::add('Transactions', 'getTypes');
$query = API::send();

$transactions = $query['Transactions']['get']['results'][0];
$transaction_types = $query['Transactions']['getTypes']['results'][0];
$pagination = Content::pagination('transactions.php', $page1, $total, 30, 5, false);

$currency_info = ($currency1) ? $CFG->currencies[strtoupper($currency1)] : array();

?>
<div class="tab-pane fade show active" id="trade-history" role="tabpanel" aria-labelledby="trade-history-tab">
<div class="row">
<? Messages::display(); ?>
<? Errors::display(); ?>
<div class="col-md-12">
<form action="" class="form-inline" style="padding: 20px;background: white;margin-top: 20px;">
<div class="form-group">
<label for="sel1" style="font-size: 12px;">Currency Pair &nbsp;
</label>

</div>
<div class="form-group">
<select class="form-control custom-select c_currency_select " id="2c_currency_select" style="width:100px;">
<? if ($CFG->currencies): ?>
<? foreach ($CFG->currencies as $key => $currency): ?>
<? if (is_numeric($key) || $currency['is_crypto'] != 'Y') continue; ?>
<option <?= $currency['id'] == $c_currency11 ? 'selected="selected"' : '' ?>  value="<?=$currency['id']?>">
<?=$currency['currency'] ?>
</option>
<? endforeach; ?>
<? endif; ?>
</select>
</div>
<div class="form-group">
<select class="form-control custom-select currency_select" id="2currency_select" style="margin-left:5px;width:100px;">
<? if ($CFG->currencies): ?>
<? foreach ($CFG->currencies as $key => $currency): ?>
<? if (is_numeric($key) || $currency['id'] == $c_currency11) continue; ?>
<option <?= $currency['id'] == $currency11 ? 'selected="selected"' : '' ?>  value="<?=$currency['id']?>">
<?=$currency['currency'] ?>
</option>
<? endforeach; ?>
<? endif; ?>
</select>
</div>
<div class="form-group" style="margin-left:10px">
<a class="download" href="transactions_downloaded.php?c_currency=<?php echo $c_currency1; ?>&currency=<?php echo $currency1; ?>" ><i class="fa fa-download"></i> <?= Lang::string('transactions-download') ?></a>
</div>
</form>
<span class="float-right">
<a href="#tradehistory" data-toggle="modal" style="    position: relative;top: -3em;right: 1em;">
<svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" xml:space="preserve">
<circle style="fill:#47a0dc" cx="25" cy="25" r="25"></circle>
<line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"></line>
<path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
C26.355,23.457,25,26.261,25,29.158V32"></path><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
<g></g><g></g><g></g><g></g><g></g><g></g><g></g>
</svg>
</a>
</span>
</div>
</div>

<?php
$c_currencyy1 = $_REQUEST['c_currency'];
$currencyy1 = $_REQUEST['currency'];
$currency_trade = $_REQUEST['trade'];
$delete_id1 = (!empty($_REQUEST['delete_id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['delete_id']) : false;

$delete_all = (!empty($_REQUEST['delete_all']));

if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']),$CFG->currencies)))
$_SESSION['oo_c_currency'] = preg_replace("/[^0-9]/", "",$_REQUEST['c_currency']);
else if (empty($_SESSION['oo_c_currency']) || $_REQUEST['c_currency'] == 'All')
$_SESSION['oo_c_currency'] = false;

if ((!empty($_REQUEST['currency']) && array_key_exists(strtoupper($_REQUEST['currency']),$CFG->currencies)))
$_SESSION['oo_currency'] = preg_replace("/[^0-9]/", "",$_REQUEST['currency']);
else if (empty($_SESSION['oo_currency']) || $_REQUEST['currency'] == 'All')
$_SESSION['oo_currency'] = false;

if ((!empty($_REQUEST['order_by'])))
$_SESSION['oo_order_by'] = preg_replace("/[^a-z]/", "",$_REQUEST['order_by']);
else if (empty($_SESSION['oo_order_by']))
$_SESSION['oo_order_by'] = false;

$c_currency1 = $_REQUEST['c_currency'] ? : 28;
$currency1 = $_REQUEST['currency'] ? : 27;
/* $currency1 = $_SESSION['oo_currency'];
$c_currency1 = $_SESSION['oo_c_currency']; */
$order_by1 = $_SESSION['oo_order_by'];
$trans_realized1 = (!empty($_REQUEST['transactions'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['transactions']) : false;
$id1 = (!empty($_REQUEST['id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['id']) : false;
$bypass = (!empty($_REQUEST['bypass']));

API::add('Orders','get',array(false,false,false,$c_currency1,$currency1,1,false,1,$order_by1,false,1));
API::add('Orders','get',array(false,false,false,$c_currency1,$currency1,1,false,false,$order_by1,1,1));
$query = API::send();

$bids = $query['Orders']['get']['results'][0];
$asks = $query['Orders']['get']['results'][1];
$currency_info = ($currency1) ? $CFG->currencies[strtoupper($currency1)] : false;

if (!empty($_REQUEST['new_order']) && !$trans_realized1)
Messages::add(Lang::string('transactions-orders-new-message'));
if (!empty($_REQUEST['edit_order']) && !$trans_realized1)
Messages::add(Lang::string('transactions-orders-edit-message'));
elseif (!empty($_REQUEST['new_order']) && $trans_realized1 > 0)
Messages::add(str_replace('[transactions]',$trans_realized1,Lang::string('transactions-orders-done-message')));
elseif (!empty($_REQUEST['edit_order']) && $trans_realized1 > 0)
Messages::add(str_replace('[transactions]',$trans_realized1,Lang::string('transactions-orders-done-edit-message')));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'order-doesnt-exist')
Errors::add(Lang::string('orders-order-doesnt-exist'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'not-your-order')
Errors::add(Lang::string('orders-not-yours'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'order-cancelled')
Messages::add(Lang::string('orders-order-cancelled'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'deleteall-error')
Errors::add(Lang::string('orders-order-cancelled-error'));
elseif (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'deleteall-success')
Messages::add(Lang::string('orders-order-cancelled-all'));

$page_title = Lang::string('openorders');
$_SESSION["openorders_uniq"] = md5(uniqid(mt_rand(),true));
?>
<div class="row">
<!-- open order table -->

<div id="buy_open_orders_table" style="width: 100%;">
<table class="table">
<thead>
<tr>
<th>Type</th>
<th><?= Lang::string('orders-price') ?></th>
<th><?= Lang::string('orders-amount') ?></th>
<th><?= Lang::string('orders-value') ?></th>
<th>Action</th>
</tr>
</thead>
<tbody>
<? 
if ($bids) {
foreach ($bids as $bid) {
$blink = ($bid['id'] == $id1) ? 'blink' : '';
$double = 0;
if ($bid['market_price'] == 'Y')
$type = '<div class="identify market_order" style="background-color:#EFE62F;text-align:center;color:white;">M</div>';
elseif ($bid['fiat_price'] > 0 && !($bid['stop_price'] > 0))
$type = '<div class="identify limit_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
elseif ($bid['stop_price'] > 0 && !($bid['fiat_price'] > 0))
$type = '<div class="identify stop_order" style="background-color:#DB82FF;text-align:center;color:white;">S</div>';
elseif ($bid['stop_price'] > 0 && $bid['fiat_price'] > 0) {
$type = '<div class="identify limit_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
$double = 1;
}

echo '
<tr id="bid_'.$bid['id'].'" class="bid_tr '.$blink.'">
<input type="hidden" class="usd_price" value="'.Stringz::currency(((empty($bid['usd_price'])) ? $bid['usd_price'] : $bid['btc_price']),($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'" />
<input type="hidden" class="order_date" value="'.$bid['date'].'" />
<input type="hidden" class="is_crypto" value="'.$bid['is_crypto'].'" />
<td>'.$type.'</td>
<td><span class="currency_char">'.$CFG->currencies[$bid['currency']]['fa_symbol'].'</span><span class="order_price">'.Stringz::currency(($bid['fiat_price'] > 0) ? $bid['fiat_price'] : $bid['stop_price'],($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="order_amount">'.Stringz::currency($bid['btc'],true).'</span> '.$CFG->currencies[$bid['c_currency']]['currency'].'</td>
<td><span class="currency_char">'.$CFG->currencies[$bid['currency']]['fa_symbol'].'</span><span class="order_value">'.Stringz::currency($bid['btc'] * (($bid['fiat_price'] > 0) ? $bid['fiat_price'] : $bid['stop_price']),($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'</span></td>
<td style="width:10%;">';

echo '<a class="buy_open_order_loader'.$bid['id'].'" style="display:none;"><img src="images/loader.gif" style="width:31%;"/></a>';

echo '<a onclick="buy_cancel_order(\''.$bid['id'].'\',\''.$_SESSION["openorders_uniq"].'\',\''.$c_currencyy1.'\',\''.$currencyy1.'\',\'buy_open_orders_table\');" title="'.Lang::string('orders-delete').'" class="remove_icon'.$bid['id'].'"><i class="fa fa-times"></i></a>';

echo '</td>
</tr>';
// <a href="edit_userbuy.php?trade=BTC-USD&order_id='.$bid['id'].'" title="'.Lang::string('orders-edit').'"><i class="fa fa-edit"></i></a>
if ($double) {
echo '
<tr id="bid_'.$bid['id'].'" class="bid_tr double">
<input type="hidden" class="is_crypto" value="'.$bid['is_crypto'].'" />
<td><div class="identify stop_order">S</div></td>
<td><span class="currency_char">'.$CFG->currencies[$bid['currency']]['fa_symbol'].'</span><span class="order_price">'.Stringz::currency($bid['stop_price'],($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="order_amount">'.Stringz::currency($bid['btc'],true).'</span> '.$CFG->currencies[$bid['c_currency']]['currency'].'</td>
<td><span class="currency_char">'.$CFG->currencies[$bid['currency']]['fa_symbol'].'</span><span class="order_value">'.Stringz::currency($bid['btc']*$bid['stop_price'],($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td>
</tr>';
}
}
}
echo '<tr id="no_bids" style="'.(is_array($bids) && count($bids) > 0 ? 'display:none;' : '').'"><td colspan="5">'.Lang::string('orders-no-bid').'</td></tr>';
?>
</tbody>
</table>
</div>

<div id="sell_open_orders_table" style="width: 100%;">
<table class="table">
<thead>
<tr>
<th>Type</th>
<th><?= Lang::string('orders-price') ?></th>
<th><?= Lang::string('orders-amount') ?></th>
<th><?= Lang::string('orders-value') ?></th>
<th>Action</th>
</tr>
</thead>
<tbody>
<? 
if ($asks) {
foreach ($asks as $ask) {
$blink = ($ask['id'] == $id1) ? 'blink' : '';
$double = 0;
if ($ask['market_price'] == 'Y')
$type = '<div class="identify market_order" style="background-color:#EFE62F;text-align:center;color:white;">M</div>';
elseif ($ask['fiat_price'] > 0 && !($ask['stop_price'] > 0))
$type = '<div class="identify limit_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
elseif ($ask['stop_price'] > 0 && !($ask['fiat_price'] > 0))
$type = '<div class="identify stop_order" style="background-color:#DB82FF;text-align:center;color:white;">S</div>';
elseif ($ask['stop_price'] > 0 && $ask['fiat_price'] > 0) {
$type = '<div class="identify limit_order" style="background-color:#FF8282;text-align:center;color:white;">L</div>';
$double = 1;
}

echo '
<tr id="ask_'.$ask['id'].'" class="ask_tr '.$blink.'">
<input type="hidden" class="usd_price" value="'.Stringz::currency(((empty($ask['usd_price'])) ? $ask['usd_price'] : $ask['btc_price']),($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'" />
<input type="hidden" class="order_date" value="'.$ask['date'].'" />
<input type="hidden" class="is_crypto" value="'.$ask['is_crypto'].'" />
<td>'.$type.'</td>
<td><span class="currency_char">'.$CFG->currencies[$ask['currency']]['fa_symbol'].'</span><span class="order_price">'.Stringz::currency(($ask['fiat_price'] > 0) ? $ask['fiat_price'] : $ask['stop_price'],($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="order_amount">'.Stringz::currency($ask['btc'],true).'</span> '.$CFG->currencies[$ask['c_currency']]['currency'].'</td>
<td><span class="currency_char">'.$CFG->currencies[$ask['currency']]['fa_symbol'].'</span><span class="order_value">'.Stringz::currency($ask['btc'] * (($ask['fiat_price'] > 0) ? $ask['fiat_price'] : $ask['stop_price']),($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'</span></td>
<td style="width:10%;">';

echo '<a class="sell_open_order_loader'.$ask['id'].'" style="display:none;"><img src="images/loader.gif" style="width:31%;"/></a>';
echo '<a onclick="sell_cancel_order(\''.$ask['id'].'\',\''.$_SESSION["openorders_uniq"].'\',\''.$c_currencyy1.'\',\''.$currencyy1.'\',\'sell_open_orders_table\');" title="'.Lang::string('orders-delete').'" class="remove_icon_sell'.$ask['id'].'"><i class="fa fa-times"></i></a>';

echo '</td>
</tr>';
// <a href="edit_userbuy.php?trade=BTC-USD&order_id='.$ask['id'].'" title="'.Lang::string('orders-edit').'"><i class="fa fa-edit"></i></a>
if ($double) {
echo '
<tr id="ask_'.$ask['id'].'" class="ask_tr double">
<input type="hidden" class="is_crypto" value="'.$ask['is_crypto'].'" />
<td><div class="identify stop_order">S</div></td>
<td><span class="currency_char">'.$CFG->currencies[$ask['currency']]['fa_symbol'].'</span><span class="order_price">'.Stringz::currency($ask['stop_price'],($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="order_amount">'.Stringz::currency($ask['btc'],true).'</span> '.$CFG->currencies[$ask['c_currency']]['currency'].'</td>
<td><span class="currency_char">'.$CFG->currencies[$ask['currency']]['fa_symbol'].'</span><span class="order_value">'.Stringz::currency($ask['stop_price']*$ask['btc'],($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')).'</span></td>
<td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td>
</tr>';
}
}
}
echo '<tr id="no_asks" style="'.(is_array($asks) && count($asks) > 0 ? 'display:none;' : '').'"><td colspan="5">'.Lang::string('orders-no-ask').'</td></tr>';
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>



<!-- End of Order history block -->

<script type="text/javascript" src="js/ops.js?v=20160210"></script>