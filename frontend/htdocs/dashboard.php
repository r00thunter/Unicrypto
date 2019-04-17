<!doctype html>
<html>

<head>
<title>Dashboard <?= $CFG->exchange_name; ?></title>

<meta property="viewport" name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/dashboard.css" rel="stylesheet" />
<link href="css/new-style.css" rel="stylesheet" />
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

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-118158391-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-118158391-1');
</script>

</head>

<body class="app signed-in static_application index" data-controller-name="static_application" data-action-name="index">
<?php 
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
Link::redirect('userprofile.php');
elseif (User::$awaiting_token)
Link::redirect('verify-token.php');
elseif (!User::isLoggedIn())
Link::redirect('login.php');

// if(empty(User::$ekyc_data) || User::$ekyc_data[0]->status != 'accepted')
// {
// Link::redirect('ekyc.php');
// }
$currencies = Settings::sessionCurrency();
// echo "<pre>"; print_r($CFG->currencies); exit;

API::add('User','getInfo',array($_SESSION['session_id']));
$query = API::send();
$user_data = $query['User']['getInfo']['results'][0];
$user_id = $user_data['id']; 

API::add('User','getAvailable');
API::add('User','getUserBalance', array($user_id,27)); //usd
API::add('User','getUserBalance', array($user_id,28)); //btc
API::add('User','getUserBalance', array($user_id,44)); //bch
API::add('User','getUserBalance', array($user_id,45)); //eth
API::add('User','getUserBalance', array($user_id,42)); //ltc
API::add('User','getUserBalance', array($user_id,43)); //zec
API::add('Transactions','get24hData',array(28,27)); //btc
API::add('Transactions','get24hData',array(42,27)); //ltc
API::add('Transactions','get24hData',array(44,27)); //bch
API::add('Transactions','get24hData',array(45,27)); //eth
API::add('Transactions','get24hData',array(43,27)); //zec

foreach ($CFG->currencies as $key => $currency) {
if (is_numeric($key) || $currency['is_crypto'] != 'Y')
continue;

API::add('Stats','getCurrent',array($currency['id'],13));
}

$query = API::send();

$usdtoall = $query['Stats']['getCurrent']['results'];

foreach ($usdtoall as $row) {
$checkusd[$row['market']] = $row;
}
$user_available = $query['User']['getAvailable']['results'][0];
$user_balances = $query['User']['getUserBalance']['results'];
 // echo "<pre>"; print_r($user_available); exit;
$transactions_24hrs_btc_usd = $query['Transactions']['get24hData']['results'][0] ;
$transactions_24hrs_ltc_usd = $query['Transactions']['get24hData']['results'][1] ;
$transactions_24hrs_bch_usd = $query['Transactions']['get24hData']['results'][2] ;
$transactions_24hrs_eth_usd = $query['Transactions']['get24hData']['results'][3] ;
$transactions_24hrs_zec_usd = $query['Transactions']['get24hData']['results'][4] ;

$c_currency_info = $CFG->currencies[$currencies['c_currency']];
$user_balances_usd = $user_available['USD'];
$user_balances_btc = $user_available['BTC'];
$user_balances_bch = $user_available['BCH'];
$user_balances_eth = $user_available['ETH'];
$user_balances_ltc = $user_available['LTC'];
$user_balances_zec = $user_available['ZEC'];

$zec_usd = $checkusd['ZEC']['last_price'] * $user_balances_zec;
$btc_usd = $checkusd['BTC']['last_price'] * $user_balances_btc;
$bch_usd = $checkusd['BCH']['last_price'] * $user_balances_bch;
$eth_usd = $checkusd['ETH']['last_price'] * $user_balances_eth;
$ltc_usd = $checkusd['LTC']['last_price'] * $user_balances_ltc;
$totalBalance = $zec_usd + $user_balances_usd + $btc_usd + $bch_usd + $eth_usd + $ltc_usd;
$totalBalance = number_format($totalBalance, 2);

