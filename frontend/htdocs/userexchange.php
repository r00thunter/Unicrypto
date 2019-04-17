<!doctype html>
<html>

<head>
<title>Profile <?= $CFG->exchange_name; ?></title>

<meta property="viewport" name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/new-style.css" rel="stylesheet" />
<link href="css/dashboard.css" rel="stylesheet" />
<link href="css/profile.css" rel="stylesheet" />

<!-- <link rel="stylesheet" href="css/style.css?v=20160204" type="text/css" /> -->
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$("div").click(function() {
window.location = $(this).find("a").attr("href");
return false;
});
</script>

<meta name="viewport" content="width=device-width, initial-scale=1.0" data-react-helmet="true">
<style>

.PriceChart__ChartAxis-hnvXTZ.kSqzO.Flex__Flex-fVJVYW.iDqRrV,
.PriceChart__ChartAxis-hnvXTZ.kSqzOS.Flex__Flex-fVJVYW.iDqRrV{
display: inline;
}

.messages{
    top: 7em;
}

#graph_orders {
    width: 100%;
    height: 300px;
    float: left;
}
#tooltip, #tooltip1 {
    padding: 5px 8px;
    position: absolute;
    float: left;
    background-color: #FFF;
    border: 1px solid #cccccc;
    display: none;
    -moz-box-shadow: 0px 0px 4px #cccccc;
    -webkit-box-shadow: 0px 0px 8px #cccccc;
    box-shadow: 0px 0px 4px #cccccc;
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";
    -moz-opacity: 0.8;
    -khtml-opacity: 0.8;
    opacity: 0.8;
}

.filters {
    font-size: 14px;
}
ul.list_empty {
    float: left;
    padding: 0 0 0 2em;
    margin: 0px;
    width: 100%;
}
ul.list_empty input,
ul.list_empty select{
	border: 1px solid #DAE1E9;
    padding: 10px;
    outline: none;
    border-radius: 4px;
    background-color: #fff;
    font-size: 16px;
    font-weight: 400;
}
ul.list_empty select{
	font-size: 14px;
}
ul {
    list-style: none;
}
.list_empty li {
    float: left;
    padding: 0px;
    margin: 0px 20px 35px 0px;
}
.list_empty li {
    float: left;
    padding: 0px;
    margin: 0px 20px 20px 0px;
    display: inline-flex;
}
.filters form{
    display: inline-block;
    margin-bottom:0;
}
.filters form label{
    min-width: 85px;
    position: relative;
    top:8px;
}
#tooltip .price, #tooltip1 .price {
    color: #1166d1;
}
.kmYTnN {
    position: static !important;
    margin-top: 1em !important;
    height: auto !important;
    border-top: none !important;
    padding-bottom: 1em;
}
</style>
</head>

<body class="app signed-in static_application index" data-controller-name="static_application" data-action-name="index" data-view-name="Coinbase.Views.StaticApplication.Index" data-account-id="">
<?php

// error_reporting(E_ERROR | E_WARNING | E_PARSE);
// ini_set('display_errors', 1);
include '../lib/common.php';


if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
    Link::redirect('usersecurity');
elseif (User::$awaiting_token)
    Link::redirect('verify_token');
elseif (!User::isLoggedIn())
    Link::redirect('login');


// if(empty(User::$ekyc_data) || User::$ekyc_data[0]->status != 'accepted')
// {
//     Link::redirect('ekyc');
// }


$currencies = Settings::sessionCurrency();
// $page_title = Lang::string('order-book');

$currency1 = $currencies['currency'];
$c_currency1 = $currencies['c_currency'];
if(!$currency1 || empty($currency1)){
    $currency1 = 13 ;
}
if(!$currency1 || empty($currency1)){
    $currency1 = 28 ;
}
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



// $delete_id1 = (!empty($_REQUEST['delete_id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['delete_id']) : false;
// if ($delete_id1 > 0 && $_SESSION["openorders_uniq"] == $_REQUEST['uniq']) {
// 	API::add('Orders','getRecord',array($delete_id1));
// 	$query = API::send();
// 	$del_order = $query['Orders']['getRecord']['results'][0];

