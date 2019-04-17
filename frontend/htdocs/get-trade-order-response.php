    <?php
        include '../lib/common.php';
        // require_once ("cfg.php");
        $conn = new mysqli("localhost","root","xchange123","bitexchange_cash");
    //     error_reporting(E_ERROR | E_WARNING | E_PARSE);
    // ini_set('display_errors', 1);
        if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y') {
            Link::redirect('userprofile.php');
        } elseif (User::$awaiting_token) {
            Link::redirect('verify-token.php');
        } elseif (!User::isLoggedIn()) {
            Link::redirect('login.php');
        }
           

        $currency_id = $_POST['currency_id'];        
        $c_currency_id = $_POST['c_currency_id'];
        $t = $_POST['trade'];

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
            //$GLOBALS['REFERRAL_BASE_URL'] = $ref_response->base_url;
        }else{
           $GLOBALS['REFERRAL'] = false; 
        }

        // end of checking referral status

        $market = $_POST['trade'];
        $currencies = Settings::sessionCurrency();
             
        $buy = (!empty($_POST['buy']));
        $sell = (!empty($_POST['sell']));
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

        $confirmed = (!empty($_POST['confirmed'])) ? $_POST['confirmed'] : false;
        $cancel = (!empty($_POST['cancel'])) ? $_POST['cancel'] : false;
        $bypass = (!empty($_REQUEST['bypass'])) ? $_REQUEST['bypass'] : false;
        $buy_market_price1 = 0;
        $sell_market_price1 = 0;
        $buy_limit = 1;
        $sell_limit = 1;
        if ($buy || $sell) {
            if (empty($_SESSION["buysell_uniq"]) || empty($_POST['uniq']) || !in_array($_POST['uniq'],$_SESSION["buysell_uniq"]))
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
           
        
           //my transactions 
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
        // echo "<pre>"; print_r($user_available); exit;
        // echo "<pre>"; print_r($user_fee_both); exit;
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

        //
        // referral bonus starts
        if ($_REQUEST['is_referral']) {
            //echo 'yes is_referral true';//bonus_amount
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
                

                //
                $his_url = $REFERRAL_BASE_URL."get-usage-history.php";
                $ch = curl_init();
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST, count($fields));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                //execute post
                $result = curl_exec($ch);
                $response = json_decode($result);
                //var_dump($response);
                //close connection
                curl_close($ch);
            }
            
        // end of referral
                    
?>        
<?php include "includes/sonance_header.php";  ?>

<!-- chart div -->

<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
<style>
body{
overflow-y: scroll !important;
}
@media only screen and (max-width: 992px){
body{
overflow-y: auto;
}
}
.amChartsButton.amcharts-period-input{
background: transparent;
border: 1px solid #000;
margin: 2px 3px 0;
border-radius: 2px;
}
#chartdiv {
width: 100%;
height: 250px;
}
.left-side-widget,
.trade-history-view{
height: 250px;
overflow: auto;
margin-bottom: 0;
}
.input-caption {
position: relative;
float: right;
top: -28px;
right: 6px;
height: 28px;
padding-top: 5px;
}
.custom-select
{
font-size: 11px;
padding: 5px 10px;
border-radius: 2px;
height: 28px !important;
}
label.cont
{
width:100%;
}
.pull-right 
{
float:right;
}

.current-otr p 
{
margin: 5px 0;
}
.left-side-widget .nav-link:hover,
.left-side-widget .nav-link:focus,
.left-side-widget .nav-link:visited,
.left-side-widget .nav-link:active{
color:#000 !important;
}
.btn.btn-primary.btn-change {
width: auto;
padding-left: 30px;
padding-right: 30px;
padding-top: 10px;
padding-bottom: 10px;
}
#chart_div {
margin-left: 0px;
padding-left: 0px;
margin-bottom: 20px;
}
footer {
margin-top: 0em !IMPORTANT;
}
.center-widget{
margin-top: 5px;
}
.left-side-widget .tab-content{
overflow : initial; 
}

.amcharts-chart-div a {
display: none !important;
}
.amChartsPeriodSelector.amcharts-period-selector-div div:nth-child(2) {
display: none !important;
}
</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/amstock.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
<script src="https://www.amcharts.com/lib/3/themes/none.js"></script>
<!-- Chart code -->

<p id=""></p>
<input type="hidden" value="<?php echo $newDate;?>" id="hello2">
<script
src="https://code.jquery.com/jquery-3.3.1.min.js"
integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
crossorigin="anonymous"></script>
<script>
var chartData = [];