// echo "<pre>"; print_r($user_balances); exit;
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
        <h1>Dashboard</h1>
    </div>
</div>
<!-- <div class="LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">
<div class="LayoutDesktop__Content-flhQBc bRMwEm Flex__Flex-fVJVYW gkSoIH">
<div class="Dashboard__FadeFlex-bFoDXs cYFmKg Flex__Flex-fVJVYW iDqRrV">
<div class="Flex__Flex-fVJVYW bHipRv">
<div></div>
<div class="Dashboard__Panels-getBDx fJxaut Flex__Flex-fVJVYW iDqRrV">
<div class="Flex__Flex-fVJVYW bHipRv">
<div class="Flex__Flex-fVJVYW gsOGkq">

<div class="Dashboard__ChartContainer-bKDMTA kjRPPr Flex__Flex-fVJVYW iDqRrV">
<div class="Flex__Flex-fVJVYW gsOGkq" style="border: 1px solid #DAE1E9;width: 100%;border-right: none;">
<? include 'includes/graph.php'; ?>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div> -->

<div class="LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">
<div class="LayoutDesktop__Content-flhQBc bRMwEm Flex__Flex-fVJVYW gkSoIH">
    <div class="Dashboard__FadeFlex-bFoDXs cYFmKg Flex__Flex-fVJVYW iDqRrV">
        <div class="Flex__Flex-fVJVYW bHipRv">
            <div></div>
            <div class="Dashboard__Panels-getBDx fJxaut Flex__Flex-fVJVYW iDqRrV">
                <div class="Flex__Flex-fVJVYW bHipRv">
                    <div class="Flex__Flex-fVJVYW gsOGkq">

                        <div class="Dashboard__ChartContainer-bKDMTA kjRPPr Flex__Flex-fVJVYW iDqRrV">
                            <div class="Flex__Flex-fVJVYW gsOGkq">
                                <!-- Market Section Starts Here -->
                                <div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; margin-bottom:0px;">
                                    <div class="Flex__Flex-fVJVYW bHipRv">
                                        <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
                                            <div class="Flex__Flex-fVJVYW iDqRrV">
                                                <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">Markets</h4>
                                            </div>
                                            <div class="WidgetHeader__Actions-bDbtim jQqaGc">
                                                <div class="Flex__Flex-fVJVYW iDqRrV">

                                                </div>
                                            </div>
                                        </div>

                                        <div class="Flex__Flex-fVJVYW iJJJTg">
                                            <div class="Flex__Flex-fVJVYW bHipRv">
                                                <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                                    <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                        <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                            <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-iuzqsK gFNfZa">
                                                                <g fill="none" fill-rule="evenodd">
                                                                    <circle fill="#FFAD02" cx="19" cy="19" r="19"></circle>
                                                                    <path d="M24.7 19.68a3.63 3.63 0 0 0 1.47-2.06c.74-2.77-.46-4.87-3.2-5.6l.89-3.33a.23.23 0 0 0-.16-.28l-1.32-.35a.23.23 0 0 0-.28.15l-.89 3.33-1.75-.47.88-3.32a.23.23 0 0 0-.16-.28l-1.31-.35a.23.23 0 0 0-.28.15l-.9 3.33-3.73-1a.23.23 0 0 0-.27.16l-.36 1.33c-.03.12.04.25.16.28l.22.06a1.83 1.83 0 0 1 1.28 2.24l-1.9 7.09a1.83 1.83 0 0 1-2.07 1.33.23.23 0 0 0-.24.12l-.69 1.24a.23.23 0 0 0 0 .2c.02.07.07.12.14.13l3.67.99-.89 3.33c-.03.12.04.24.16.27l1.32.35c.12.03.24-.04.28-.16l.89-3.32 1.76.47-.9 3.33c-.02.12.05.24.16.27l1.32.35c.12.03.25-.04.28-.16l.9-3.32.87.23c2.74.74 4.83-.48 5.57-3.25.35-1.3-.05-2.6-.92-3.48zm-5.96-5.95l2.64.7a1.83 1.83 0 0 1 1.28 2.24 1.83 1.83 0 0 1-2.23 1.3l-2.64-.7.95-3.54zm1.14 9.8l-3.51-.95.95-3.54 3.51.94a1.83 1.83 0 0 1 1.28 2.24 1.83 1.83 0 0 1-2.23 1.3z" fill="#FFF"></path>
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <span class="Text__Font-jgIzVM ZJjdO" color="slateDark">Bitcoin</span>
                                                    </div>
                                                    <div class="Flex__Flex-fVJVYW dPenpn">
                                                        <div class="Flex__Flex-fVJVYW fIpMDl">
                                                            <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
    <span>
        $<? echo $transactions_24hrs_btc_usd['lastPrice'] ? $transactions_24hrs_btc_usd['lastPrice'] : '0.00'; ?></span>
                                                                </span>
                                                            </div>
                                                            <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM bZSaVE" color="slate">
    <span>+<? echo $transactions_24hrs_btc_usd['transactions_24hrs'] ? $transactions_24hrs_btc_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                                    <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                        <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" class="CurrencyIcon-UEZcn eaRwjt">
                                                                <g fill="none" fill-rule="evenodd">
                                                                    <circle cx="19" cy="19" r="19" fill="#B5B5B5" fill-rule="nonzero"></circle>
                                                                    <path fill="#FFF" d="M12.29 28.04l1.29-5.52-1.58.67.63-2.85 1.64-.68L16.52 10h5.23l-1.52 7.14 2.09-.74-.58 2.7-2.05.8-.9 4.34h8.1l-.99 3.8z"></path>
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <span class="Text__Font-jgIzVM ZJjdO" color="slateDark">Litecoin</span>
                                                    </div>
                                                    <div class="Flex__Flex-fVJVYW dPenpn">
                                                        <div class="Flex__Flex-fVJVYW fIpMDl">
                                                            <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
    <span>
        $<? echo $transactions_24hrs_ltc_usd['lastPrice'] ? $transactions_24hrs_ltc_usd['lastPrice'] : '0.00'; ?></span>
                                                                </span>
                                                            </div>
                                                            <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM bZSaVE" color="slate">
    <span><? echo $transactions_24hrs_ltc_usd['transactions_24hrs'] ? $transactions_24hrs_ltc_usd['transactions_24hrs'] : '0.00'; ?>%</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                                    <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                        <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                            <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-Marok huTMcA">
                                                                <g fill="none" fill-rule="evenodd">
                                                                    <circle fill="##8DC451" cx="19" cy="19" r="19"></circle>
                                                                    <path d="M24.5 16.72c.37-.76.48-1.64.25-2.52-.75-2.76-2.84-3.98-5.58-3.25l-.89-3.32a.23.23 0 0 0-.28-.16l-1.32.35a.23.23 0 0 0-.16.27l.9 3.33-1.76.47-.9-3.32a.23.23 0 0 0-.27-.16l-1.32.35a.23.23 0 0 0-.16.28l.9 3.32-3.74 1a.23.23 0 0 0-.16.29l.35 1.32c.04.12.16.2.28.17l.22-.06c.97-.26 1.97.32 2.23 1.3l1.9 7.08c.25.93-.25 1.87-1.13 2.2a.23.23 0 0 0-.14.21l.02 1.43c0 .07.04.13.1.18.05.04.12.05.19.04l3.67-.99.9 3.33c.03.12.15.19.27.15l1.31-.35c.12-.03.2-.16.16-.28l-.88-3.32 1.75-.47.9 3.33c.03.12.15.19.27.15l1.32-.35c.12-.03.19-.16.16-.28l-.9-3.32.88-.24c2.75-.73 3.95-2.83 3.2-5.6a3.63 3.63 0 0 0-2.54-2.56zm-8.13-2.17l2.63-.7c.97-.26 1.97.32 2.23 1.3.27.97-.3 1.98-1.28 2.24l-2.63.7-.95-3.54zm5.88 7.91l-3.5.94-.96-3.54 3.51-.94c.97-.26 1.97.32 2.24 1.3.26.98-.32 1.98-1.29 2.24z" fill="#FFF"></path>
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <span class="Text__Font-jgIzVM ZJjdO" color="slateDark">Bitcoin Cash</span>
                                                    </div>
                                                    <div class="Flex__Flex-fVJVYW dPenpn">
                                                        <div class="Flex__Flex-fVJVYW fIpMDl">
                                                            <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
    <span>
        $<? echo $transactions_24hrs_bch_usd['lastPrice'] ? $transactions_24hrs_bch_usd['lastPrice'] : '0.00'; ?></span>
                                                                </span>
                                                            </div>
                                                            <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM bZSaVE" color="slate">
    <span>0.00%</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                                    <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                        <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 32 32" class="CurrencyIcon-ksscak llqTCK">
                                                                <g fill="none" fill-rule="evenodd">
                                                                    <ellipse cx="16" cy="16" fill="#6F7CBA" rx="16" ry="16"></ellipse>
                                                                    <path fill="#FFF" d="M10.13 17.76c-.1-.15-.06-.2.09-.12l5.49 3.09c.15.08.4.08.56 0l5.58-3.08c.16-.08.2-.03.1.11L16.2 25.9c-.1.15-.28.15-.38 0l-5.7-8.13zm.04-2.03a.3.3 0 0 1-.13-.42l5.74-9.2c.1-.15.25-.15.34 0l5.77 9.19c.1.14.05.33-.12.41l-5.5 2.78a.73.73 0 0 1-.6 0l-5.5-2.76z"></path>
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <span class="Text__Font-jgIzVM ZJjdO" color="slateDark">Ethereum</span>
                                                    </div>
                                                    <div class="Flex__Flex-fVJVYW dPenpn">
                                                        <div class="Flex__Flex-fVJVYW fIpMDl">
                                                            <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
    <span>
        $<? echo $transactions_24hrs_eth_usd['lastPrice'] ? $transactions_24hrs_eth_usd['lastPrice'] : '0.00'; ?></span>
                                                                </span>
                                                            </div>
                                                            <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM bZSaVE" color="slate">
    <span>0.00%</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                                    <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                        <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="38" viewBox="0 0 256 256" width="38">
                                                                <defs>
                                                                    <style>
                                                                        .cls-1 {
                                                                            fill: #252525;
                                                                        }
                                                                        
                                                                        .cls-2 {
                                                                            fill: #fff;
                                                                            fill-rule: evenodd;
                                                                        }
                                                                    </style>
                                                                </defs>
                                                                <g data-name="zcash zec" id="zcash_zec">
                                                                    <g data-name="zcash zec" id="zcash_zec-2">
                                                                        <circle class="cls-1" cx="128" cy="128" data-name="Эллипс 27" id="Эллипс_27" r="128" />
                                                                        <path class="cls-2" d="M568,1958a79,79,0,1,1-79,79A79,79,0,0,1,568,1958Zm0,17.77A61.225,61.225,0,1,1,506.775,2037,61.231,61.231,0,0,1,568,1975.77Zm-27.65,23.7H560.1v-13.82h15.8v13.82h21.725v17.78l-33.575,37.52h33.575v17.78H575.9v13.83H560.1v-13.83H538.375v-17.78l33.575-37.52h-31.6v-17.78Z" data-name="Эллипс 26" id="Эллипс_26" transform="translate(-440 -1909)" />
                                                                    </g>
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <span class="Text__Font-jgIzVM ZJjdO" color="slateDark">ZCash</span>
                                                    </div>
                                                    <div class="Flex__Flex-fVJVYW dPenpn">
                                                        <div class="Flex__Flex-fVJVYW fIpMDl">
                                                            <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
    <span>
        $<? echo $transactions_24hrs_zec_usd['lastPrice'] ? $transactions_24hrs_zec_usd['lastPrice'] : '0.00'; ?></span>
                                                                </span>
                                                            </div>
                                                            <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe">
                                                                <span class="Text__Font-jgIzVM bZSaVE" color="slate">
    <span>0.00%</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <!-- Market Section Ends Here -->