// 	if (!$del_order) {
// 		Link::redirect('open-orders.php?message=order-doesnt-exist');
// 	}
// 	elseif ($del_order['site_user'] != $del_order['user_id'] || !($del_order['id'] > 0)) {
// 		Link::redirect('open-orders.php?message=not-your-order');
// 	}
// 	else {
// 		API::add('Orders','delete',array($delete_id1));
// 		$query = API::send();
		
// 		Link::redirect('open-orders.php?message=order-cancelled');
// 	}
// }

// $delete_all = (!empty($_REQUEST['delete_all']));
// if ($delete_all && $_SESSION["openorders_uniq"] == $_REQUEST['uniq']) {
// 	API::add('Orders','deleteAll');
// 	$query = API::send();
// 	$del_order = $query['Orders']['deleteAll']['results'][0];

// 	if (!$del_order)
// 		Link::redirect('open-orders.php?message=deleteall-error');
// 	else
// 		Link::redirect('open-orders.php?message=deleteall-success');
// }

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

if ($trans_realized1 > 0)
    Messages::add(str_replace('[transactions]', $trans_realized1, Lang::string('transactions-done-message')));


?>
<div id="root">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Toasts__Container-kTLjCb jeFCaz"></div>
<div class="Layout__Container-jkalbK gCVQUv Flex__Flex-fVJVYW bHipRv">
<div class="LayoutDesktop__AppWrapper-cPGAqn WhXLX Flex__Flex-fVJVYW bHipRv">
<? include 'includes/topheader.php'; ?>
<div class="LayoutDesktop__ContentContainer-cdKOaO cpwUZB Flex__Flex-fVJVYW bHipRv">


<? include 'includes/menubar.php'; ?>
<div class="banner">
    <div class="container content">
        <h1>Exchange</h1>
    </div>
</div>
<style>
#tv-medium-widget iframe{
    height: 350px !important;
}
</style>



<div class="LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">






<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">





<div class="Trade__TradeView-eeHZtW jkvMB Flex__Flex-fVJVYW iDqRrV">
<div class="Trade__TradeFormContainer-lhJJLd hNLWXE Flex__Flex-fVJVYW iDqRrV" style="width:100%;">






<div class="PriceChart__Container-klmtfG fkKUHd Panel__Container-hCUKEb gmOPIV">
<? Messages::display(); ?>
		<? Errors::display(); ?>
<div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
    <div class="Flex__Flex-fVJVYW iDqRrV">
        <div class="Flex__Flex-fVJVYW iDqRrV">
            <div class="PriceChart__PriceHeading-iIpDul gaOoIW Flex__Flex-fVJVYW jGNjWx">

                <div class="Flex__Flex-fVJVYW reCYb">
                    <h4 class="PriceChart__HeadingTitle-bZuIYw eopEKS Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">Chart</h4>

                </div>
            </div>

        </div>
    </div>

