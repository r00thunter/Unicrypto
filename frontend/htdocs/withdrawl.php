<?php
include '../lib/common.php';

// if(empty(User::$ekyc_data) || User::$ekyc_data[0]->status != 'accepted')
// {
//     Link::redirect('ekyc');
// }

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
	Link::redirect('userprofile');
elseif (User::$awaiting_token)
	Link::redirect('verify-token');
elseif (!User::isLoggedIn())
	Link::redirect('login');

$currencies = Settings::sessionCurrency();
API::add('Wallets','getWallet',array($currencies['c_currency']));
$query = API::send();

$wallet = $query['Wallets']['getWallet']['results'][0];
$c_currency_info = $CFG->currencies[$currencies['c_currency']];
$page1 = (!empty($_REQUEST['page'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['page']) : false;
$btc_address1 = (!empty($_REQUEST['btc_address'])) ?  preg_replace("/[^\da-z]/i", "",$_REQUEST['btc_address']) : false;
$btc_amount1 = (!empty($_REQUEST['btc_amount'])) ? Stringz::currencyInput($_REQUEST['btc_amount']) : 0;
$btc_total1 = ($btc_amount1 > 0) ? $btc_amount1 - $wallet['bitcoin_sending_fee'] : 0;
$account1 = (!empty($_REQUEST['account'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['account']) : false;
$fiat_amount1 = (!empty($_REQUEST['fiat_amount'])) ? Stringz::currencyInput($_REQUEST['fiat_amount']) : 0;
$fiat_total1 = ($fiat_amount1 > 0) ? $fiat_amount1 - $CFG->fiat_withdraw_fee : 0;
$token1 = (!empty($_REQUEST['token'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['token']) : false;
$authcode1 = (!empty($_REQUEST['authcode'])) ? $_REQUEST['authcode'] : false;
$request_2fa = false;
$no_token = false;

if ((!empty($_REQUEST['bitcoins']) || !empty($_REQUEST['fiat'])) && !$token1) {
	if (!empty($_REQUEST['request_2fa'])) {
		if (!($token1 > 0)) {
			$no_token = true;
			$request_2fa = true;
			Errors::add(Lang::string('security-no-token'));
		}
	}

	if ((User::$info['verified_authy'] == 'Y'|| User::$info['verified_google'] == 'Y') && ((User::$info['confirm_withdrawal_2fa_btc'] == 'Y' && $_REQUEST['bitcoins']) || (User::$info['confirm_withdrawal_2fa_bank'] == 'Y' && $_REQUEST['fiat']))) {
		if (!empty($_REQUEST['send_sms']) || User::$info['using_sms'] == 'Y') {
			if (User::sendSMS()) {
				$sent_sms = true;
				Messages::add(Lang::string('withdraw-sms-sent'));
			}
		}
		$request_2fa = true;
	}
}

if ($authcode1) {
	API::add('Requests','emailValidate',array(urlencode($authcode1)));
	$query = API::send();

	if ($query['Requests']['emailValidate']['results'][0]) {
		Link::redirect('withdraw?message=withdraw-2fa-success');
	}
	else {
		Errors::add(Lang::string('settings-request-expired'));
	}
}

API::add('Content','getRecord',array('deposit-no-bank'));
API::add('User','getAvailable');
API::add('Requests','get',array(1,false,false,1));
API::add('Requests','get',array(false,$page1,15,1));
API::add('BankAccounts','get');
if ($account1 > 0)
	API::add('BankAccounts','getRecord',array($account1));
if ($btc_address1)
	API::add('BitcoinAddresses','validateAddress',array($currencies['c_currency'],$btc_address1));
$query = API::send();

$user_available = $query['User']['getAvailable']['results'][0];
$bank_instructions = $query['Content']['getRecord']['results'][0];
$bank_accounts = $query['BankAccounts']['get']['results'][0];
// echo "<pre>"; print_r($bank_accounts); exit;
$total = $query['Requests']['get']['results'][0];
$requests = $query['Requests']['get']['results'][1];

if ($account1 > 0) {
	$bank_account = $query['BankAccounts']['getRecord']['results'][0];
}
elseif ($bank_accounts) {
	$key = key($bank_accounts);
	$bank_account = $bank_accounts[$key];	
}

if ($bank_account) {
	$currency_info = $CFG->currencies[$bank_account['currency']];
	$currency1 = $currency_info['currency'];
}

$pagination = Content::pagination('withdraw',$page1,$total,15,5,false);

if ($CFG->withdrawals_status == 'suspended')
	Errors::add(Lang::string('withdrawal-suspended'));

if (!empty($_REQUEST['bitcoins'])) {
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
			Link::redirect('withdraw?notice=email');
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
						Link::redirect('withdraw?message=withdraw-2fa-success');
					else
						Link::redirect('withdraw?message=withdraw-success');
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
elseif (!empty($_REQUEST['fiat'])) {
	if (!($account1 > 0))
		Errors::add(Lang::string('withdraw-no-account'));
	if (!is_array($bank_account))
		Errors::add(Lang::string('withdraw-account-not-found'));
	if (!($fiat_amount1 > 0))
		Errors::add(Lang::string('withdraw-amount-zero'));
	if ($fiat_amount1 > 0 && $fiat_amount1 < 1)
		Errors::add(Lang::string('withdraw-amount-one'));
	if (!$bank_accounts[$bank_account['account_number']])
		Errors::add(Lang::string('withdraw-account-not-found'));
	if ($fiat_amount1 > $user_available[strtoupper($currency1)])
		Errors::add(Lang::string('withdraw-too-much'));
		
	if (!is_array(Errors::$errors)) {
		if (User::$info['confirm_withdrawal_email_bank'] == 'Y' && !$request_2fa && !$token1) {
			API::add('Requests','insert',array($bank_account['currency'],$fiat_amount1,false,$bank_account['account_number']));
			$query = API::send();
			Link::redirect('withdraw?notice=email');
		}
		elseif (!$request_2fa) {
			API::token($token1);
			API::add('Requests','insert',array($bank_account['currency'],$fiat_amount1,false,$bank_account['account_number']));
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
						Link::redirect('withdraw?message=withdraw-2fa-success');
					else
						Link::redirect('withdraw?message=withdraw-success');
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
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-118158391-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-118158391-1');
</script>
</head>
<link href="css/new-style.css" rel="stylesheet" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
</script>
<style type="text/css" data-styled-components="cZxgpV iJJJTg bHipRv fWgtDL kSAvah htCQOP bggzhW jHhVVr imiCeQ cUBSWS gpfewV gzmQji jMrxtq fQHoSi kwMMmE fMiZdp gkSoIH hZbtHZ jmXzPI cdGmEJ dBgvfG gJSYCj jBmRma dyDdjC hBrjIA ihpAbH gJbZHL iXnAHY dzohzs hruEfI cFMUnB iDqRrV eVuugS gUAdiw flJTLp jqhZyV cpsCBW cUpCZC ghkoKS faverQ hQXxaf reCYb THIro guwUit cdNVJh cvPMrQ gcaWCM gmOPIV dQBmZw hvidLq hBiYjT iQMzvE hyoBQr doTYNU cIjqSE gsOGkq jGNjWx dPenpn fIpMDl jvHpwe iBOmIt jYOuLK RXWtZ kJofUt gwzCiY LxVbQ jakknY icsYhc dJgHtE jSyChH kxBzvP fboOWG dhIyk kZBVvC aApUU cFkyRu fGpaje uBJje fBvETs ejcVRF gDIgIt gVLEBI xMmwt khEpQt"
       data-styled-components-is-local="true">
 
  .show-menu{
    display: block !important;
  }
  .slideIn-appear {
    opacity: 0.01;
    transform: translateX(-10px);
    transition: all 0.25s ease;
  }
  .slideIn-appear.slideIn-appear-active {
    opacity: 1;
    transform: translateX(0px);
    transition: all 0.25s ease;
  }
  .slideIn-leave {
    opacity: 1;
    transition: all 0.25s ease;
  }
  .slideIn-leave.slideIn-leave-active {
    opacity: 0.01;
    transition: all 0.25s ease;
  }
  .transitionFadeInOut-enter {
    opacity: 0.01;
  }
  .transitionFadeInOut-enter.transitionFadeInOut-enter-active {
    opacity: 1;
    transition: opacity 250ms ease-in;
  }
  .transitionFadeInOut-leave {
    opacity: 1;
  }
  .transitionFadeInOut-leave.transitionFadeInOut-leave-active {
    opacity: 0.01;
    transition: opacity 250ms ease-in;
  }
  .transitionFadeInOut-appear {
    opacity: 0.01;
  }
  .transitionFadeInOut-appear.transitionFadeInOut-appear-active {
    opacity: 1;
    transition: opacity 350ms ease-in;
  }
  {
    /* apply a natural box layout model to all elements, but allowing components to change */
  }
  html {
    box-sizing: border-box;
  }
  *,
  *:before,
  *:after {
    box-sizing: inherit;
  }
  html,
  body {
    height: 100%;
  }
  body {
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 400;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
  }
  h1,
  h2,
  h3,
  h4,
  h5,
  h6 {
    font-weight: 500;
    color: #0067C8;
  }
  a,
  *[role=button] {
    text-decoration: none;
    cursor: pointer;
    color: #0067C8;
  }
  button {
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
  }
  strong {
    font-weight: 600;
  }
  label,
  input {
    cursor: inherit;
  }
  input::-webkit-input-placeholder,
  textarea::-webkit-input-placeholder {
    color: #9BA6B2;
  }
  .Widget__widgetContainer___2LMdu {
    flex: 1 50%;
  }
  .Widget__container___3cea1 {
    height: 440px;
    margin-bottom: 20px;
    border-radius: 2px;
    border-width: 1px;
    border-style: solid;
    border-color: #dae1e9;
    background-color: #FFFFFF;
    box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.05);
  }
  /* Disable because BlinkMacSystemFont */
  .SVG__xsmall___2nDpL svg {
    width: 13px;
    height: 13px;
  }
  .SVG__small___3lVpf svg {
    width: 18px;
    height: 18px;
  }
  .SVG__medium___33i7K svg {
    width: 22px;
    height: 22px;
  }
  .SVG__large___9aBDB svg {
    width: 42px;
    height: 42px;
  }
  .SVG__xlarge___3TdeE svg {
    width: 60px;
    height: 60px;
  }
  .SVG__brandBlue___2G7K9 * {
    fill: #0067C8;
  }
  .SVG__blue___2E3-9 * {
    fill: #b0bfd1;
  }
  .SVG__green___1d-r7 * {
    fill: #61CA00;
  }
  .SVG__white___3NNm1 * {
    fill: #FFFFFF;
  }
  .SVG__yellow___1RENs * {
    fill: #F8B700;
  }
  .SVG__btn___1A2Zb {
    margin-right: 8px;
  }
  .SVG__big___3bkxU {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 15px;
  }
  .SVG__big___3bkxU svg {
    width: 28px;
    height: 28px;
  }
  .SVG__center___2IaZ3 {
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .SVG__inline___jZHEo svg {
    width: 12px;
    height: 12px;
  }
  .SVG__inline___jZHEo * {
    fill: #99bbeb;
    transition: fill 0.25s ease;
  }
  /* stylelint-disable selector-pseudo-class-no-unknown */
  .QuickstartIcon__flex___2PxUq {
    display: flex;
    flex: 1;
  }
  .QuickstartIcon__divider___-lYOc {
    display: flex;
    flex: 1;
    width: 0;
    border-right: 2px solid #F8B700;
  }
  .QuickstartIcon__divider___-lYOc.QuickstartIcon__completed___1K8A5 {
    border-right: 2px solid #0067C8;
  }
  .QuickstartIcon__step___2tOi- {
    border-radius: 50%;
    border-width: 2px;
    border-style: solid;
    border-color: #F8B700;
  }
  .QuickstartIcon__step___2tOi-.QuickstartIcon__completed___1K8A5 {
    border-color: #0067C8;
  }
  .QuickstartIcon__medium___vFCJq {
    width: 50px;
    height: 50px;
  }
  .QuickstartIcon__small___34mOP {
    width: 40px;
    height: 40px;
  }
  /* Disable because BlinkMacSystemFont */
  .RecentTransactionsWidget__txWrapper___Q9-rB {
    min-height: 0;
  }
  .RecentTransactionsWidget__adImage___3oE-m {
    width: 140px;
    height: 80px;
    margin-bottom: 20px;
    background-image: url(https://assets.coinbase.com/deploys/2018-01-02-191103_506953c9c72eca5c9cc47d6dff5080df52841118/38116c02bbf54193c8f8ce085bf939e2.png);
    background-size: cover;
    background-repeat: no-repeat;
  }
  .RecentTransactionsWidget__adTitle___1MoJA,
  .RecentTransactionsWidget__adCopy___c5ub6 {
    text-align: center;
  }
  .RecentTransactionsWidget__adTitle___1MoJA {
    margin-bottom: 15px;
    font-size: 26px;
    font-weight: 500;
    color: #5C6878;
  }
  .RecentTransactionsWidget__adCopy___c5ub6 {
    max-width: 350px;
    margin-bottom: 20px;
    color: #5C6878;
  }
  .RecentTransactionsWidget__adButton___-6Xzl {
    width: 120px;
  }
  /* Disable because BlinkMacSystemFont */
  .SelectButton__buttonContainer___vAWIh {
    position: relative;
    width: 130px;
    height: 70px;
    border: 1px solid #dae1e9;
    border-radius: 2px;
    font-size: 16px;
    font-weight: 500;
    color: #5C6878;
    background-color: #fff;
    cursor: pointer;
    transition: border ease 0.25s;
  }
  .SelectButton__buttonContainer___vAWIh+.SelectButton__buttonContainer___vAWIh {
    margin-left: 14px;
  }
  .SelectButton__buttonContainer___vAWIh:hover {
    border-color: #b9c7d7;
    transition: border ease 0.25s;
  }
  /* stylelint-enable */
  .SelectButton__icon___1F5Kw {
    height: 22px;
    margin-bottom: 5px;
  }
  .SelectButton__compact___3cZs9 {
    width: 50px;
    height: 25px;
  }
  .SelectButton__large___3nWXt {
    height: 90px;
  }
  .SelectButton__selected___B9WSh {
    border: 1px solid #4BAD02;
  }
  .SelectButton__selected___B9WSh:hover {
    border-color: #4BAD02;
  }
  .SelectButton__disabled___1CYek {
    opacity: 0.5;
    filter: grayscale(100%);
  }
  .SelectButton__label___1EUAx {
    white-space: nowrap;
    overflow: hidden;
    width: 100%;
    padding: 0 10px;
    font-size: 14px;
    text-overflow: ellipsis;
    text-align: center;
  }
  .SelectButton__details___2LTRf {
    font-size: 12px;
    text-align: center;
    color: #b0bfd1;
  }
  .SelectButton__checkmark___1J0tX {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 16px;
    height: 16px;
    border: 1px solid #4BAD02;
    border-radius: 50%;
    color: #FFFFFF;
    background-color: #61CA00;
    box-shadow: 0 0 0 3px #FFFFFF;
  }
  .SelectButton__checkIcon___3wkZz {
    margin: -4px 0 0 1px;
  }
  /* Using this magic to change svg hover color */
  /* stylelint-disable selector-no-universal, max-nesting-depth */
  .SelectButton__addNew___2KKCm {
    border-style: dashed;
  }
  .SelectButton__addNew___2KKCm * {
    fill: #b0bfd1;
    transition: fill 0.25s ease;
  }
  .SelectButton__addNew___2KKCm:hover * {
    fill: #90a5be;
    transition: fill 0.25s ease;
  }
  .Progress__container___2K33G {
    padding: 5px 0;
  }
  .Progress__progressBar___2BgLq {
    overflow: hidden;
    height: 8px;
    border-radius: 4px;
    background-color: #dae1e9;
  }
  /* Stylelint doesn't like [value] */
  /* stylelint-disable selector-no-attribute */
  .Progress__progressBar___2BgLq[value] {
    /* Reset the default appearance */
    -webkit-appearance: none;
    appearance: none;
    display: block;
    width: 100%;
    overflow: hidden;
    border: 0;
  }
  .Progress__progressBar___2BgLq::-webkit-progress-bar {
    background-color: #F4F7FA;
    box-shadow: inset 0px 0px 1px #b9c7d7;
  }
  .Progress__progressBar___2BgLq::-webkit-progress-value {
    background-color: currentColor;
    transition: all ease 0.25s;
  }
  .Progress__progressBar___2BgLq::-moz-progress-bar {
    background-color: currentColor;
    transition: all ease 0.25s;
  }
  .ConfirmWidget__zigzag___FkjQ9 {
    position: relative;
  }
  .ConfirmWidget__zigzag___FkjQ9:before {
    top: 0;
    background-position: left top;
    background: linear-gradient(-135deg, #dae1e9 3px, transparent 0), linear-gradient(135deg, #dae1e9 3px, transparent 0);
    content: '';
    position: absolute;
    left: 0px;
    display: block;
    width: 100%;
    height: 6px;
    background-repeat: repeat-x;
    background-size: 6px 6px;
  }
  .ConfirmWidget__zigzag___FkjQ9 {
    position: relative;
  }
  .ConfirmWidget__zigzag___FkjQ9:after {
    bottom: 0;
    background-position: left bottom;
    background: linear-gradient(-45deg, #F4F7FA 3px, transparent 0), linear-gradient(45deg, #F4F7FA 3px, transparent 0);
    content: '';
    position: absolute;
    left: 0px;
    display: block;
    width: 100%;
    height: 6px;
    background-repeat: repeat-x;
    background-size: 6px 6px;
  }
  .ConfirmWidget__zigzag___FkjQ9:after {
    bottom: -1px;
  }
  .ConfirmWidget__zigzagInner___23ncG {
    box-shadow: inset -1px 0 0 #e0e6ed, inset 1px 0 0 #e0e6ed;
  }
  .ConfirmWidget__zigzagInner___23ncG {
    position: relative;
  }
  .ConfirmWidget__zigzagInner___23ncG:before {
    top: 0;
    background-position: left top;
    background: linear-gradient(-135deg, #F4F7FA 3px, transparent 0), linear-gradient(135deg, #F4F7FA 3px, transparent 0);
    content: '';
    position: absolute;
    left: 0px;
    display: block;
    width: 100%;
    height: 6px;
    background-repeat: repeat-x;
    background-size: 6px 6px;
  }
  .ConfirmWidget__zigzagInner___23ncG {
    position: relative;
  }
  .ConfirmWidget__zigzagInner___23ncG:after {
    bottom: 0;
    background-position: left bottom;
    background: linear-gradient(-45deg, #dae1e9 3px, transparent 0), linear-gradient(45deg, #dae1e9 3px, transparent 0);
    content: '';
    position: absolute;
    left: 0px;
    display: block;
    width: 100%;
    height: 6px;
    background-repeat: repeat-x;
    background-size: 6px 6px;
  }
  .ConfirmWidget__zigzagInner___23ncG:before {
    top: -1px;
  }
  /* Disable because BlinkMacSystemFont */
  .Message__container___2W3uS {
    width: 100%;
    padding: 15px 20px;
    border-width: 0px;
    font-weight: 500;
    font-size: 15px;
    color: #FFFFFF;
    transition: all 0.1s ease;
    /* Required to match more freely */
    /* stylelint-disable */
    /* stylelint-enable */
  }
  .Message__container___2W3uS a,
  .Message__container___2W3uS *[role=button] {
    color: #FFFFFF;
  }
  .Message__rounded___2XXe9 {
    border-radius: 2px;
    border-width: 1px;
    border-style: solid;
  }
  /** Themes */
  .Message__success___2sh3F {
    border-color: #4BAD02;
    background-color: #61CA00;
  }
  .Message__info___3oRjp {
    border-color: #2E7BC4;
    background-color: #3C90DF;
  }
  .Message__warning___CPEMI {
    border-color: #E6A314;
    background-color: #F8B700;
  }
  .Message__error___3VU1a {
    border-color: #E82F2F;
    background-color: #FF4949;
  }
  .Dropdown__container___1kJL5 {
    position: relative;
    font-weight: 500;
    color: #5C6878;
  }
  .DropdownSeparator__container___-YUnQ {
    border-top: 1px solid #dae1e9;
  }
  /* Disable because BlinkMacSystemFont */
  .SubNavigationItem__linkContainer___2yrv7 {
    display: flex;
    flex: 1;
    align-items: center;
    justify-content: center;
    height: 60px;
    border-bottom: 1px solid #dae1e9;
    font-size: 16px;
    font-weight: 500;
    transition: border ease 0.25s;
  }
  @media (min-width: 768px) {
    .SubNavigationItem__linkContainer___2yrv7 {
      flex: initial;
    }
  }
  .SubNavigationItem__linkContainer___2yrv7+.SubNavigationItem__linkContainer___2yrv7 {
    margin-left: 10px;
  }
  .SubNavigationItem__linkContainer___2yrv7 .SubNavigationItem__link___3gsEK {
    display: inline;
    width: 100%;
    padding: 14px;
    text-align: center;
    color: #9BA6B2;
    transition: color ease 0.25s;
    /* Breakpoints should be located within class */
    /* stylelint-disable max-nesting-depth */
    /* stylelint-enable */
  }
  @media (min-width: 768px) {
    .SubNavigationItem__linkContainer___2yrv7 .SubNavigationItem__link___3gsEK {
      width: auto;
    }
  }
  .SubNavigationItem__active___p0wGA {
    border-bottom: 1px solid #0067C8;
  }
  .SubNavigationItem__active___p0wGA .SubNavigationItem__link___3gsEK {
    color: #0067C8;
  }
  .SubNavigationItem__active___p0wGA:hover {
    border-bottom: 1px solid #0067C8;
  }
  .SubNavigationItem__linkContainer___2yrv7 .SubNavigationItem__link___3gsEK:hover {
    color: #0067C8;
    transition: color ease 0.25s;
  }
  /*# sourceMappingURL=styles.717f77822d45e5bf78ab.css.map*/
  html,
  body {
    height: 100%;
    background-color: #F4F7FA!important
  }
  body {
    margin: 0;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 400;
    line-height: normal;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility
  }
  a {
    text-decoration: none;
    cursor: pointer;
    color: #0067C8
  }
  h1,
  h2,
  h3,
  h4,
  h5,
  h6 {
    font-weight: 500;
    color: #0067C8
  }
  .shell {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    background-color: #F7F7F7
  }
  .shell .header {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    height: 70px;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    background-color: #0067C8
  }
  .shell .content {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-flex: 1;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1
  }
  body:not(.static_application) {
    min-width: 1250px
  }
  a:hover,
  a:focus {
    text-decoration: none
  }
  .nav>li>a:focus,
  .nav>li>a:hover {
    background-color: inherit;
    -webkit-box-shadow: none;
    box-shadow: none
  }
  .nav>li>a {
    padding: 21px 14px;
    font-size: 16px;
    font-weight: 500;
    color: #C0C0C0;
    border-bottom: 1px solid transparent;
    -webkit-box-shadow: none;
    box-shadow: none
  }
  .nav-tabs>.active>a {
    color: #0067C8;
    -webkit-box-shadow: none;
    box-shadow: none;
    border-bottom: 1px solid #0067C8;
    background: none
  }
  .nav-tabs>.active>a:hover,
  .nav-tabs>.active>a:focus {
    cursor: pointer;
    box-shadow: none;
    -webkit-box-shadow: none;
    border-bottom: 1px solid #0067C8
  }
  legend {
    position: relative
  }
  legend.pull-right {
    position: absolute;
    right: 0
  }
  button {
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif
  }
  .nav-tabs li {
    margin: 0 0 -1px 0
  }
  .nav-tabs {
    margin: 0 -25px 25px!important;
    padding: 0 25px!important
  }
  .row {
    margin: 0 25px!important
  }
  #root {
    height: 100%
  }
  .alert-full {
    margin: 0;
    padding: 25px 0
  }
  .header,
  .header--confirm {
    font-size: 18px;
    color: #0067C8;
    font-weight: 500;
    padding-bottom: 8px;
    border-bottom: 1px solid #E4E6E8;
    margin-bottom: 24px
  }
  .narrow-content {
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-flex: 1;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    width: 500px;
    border-right: 1px solid #E4E6E8
  }
  #main>.main-header,
  #vault .main-header {
    margin: 0 -25px
  }
  .manage-accounts {
    margin: 0 -25px
  }
  #main.accounts #account_changes {
    margin: 0 -25px
  }
  #main.accounts .snd-header {
    margin: 0 -25px
  }
  .settings.show .span4,
  .settings.show .span5,
  .settings.show .span9,
  .settings.show form .row {
    margin-left: 0!important;
    margin-right: 0!important
  }
  .Button__primary___zYyzg {
    border: 1px solid #2E7BC4;
    background-color: #3C90DF
  }
  .Button__container___1Nus9.Button__small___nAPfO {
    padding: 5px 10px;
    border-radius: 2px;
    font-size: 12px;
    font-weight: 600
  }
  .Button__container___1Nus9 {
    position: relative;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    padding: 10px 15px;
    border-radius: 2px;
    font-size: 14px;
    font-weight: 600;
    color: #FFFFFF;
    cursor: pointer;
    -webkit-transition: all ease 0.25s;
    transition: all ease 0.25s
  }
  .hmKuRU {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-box-flex: 1;
    flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    -webkit-flex: 1 1 auto
  }
  .hoxnhy {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-box-flex: 1;
    flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    -webkit-flex: 1 1 auto;
    flex-direction: column;
    -webkit-box-direction: normal;
    -webkit-box-orient: vertical;
    -ms-flex-direction: column;
    -webkit-flex-direction: column
  }
  .djliRF {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    justify-content: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    -webkit-justify-content: center;
    align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center
  }
  .FnsEJ {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    justify-content: space-between;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    -webkit-justify-content: space-between;
    align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center
  }
  .dnnlxI {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center
  }
  .lixsPe {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    flex-direction: column;
    -webkit-box-direction: normal;
    -webkit-box-orient: vertical;
    -ms-flex-direction: column;
    -webkit-flex-direction: column
  }
  .eHKFGW {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center;
    flex-direction: column;
    -webkit-box-direction: normal;
    -webkit-box-orient: vertical;
    -ms-flex-direction: column;
    -webkit-flex-direction: column
  }
  .gTHKWe {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex
  }
  .bTBDKY {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    justify-content: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    -webkit-justify-content: center
  }
  .klYNQL {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-box-flex: 1;
    flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    -webkit-flex: 1 1 auto;
    align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center;
    flex-direction: column;
    -webkit-box-direction: normal;
    -webkit-box-orient: vertical;
    -ms-flex-direction: column;
    -webkit-flex-direction: column
  }
  .Avatar__avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #FFFFFF
  }
  .jWCjTE {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #FFFFFF
  }
  .DropdownMenu__container {
    position: absolute;
    z-index: 9;
    display: none;
    top: 40px;
    right: -16px;
    font-size: 16px
  }
  .DropdownMenu__container .Avatar__avatar {
    width: 60px;
    height: 60px
  }
  .bgbBSk {
    z-index: 2;
    min-width: 260px;
    border-radius: 4px;
    border: 1px solid #DAE1E9;
    -webkit-box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    background-color: #FFFFFF
  }
  .gBGaol {
    padding: 20px 50px;
    font-weight: 500;
    color: #4E5C6E
  }
  .gBGaol img {
    margin-bottom: 12px
  }
  .elKcFV {
    margin-bottom: 2px;
    font-size: 18px
  }
  .cSpTnp {
    font-size: 14px;
    color: #9BA6B2
  }
  .ngzyr {
    border-top: 1px solid #DAE1E9
  }
  .jCDdQR {
    align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center;
    height: 48px;
    padding: 0px 16px;
    font-weight: 500;
    cursor: pointer
  }
  .jCDdQR:hover {
    background-color: #F9FBFC
  }
  .jCDdQR:last-child:hover {
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px
  }
  .fMrRoP {
    -webkit-box-flex: 1;
    flex: 1;
    -ms-flex: 1;
    -webkit-flex: 1;
    color: #4E5C6E
  }
  .iGHQvA {
    padding: 4px 8px;
    border: 1px solid #00AA6D;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    color: #FFFFFF;
    background-color: #00C57F;
    background-image: url("data:image/svg+xml,%3Csvg width='40' height='8' viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.562 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.438 14 100 14v-2c-10.271 0-15.362-1.222-24.629-4.928C65.888 3.278 60.562 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.223 10.84 8.163 12 0 12v2z' fill='%2300aa6d' fill-opacity='0.43' fill-rule='evenodd'/%3E%3C/svg%3E")
  }
  .icIrgI {
    position: fixed;
    z-index: 1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: none
  }
  .bbYkHy {
    height: 70px;
    color: #FFFFFF;
    background-color: #0667D0
  }
  .jZazd {
    width: 1180px
  }
  .iVdAJG {
    width: 96px;
    height: 22px;
    fill: #FFFFFF;
    cursor: pointer
  }
  .flsCtu {
    margin-left: 8px;
    margin-right: 6px;
    font-size: 14px;
    font-weight: 500;
    color: #FFFFFF
  }
  .Header__userMenu_wrapper {
    position: relative
  }
  .bDZnTJ {
    cursor: pointer
  }
  .fhhenV {
    width: 10px;
    height: 6px;
    margin-top: 2px;
    fill: #FFFFFF;
    opacity: 0.5
  }
  .jXmhhY {
    height: 64px;
    border-bottom: 1px solid #DAE1E9;
    background-color: #FFFFFF
  }
  .kdQNpM {
    flex-direction: row;
    -webkit-box-direction: normal;
    -webkit-box-orient: horizontal;
    -ms-flex-direction: row;
    -webkit-flex-direction: row;
    width: 1180px
  }
  .dHMsll:last-child {
    margin-bottom: 0
  }
  .dHMsll:not(:first-child) {
    margin-left: 30px
  }
  .rsdjt {
    position: relative;
    align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center;
    cursor: pointer;
    color: #7D95B6
  }
  .rsdjt:hover:after {
    border-bottom-color: #7D95B6
  }
  .rsdjt:after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0px;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid transparent
  }
  .jAXUQz {
    position: relative;
    align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    -webkit-align-items: center;
    cursor: pointer;
    color: #0667D0
  }
  .jAXUQz:hover:after {
    border-bottom-color: #0667D0
  }
  .jAXUQz:after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0px;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #0667D0
  }
  .bgdPDV {
    font-size: 16px;
    font-weight: 500
  }
  .hvLrBO {
    height: 64px;
    border-top: 1px solid #DAE1E9;
    background-color: #FFFFFF
  }
  .gACRnh {
    width: 1180px;
    font-size: 14px;
    color: #9BA6B2
  }
  .eQcTdK {
    margin-right: 15px
  }
  .hzaXlf {
    color: #4E5C6E
  }
  .hzaXlf:not(:first-child) {
    margin-left: 15px
  }
  .fLgxBf {
    background-color: #F4F7FA
  }
  .lmWelJ {
    -webkit-box-flex: 0;
    flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    -webkit-flex: 0 1 auto;
    width: 1180px;
    margin: 0px;
    padding: 25px 0
  }
  .KuuEs {
    min-height: 100vh
  }
  .jdmxYg {
    display: -webkit-box;
    display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    flex-direction: column;
    -webkit-box-direction: normal;
    -webkit-box-orient: vertical;
    -ms-flex-direction: column;
    -webkit-flex-direction: column;
    -webkit-box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    background-color: #FFFFFF;
    border-radius: 4px;
    border: 1px solid #DAE1E9
  }
  .joarYq {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor
  }
  .CSsIs {
    width: 96px;
    height: 22px;
    margin-top: 2px;
    fill: #FFFFFF;
    cursor: pointer
  }
  .Backdrop__container {
    display: none;
    position: absolute;
    top: 70px;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(26, 54, 80, 0.1)
  }
  /* sc-component-id: Flex__Flex-fVJVYW */
  .iJJJTg {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .bHipRv {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .kSAvah {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .bggzhW {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .gpfewV {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    cursor: pointer;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .gkSoIH {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .jmXzPI {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .hBrjIA {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    cursor: pointer;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .hruEfI {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .iDqRrV {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .cpsCBW {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .ghkoKS {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .hQXxaf {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .reCYb {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .gsOGkq {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .jGNjWx {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    cursor: pointer;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .dPenpn {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .fIpMDl {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-align-items: flex-end;
    -webkit-box-align: flex-end;
    -ms-flex-align: flex-end;
    align-items: flex-end;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .jvHpwe {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: end;
    -webkit-justify-content: flex-end;
    -ms-flex-pack: end;
    justify-content: flex-end;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .iBOmIt {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .jYOuLK {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .RXWtZ {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    cursor: pointer;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .kJofUt {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-box-pack: space-around;
    -webkit-justify-content: space-around;
    -ms-flex-pack: space-around;
    justify-content: space-around;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .gwzCiY {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-align-items: flex-start;
    -webkit-box-align: flex-start;
    -ms-flex-align: flex-start;
    align-items: flex-start;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .LxVbQ {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-align-items: flex-end;
    -webkit-box-align: flex-end;
    -ms-flex-align: flex-end;
    align-items: flex-end;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .icsYhc {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    cursor: pointer;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .dJgHtE {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .jSyChH {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    cursor: pointer;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .kxBzvP {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .fboOWG {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    cursor: pointer;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  .aApUU {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .cFkyRu {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .fGpaje {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: flex-end;
    -webkit-box-align: flex-end;
    -ms-flex-align: flex-end;
    align-items: flex-end;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  .xMmwt {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
  }
  /* sc-component-id: Panel__Container-hCUKEb */
  .gmOPIV {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    background-color: #FFFFFF;
    border-radius: 4px;
    border: 1px solid #DAE1E9;
  }
  .ejcVRF {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    background-color: #FFFFFF;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    height: 100%;
    max-height: 100%;
    overflow: hidden;
    border-radius: 4px;
    border: 1px solid #DAE1E9;
  }
  /* sc-component-id: Alert__ActionWrapper-gnVoNQ */
  .hyoBQr {
    -webkit-flex-shrink: 0;
    -ms-flex-shrink: 0;
    flex-shrink: 0;
  }
  /* sc-component-id: Alert__ActionButton-dzhBct */
  .doTYNU {
    padding: 8px 18px;
    font-size: 14px;
    border: 1px solid #FFFFFF;
    border-radius: 4px;
    -webkit-transition: all 0.1s ease;
    transition: all 0.1s ease;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 600;
    cursor: pointer;
    color: #FFFFFF;
    background: #3C90DF;
  }
  .doTYNU:not(:first-child) {
    margin-left: 16px;
  }
  .cIjqSE {
    padding: 8px 18px;
    font-size: 14px;
    border: 1px solid #FFFFFF;
    border-radius: 4px;
    -webkit-transition: all 0.1s ease;
    transition: all 0.1s ease;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 600;
    cursor: pointer;
    color: #3C90DF;
    background: #FFFFFF;
  }
  .cIjqSE:not(:first-child) {
    margin-left: 16px;
  }
  .fBvETs {
    padding: 8px 18px;
    font-size: 14px;
    border: 1px solid #FFFFFF;
    border-radius: 4px;
    -webkit-transition: all 0.1s ease;
    transition: all 0.1s ease;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 600;
    cursor: pointer;
    color: #F8B700;
    background: #FFFFFF;
  }
  .fBvETs:not(:first-child) {
    margin-left: 16px;
  }
  /* sc-component-id: Alert__AlertContainer-cJvXpK */
  .gcaWCM {
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    width: 100%;
    height: auto;
    min-height: 80px;
    padding: 20px;
    background-color: #3C90DF;
    border: 1px solid #2E7BC4;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    color: #FFFFFF;
    text-align: initial;
    border-radius: 4px;
  }
  .uBJje {
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    width: 100%;
    height: auto;
    min-height: 80px;
    padding: 20px;
    background-color: #F8B700;
    border: 1px solid #E6A314;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    color: #FFFFFF;
    text-align: initial;
    border-radius: 4px;
  }
  /* sc-component-id: Alert__ContentContainer-hvAxWh */
  .hvidLq {
    padding-right: 18px;
  }
  /* sc-component-id: Alert__IconWrapper-dDLZrK */
  .dQBmZw {
    margin-right: 18px;
  }
  /* sc-component-id: Alert__Title-bybOAf */
  .hBiYjT {
    margin-bottom: 4px;
    font-size: 18px;
    font-weight: 600;
    color: #FFFFFF;
  }
  /* sc-component-id: Alert__Subtitle-czrfwO */
  .iQMzvE {
    font-size: 14px;
    font-weight: 500;
    color: #FFFFFF;
  }
  /* sc-component-id: TopLevelAlerts__StyledAlert-gRSiLN */
  .cvPMrQ {
    margin-bottom: 25px;
  }
  /* sc-component-id: sc-keyframes-cZxgpV */
  @-webkit-keyframes cZxgpV {
    100% {
      -webkit-transform: rotate(360deg);
      -ms-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }
  @keyframes cZxgpV {
    100% {
      -webkit-transform: rotate(360deg);
      -ms-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }
  /* sc-component-id: Spinner__Container-hhKtJv */
  .faverQ {
    width: 45px;
    height: 45px;
    border-radius: 100%;
    border: 3px solid rgba(6, 103, 208, 0.05);
    border-top-color: #0667D0;
    -webkit-animation: cZxgpV 1s infinite linear;
    animation: cZxgpV 1s infinite linear;
  }
  /* sc-component-id: DelayedLoading__TransitionContent-dqjKlj */
  .cUpCZC {
    -webkit-transition: opacity 150ms ease-in-out;
    transition: opacity 150ms ease-in-out;
    opacity: 0;
  }
  .jakknY {
    -webkit-transition: opacity 150ms ease-in-out;
    transition: opacity 150ms ease-in-out;
    opacity: 1;
  }
  /* sc-component-id: Avatar__AvatarImage-bFLlyY */
  .gzmQji {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #FFFFFF;
  }
  .cdGmEJ {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #FFFFFF;
  }
  /* sc-component-id: DropdownMenu__Wrapper-ieiZya */
  .kwMMmE {
    position: absolute;
    display: none;
    top: 40px;
    right: -16px;
    z-index: 999;
  }
  /* sc-component-id: DropdownMenu__Dropdown-kuxaaY */
  .fMiZdp {
    z-index: 2;
    min-width: 260px;
    border-radius: 4px;
    border: 1px solid #DAE1E9;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    background-color: #FFFFFF;
  }
  /* sc-component-id: DropdownMenu__Header-kQtcOQ */
  .hZbtHZ {
    padding: 20px 50px;
    font-weight: 500;
    color: #4E5C6E;
  }
  .hZbtHZ img {
    margin-bottom: 12px;
  }
  /* sc-component-id: DropdownMenu__Name-hpHChW */
  .dBgvfG {
    margin-bottom: 2px;
    font-size: 18px;
  }
  /* sc-component-id: DropdownMenu__Email-cxInkz */
  .gJSYCj {
    font-size: 14px;
    color: #9BA6B2;
  }
  /* sc-component-id: DropdownMenu__Separator-cnqUyC */
  .jBmRma {
    border-top: 1px solid #DAE1E9;
  }
  /* sc-component-id: DropdownMenu__DropdownLink-kJecXv */
  .dyDdjC {
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    height: 48px;
    padding: 0px 16px;
    font-weight: 500;
    cursor: pointer;
  }
  .dyDdjC:hover {
    background-color: #F9FBFC;
  }
  .dyDdjC:last-child:hover {
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
  }
  /* sc-component-id: DropdownMenu__Title-bLKAie */
  .ihpAbH {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    color: #4E5C6E;
  }
  /* sc-component-id: DropdownMenu__PromoLabel-bqVhkQ */
  .gJbZHL {
    padding: 4px 8px;
    border: 1px solid #00AA6D;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    color: #FFFFFF;
    background-color: #00C57F;
    background-image: url("data:image/svg+xml,%3Csvg width='40' height='8' viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.562 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.438 14 100 14v-2c-10.271 0-15.362-1.222-24.629-4.928C65.888 3.278 60.562 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.223 10.84 8.163 12 0 12v2z' fill='%2300aa6d' fill-opacity='0.43' fill-rule='evenodd'/%3E%3C/svg%3E");
  }
  /* sc-component-id: DropdownMenu__Overlay-kLnxWE */
  .iXnAHY {
    position: fixed;
    z-index: 1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: none;
  }
  /* sc-component-id: Header__Wrapper-cwuouQ */
  .fWgtDL {
    color: #FFFFFF;
    background-color: #2f3340;
  }
  /* sc-component-id: Header__Content-dOLsDz */
  .htCQOP {
    width: 1180px;
  }
  /* sc-component-id: Header__Logo-egROsK */
  .jHhVVr {
    width: 96px;
    height: 22px;
    margin-top: 2px;
    fill: #FFFFFF;
    cursor: pointer;
  }
  /* sc-component-id: Header__Username-bPaLgO */
  .jMrxtq {
    margin-left: 8px;
    margin-right: 6px;
    font-size: 14px;
    font-weight: 500;
    color: #FFFFFF;
  }
  /* sc-component-id: Header__Dropdown-kRsXac */
  .imiCeQ {
    position: relative;
  }
  /* sc-component-id: Header__DropdownButton-dItiAm */
  .cUBSWS {
    cursor: pointer;
  }
  /* sc-component-id: Header__DropdownArrow-cjWXoe */
  .fQHoSi {
    width: 10px;
    height: 6px;
    margin-top: 2px;
    fill: #FFFFFF;
    opacity: 0.5;
  }
  /* sc-component-id: Navbar__DesktopWrapper-jiGyXa */
  .dzohzs {
    -webkit-flex: 0 0 64px;
    -ms-flex: 0 0 64px;
    flex: 0 0 64px;
    border-bottom: 1px solid #DAE1E9;
  }
  /* sc-component-id: Navbar__Content-cgqezH */
  .cFMUnB {
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    width: 1180px;
  }
  /* sc-component-id: Navbar__LinkContainer-jXaDVl */
  .eVuugS:last-child {
    margin-bottom: 0;
  }
  .eVuugS:not(:first-child) {
    margin-left: 30px;
  }
  /* sc-component-id: Navbar__LinkContent-fFVkWH */
  .gUAdiw {
    position: relative;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    cursor: pointer;
    color: #0667D0;
  }
  .gUAdiw:hover:after {
    border-bottom-color: #0667D0;
  }
  .gUAdiw:after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0px;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #0667D0;
  }
  .jqhZyV {
    position: relative;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    cursor: pointer;
    color: #7D95B6;
  }
  .jqhZyV:hover:after {
    border-bottom-color: #7D95B6;
  }
  .jqhZyV:after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0px;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid transparent;
  }
  /* sc-component-id: Navbar__Title-hJulrY */
  .flJTLp {
    font-weight: 500;
  }
  /* sc-component-id: Backdrop__LayoutBackdrop-eRYGPr */
  .cdNVJh {
    position: absolute;
    z-index: -1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(26, 54, 80, 0.1);
    opacity: 0;
    -webkit-transition: opacity 0.5s ease, z-index 0.5s ease;
    transition: opacity 0.5s ease, z-index 0.5s ease;
  }
  /* sc-component-id: Select__SelectWrapper-fXiYlv */
  .THIro {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    padding: 4px;
    padding-left: 8px;
    padding-right: 22px;
    border: 1px solid #DAE1E9;
    border-radius: 4px;
    font-weight: 500;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-size: 14px;
    color: #4E5C6E;
    background-color: #FFFFFF;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    outline: none;
    cursor: pointer;
    background: #FFFFFF url(https://assets.coinbase.com/deploys/2018-01-02-191103_506953c9c72eca5c9cc47d6dff5080df52841118/eb61c584b44fae6c4950959043e87f89.png) no-repeat;
    background-size: 8px;
    background-position: right 8px center;
    -webkit-transition: all ease 0.25s;
    transition: all ease 0.25s;
  }
  .THIro:disabled {
    color: #DAE1E9;
    background-color: #F9FBFC;
  }
  /* sc-component-id: Button__Container-hQftQV */
  .guwUit {
    position: relative;
    width: auto;
    margin: 0px;
    border-radius: 4px;
    font-weight: 600;
    color: #FFFFFF;
    cursor: pointer;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #2E7BC4;
    background-color: #3C90DF;
  }
  .guwUit:focus {
    outline: none;
  }
  .guwUit:hover {
    background-color: #2E7BC4;
    -webkit-transition: background-color ease 0.25s;
    transition: background-color ease 0.25s;
  }
  .guwUit:active {
    border: 1px solid #3C90DF;
    background-color: #3C90DF;
  }
  .dhIyk {
    position: relative;
    width: auto;
    margin: 0px;
    border-radius: 4px;
    font-weight: 600;
    color: #FFFFFF;
    cursor: default;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #DAE1E9;
    color: #7D95B6;
    background-color: white;
    color: #DAE1E9;
    background-color: #F9FBFC;
  }
  .dhIyk:focus {
    outline: none;
  }
  .dhIyk:hover {
    border: 1px solid #9BA6B2;
    color: #4E5C6E;
  }
  .dhIyk:hover:before {
    position: absolute;
    z-index: 1;
    width: 1px;
    top: -1px;
    bottom: -1px;
    left: -1px;
    content: '';
    background-color: #9BA6B2;
  }
  .dhIyk:first-child:before {
    display: none;
  }
  .dhIyk:last-child:after {
    display: none;
  }
  .dhIyk:hover {
    border: 1px solid #DAE1E9;
    color: #DAE1E9;
  }
  .dhIyk:before,
  .dhIyk:after {
    display: none;
  }
  .kZBVvC {
    position: relative;
    width: auto;
    margin: 0px;
    border-radius: 4px;
    font-weight: 600;
    color: #FFFFFF;
    cursor: default;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    padding: 20px 35px;
    font-size: 18px;
    border: 1px solid #2E7BC4;
    background-color: #3C90DF;
    border: 1px solid #2E7BC4;
    background-color: #3C90DF;
    width: 100%;
  }
  .kZBVvC:focus {
    outline: none;
  }
  .kZBVvC:hover {
    background-color: #2E7BC4;
    -webkit-transition: background-color ease 0.25s;
    transition: background-color ease 0.25s;
  }
  .kZBVvC:active {
    border: 1px solid #3C90DF;
    background-color: #3C90DF;
  }
  .kZBVvC:hover {
    border-color: #2E7BC4;
    background-color: #3C90DF;
  }
  .gDIgIt {
    position: relative;
    width: auto;
    margin: 0px;
    border-radius: 4px;
    font-weight: 600;
    color: #FFFFFF;
    cursor: default;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #DAE1E9;
    color: #9BA6B2;
    background-color: #FFFFFF;
    color: #DAE1E9;
    background-color: #F9FBFC;
  }
  .gDIgIt:focus {
    outline: none;
  }
  .gDIgIt:hover {
    color: #4E5C6E;
    border: 1px solid #9BA6B2;
  }
  .gDIgIt svg {
    fill: #DAE1E9;
  }
  .gDIgIt:hover {
    border: 1px solid #DAE1E9;
    color: #DAE1E9;
  }
  .gVLEBI {
    position: relative;
    width: auto;
    margin: 0px;
    border-radius: 4px;
    font-weight: 600;
    color: #FFFFFF;
    cursor: pointer;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #DAE1E9;
    color: #9BA6B2;
    background-color: #FFFFFF;
  }
  .gVLEBI:focus {
    outline: none;
  }
  .gVLEBI:hover {
    color: #4E5C6E;
    border: 1px solid #9BA6B2;
  }
  .khEpQt {
    position: relative;
    width: auto;
    margin: 0px;
    border-radius: 4px;
    font-weight: 600;
    color: #FFFFFF;
    cursor: pointer;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    padding: 15px 25px;
    font-size: 16px;
    border: 1px solid #2E7BC4;
    background-color: #3C90DF;
  }
  .khEpQt:focus {
    outline: none;
  }
  .khEpQt:hover {
    background-color: #2E7BC4;
    -webkit-transition: background-color ease 0.25s;
    transition: background-color ease 0.25s;
  }
  .khEpQt:active {
    border: 1px solid #3C90DF;
    background-color: #3C90DF;
  }
</style>
<style type="text/css" data-styled-components="gCVQUv WhXLX cpwUZB fWIqmZ bRMwEm emZeiu fTUMdy kmYTnN klDPGy bsspIM dVuYMi cGVJJy gSHJhw kxbyEA gkzfZl iOGmBb gkEpki hwfHDH jQqaGc uKBRe bxGiua jiMbBQ kbqVDF bNXXSQ gBskIE jdlzFZ eUEQWj bGAtDj guNrkG kigJcx iHOEuK cHdqpn kuJaHF"
       data-styled-components-is-local="true">
  /* sc-component-id: Button__Content-eaBvLU */
  .iOGmBb {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 100%;
    pointer-events: none;
  }
  /* sc-component-id: Footer__Links-jOwZdP */
  /* sc-component-id: Footer__NeedHelp-kNHURG */
  /* sc-component-id: Footer__Right-iFQtJS */
  .cGVJJy {
    -webkit-flex: 0 0 auto;
    -ms-flex: 0 0 auto;
    flex: 0 0 auto;
  }
  /* sc-component-id: Footer__Wrapper-jgZZNA */
  .kmYTnN {
    height: 64px;
    max-height: 64px;
    border-top: 1px solid #DAE1E9;
  }
  /* sc-component-id: Footer__Content-kfdTYL */
  .klDPGy {
    width: 1180px;
    max-width: 1180px;
    font-size: 14px;
    color: #9BA6B2;
  }
  /* sc-component-id: Footer__Copyright-eTGlms */
  .gSHJhw {
    margin-right: 15px;
  }
  /* sc-component-id: Footer__SelectWrapper-fEXIsO */
  .kxbyEA {
    margin-right: 15px;
  }
  /* sc-component-id: Footer__Link-hmaedR */
  .dVuYMi {
    color: #4E5C6E;
  }
  .dVuYMi:not(:first-child) {
    margin-left: 15px;
  }
  /* sc-component-id: LayoutDesktop__AppWrapper-cPGAqn */
  .WhXLX {
    min-height: 100vh;
  }
  /* sc-component-id: LayoutDesktop__Wrapper-ksSvka */
  .fWIqmZ {
    padding: 0 24px;
    background-color: #F4F7FA;
  }
  /* sc-component-id: LayoutDesktop__ContentContainer-cdKOaO */
  .cpwUZB {
    position: relative;
  }
  /* sc-component-id: LayoutDesktop__Content-flhQBc */
  .bRMwEm {
    -webkit-flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    flex: 0 1 auto;
    width: 1180px;
    overflow: hidden;
    margin: 0px;
    padding: 0;
  }
  .cHdqpn {
    -webkit-flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    flex: 0 1 auto;
    width: 1180px;
    overflow: hidden;
    margin: 0px;
    padding: 25px 0;
  }
  .kuJaHF {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    width: 1180px;
    overflow: hidden;
    margin: 0px;
    padding: 25px 0;
  }
  /* sc-component-id: Layout__Container-jkalbK */
  .gCVQUv {
    min-height: 100vh;
    background-color: #FFFFFF;
  }
  /* sc-component-id: Layout__SpinnerContainer-fSvIvz */
  .emZeiu {
    padding: 300px 0;
  }
  /* sc-component-id: Layout__LoadingSpinner-kBfATm */
  .fTUMdy {
    width: 60px;
    height: 60px;
    border-top-color: #BCD1EE;
  }
  /* sc-component-id: Heading__StyledHeading-sALAQ */
  .hwfHDH {
    margin: 0;
    font-weight: 500;
    color: #0667D0;
  }
  /* sc-component-id: BigAmount__Number-fWXHBq */
  .gBskIE {
    font-size: 48px;
    color: #4E5C6E;
  }
  /* sc-component-id: BigAmount__AmountSuper-jnVzGG */
  .jdlzFZ {
    position: relative;
    top: -13px;
    vertical-align: baseline;
    font-size: 30px;
    font-weight: 500;
  }
  /* sc-component-id: BigAmount__Direction-ovzBE */
  .eUEQWj {
    color: #61CA00;
  }
  /* sc-component-id: WidgetHeader__Wrapper-lkOFAm */
  .gkEpki {
    -webkit-flex-shrink: 0;
    -ms-flex-shrink: 0;
    flex-shrink: 0;
    height: 54px;
    padding: 0px 20px;
    border-bottom: 1px solid #DAE1E9;
  }
  /* sc-component-id: WidgetHeader__Actions-bDbtim */
  .jQqaGc {
    font-size: 14px;
    font-weight: 500;
  }
  /* sc-component-id: WidgetFooter__Wrapper-srJyb */
  .uKBRe {
    -webkit-flex-shrink: 0;
    -ms-flex-shrink: 0;
    flex-shrink: 0;
    height: 54px;
    border-top: 1px solid #DAE1E9;
    font-weight: 500;
    color: #7D95B6;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  .bxGiua {
    -webkit-flex-shrink: 0;
    -ms-flex-shrink: 0;
    flex-shrink: 0;
    height: 54px;
    border-top: 1px solid #DAE1E9;
    font-weight: 500;
    color: #7D95B6;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
    cursor: pointer;
  }
  .bxGiua:hover {
    color: #0667D0;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  .bxGiua:hover svg {
    -webkit-transform: translateX(4px);
    -ms-transform: translateX(4px);
    transform: translateX(4px);
    fill: #0667D0;
  }
  /* sc-component-id: WidgetFooter__ArrowIcon-JsoBB */
  .jiMbBQ {
    width: 5px;
    height: 10px;
    margin-top: 2px;
    margin-left: 6px;
    fill: #7D95B6;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  /* sc-component-id: PeriodToggle__Wrapper-kiZBfx */
  .kbqVDF {
    position: relative;
    text-transform: uppercase;
    font-size: 14px;
    color: #7D95B6;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
    cursor: pointer;
  }
  .kbqVDF:not(:first-child) {
    margin-left: 12px;
  }
  .kbqVDF:hover:after {
    border-bottom-color: #7D95B6;
  }
  .kbqVDF:after {
    content: '';
    position: absolute;
    bottom: -18px;
    left: 0px;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid transparent;
  }
  .bNXXSQ {
    position: relative;
    text-transform: uppercase;
    font-size: 14px;
    color: #0667D0;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
    cursor: pointer;
  }
  .bNXXSQ:not(:first-child) {
    margin-left: 12px;
  }
  .bNXXSQ:hover:after {
    border-bottom-color: #0667D0;
  }
  .bNXXSQ:after {
    content: '';
    position: absolute;
    bottom: -18px;
    left: 0px;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #0667D0;
  }
  /* sc-component-id: HoverIndicator__Line-fyUVWt */
  .guNrkG {
    stroke: #7D95B6;
    stroke-width: 1;
  }
  /* sc-component-id: HoverIndicator__Circle-cpnygq */
  .kigJcx {
    stroke: #7D95B6;
    stroke-width: 2;
    fill: white;
  }
  /* sc-component-id: HoverIndicator__Group-fMnkKY */
  .bGAtDj {
    opacity: 0;
    -webkit-transition: opacity 300ms;
    transition: opacity 300ms;
    opacity: 0;
  }
  .iHOEuK {
    opacity: 0;
    -webkit-transition: opacity 300ms;
    transition: opacity 300ms;
    opacity: 1;
  }
</style>
<style type="text/css" data-styled-components="fkKUHd gaOoIW eopEKS bBFStv gLGtWx cawfqU bggHeP gzkfOx blbMMz ZJjdO gJaRtZ bZSaVE vbPXh bpZMcw fSWgoh dRxFxx hXuKUF IDahi fSSVge kSqzOS hZFMLT fsuujI bDSDMM cgqGbT gWAHYV cZjfCo eJvkmu egBQEa bsumUB hlRAYZ RQsvY hiDTSU cXMret gxSINW kIzKjJ"
       data-styled-components-is-local="true">
  /* sc-component-id: Dataset__HiddenPath-jkEwsT */
  .egBQEa {
    visibility: hidden;
    pointer-events: none;
  }
  /* sc-component-id: Dataset__DataPath-hYITHz */
  .eJvkmu {
    stroke-width: 1.7;
    stroke: #FFB119;
    stroke-width: 1.7;
    fill: #ffecc6;
    pointer-events: none;
  }
  /* sc-component-id: Text__Font-jgIzVM */
  .ZJjdO {
    display: inline-block;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 500;
    font-size: 18px;
    color: #4E5C6E;
  }
  .gJaRtZ {
    display: inline-block;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 500;
    font-size: 16px;
    color: #4E5C6E;
  }
  .bZSaVE {
    display: inline-block;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 500;
    font-size: 16px;
    color: #9BA6B2;
  }
  .cXMret {
    display: inline-block;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 600;
    font-size: 16px;
    color: #4E5C6E;
  }
  .gxSINW {
    display: inline-block;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-weight: 500;
    font-size: 14px;
    color: #4E5C6E;
  }
  /* sc-component-id: Chart__ChartSvg-bKQeqx */
  .cZjfCo {
    height: 100%;
    width: 100%;
    overflow: hidden;
  }
  /* sc-component-id: Chart__Container-jpTXgq */
  .hZFMLT {
    position: relative;
    width: 100%;
    height: 221px;
    cursor: crosshair;
  }
  /* sc-component-id: Chart__HoverContainer-hKRbrp */
  .fsuujI {
    position: absolute;
    top: -12px;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 200px;
    opacity: 0;
    -webkit-transition: opacity 300ms;
    transition: opacity 300ms;
  }
  .cgqGbT {
    position: absolute;
    bottom: -12px;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 200px;
    opacity: 0;
    -webkit-transition: opacity 300ms;
    transition: opacity 300ms;
  }
  .RQsvY {
    position: absolute;
    top: -12px;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 200px;
    opacity: 1;
    -webkit-transition: opacity 300ms;
    transition: opacity 300ms;
  }
  .hiDTSU {
    position: absolute;
    bottom: -12px;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 200px;
    opacity: 1;
    -webkit-transition: opacity 300ms;
    transition: opacity 300ms;
  }
  /* sc-component-id: Chart__HoverContent-eqqQwo */
  .bDSDMM {
    padding: 1px 6px;
    border-radius: 5px;
    background: #7D95B6;
    font-size: 14px;
    font-weight: 500;
    color: #FFFFFF;
  }
  .gWAHYV {
    padding: 1px 6px;
    border-radius: 5px;
    background: #FFFFFF;
    border: 1px solid #7D95B6;
    font-size: 14px;
    font-weight: 500;
    color: #7D95B6;
  }
  /* sc-component-id: HorizontalAxis__Tick-buareL */
  .hlRAYZ {
    font-size: 14px;
    font-weight: 500;
    color: #7D95B6;
  }
  /* sc-component-id: PriceChart__Container-klmtfG */
  .fkKUHd {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  /* sc-component-id: PriceChart__PriceHeading-iIpDul */
  .gaOoIW {
    position: relative;
    height: 54px;
    margin-right: 8px;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
    cursor: pointer;
  }
  .gaOoIW:not(:first-child) {
    margin-left: 12px;
  }
  .gaOoIW:hover:after {
    border-bottom-color: #0667D0;
  }
  .gaOoIW:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #0667D0;
  }
  .cawfqU {
    position: relative;
    height: 54px;
    margin-right: 8px;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
    cursor: pointer;
  }
  .cawfqU:not(:first-child) {
    margin-left: 12px;
  }
  .cawfqU:hover:after {
    border-bottom-color: #7D95B6;
  }
  .cawfqU:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid transparent;
  }
  /* sc-component-id: PriceChart__HeadingTitle-bZuIYw */
  .eopEKS {
    color: #0667D0;
  }
  .bggHeP {
    color: #7D95B6;
  }
  /* sc-component-id: PriceChart__StyledHorizontalAxis-gQdITJ */
  .bsumUB {
    margin: 10px 40px;
  }
  /* sc-component-id: PriceChart__HeadingSeparator-fIxuZZ */
  .bBFStv {
    margin-right: 4px;
    margin-left: 4px;
    font-size: 18px;
    font-weight: 500;
    color: #0667D0;
  }
  .gzkfOx {
    margin-right: 4px;
    margin-left: 4px;
    font-size: 18px;
    font-weight: 500;
    color: #7D95B6;
  }
  /* sc-component-id: PriceChart__HeadingPrice-iOthZP */
  .gLGtWx {
    margin-top: 1px;
    color: #7D95B6;
    color: #0667D0;
    font-size: 16px;
    font-weight: 500;
  }
  .blbMMz {
    margin-top: 1px;
    color: #7D95B6;
    font-size: 16px;
    font-weight: 500;
  }
  /* sc-component-id: PriceChart__PriceSeparator-dmkqQu */
  .hXuKUF {
    border-right: 1px solid #DAE1E9;
    height: 100px;
  }
  /* sc-component-id: PriceChart__NumberContainer-dkVZjE */
  .bpZMcw {
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
  }
  /* sc-component-id: PriceChart__CenteredBigAmount-GFMLl */
  .fSWgoh {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
  }
  /* sc-component-id: PriceChart__NumberDetails-furklk */
  .dRxFxx {
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 500;
    color: #7D95B6;
    text-transform: uppercase;
    -webkit-letter-spacing: 2px;
    -moz-letter-spacing: 2px;
    -ms-letter-spacing: 2px;
    letter-spacing: 2px;
    white-space: nowrap;
  }
  /* sc-component-id: PriceChart__PriceContainer-fkPIYJ */
  .vbPXh {
    -webkit-flex-shrink: 0;
    -ms-flex-shrink: 0;
    flex-shrink: 0;
    height: 140px;
    padding: 0 56px;
  }
  /* sc-component-id: PriceChart__ChartContainer-egjApN */
  .IDahi {
    position: relative;
    margin: -1px 20px 0 20px;
    border-top: 1px solid #DAE1E9;
    border-bottom: 1px solid #DAE1E9;
  }
  /* sc-component-id: PriceChart__ChartAxisContainer-jFZAnB */
  .fSSVge {
    z-index: 1;
    width: 50px;
    padding: 7px 0;
  }
  /* sc-component-id: PriceChart__ChartAxis-hnvXTZ */
  .kSqzOS {
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    color: #7D95B6;
  }
  /* sc-component-id: CloseIcon__CloseAction-gmUZlK */
  .kIzKjJ {
    width: 18px;
    height: 18px;
    margin-left: auto;
    fill: #9BA6B2;
    cursor: pointer;
    -webkit-transition: fill 0.15s ease;
    transition: fill 0.15s ease;
  }
  .kIzKjJ:hover {
    fill: #4E5C6E;
  }
</style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true">
</style>
<style type="text/css" data-styled-components="eSREzx kcfKbe bioUsD cclgsU iWqNle bHtzlx czuHmn eazIHM crwjQk iXEclm eyUpCA kjIRZG fNPzZN gJLQer gtgZVV bZaCwQ bvSVQy" data-styled-components-is-local="true">
  /* sc-component-id: Icon__Wrapper-fDDgDg */
  .iWqNle {
    font-size: 0;
  }
  .iWqNle svg {
    fill: #0667D0;
    height: 32px;
    width: 32px;
  }
  /* sc-component-id: Input__InputField-gFkBsN */
  .bZaCwQ {
    z-index: 0;
    -webkit-flex: 1 1 0px;
    -ms-flex: 1 1 0px;
    flex: 1 1 0px;
    margin: 0;
    padding: 20px;
    border: none;
    border-radius: 3px;
    background: none;
    font-size: 18px;
    font-weight: 500;
    color: #4E5C6E;
    font-family: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
  }
  .bZaCwQ:focus {
    outline: none;
  }
  /* sc-component-id: Input__Container-evMrUq */
  .gtgZVV {
    position: relative;
    border-width: 1px;
    border-style: solid;
    border-color: #DAE1E9;
    border-radius: 4px;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
    background: #FFFFFF;
  }
  .gtgZVV:hover {
    border-color: #9BA6B2;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  /* sc-component-id: Input__Label-dTgnUu */
  .bvSVQy {
    margin-right: 20px;
    font-size: 18px;
    font-weight: 500;
    color: #9BA6B2;
  }
  /* sc-component-id: SelectList__Wrapper-hHZYYo */
  .eSREzx {
    position: relative;
    z-index: 0;
    height: 70px;
  }
  .eSREzx:focus {
    outline: none;
  }
  /* sc-component-id: SelectList__Select-JoEsj */
  .kcfKbe {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
  }
  /* sc-component-id: SelectList__Selector-cETNHI */
  .bioUsD {
    border: 1px solid #DAE1E9;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
    background-color: #FFFFFF;
  }
  .bioUsD>div {
    height: 68px;
    border-top: none;
  }
  /* sc-component-id: SelectList__Options-gKFIsB */
  .eazIHM {
    display: none;
    max-height: 415px;
    overflow: auto;
    border: 1px solid #DAE1E9;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
    border-top-left-radius: 4px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    background-color: #FFFFFF;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }
  .eazIHM>div:first-child {
    height: 68px;
    border-top: none;
  }
  /* sc-component-id: SelectList__Toggle-kgrmdE */
  .fNPzZN {
    width: 24px;
    border: 1px solid #DAE1E9;
    border-left: none;
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
    background-color: #F9FBFC;
  }
  /* sc-component-id: SelectList__ArrowIcon-YKqXD */
  .gJLQer {
    width: 10px;
    height: 14px;
    fill: #7D95B6;
  }
  /* sc-component-id: SelectListItem__Wrapper-hGlbbm */
  .cclgsU {
    height: 69px;
    padding: 0 15px;
    border-top-width: 1px;
    border-top-color: #DAE1E9;
    border-top-style: solid;
    font-size: 18px;
    font-weight: 500;
    color: #4E5C6E;
  }
  .crwjQk {
    height: 69px;
    padding: 0 15px;
    border-top-width: 1px;
    border-top-color: #DAE1E9;
    border-top-style: solid;
    font-size: 18px;
    font-weight: 500;
    color: #4E5C6E;
    background-color: #F9FBFC;
  }
  .iXEclm {
    height: 42px !important;
    padding: 0 15px;
    border-top-width: 1px;
    border-top-color: #DAE1E9;
    border-top-style: dashed;
    font-size: 14px;
    font-weight: 500;
    color: #4E5C6E;
  }
  /* sc-component-id: SelectListItem__StyledText-gavBoh */
  .czuHmn {
    font-size: 18px;
    color: #4E5C6E;
  }
  .kjIRZG {
    font-size: 14px;
    color: #4E5C6E;
  }
  /* sc-component-id: SelectListItem__Info-dtcTnd */
  .bHtzlx {
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    color: #4E5C6E;
  }
  .eyUpCA {
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    color: #4E5C6E;
  }
</style>
<style type="text/css" data-styled-components="iXByDW dZuoJU" data-styled-components-is-local="true">
  /* sc-component-id: SelectListItem__Details-jGfRUd */
  .iXByDW {
    font-size: 14px;
  }
  /* sc-component-id: SelectListItem__PlusIcon-kraSoD */
  .dZuoJU {
    width: 14px;
    height: 14px;
    margin-right: 6px;
    fill: #9BA6B2;
  }
</style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="false">
  /* sc-component-id: sc-global-489285438 */
  .pac-container {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    border: 1px solid #DAE1E9;
    border-top-right-radius: 0;
    border-top-left-radius: 0;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
  }
  .pac-container:after {
    background-image: none !important;
    height: 0px;
  }
  .pac-item {
    padding: 12px;
    font: "Avenir Next", -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    font-size: 14px;
  }
</style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true">
</style>
<style type="text/css" data-styled-components="hcfarJ EebUs faLhgV eSkPBG Okldq" data-styled-components-is-local="true">
  /* sc-component-id: Checkbox__Wrap-geEjPv */
  .hcfarJ {
    cursor: pointer;
  }
  /* sc-component-id: Checkbox__Label-grdJg */
  .EebUs {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    margin-left: 8px;
  }
  /* sc-component-id: Checkbox__Check-sSoeJ */
  .Okldq {
    display: none;
  }
  /* sc-component-id: Checkbox__CheckIcon-biZoiX */
  .eSkPBG {
    width: 10px;
    height: 10px;
    fill: #FFFFFF;
  }
  /* sc-component-id: Checkbox__Indicator-cTVDOj */
  .faLhgV {
    width: 16px;
    height: 16px;
    border: 1px solid #DAE1E9;
    border-radius: 2px;
    text-align: center;
    background: #FFFFFF;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-transition: 100ms ease-in-out;
    transition: 100ms ease-in-out;
    -webkit-transition-property: background, border-color, box-shadow, color;
    transition-property: background, border-color, box-shadow, color;
  }
  .faLhgV:hover {
    border-color: #9BA6B2;
  }
  .faLhgV:focus {
    box-shadow: inset 0 2px 2px 0 rgba(0, 0, 0, 0.1);
  }
</style>
<style type="text/css" data-styled-components="ciNoGH bGBUHV bojYQs gOQkOA bKjIJn esdkMc vjDPW kJIQEM coUWrV bQCmbw gaEIjw" data-styled-components-is-local="true">
  /* sc-component-id: BlockingMessage__StyledFlex-blKFnJ */
  .vjDPW {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    padding: 0px;
    color: #4E5C6E;
  }
  /* sc-component-id: BlockingMessage__Content-dxeWVg */
  .kJIQEM {
    max-width: 420px;
    padding: 31px 0;
    text-align: center;
  }
  /* sc-component-id: BlockingMessage__PromoImage-fzFfXr */
  .coUWrV {
    height: 150px;
    margin-bottom: 20px;
  }
  /* sc-component-id: BlockingMessage__Title-hOMXDp */
  .bQCmbw {
    margin-bottom: 20px;
    font-size: 28px;
    font-weight: 500;
  }
  /* sc-component-id: BlockingMessage__Description-bhlnow */
  .gaEIjw {
    margin-bottom: 30px;
  }
  /* sc-component-id: PaymentMethodOption__Wrapper-hUXQyu */
  .bGBUHV {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
  }
  /* sc-component-id: PaymentMethodOption__Balance-bvAqdT */
  .bojYQs {
    font-size: 12px;
    color: #9BA6B2;
  }
  /* sc-component-id: PaymentMethodOption__IconWrapper-cJwaDh */
  .ciNoGH {
    margin-right: 14px;
  }
  /* sc-component-id: AccountOption__BalanceNative-bCLyRK */
  .esdkMc {
    font-size: 12px;
    color: #9BA6B2;
  }
  /* sc-component-id: AccountOption__IconWrapper-cwmoBE */
  .bKjIJn {
    margin-right: 14px;
  }
  /* sc-component-id: ConfirmDiagram-idhDpz */
  .gOQkOA {
    width: 16px;
    height: 16px;
    fill: #0667D0;
  }
</style>
<style type="text/css" data-styled-components="djoBUG ibMGEK rYbIC eDMnxP" data-styled-components-is-local="true">
  /* sc-component-id: ConfirmDiagram-cnhWYg */
  .eDMnxP {
    width: 16px;
    height: 16px;
    fill: #0667D0;
  }
  /* sc-component-id: ConfirmDiagram__Wrapper-jnqmHk */
  .djoBUG {
    margin: 22px 0px;
    margin-right: 18px;
    font-weight: 500;
    color: #0667D0;
  }
  /* sc-component-id: ConfirmDiagram__IconContainer-khAHkI */
  .ibMGEK {
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: #EBF1FA;
  }
  /* sc-component-id: ConfirmDiagram__Line-RGTdI */
  .rYbIC {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    margin: 6px 0px;
    margin-right: -1px;
    border-right: 1px dashed #0667D0;
  }
</style>
<style type="text/css" data-styled-components="iVXwXF iwUpTz csnBXu bYfUmM hVyxPC jjlJLI fyqmeI" data-styled-components-is-local="true">
  /* sc-component-id: BalanceRow__Wrapper-GRndq */
  .csnBXu {
    padding: 20px;
  }
  .csnBXu:not(:first-child) {
    border-top: 1px solid #DAE1E9;
  }
  /* sc-component-id: BalanceRow__Title-fAGjSb */
  .bYfUmM {
    min-width: 175px;
  }
  /* sc-component-id: BalanceRow__Icon-llPQSA */
  .hVyxPC {
    margin-right: 16px;
  }
  /* sc-component-id: BalanceRow__Amount-hltLsT */
  .jjlJLI {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
  }
  /* sc-component-id: BalanceRow__NativeAmount-jQZVyX */
  .fyqmeI {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
  }
  /* sc-component-id: BalancesWidget__Option-fkDZqx */
  .iVXwXF {
    position: relative;
    color: #0667D0;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
    cursor: pointer;
    font-size: 16px;
  }
  .iVXwXF:not(:first-child) {
    margin-left: 12px;
  }
  .iVXwXF:hover:after {
    border-bottom-color: #0667D0;
  }
  .iVXwXF:after {
    content: '';
    position: absolute;
    bottom: -16px;
    left: 0px;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid #0667D0;
  }
  .iwUpTz {
    position: relative;
    color: #7D95B6;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
    cursor: pointer;
    font-size: 16px;
  }
  .iwUpTz:not(:first-child) {
    margin-left: 12px;
  }
  .iwUpTz:hover:after {
    border-bottom-color: #7D95B6;
  }
  .iwUpTz:after {
    content: '';
    position: absolute;
    bottom: -16px;
    left: 0px;
    width: 100%;
    height: 1px;
    border-bottom: 1px solid transparent;
  }
</style>
<style type="text/css" data-styled-components="cYFmKg kbQyLI fJxaut kjRPPr fEfrBY cNJUjb eoNrlE iKLMhg gHBIFB cJShLP nDuUc doKCBi dThaic cwTBLf eMNjQO keHVTX kIXsIf hMgXGE TMIzi bskbTZ jkjlXM bKukTQ imvZJu ePPuQH bLDjXP ciHInt gjnxOl behCRk bHtbRs CrFOg enMLke iSKlLS ljJDpM iDhnxQ ibdHOF"
       data-styled-components-is-local="true">
  /* sc-component-id: TransactionListItem__LinkContainer-dcvOgD */
  .cNJUjb {
    -webkit-flex: 0 0 88px;
    -ms-flex: 0 0 88px;
    flex: 0 0 88px;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    max-height: 88px;
    padding: 20px;
    border-bottom: 1px solid #DAE1E9;
    color: #9BA6B2;
    cursor: pointer;
    -webkit-transition: background-color ease 0.25s;
    transition: background-color ease 0.25s;
  }
  .cNJUjb:hover {
    background-color: lighten($smokeLight, 2%);
    -webkit-transition: background-color ease 0.25s;
    transition: background-color ease 0.25s;
  }
  .cNJUjb:focus {
    outline: none;
  }
  .cwTBLf {
    -webkit-flex: 0 0 88px;
    -ms-flex: 0 0 88px;
    flex: 0 0 88px;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    max-height: 88px;
    padding: 20px;
    border-bottom: none;
    color: #9BA6B2;
    cursor: pointer;
    -webkit-transition: background-color ease 0.25s;
    transition: background-color ease 0.25s;
  }
  .cwTBLf:hover {
    background-color: lighten($smokeLight, 2%);
    -webkit-transition: background-color ease 0.25s;
    transition: background-color ease 0.25s;
  }
  .cwTBLf:focus {
    outline: none;
  }
  /* sc-component-id: TransactionListItem__DateContainer-gqotRI */
  .eoNrlE {
	min-width: 178px;
    margin-top: 5px;
    margin-right: 20px;
    text-align: center;
    max-width: 178px;
    overflow: hidden;
  }
  /* sc-component-id: TransactionListItem__DateMonth-jfcbZP */
  .iKLMhg {
    font-size: 14px;
    font-weight: 600;
    -webkit-letter-spacing: 1px;
    -moz-letter-spacing: 1px;
    -ms-letter-spacing: 1px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #4E5C6E;
  }
  /* sc-component-id: TransactionListItem__DateDay-eNBQyR */
  .gHBIFB {
    font-size: 22px;
    -webkit-letter-spacing: 1px;
    -moz-letter-spacing: 1px;
    -ms-letter-spacing: 1px;
    letter-spacing: 1px;
    color: #9BA6B2;
  }
  /* sc-component-id: TransactionListItem__IconWrapper-ddOKma */
  .cJShLP {
    margin-right: 20px;
  }
  /* sc-component-id: TransactionListItem__TransactionTitle-hrYDmM */
  .nDuUc {
    font-size: 18px;
    font-weight: 500;
    color: #4E5C6E;
  }
  /* sc-component-id: TransactionListItem__TransactionSubtitle-CAWdv */
  .doKCBi {
    font-size: 14px;
    font-weight: 500;
    color: #9BA6B2;
  }
  /* sc-component-id: TransactionListItem__AmountContainer-vrpLr */
  .dThaic {
    -webkit-flex-shrink: 0;
    -ms-flex-shrink: 0;
    flex-shrink: 0;
    text-align: right;
  }
  /* sc-component-id: Dashboard__FadeFlex-bFoDXs */
  .cYFmKg {
    opacity: 1;
    -webkit-transition: opacity 1s ease;
    transition: opacity 1s ease;
    width: 100%;
  }
  /* sc-component-id: Dashboard__StyledTopLevelAlerts-cIJQwz */
  .kbQyLI {
    margin-top: 25px;
    margin-bottom: 0;
  }
  /* sc-component-id: Dashboard__Panels-getBDx */
  .fJxaut {
    width: 100%;
    padding: 25px 0;
  }
  /* sc-component-id: Dashboard__DashPanel-hIpZDh */
  .fEfrBY {
    width: 578px;
    height: 460px;
    margin-bottom: 25px;
  }
  /* sc-component-id: Dashboard__ChartContainer-bKDMTA */
  .kjRPPr {
    width: 100%;
    height: 460px;
    margin-bottom: 25px;
  }
  /* sc-component-id: TradeFormTabContainer__Container-cUyfJR */
  .eMNjQO {
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    width: 578px;
    opacity: 1;
    -webkit-transition: opacity ease 0.75s;
    transition: opacity ease 0.75s;
  }
  /* sc-component-id: TradeFormTabContainer__Content-bTJPSU */
  .TMIzi {
    min-height: 500px;
    padding: 20px;
  }
  /* sc-component-id: TradeFormTabContainer__Tab-caAlbq */
  .keHVTX {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    height: 54px;
    margin-top: -1px;
    cursor: pointer;
    font-size: 18px;
    font-weight: 500;
    background-color: #FFFFFF;
    border-left: 1px solid #DAE1E9;
    border-right: 1px solid #DAE1E9;
    border-top: 1px solid #0667D0;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    padding-top: 1px;
  }
  .keHVTX a {
    margin-top: -1px;
    color: #0667D0;
  }
  .keHVTX:first-child {
    margin-left: -1px;
  }
  .keHVTX:last-child {
    margin-right: -1px;
  }
  .hMgXGE {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    height: 54px;
    margin-top: -1px;
    cursor: pointer;
    font-size: 18px;
    font-weight: 500;
    background-color: #F9FBFC;
    border-bottom: 1px solid #DAE1E9;
    border-top: 1px solid #DAE1E9;
    border-radius: 0;
  }
  .hMgXGE a {
    margin-top: 0px;
    color: #7D95B6;
  }
  .hMgXGE:first-child {
    border-top-left-radius: 4px;
  }
  .hMgXGE:last-child {
    border-top-right-radius: 4px;
  }
  /* sc-component-id: TradeFormTabContainer__TabLink-bIVxHh */
  .kIXsIf {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 100%;
    height: 54px;
    font-weight: 500;
  }
  /* sc-component-id: SelectButton__Wrapper-iEXBEe */
  .bKukTQ {
    position: relative;
    height: 105px;
    border: 1px solid #4BAD02;
    border-radius: 4px;
    background: #FFFFFF;
    font-size: 16px;
    font-weight: 500;
    color: #4E5C6E;
    cursor: pointer;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  .bKukTQ:not(:first-child) {
    margin-left: 12px;
  }
  .bKukTQ:hover {
    border-color: #4BAD02;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  .bHtbRs {
    position: relative;
    height: 105px;
    border: 1px solid #DAE1E9;
    border-radius: 4px;
    background: #FFFFFF;
    font-size: 16px;
    font-weight: 500;
    color: #4E5C6E;
    cursor: pointer;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  .bHtbRs:not(:first-child) {
    margin-left: 12px;
  }
  .bHtbRs:hover {
    border-color: #9BA6B2;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  /* sc-component-id: SelectButton__Content-kgjEED */
  .imvZJu {
    opacity: 1;
  }
  /* sc-component-id: SelectButton__Check-kqKqRu */
  .gjnxOl {
    position: absolute;
    top: -9px;
    right: -9px;
    width: 18px;
    height: 18px;
    border: 1px solid #4BAD02;
    border-radius: 50%;
    box-shadow: 0 0 0 3px #FFFFFF;
    color: $white;
    background-color: #61CA00;
  }
  /* sc-component-id: SelectButton__CheckIcon-iRqjpt */
  .behCRk {
    width: 10px;
    height: 10px;
    fill: #FFFFFF;
    margin-left: -1px;
  }
  /* sc-component-id: TradeSection__Wrapper-jIpuvx */
  .bskbTZ {
    position: relative;
    margin-bottom: 25px;
  }
  /* sc-component-id: TradeSection__Label-bicWvY */
  .CrFOg {
    padding-bottom: 10px;
  }
  /* sc-component-id: CurrencyNav__CurrencyButton-icxiLF */
  .jkjlXM {
    width: 124px;
  }
  /* sc-component-id: CurrencyNav__CurrencyName-kRqXiJ */
  .bLDjXP {
    margin-bottom: 2px;
    font-size: 14px;
    text-align: center;
  }
  /* sc-component-id: CurrencyNav__CurrencyQuote-fkMbXL */
  .ciHInt {
    font-size: 12px;
    color: #9BA6B2;
  }
  /* sc-component-id: CurrencyNav__IconWrapper-bzroag */
  .ePPuQH {
    margin-bottom: 0px;
  }
  /* sc-component-id: Limit__Wrapper-iEYShU */
  .enMLke {
    font-size: 14px;
    font-weight: 500;
    color: #4E5C6E;
  }
  /* sc-component-id: Limit__LimitInfo-dXMGSt */
  .iSKlLS {
    margin-bottom: 8px;
  }
  /* sc-component-id: Limit__LimitsLink-gZXRcd */
  .ljJDpM {
    margin-left: 5px;
    text-decoration: underline;
    color: #0667D0;
  }
  .ljJDpM:before {
    content: '·';
    display: inline-block;
    margin-right: 5px;
    color: #4E5C6E;
  }
  /* sc-component-id: LinkedInput__StyledAmountInput-kVLsbQ */
  .iDhnxQ input,
  .iDhnxQ input:focus {
    width: 150px;
    min-width: 0;
  }
  /* sc-component-id: LinkedInput__ExchangeIcon-bhcRJw */
  .ibdHOF {
    width: 20px;
    height: 22px;
    margin: 22px 14px;
    fill: #9BA6B2;
  }
</style>
<style type="text/css" data-styled-components="dDwder eZjiHY exnfsR bntZhQ dVUOBP jLcYlp kPUfcv MLjft fylBgC cHKYQN iZlLYP hcGcnN jSlsxS huYTkB dSuuXk gmJeyK bPDeSX gcCxss dokghr" data-styled-components-is-local="true">
  /* sc-component-id: LinkedInput__InputContainer-kmPcpF */
  .jLcYlp {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
  }
  /* sc-component-id: Input__StyledCurrencyInput-eTxsyD */
  .dVUOBP {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
  }
  /* sc-component-id: Input__InputContainer-bvqDaI */
  .bntZhQ {
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
  }
  /* sc-component-id: Input__Divider-gIYeAf */
  .dDwder {
    margin-bottom: 14px;
    border-top: 1px solid #DAE1E9;
  }
  /* sc-component-id: Input__Limits-kpBYrq */
  .eZjiHY {
    position: relative;
    margin-bottom: 10px;
  }
  /* sc-component-id: Input__LimitProgress-dJSEep */
  .exnfsR {
    position: absolute;
    right: 0;
    bottom: 0;
    left: 0;
    opacity: 0.75;
  }
  /* sc-component-id: ConfirmRow__Wrapper-jqXOgs */
  .jSlsxS {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    max-width: 300px;
    margin: 18px 0px;
    font-weight: 500;
    color: #0667D0;
  }
  /* sc-component-id: ConfirmRow__Title-cXDnUJ */
  .huYTkB {
    margin-bottom: 2px;
    font-size: 14px;
    color: #7AA4DE;
  }
  /* sc-component-id: ConfirmRow__Body-kqtsVp */
  .dSuuXk {
    font-size: 18px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
  }
  /* sc-component-id: TotalsRow__Wrapper-jgGRGE */
  .gmJeyK {
    margin-bottom: 15px;
    padding: 20px 0 0;
    font-size: 14px;
  }
  /* sc-component-id: TotalsRow__Row-QBNEe */
  .bPDeSX {
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    border-bottom: 1px dashed #BCD1EE;
  }
  .bPDeSX:not(:first-child) {
    margin-top: 15px;
  }
  /* sc-component-id: TotalsRow__Header-eOQejA */
  .gcCxss {
    margin-bottom: -6px;
    padding-right: 10px;
    color: #7AA4DE;
    background: #FFFFFF;
  }
  /* sc-component-id: TotalsRow__Amount-eucQTi */
  .dokghr {
    margin-bottom: -6px;
    padding-left: 10px;
    color: #0667D0;
    background: #FFFFFF;
  }
  /* sc-component-id: ConfirmWidget__Receipt-hzvtey */
  .kPUfcv {
    position: relative;
    width: 420px;
    background: #FFFFFF;
  }
  /* sc-component-id: ConfirmWidget__Content-kdmBWh */
  .MLjft {
    padding: 30px;
    font-weight: 500;
  }
  /* sc-component-id: ConfirmWidget__Header-scrbV */
  .fylBgC {
    padding-bottom: 25px;
    border-bottom: 1px solid #BCD1EE;
    text-align: center;
  }
  /* sc-component-id: ConfirmWidget__Description-eGcDwz */
  .cHKYQN {
    font-size: 12px;
    text-transform: uppercase;
    -webkit-letter-spacing: 2px;
    -moz-letter-spacing: 2px;
    -ms-letter-spacing: 2px;
    letter-spacing: 2px;
    text-align: center;
    color: #7AA4DE;
  }
  /* sc-component-id: ConfirmWidget__Amount-hQwIoH */
  .iZlLYP {
    margin: 10px;
    font-size: 42px;
    color: #0667D0;
  }
  /* sc-component-id: ConfirmWidget__Price-cwkfDR */
  .hcGcnN {
    font-size: 14px;
    color: #0667D0;
  }
</style>
<style type="text/css" data-styled-components="jkvMB hNLWXE eDmWpK hihUrD fkZQvs kkSOOR ksjufd kXusyX eezuXj gNBMlg" data-styled-components-is-local="true">
  /* sc-component-id: ConfirmWidget__Separator-ktwNJg */
  .ksjufd {
    border-top: 1px solid #BCD1EE;
  }
  /* sc-component-id: ConfirmWidget__Dull-eHFqvN */
  .kXusyX {
    color: #7AA4DE;
    opacity: 0.5;
  }
  /* sc-component-id: ConfirmWidget__Footnote-cmovJH */
  .eezuXj {
    margin-top: 15px;
    font-size: 12px;
    text-align: center;
    color: #7AA4DE;
  }
  .eezuXj a {
    text-decoration: underline;
    color: #7AA4DE;
  }
  /* sc-component-id: ButtonGroup__ButtonContainer-jvlCJq */
  .fkZQvs {
    z-index: 0;
  }
  .fkZQvs button {
    z-index: -3;
    padding: 6px 18px;
    font-weight: 500;
  }
  .fkZQvs button:hover {
    z-index: -2;
  }
  .fkZQvs button:first-child {
    margin-right: -1px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }
  .fkZQvs button:not(:first-child):not(:last-child) {
    margin-right: -1px;
    border-radius: 0;
  }
  .fkZQvs button:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }
  /* sc-component-id: RecurrenceSelector__Divider-buySbC */
  .eDmWpK {
    margin-bottom: 20px;
    border-top: 1px solid #DAE1E9;
  }
  /* sc-component-id: RecurrenceSelector__EnableRecurrence-jBvzGm */
  .hihUrD {
    margin-right: 4px;
  }
  .hihUrD input {
    margin: 0;
    font-size: 16px;
  }
  .hihUrD label {
    font-size: 14px;
    font-weight: 500;
    color: #4E5C6E;
  }
  /* sc-component-id: Trade__TradeView-eeHZtW */
  .jkvMB {
    -webkit-flex-shrink: 0;
    -ms-flex-shrink: 0;
    flex-shrink: 0;
    position: relative;
    min-height: 600px;
  }
  /* sc-component-id: Trade__TradeFormContainer-lhJJLd */
  .hNLWXE {
    -webkit-transition: -webkit-transform ease 0.75s;
    -webkit-transition: transform ease 0.75s;
    transition: transform ease 0.75s;
    will-change: transform;
    -webkit-flex: 1 0 auto;
    -ms-flex: 1 0 auto;
    flex: 1 0 auto;
  }
  /* sc-component-id: Trade__Preview-ftIHSO */
  .kkSOOR {
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 100%;
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
    width: auto;
  }
  /* sc-component-id: Trade__StyledAccountSelect-cDTyZe */
  .gNBMlg {
    z-index: 101;
  }
</style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true">
</style>
<style type="text/css" data-styled-components="jIylJE eaiFtd koXqeq bEvKsN GENJj IEDDp kAitaK fNrJZo hOLrOE hrRTUc fncwsP cCtppT jHWdbr DjVLU bwjNFz eShXlj eBHdBb gQzZCC dLmsTJ gGcmVR jRHZPa kxlzLR enflJl kDYzjU ePbcro fyPAlX gFkBlW iRVFpq cptJlx jeFGBP hgQNZs ACHdb cDiUHB fSQeym"
       data-styled-components-is-local="true">
  /* sc-component-id: DropdownMenu__Wrapper-ftzJux */
  .DjVLU {
    position: absolute;
    top: 100%;
    right: auto;
    bottom: auto;
    left: 0;
    display: none;
    right: 0;
    left: auto;
  }
  .kDYzjU {
    position: absolute;
    top: 100%;
    right: auto;
    bottom: auto;
    left: 0;
    display: none;
    right: 0;
    left: auto;
    top: auto;
    bottom: 100%;
  }
  /* sc-component-id: DropdownMenu__Overlay-fkiKFB */
  .bwjNFz {
    position: fixed;
    z-index: 99;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: none;
  }
  /* sc-component-id: DropdownMenu__Content-hEqeZW */
  .eShXlj {
    z-index: 100;
    min-width: 180px;
    margin: 8px 0;
    border: 1px solid #DAE1E9;
    border-radius: 4px;
    box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.05);
    background-color: #FFFFFF;
  }
  /* sc-component-id: DropdownItem__Wrapper-cbEAmf */
  .eBHdBb {
    padding: 12px;
    cursor: pointer;
    -webkit-transition: background-color 0.25s ease;
    transition: background-color 0.25s ease;
  }
  .eBHdBb:first-child {
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
  }
  .eBHdBb:last-child {
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
  }
  .eBHdBb:hover {
    background-color: #F9FBFC;
    -webkit-transition: all 0.25s ease;
    transition: all 0.25s ease;
  }
  /* sc-component-id: AccountActionsMenu__EditIcon-foMaEM */
  .gQzZCC {
    width: 12px;
    height: 12px;
    fill: #9BA6B2;
    margin-right: 10px;
  }
  /* sc-component-id: AccountActionsMenu__QRIcon-jccpKP */
  .ePbcro {
    width: 14px;
    height: 14px;
    fill: #9BA6B2;
    margin-right: 10px;
  }
  /* sc-component-id: MenuButton__Container-eEqVio */
  .cCtppT {
    position: relative;
  }
  .cCtppT button {
    width: 41px;
    height: 41px;
  }
  /* sc-component-id: Currency__SpacerText-fSDTqc */
  .kAitaK {
    padding: 0 5px;
  }
  /* sc-component-id: EditableAccountName__AccountName-eWmPDg */
  .IEDDp {
    margin-bottom: 4px;
    max-width: 275px;
    text-overflow: ellipsis;
    overflow: hidden;
  }
  /* sc-component-id: AccountActionButtons__AccountButton-ejRNLb */
  /* sc-component-id: AccountActionButtons__DepositIcon-eHtQuS */
  .kxlzLR {
    fill: #9BA6B2;
    margin-right: 8px;
  }
  /* sc-component-id: AccountActionButtons__WithdrawIcon-cEXuOU */
  .enflJl {
    fill: #1166d1;
    margin-right: 8px;
  }
  /* sc-component-id: AccountActionButtons__SendIcon-dZmYeo */
  .hrRTUc {
    fill: #9BA6B2;
    width: 13px;
    height: 13px;
    margin-right: 8px;
  }
  /* sc-component-id: AccountActionButtons__QRIcon-iiSViy */
  .fncwsP {
    fill: #9BA6B2;
    width: 13px;
    height: 13px;
    margin-right: 8px;
  }
  /* sc-component-id: AccountListItem__Account-laXKDv */
  .jIylJE {
    min-width: 360px;
    cursor: pointer;
    background-color: #F9FBFC;
    background-color: #FFFFFF;
  }
  .dLmsTJ {
    min-width: 360px;
    cursor: pointer;
    background-color: #F9FBFC;
    border-bottom: 1px solid #DAE1E9;
  }
  /* sc-component-id: AccountListItem__SelectedIndicator-dpXoDO */
  .eaiFtd {
    -webkit-flex: 0 0 1px;
    -ms-flex: 0 0 1px;
    flex: 0 0 1px;
    margin-top: -1px;
    background-color: #0667D0;
  }
  .gGcmVR {
    -webkit-flex: 0 0 1px;
    -ms-flex: 0 0 1px;
    flex: 0 0 1px;
  }
  /* sc-component-id: AccountListItem__ContentWrap-kSwyDk */
  .koXqeq {
    border-bottom: 1px solid #DAE1E9;
  }
  /* sc-component-id: AccountListItem__Icon-jlvqZo */
  .bEvKsN {
    padding: 16px;
    padding-top: 20px;
  }
  /* sc-component-id: AccountListItem__Details-cWizxw */
  .GENJj {
    padding: 16px 16px 16px 0;
  }
  /* sc-component-id: AccountListItem__Actions-bsqZNF */
  .fNrJZo {
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    margin-top: 16px;
  }
  .fNrJZo button {
    -webkit-flex: 1;
    -ms-flex: 1;
    flex: 1;
  }
  .fNrJZo button:not(:first-child) {
    margin-left: 8px;
    margin-right: 8px;
  }
  /* sc-component-id: AccountListItem__MoreIcon-bhuCDT */
  .jHWdbr {
    fill: #9BA6B2;
  }
  /* sc-component-id: AccountPromoListItem__Subtext-cSEYXh */
  .hgQNZs {
    margin-top: 2px;
  }
  /* sc-component-id: AccountPromoListItem__AccountLink-bIRmsd */
  .fyPAlX {
    outline: none;
  }
  /* sc-component-id: AccountPromoListItem__Account-iOKRIR */
  .gFkBlW {
    min-width: 360px;
    cursor: pointer;
    background-color: #F9FBFC;
    border-bottom: 1px solid #DAE1E9;
  }
  .ACHdb {
    min-width: 360px;
    cursor: pointer;
    background-color: #F9FBFC;
    background-color: #FFFFFF;
  }
  /* sc-component-id: AccountPromoListItem__SelectedIndicator-gnUGdi */
  .iRVFpq {
    -webkit-flex: 0 0 1px;
    -ms-flex: 0 0 1px;
    flex: 0 0 1px;
  }
  .cDiUHB {
    -webkit-flex: 0 0 1px;
    -ms-flex: 0 0 1px;
    flex: 0 0 1px;
    margin-top: -1px;
    background-color: #0667D0;
  }
  /* sc-component-id: AccountPromoListItem__ContentWrap-gLxIHM */
  .fSQeym {
    border-bottom: 1px solid #DAE1E9;
  }
  /* sc-component-id: AccountPromoListItem__Icon-lmSZJW */
  .jeFGBP {
    padding: 16px;
    padding-top: 20px;
  }
</style>
<style type="text/css" data-styled-components="jZdQTT bnXidV khoFYY dOVJhZ hhOcaD ICfmD jlkapd eLRdEm iNsmUo bffLaf gCXeDQ" data-styled-components-is-local="true">
  /* sc-component-id: AccountPromoListItem__Details-dVNKmu */
  .khoFYY {
    position: relative;
    padding: 16px 16px 16px 0;
  }
  /* sc-component-id: AccountPromoListItem__CloseAction-hPxcMX */
  .dOVJhZ {
    width: 14px;
    height: 14px;
    -webkit-align-self: center;
    -ms-flex-item-align: center;
    align-self: center;
    margin-right: 16px;
  }
  /* sc-component-id: AccountList__AccountsList-eEZOms */
  .jZdQTT {
    overflow-y: auto;
    height: 553;
  }
  /* sc-component-id: AccountList__AccountLink-cGluzb */
  .bnXidV {
    outline: none;
  }
  /* sc-component-id: TransactionList__Container-hwtrOD */
  .hhOcaD {
    max-height: 100%;
  }
  /* sc-component-id: TransactionList__SearchBar-gjttrf */
  .eLRdEm {
    -webkit-flex: 0 0 50px;
    -ms-flex: 0 0 50px;
    flex: 0 0 50px;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    justify-content: space-between;
    padding: 0 16px 0 24px;
    border-bottom: 1px solid #DAE1E9;
    background: #F9FBFC;
  }
  /* sc-component-id: TransactionList__SearchWrapper-kKeJba */
  .iNsmUo {
    position: relative;
  }
  /* sc-component-id: TransactionList__SearchIcon-klWtWk */
  .bffLaf {
    position: absolute;
    z-index: 1;
    left: 10px;
    top: 10px;
    width: 14px;
    height: 14px;
    fill: #9BA6B2;
  }
  /* sc-component-id: TransactionList__SearchInput-dFfUXN */
  .gCXeDQ {
    border-radius: 4px;
  }
  .gCXeDQ input {
    padding: 6px;
    padding-left: 30px;
    border-radius: 4px;
    background: #FFFFFF;
    font-size: 14px;
  }
  .gCXeDQ input:placeholder {
    color: red;
  }
  /* sc-component-id: TransactionList__ScrollContainer-PYBhA */
  .ICfmD {
    position: relative;
    overflow-y: auto;
    height: 561;
  }
  /* sc-component-id: TransactionList__Center-ioVhhz */
  .jlkapd {
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    min-height: 300px;
    padding: 20px;
  }
</style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true">
</style>
<style type="text/css" data-styled-components="TBfrq hjYHLC bjqSJc fzpAGl kjoxfT cBaRUg eCaGZQ" data-styled-components-is-local="true">
  /* sc-component-id: Accounts__Container-cJqPrg */
  .TBfrq {
    -webkit-flex: 0 0 calc(100vh - 200px);
    -ms-flex: 0 0 calc(100vh - 200px);
    flex: 0 0 calc(100vh - 200px);
    min-height: 717px;
  }
  /* sc-component-id: Accounts__HeightWrapper-bIafrT */
  .bjqSJc {
    -webkit-flex: height: 100%;
    -ms-flex: height: 100%;
    flex: height: 100%;
    max-height: 100%;
    overflow: hidden;
  }
  /* sc-component-id: Accounts__Header-kUlsOz */
  .hjYHLC {
    -webkit-flex: 0 0 54px;
    -ms-flex: 0 0 54px;
    flex: 0 0 54px;
    padding: 0px 20px;
    border-bottom: 1px solid #DAE1E9;
  }
  /* sc-component-id: Accounts__AccountListWrapper-hJZLkd */
  .fzpAGl {
    position: relative;
    -webkit-flex: 0 0 360px;
    -ms-flex: 0 0 360px;
    flex: 0 0 360px;
    padding-bottom: 58px;
  }
  /* sc-component-id: Accounts__PlusIcon-YOjBG */
  .cBaRUg {
    width: 12px;
    height: 12px;
    margin-right: 6px;
    fill: #9BA6B2;
  }
  /* sc-component-id: Accounts__FooterLink-bzmsMV */
  .kjoxfT {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    padding: 18px;
    border-bottom-left-radius: 4px;
    border-top: 1px solid #DAE1E9;
    background-color: #FFFFFF;
    cursor: pointer;
    color: #9BA6B2;
    font-weight: 500;
  }
  .kjoxfT:hover {
    color: #4E5C6E;
  }
  /* sc-component-id: Accounts__AccountDetailsContainer-gmlBsr */
  .eCaGZQ {
    border-left: 1px solid #DAE1E9;
  }
</style>
<style type="text/css" data-styled-components="jeFCaz fSdpHS bBGwcz" data-styled-components-is-local="true">
  /* sc-component-id: Toasts__Container-kTLjCb */
  .jeFCaz {
    position: fixed;
    z-index: 9;
    top: 18px;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-align-items: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
    width: 100%;
  }
  /* sc-component-id: Navbar-isMmFU */
  .fSdpHS {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-bhheCq */
  .bBGwcz {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
</style>
<style type="text/css" data-styled-components="bgdsDH kmGoDe ediliA fPzmyn gFNfZa huTMcA llqTCK eaRwjt btDRbj exnUIm jGCZMs ccIZQY bdBCxn hAYUyK hUePnF hFKNhN deisrS eOznwO cryMpR kxILlN haAoJJ fHcfYp CmCWI VGQlh bivroQ gPCpvH jjIgrh OPpnF eJLpnU gqJZYX gsFTKb irLCbp fpIDHk kxcWlM hyvpzb eFvPEx LDQwh"
       data-styled-components-is-local="true">
  /* sc-component-id: Navbar-kqtHnp */
  .bgdsDH {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-jlTUPj */
  .kmGoDe {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-bSZOVv */
  .ediliA {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Alert-cAvYle */
  .fPzmyn {
    width: 42px;
    height: 42px;
  }
  /* sc-component-id: CurrencyIcon-iuzqsK */
  .gFNfZa circle {
    fill: #FFB119;
  }
  /* sc-component-id: CurrencyIcon-Marok */
  .huTMcA circle {
    fill: #8DC451;
  }
  /* sc-component-id: CurrencyIcon-ksscak */
  .llqTCK circle {
    fill: #6F7CBA;
  }
  /* sc-component-id: CurrencyIcon-UEZcn */
  .eaRwjt circle {
    fill: #B5B5B5;
  }
  /* sc-component-id: CurrencyIcon-zLxKQ */
  .btDRbj circle {
    fill: #0066cf;
  }
  /* sc-component-id: TransactionIcon-kQSElj */
  .exnUIm {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-hhVIAM */
  .jGCZMs {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-eDnHFh */
  .ccIZQY {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-dYRJvP */
  .bdBCxn {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: Navbar-cOPJGM */
  .hAYUyK {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-kCOMuB */
  .hUePnF {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-jXrcLu */
  .hFKNhN {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-bDNBkZ */
  .deisrS {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-kuicZq */
  .eOznwO {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: CurrencyIcon-ewqwUN */
  .cryMpR circle {
    fill: #FFB119;
  }
  /* sc-component-id: CurrencyIcon-kSehSM */
  .kxILlN circle {
    fill: #8DC451;
  }
  /* sc-component-id: CurrencyIcon-fhdSmQ */
  .haAoJJ circle {
    fill: #6F7CBA;
  }
  /* sc-component-id: CurrencyIcon-kMpiiS */
  .fHcfYp circle {
    fill: #B5B5B5;
  }
  /* sc-component-id: Limit-eUCAYm */
  .CmCWI {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    fill: #9BA6B2;
  }
  /* sc-component-id: ConfirmDiagram-fpFXpD */
  .VGQlh {
    width: 16px;
    height: 16px;
    fill: #0667D0;
  }
  /* sc-component-id: Navbar-cyuMYy */
  .bivroQ {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-glTlyW */
  .gPCpvH {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-fDGLww */
  .jjIgrh {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-jrPCGJ */
  .OPpnF {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-joUGTi */
  .eJLpnU {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: CurrencyIcon-kHDtbW */
  .gqJZYX circle {
    fill: #FFB119;
  }
  /* sc-component-id: CurrencyIcon-djwNNb */
  .gsFTKb circle {
    fill: #FFB119;
  }
  /* sc-component-id: CurrencyIcon-gifjgw */
  .irLCbp circle {
    fill: #B5B5B5;
  }
  /* sc-component-id: CurrencyIcon-fXqkjI */
  .fpIDHk circle {
    fill: #6F7CBA;
  }
  /* sc-component-id: CurrencyIcon-ljiVLA */
  .kxcWlM circle {
    fill: #8DC451;
  }
  /* sc-component-id: CurrencyIcon-cmTiGc */
  .hyvpzb circle {
    fill: #0066cf;
  }
  /* sc-component-id: CurrencyIcon-gpKYyS */
  .eFvPEx circle {
    fill: #0066cf;
  }
  /* sc-component-id: Limit-klrxDt */
  .LDQwh {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    fill: #9BA6B2;
  }
</style>
<style type="text/css" data-styled-components="ixUIjj kiixce ktfAVt ehnSVZ ftwEdb hCNUeX dkcmGV igKoss eMpCyW kvuzzu YpScr cQHqoD iwHnlk oUsjd dIbXDM dkwtUG jOalFf gaxDKY jziFcF crJeiC dXMUIb kJWJBN iMGyub kLEOSY kqOkPS bhTqcU ijBmwv Ueunz hiMbgx kNsrQY dwxhTn jUubVf jbpEjf gzzPcM fUSzIo hSxbQY ksKIDT hRWfQp bIhzNl"
       data-styled-components-is-local="true">
  /* sc-component-id: ConfirmDiagram-jiKPMa */
  .ixUIjj {
    width: 16px;
    height: 16px;
    fill: #0667D0;
  }
  /* sc-component-id: CurrencyIcon-ljvvYD */
  .kiixce circle {
    fill: #FFB119;
  }
  /* sc-component-id: CurrencyIcon-fYyhNC */
  .ktfAVt circle {
    fill: #FFB119;
  }
  /* sc-component-id: CurrencyIcon-fbtSMQ */
  .ehnSVZ circle {
    fill: #B5B5B5;
  }
  /* sc-component-id: CurrencyIcon-dLmcGu */
  .ftwEdb circle {
    fill: #6F7CBA;
  }
  /* sc-component-id: CurrencyIcon-gsWJJS */
  .hCNUeX circle {
    fill: #8DC451;
  }
  /* sc-component-id: Limit-bBgsZm */
  .dkcmGV {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    fill: #9BA6B2;
  }
  /* sc-component-id: Navbar-gRDuYJ */
  .igKoss {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-hEHYvu */
  .eMpCyW {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-eHXCAp */
  .kvuzzu {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-dAcigD */
  .YpScr {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Navbar-iuXgTD */
  .cQHqoD {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* sc-component-id: Alert-cFYdsw */
  .iwHnlk {
    width: 42px;
    height: 42px;
  }
  /* sc-component-id: CurrencyIcon-jPnOLu */
  .oUsjd circle {
    fill: #FFB119;
  }
  /* sc-component-id: CurrencyIcon-guHtfF */
  .dIbXDM circle {
    fill: #8DC451;
  }
  /* sc-component-id: CurrencyIcon-gHmKBL */
  .dkwtUG circle {
    fill: #6F7CBA;
  }
  /* sc-component-id: CurrencyIcon-hpufja */
  .jOalFf circle {
    fill: #B5B5B5;
  }
  /* sc-component-id: CurrencyIcon-haUTdU */
  .gaxDKY circle {
    fill: #0066cf;
  }
  /* sc-component-id: CurrencyIcon-hQoWPs */
  .jziFcF circle {
    fill: #0066cf;
  }
  /* sc-component-id: CurrencyIcon-gdWZMT */
  .crJeiC circle {
    fill: #FFB119;
  }
  /* sc-component-id: CurrencyIcon-kGmnaC */
  .dXMUIb circle {
    fill: #8DC451;
  }
  /* sc-component-id: CurrencyIcon-lapLDb */
  .kJWJBN circle {
    fill: #6F7CBA;
  }
  /* sc-component-id: CurrencyIcon-fhkqpi */
  .iMGyub circle {
    fill: #B5B5B5;
  }
  /* sc-component-id: CurrencyIcon-dcVXFQ */
  .kLEOSY circle {
    fill: #0066cf;
  }
  /* sc-component-id: Alert-hQsKMA */
  .kqOkPS {
    width: 42px;
    height: 42px;
  }
  /* sc-component-id: TransactionIcon-krmzvW */
  .bhTqcU {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-jIRIgG */
  .ijBmwv {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-fpKejE */
  .Ueunz {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-jDuRHa */
  .hiMbgx {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-fUuqiZ */
  .kNsrQY {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-fuTuMt */
  .dwxhTn {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-jcPVcJ */
  .jUubVf {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-fpPvBx */
  .jbpEjf {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: Alert-gLVtPy */
  .gzzPcM {
    width: 42px;
    height: 42px;
  }
  /* sc-component-id: Alert-IcIKo */
  .fUSzIo {
    width: 42px;
    height: 42px;
  }
  /* sc-component-id: TransactionIcon-eWbeqQ */
  .hSxbQY {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-iyPHGv */
  .ksKIDT {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-ffrQIi */
  .hRWfQp {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-dKcMBM */
  .bIhzNl {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
</style>
<meta name="viewport" content="width=device-width, initial-scale=1.0" data-react-helmet="true">
<style type="text/css" data-styled-components="bXZbHn gItvjQ fpteCB gZCxkG dzGsNa gEkxDi jewIZY blikWQ jaGpWY" data-styled-components-is-local="true">
  /* sc-component-id: TransactionIcon-lfvUFE */
  .bXZbHn {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-DYKRy */
  .gItvjQ {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-gtCfwP */
  .fpteCB {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-hHwYSW */
  .gZCxkG {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-dYHsTi */
  .dzGsNa {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-ezeROF */
  .gEkxDi {
    stroke: #0066cf;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-gOkGCa */
  .jewIZY {
    stroke: #FFB119;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-gibvPj */
  .blikWQ {
    stroke: #0066cf;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  /* sc-component-id: TransactionIcon-hHpoia */
  .jaGpWY {
    stroke: #0066cf;
    width: 42px;
    height: 42px;
    stroke-width: 1;
  }
  .eyRwnK {
    width: 16px;
    height: 16px;
    margin-right: 10px;
    fill: currentColor;
  }
  /* responsive css */
  @media (max-width: 991px){
     .iJJJTg,.bHipRv {
      -webkit-flex-direction: column;
      -ms-flex-direction: column;
      flex-direction: column;
    }
    .jZdQTT,.ejcVRF{
      width: 100%;
      height: inherit;
    }
    .klDPGy {
      max-width: 1180px;
      width: 100%;
    }
    .cFMUnB {
      max-width: 1180px;
      width: 100%;
    }
    .htCQOP {
      max-width: 1180px;
      width: 100%;
    }
    .AccountListItem__Icon-jlvqZo.bEvKsN{
      margin:auto;
    }
    .Navbar__Content-cgqezH.cFMUnB{
      flex-direction: column;
      padding: .4em 0 1em;
    }
    .Navbar__DesktopWrapper-jiGyXa.dzohzs{
      flex:auto;
    }
    .Navbar__LinkContainer-jXaDVl.hBrjIA{
      margin: 10px 15px;
    }
    .Navbar__LinkContainer-jXaDVl.hBrjIA:first-child{
      margin-left: 15px;
    }
    .TransactionListItem__LinkContainer-dcvOgD.cNJUjb {
      padding:3px;
    }
    .Footer__Right-iFQtJS.reCYb{
      flex-direction: column;
      align-items: flex-start;
    }
    .AccountListItem__Details-cWizxw.GENJj{
      padding:8px;
    }
    .LayoutDesktop__Content-flhQBc.kuJaHF {
      width: 100%;
      padding: 0;
      display: initial;
      overflow: initial;
    }
    .deposit-descp{
      margin-bottom: 0 !important;
    }
    .Accounts__Container-cJqPrg.fWIqmZ{
      padding:0;
    }
    .Footer__Wrapper-jgZZNA.kmYTnN{
      height: initial;
      max-height: initial;
      padding: 1em;
      width: 100%;
    }
    .Footer__Content-kfdTYL.hQXxaf{
      flex-direction: column;
      align-items: flex-start;
      display: inline-block;
    }
    .Footer__Link-hmaedR.dVuYMi{
      display: inline-block;
      padding: 10px 0;
      margin-left: 0 !important;
    }
    .Footer__Right-iFQtJS .gSHJhw,
    .Footer__Right-iFQtJS .kxbyEA,
    .Footer__Right-iFQtJS .Footer__NeedHelp-kNHURG{
      display: inline-block;
      padding: 10px 0;
    }
    .TransactionListItem__LinkContainer-dcvOgD .nDuUc{
      font-size: 15px;
    }
    .TransactionListItem__LinkContainer-dcvOgD.cNJUjb {
      min-height: 125px;
    }
    .Header__Wrapper-cwuouQ.fWgtDL{
      padding-right: 15px;
    }
  }
  }



</style>
<style>
  .dummy {
    cursor: pointer;
    color: #1166d1 !important;
    border-color: #1166d1;
}
.errors{
  position:initial !important;
}
.messages{
  position:initial !important;
}


</style>
<body class="app signed-in static_application index" data-controller-name="static_application" data-action-name="index" data-view-name="Coinbase.Views.StaticApplication.Index" data-account-id="">
  <div id="root">
    <div class="Flex__Flex-fVJVYW iJJJTg">
      <div class="Flex__Flex-fVJVYW iJJJTg">
        <div class="Toasts__Container-kTLjCb jeFCaz">
        </div>
        <div class="Layout__Container-jkalbK gCVQUv Flex__Flex-fVJVYW bHipRv">
          <div class="LayoutDesktop__AppWrapper-cPGAqn WhXLX Flex__Flex-fVJVYW bHipRv">
		  <? include 'includes/topheader.php'; ?>

            <div class="LayoutDesktop__ContentContainer-cdKOaO cpwUZB Flex__Flex-fVJVYW bHipRv">
			
			<? include 'includes/menubar.php'; ?>
      <div class="banner">
    <div class="container content">
        <h1>Withdraw</h1>
         <p style="text-align: center;color: #fff;">Create a Withdrawal request, view your Withdrawal transaction history.</p>
    </div>
</div>
              <div class="Accounts__Container-cJqPrg TBfrq LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">
                <div class="LayoutDesktop__Content-flhQBc kuJaHF Flex__Flex-fVJVYW gkSoIH">
                  <div class="description-box" style="    border: 1px solid #DAE1E9;padding: 10px;margin: 0 0 1em;border-radius: 4px;">
                    <p>Here you can:</p>
                    <ol>
                      <li>Place a Withdraw requests. </li>
                      <li>View all your withdrawal transaction details.</li>
                    </ol>
                </div>
                  <div id="myModal" class="modal">
                    
                    <div class="modal-content">
                    <? Errors::display(); ?>
		<? Messages::display(); ?>
                      <span class="close">&times;
                      </span>
                      <h3>
                        <u><?= Lang::string('withdraw-fiat') ?>
                        </u>
                      </h3>
                      <form id="buy_form" action="withdraw.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
                          <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                            <!-- <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('bank-accounts-account-cc') ?>
                            </h4> -->
                          </div>
                          <div>
                            <div class="Flex__Flex-fVJVYW gkSoIH">
                              <div class="form-group">
                              <?= str_replace('[currency]','<span class="currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-fiat-available')) ?></div>
								<div class="value"><span class="currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="user_available"><?= Stringz::currency($user_available[strtoupper($currency1)]) ?>
                              </div>
                            </div>
                          </div>
                        </div>
                       
                        <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
                          <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('withdraw-fiat-account') ?>
                            </h4>
                          </div>
                          <div>
                            <div class="Flex__Flex-fVJVYW gkSoIH">
                              <div class="form-group">
                                <div class="form-group">
                                <select id="withdraw_account"  class="form-control" name="account">
								<?
								if ($bank_accounts) {
									foreach ($bank_accounts as $account) {
										echo '<option '.(($bank_account['id'] == $account['id']) ? 'selected="selected"' : '').' value="'.$account['id'].'">'.$account['account_number'].' - ('.$account['currency'].')</option>';
									}
								}	
								?>
								</select>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
                          <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= Lang::string('withdraw-amount') ?>
                            </h4>
                          </div>
                          <div>
                            <div class="Flex__Flex-fVJVYW gkSoIH">
                              <div class="form-group">
                                <div class="form-group">
                                <input type="text" id="fiat_amount"  class="form-control"name="fiat_amount" value="<?= Stringz::currencyOutput($fiat_amount1) ?>" />
								<div class="qualify"><span class="currency_label"><?= $currency_info['currency'] ?></span></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
                          <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"><?= str_replace('[currency]','<span class="currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-fee')) ?> <a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule"><?= str_replace('[currency]','<span class="currency_label">'.$currency_info['currency'].'</span>',Lang::string('buy-fee')) ?> <a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule"><i class="fa fa-question-circle"></i></a>
                            </h4>
                          </div>
                          <div>
                            <div class="Flex__Flex-fVJVYW gkSoIH">
                              <div class="form-group">
                              <span class="currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="withdraw_fiat_fee"><?= Stringz::currencyOutput($CFG->fiat_withdraw_fee) ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="TradeSection__Wrapper-jIpuvx bskbTZ">
                          <div class="TradeSection__Label-bicWvY CrFOg Flex__Flex-fVJVYW gsOGkq">
                            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;"> <?= str_replace('[currency]','<span class="currency_label">'.$currency_info['currency'].'</span>',Lang::string('withdraw-total')) ?>
                            </h4>
                          </div>
                          <div>
                            <div class="Flex__Flex-fVJVYW gkSoIH">
                              <div class="form-group">
                                
                              <span class="currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="withdraw_fiat_total"><?= Stringz::currency($fiat_total1) ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <input type="hidden" name="fiat" value="1" />
                        <button type="submit" class="Button__Container-hQftQV kZBVvC" style="cursor: pointer;">
                          <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                            <div class="Flex__Flex-fVJVYW ghkoKS"><?= Lang::string('withdraw-withdraw') ?> 
                            </div>
                          </div>
                        </button>
                      </form>
                    </div>
                  </div>
                  
		<?= (!empty($notice)) ? '<div class="notice"><div class="message-box-wrap">'.$notice.'</div></div>' : '' ?>
                  <div class="Panel__Container-hCUKEb ejcVRF">
                    <div class="Accounts__Header-kUlsOz hjYHLC Flex__Flex-fVJVYW hQXxaf">
                      <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">
                        <span>Your Withdrawal transactions
                        </span>
                      </h4>
                      <span style="float:right">
                          <a href="javascript:void(0);"  id="myBtn" class="gVLEBI dummy ">Withdraw Currency</a>
                          <a href="bank-accounts" class="gVLEBI dummy ">Manage Bank accounts</a>
                        </span>
                        
                    </div>
                    <div class="Accounts__HeightWrapper-bIafrT bjqSJc Flex__Flex-fVJVYW iJJJTg">
                      <div class="Accounts__AccountListWrapper-hJZLkd fzpAGl Flex__Flex-fVJVYW iDqRrV">
                        <div class="AccountList__AccountsList-eEZOms jZdQTT Flex__Flex-fVJVYW gkSoIH">
                          <a class="AccountList__AccountLink-cGluzb bnXidV">
                            <div class="AccountListItem__Account-laXKDv jIylJE Flex__Flex-fVJVYW iDqRrV">
                              <div class="AccountListItem__SelectedIndicator-dpXoDO eaiFtd Flex__Flex-fVJVYW iDqRrV">
                              </div>
                              <div class="AccountListItem__ContentWrap-kSwyDk koXqeq Flex__Flex-fVJVYW iJJJTg">
                                <div class="AccountListItem__Icon-jlvqZo bEvKsN Flex__Flex-fVJVYW iDqRrV">
                                  <img src="images/dollar.png" style="width:35px; height:35px;">
                                </div>
                                <div class="Flex__Flex-fVJVYW iJJJTg">
                                  <div class="AccountListItem__Details-cWizxw GENJj Flex__Flex-fVJVYW bHipRv">
                                    <span class="EditableAccountName__AccountName-eWmPDg IEDDp Text__Font-jgIzVM cXMret" color="slateDark">USD Wallet
                                    </span>
                                    <div>
                                      <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
                                        <span>
                                        $ <?= Stringz::currency($user_available[
                                          'USD'
                                        ]) ?>
                                        </span>
                                      </span>
                                    </div>
                                    <div class="AccountListItem__Actions-bsqZNF fNrJZo Flex__Flex-fVJVYW iDqRrV">
                                      <div class="Flex__Flex-fVJVYW ghkoKS">
                                        <button onclick="location.href='deposit'"  class="AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI" id="deposite">
                                          <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="13" viewBox="0 0 11 13" class="AccountActionButtons__DepositIcon-eHtQuS kxlzLR">
                                              <path d="M5.5 9.75a.7.7 0 0 0 .55-.24l4.48-4.63-1.1-1.14-3.14 3.25V0H4.7v6.99L1.57 3.74.47 4.87l4.48 4.64a.7.7 0 0 0 .55.24zM0 11.38h11v1.63H0z">
                                              </path>
                                            </svg>
                                            <span>Deposit
                                            </span>
                                          </div>
                                        </button>
                                        <button  onclick="location.href='withdraw'" class="myBtn dummy AccountActionButtons__AccountButton-ejRNLb hOLrOE Button__Container-hQftQV gVLEBI" id="withdraw">
                                          <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="13" viewBox="0 0 11 13" class="AccountActionButtons__WithdrawIcon-cEXuOU enflJl">
                                              <path d="M5.5 0c.24 0 .4.08.55.24l4.48 4.63-1.1 1.14-3.14-3.25v6.99H4.7V2.76L1.57 6.01.47 4.88 4.95.24A.7.7 0 0 1 5.5 0zM0 11.38h11v1.63H0z">
                                              </path>
                                            </svg>
                                            <span>Withdraw
                                            </span>
                                          </div>
                                        </button>
                                      </div>
                                      <!-- <div class="MenuButton__Container-eEqVio cCtppT">
                                        <button class="Button__Container-hQftQV gVLEBI">
                                          <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="3" viewBox="0 0 12 3" class="AccountListItem__MoreIcon-bhuCDT jHWdbr">
                                              <path fill-rule="evenodd" d="M0 1.1C0 .8.11.53.33.31.55.11.8 0 1.12 0c.3 0 .56.1.78.3a1.04 1.04 0 0 1 0 1.53c-.2.22-.47.33-.78.33a1.22 1.22 0 0 1-.78-.3A1.04 1.04 0 0 1 0 1.08zm4.66 0c0-.3.11-.56.33-.78.22-.21.48-.32.8-.32.3 0 .55.1.78.3.22.21.33.47.33.76 0 .3-.1.56-.33.77a1.1 1.1 0 0 1-1.22.24A1.22 1.22 0 0 1 5 1.85a1.04 1.04 0 0 1-.34-.76zm4.66 0c0-.3.11-.56.33-.78.22-.21.49-.32.8-.32.3 0 .55.1.78.3.22.21.33.47.33.76 0 .3-.1.56-.33.77-.21.22-.48.33-.79.33a1.22 1.22 0 0 1-.78-.3 1.04 1.04 0 0 1-.34-.77z">
                                              </path>
                                            </svg>
                                          </div>
                                        </button>
                                        <div>
                                          <div class="DropdownMenu__Wrapper-ftzJux kDYzjU">
                                            <div class="DropdownMenu__Overlay-fkiKFB bwjNFz">
                                            </div>
                                            <div class="DropdownMenu__Content-hEqeZW eShXlj">
                                              <div class="DropdownItem__Wrapper-cbEAmf eBHdBb Flex__Flex-fVJVYW gpfewV">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="19" viewBox="0 0 18 19" class="AccountActionsMenu__QRIcon-jccpKP ePbcro">
                                                  <path d="M6.55 2.2H2v4.55h4.55V2.21zM0 8.76V.21h8.55v8.54H0zm16-6.54h-4.55v4.54H16V2.21zM9.45 8.75V.21H18v8.54H9.45zm-2.9 2.91H2v4.55h4.55v-4.55zM0 18.21V9.66h8.55v8.55H0zM12.18 11.84V9.66h-2v4.18h4.91v-2.18H16v3.64h2V9.66h-4.9v2.18zM12.18 14.75h-2v3.46H18v-2h-5.82z">
                                                  </path>
                                                  <path d="M3.1 5.64V3.32h2.34v2.32zM12.56 5.64h2.33V3.32h-2.33zM3.1 15.1v-2.33h2.34v2.33z">
                                                  </path>
                                                </svg>
                                                <span class="Text__Font-jgIzVM gxSINW" color="slateDark">
                                                  <span>Bitcoin Address
                                                  </span>
                                                </span>
                                              </div>
                                              <div class="DropdownItem__Wrapper-cbEAmf eBHdBb Flex__Flex-fVJVYW gpfewV">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="AccountActionsMenu__EditIcon-foMaEM gQzZCC">
                                                  <path d="M8.1 3.5L.3 11.3c-.2.2-.3.4-.3.7v3c0 .6.4 1 1 1h3c.3 0 .5-.1.7-.3l7.8-7.8-4.4-4.4zm7.6-.2l-3-3c-.4-.4-1-.4-1.4 0L9.5 2.1l4.4 4.4 1.8-1.8c.4-.4.4-1 0-1.4z">
                                                  </path>
                                                </svg>
                                                <span class="Text__Font-jgIzVM gxSINW" color="slateDark">
                                                  <span>Rename Account
                                                  </span>
                                                </span>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div> -->
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </a>
                          
                        </div>
                        
                        <div class="Accounts__FooterLink-bzmsMV kjoxfT Flex__Flex-fVJVYW hBrjIA" style="display:none;">
                       
                          
                        </div>

                      </div>
                      <div class="Accounts__AccountDetailsContainer-gmlBsr eCaGZQ Flex__Flex-fVJVYW bHipRv">
                        <div class="TransactionList__Container-hwtrOD hhOcaD Flex__Flex-fVJVYW bHipRv">
                          <div class="TransactionList__SearchBar-gjttrf eLRdEm Flex__Flex-fVJVYW reCYb">
                            <span class="Text__Font-jgIzVM gJaRtZ" color="slateDark">
                              <span><?= $page_title ?>
                              </span>
                            </span>
                            <!-- <div class="TransactionList__SearchWrapper-kKeJba iNsmUo Flex__Flex-fVJVYW iDqRrV">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" class="TransactionList__SearchIcon-klWtWk bffLaf">
                                <path d="M12.7 11.23A7.02 7.02 0 0 0 7.1 0 7.1 7.1 0 0 0 0 7.06a7.1 7.1 0 0 0 11.3 5.66l3 2.98c.2.2.5.3.7.3.2 0 .5-.1.7-.3.4-.4.4-1 0-1.39l-3-3.08zm-5.6.8c-2.8 0-5.1-2.2-5.1-4.97a5.1 5.1 0 0 1 10.2 0c0 2.78-2.3 4.96-5.1 4.96z">
                                </path>
                              </svg>
                              <div class="TransactionList__SearchInput-dFfUXN gCXeDQ Input__Container-evMrUq gtgZVV Flex__Flex-fVJVYW iJJJTg">
                                <input class="Input__InputField-gFkBsN bZaCwQ" placeholder="Search" value="" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                              </div>
                            </div> -->
                          </div>
                          <div class="TransactionList__ScrollContainer-PYBhA ICfmD Flex__Flex-fVJVYW bHipRv">
                            <?
if ($requests) {
    foreach ($requests as $request) {
        ?>
                            <div class="TransactionListItem__LinkContainer-dcvOgD cNJUjb Flex__Flex-fVJVYW hBrjIA">
                              <div class="Flex__Flex-fVJVYW reCYb">
                                <div class="TransactionListItem__DateContainer-gqotRI eoNrlE Flex__Flex-fVJVYW jmXzPI">
                                  <div class="TransactionListItem__DateMonth-jfcbZP iKLMhg Flex__Flex-fVJVYW iDqRrV">
								 <?php $d = date_create($request['date']);
        echo date_format($d, "M"); ?>
                                  </div>
                                  <div class="TransactionListItem__DateDay-eNBQyR gHBIFB Flex__Flex-fVJVYW iDqRrV">
                                  <?php echo date_format($d, "d"); ?>
                                  </div>
                                </div>
                                <div class="TransactionListItem__IconWrapper-ddOKma cJShLP Flex__Flex-fVJVYW reCYb">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="TransactionIcon-dYHsTi dzGsNa" currency="BTC">
                                    <g fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round" transform="translate(1 1)">
                                      <circle cx="15" cy="15" r="15">
                                      </circle>
                                      <path d="M23.68 17.37H7.9l5.53 5.52M6.32 12.63H22.1L16.57 7.1">
                                      </path>
                                    </g>
                                  </svg>
                                </div>
                                <div class="Flex__Flex-fVJVYW jYOuLK">
                                  <div class="TransactionListItem__TransactionTitle-hrYDmM nDuUc Flex__Flex-fVJVYW iDqRrV"><?= (($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency((($request['net_amount'] > 0) ? $request['net_amount'] : ($request['amount'] - $request['fee'])),true).' '.$request['fa_symbol'] : $request['fa_symbol'].Stringz::currency((($request['net_amount'] > 0) ? $request['net_amount'] : ($request['amount'] - $request['fee'])))) ?>
                                  </div>
                                  <div class="TransactionListItem__TransactionSubtitle-CAWdv doKCBi Flex__Flex-fVJVYW reCYb"><?= (($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency($request['amount'],true).' '.$request['fa_symbol'] : $request['fa_symbol'].Stringz::currency($request['amount'])) ?>
                                  </div>
                                </div>
                              </div>
                              <div class="TransactionListItem__AmountContainer-vrpLr dThaic Flex__Flex-fVJVYW gkSoIH">
                                <div class="Flex__Flex-fVJVYW jvHpwe">
                                  <div class="TransactionListItem__TransactionTitle-hrYDmM nDuUc Flex__Flex-fVJVYW iDqRrV">
                                    <span>
                                      <!-- <img src="https://png.icons8.com/small/50/000000/rupee.png" style="width:13px;"> -->
                                      <?= Lang::string('deposit-status') ?>
                                    </span>
                                  </div>
                                </div>
                                <div class="Flex__Flex-fVJVYW jvHpwe">
                                  <div class="TransactionListItem__TransactionSubtitle-CAWdv doKCBi Flex__Flex-fVJVYW iDqRrV">
                                    <span>
                                      <!-- <img src="https://png.icons8.com/small/50/000000/rupee.png" style="width:13px;"> -->
                                      <?= $request['status'] ?>
                                    </span>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?
    }
} else {
    echo '<div style="padding:20% 35%;">No bank accounts found.</div>';
}
?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php include "includes/footer.php"; ?>
                <div class="Backdrop__LayoutBackdrop-eRYGPr cdNVJh">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div>
        </div>
      </div>
    </div>
    <script>
    </script>
    <script type="text/javascript" src="js/ops.js?v=20160210"></script>

    <script>
      $(document).ready(function(){
        $(".Header__DropdownButton-dItiAm").click(function(){
          $(".DropdownMenu__Wrapper-ieiZya.kwMMmE").toggleClass("show-menu");
        }
                                                 );
      }
                       );
      $(document).ready(function(){
        var modal = document.getElementById('myModal');
        var btn = document.getElementsByClassName("myBtn")[0];
        var btn1 = document.getElementById("myBtn")
        var span = document.getElementsByClassName("close")[0];
        <?php if (!empty($_REQUEST['action'])) {
    ?>
            modal.style.display = "block";
          <?}
?>
              btn.onclick = function() {
              modal.style.display = "block";
            }
            btn1.onclick = function() {
              modal.style.display = "block";
            }
            span.onclick = function() {
              modal.style.display = "none";
            }
            window.onclick = function(event) {
              if (event.target == modal) {
                modal.style.display = "none";
              }
            }
        }
        );
    </script>
