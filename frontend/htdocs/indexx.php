<!DOCTYPE html>
<html lang="en">
    <?php include '../lib/common.php';
    include 'vendor/autoload.php';
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;
Rollbar::init(
    array(
        'access_token' => 'b7580f9653c34fb48211762e92f0ab50',
        'environment' => 'development'
    )
);
        $conn = new mysqli($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
        $cur_sql = "SELECT * FROM currencies";
        $currency_query = mysqli_query($conn,$cur_sql); 
        $currency_id = $_REQUEST['currency'];
        if (!$currency_id) {
           $currency_id = 28;
           $_REQUEST['currency'] = 28;
        }
        include "includes/sonance_header.php";
    
        $currencies = Settings::sessionCurrency();
        $currency1 = $currencies['currency'];
        $c_currency1 = $currencies['c_currency'];
        $usd_field = 'usd_ask';
        API::add('Transactions','get',array(false,false,1,$c_currency1,$currency1));
        API::add('Stats','getCurrent',array($currencies['c_currency'],$currencies['currency']));
        //27-USD, 28-BTC, 42-LTC, 43-ZEC, 44-BCH, 45-ETH
        API::add('Transactions','get24hData',array(28,27));
        API::add('Transactions','get24hData',array(42,27));
        API::add('Transactions','get24hData',array(42,28));
        API::add('Transactions','get24hData',array(43,27));
        API::add('Transactions','get24hData',array(43,28));
        API::add('Transactions','get24hData',array(45,27));
        API::add('Transactions','get24hData',array(45,28));
        API::add('Transactions','get24hData',array(42,45));
        API::add('Transactions','get24hData',array(43,45));
        API::add('Transactions','get24hData',array(44,27));
        API::add('Transactions','get24hData',array(44,28));
        $query = API::send();
        // echo "<pre>"; print_r($query['Stats']['getCurrent']['results']); exit;
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
        $currency_info = $CFG->currencies[$currencies['currency']];
        $c_currency_info = $CFG->currencies[$currencies['c_currency']];
        $currency_majors = array('USD','EUR','CNY','RUB','CHF','JPY','GBP','CAD','AUD');
        $c_majors = count($currency_majors);
        $curr_list = $CFG->currencies;
        $curr_list1 = array();
        foreach ($currency_majors as $currency) {
            if (empty($curr_list[$currency]))
                continue;
        
            $curr_list1[$currency] = $curr_list[$currency];
            unset($curr_list[$currency]);
        }
        $curr_list = array_merge($curr_list1,$curr_list);
        // echo "<pre>"; print_r($curr_list); exit;
        $stats = $query['Stats']['getCurrent']['results'][0];
        if ($stats['daily_change'] > 0)
            $arrow = '<i id="up_or_down" class="fa fa-caret-up price-green"></i> ';
        elseif ($stats['daily_change'] < 0)
            $arrow = '<i id="up_or_down" class="fa fa-caret-down price-red"></i> ';
        else
            $arrow = '<i id="up_or_down" class="fa fa-minus"></i> ';
        ?>
        <style>
            .table td, .table th,.table tr{
                cursor: auto;
            }
        </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
        <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script> 
        <header>
            <div class="banner row no-margin">
                <div class="container content">
                    <h1><?= $CFG->exchange_name; ?> - Exchange The World</h1>
                    <div class="links">
                        <p><a href="register">Create Account</a><span class="line"></span>Already Registered? <a href="login">Login</a></p>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="banner-images">
                                <a href="https://bitexchange.systems/cryptocurrency-wallet-script/" target="_blank" rel="nofollow"><img src="sonance/img/banner/1.png"></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="banner-images">
                                <a href="https://bitexchange.systems/bitcoin-exchange-android-app-theme/" target="_blank" rel="nofollow"><img src="sonance/img/banner/2.png"></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="banner-images">
                                <a href="https://bitexchange.live/api-docs.php" target="_blank" rel="nofollow"><img src="sonance/img/banner/3.png"></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="banner-images">
                                <a href="http://bitcoinscript.bitexchange.systems/2018/01/cryptocurrency-exchange-software-security.html" target="_blank" rel="nofollow"><img src="sonance/img/banner/4.png"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sticky row no-margin">
                <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                    <div class="container">
                        <div class="row">
                            <div style="padding:0 3em;"><span><a href="#"><span><b>BTC</b></span>&nbsp; <i><?=$transactions_24hrs_btc_usd['lastPrice'] ? $transactions_24hrs_btc_usd['lastPrice'] : '0.00'?></i></a></span></div>
                            <div style="padding:0 3em;"><span><a href="#"><span><b>LTC</b></span>&nbsp; <i><?=$transactions_24hrs_ltc_usd['lastPrice'] ? $transactions_24hrs_ltc_usd['lastPrice'] : '0.00'?></i></a></span></div>
                            <div style="padding:0 3em;"><span><a href="#"><span><b>ZEC</b></span>&nbsp; <i><?=$transactions_24hrs_zec_usd['lastPrice'] ? $transactions_24hrs_zec_usd['lastPrice'] : '0.00'?></i></a></span></div>
                            <div style="padding:0 3em;"><span><a href="#"><span><b>BCH</b></span>&nbsp; <i><?=$transactions_24hrs_bch_usd['lastPrice'] ? $transactions_24hrs_bch_usd['lastPrice'] : '0.00'?></i></a></span></div>
                            <div style="padding:0 3em;"><span><a href="#"><span><b>ETH</b></span>&nbsp; <i><?=$transactions_24hrs_eth_usd['lastPrice'] ? $transactions_24hrs_eth_usd['lastPrice'] : '0.00'?></i></a></span></div>
                        
                            <?
                                /* if ($curr_list) {
                                    foreach ($curr_list as $key => $currency) {
                                        if (is_numeric($key) || $currency['id'] == $c_currency_info['id'])
                                            continue;
                                
                                        $last_price = Stringz::currency($stats['last_price'] * ((empty($currency_info) || $currency_info['currency'] == 'USD') ? 1/$currency[$usd_field] : $currency_info[$usd_field] / $currency[$usd_field]),2,4);
                                        echo '<div style="padding:0 3em;"><span><a href="#"><span><b>'.$currency['currency'].'</b></span>&nbsp; <i>'.$last_price.'</i></a></span></div>';
                                    }
                                } */
                                ?>
                            <!--  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <p><a href="#"><span><?= $CFG->exchange_name; ?> Lists Red Pulse (RPX)</span><i>(01-12)</i></a></p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <p><a href="#"><span><?= $CFG->exchange_name; ?> Lists Red Pulse (RPX)</span><i>(01-12)</i></a></p>
                                </div> -->
                        </div>
                    </div>
                </marquee>
            </div>
        </header>
        <div class="page-container">
            <div class="container">
                <div class="row statistics-widget">
                    <div class="col">
                        <a href="userbuy?trade=BTC-USD" class="statistics-widget-link">
                            <div class="statistics-widget-grid">
                                <div class="content">
                                    <h5>BTC/USD</h5>
                                    <h6>
                                        <strong class="green-color"><? echo $transactions_24hrs_btc_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_btc_usd['lastPrice'],2,4) : '0.00' ; ?></strong><!--  $0.05 -->
                                    </h6>
                                    <p>Volume : <? echo $transactions_24hrs_btc_usd['transactions_24hrs'] ? $transactions_24hrs_btc_usd['transactions_24hrs'] : '0.00'; ?> BTC</p>
                                </div>
                                <span class="status green-color"><? echo $transactions_24hrs_btc_usd['change_24hrs'] ? $transactions_24hrs_btc_usd['change_24hrs'] : '0.00'; ?></span>
                                <div class="chart-bar">
                                    <svg version="1.1" class="highcharts-root" xmlns="http://www.w3.org/2000/svg">
                                        <g transform="translate(0.5,0.5)">
                                            <path id="BNBBTC" stroke="rgba(244,220,174,1)" fill="none" stroke-width="1" d="M0 0 L10 8 L20 2 L30 1 L40 4 L50 9 L60 23 L70 24 L80 23 L90 26 L100 35 L110 40 L120 40 L130 37 L140 38 L150 30 L160 26 L170 23 L180 28 L190 24 L200 17 L210 21 L220 24 L230 24"></path>
                                            <path id="BNBBTCfill" fill="rgba(254,251,245,1)" stroke="none" d="M0 40 L0 0 L10 8 L20 2 L30 1 L40 4 L50 9 L60 23 L70 24 L80 23 L90 26 L100 35 L110 40 L120 40 L130 37 L140 38 L150 30 L160 26 L170 23 L180 28 L190 24 L200 17 L210 21 L220 24 L230 24 L230 40"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="userbuy?trade=LTC-USD" class="statistics-widget-link">
                            <div class="statistics-widget-grid">
                                <div class="content">
                                    <h5>LTC/USD</h5>
                                    <h6>
                                        <strong class="green-color"><? echo $transactions_24hrs_ltc_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_ltc_usd['lastPrice'],2,4) : '0.00' ; ?></strong><!--  $0.05 -->
                                    </h6>
                                    <p>Volume : <? echo $transactions_24hrs_ltc_usd['transactions_24hrs'] ? $transactions_24hrs_ltc_usd['transactions_24hrs'] : '0.00'; ?> BTC</p>
                                </div>
                                <span class="status green-color"><? echo $transactions_24hrs_ltc_usd['change_24hrs'] ? $transactions_24hrs_ltc_usd['change_24hrs'] : '0.00'; ?></span>
                                <div class="chart-bar">
                                    <svg version="1.1" class="highcharts-root" xmlns="http://www.w3.org/2000/svg">
                                        <g transform="translate(0.5,0.5)">
                                            <path id="TRXBTC" stroke="rgba(244,220,174,1)" fill="none" stroke-width="1" d="M0 30 L10 27 L20 35 L30 38 L40 22 L50 38 L60 38 L70 35 L80 30 L90 30 L100 35 L110 40 L120 35 L130 38 L140 32 L150 30 L160 35 L170 30 L180 25 L190 13 L200 0 L210 13 L220 10 L230 13"></path>
                                            <path id="TRXBTCfill" fill="rgba(254,251,245,1)" stroke="none" d="M0 40 L0 30 L10 27 L20 35 L30 38 L40 22 L50 38 L60 38 L70 35 L80 30 L90 30 L100 35 L110 40 L120 35 L130 38 L140 32 L150 30 L160 35 L170 30 L180 25 L190 13 L200 0 L210 13 L220 10 L230 13 L230 40"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="userbuy?trade=BCH-USD" class="statistics-widget-link">
                            <div class="statistics-widget-grid">
                                <div class="content">
                                    <h5>BCH/USD</h5>
                                    <h6>
                                        <strong class="green-color"><? echo $transactions_24hrs_bch_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_bch_usd['lastPrice'],2,4) : '0.00' ; ?></strong><!--  $0.05 -->
                                    </h6>
                                    <p>Volume : <? echo $transactions_24hrs_bch_usd['transactions_24hrs'] ? $transactions_24hrs_bch_usd['transactions_24hrs'] : '0.00'; ?> BTC</p>
                                </div>
                                <span class="status green-color"><? echo $transactions_24hrs_bch_usd['transactions_24hrs'] ? $transactions_24hrs_bch_usd['change_24hrs'] : '0.00'; ?></span>
                                <div class="chart-bar">
                                    <svg version="1.1" class="highcharts-root" xmlns="http://www.w3.org/2000/svg">
                                        <g transform="translate(0.5,0.5)">
                                            <path id="RPXBTC" stroke="rgba(244,220,174,1)" fill="none" stroke-width="1" d="M0 13 L10 0 L20 15 L30 19 L40 20 L50 24 L60 24 L70 25 L80 22 L90 26 L100 22 L110 22 L120 32 L130 33 L140 36 L150 30 L160 37 L170 40 L180 35 L190 33 L200 34 L210 32 L220 34 L230 29"></path>
                                            <path id="RPXBTCfill" fill="rgba(254,251,245,1)" stroke="none" d="M0 40 L0 13 L10 0 L20 15 L30 19 L40 20 L50 24 L60 24 L70 25 L80 22 L90 26 L100 22 L110 22 L120 32 L130 33 L140 36 L150 30 L160 37 L170 40 L180 35 L190 33 L200 34 L210 32 L220 34 L230 29 L230 40"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="userbuy?trade=ZEC-USD" class="statistics-widget-link">
                            <div class="statistics-widget-grid">
                                <div class="content">
                                    <h5>ZEC/USD</h5>
                                    <h6>
                                        <strong class="green-color"><? echo $transactions_24hrs_zec_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_zec_usd['lastPrice'],2,4) : '0.00' ; ?></strong><!--  $0.05 -->
                                    </h6>
                                    <p>Volume : <? echo $transactions_24hrs_zec_usd['transactions_24hrs'] ? $transactions_24hrs_zec_usd['transactions_24hrs'] : '0.00'; ?> BTC</p>
                                </div>
                                <span class="status green-color"><? echo $transactions_24hrs_zec_usd['transactions_24hrs'] ? $transactions_24hrs_zec_usd['change_24hrs'] : '0.00'; ?></span>
                                <div class="chart-bar">
                                    <svg version="1.1" class="highcharts-root" xmlns="http://www.w3.org/2000/svg">
                                        <g transform="translate(0.5,0.5)">
                                            <path id="GTOBTC" stroke="rgba(244,220,174,1)" fill="none" stroke-width="1" d="M0 7 L10 6 L20 8 L30 9 L40 3 L50 0 L60 6 L70 3 L80 8 L90 3 L100 3 L110 14 L120 12 L130 32 L140 38 L150 36 L160 35 L170 40 L180 37 L190 27 L200 32 L210 37 L220 39 L230 39"></path>
                                            <path id="GTOBTCfill" fill="rgba(254,251,245,1)" stroke="none" d="M0 40 L0 7 L10 6 L20 8 L30 9 L40 3 L50 0 L60 6 L70 3 L80 8 L90 3 L100 3 L110 14 L120 12 L130 32 L140 38 L150 36 L160 35 L170 40 L180 37 L190 27 L200 32 L210 37 L220 39 L230 39 L230 40"></path>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="graph-outer">
                    <!-- TradingView Widget BEGIN -->

                    <form name="chat_filter" method="get" action="">
                            <input type="hidden" name="trade" value="<?php echo $_REQUEST['trade']; ?>">
                        <div class="row">
                            <div class="col-md-2 col-sm-6 col-xs-6">
                                <select class="form-group form-control" name="currency">
                                 <?php while($currency = mysqli_fetch_assoc($currency_query)) { ?>
                                        <option 
                                        <?php if($_REQUEST['currency'] == $currency['id']) { ?> 
                                        selected
                                        <?php } ?>
                                        value="<?php echo $currency['id']; ?>">
                                            <?php echo $currency['currency']; ?>
                                        </option>
                                    <?php } ?>
                                    
                                </select>
                            </div>
                            <div class="col-md-8 col-sm-6 col-xs-6">
                                <button style="width: 10%;padding-left: 13px;" class="btn btn-primary btn-change">GO</button>
                            </div>
                        </div>
                        </form>
                     <div id="chart_div"></div>

                    <!-- TradingView Widget END -->
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="home-data-table">
                            <nav class="nav-justified">
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="nav-btc-tab" data-toggle="tab" href="#nav-btc" role="tab" aria-controls="nav-btc" aria-selected="true">USD Markets</a>
                                    <!-- <a class="nav-item nav-link" id="nav-ltc-tab" data-toggle="tab" href="#nav-ltc" role="tab" aria-controls="nav-btc" aria-selected="false">LTC Markets</a> -->
                                    <a class="nav-item nav-link" id="nav-eth-tab" data-toggle="tab" href="#nav-eth" role="tab" aria-controls="nav-eth" aria-selected="false">ETH Markets</a>
                                    <!-- <a class="nav-item nav-link" id="nav-usdt-tab" data-toggle="tab" href="#nav-usdt" role="tab" aria-controls="nav-usdt" aria-selected="false">BCH Markets</a> -->
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-btc" role="tabpanel" aria-labelledby="nav-btc-tab">
                                    <table id="hm-data-table" class="table row-border hm-data-table table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Pair</th>
                                                <th>Last Price</th>
                                                <th>24h Change</th>
                                                <th>24h Volume</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr >
                                                <td>
                                                    <div class="star-inner">
                                                        <input id="star1" type="checkbox" name="time" />
                                                        <!-- <label for="star1"><i class="fas fa-star"></i></label> -->
                                                        BTC/USD
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_btc_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_btc_usd['lastPrice'],2,4) : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_btc_usd['change_24hrs'] ? $transactions_24hrs_btc_usd['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_btc_usd['transactions_24hrs'] ? $transactions_24hrs_btc_usd['transactions_24hrs'] : '0.00' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="star-inner">
                                                        <input id="star1" type="checkbox" name="time" />
                                                        <!-- <label for="star1"><i class="fas fa-star"></i></label> -->
                                                        LTC/USD
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_ltc_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_ltc_usd['lastPrice'],2,4) : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_ltc_usd['change_24hrs'] ? $transactions_24hrs_ltc_usd['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_ltc_usd['transactions_24hrs'] ? $transactions_24hrs_ltc_usd['transactions_24hrs'] : '0.00' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="star-inner">
                                                        <input id="star1" type="checkbox" name="time" />
                                                        <!-- <label for="star1"><i class="fas fa-star"></i></label> -->
                                                        BCH/USD
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_bch_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_bch_usd['lastPrice'],2,4) : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_bch_usd['change_24hrs'] ? $transactions_24hrs_bch_usd['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_bch_usd['transactions_24hrs'] ? $transactions_24hrs_bch_usd['transactions_24hrs'] : '0.00' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="star-inner">
                                                        <input id="star1" type="checkbox" name="time" />
                                                        <!-- <label for="star1"><i class="fas fa-star"></i></label> -->
                                                        ZEC/USD
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_zec_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_zec_usd['lastPrice'],2,4) : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_zec_usd['change_24hrs'] ? $transactions_24hrs_zec_usd['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_zec_usd['transactions_24hrs'] ? $transactions_24hrs_zec_usd['transactions_24hrs'] : '0.00' ; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="nav-eth" role="tabpanel" aria-labelledby="nav-ltc-tab">
                                    <table id="hm-data-table" class="table row-border hm-data-table table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Pair</th>
                                                <th>Last Price</th>
                                                <th>24h Change</th>
                                                <th>24h Volume</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="star-inner">
                                                        <input id="star1" type="checkbox" name="time" />
                                                        <label for="star1"><i class="fas fa-star"></i></label>
                                                        LTC/ETH
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_ltc_eth['lastPrice'] ? $transactions_24hrs_ltc_eth['lastPrice'] : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_ltc_eth['change_24hrs'] ? $transactions_24hrs_ltc_eth['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_ltc_eth['transactions_24hrs'] ? $transactions_24hrs_ltc_eth['transactions_24hrs'] : '0.00' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="star-inner">
                                                        <input id="star1" type="checkbox" name="time" />
                                                        <label for="star1"><i class="fas fa-star"></i></label>
                                                        ZEC/ETH
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_zec_eth['lastPrice'] ? $transactions_24hrs_zec_eth['lastPrice'] : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_zec_eth['change_24hrs'] ? $transactions_24hrs_zec_eth['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_zec_eth['transactions_24hrs'] ? $transactions_24hrs_zec_eth['transactions_24hrs'] : '0.00' ; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- <div class="tab-pane fade" id="nav-usdt" role="tabpanel" aria-labelledby="nav-usdt-tab">
                                    <table id="hm-data-table" class="table row-border hm-data-table table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Pair</th>
                                                <th>Last Price</th>
                                                <th>24h Change</th>
                                                <th>24h High</th>
                                                <th>24h Low</th>
                                                <th>24h Volume</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="clickable-row" data-href="trade.html">
                                                <td>
                                                    <div class="star-inner">
                                                        <input id="star1" type="checkbox" name="time" />
                                                        <label for="star1"><i class="fas fa-star"></i></label>
                                                        ETH/BTC
                                                    </div>
                                                </td>
                                                <td><span class="green-color">0.086583</span> <span class="gray-color">/ $944.16</span></td>
                                                <td><span class="red-color">-1.87</span></td>
                                                <td>0.088899</td>
                                                <td>0.086131</td>
                                                <td>9,349.24283190</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        $sql = "SELECT A.date,A.btc_price,B.currency FROM `transactions` A,`currencies` B WHERE A.c_currency = B.id AND c_currency = $currency_id";
        $my_query = mysqli_query($conn,$sql);
        ?>
        <?php include "includes/sonance_footer.php"; ?>
        <script type='text/javascript'>


        google.charts.load('current', {'packages':['annotatedtimeline']});
      google.charts.setOnLoadCallback(drawChart);

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
        <?php } ?>
        ]);

        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
        chart.draw(data, {displayAnnotations: false});
      }
    

    //load_chart();
    </script>

        <div class="fb_chat" style=""> 
            <a href="https://www.messenger.com/t/194868894428597" class="fb-msg-btn-chat" target="_blank" rel="nofollow"> Contact us on Facebook</a> 
        </div>
</html>