</div>
<!-- TradingView Widget BEGIN -->
                                    <div class="tradingview-widget-container">
                                    <div id="tv-medium-widget"></div>
                                    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                                    <script type="text/javascript">
                                    new TradingView.MediumWidget(
                                    {
                                    "container_id": "tv-medium-widget",
                                    "symbols": [
                                    "BITFINEX:BTCUSD|1d"
                                    ],
                                    "greyText": "Quotes by",
                                    "gridLineColor": "#e9e9ea",
                                    "fontColor": "#83888D",
                                    "underLineColor": "#dbeffb",
                                    "trendLineColor": "#4bafe9",
                                    "width": "100%",
                                    "height": "100%",
                                    "locale": "in"
                                    }
                                    );
                                    </script>
                                    </div>
 <div class="filters">
    <input type="hidden" id="language_selector" value="en">
			<input type="hidden" id="is_crypto" value="<?= $currency_info['is_crypto'] ?>" />
			<form method="GET" action="userexchange.php">
				<ul class="list_empty">
					<li>
						<label for="c_currency"><?= Lang::string('market') ?></label>
						<select id="c_currency" name="currency">
                            
							<?
        if ($CFG->currencies) {
            foreach ($CFG->currencies as $key => $currency) {
                if (is_numeric($key) || $currency['is_crypto'] != 'Y')
                    continue;

                echo '<option ' . (($currency['id'] == $c_currency1) ? 'selected="selected"' : '') . ' value="' . $currency['id'] . '">' . $currency['currency'] . '</option>';
            }
        }
        ?>
						</select>
					</li>
					<li>
						<label for="ob_currency"><?= Lang::string('currency') ?></label>
						<select id="ob_currency" name="currency">
							<?
        if ($CFG->currencies) {
            foreach ($CFG->currencies as $key => $currency) {
                if (is_numeric($key) || $currency['id'] == $c_currency1)
                    continue;

                    echo '<option '.(($currency['id'] == $currency1) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';								
            }
        }
        ?>
						</select>
					</li>
					<li>
                        <label for="last_price"><?= Lang::string('home-stats-last-price') ?>&nbsp; <a target="_blank" href="" title="<?= Lang::string('order-book-last-price-explain') ?>"><i class="fa fa-question-circle"></i></a></label>
						<input type="text" id="last_price" class="<?= $last_trans_color ?>" value="<?= ($currency_info['is_crypto'] != 'Y' ? $last_trans_symbol : '') . Stringz::currency($last_transaction['btc_price'], 2, 4) ?>" disabled="disabled" />
						
					</li>
				</ul>
			</form>
			<div class="clear"></div>
</div> 
</div>
</div>

</div>
</div><!-- Block 2 -->


<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">

<div class="Flex__Flex-fVJVYW gsOGkq" >


<div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; margin-bottom:0px;">
<div class="Flex__Flex-fVJVYW bHipRv">
    <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">
            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"> <?= Lang::string('order-book') ?> Buy</h4>
        </div>
        
    </div>


    <div class="Flex__Flex-fVJVYW iJJJTg">
        <div class="Flex__Flex-fVJVYW bHipRv">
          <div class="table-otr">
            <table id="bids_list">
              <tr>
              <th><?= Lang::string('orders-price') ?></th>
        				<th><?= Lang::string('orders-amount') ?></th>
        				<th><?= Lang::string('orders-value') ?></th>
              </tr>
              <?
                if ($bids) {
                    $i = 0;
                    foreach ($bids as $bid) {
                        $min_bid = (empty($min_bid) || $bid['btc_price'] < $min_bid) ? $bid['btc_price'] : $min_bid;
                        $max_bid = (empty($max_bid) || $bid['btc_price'] > $max_bid) ? $bid['btc_price'] : $max_bid;
                        $mine = (!empty(User::$info['user']) && $bid['user_id'] == User::$info['user'] && $bid['btc_price'] == $bid['fiat_price']) ? '<a class="fa fa-user" href="open-orders.php?id=' . $bid['id'] . '" title="' . Lang::string('home-your-order') . '"></a>' : '';

                        echo '
					<tr id="bid_' . $bid['id'] . '" class="bid_tr">
						<td>' . $mine . $currency_info['fa_symbol'] . '<span class="order_price">' . Stringz::currency($bid['btc_price']) . '</span> ' . (($bid['btc_price'] != $bid['fiat_price']) ? '<a title="' . str_replace('[currency]', $CFG->currencies[$bid['currency']]['currency'], Lang::string('orders-converted-from')) . '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') . '</td>
						<td><span class="order_amount">' . Stringz::currency($bid['btc'], true) . '</span> ' . $c_currency_info['currency'] . '</td>
						<td>' . $currency_info['fa_symbol'] . '<span class="order_value">' . Stringz::currency(($bid['btc_price'] * $bid['btc'])) . '</span></td>
					</tr>';
                        $i++;
                    }
                }
                echo '<tr id="no_bids" style="' . ((is_array($bids) && count($bids) > 0) ? 'display:none;' : '') . '"><td colspan="4"> <div class="" style=" text-align:  center;
                    "><img src="images/no-results.gif" style="width: 300px;height: auto;" ></div></td></tr>';
                ?>
            </table>
            </div>

        </div>
    </div>

</div>

</div>





<div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; width:599px; margin-bottom:0px;">
<div class="Flex__Flex-fVJVYW bHipRv">
    <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">


            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">
            <?= Lang::string('order-book') ?> Sell
            </h4>
        </div>
        <div class="WidgetHeader__Actions-bDbtim jQqaGc">
            <div class="Flex__Flex-fVJVYW iDqRrV">


            </div>
        </div>
    </div>
    <div class="Flex__Flex-fVJVYW iJJJTg">
         <div class="table-otr">
          <table id="asks_list">
            <tr>
            <th><?= Lang::string('orders-price') ?></th>
        				<th><?= Lang::string('orders-amount') ?></th>
        				<th><?= Lang::string('orders-value') ?></th>
            </tr>
            <?
            if ($asks) {
                $i = 0;
                foreach ($asks as $ask) {
                    $min_ask = (empty($min_ask) || $ask['btc_price'] < $min_ask) ? $ask['btc_price'] : $min_ask;
                    $max_ask = (empty($max_ask) || $ask['btc_price'] > $max_ask) ? $ask['btc_price'] : $max_ask;
                    $mine = (!empty(User::$info['user']) && $ask['user_id'] == User::$info['user'] && $ask['btc_price'] == $ask['fiat_price']) ? '<a class="fa fa-user" href="open-orders.php?id=' . $ask['id'] . '" title="' . Lang::string('home-your-order') . '"></a>' : '';

                    echo '
					<tr id="ask_' . $ask['id'] . '" class="ask_tr">
						<td>' . $mine . $currency_info['fa_symbol'] . '<span class="order_price">' . Stringz::currency($ask['btc_price']) . '</span> ' . (($ask['btc_price'] != $ask['fiat_price']) ? '<a title="' . str_replace('[currency]', $CFG->currencies[$ask['currency']]['currency'], Lang::string('orders-converted-from')) . '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') . '</td>
						<td><span class="order_amount">' . Stringz::currency($ask['btc'], true) . '</span> ' . $c_currency_info['currency'] . '</td>
						<td>' . $currency_info['fa_symbol'] . '<span class="order_value">' . Stringz::currency(($ask['btc_price'] * $ask['btc'])) . '</span></td>
					</tr>';
                    $i++;
                }
            }
            echo '<tr id="no_asks" style="' . ((is_array($asks) && count($asks) > 0) ? 'display:none;' : '') . '"><td colspan="3">  <div class="" style=" text-align:  center;
                "><img src="images/no-results.gif" style="width: 300px;height: auto;" ></div></td></tr>';
            ?>
          </table>
          </div>
    </div>
</div>
</div>
</div>

</div>

<!-- Block 3 -->
<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">

<div class="Flex__Flex-fVJVYW gsOGkq" >


<div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; margin-bottom:0px;">
<div class="Flex__Flex-fVJVYW bHipRv">
    <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">
            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('open-orders') ?> Buy</h4>
            <input type="hidden" id="open_orders_user" value="1" />
			<input type="hidden" id="uniq" value="<?= $_SESSION["openorders_uniq"] ?>" />
        </div>
        <div class="WidgetHeader__Actions-bDbtim jQqaGc">
            <div class="Flex__Flex-fVJVYW iDqRrV">
               

            </div>
        </div>
    </div>
    <!-- <td><a href="edit-order.php?order_id='.$bid['id'].'" title="'.Lang::string('orders-edit').'">edit</a> <a href="open-orders.php?delete_id='.$bid['id'].'&uniq='.$_SESSION["openorders_uniq"].'" title="'.Lang::string('orders-delete').'">delete</a></td> -->
    <div class="Flex__Flex-fVJVYW iJJJTg">
        <div class="Flex__Flex-fVJVYW bHipRv">
            <div class="table-otr" id="order_by">
             <table id="openbids_list">
              <tr>
              <th></th>
	        				<th><?= Lang::string('orders-price') ?></th>
	        				<th><?= Lang::string('orders-amount') ?></th>
	        				<th><?= Lang::string('orders-value') ?></th>
	        				<!-- <th></th> -->
              </tr>
              <?
                if ($open_bids) {
                    foreach ($open_bids as $bid) {
                        $blink = ($bid['id'] == $id1) ? 'blink' : '';
                        $double = 0;
                        if ($bid['market_price'] == 'Y')
                            $type = '<div class="identify market_order">M</div>';
                        elseif ($bid['fiat_price'] > 0 && !($bid['stop_price'] > 0))
                            $type = '<div class="identify limit_order">L</div>';
                        elseif ($bid['stop_price'] > 0 && !($bid['fiat_price'] > 0))
                            $type = '<div class="identify stop_order">S</div>';
                        elseif ($bid['stop_price'] > 0 && $bid['fiat_price'] > 0) {
                            $type = '<div class="identify limit_order">L</div>';
                            $double = 1;
                        }

                        echo '
						<tr id="openbid_' . $bid['id'] . '" class="openbid_tr ' . $blink . '">
							<input type="hidden" class="usd_price" value="' . Stringz::currency(((empty($bid['usd_price'])) ? $bid['usd_price'] : $bid['btc_price']), ($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')) . '" />
							<input type="hidden" class="order_date" value="' . $bid['date'] . '" />
							<input type="hidden" class="is_crypto" value="' . $bid['is_crypto'] . '" />
							<td>' . $type . '</td>
							<td><span class="currency_char">' . $CFG->currencies[$bid['currency']]['fa_symbol'] . '</span><span class="order_price">' . Stringz::currency(($bid['fiat_price'] > 0) ? $bid['fiat_price'] : $bid['stop_price'], ($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')) . '</span></td>
							<td><span class="order_amount">' . Stringz::currency($bid['btc'], true) . '</span> ' . $CFG->currencies[$bid['c_currency']]['currency'] . '</td>
							<td><span class="currency_char">' . $CFG->currencies[$bid['currency']]['fa_symbol'] . '</span><span class="order_value">' . Stringz::currency($bid['btc'] * (($bid['fiat_price'] > 0) ? $bid['fiat_price'] : $bid['stop_price']), ($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')) . '</span></td>
							
						</tr>';
                        if ($double) {
                            echo '
						<tr id="openbid_' . $bid['id'] . '" class="openbid_tr double">
							<input type="hidden" class="is_crypto" value="' . $bid['is_crypto'] . '" />
							<td><div class="identify stop_order">S</div></td>
							<td><span class="currency_char">' . $CFG->currencies[$bid['currency']]['fa_symbol'] . '</span><span class="order_price">' . Stringz::currency($bid['stop_price'], ($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')) . '</span></td>
							<td><span class="order_amount">' . Stringz::currency($bid['btc'], true) . '</span> ' . $CFG->currencies[$bid['c_currency']]['currency'] . '</td>
							<td><span class="currency_char">' . $CFG->currencies[$bid['currency']]['fa_symbol'] . '</span><span class="order_value">' . Stringz::currency($bid['btc'] * $bid['stop_price'], ($CFG->currencies[$bid['currency']]['is_crypto'] == 'Y')) . '</span></td>
							<td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td>
						</tr>';
                        }
                    }
                }
                echo '<tr id="no_openbids" style="' . (is_array($open_bids) && count($open_bids) > 0 ? 'display:none;' : '') . '"><td colspan="5"><div class="" style=" text-align:  center;
                        "><img src="images/no-results.gif" style="width: 300px;height: auto;" ></div></td></tr>';
                ?>
            </table>
            </div>

        </div>
    </div>

