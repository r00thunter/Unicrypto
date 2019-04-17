<!DOCTYPE html>
<html lang="en">

    <?php
   // error_reporting(E_ALL);
   //      ini_set('display_errors', 1);

     include '../lib/common.php';
    require_once ("cfg.php");
        // $conn = new mysqli("localhost","root","ghost227#","bitexchange_cash");

    if(!User::isLoggedIn()) { $is_user_login=0; }else{
        $is_user_login=1;
    }

        $explode_value = explode("-", $_REQUEST['currency']);
     echo $explode_value[0];  echo "<br>";  echo $explode_value[1];   echo "<br>";  echo $explode_value[2];  echo "<br>";  echo $explode_value[3];  
        $currency_id1 = $explode_value[3];
        $c_currency_id = $explode_value[2];  
        if ($_REQUEST['currency'] == 28 || !$_REQUEST['currency']) {
            $currency_id1 = 27;
            $c_currency_id = 28;  
        }
        
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
        API::add('Transactions','get24hData',array(46,27));
        API::add('Transactions','get24hData',array(46,28));
        API::add('Transactions','get24hData',array(47,27));
        API::add('Transactions','get24hData',array(47,28));
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

        $transactions_24hrs_xrp_usd = $query['Transactions']['get24hData']['results'][11] ;
        $transactions_24hrs_xrp_btc = $query['Transactions']['get24hData']['results'][12] ;
        $transactions_24hrs_xlm_usd = $query['Transactions']['get24hData']['results'][13] ;
        $transactions_24hrs_xlm_btc = $query['Transactions']['get24hData']['results'][14] ;
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
         <?php
        $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='Home'");
        while($pagerow=mysqli_fetch_array($page_sql))
        {
            $page_id=$pagerow['id'];
        }

        $page_sql1=mysqli_query($conn_l, "select page_content_key,page_content from trans_page_value where page_id=".$page_id);

            $symbol = $_SESSION[LANG];
        while($pagerow1=mysqli_fetch_array($page_sql1))
        {
           
            $page_content = $pagerow1[0];
            // echo $page_content."<br>";
            $page_content1 = json_decode($pagerow1[1],true);
            // print_r($page_content1[$symbol][$page_content]);
            $pgcont[$page_content]=$page_content1[$symbol][$page_content];
            // print_r($pgcont);
           
        }
         // echo "<br><br>"; echo "<br><br>"; echo "<br><br>hello";