<!-- TradingView Widget END -->
                                <div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; width:599px; margin-bottom:0px;margin-left:1px;">
                                   <!-- TradingView Widget BEGIN -->
                                    <div class="tradingview-widget-container">
                                    <div id="tv-medium-widget"></div>
                                    <div class="tradingview-widget-copyright"><span class="blue-text"><a href="https://in.tradingview.com/symbols/BITFINEX-BTCUSD/" rel="noopener" target="_blank"><span class="blue-text">BTCUSD</span></a></span> <span class="blue-text">Quotes</span> by TradingView</div>
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
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="Flex__Flex-fVJVYW gsOGkq">

                        <div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; margin-bottom:0px;min-height: 500px;margin-right: 0;">
                            <div class="Flex__Flex-fVJVYW bHipRv">
                                <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
                                    <div class="Flex__Flex-fVJVYW iDqRrV">
                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">Balances</h4></div>
                                    <div class="WidgetHeader__Actions-bDbtim jQqaGc">
                                        <div class="Flex__Flex-fVJVYW iDqRrV">

                                            <!-- <div class="BalancesWidget__Option-fkDZqx iVXwXF"><span>List</span></div> -->

                                            <!-- <div class="BalancesWidget__Option-fkDZqx iwUpTz"><span>Chart</span></div> -->

                                        </div>
                                    </div>
                                </div>

                                <div class="Flex__Flex-fVJVYW iJJJTg">
                                    <div class="Flex__Flex-fVJVYW bHipRv">
                                        <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                            <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                    <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-iuzqsK gFNfZa">
                                                        <g fill="none" fill-rule="evenodd">
                                                            <circle fill="#FFAD02" cx="19" cy="19" r="19"></circle>
                                                            <path d="M24.7 19.68a3.63 3.63 0 0 0 1.47-2.06c.74-2.77-.46-4.87-3.2-5.6l.89-3.33a.23.23 0 0 0-.16-.28l-1.32-.35a.23.23 0 0 0-.28.15l-.89 3.33-1.75-.47.88-3.32a.23.23 0 0 0-.16-.28l-1.31-.35a.23.23 0 0 0-.28.15l-.9 3.33-3.73-1a.23.23 0 0 0-.27.16l-.36 1.33c-.03.12.04.25.16.28l.22.06a1.83 1.83 0 0 1 1.28 2.24l-1.9 7.09a1.83 1.83 0 0 1-2.07 1.33.23.23 0 0 0-.24.12l-.69 1.24a.23.23 0 0 0 0 .2c.02.07.07.12.14.13l3.67.99-.89 3.33c-.03.12.04.24.16.27l1.32.35c.12.03.24-.04.28-.16l.89-3.32 1.76.47-.9 3.33c-.02.12.05.24.16.27l1.32.35c.12.03.25-.04.28-.16l.9-3.32.87.23c2.74.74 4.83-.48 5.57-3.25.35-1.3-.05-2.6-.92-3.48zm-5.96-5.95l2.64.7a1.83 1.83 0 0 1 1.28 2.24 1.83 1.83 0 0 1-2.23 1.3l-2.64-.7.95-3.54zm1.14 9.8l-3.51-.95.95-3.54 3.51.94a1.83 1.83 0 0 1 1.28 2.24 1.83 1.83 0 0 1-2.23 1.3z" fill="#FFF"></path>
                                                        </g>
                                                    </svg>
                                                </div><span class="Text__Font-jgIzVM ZJjdO" color="slateDark">Bitcoin</span></div>
                                            <div class="Flex__Flex-fVJVYW dPenpn">
                                                <div class="Flex__Flex-fVJVYW fIpMDl">
                                                    <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM gJaRtZ" color="slateDark"><span><?= Stringz::currency($user_balances_btc,true) ?> BTC</span></span>
                                                    </div>
                                                    <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM bZSaVE" color="slate"><span>$<?php
                                    echo Stringz::currency($checkusd['BTC']['last_price'] * Stringz::currency($user_balances_btc,true)); ?></span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                            <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                    <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-Marok huTMcA">
                                                        <g fill="none" fill-rule="evenodd">
                                                            <circle fill="##8DC451" cx="19" cy="19" r="19"></circle>
                                                            <path d="M24.5 16.72c.37-.76.48-1.64.25-2.52-.75-2.76-2.84-3.98-5.58-3.25l-.89-3.32a.23.23 0 0 0-.28-.16l-1.32.35a.23.23 0 0 0-.16.27l.9 3.33-1.76.47-.9-3.32a.23.23 0 0 0-.27-.16l-1.32.35a.23.23 0 0 0-.16.28l.9 3.32-3.74 1a.23.23 0 0 0-.16.29l.35 1.32c.04.12.16.2.28.17l.22-.06c.97-.26 1.97.32 2.23 1.3l1.9 7.08c.25.93-.25 1.87-1.13 2.2a.23.23 0 0 0-.14.21l.02 1.43c0 .07.04.13.1.18.05.04.12.05.19.04l3.67-.99.9 3.33c.03.12.15.19.27.15l1.31-.35c.12-.03.2-.16.16-.28l-.88-3.32 1.75-.47.9 3.33c.03.12.15.19.27.15l1.32-.35c.12-.03.19-.16.16-.28l-.9-3.32.88-.24c2.75-.73 3.95-2.83 3.2-5.6a3.63 3.63 0 0 0-2.54-2.56zm-8.13-2.17l2.63-.7c.97-.26 1.97.32 2.23 1.3.27.97-.3 1.98-1.28 2.24l-2.63.7-.95-3.54zm5.88 7.91l-3.5.94-.96-3.54 3.51-.94c.97-.26 1.97.32 2.24 1.3.26.98-.32 1.98-1.29 2.24z" fill="#FFF"></path>
                                                        </g>
                                                    </svg>
                                                </div><span class="Text__Font-jgIzVM ZJjdO" color="slateDark">Bitcoin Cash</span></div>
                                            <div class="Flex__Flex-fVJVYW dPenpn">
                                                <div class="Flex__Flex-fVJVYW fIpMDl">
                                                    <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM gJaRtZ" color="slateDark"><span><?= Stringz::currency($user_balances_bch,true) ?> BCH</span></span>
                                                    </div>
                                                    <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM bZSaVE" color="slate"><span>$<?php
                                    echo Stringz::currency($checkusd['BCH']['last_price'] * Stringz::currency($user_balances_bch,true)); ?></span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                            <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 32 32" class="CurrencyIcon-ksscak llqTCK">
                                                        <g fill="none" fill-rule="evenodd">
                                                            <ellipse cx="16" cy="16" fill="#6F7CBA" rx="16" ry="16"></ellipse>
                                                            <path fill="#FFF" d="M10.13 17.76c-.1-.15-.06-.2.09-.12l5.49 3.09c.15.08.4.08.56 0l5.58-3.08c.16-.08.2-.03.1.11L16.2 25.9c-.1.15-.28.15-.38 0l-5.7-8.13zm.04-2.03a.3.3 0 0 1-.13-.42l5.74-9.2c.1-.15.25-.15.34 0l5.77 9.19c.1.14.05.33-.12.41l-5.5 2.78a.73.73 0 0 1-.6 0l-5.5-2.76z"></path>
                                                        </g>
                                                    </svg>
                                                </div><span class="Text__Font-jgIzVM ZJjdO" color="slateDark">Ethereum</span></div>
                                            <div class="Flex__Flex-fVJVYW dPenpn">
                                                <div class="Flex__Flex-fVJVYW fIpMDl">
                                                    <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM gJaRtZ" color="slateDark"><span><?= Stringz::currency($user_balances_eth,true) ?> ETH</span></span>
                                                    </div>
                                                    <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM bZSaVE" color="slate"><span>$<?php
                                    echo Stringz::currency($checkusd['ETH']['last_price'] * Stringz::currency($user_balances_eth,true)); ?></span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- zcash balance section start -->
                                                                <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                                                    <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                                        <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" height="38" viewBox="0 0 256 256" width="38">
                                                                                        <defs>
                                                                                            <style>
                                                                                                .cls-1 {
                                                                                                fill: #252525;
                                                                                                }
                                                                                                .cls-2 {
                                                                                                fill: #fff;
                                                                                                fill-rule: evenodd;
                                                                                                }
                                                                                            </style>
                                                                                        </defs>
                                                                                        <g data-name="zcash zec" id="zcash_zec">
                                                                                            <g data-name="zcash zec" id="zcash_zec-2">
                                                                                                <circle class="cls-1" cx="128" cy="128" data-name="Эллипс 27" id="Эллипс_27" r="128"></circle>
                                                                                                <path class="cls-2" d="M568,1958a79,79,0,1,1-79,79A79,79,0,0,1,568,1958Zm0,17.77A61.225,61.225,0,1,1,506.775,2037,61.231,61.231,0,0,1,568,1975.77Zm-27.65,23.7H560.1v-13.82h15.8v13.82h21.725v17.78l-33.575,37.52h33.575v17.78H575.9v13.83H560.1v-13.83H538.375v-17.78l33.575-37.52h-31.6v-17.78Z" data-name="Эллипс 26" id="Эллипс_26" transform="translate(-440 -1909)"></path>
                                                                                            </g>
                                                                                        </g>
                                                                                    </svg>
                                                                        </div>
                                                                        <span class="Text__Font-jgIzVM ZJjdO" color="slateDark">ZCash</span>
                                                                    </div>
                                                                    <div class="Flex__Flex-fVJVYW dPenpn">
                                                                        <div class="Flex__Flex-fVJVYW fIpMDl">
                                                                            <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM gJaRtZ" color="slateDark"><span><?= Stringz::currency($user_balances_zec,true) ?> ZEC</span></span>
                                                                            </div>
                                                                            <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM bZSaVE" color="slate"><span>$<?php
                                                                                echo Stringz::currency($checkusd['ZEC']['last_price'] * Stringz::currency($user_balances_zec,true)); ?></span></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- zcash balance section end -->

                                        <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                            <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" class="CurrencyIcon-UEZcn eaRwjt">
                                                        <g fill="none" fill-rule="evenodd">
                                                            <circle cx="19" cy="19" r="19" fill="#B5B5B5" fill-rule="nonzero"></circle>
                                                            <path fill="#FFF" d="M12.29 28.04l1.29-5.52-1.58.67.63-2.85 1.64-.68L16.52 10h5.23l-1.52 7.14 2.09-.74-.58 2.7-2.05.8-.9 4.34h8.1l-.99 3.8z"></path>
                                                        </g>
                                                    </svg>
                                                </div><span class="Text__Font-jgIzVM ZJjdO" color="slateDark">Litecoin</span></div>
                                            <div class="Flex__Flex-fVJVYW dPenpn">
                                                <div class="Flex__Flex-fVJVYW fIpMDl">
                                                    <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM gJaRtZ" color="slateDark"><span><?= Stringz::currency($user_balances_ltc,true) ?> LTC</span></span>
                                                    </div>
                                                    <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM bZSaVE" color="slate"><span>$<?php
                                    echo Stringz::currency($checkusd['LTC']['last_price'] * Stringz::currency($user_balances_ltc,true)); ?></span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="BalanceRow__Wrapper-GRndq csnBXu Flex__Flex-fVJVYW hQXxaf">
                                            <div class="BalanceRow__Title-fAGjSb bYfUmM Flex__Flex-fVJVYW reCYb">
                                                <div class="BalanceRow__Icon-llPQSA hVyxPC Flex__Flex-fVJVYW iDqRrV">

                                                    <img src="images/dollar.png" style="width:40px; height:40px;">

                                                </div><span class="Text__Font-jgIzVM ZJjdO" color="slateDark">USD</span></div>
                                            <div class="Flex__Flex-fVJVYW dPenpn">
                                                <div class="Flex__Flex-fVJVYW fIpMDl">
                                                    <div class="BalanceRow__Amount-hltLsT jjlJLI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM gJaRtZ" color="slateDark"><span>$<?= Stringz::currency($user_balances_usd,true) ?></span></span>
                                                    </div>
                                                    <!-- <div class="BalanceRow__NativeAmount-jQZVyX fyqmeI Flex__Flex-fVJVYW jvHpwe"><span class="Text__Font-jgIzVM bZSaVE" color="slate"><span>$0.00</span></span>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="WidgetFooter__Wrapper-srJyb uKBRe Flex__Flex-fVJVYW kSAvah">Total Balance&nbsp;&asymp;&nbsp;<span>$<?= $totalBalance; ?></span></div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; width:599px; margin-bottom:0px;height:auto;">
                            <div class="Flex__Flex-fVJVYW bHipRv">
                                <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
                                    <div class="Flex__Flex-fVJVYW iDqRrV">
                                        <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"></h4></div>
                                    <div class="WidgetHeader__Actions-bDbtim jQqaGc">
                                        <div class="Flex__Flex-fVJVYW iDqRrV">

                                            <!--  <div class="BalancesWidget__Option-fkDZqx iwUpTz"><span>List</span></div> 