</div>

</div>
<div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; width:599px; margin-bottom:0px;">
<div class="Flex__Flex-fVJVYW bHipRv">
    <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">


            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">
            <?= Lang::string('open-orders') ?> Sell
            </h4>
        </div>
        <div class="WidgetHeader__Actions-bDbtim jQqaGc">
            <div class="Flex__Flex-fVJVYW iDqRrV">

            
            </div>
        </div>
    </div>

    <!-- <td><a href="edit-order.php?order_id='.$ask['id'].'" title="'.Lang::string('orders-edit').'"><i class="fa fa-pencil"></i></a> <a href="open-orders.php?delete_id='.$ask['id'].'&uniq='.$_SESSION["openorders_uniq"].'" title="'.Lang::string('orders-delete').'"><i class="fa fa-times"></i></a></td> -->
    <div class="Flex__Flex-fVJVYW iJJJTg">
         <div class="table-otr">
          <table id="openasks_list">
            <tr>
            <th></th>
							<th><?= Lang::string('orders-price') ?></th>
	        				<th><?= Lang::string('orders-amount') ?></th>
	        				<th><?= Lang::string('orders-value') ?></th>
	        				<!-- <th></th> -->
            </tr>
           
            <?
            if ($open_asks) {
                foreach ($open_asks as $ask) {
                    $blink = ($ask['id'] == $id1) ? 'blink' : '';
                    $double = 0;
                    if ($ask['market_price'] == 'Y')
                        $type = '<div class="identify market_order">M</div>';
                    elseif ($ask['fiat_price'] > 0 && !($ask['stop_price'] > 0))
                        $type = '<div class="identify limit_order">L</div>';
                    elseif ($ask['stop_price'] > 0 && !($ask['fiat_price'] > 0))
                        $type = '<div class="identify stop_order">S</div>';
                    elseif ($ask['stop_price'] > 0 && $ask['fiat_price'] > 0) {
                        $type = '<div class="identify limit_order">L</div>';
                        $double = 1;
                    }

                    echo '
						<tr id="openask_' . $ask['id'] . '" class="openask_tr ' . $blink . '">
							<input type="hidden" class="usd_price" value="' . Stringz::currency(((empty($ask['usd_price'])) ? $ask['usd_price'] : $ask['btc_price']), ($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')) . '" />
							<input type="hidden" class="order_date" value="' . $ask['date'] . '" />
							<input type="hidden" class="is_crypto" value="' . $ask['is_crypto'] . '" />
							<td>' . $type . '</td>
							<td><span class="currency_char">' . $CFG->currencies[$ask['currency']]['fa_symbol'] . '</span><span class="order_price">' . Stringz::currency(($ask['fiat_price'] > 0) ? $ask['fiat_price'] : $ask['stop_price'], ($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')) . '</span></td>
							<td><span class="order_amount">' . Stringz::currency($ask['btc'], true) . '</span> ' . $CFG->currencies[$ask['c_currency']]['currency'] . '</td>
							<td><span class="currency_char">' . $CFG->currencies[$ask['currency']]['fa_symbol'] . '</span><span class="order_value">' . Stringz::currency($ask['btc'] * (($ask['fiat_price'] > 0) ? $ask['fiat_price'] : $ask['stop_price']), ($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')) . '</span></td>
							
						</tr>';

                    if ($double) {
                        echo '
						<tr id="openask_' . $ask['id'] . '" class="openask_tr double">
							<input type="hidden" class="is_crypto" value="' . $ask['is_crypto'] . '" />
							<td><div class="identify stop_order">S</div></td>
							<td><span class="currency_char">' . $CFG->currencies[$ask['currency']]['fa_symbol'] . '</span><span class="order_price">' . Stringz::currency($ask['stop_price'], ($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')) . '</span></td>
							<td><span class="order_amount">' . Stringz::currency($ask['btc'], true) . '</span> ' . $CFG->currencies[$ask['c_currency']]['currency'] . '</td>
							<td><span class="currency_char">' . $CFG->currencies[$ask['currency']]['fa_symbol'] . '</span><span class="order_value">' . Stringz::currency($ask['stop_price'] * $ask['btc'], ($CFG->currencies[$ask['currency']]['is_crypto'] == 'Y')) . '</span></td>
							<td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td>
						</tr>';
                    }
                }
            }
            echo '<tr id="no_openasks" style="' . (is_array($open_asks) && count($open_asks) > 0 ? 'display:none;' : '') . '"><td colspan="5"><div class="" style=" text-align:  center;
                        "><img src="images/no-results.gif" style="width: 300px;height: auto;" ></div></td></tr>';
            ?>
          </table>
          </div>
    </div>