function generateChartData() {
var firstDate = new Date();
firstDate.setHours( 0, 0, 0, 0 );
firstDate.setDate( firstDate.getDate() - 2000 );


for ( var i = 0; i < 2000; i++ ) {
var newDate = new Date( firstDate );

newDate.setDate( newDate.getDate() + i );

var open = Math.round( Math.random() * ( 30 ) + 100 );
var close = open + Math.round( Math.random() * ( 15 ) - Math.random() * 10 );

var low;
if ( open < close ) {
low = open - Math.round( Math.random() * 5 );
} else {
low = close - Math.round( Math.random() * 5 );
}

var high;
if ( open < close ) {
high = close + Math.round( Math.random() * 5 );
} else {
high = open + Math.round( Math.random() * 5 );
}

var volume = Math.round( Math.random() * ( 1000 + i ) ) + 100 + i;


chartData[ i ] = ( {
"date": newDate,
"open": open,
"close": close,
"high": high,
"low": low,
"volume": volume
} );
}
}

var n_chart_data = [];

var api_url = "chart_json.php?currency="+<?php echo $currency_id; ?>;

$.ajax({
type: "GET",
url: api_url,
dataType:'json',
success: function(data){
//alert(data.Data.length);
data.Data.forEach(function(element) {
console.log(element);
var newDate = new Date(element.date*1000);
n_chart_data.push( {
"date": newDate,
"value": element.btc_price,
"volume": element.btc_before
} );
console.log("single Data :"+n_chart_data);
});

if (data.Data.length == 0) {
var newDate = new Date();
n_chart_data.push( {
"date": newDate,
"value": 0,
"volume": 0
} );
}

var chart = AmCharts.makeChart( "chartdiv", {
"type": "stock",
"theme": "light",
"categoryAxesSettings": {
"minPeriod": "mm"
},

"dataSets": [ {
"color": "#b0de09",
"fieldMappings": [ {
"fromField": "value",
"toField": "value"
}, {
"fromField": "volume",
"toField": "volume"
} ],

"dataProvider": n_chart_data,
"categoryField": "date"
} ],

"panels": [ {
"showCategoryAxis": false,
"title": "Value",
"percentHeight": 70,

"stockGraphs": [ {
"id": "g1",
"valueField": "value",
"type": "smoothedLine",
"lineThickness": 2,
"bullet": "round"
} ],


"stockLegend": {
"valueTextRegular": " ",
"markerType": "none"
}
}, {
"title": "Volume",
"percentHeight": 30,
"stockGraphs": [ {
"valueField": "volume",
"type": "column",
"cornerRadiusTop": 2,
"fillAlphas": 1
} ],

"stockLegend": {
"valueTextRegular": " ",
"markerType": "none"
}
} ],

"chartScrollbarSettings": {
"graph": "g1",
"usePeriod": "10mm",
"position": "top"
},

"chartCursorSettings": {
"valueBalloonsEnabled": true
},

"periodSelector": {
"position": "top",
"dateFormat": "YYYY-MM-DD JJ:NN",
"inputFieldWidth": 150,
"periods": [ {
"period": "hh",
"count": 1,
"label": "1 hour"
}, {
"period": "hh",
"count": 2,
"label": "2 hours"
}, {
"period": "hh",
"count": 5,
"selected": true,
"label": "5 hour"
}, {
"period": "hh",
"count": 12,
"label": "12 hours"
}, {
"period": "MAX",
"label": "MAX"
} ]
},

"panelsSettings": {
"usePrefixes": true
},

"export": {
"enabled": true,
"position": "bottom-right"
}
} );
}
});

generateChartData();



function addPanel() {
var chart = AmCharts.charts[ 0 ];
if ( chart.panels.length == 1 ) {
var newPanel = new AmCharts.StockPanel();
newPanel.allowTurningOff = true;
newPanel.title = "Volume";
newPanel.showCategoryAxis = false;

var graph = new AmCharts.StockGraph();
graph.valueField = "volume";
graph.fillAlphas = 0.15;
newPanel.addStockGraph( graph );

var legend = new AmCharts.StockLegend();
legend.markerType = "none";
legend.markerSize = 0;
newPanel.stockLegend = legend;

chart.addPanelAt( newPanel, 1 );
chart.validateNow();
}
}

function removePanel() {
var chart = AmCharts.charts[ 0 ];
if ( chart.panels.length > 1 ) {
chart.removePanel( chart.panels[ 1 ] );
chart.validateNow();
}
}
</script>


<div class="left-side-widget">
<div id="chartdiv" style="background-color: #fff;"></div> 
</div>

<!-- End of chart div -->

<?php
echo '~';
?>

<!-- Buy sell div -->

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

<!-- End of Buy sell div -->

<?php
echo '~';
?>

<!-- Trade history div -->
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
</div>
<!-- End of Trade history div -->

<?php
echo '~';
?>

<!-- order history div -->


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
<!-- <li class="nav-item">
<a class="nav-link" id="funds-tab" data-toggle="tab" href="#funds" role="tab" aria-controls="funds" aria-selected="false">Funds</a>
</li> -->
</ul>
<div class="tab-content" id="myTabContent" style="height: 283px;
overflow: auto;">
<?php