// print_r(mysqli_fetch_array($page_sql1));
        // exit;
        // $pg_cont_sql=mysqli_query($conn_l, "select page_content_key, ".$_SESSION[LANG]."_page_content from trans_page_value where page_content_status=1 and page_id='".$page_id."'");
        // while($pagecontrow=mysqli_fetch_array($pg_cont_sql))
        // {
        //     $pgcont[$pagecontrow['page_content_key']]=$pagecontrow[$_SESSION[LANG].'_page_content'];
        // }       
        ?>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
        <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script> 
        <header>
            <div class="banner row no-margin">
                <div class="container content">
                    <h1>Unicrypto Leading Digital Exchange</h1>
                   <?php if($is_user_login==0)
                   { ?>
                    <div class="links">
                        <p><a href="register"><?php echo isset($pgcont['home_create_account_key']) ? $pgcont['home_create_account_key'] : "Create Account"; ?></a><span class="line"></span><?php echo isset($pgcont['home_already_register_key']) ? $pgcont['home_already_register_key'] : "Already Registered"; ?>? <a href="login"><?php echo isset($pgcont['home_login_key']) ? $pgcont['home_login_key'] : "Login"; ?></a></p>
                    </div><?php } ?>
                </div>
            </div>
            <div class="sticky row no-margin">
                <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                    <div class="container">
                        <div class="row">
                            <div style="padding:0 3em;"><span><a href="#"><span><b>BTC</b></span>&nbsp; <i><?=$transactions_24hrs_btc_usd['lastPrice'] ? $transactions_24hrs_btc_usd['lastPrice'] : '0.00'?></i></a></span></div>
                            <div style="padding:0 3em;"><span><a href="#"><span><b>LTC</b></span>&nbsp; <i><?=$transactions_24hrs_ltc_usd['lastPrice'] ? $transactions_24hrs_ltc_usd['lastPrice'] : '0.00'?></i></a></span></div>
                            <div style="padding:0 3em;"><span><a href="#"><span><b>XLM</b></span>&nbsp; <i><?=$transactions_24hrs_xlm_usd['lastPrice'] ? $transactions_24hrs_xlm_usd['lastPrice'] : '0.00'?></i></a></span></div>
                            <div style="padding:0 3em;"><span><a href="#"><span><b>XRP</b></span>&nbsp; <i><?=$transactions_24hrs_xrp_usd['lastPrice'] ? $transactions_24hrs_xrp_usd['lastPrice'] : '0.00'?></i></a></span></div>
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
                        <a href="userbuy?trade=XRP-USD" class="statistics-widget-link">
                            <div class="statistics-widget-grid">
                                <div class="content">
                                    <h5>XRP/USD</h5>
                                    <h6>
                                        <strong class="green-color"><? echo $transactions_24hrs_xrp_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_xrp_usd['lastPrice'],2,4) : '0.00' ; ?></strong><!--  $0.05 -->
                                    </h6>
                                    <p>Volume : <? echo $transactions_24hrs_xrp_usd['transactions_24hrs'] ? $transactions_24hrs_xrp_usd['transactions_24hrs'] : '0.00'; ?> BTC</p>
                                </div>
                                <span class="status green-color"><? echo $transactions_24hrs_xrp_usd['transactions_24hrs'] ? $transactions_24hrs_xrp_usd['change_24hrs'] : '0.00'; ?></span>
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
                        <a href="userbuy?trade=XLM-USD" class="statistics-widget-link">
                            <div class="statistics-widget-grid">
                                <div class="content">
                                    <h5>XLM/USD</h5>
                                    <h6>
                                        <strong class="green-color"><? echo $transactions_24hrs_xlm_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_xlm_usd['lastPrice'],2,4) : '0.00' ; ?></strong><!--  $0.05 -->
                                    </h6>
                                    <p>Volume : <? echo $transactions_24hrs_xlm_usd['transactions_24hrs'] ? $transactions_24hrs_xlm_usd['transactions_24hrs'] : '0.00'; ?> BTC</p>
                                </div>
                                <span class="status green-color"><? echo $transactions_24hrs_xlm_usd['transactions_24hrs'] ? $transactions_24hrs_xlm_usd['change_24hrs'] : '0.00'; ?></span>
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
                            <!-- <input type="hidden" name="trade" value="<?php echo $_REQUEST['trade']; ?>"> -->
                        <div class="row">
                            <div class="col-md-2 col-sm-6 col-xs-6">
                                <select class="form-group form-control" name="currency">
                                <!--  <?php while($currency = mysqli_fetch_assoc($currency_query)) { ?>
                                        <option 
                                        <?php if($_REQUEST['currency'] == $currency['id']) { 
                                           $g_name = $currency['currency']; ?> 
                                        selected
                                        <?php } ?>
                                        value="<?php echo $currency['id']; ?>">
                                            <?php echo $currency['currency']; ?>
                                        </option>
                                    <?php } ?> -->
                                <?php
                                if ($_REQUEST['currency'] != 28) {
                                    ?>
                                    <option value="<?php echo $explode_value[0];?>-<?php echo $explode_value[1];?>-<?php echo $explode_value[2];?>-<?php echo $explode_value[3]; ?>"><?php echo $explode_value[0]; ?>/<?php echo $explode_value[1]; ?></option>
                                    <?php
                                    $g_name = $explode_value[0].'- '.$explode_value[1];
                                    // echo $g_name ;
                                }
                                ?>
                                
                                <option value="BTC-USD-28-27">BTC/USD</option>
                                <option value="LTC-USD-42-27">LTC/USD</option>
                                <option value="ETH-USD-45-27">ETH/USD</option>
                                <option value="XRP-USD-46-27">XRP/USD</option>
                                <option value="XLM-USD-47-27">XLM/USD</option>
                                <option value="LTC-BTC-42-28">LTC/BTC</option>
                                <option value="ETH-BTC-45-28">ETH/BTC</option>
                                <option value="XRP-BTC-46-28">XRP/BTC</option>
                                <option value="XLM-BTC-47-28">XLM/BTC</option>
                                <option value="BTC-LTC-28-42">BTC/LTC</option>
                                <option value="ETH-LTC-45-42">ETH/LTC</option>
                                <option value="XLM-LTC-47-42">XLM/LTC</option>
                                <option value="XRP-LTC-46-42">XRP/LTC</option>
                                <option value="BTC-XRP-28-46">BTC/XRP</option>
                                <option value="ETH-XRP-45-46">ETH/XRP</option>
                                <option value="XLM-XRP-47-46">XLM/XRP</option>
                                <option value="LTC-XRP-42-46">LTC/XRP</option>
                                <option value="BTC-XLM-28-47">BTC/XLM</option>
                                <option value="LTC-XLM-42-47">LTC/XLM</option>
                                <option value="ETH-XLM-45-47">ETH/XLM</option>
                                <option value="XRP-XLM-46-47">XRP/XLM</option>
                                <option value="BTC-ETH-28-45">BTC/ETH</option>
                                <option value="LTC-ETH-42-45">LTC/ETH</option>
                                <option value="XRP-ETH-46-45">XRP/ETH</option>
                                <option value="XLM-ETH-47-45">XLM/ETH</option>
                                </select>
                            </div>
                            <div class="col-md-8 col-sm-6 col-xs-6">
                                <button style="width: 10%;padding-left: 13px;" class="btn btn-primary btn-change"><?php echo isset($pgcont['home_graph_go_key']) ? $pgcont['home_graph_go_key'] : 'GO'; ?></button>
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
                                    <a class="nav-item nav-link active" id="nav-btc-tab" data-toggle="tab" href="#nav-btc" role="tab" aria-controls="nav-btc" aria-selected="true">USD <?php echo isset($pgcont['home_table_head_markets_key']) ? $pgcont['home_table_head_markets_key'] : 'Markets'; ?></a>
                                    <!-- <a class="nav-item nav-link" id="nav-ltc-tab" data-toggle="tab" href="#nav-ltc" role="tab" aria-controls="nav-btc" aria-selected="false">LTC Markets</a> -->
                                    <a class="nav-item nav-link" id="nav-eth-tab" data-toggle="tab" href="#nav-eth" role="tab" aria-controls="nav-eth" aria-selected="false">ETH <?php echo isset($pgcont['home_table_head_markets_key']) ? $pgcont['home_table_head_markets_key'] : 'Markets'; ?></a>
                                    <!-- <a class="nav-item nav-link" id="nav-usdt-tab" data-toggle="tab" href="#nav-usdt" role="tab" aria-controls="nav-usdt" aria-selected="false">BCH Markets</a> -->
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-btc" role="tabpanel" aria-labelledby="nav-btc-tab">
                                    <table id="hm-data-table" class="table row-border hm-data-table table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th><?php echo isset($pgcont['home_table_th_pair_key']) ? $pgcont['home_table_th_pair_key'] : 'Pair'; ?></th>
                                                <th><?php echo isset($pgcont['home_table_th_last_price_key']) ? $pgcont['home_table_th_last_price_key'] : 'Last Price'; ?></th>
                                                <th><?php echo isset($pgcont['home_table_th_24hchanges_key']) ? $pgcont['home_table_th_24hchanges_key'] : '24h Change'; ?></th>
                                                <th><?php echo isset($pgcont['home_table_th_24hvolume_key']) ? $pgcont['home_table_th_24hvolume_key'] : '24h Volume'; ?></th>
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
                                                        XRP/USD
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_xrp_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_xrp_usd['lastPrice'],2,4) : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_xrp_usd['change_24hrs'] ? $transactions_24hrs_xrp_usd['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_xrp_usd['transactions_24hrs'] ? $transactions_24hrs_xrp_usd['transactions_24hrs'] : '0.00' ; ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="star-inner">
                                                        <input id="star1" type="checkbox" name="time" />
                                                        <!-- <label for="star1"><i class="fas fa-star"></i></label> -->
                                                        XLM/USD
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_xlm_usd['lastPrice'] ? Stringz::currency($transactions_24hrs_xlm_usd['lastPrice'],2,4) : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_xlm_usd['change_24hrs'] ? $transactions_24hrs_xlm_usd['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_xlm_usd['transactions_24hrs'] ? $transactions_24hrs_xlm_usd['transactions_24hrs'] : '0.00' ; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="nav-eth" role="tabpanel" aria-labelledby="nav-ltc-tab">
                                    <table id="hm-data-table" class="table row-border hm-data-table table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th><?php echo isset($pgcont['home_table_th_pair_key']) ? $pgcont['home_table_th_pair_key'] : 'Pair'; ?></th>
                                                <th><?php echo isset($pgcont['home_table_th_last_price_key']) ? $pgcont['home_table_th_last_price_key'] : 'Last Price'; ?></th>
                                                <th><?php echo isset($pgcont['home_table_th_24hchanges_key']) ? $pgcont['home_table_th_24hchanges_key'] : '24h Change'; ?></th>
                                                <th><?php echo isset($pgcont['home_table_th_24hvolume_key']) ? $pgcont['home_table_th_24hvolume_key'] : '24h Volume'; ?></th>
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
                                                        XLM/ETH
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="green-color"><? echo $transactions_24hrs_xlm_eth['lastPrice'] ? $transactions_24hrs_xlm_eth['lastPrice'] : '0.00' ; ?></span> <!-- <span class="gray-color">/ $944.16</span> -->
                                                </td>
                                                <td><span class="red-color"><? echo $transactions_24hrs_xlm_eth['change_24hrs'] ? $transactions_24hrs_xlm_eth['change_24hrs'] : '0.00' ; ?></span></td>
                                                <td><? echo $transactions_24hrs_xlm_eth['transactions_24hrs'] ? $transactions_24hrs_xlm_eth['transactions_24hrs'] : '0.00' ; ?></td>
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
        $sql = "SELECT A.date,A.btc_price,B.currency FROM `transactions` A,`currencies` B WHERE A.c_currency = B.id AND  A.c_currency = $c_currency_id  AND A.currency = $currency_id1";
        $my_query = mysqli_query($conn,$sql);
        // echo "string";
        // print_r(mysqli_fetch_assoc($my_query));
        ?>
        <?php include "includes/sonance_footer.php"; ?>
        <script type='text/javascript'>


        google.charts.load('current', {'packages':['annotatedtimeline']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', '<?php echo $g_name; ?>');
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
           
            for($i=1;$i<=2;$i++)
            {          
            $btc_p=0;           
            if($i==1) {
                $year = date("Y");
                $month = date("m");
                $date = date("d");
            }
            else {
                $year = date('Y',(strtotime ( '-1 day' , strtotime ( date('Y-m-d')) ) ));
                $month = date('m',(strtotime ( '-1 day' , strtotime ( date('Y-m-d')) ) ));
                $date = date('d',(strtotime ( '-1 day' , strtotime ( date('Y-m-d')) ) ));
            }

            ?>
            [new Date(<?php echo $year; ?>, <?php echo $month-1; ?> ,<?php echo $date; ?>), <?php echo $btc_p; ?>, undefined, undefined] ,           
           
            <?php } } ?>
        ]);

        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
        chart.draw(data, {displayAnnotations: false});
      }
    

    //load_chart();
    </script>

        <!-- <div class="fb_chat" style=""> 
            <a href="https://www.messenger.com/t/194868894428597" class="fb-msg-btn-chat" target="_blank" rel="nofollow"> Contact us on Facebook</a> 
        </div> -->
</html>