</div>
</div>
</div>
</div>


<!-- Block 4 -->
<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">

<div class="Flex__Flex-fVJVYW gsOGkq">

<div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; width:100%; margin-bottom:0px;">
<div class="Flex__Flex-fVJVYW bHipRv">
    <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">


            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">
            <?= Lang::string('transactions') ?>
            </h4>
        </div>
        <div class="WidgetHeader__Actions-bDbtim jQqaGc">
            <div class="Flex__Flex-fVJVYW iDqRrV">

            
            </div>
        </div>
    </div>
    <div class="Flex__Flex-fVJVYW iJJJTg">
         <div class="table-otr">
         <input type="hidden" id="refresh_transactions" value="1" />
        		<input type="hidden" id="page" value="<?= $page1 ?>" />
          <table id="transactions_list">
            <tr>
            <th><?= Lang::string('transactions-type') ?></th>
        				<th><?= Lang::string('transactions-time') ?></th>
        				<th><?= Lang::string('orders-amount') ?></th>
        				<th><?= Lang::string('transactions-fiat') ?></th>
        				<th><?= Lang::string('orders-price') ?></th>
        				<th><?= Lang::string('transactions-fee') ?></th>
            </tr>
           
            <?
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
            echo '<tr id="no_transactions" style="' . (is_array($transactions) ? 'display:none;' : '') . '"><td colspan="6"><div class="" style=" text-align:  center;
                    "><img src="images/no-results.gif" style="width: 300px;height: auto;" ></div></td></tr>';
            ?>
          </table>
          <?= $pagination ?>
          </div>
    </div>