<div class="BalancesWidget__Option-fkDZqx iVXwXF"><span>Chart</span></div>  -->

                                        </div>
                                    </div>
                                </div>
                                <div class="Flex__Flex-fVJVYW iJJJTg">
                                    <div class="BalancesWidget__ChartContainer-kgTRHW gfLJuO Flex__Flex-fVJVYW iJJJTg">
                                        <div class="BalancesWidget__AbsoluteContainer-gBvypJ bgLVqk Flex__Flex-fVJVYW aApUU">
                                            <svg class="Circle__Circle-eIRfZN dIYXeD" width="325" height="325">
                                                <circle r="157.5" cx="162.5" cy="162.5" width="325" stroke-width="2.5" fill="rgba(0,0,0,0)" stroke="#DAE1E9" stroke-dasharray="989.6016858807849 989.6016858807849"></circle>
                                            </svg>
                                        </div>
                                        <div class="BalancesWidget__AbsoluteContainer-gBvypJ bgLVqk Flex__Flex-fVJVYW aApUU">
                                            <div class="BigAmount__Number-fWXHBq gBskIE Flex__Flex-fVJVYW iDqRrV"><span><span class="BigAmount__AmountSuper-jnVzGG jdlzFZ">
$</span><span><?=explode('.', $totalBalance)[0];?></span><span class="BigAmount__AmountSuper-jnVzGG jdlzFZ">.<?=explode('.', $totalBalance)[1];?></span></span>
                                            </div><span class="BalancesWidget__UpperCaseText-coAUrJ gTiMma Text__Font-jgIzVM knUWzj" color="slate">Total Balance</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- Footer Section Starts Here -->
        <?php include "includes/footer.php"; ?>
        <!-- Footer Section Ends Here -->
<div class="Backdrop__LayoutBackdrop-eRYGPr cdNVJh"></div>
</div>
</div>
</div>
</div>
<div></div>
</div>
</div>

<script>
$(document).ready(function() {
$(".Header__DropdownButton-dItiAm").click(function() {
$(".DropdownMenu__Wrapper-ieiZya.kwMMmE").toggleClass("show-menu");
});
});
</script>
<script type="text/javascript" src="js/ops.js?v=20160210"></script>

<script type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.time.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.crosshairs.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.candle.js"></script>

</body>

</html>