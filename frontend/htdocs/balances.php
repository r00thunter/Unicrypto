<?php
    include '../lib/common.php';
    include 'getbalance.php';
    
    $conn = new mysqli("localhost","root","xchange123","bitexchange_cash");

    if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y') {
        Link::redirect('userprofile.php');
    } elseif (User::$awaiting_token) {
        Link::redirect('verify-token.php');
    } elseif (!User::isLoggedIn()) {
        Link::redirect('login.php');
    }

    $cur_sql = "SELECT * FROM currencies";
    $currency_query = mysqli_query($conn,$cur_sql); 
    $currency_id = $_REQUEST['currency'];
    if (!$currency_id) {
       $currency_id = 28;
       $_REQUEST['currency'] = 28;
    }
    
    /**
     * fetching loggin user data
     */
    API::add('User','getInfo',array($_SESSION['session_id']));
    $fetchUserDataQuery = API::send();
    $user_data = $fetchUserDataQuery['User']['getInfo']['results'][0];
    $user_id = $user_data['id']; 
    
    API::add('User','getAvailable');
    API::add('User','getUserBalance', array($user_id,27)); //usd
    API::add('User','getUserBalance', array($user_id,28)); //btc
    API::add('User','getUserBalance', array($user_id,44)); //bch
    API::add('User','getUserBalance', array($user_id,45)); //eth
    API::add('User','getUserBalance', array($user_id,42)); //ltc
    API::add('User','getUserBalance', array($user_id,43)); //zec
    API::add('User','getUserBalance', array($user_id,46)); //XRP
    API::add('User','getUserBalance', array($user_id,47)); //XLM
    
    //fetching last 24 hrs transactions data
    API::add('Transactions','get24hData',array(28,27)); //btc
    API::add('Transactions','get24hData',array(42,27)); //ltc
    API::add('Transactions','get24hData',array(44,27)); //bch
    API::add('Transactions','get24hData',array(45,27)); //eth
    API::add('Transactions','get24hData',array(43,27)); //zec
    API::add('Transactions','get24hData',array(46,27)); //XRP
    API::add('Transactions','get24hData',array(47,27)); //XLM

////////////////////        Added For Balances On Hold       ////////////////////////////////
    API::add('User','getOnHold');
    API::add('User','getVolume');
    API::add('FeeSchedule','getRecord',array(User::$info['fee_schedule']));
    API::add('Stats','getBTCTraded',array($_SESSION['c_currency']));
    API::add('Currencies','getMain');
    API::add('User','verifiedBankAccount',array($user_id));
   
///////////////////////////////////////////////////////////////////////////////////////// 
    foreach ($CFG->currencies as $key => $currency) {
        if (is_numeric($key) || $currency['is_crypto'] != 'Y') { continue; }
        API::add('Stats','getCurrent',array($currency['id'], 27));
    }
    
    $query = API::send();
    
    $usdtoall = $query['Stats']['getCurrent']['results'];
    $verifiedBankAccount = $query['User']['verifiedBankAccount']['results'][0];
    // print_r($verifiedBankAccount);
    foreach ($usdtoall as $row) {
    $checkusd[$row['market']] = $row;
    }
    $user_available = $query['User']['getAvailable']['results'][0];
    $user_balances = $query['User']['getUserBalance']['results'];
/////////////////           Added For Balances On Hold       ////////////////////////////////
    $currencies = $CFG->currencies;
    $on_hold = $query['User']['getOnHold']['results'][0];
    $available = $query['User']['getAvailable']['results'][0];
    $volume = $query['User']['getVolume']['results'][0];
    $fee_bracket = $query['FeeSchedule']['getRecord']['results'][0];
    $total_btc_volume = $query['Stats']['getBTCTraded']['results'][0][0]['total_btc_traded'];
    $main = $query['Currencies']['getMain']['results'][0];
