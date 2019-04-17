<!doctype html>
<html xmlns:fb="http://ogp.me/ns/fb#">

<head>
<title>Crypto Wallet <?= $CFG->exchange_name; ?></title>

<meta property="viewport" name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/new-style.css" rel="stylesheet" />
<link href="css/wallet.css" rel="stylesheet" />

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1.0" data-react-helmet="true">

</head>

<body class="app signed-in static_application index" data-controller-name="static_application" data-action-name="index" data-view-name="Coinbase.Views.StaticApplication.Index" data-account-id="">
<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include '../lib/common.php';
// echo "<pre>"; print_r($CFG); exit;
if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
	Link::redirect('userprofile');
elseif (User::$awaiting_token)
	Link::redirect('verify-token');
elseif (!User::isLoggedIn())
    Link::redirect('login'); 
    
//     if(empty(User::$ekyc_data) || User::$ekyc_data[0]->status != 'accepted')
// {
//     Link::redirect('ekyc');
// }

$page1 = (!empty($_REQUEST['page'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['page']) : false;
$currencies = Settings::sessionCurrency();
API::add('BankAccounts','get');
API::add('User','getAvailable');
API::add('BitcoinAddresses','get',array(false,$currencies['c_currency'],false,1,1));
API::add('Content','getRecord',array('deposit-bank-instructions'));
API::add('Content','getRecord',array('deposit-no-bank'));
API::add('Wallets','getWallet',array($currencies['c_currency']));
foreach ($CFG->currencies as $key => $currency) {
	if (is_numeric($key) || $currency['is_crypto'] != 'Y')
		continue;
		
	API::add('Stats','getCurrent',array($currency['id'],13));
}

$query = API::send();

$inrtoall = $query['Stats']['getCurrent']['results'];

foreach ($inrtoall as $row) {
    $checkinr[$row['market']] = $row;
}

$bank_accounts = $query['BankAccounts']['get']['results'][0];
$bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
$user_available = $query['User']['getAvailable']['results'][0];
// echo "<pre>"; print_r($user_available); exit;

$wallet = $query['Wallets']['getWallet']['results'][0];
$c_currency_info = $CFG->currencies[$currencies['c_currency']];
$btc_address1 = (!empty($_REQUEST['btc_address'])) ?  preg_replace("/[^\da-z]/i", "",$_REQUEST['btc_address']) : false;
// echo "string ".$btc_address1; exit;
$btc_amount1 = (!empty($_REQUEST['btc_amount'])) ? Stringz::currencyInput($_REQUEST['btc_amount']) : 0;
$btc_total1 = ($btc_amount1 > 0) ? $btc_amount1 - $wallet['bitcoin_sending_fee'] : 0;
$account1 = (!empty($_REQUEST['account'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['account']) : false;
$fiat_amount1 = (!empty($_REQUEST['fiat_amount'])) ? Stringz::currencyInput($_REQUEST['fiat_amount']) : 0;
$fiat_total1 = ($fiat_amount1 > 0) ? $fiat_amount1 - $CFG->fiat_withdraw_fee : 0;
$token1 = (!empty($_REQUEST['token'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['token']) : false;
$authcode1 = (!empty($_REQUEST['authcode'])) ? $_REQUEST['authcode'] : false;
$request_2fa = false;
$no_token = false;

if ($authcode1) {
	API::add('Requests','emailValidate',array(urlencode($authcode1)));
	$query = API::send();

	if ($query['Requests']['emailValidate']['results'][0]) {
		Link::redirect('userwallet?message=withdraw-2fa-success');
	}
	else {
		Errors::add(Lang::string('settings-request-expired'));
	}
}
API::add('Requests','get',array(1,false,false,1));
API::add('Requests','get',array(false,$page1,15,1));
$query = API::send();

$withdraw_requests = $query['Requests']['get']['results'][1];
// echo "<pre>"; print_r($withdraw_requests); exit;

API::add('Requests','get',array(1));
API::add('Requests','get',array(false,$page1,15));
$query = API::send();
$deposit_requests = $query['Requests']['get']['results'][1];
// echo "<pre>"; print_r($deposit_requests); exit;

if ($CFG->withdrawals_status == 'suspended')
    Errors::add(Lang::string('withdrawal-suspended'));

if ($btc_address1)
    API::add('BitcoinAddresses','validateAddress',array($currencies['c_currency'],$btc_address1));
    $query = API::send();
 // echo "<pre>"; print_r($query['BitcoinAddresses']['validateAddress']['results']);

if (!empty($_REQUEST['bitcoins'])) {
    // echo "string"; exit;
	if (($btc_amount1 - $wallet['bitcoin_sending_fee']) < 0.00000001)
		Errors::add(Lang::string('withdraw-amount-zero'));
	if ($btc_amount1 > $user_available[$c_currency_info['currency']])
		Errors::add(str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('withdraw-too-much')));
	if (!$query['BitcoinAddresses']['validateAddress']['results'][0])
		Errors::add(str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('withdraw-address-invalid')));
	
	if (!is_array(Errors::$errors)) {
		if (User::$info['confirm_withdrawal_email_btc'] == 'Y' && !$request_2fa && !$token1) {
			API::add('Requests','insert',array($c_currency_info['id'],$btc_amount1,$btc_address1));
			$query = API::send();
			Link::redirect('userwallet?notice=email');
		}
		elseif (!$request_2fa) {
			API::token($token1);
			API::add('Requests','insert',array($c_currency_info['id'],$btc_amount1,$btc_address1));
			$query = API::send();
			
			if ($query['error'] == 'security-com-error')
				Errors::add(Lang::string('security-com-error'));
			
			if ($query['error'] == 'authy-errors')
				Errors::merge($query['authy_errors']);
			
			if ($query['error'] == 'security-incorrect-token')
				Errors::add(Lang::string('security-incorrect-token'));
			
			if (!is_array(Errors::$errors)) {
				if ($query['Requests']['insert']['results'][0]) {
					if ($token1 > 0)
						Link::redirect('userwallet?message=withdraw-2fa-success');
					else
						Link::redirect('userwallet?message=withdraw-success');
				}	
			}
			elseif (!$no_token) {
				$request_2fa = true;
			}
		}
	}
	elseif (!$no_token) {
		$request_2fa = false;
	}
}

if (!empty($_REQUEST['message'])) {
    if ($_REQUEST['message'] == 'withdraw-2fa-success')
        Messages::add(Lang::string('withdraw-2fa-success'));
    elseif ($_REQUEST['message'] == 'withdraw-success')
        Messages::add(Lang::string('withdraw-success'));
}

if (!empty($_REQUEST['notice']) && $_REQUEST['notice'] == 'email')
    $notice = Lang::string('withdraw-email-notice');

$page_title = Lang::string('withdraw');

?>
<style>
    .bg-active{
        background:#fff ;
    }
    .bg-active .iDqRrV
    {
        border-left: .2px solid #1166d1;
    }
    .bg-normal{
        background-color: #F9FBFC;
    }
    .bg-normal .eaiFtd{
        display:none;
    }
</style>
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
        <h1>Wallet</h1>
    </div>
</div>
<div class="Accounts__Container-cJqPrg TBfrq LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">
<div class="LayoutDesktop__Content-flhQBc kuJaHF Flex__Flex-fVJVYW gkSoIH">

<div class="Panel__Container-hCUKEb ejcVRF">
<div class="Accounts__Header-kUlsOz hjYHLC Flex__Flex-fVJVYW hQXxaf">
<h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">
<span>Your Crypto Accounts </span>
</h4>
<? Errors::display(); ?>
	<? Messages::display(); ?>
	<?= (!empty($notice)) ? '<div class="notice"><div class="message-box-wrap">'.$notice.'</div></div>' : '' ?>
</div>
<div class="Accounts__HeightWrapper-bIafrT bjqSJc Flex__Flex-fVJVYW iJJJTg">

<div class="Accounts__AccountDetailsContainer-gmlBsr eCaGZQ Flex__Flex-fVJVYW bHipRv">
<div class="TransactionList__Container-hwtrOD hhOcaD Flex__Flex-fVJVYW bHipRv">

<!-- Receive Coin Starts Here -->
<div style="width:100%;display: flex;" class="container-otr">
	

<!-- Send Coin Starts Here -->
	<div id="sendContainer" class="modal-content" style="max-width: 100%; width: 50%;margin: 5px;border: 1px solid #ddd;display: inline-block;float: left;">
	<h3>Send Cryptos</h3>
	<form id="buy_form" action="userwallet.php" method="POST">
	<div class="calc dotted">
	    <p>
	        <?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('sell-btc-available')) ?>
	        <span class="pull-right"><?= Stringz::currency($user_available[$c_currency_info['currency']],true) ?> <?= $c_currency_info['currency'] ?></span>
	    </p>
	</div>

	<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
	<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
	    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('withdraw-withdraw') ?></h4>
	</div>
	<div>
	    <div class="Flex__Flex-fVJVYW gkSoIH">
	        <div class="form-group">
	        	<select id="c_currency" name="currency" class="form-control">
				<?
				if ($CFG->currencies) {
					foreach ($CFG->currencies as $key => $currency) {
						if (is_numeric($key) || $currency['is_crypto'] != 'Y')
							continue;
						
						echo '<option '.(($currency['id'] == $currencies['c_currency']) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
					}
				}	
				?>
				</select>	
	        </div>
	    </div>
	</div>
	</div>

	<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
	<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
	    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('withdraw-send-to-address') ?></h4>
	</div>
	<div>
	    <div class="Flex__Flex-fVJVYW gkSoIH">
	        <div class="form-group">
	        	<input type="text" class="form-control " id="btc_address" name="btc_address" value="<?= $btc_address1 ?>" />
	        </div>
	    </div>
	</div>
	</div>

	<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
	<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
	    <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('withdraw-send-amount') ?></h4>
	</div>
	<div>
	    <div class="Flex__Flex-fVJVYW gkSoIH">
	        <div class="form-group">
	        	<input type="text" class="form-control" id="btc_amount" name="btc_amount" value="<?= Stringz::currency($btc_amount1,true) ?>" />
	        <div class="input-caption"><?= $c_currency_info['currency'] ?></div>
	        </div>
	    </div>
	</div>
	</div>

	<div class="current-otr">
	    <p>
	        <?= Lang::string('withdraw-network-fee') ?>
	        <span class="pull-right"><span id="withdraw_btc_network_fee"><?= Stringz::currencyOutput($wallet['bitcoin_sending_fee']) ?></span> <?= $c_currency_info['currency'] ?></span>
	    </p>
	</div>
	<div class="current-otr">
	    <p>
	       <span id="withdraw_btc_total_label"><?= str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('withdraw-btc-total')) ?> </span>
	        <span class="pull-right"><span id="withdraw_btc_total"><?= Stringz::currency($btc_total1,true) ?></span></span>
	    </p>
	</div>
	<input type="hidden" name="bitcoins" value="1" />
	<input type="submit" name="submit" value="<?= Lang::string('withdraw-send-bitcoins') ?>" class="but_user buy-btc" />

	</form>
	</div>
<!-- Send Coin Ends Here -->
<div id="receiveContainer" class="modal-content-new" style="max-width: 100%;width: 50%;margin: 5px;border: 1px solid #ddd;display: inline-block;float: left;">
	<h3>Receive Cryptos </h3>

	<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
	<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
	<p>Select Currency</p>
	</div>
	<div>
	<div class="Flex__Flex-fVJVYW gkSoIH">
	    <div class="form-group">
	    <select id="c_currency" name="currency" class="form-control">
		<?
		if ($CFG->currencies) {
			foreach ($CFG->currencies as $key => $currency) {
				if (is_numeric($key) || $currency['is_crypto'] != 'Y')
					continue;
				
				echo '<option '.(($currency['id'] == $currencies['c_currency']) ? 'selected="selected"' : '').' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
			}
		}	
		?>
		</select>	
	    </div>
	</div>
	</div>
	</div>

	<div class="TradeSection__Wrapper-jIpuvx bskbTZ">
	<div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
	<p><?= Lang::string('deposit-send-to-address') ?><p>
	</div>
	<div>
	<div class="Flex__Flex-fVJVYW gkSoIH">
	    <div class="form-group">
	    	<input type="text" class="form-control" id="deposit_address" name="deposit_address" value="<?= $bitcoin_addresses[0]['address'] ?>" />
	    </div>
	    <div class="form-group" style="text-align: center;margin-top:2em;">
	        <img class="qrcode" src="includes/qrcode.php?code=<?= $bitcoin_addresses[0]['address'] ?>" style="width: 185px;height: 185px;"/>
	    </div>
	</div>
	</div>
	</div>

	<a href="bitcoin-addresses"><button class="Button__Container-hQftQV kZBVvC" style="cursor: pointer;">
	<div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
	<div class="Flex__Flex-fVJVYW ghkoKS"><?= Lang::string('deposit-manage-addresses') ?></div>
	</div>
	</button></a>
	</div>
<!-- Receive Coin Ends Here -->
</div>

</div>
</div>
<div class="Accounts__AccountListWrapper-hJZLkd fzpAGl Flex__Flex-fVJVYW iDqRrV">
<div class="AccountList__AccountsList-eEZOms jZdQTT Flex__Flex-fVJVYW gkSoIH">

<a class="AccountList__AccountLink-cGluzb bnXidV" href="javascript:void(0);">
    <div class="AccountListItem__Account-laXKDv jIylJE Flex__Flex-fVJVYW iDqRrV">
        <div class="AccountListItem__SelectedIndicator-dpXoDO eaiFtd Flex__Flex-fVJVYW iDqRrV"></div>
        <div class="AccountListItem__ContentWrap-kSwyDk koXqeq Flex__Flex-fVJVYW iJJJTg">
            <div class="AccountListItem__Icon-jlvqZo bEvKsN Flex__Flex-fVJVYW iDqRrV">
                <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-gdWZMT crJeiC">
                    <g fill="none" fill-rule="evenodd">
                        <circle fill="#FFAD02" cx="19" cy="19" r="19"></circle>
                        <path d="M24.7 19.68a3.63 3.63 0 0 0 1.47-2.06c.74-2.77-.46-4.87-3.2-5.6l.89-3.33a.23.23 0 0 0-.16-.28l-1.32-.35a.23.23 0 0 0-.28.15l-.89 3.33-1.75-.47.88-3.32a.23.23 0 0 0-.16-.28l-1.31-.35a.23.23 0 0 0-.28.15l-.9 3.33-3.73-1a.23.23 0 0 0-.27.16l-.36 1.33c-.03.12.04.25.16.28l.22.06a1.83 1.83 0 0 1 1.28 2.24l-1.9 7.09a1.83 1.83 0 0 1-2.07 1.33.23.23 0 0 0-.24.12l-.69 1.24a.23.23 0 0 0 0 .2c.02.07.07.12.14.13l3.67.99-.89 3.33c-.03.12.04.24.16.27l1.32.35c.12.03.24-.04.28-.16l.89-3.32 1.76.47-.9 3.33c-.02.12.05.24.16.27l1.32.35c.12.03.25-.04.28-.16l.9-3.32.87.23c2.74.74 4.83-.48 5.57-3.25.35-1.3-.05-2.6-.92-3.48zm-5.96-5.95l2.64.7a1.83 1.83 0 0 1 1.28 2.24 1.83 1.83 0 0 1-2.23 1.3l-2.64-.7.95-3.54zm1.14 9.8l-3.51-.95.95-3.54 3.51.94a1.83 1.83 0 0 1 1.28 2.24 1.83 1.83 0 0 1-2.23 1.3z"
                            fill="#FFF"></path>
                    </g>
                </svg>
            </div>
            <div class="Flex__Flex-fVJVYW iJJJTg">
                <div class="AccountListItem__Details-cWizxw GENJj Flex__Flex-fVJVYW bHipRv">
                    <span class="EditableAccountName__AccountName-eWmPDg IEDDp Text__Font-jgIzVM cXMret" color="slateDark">BTC Wallet</span>
                    <div>
                        <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
                            <span><?= Stringz::currency($user_available['BTC'],true) ?> BTC</span>
                        </span>
                        <span>
                            <span class="Currency__SpacerText-fSDTqc kAitaK Text__Font-jgIzVM gJaRtZ" color="slateDark">&asymp;</span>
                            <span class="Text__Font-jgIzVM bZSaVE" color="slate">
                                <span>
                                    $<?php
                                    echo Stringz::currency($checkinr['BTC']['last_price'] * Stringz::currency($user_available['BTC'],true));
                                    ?>
                                    </span>
                            </span>
                        </span>
                    </div>
                    <!-- <div class="AccountListItem__Actions-bsqZNF fNrJZo Flex__Flex-fVJVYW iDqRrV">
                        <div class="Flex__Flex-fVJVYW ghkoKS">
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI sendcls" >
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="AccountActionButtons__SendIcon-dZmYeo hrRTUc">
                                        <path d="M15.7.3a1 1 0 0 0-1.04-.24l-14 5a1 1 0 0 0-.1 1.83l4.58 2.3L11 5l-4.19 5.86 2.3 4.59a1 1 0 0 0 1.83-.11l5-14a1 1 0 0 0-.23-1.05z">
                                        </path>
                                    </svg>
                                    <span>Send</span>
                                </div>
                            </button>
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI receivecls">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" class="AccountActionButtons__QRIcon-iiSViy fncwsP">
                                        <path d="M6.42 6.42H0V0h6.42v6.42zM1.17 5.25h4.08V1.17H1.17v4.08zM14 6.42H7.58V0H14v6.42zM8.75 5.25h4.08V1.17H8.75v4.08zM6.42 14H0V7.58h6.42V14zm-5.25-1.17h4.08V8.75H1.17v4.08zM14 11.67h-1.17V8.75h-1.16v1.75h-3.5V7.58h1.16v1.75h1.17V7.58H14zM14 14H8.17v-2.33h1.16v1.16H14z"></path>
                                        <path d="M2.33 2.33h1.75v1.75H2.33zM9.92 2.33h1.75v1.75H9.92zM2.33 9.92h1.75v1.75H2.33z"></path>
                                    </svg>
                                    <span>Receive</span>
                                </div>
                            </button>
                        </div>
                        
                    </div> -->
                </div>
            </div>
        </div>
    </div>

</a>

<a class="AccountList__AccountLink-cGluzb bnXidV" href="javascript:void(0);">
    <div class="AccountListItem__Account-laXKDv dLmsTJ Flex__Flex-fVJVYW iDqRrV ">
        <div class="AccountListItem__SelectedIndicator-dpXoDO gGcmVR Flex__Flex-fVJVYW iDqRrV"></div>
        <div class="AccountListItem__ContentWrap-kSwyDk jRHZPa Flex__Flex-fVJVYW iJJJTg">
            <div class="AccountListItem__Icon-jlvqZo bEvKsN Flex__Flex-fVJVYW iDqRrV">
                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" class="CurrencyIcon-fhkqpi iMGyub">
                    <g fill="none" fill-rule="evenodd">
                        <circle cx="19" cy="19" r="19" fill="#B5B5B5" fill-rule="nonzero"></circle>
                        <path fill="#FFF" d="M12.29 28.04l1.29-5.52-1.58.67.63-2.85 1.64-.68L16.52 10h5.23l-1.52 7.14 2.09-.74-.58 2.7-2.05.8-.9 4.34h8.1l-.99 3.8z"></path>
                    </g>
                </svg>
            </div>
            <div class="Flex__Flex-fVJVYW iJJJTg">
                <div class="AccountListItem__Details-cWizxw GENJj Flex__Flex-fVJVYW bHipRv">
                    <span class="EditableAccountName__AccountName-eWmPDg IEDDp Text__Font-jgIzVM cXMret" color="slateDark">LTC Wallet</span>
                    <div>
                        <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
                            <span><?= Stringz::currency($user_available['LTC'],true) ?> LTC</span>
                        </span>
                        <span>
                            <span class="Currency__SpacerText-fSDTqc kAitaK Text__Font-jgIzVM gJaRtZ" color="slateDark">&asymp;</span>
                            <span class="Text__Font-jgIzVM bZSaVE" color="slate">
                                <span>
                                    $
                                    <?php
                                    echo Stringz::currency($checkinr['LTC']['last_price'] * Stringz::currency($user_available['LTC'],true));
                                    ?></span>
                            </span>
                        </span>
                    </div>
                    <!-- <div class="AccountListItem__Actions-bsqZNF fNrJZo Flex__Flex-fVJVYW iDqRrV">
                        <div class="Flex__Flex-fVJVYW ghkoKS">
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI" disabled="">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="AccountActionButtons__SendIcon-dZmYeo hrRTUc">
                                        <path d="M15.7.3a1 1 0 0 0-1.04-.24l-14 5a1 1 0 0 0-.1 1.83l4.58 2.3L11 5l-4.19 5.86 2.3 4.59a1 1 0 0 0 1.83-.11l5-14a1 1 0 0 0-.23-1.05z"></path>
                                    </svg>
                                    <span>Send</span>
                                </div>
                            </button>
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI" id="newltcBtn">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" class="AccountActionButtons__QRIcon-iiSViy fncwsP">
                                        <path d="M6.42 6.42H0V0h6.42v6.42zM1.17 5.25h4.08V1.17H1.17v4.08zM14 6.42H7.58V0H14v6.42zM8.75 5.25h4.08V1.17H8.75v4.08zM6.42 14H0V7.58h6.42V14zm-5.25-1.17h4.08V8.75H1.17v4.08zM14 11.67h-1.17V8.75h-1.16v1.75h-3.5V7.58h1.16v1.75h1.17V7.58H14zM14 14H8.17v-2.33h1.16v1.16H14z"></path>
                                        <path d="M2.33 2.33h1.75v1.75H2.33zM9.92 2.33h1.75v1.75H9.92zM2.33 9.92h1.75v1.75H2.33z"></path>
                                    </svg>
                                    <span>Receive</span>
                                </div>
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</a>

<a class="AccountList__AccountLink-cGluzb bnXidV" href="javascript:void(0);">
    <div class="AccountListItem__Account-laXKDv dLmsTJ Flex__Flex-fVJVYW iDqRrV ">
        <div class="AccountListItem__SelectedIndicator-dpXoDO gGcmVR Flex__Flex-fVJVYW iDqRrV"></div>
        <div class="AccountListItem__ContentWrap-kSwyDk jRHZPa Flex__Flex-fVJVYW iJJJTg">
            <div class="AccountListItem__Icon-jlvqZo bEvKsN Flex__Flex-fVJVYW iDqRrV">
                <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" class="CurrencyIcon-kGmnaC dXMUIb">
                    <g fill="none" fill-rule="evenodd">
                        <circle fill="##8DC451" cx="19" cy="19" r="19"></circle>
                        <path d="M24.5 16.72c.37-.76.48-1.64.25-2.52-.75-2.76-2.84-3.98-5.58-3.25l-.89-3.32a.23.23 0 0 0-.28-.16l-1.32.35a.23.23 0 0 0-.16.27l.9 3.33-1.76.47-.9-3.32a.23.23 0 0 0-.27-.16l-1.32.35a.23.23 0 0 0-.16.28l.9 3.32-3.74 1a.23.23 0 0 0-.16.29l.35 1.32c.04.12.16.2.28.17l.22-.06c.97-.26 1.97.32 2.23 1.3l1.9 7.08c.25.93-.25 1.87-1.13 2.2a.23.23 0 0 0-.14.21l.02 1.43c0 .07.04.13.1.18.05.04.12.05.19.04l3.67-.99.9 3.33c.03.12.15.19.27.15l1.31-.35c.12-.03.2-.16.16-.28l-.88-3.32 1.75-.47.9 3.33c.03.12.15.19.27.15l1.32-.35c.12-.03.19-.16.16-.28l-.9-3.32.88-.24c2.75-.73 3.95-2.83 3.2-5.6a3.63 3.63 0 0 0-2.54-2.56zm-8.13-2.17l2.63-.7c.97-.26 1.97.32 2.23 1.3.27.97-.3 1.98-1.28 2.24l-2.63.7-.95-3.54zm5.88 7.91l-3.5.94-.96-3.54 3.51-.94c.97-.26 1.97.32 2.24 1.3.26.98-.32 1.98-1.29 2.24z"
                            fill="#FFF"></path>
                    </g>
                </svg>
            </div>
            <div class="Flex__Flex-fVJVYW iJJJTg">
                <div class="AccountListItem__Details-cWizxw GENJj Flex__Flex-fVJVYW bHipRv">
                    <span class="EditableAccountName__AccountName-eWmPDg IEDDp Text__Font-jgIzVM cXMret" color="slateDark">BCH Wallet</span>
                    <div>
                        <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
                            <span><?= Stringz::currency($user_available['BCH'],true) ?> BCH</span>
                        </span>
                        <span>
                            <span class="Currency__SpacerText-fSDTqc kAitaK Text__Font-jgIzVM gJaRtZ" color="slateDark">&asymp;</span>
                            <span class="Text__Font-jgIzVM bZSaVE" color="slate">
                                <span>
                                    $<?php
                                    echo Stringz::currency($checkinr['BCH']['last_price'] * Stringz::currency($user_available['BCH'],true));
                                    ?></span>
                            </span>
                        </span>
                    </div>
                    <!-- <div class="AccountListItem__Actions-bsqZNF fNrJZo Flex__Flex-fVJVYW iDqRrV">
                        <div class="Flex__Flex-fVJVYW ghkoKS">
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI" disabled="">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="AccountActionButtons__SendIcon-dZmYeo hrRTUc">
                                        <path d="M15.7.3a1 1 0 0 0-1.04-.24l-14 5a1 1 0 0 0-.1 1.83l4.58 2.3L11 5l-4.19 5.86 2.3 4.59a1 1 0 0 0 1.83-.11l5-14a1 1 0 0 0-.23-1.05z"></path>
                                    </svg>
                                    <span>Send</span>
                                </div>
                            </button>
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" class="AccountActionButtons__QRIcon-iiSViy fncwsP">
                                        <path d="M6.42 6.42H0V0h6.42v6.42zM1.17 5.25h4.08V1.17H1.17v4.08zM14 6.42H7.58V0H14v6.42zM8.75 5.25h4.08V1.17H8.75v4.08zM6.42 14H0V7.58h6.42V14zm-5.25-1.17h4.08V8.75H1.17v4.08zM14 11.67h-1.17V8.75h-1.16v1.75h-3.5V7.58h1.16v1.75h1.17V7.58H14zM14 14H8.17v-2.33h1.16v1.16H14z"></path>
                                        <path d="M2.33 2.33h1.75v1.75H2.33zM9.92 2.33h1.75v1.75H9.92zM2.33 9.92h1.75v1.75H2.33z"></path>
                                    </svg>
                                    <span>Receive</span>
                                </div>
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</a>
<a class="AccountList__AccountLink-cGluzb bnXidV" href="javascript:void(0);">
    <div class="AccountListItem__Account-laXKDv dLmsTJ Flex__Flex-fVJVYW iDqRrV">
        <div class="AccountListItem__SelectedIndicator-dpXoDO gGcmVR Flex__Flex-fVJVYW iDqRrV">
        </div>
        <div class="AccountListItem__ContentWrap-kSwyDk jRHZPa Flex__Flex-fVJVYW iJJJTg">
            <div class="AccountListItem__Icon-jlvqZo bEvKsN Flex__Flex-fVJVYW iDqRrV">
                <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 32 32" class="CurrencyIcon-lapLDb kJWJBN">
                    <g fill="none" fill-rule="evenodd">
                        <ellipse cx="16" cy="16" fill="#6F7CBA" rx="16" ry="16"></ellipse>
                        <path fill="#FFF" d="M10.13 17.76c-.1-.15-.06-.2.09-.12l5.49 3.09c.15.08.4.08.56 0l5.58-3.08c.16-.08.2-.03.1.11L16.2 25.9c-.1.15-.28.15-.38 0l-5.7-8.13zm.04-2.03a.3.3 0 0 1-.13-.42l5.74-9.2c.1-.15.25-.15.34 0l5.77 9.19c.1.14.05.33-.12.41l-5.5 2.78a.73.73 0 0 1-.6 0l-5.5-2.76z"></path>
                    </g>
                </svg>
            </div>
            <div class="Flex__Flex-fVJVYW iJJJTg">
                <div class="AccountListItem__Details-cWizxw GENJj Flex__Flex-fVJVYW bHipRv">
                    <span class="EditableAccountName__AccountName-eWmPDg IEDDp Text__Font-jgIzVM cXMret" color="slateDark">ETH Wallet</span>
                    <div>
                        <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
                            <span><?= Stringz::currency($user_available['ETH'],true) ?> ETH</span>
                        </span>
                        <span>
                            <span class="Currency__SpacerText-fSDTqc kAitaK Text__Font-jgIzVM gJaRtZ" color="slateDark">&asymp;</span>
                            <span class="Text__Font-jgIzVM bZSaVE" color="slate">
                                <span>
                                    $<?php
                                    echo Stringz::currency($checkinr['ETH']['last_price'] * Stringz::currency($user_available['ETH'],true));
                                    ?></span>
                            </span>
                        </span>
                    </div>
                    <!-- <div class="AccountListItem__Actions-bsqZNF fNrJZo Flex__Flex-fVJVYW iDqRrV">
                        <div class="Flex__Flex-fVJVYW ghkoKS">
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI" disabled="">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="AccountActionButtons__SendIcon-dZmYeo hrRTUc">
                                        <path d="M15.7.3a1 1 0 0 0-1.04-.24l-14 5a1 1 0 0 0-.1 1.83l4.58 2.3L11 5l-4.19 5.86 2.3 4.59a1 1 0 0 0 1.83-.11l5-14a1 1 0 0 0-.23-1.05z"></path>
                                    </svg>
                                    <span>Send</span>
                                </div>
                            </button>
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" class="AccountActionButtons__QRIcon-iiSViy fncwsP">
                                        <path d="M6.42 6.42H0V0h6.42v6.42zM1.17 5.25h4.08V1.17H1.17v4.08zM14 6.42H7.58V0H14v6.42zM8.75 5.25h4.08V1.17H8.75v4.08zM6.42 14H0V7.58h6.42V14zm-5.25-1.17h4.08V8.75H1.17v4.08zM14 11.67h-1.17V8.75h-1.16v1.75h-3.5V7.58h1.16v1.75h1.17V7.58H14zM14 14H8.17v-2.33h1.16v1.16H14z"></path>
                                        <path d="M2.33 2.33h1.75v1.75H2.33zM9.92 2.33h1.75v1.75H9.92zM2.33 9.92h1.75v1.75H2.33z"></path>
                                    </svg>
                                    <span>Receive</span>
                                </div>
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</a>
<a class="AccountList__AccountLink-cGluzb bnXidV" href="javascript:void(0);">
    <div class="AccountListItem__Account-laXKDv dLmsTJ Flex__Flex-fVJVYW iDqRrV">
        <div class="AccountListItem__SelectedIndicator-dpXoDO gGcmVR Flex__Flex-fVJVYW iDqRrV">
        </div>
        <div class="AccountListItem__ContentWrap-kSwyDk jRHZPa Flex__Flex-fVJVYW iJJJTg">
            <div class="AccountListItem__Icon-jlvqZo bEvKsN Flex__Flex-fVJVYW iDqRrV">
            <svg xmlns="http://www.w3.org/2000/svg" height="38" viewBox="0 0 256 256" width="38"><defs><style>
      .cls-1 {
        fill: #252525;
      }

      .cls-2 {
        fill: #fff;
        fill-rule: evenodd;
      }
    </style></defs><g data-name="zcash zec" id="zcash_zec"><g data-name="zcash zec" id="zcash_zec-2"><circle class="cls-1" cx="128" cy="128" data-name="Эллипс 27" id="Эллипс_27" r="128"/><path class="cls-2" d="M568,1958a79,79,0,1,1-79,79A79,79,0,0,1,568,1958Zm0,17.77A61.225,61.225,0,1,1,506.775,2037,61.231,61.231,0,0,1,568,1975.77Zm-27.65,23.7H560.1v-13.82h15.8v13.82h21.725v17.78l-33.575,37.52h33.575v17.78H575.9v13.83H560.1v-13.83H538.375v-17.78l33.575-37.52h-31.6v-17.78Z" data-name="Эллипс 26" id="Эллипс_26" transform="translate(-440 -1909)"/></g></g></svg>
            </div>
            <div class="Flex__Flex-fVJVYW iJJJTg">
                <div class="AccountListItem__Details-cWizxw GENJj Flex__Flex-fVJVYW bHipRv">
                    <span class="EditableAccountName__AccountName-eWmPDg IEDDp Text__Font-jgIzVM cXMret" color="slateDark">ZEC Wallet</span>
                    <div>
                        <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
                            <span><?= Stringz::currency($user_available['ZEC'],true) ?> ZEC</span>
                        </span>
                        <span>
                            <span class="Currency__SpacerText-fSDTqc kAitaK Text__Font-jgIzVM gJaRtZ" color="slateDark">&asymp;</span>
                            <span class="Text__Font-jgIzVM bZSaVE" color="slate">
                                <span>
                                    $<?php
                                    echo Stringz::currency($checkinr['ZEC']['last_price'] * Stringz::currency($user_available['ZEC'],true));
                                    ?></span>
                            </span>
                        </span>
                    </div>
                    <!-- <div class="AccountListItem__Actions-bsqZNF fNrJZo Flex__Flex-fVJVYW iDqRrV">
                        <div class="Flex__Flex-fVJVYW ghkoKS">
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI" disabled="">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="AccountActionButtons__SendIcon-dZmYeo hrRTUc">
                                        <path d="M15.7.3a1 1 0 0 0-1.04-.24l-14 5a1 1 0 0 0-.1 1.83l4.58 2.3L11 5l-4.19 5.86 2.3 4.59a1 1 0 0 0 1.83-.11l5-14a1 1 0 0 0-.23-1.05z"></path>
                                    </svg>
                                    <span>Send</span>
                                </div>
                            </button>
                            <button class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI">
                                <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" class="AccountActionButtons__QRIcon-iiSViy fncwsP">
                                        <path d="M6.42 6.42H0V0h6.42v6.42zM1.17 5.25h4.08V1.17H1.17v4.08zM14 6.42H7.58V0H14v6.42zM8.75 5.25h4.08V1.17H8.75v4.08zM6.42 14H0V7.58h6.42V14zm-5.25-1.17h4.08V8.75H1.17v4.08zM14 11.67h-1.17V8.75h-1.16v1.75h-3.5V7.58h1.16v1.75h1.17V7.58H14zM14 14H8.17v-2.33h1.16v1.16H14z"></path>
                                        <path d="M2.33 2.33h1.75v1.75H2.33zM9.92 2.33h1.75v1.75H9.92zM2.33 9.92h1.75v1.75H2.33z"></path>
                                    </svg>
                                    <span>Receive</span>
                                </div>
                            </button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</a>

</div>
</div>
</div>
</div>


<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">

<div class="Flex__Flex-fVJVYW gsOGkq">

<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH" style="padding-top:0;">
	<div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">
            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('deposit-recent') ?></h4>
        </div>
    </div>
	 <div class="Flex__Flex-fVJVYW iJJJTg">
         <div class="table-otr">
          <table id="bids_list">
            <tr>
            	<th>ID</th>
				<th><?= Lang::string('deposit-date') ?></th>
				<th><?= Lang::string('deposit-description') ?></th>
				<th><?= Lang::string('deposit-amount') ?></th>
				<th><?= Lang::string('withdraw-net-amount') ?></th>
				<th><?= Lang::string('deposit-status') ?></th>
			</tr>
			<? 
				if ($deposit_requests) {
					foreach ($deposit_requests as $request) {
						echo '
				<tr>
					<td>'.$request['id'].'</td>
					<td><input type="hidden" class="localdate" value="'.(strtotime($request['date'])/* + $CFG->timezone_offset*/).'" /></td>
					<td>'.$request['description'].'</td>
					<td>'.(($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency($request['amount'],true).' '.$request['fa_symbol'] : $request['fa_symbol'].Stringz::currency($request['amount'])).'</td>
					<td>'.(($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency((($request['net_amount'] > 0) ? $request['net_amount'] : ($request['amount'] - $request['fee'])),true).' '.$request['fa_symbol'] : $request['fa_symbol'].Stringz::currency((($request['net_amount'] > 0) ? $request['net_amount'] : ($request['amount'] - $request['fee'])))).'</td>
					<td>'.$request['status'].'</td>
				</tr>';
					}
				}
				else {
					echo '<tr><td colspan="6">'.Lang::string('withdraw-no').'</td></tr>';
				}
				?>
          </table>
          </div>
    </div>
</div>

</div>

</div>

<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">

<div class="Flex__Flex-fVJVYW gsOGkq" >

<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH" style="padding-top:0;">
	<div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">
            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('withdrawal-recent') ?></h4>
        </div>
    </div>
	 <div class="Flex__Flex-fVJVYW iJJJTg">
         <div class="table-otr">
          <table id="bids_list">
            <tr>
            	<th>ID</th>
				<th><?= Lang::string('deposit-date') ?></th>
				<th><?= Lang::string('deposit-description') ?></th>
				<th><?= Lang::string('deposit-amount') ?></th>
				<th><?= Lang::string('withdraw-net-amount') ?></th>
				<th><?= Lang::string('deposit-status') ?></th>
			</tr>
			<? 
				if ($withdraw_requests) {
					foreach ($withdraw_requests as $request) {
						echo '
				<tr>
					<td>'.$request['id'].'</td>
					<td><input type="hidden" class="localdate" value="'.(strtotime($request['date'])/* + $CFG->timezone_offset*/).'" /></td>
					<td>'.$request['description'].'</td>
					<td>'.(($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency($request['amount'],true).' '.$request['fa_symbol'] : $request['fa_symbol'].Stringz::currency($request['amount'])).'</td>
					<td>'.(($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency((($request['net_amount'] > 0) ? $request['net_amount'] : ($request['amount'] - $request['fee'])),true).' '.$request['fa_symbol'] : $request['fa_symbol'].Stringz::currency((($request['net_amount'] > 0) ? $request['net_amount'] : ($request['amount'] - $request['fee'])))).'</td>
					<td>'.$request['status'].'</td>
				</tr>';
					}
				}
				else {
					echo '<tr><td colspan="6">'.Lang::string('withdraw-no').'</td></tr>';
				}
				?>
          </table>
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
</div>
<div>
<script src="https://d2wy8f7a9ursnm.cloudfront.net/bugsnag-2.min.js" data-apikey="afb3b2c84dbb04bf0f2f260003685211"></script>
<!-- main js -->
<script type="text/javascript" src="js/ops.js?v=20160210"></script>
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


</body>

</html>