</div>
</div>
                    </div>
                    </div>

</div>
<?php include "includes/footer.php"; ?>
<div class="Backdrop__LayoutBackdrop-eRYGPr cdNVJh"></div>
</div>
</div>
</div>
<div>


</div>
</div>
</div>

</div>
</div>
<script>
$(document).ready(function(){

$(".Header__DropdownButton-dItiAm").click(function(){
$(".DropdownMenu__Wrapper-ieiZya.kwMMmE").toggleClass("show-menu");
});
});
</script>
<!-- ######### JS FILES ######### -->
<script type="text/javascript" src="js/socket.io.js"></script>
<script type="text/javascript" src="js/universal/jquery.js"></script>
<script type="text/javascript" src="js/universal/jquery-ui-1.10.3.custom.min.js"></script>

<script type="text/javascript" src="js/ops.js?v=20160210"></script>


<? if ($CFG->self == 'userexchange.php') { ?>
<!-- flot -->
<script type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.time.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.crosshairs.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.candle.js"></script>
<?
} ?>


<script type="text/javascript">
<?php 
// $bid_range = $max_bid - $min_bid;
// $ask_range = $max_ask - $min_ask;
// $c_bids = count($bids);
// $c_asks = count($asks);
// $lower_range = ($bid_range < $ask_range) ? $bid_range : $ask_range;
// $vars = array('bids' => array(), 'asks' => array());

// if ($bids) {
//     $cum_btc = 0;
//     foreach ($bids as $bid) {
//         if ($max_bid && $c_asks > 1 && (($max_bid - $bid['btc_price']) > $lower_range))
//             continue;

//         $cum_btc += $bid['btc'];
//         $vars['bids'][] = array($bid['btc_price'], $cum_btc);
//     }

//     if ($max_bid && $c_asks > 1)
//         $vars['bids'][] = array(($max_bid - $lower_range), $cum_btc);

// }
// if ($asks) {
//     $cum_btc = 0;
//     foreach ($asks as $ask) {
//         if ($min_ask && $c_bids > 1 && (($ask['btc_price'] - $min_ask) > $lower_range))
//             continue;

//         $cum_btc += $ask['btc'];
//         $vars['asks'][] = array($ask['btc_price'], $cum_btc);
//     }

//     if ($min_ask && $c_bids > 1)
//         $vars['asks'][] = array(($min_ask + $lower_range), $cum_btc);
// }
// echo 'var static_data = ' . json_encode($vars) . ';';
?>
    </script>
</body>

</html>