////////////////////////////////////////////////////////////////////////////////////////////////////
     
    $transactions_24hrs_btc_usd = $query['Transactions']['get24hData']['results'][0] ;
    $transactions_24hrs_ltc_usd = $query['Transactions']['get24hData']['results'][1] ;
    $transactions_24hrs_bch_usd = $query['Transactions']['get24hData']['results'][2] ;
    $transactions_24hrs_eth_usd = $query['Transactions']['get24hData']['results'][3] ;
    $transactions_24hrs_zec_usd = $query['Transactions']['get24hData']['results'][4] ;
    $transactions_24hrs_xrp_usd = $query['Transactions']['get24hData']['results'][5] ;
    $transactions_24hrs_xlm_usd = $query['Transactions']['get24hData']['results'][6] ;
    
    $user_balances_usd = $user_available['USD'];
    $user_balances_btc = $user_available['BTC'];
    $user_balances_bch = $user_available['BCH'];
    $user_balances_eth = $user_available['ETH'];
    $user_balances_ltc = $user_available['LTC'];
    $user_balances_zec = $user_available['ZEC'];
    $user_balances_xrp = $user_available['XRP'];
    $user_balances_xlm = $user_available['XLM'];

    $zec_usd = $transactions_24hrs_zec_usd['lastPrice'] * $user_balances_zec;  // echo  $checkusd['ZEC']['last_price'];  185
    $btc_usd = $transactions_24hrs_btc_usd['lastPrice'] * $user_balances_btc;  // echo $checkusd['BTC']['last_price'];  6002
    $bch_usd = $transactions_24hrs_bch_usd['lastPrice'] * $user_balances_bch; // echo $checkusd['BCH']['last_price']; 83
    $eth_usd = $transactions_24hrs_eth_usd['lastPrice'] * $user_balances_eth;  //   echo $checkusd['ETH']['last_price']; 1186.9
    $ltc_usd = $transactions_24hrs_ltc_usd['lastPrice'] * $user_balances_ltc; //echo $checkusd['LTC']['last_price']; 7000
    $xrp_usd = $transactions_24hrs_xrp_usd['lastPrice'] * $user_balances_xrp; //echo $checkusd['LTC']['last_price']; 7000
    $xlm_usd = $transactions_24hrs_xlm_usd['lastPrice'] * $user_balances_xlm; //echo $checkusd['LTC']['last_price']; 7000
    $totalBalance = $xrp_usd + $user_balances_usd + $btc_usd + $xlm_usd + $eth_usd + $ltc_usd;
    $fiatBalance = $user_balances_usd;
    $cryptoBalance = $xrp_usd + $btc_usd + $xlm_usd + $eth_usd + $ltc_usd;
    $fiatBalance = number_format($fiatBalance, 6);
    $cryptoBalance = number_format($cryptoBalance, 6);
    $totalBalance = number_format($totalBalance, 6);
    ?>