$c_currency1111 = $_GET['c_currency'] ? : 28;
$currency1111 = $_GET['currency'] ? : 27;
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


$c_currency1 = $_GET['c_currency'] ? : 28;
$currency1 = $_GET['currency'] ? : 27;

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

//var_dump($transactions); exit;
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
<div class="tab-pane fade show active" id="order-history" role="tabpanel" aria-labelledby="order-history-tab">
<?php 
$currencies1 = Settings::sessionCurrency(); 


$c_currency11 = $_GET['c_currency'] ? : 28;
$currency11 = $_GET['currency'] ? : 27;

/* $currency1 = $currencies['currency'];
$c_currency1 = $currencies['c_currency'];
if(!$currency1 || empty($currency1)){
$currency1 = 13 ;
}
if(!$currency1 || empty($currency1)){
$currency1 = 28 ;
} */
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
$c_currency1 = $_GET['c_currency'] ? : 28;
$currency1 = $_GET['currency'] ? : 27;

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
// $page_title = Lang::string('order-book');


$c_currency111 = $_GET['c_currency'] ? : 28;
$currency111 = $_GET['currency'] ? : 27;


//commented out because of not required to take currency and c_currency from session
/* $currency1 = $currencies['currency'];
$c_currency1 = $currencies['c_currency']; */
/*  if(!$currency1 || empty($currency1)){
$currency1 = 27 ;
}
if(!$c_currency1 || empty($c_currency1)){
$c_currency1 = 28 ;
} */
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


//transaction

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
<div class="tab-pane fade" id="trade-history" role="tabpanel" aria-labelledby="trade-history-tab">
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
$c_currencyy1 = $_GET['c_currency'];
$currencyy1 = $_GET['currency'];
$currency_trade = $_GET['trade'];
$delete_id1 = (!empty($_REQUEST['delete_id'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['delete_id']) : false;
if ($delete_id1 > 0 && $_SESSION["openorders_uniq"] == $_REQUEST['uniq']) {
API::add('Orders','getRecord',array($delete_id1));
$query = API::send();
$del_order = $query['advanced-trade']['getRecord']['results'][0];

if (!$del_order) {
Link::redirect('advanced-trade.php?message=order-doesnt-exist&trade='.$currency_trade.'&c_currency='.$c_currencyy1.'&currency='.$currencyy1.'');
}
elseif ($del_order['site_user'] != $del_order['user_id'] || !($del_order['id'] > 0)) {
Link::redirect('advanced-trade.php?message=not-your-order&trade='.$currency_trade.'&c_currency='.$c_currencyy1.'&currency='.$currencyy1.'');
}
else {
API::add('Orders','delete',array($delete_id1));
$query = API::send();

Link::redirect('advanced-trade.php?message=order-cancelled&trade='.$currency_trade.'&c_currency='.$c_currencyy1.'&currency='.$currencyy1.'');
}
}

$delete_all = (!empty($_REQUEST['delete_all']));
if ($delete_all && $_SESSION["openorders_uniq"] == $_REQUEST['uniq']) {
API::add('Orders','deleteAll');
$query = API::send();
$del_order = $query['Orders']['deleteAll']['results'][0];

if (!$del_order)
Link::redirect('openorders.php?message=deleteall-error');
else
Link::redirect('openorders.php?message=deleteall-success');
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

$c_currency1 = $_GET['c_currency'] ? : 28;
$currency1 = $_GET['currency'] ? : 27;
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
<td>

<a href="advanced-trade.php?delete_id='.$bid['id'].'&uniq='.$_SESSION["openorders_uniq"].'&trade='.$currency_trade.'&c_currency='.$c_currencyy1.'&currency='.$currencyy1.'"  title="'.Lang::string('orders-delete').'"><i class="fa fa-times"></i></a></td>
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
<td> 
<a href="advanced-trade.php?delete_id='.$ask['id'].'&uniq='.$_SESSION["openorders_uniq"].'&trade='.$currency_trade.'&c_currency='.$c_currencyy1.'&currency='.$currencyy1.'" title="'.Lang::string('orders-delete').'"><i class="fa fa-times"></i></a></td>
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
<div class="tab-pane fade" id="funds" role="tabpanel" aria-labelledby="funds-tab">
<div class="row">
<table class="table">
<thead>
<tr>
<th>Date</th>
<th>Pair</th>
<th>Type</th>
<th>Side</th>
<th>Avg</th>
<th>Price</th>
<th>Filled</th>
<th>Amount</th>
<th>Total</th>
<th>Trigger Conditions</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<tr>
<td>10-5-18</td>
<td>0.0424678</td>
<td>Type1</td>
<td>Side</td>
<td>1234</td>
<td>54637</td>
<td>Filled</td>
<td>1,24566,445</td>
<td>3,24566,445</td>
<td>Trigger</td>
<td>Recived</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</div>
                    
                    <!-- End of order history div -->