<!DOCTYPE html>
<html lang="en">

    <?php include "includes/sonance_header.php";  ?>
    <style>
        .table-hover tbody tr:hover
        {
            cursor : default;
            background: none;
        }
        .balance-table td a
        {
            cursor : default;
            text-decoration : none;
        }
        .balance-table td a.outline-btn
        {
            cursor : pointer;
        }
        #chartdiv {
          width: 100%;
          height: 450px;
        }
        #chart_div {
            margin-left: 0px;
            padding-left: 0px;
            margin-bottom: 20px;
        }
    </style>
   
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
        <?php
        $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='balances'");
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

        // $pg_cont_sql=mysqli_query($conn_l, "select page_content_key, ".$_SESSION[LANG]."_page_content from trans_page_value where page_content_status=1 and page_id='".$page_id."'");
        // while($pagecontrow=mysqli_fetch_array($pg_cont_sql))
        // {
        //     $pgcont[$pagecontrow['page_content_key']]=$pagecontrow[$_SESSION[LANG].'_page_content'];
        // }       
        ?>
        <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>

        <header>
            <div class="banner row">
                <div class="container content">
                    <h1><?php echo isset($pgcont['balance_heading_key']) ? $pgcont['balance_heading_key'] : 'YOUR ACCOUNT BALANCE'; ?></h1>
                    <p class="text-white text-center"><?php echo isset($pgcont['balance_sub_heading_key']) ? $pgcont['balance_sub_heading_key'] : 'Your Available individual cryptocurrency balances and their respective USD values are listed here.'; ?></p>
                    <div class="text-center">
                        <?php
                            if ($verifiedBankAccount != 0) { ?>
                                <a href="userbuy?trade=BTC-USD&c_currency=28&currency=27"  class="btn text-black"><?php echo isset($pgcont['balance_heading_button_buy_sell_key']) ? $pgcont['balance_heading_button_buy_sell_key'] : 'Buy/Sell Cryptocurrency'; ?></a>
                                <a href="deposit" class="btn" style="background: #007bff !important;"><?php echo isset($pgcont['balance_heading_button_deposit_fiat_key']) ? $pgcont['balance_heading_button_deposit_fiat_key'] : 'Deposit Fiat Currency'; ?></a>
                                <?php
                            }else{ ?>
                                <a class="btn text-black" style="opacity: 0.4;"><?php echo isset($pgcont['balance_heading_button_buy_sell_key']) ? $pgcont['balance_heading_button_buy_sell_key'] : 'Buy/Sell Cryptocurrency'; ?></a>
                                <a class="btn" style="background: #007bff !important;opacity: 0.4;"><?php echo isset($pgcont['balance_heading_button_deposit_fiat_key']) ? $pgcont['balance_heading_button_deposit_fiat_key'] : 'Deposit Fiat Currency'; ?></a>
                                <!-- <a href="manageaccounts"  class="btn text-black">Manage Bank Account</a> -->
                            <?php }
                        ?>

                    </div>
                </div>
            </div>
        </header>
        <div class="page-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <div class="col-md-12" style="background: white;padding: 20px;margin-bottom: 20px;text-align:center">
                            <div class="row">
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-4">
                                   <?php echo isset($pgcont['balance_total_crypto_key']) ? $pgcont['balance_total_crypto_key'] : 'Estimated Total Cryptocurrencies Value in USD'; ?> 
                                    <h4><strong class="totalbalance">$ <?= $cryptoBalance; ?></strong></h4>
                                 </div>
                                 <div class="col-md-4">
                                   <?php echo isset($pgcont['balance_total_usd_key']) ? $pgcont['balance_total_usd_key'] : 'Estimated Total Fiat Value in USD'; ?> 
                                    <h4><strong class="totalbalance">$ <?= $fiatBalance; ?></strong></h4>
                                 </div>
                                 <div class="col-md-2">
                                </div>
                           </div>
                             <p> <?php echo isset($pgcont['balance_total_cryp_usd_key']) ? $pgcont['balance_total_cryp_usd_key'] : 'The total <b>USD</b> value of the Cryptocurrencies and fiat currency present in your respective wallets.'; ?> <p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-table-outer" style="padding:0px" id="my-element">
                            <table  style="margin:0px !important;" class="table row-border info-data-table table-hover balance-table table-border" cellspacing="0 " width="100%">
                                <thead style="background-color: initial;">
                                    <tr>
                                        <th colspan="2">
                                            <h5><?php echo isset($pgcont['balance_market_rate_key']) ? $pgcont['balance_market_rate_key'] : 'Market Rate'; ?>
                                                <span class="float-right">
                                                    <a href="#exampleModal" data-toggle="modal">
                                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                         viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" >
                                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"/>
                                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"/>
                                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                                        C26.355,23.457,25,26.261,25,29.158V32"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                                    </svg>
                                                </a>
                                                </span>
                                            </h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <svg width="25" height="25" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-iuzqsK gFNfZa">
                                                <g fill="none" fill-rule="evenodd">
                                                    <circle fill="#FFAD02" cx="19" cy="19" r="19"></circle>
                                                    <path d="M24.7 19.68a3.63 3.63 0 0 0 1.47-2.06c.74-2.77-.46-4.87-3.2-5.6l.89-3.33a.23.23 0 0 0-.16-.28l-1.32-.35a.23.23 0 0 0-.28.15l-.89 3.33-1.75-.47.88-3.32a.23.23 0 0 0-.16-.28l-1.31-.35a.23.23 0 0 0-.28.15l-.9 3.33-3.73-1a.23.23 0 0 0-.27.16l-.36 1.33c-.03.12.04.25.16.28l.22.06a1.83 1.83 0 0 1 1.28 2.24l-1.9 7.09a1.83 1.83 0 0 1-2.07 1.33.23.23 0 0 0-.24.12l-.69 1.24a.23.23 0 0 0 0 .2c.02.07.07.12.14.13l3.67.99-.89 3.33c-.03.12.04.24.16.27l1.32.35c.12.03.24-.04.28-.16l.89-3.32 1.76.47-.9 3.33c-.02.12.05.24.16.27l1.32.35c.12.03.25-.04.28-.16l.9-3.32.87.23c2.74.74 4.83-.48 5.57-3.25.35-1.3-.05-2.6-.92-3.48zm-5.96-5.95l2.64.7a1.83 1.83 0 0 1 1.28 2.24 1.83 1.83 0 0 1-2.23 1.3l-2.64-.7.95-3.54zm1.14 9.8l-3.51-.95.95-3.54 3.51.94a1.83 1.83 0 0 1 1.28 2.24 1.83 1.83 0 0 1-2.23 1.3z" fill="#FFF"></path>
                                                </g>
                                            </svg>
                                            <span style="position: relative;top: -7px;">Bitcoin</span>
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;">$ <?= $transactions_24hrs_btc_usd['lastPrice'] ? $transactions_24hrs_btc_usd['lastPrice'] : '0.00'; ?></h5>
                                            <span>+<? echo $transactions_24hrs_btc_usd['transactions_24hrs'] ? $transactions_24hrs_btc_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <svg width="25" height="25" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-iuzqsK gFNfZa">
                                                <g fill="none" fill-rule="evenodd">
                                                    <circle cx="19" cy="19" r="19" fill="#B5B5B5" fill-rule="nonzero"></circle>
                                                    <path fill="#FFF" d="M12.29 28.04l1.29-5.52-1.58.67.63-2.85 1.64-.68L16.52 10h5.23l-1.52 7.14 2.09-.74-.58 2.7-2.05.8-.9 4.34h8.1l-.99 3.8z"></path>
                                                </g>
                                            </svg>
                                            <span style="position: relative;top: -7px;">Litecoin</span>
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;">$ <? echo $transactions_24hrs_ltc_usd['lastPrice'] ? $transactions_24hrs_ltc_usd['lastPrice'] : '0.00'; ?></h5>
                                            <span>+<? echo $transactions_24hrs_ltc_usd['transactions_24hrs'] ? $transactions_24hrs_ltc_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                        </td>
                                    </tr>
                                   <!--  <tr>
                                        <td>
                                            <img src="sonance/img/coins/bcc-coin.jpg" style="width:25px;height:25px" class="hoverZoomLink">
                                            <span style="position: relative;top: 5px;">Bitcoin Cash</span>
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;">$ <? echo $transactions_24hrs_bch_usd['lastPrice'] ? $transactions_24hrs_bch_usd['lastPrice'] : '0.00'; ?></h5>
                                            <span>+<? echo $transactions_24hrs_bch_usd['transactions_24hrs'] ? $transactions_24hrs_bch_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                        </td>
                                    </tr> -->
                                    <tr>
                                        <td>
                                            <svg width="25" height="25" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-Marok huTMcA">
                                                <g fill="none" fill-rule="evenodd">
                                                    <ellipse cx="16" cy="16" fill="#6F7CBA" rx="16" ry="16"></ellipse>
                                                    <path fill="#FFF" d="M10.13 17.76c-.1-.15-.06-.2.09-.12l5.49 3.09c.15.08.4.08.56 0l5.58-3.08c.16-.08.2-.03.1.11L16.2 25.9c-.1.15-.28.15-.38 0l-5.7-8.13zm.04-2.03a.3.3 0 0 1-.13-.42l5.74-9.2c.1-.15.25-.15.34 0l5.77 9.19c.1.14.05.33-.12.41l-5.5 2.78a.73.73 0 0 1-.6 0l-5.5-2.76z"></path>
                                                </g>
                                            </svg>
                                            <span style="position: relative;top: -7px;">Ethereum</span>
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;">$ <? echo $transactions_24hrs_eth_usd['lastPrice'] ? $transactions_24hrs_eth_usd['lastPrice'] : '0.00'; ?></h5>
                                            <span>+<? echo $transactions_24hrs_eth_usd['transactions_24hrs'] ? $transactions_24hrs_eth_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <td>
                                            <svg xmlns="http://www.w3.org/2000/svg" height="25" viewBox="0 0 256 256" width="25">
                                                <defs>
                                                    <style>
                                                        .cls-1 { fill: #252525; }
                                                        .cls-2 { fill: #fff; fill-rule: evenodd; }
                                                    </style>
                                                </defs>
                                                <g data-name="zcash zec" id="zcash_zec">
                                                    <g data-name="zcash zec" id="zcash_zec-2">
                                                        <circle class="cls-1" cx="128" cy="128" data-name="Эллипс 27" id="Эллипс_27" r="128" />
                                                        <path class="cls-2" d="M568,1958a79,79,0,1,1-79,79A79,79,0,0,1,568,1958Zm0,17.77A61.225,61.225,0,1,1,506.775,2037,61.231,61.231,0,0,1,568,1975.77Zm-27.65,23.7H560.1v-13.82h15.8v13.82h21.725v17.78l-33.575,37.52h33.575v17.78H575.9v13.83H560.1v-13.83H538.375v-17.78l33.575-37.52h-31.6v-17.78Z" data-name="Эллипс 26" id="Эллипс_26" transform="translate(-440 -1909)" />
                                                    </g>
                                                </g>
                                            </svg>
                                            <span style="position: relative;top: -7px;">ZCash</span>
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;">$ <? echo $transactions_24hrs_zec_usd['lastPrice'] ? $transactions_24hrs_zec_usd['lastPrice'] : '0.00'; ?></h5>
                                            <span>+<? echo $transactions_24hrs_zec_usd['transactions_24hrs'] ? $transactions_24hrs_zec_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                        </td>
                                    </tr> -->
                                     <tr>
                                        <td>
                                            <img src="images/xlm.png" style="width:25px;height:25px" class="hoverZoomLink">
                                            <span style="position: relative;top: 5px;">stellar</span>
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;">$ <? echo $transactions_24hrs_xlm_usd['lastPrice'] ? $transactions_24hrs_xlm_usd['lastPrice'] : '0.00'; ?></h5>
                                            <span>+<? echo $transactions_24hrs_xlm_usd['transactions_24hrs'] ? $transactions_24hrs_xlm_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                        </td>
                                    </tr>
                                     <tr>
                                        <td>
                                            <img src="images/xrp.png" style="width:25px;height:25px" class="hoverZoomLink">
                                            <span style="position: relative;top: 5px;">Ripple</span>
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;">$ <? echo $transactions_24hrs_xrp_usd['lastPrice'] ? $transactions_24hrs_xrp_usd['lastPrice'] : '0.00'; ?></h5>
                                            <span>+<? echo $transactions_24hrs_xrp_usd['transactions_24hrs'] ? $transactions_24hrs_xrp_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-8">
    
                        <div class="info-table-outer" id="my-other-element">
                            <h5 class="balance-caption"><?php echo isset($pgcont['balance_available_balance_key']) ? $pgcont['balance_available_balance_key'] : 'Your Available Balances'; ?>
                                <span class="float-right">
                                    <a href="#exampleModal1" data-toggle="modal">
                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" >
                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"/>
                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"/>
                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                        C26.355,23.457,25,26.261,25,29.158V32"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                    </a>
                                </span>
                            </h5>
                            <table id="info-data-table " class="table row-border info-data-table table-hover balance-table table-border" cellspacing="0 " width="100% ">
                                <thead>
                                    <tr>
                                        <th><?php echo isset($pgcont['balance_available_balance_table_coin_key']) ? $pgcont['balance_available_balance_table_coin_key'] : 'Coin'; ?></th>
                                        <th><?php echo isset($pgcont['balance_available_balance_table_name_key']) ? $pgcont['balance_available_balance_table_name_key'] : 'Name'; ?></th>
                                        <th><?php echo isset($pgcont['balance_available_balance_table_balance_key']) ? $pgcont['balance_available_balance_table_balance_key'] : 'Balance'; ?></th>
                                        <th><?php echo isset($pgcont['balance_available_balance_table_estimate_key']) ? $pgcont['balance_available_balance_table_estimate_key'] : 'Estimated Fiat ≈'; ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><img src="sonance/img/coins/btc-coin.png"></td>
                                        <td><a href="">BTC<span class="name">(Bitcoin)</span></a></td>
                                        <td><?= Stringz::currency($user_balances_btc,true) ?> BTC</td>
                                        <td>$<?=Stringz::currency($transactions_24hrs_btc_usd['lastPrice'] * Stringz::currency($user_balances_btc,true)); ?></td>
                                        <td>
                                            <a href="cryptowallet?c_currency=28" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_deposite_key']) ? $pgcont['balance_available_balance_table_td_deposite_key'] : 'Deposit'; ?></a>
                                            <a href="cryptowallet?c_currency=28" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_withdraw_key']) ? $pgcont['balance_available_balance_table_td_withdraw_key'] : 'Withdraw'; ?></a>
                                            <a href="userbuy?trade=BTC-USD" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_trade_key']) ? $pgcont['balance_available_balance_table_td_trade_key'] : 'Trade'; ?></a>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <td><img src="sonance/img/coins/bcc-coin.jpg"></td>
                                        <td><a href="">BCH<span class="name">(Bitcoin Cash)</span></a></td>
                                        <td><?= Stringz::currency($user_balances_bch,true) ?> BCH</td>
                                        <td>$<?=Stringz::currency($transactions_24hrs_bch_usd['lastPrice'] * Stringz::currency($user_balances_bch,true)); ?></td>
                                        <td>
                                            <a href="cryptowallet?c_currency=44" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_deposite_key']) ? $pgcont['balance_available_balance_table_td_deposite_key'] : 'Deposit'; ?></a>
                                            <a href="cryptowallet?c_currency=44" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_withdraw_key']) ? $pgcont['balance_available_balance_table_td_withdraw_key'] : 'Withdraw'; ?></a>
                                            <a href="userbuy?trade=BCH-USD" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_trade_key']) ? $pgcont['balance_available_balance_table_td_trade_key'] : 'Trade'; ?></a>
                                        </td>
                                    </tr> -->
                                    <tr>
                                        <td><img src="sonance/img/coins/eth-coin.png"></td>
                                        <td><a href="">ETH<span class="name">(Ethereum)</span></a></td>
                                        <td><?= Stringz::currency($user_balances_eth,true) ?> ETH</td>
                                        <td>$<?=Stringz::currency($transactions_24hrs_eth_usd['lastPrice'] * Stringz::currency($user_balances_eth,true)); ?></td>
                                        <td>
                                            <a href="cryptowallet?c_currency=45" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_deposite_key']) ? $pgcont['balance_available_balance_table_td_deposite_key'] : 'Deposit'; ?></a>
                                            <a href="cryptowallet?c_currency=45" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_withdraw_key']) ? $pgcont['balance_available_balance_table_td_withdraw_key'] : 'Withdraw'; ?></a>
                                            <a href="userbuy?trade=ETH-USD" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_trade_key']) ? $pgcont['balance_available_balance_table_td_trade_key'] : 'Trade'; ?></a>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <td>
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 256 256" width="20">
                                                <defs>
                                                    <style>
                                                        .cls-1 { fill: #252525; }
                                                        .cls-2 { fill: #fff; fill-rule: evenodd; }
                                                    </style>
                                                </defs>
                                                <g data-name="zcash zec" id="zcash_zec">
                                                    <g data-name="zcash zec" id="zcash_zec-2">
                                                        <circle class="cls-1" cx="128" cy="128" data-name="Эллипс 27" id="Эллипс_27" r="128" />
                                                        <path class="cls-2" d="M568,1958a79,79,0,1,1-79,79A79,79,0,0,1,568,1958Zm0,17.77A61.225,61.225,0,1,1,506.775,2037,61.231,61.231,0,0,1,568,1975.77Zm-27.65,23.7H560.1v-13.82h15.8v13.82h21.725v17.78l-33.575,37.52h33.575v17.78H575.9v13.83H560.1v-13.83H538.375v-17.78l33.575-37.52h-31.6v-17.78Z" data-name="Эллипс 26" id="Эллипс_26" transform="translate(-440 -1909)" />
                                                    </g>
                                                </g>
                                            </svg>
                                        </td>
                                        <td><a href="">ZEC<span class="name">(ZCash)</span></a></td>
                                        <td><?= Stringz::currency($user_balances_zec,true) ?> ZEC</td>
                                        <td>$<?= Stringz::currency($transactions_24hrs_zec_usd['lastPrice'] * Stringz::currency($user_balances_zec,true)); ?></td>
                                        <td>
                                            <a href="cryptowallet?c_currency=43" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_deposite_key']) ? $pgcont['balance_available_balance_table_td_deposite_key'] : 'Deposit'; ?></a>
                                            <a href="cryptowallet?c_currency=43" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_withdraw_key']) ? $pgcont['balance_available_balance_table_td_withdraw_key'] : 'Withdraw'; ?></a>
                                            <a href="userbuy?trade=ZEC-USD" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_trade_key']) ? $pgcont['balance_available_balance_table_td_trade_key'] : 'Trade'; ?></a>
                                        </td>
                                    </tr> -->
                                    <tr>
                                        <td><img src="sonance/img/coins/ltc-coin.png"></td>
                                        <td><a href="">LTC<span class="name">(Litecoin)</span></a></td>
                                        <td><?= Stringz::currency($user_balances_ltc,true) ?> LTC</td>
                                        <td>$<?= Stringz::currency($transactions_24hrs_ltc_usd['lastPrice'] * Stringz::currency($user_balances_ltc,true)); ?></td>
                                        <td>
                                            <a href="cryptowallet?c_currency=42" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_deposite_key']) ? $pgcont['balance_available_balance_table_td_deposite_key'] : 'Deposit'; ?></a>
                                            <a href="cryptowallet?c_currency=42" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_withdraw_key']) ? $pgcont['balance_available_balance_table_td_withdraw_key'] : 'Withdraw'; ?></a>
                                            <a href="userbuy?trade=LTC-USD" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_trade_key']) ? $pgcont['balance_available_balance_table_td_trade_key'] : 'Trade'; ?></a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><img src="images/xrp.png"></td>
                                        <td><a href="">XRP<span class="name">(Ripple)</span></a></td>
                                        <td><?= Stringz::currency($user_balances_xrp,true) ?> XRP</td>
                                        <td>$<?=Stringz::currency($transactions_24hrs_xrp_usd['lastPrice'] * Stringz::currency($user_balances_xrp,true)); ?></td>
                                        <td>
                                            <a href="cryptowallet?c_currency=46" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_deposite_key']) ? $pgcont['balance_available_balance_table_td_deposite_key'] : 'Deposit'; ?></a>
                                            <a href="cryptowallet?c_currency=46" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_withdraw_key']) ? $pgcont['balance_available_balance_table_td_withdraw_key'] : 'Withdraw'; ?></a>
                                            <a href="userbuy?trade=XRP-USD" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_trade_key']) ? $pgcont['balance_available_balance_table_td_trade_key'] : 'Trade'; ?></a>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td><img src="images/xlm.png"></td>
                                        <td><a href="">XLM<span class="name">(Stellar)</span></a></td>
                                        <td><?= Stringz::currency($user_balances_xlm,true) ?> XLM</td>
                                        <td>$<?=Stringz::currency($transactions_24hrs_xlm_usd['lastPrice'] * Stringz::currency($user_balances_xlm,true)); ?></td>
                                        <td>
                                            <a href="cryptowallet?c_currency=47" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_deposite_key']) ? $pgcont['balance_available_balance_table_td_deposite_key'] : 'Deposit'; ?></a>
                                            <a href="cryptowallet?c_currency=47" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_withdraw_key']) ? $pgcont['balance_available_balance_table_td_withdraw_key'] : 'Withdraw'; ?></a>
                                            <a href="userbuy?trade=XLM-USD" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_trade_key']) ? $pgcont['balance_available_balance_table_td_trade_key'] : 'Trade'; ?></a>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td><img src="images/dollar.png" style="width:20px; height:20px;"></td>
                                        <td><a href="">USD<span class="name">(US Dollars)</span></a></td>
                                        <td colspan="2">$<?= Stringz::currency($user_balances_usd,true) ?>
                                            <p><small>(Us dollars in your usd wallet)</small></p></td>
                                        <td>
                                            <a href="deposit" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_deposite_key']) ? $pgcont['balance_available_balance_table_td_deposite_key'] : 'Deposit'; ?></a>
                                            <a href="withdraw" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_withdraw_key']) ? $pgcont['balance_available_balance_table_td_withdraw_key'] : 'Withdraw'; ?></a>
                                            <!-- <a href="" class="outline-btn"><?php echo isset($pgcont['balance_available_balance_table_td_trade_key']) ? $pgcont['balance_available_balance_table_td_trade_key'] : 'Trade'; ?></a> -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                       <!--  <form name="chat_filter" method="get" action="">
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
                     <div id="chart_div"></div> -->
                        
                    </div>

                   
                </div></div></div>
                <div class="page-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-table-outer" style="padding:0px" id="my-element">
                            <table  style="margin:0px !important;margin-top: 10px;" class="table row-border info-data-table table-hover balance-table table-border" cellspacing="0 " width="100%">
                                <thead style="background-color: initial;">
                                    <tr>
                                        <th colspan="2">
                                            <h5>
                                                <?php echo isset($pgcont['balance_fee_level_key']) ? $pgcont['balance_fee_level_key'] : 'Fee Level and Volume'; ?>                                             
                                            </h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?= Lang::string('account-fee-bracket1') ?>:
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;"><?= $fee_bracket['fee1'] ?>% 
                                                <!--<a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule.php"><i class="fa fa-question-circle"></i></a>--></h5>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                           <?= Lang::string('account-fee-bracket') ?>:
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;"><?= $fee_bracket['fee'] ?>% <!--<a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule.php"><i class="fa fa-question-circle"></i></a>--></h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= str_replace('[currency]',$CFG->currencies[$main['fiat']]['currency'],Lang::string('account-30-day-vol')) ?>:
                                        </td>
                                        <td style="text-align:right">
                                            <h5 style="margin-bottom: 0px;"><?= $CFG->currencies[$main['fiat']]['fa_symbol'].Stringz::currency($volume / $CFG->currencies[$main['fiat']]['usd_ask']) ?></h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8" style="margin-top: 10px;">
                        <div class="info-table-outer" id="my-other-element">
                            <h5 class="balance-caption">
                                <?php echo isset($pgcont['balance_on_hold_key']) ? $pgcont['balance_on_hold_key'] : 'Balances On Hold'; ?>
                            </h5>
                            <table id="info-data-table " class="table row-border info-data-table table-hover balance-table table-border" cellspacing="0 " width="100% ">
                                <thead>
                                    <tr>
                                        <th><?php echo isset($pgcont['balance_available_balance_table_coin_key']) ? $pgcont['balance_available_balance_table_coin_key'] : 'Coin'; ?></th>
                                        <th><?php echo isset($pgcont['balance_available_balance_table_name_key']) ? $pgcont['balance_available_balance_table_name_key'] : 'Name'; ?></th>
                                        <th><?php echo isset($pgcont['balance_on_hold_table_open_key']) ? $pgcont['balance_on_hold_table_open_key'] : 'Open Orders'; ?></th>
                                        <th><?php echo isset($pgcont['balance_on_hold_table_withdrawl_key']) ? $pgcont['balance_on_hold_table_withdrawl_key'] : 'Waiting for Withdrawal'; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                              <?
                                if ($on_hold) {
                                    foreach ($on_hold as $currency => $balance) {
                                        if ($CFG->currencies[$currency]['id'] != $main['crypto'] && (empty($balance['order']) && empty($balance['withdrawal'])))
                                            continue;
                                        
                                        $is_crypto = ($CFG->currencies[$currency]['is_crypto'] == 'Y');
                                ?>
                                    <tr>
                                        <td>
                                            <? if($currency=="BTC")
                                            { ?>
                                            <img src="sonance/img/coins/btc-coin.png">
                                            <?
                                            }else
                                            if($currency=="XRP")
                                                { ?>
                                               <img src="images/xrp.png" >
                                                <?
                                            }else
                                            if($currency=="ETH")
                                                { ?>
                                                <img src="sonance/img/coins/eth-coin.png">
                                                <?
                                            }else
                                            if($currency=="XLM")
                                                { ?>
                                                <img src="images/xlm.png" >
                                                <?
                                            }else
                                            if($currency=="LTC")
                                                { ?>
                                                <img src="sonance/img/coins/ltc-coin.png">
                                                <?
                                            }else
                                            if($currency=="USD")
                                                { ?>
                                                <img src="images/dollar.png" style="width:20px; height:20px;">
                                                <?
                                            }
                                            ?>
                                        </td>
                                        <td><a href=""><?= $currency ?>
                                            <span class="name">

                                                 <? if($currency=="BTC")
                                            { ?>
                                            (Bitcoin)
                                            <?
                                            }else
                                            if($currency=="XRP")
                                                { ?>
                                               (Ripple)
                                                <?
                                            }else
                                            if($currency=="ETH")
                                                { ?>
                                               (Ethereum)
                                                <?
                                            }else
                                            if($currency=="XLM")
                                                { ?>
                                               (Stellar)
                                                <?
                                            }else
                                            if($currency=="LTC")
                                                { ?>
                                               (Litecoin)
                                                <?
                                            }else
                                            if($currency=="USD")
                                                { ?>
                                              (US Dollars)
                                                <?
                                            }?>
                                                    

                                            </span>
                                            </a>
                                        </td>
                                        <td><?= ((!$is_crypto) ? $CFG->currencies[$currency]['fa_symbol'] : '').(!empty($balance['order']) ? Stringz::currency($balance['order'],$is_crypto) : '0.00') ?></td>
                                        <td><?= ((!$is_crypto) ? $CFG->currencies[$currency]['fa_symbol'] : '').(!empty($balance['withdrawal']) ? Stringz::currency($balance['withdrawal'],$is_crypto) : '0.00') ?></td>
                                        
                                    </tr>
                            
                                    <?
                                      }
                                  }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                                      
                    </div>
                     <div class="col-md-8">
                        
                    </div>
                </div>
            </div>
        </div>
    <!-- Modal-1-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['balance_market_rate_key']) ? $pgcont['balance_market_rate_key'] : 'Market Rate'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['balance_market_rate_modal_key']) ? $pgcont['balance_market_rate_modal_key'] : 'The current market rate of all the cryptocurrencies available on this exchange'; ?></p>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>
<!--modal-2-->
<div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['balance_available_balance_key']) ? $pgcont['balance_available_balance_key'] : 'Your Available Balances'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['balance_available_balance_modal_key']) ? $pgcont['balance_available_balance_modal_key'] : 'Your individual  cryptocurrency balances and their respective USD values are listed here.'; ?></p>
      </div>
     <!--  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>
        <?php include "includes/sonance_footer.php"; ?>

        <?php 


        $sql = "SELECT A.date,A.btc_price,B.currency FROM `transactions` A,`currencies` B WHERE A.c_currency = B.id AND c_currency = $currency_id";
        $my_query = mysqli_query($conn,$sql);

        ?>

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
 <!--        <script>
var tour = new Tour({
  steps: [
  {
    element: "#my-element",
    title: "Title of my step",
    content: "Content of my step"
  },
  {
    element: "#my-other-element",
    title: "Title of my step",
    content: "Content of my step"
  }
]}
 
);

tour.init();

tour.start();
 </script>  -->
</html>