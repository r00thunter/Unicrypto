<?php
 error_reporting(E_ERROR | E_WARNING | E_PARSE);
 ini_set('display_errors', 1);
include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
	Link::redirect('userprofile');
elseif (User::$awaiting_token)
	Link::redirect('verify-token');
elseif (!User::isLoggedIn())
	Link::redirect('login');

// 	if(empty(User::$ekyc_data) || User::$ekyc_data[0]->status != 'accepted')
// {
//     Link::redirect('ekyc');
// }

if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']),$CFG->currencies)))
	$_SESSION['ba_c_currency'] = $_REQUEST['c_currency'];
else if (empty($_SESSION['ba_c_currency']))
	$_SESSION['ba_c_currency'] = $_SESSION['c_currency'];


$c_currency = $_SESSION['ba_c_currency'];
API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
API::add('Content','getRecord',array('bitcoin-addresses'));
$query = API::send();

$bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
$content = $query['Content']['getRecord']['results'][0];
$page_title = Lang::string('bitcoin-addresses');

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'add' && $_SESSION["btc_uniq"] == $_REQUEST['uniq']) {
	if (strtotime($bitcoin_addresses[0]['date']) >= strtotime('-1 day'))
		Errors::add('You can only add one new '.$CFG->currencies[$c_currency]['currency'] .' address every 24 hours.');
	
	if (!is_array(Errors::$errors)) {
		API::add('BitcoinAddresses','getNew',array($c_currency));
		API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
		$query = API::send();
		$bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
		
		Messages::add(Lang::string('bitcoin-addresses-added'));
		Link::redirect('bitcoin-addresses.php');

	}
}

$_SESSION["btc_uniq"] = md5(uniqid(mt_rand(),true));
// include 'includes/head.php';
?>
<!-- <div class="page_title">
	<div class="container">
		<div class="title"><h1><?= $page_title ?></h1></div>
        <div class="pagenation">&nbsp;<a href="index.php"><?= Lang::string('home') ?></a> <i>/</i> <a href="account.php"><?= Lang::string('account') ?></a> <i>/</i> <a href="bitcoin-addresses.php"><?= $page_title ?></a></div>
	</div>
</div>
<div class="container">
	<div class="content_right">
    	<div class="text"><?= $content['content'] ?></div>
    	<div class="clearfix mar_top2"></div>
    	<div class="clear"></div>
    	<? Errors::display(); ?>
    	<? Messages::display(); ?>
    	<div class="clear"></div>
    	<div class="filters">
	    	<ul class="list_empty">
	    		<li>
	    			<label for="c_currency"><?= Lang::string('currency') ?></label>
	    			<select id="c_currency">
	    			<? 
					foreach ($CFG->currencies as $key => $currency1) {
						if (is_numeric($key) || $currency1['is_crypto'] != 'Y')
							continue;
						
						echo '<option value="'.$currency1['id'].'" '.($currency1['id'] == $c_currency ? 'selected="selected"' : '').'>'.$currency1['currency'].'</option>';
					}
					?>
	    			</select>
	    		</li>
				<li><a href="bitcoin-addresses.php?action=add&c_currency=<?= $c_currency ?>&uniq=<?= $_SESSION["btc_uniq"] ?>" class="but_user"><i class="fa fa-plus fa-lg"></i> <?= Lang::string('bitcoin-addresses-add') ?></a></li>
			</ul>
		</div>
		<div id="filters_area">
	    	<div class="table-style">
	    		<table class="table-list trades">
					<tr>
						<th><?= Lang::string('currency') ?></th>
						<th><?= Lang::string('bitcoin-addresses-date') ?></th>
						<th><?= Lang::string('bitcoin-addresses-address') ?></th>
					</tr>
					<? 
					if ($bitcoin_addresses) {
						foreach ($bitcoin_addresses as $address) {
					?>
					<tr>
						<td><?= $CFG->currencies[$address['c_currency']]['currency'] ?></td>
						<td><input type="hidden" class="localdate" value="<?= (strtotime($address['date']) + $CFG->timezone_offset) ?>" /></td>
						<td><?= $address['address'] ?></td>
					</tr>
					<?
						}
					}
					else {
						echo '<tr><td colspan="3">'.str_replace('[c_currency]',$CFG->currencies[$c_currency]['currency'],Lang::string('bitcoin-addresses-no')).'</td></tr>';
					}
					?>
				</table>
			</div>
		</div>
    </div>
	<div class="clearfix mar_top8"></div>
</div> -->











<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/new-style.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<style type="text/css" data-styled-components="cZxgpV iJJJTg bHipRv fWgtDL kSAvah htCQOP bggzhW jHhVVr imiCeQ cUBSWS gpfewV gzmQji jMrxtq fQHoSi kwMMmE fMiZdp gkSoIH hZbtHZ jmXzPI cdGmEJ dBgvfG gJSYCj jBmRma dyDdjC hBrjIA ihpAbH gJbZHL iXnAHY dzohzs hruEfI cFMUnB iDqRrV eVuugS gUAdiw flJTLp jqhZyV cpsCBW cUpCZC ghkoKS faverQ hQXxaf reCYb THIro guwUit cdNVJh cvPMrQ gcaWCM gmOPIV dQBmZw hvidLq hBiYjT iQMzvE hyoBQr doTYNU cIjqSE gsOGkq jGNjWx dPenpn fIpMDl jvHpwe iBOmIt jYOuLK RXWtZ kJofUt gwzCiY LxVbQ jakknY icsYhc dJgHtE jSyChH kxBzvP fboOWG dhIyk kZBVvC aApUU cFkyRu"
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
height: 70px;
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
padding: 15px 25px;
font-size: 18px;
border: 1px solid #2E7BC4;
background-color: #3C90DF;
border: 1px solid #2E7BC4;
background-color: #3C90DF;
opacity: 0.5;
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
</style>
<style type="text/css" data-styled-components="gCVQUv WhXLX cpwUZB fWIqmZ bRMwEm emZeiu fTUMdy kmYTnN klDPGy bsspIM dVuYMi cGVJJy gSHJhw kxbyEA gkzfZl iOGmBb gkEpki hwfHDH jQqaGc uKBRe bxGiua jiMbBQ kbqVDF bNXXSQ gBskIE jdlzFZ eUEQWj bGAtDj guNrkG kigJcx iHOEuK cHdqpn"
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
<style type="text/css" data-styled-components="fkKUHd gaOoIW eopEKS bBFStv gLGtWx cawfqU bggHeP gzkfOx blbMMz ZJjdO gJaRtZ bZSaVE vbPXh bpZMcw fSWgoh dRxFxx hXuKUF IDahi fSSVge kSqzOS hZFMLT fsuujI bDSDMM cgqGbT gWAHYV cZjfCo eJvkmu egBQEa bsumUB hlRAYZ RQsvY hiDTSU"
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
height: 361px;
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
</style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true"></style>
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
padding: 7px;
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
height: 27px;
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
<style type="text/css" data-styled-components="" data-styled-components-is-local="true"></style>
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
<style type="text/css" data-styled-components="ciNoGH bGBUHV bojYQs gOQkOA" data-styled-components-is-local="true">
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
border-bottom:
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
min-width: 34px;
margin-top: 5px;
margin-right: 20px;
text-align: center;
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
width: 278px;
opacity: 1;
-webkit-transition: opacity ease 0.75s;
transition: opacity ease 0.75s;
}
/* sc-component-id: TradeFormTabContainer__Content-bTJPSU */

.TMIzi {
min-height: 200px;
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
padding-left: 20px;
padding-right: 20px;
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
/* responsive css */
@media (max-width: 991px){
.cHdqpn,.bRMwEm,.gACRnh,.lmWelJ,.htCQOP,.cFMUnB   {
max-width: 1180px;
width: 100%;
} 
.PriceChart__Container-klmtfG.fkKUHd{
margin-top: 3em;
}
.PriceChart__ChartAxisContainer-jFZAnB.fSSVge.Flex__Flex-fVJVYW.gwzCiY,
.PriceChart__ChartAxisContainer-jFZAnB.fSSVge.Flex__Flex-fVJVYW.LxVbQ{
flex-direction: row;
}
.Flex__Flex-fVJVYW .iJJJTg{
overflow-y: auto;
}
.LayoutDesktop__Wrapper-ksSvka.fWIqmZ.Flex__Flex-fVJVYW.cpsCBW{
padding: 0;
}
.BalanceRow__Wrapper-GRndq.csnBXu.Flex__Flex-fVJVYW.hQXxaf{
padding: 30px;
}
.gsOGkq{
flex-direction: column;
}
.Dashboard__DashPanel-hIpZDh.fEfrBY,.fEfrBY, .fSSVge {
width: 100% !important;
}
.iJJJTg,.bHipRv {
-webkit-flex-direction: column;
-ms-flex-direction: column;
flex-direction: column;
}
.jZdQTT{
width: 100%;
}
.klDPGy {
max-width: 1180px;
}
.cFMUnB {
max-width: 1180px;
}
.htCQOP {
max-width: 1180px;
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
}
.Accounts__Container-cJqPrg.fWIqmZ{
padding:0 10px;
}
.Footer__Wrapper-jgZZNA.kmYTnN{
height: initial;
max-height: initial;
padding: 1em;
}
.Footer__Content-kfdTYL.hQXxaf{
flex-direction: column;
align-items: flex-start;
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
.cHdqpn{
max-width: 1180px;
width: 100%;
margin-bottom: 20px;
}
.eMNjQO{
max-width: 578px;
width: 100%;
}
.kPUfcv{
width: 100%;
margin-top:2em;
}
.Trade__Preview-ftIHSO.iDqRrV,
.Trade__Preview-ftIHSO.kkSOOR.Flex__Flex-fVJVYW.iDqRrV,.iDqRrV{
flex-direction: column;
}
.TotalsRow__Row-QBNEe.bPDeSX.Flex__Flex-fVJVYW.iDqRrV .gcCxss{
margin-bottom: 5px;
}

.TotalsRow__Row-QBNEe.bPDeSX.Flex__Flex-fVJVYW.iDqRrV .dokghr{
margin-bottom: 10px;
padding-left: 0;
}
.Navbar__LinkContent-fFVkWH.jqhZyV.Flex__Flex-fVJVYW.iDqRrV,
.Navbar__LinkContent-fFVkWH.gUAdiw.Flex__Flex-fVJVYW.iDqRrV{
flex-direction: initial !important;
}
.ConfirmDiagram__Wrapper-jnqmHk.djoBUG.Flex__Flex-fVJVYW.cFkyRu{
flex-direction: row;
}

.TradeSection__Wrapper-jIpuvx.bskbTZ .SelectList__Wrapper-hHZYYo{
margin-bottom: 1em;
}
.TradeSection__Wrapper-jIpuvx.bskbTZ .fNPzZN{
width: 100%;
border: 14px solid #DAE1E9;
border-radius: 0;
}
.Footer.Content-kfdTYL.klDPGy,
.klDPGy{
width: 100%;
}
.TradeFormTabContainer__Content-bTJPSU h4.Heading__StyledHeading-sALAQ.hwfHDH{
margin-top: 2em;
}
}
</style>
<style type="text/css" data-styled-components="jkvMB hNLWXE eDmWpK hihUrD fkZQvs kkSOOR ksjufd kXusyX eezuXj" data-styled-components-is-local="true">
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
min-height: 400px;
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
</style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true"></style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true"></style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true"></style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true"></style>
<style type="text/css" data-styled-components="" data-styled-components-is-local="true"></style>
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
<style type="text/css" data-styled-components="bgdsDH kmGoDe ediliA fPzmyn gFNfZa huTMcA llqTCK eaRwjt btDRbj exnUIm jGCZMs ccIZQY bdBCxn hAYUyK hUePnF hFKNhN deisrS eOznwO cryMpR kxILlN haAoJJ fHcfYp CmCWI VGQlh" data-styled-components-is-local="true">
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

.eyRwnK {
width: 16px;
height: 16px;
margin-right: 10px;
fill: currentColor;
}

@media (max-width: 991px) {
.cHdqpn,
.bRMwEm,
.gACRnh,
.lmWelJ,
.htCQOP,
.cFMUnB {
max-width: 1180px;
width: 100%;
}
.Flex__Flex-fVJVYW .iJJJTg {
overflow-y: auto;
}
.LayoutDesktop__Wrapper-ksSvka.fWIqmZ.Flex__Flex-fVJVYW.cpsCBW {
padding: 0;
}
.BalanceRow__Wrapper-GRndq.csnBXu.Flex__Flex-fVJVYW.hQXxaf {
padding: 30px;
}
.gsOGkq {
flex-direction: column;
}
.Dashboard__DashPanel-hIpZDh.fEfrBY,
.fEfrBY,
.fSSVge {
width: 100% !important;
}
.iJJJTg,
.bHipRv {
-webkit-flex-direction: column;
-ms-flex-direction: column;
flex-direction: column;
}
.jZdQTT {
width: 100%;
}
.klDPGy {
max-width: 1180px;
}
.cFMUnB {
max-width: 1180px;
}
.htCQOP {
max-width: 1180px;
}
.AccountListItem__Icon-jlvqZo.bEvKsN {
margin: auto;
}
.Navbar__Content-cgqezH.cFMUnB {
flex-direction: column;
padding: .4em 0 1em;
}
.Navbar__DesktopWrapper-jiGyXa.dzohzs {
flex: auto;
}
.Navbar__LinkContainer-jXaDVl.hBrjIA {
margin: 10px 15px;
}
.Navbar__LinkContainer-jXaDVl.hBrjIA:first-child {
margin-left: 15px;
}
.TransactionListItem__LinkContainer-dcvOgD.cNJUjb {
padding: 3px;
}
.Footer__Right-iFQtJS.reCYb {
flex-direction: column;
align-items: flex-start;
}
.AccountListItem__Details-cWizxw.GENJj {
padding: 8px;
}
.LayoutDesktop__Content-flhQBc.kuJaHF {
width: 100%;
}
.Accounts__Container-cJqPrg.fWIqmZ {
padding: 0 10px;
}
.Footer__Wrapper-jgZZNA.kmYTnN {
height: initial;
max-height: initial;
padding: 1em;
width: 100%;
background: #fff;
}
.Footer__Content-kfdTYL.hQXxaf {
flex-direction: column;
align-items: flex-start;
}
.Footer__Link-hmaedR.dVuYMi {
display: inline-block;
padding: 10px 0;
margin-left: 0 !important;
}
.Footer__Right-iFQtJS .gSHJhw,
.Footer__Right-iFQtJS .kxbyEA,
.Footer__Right-iFQtJS .Footer__NeedHelp-kNHURG {
display: inline-block;
padding: 10px 0;
}
.TransactionListItem__LinkContainer-dcvOgD .nDuUc {
font-size: 15px;
}
.TransactionListItem__LinkContainer-dcvOgD.cNJUjb {
min-height: 125px;
}
.Header__Wrapper-cwuouQ.fWgtDL {
padding-right: 15px;
}
.cHdqpn {
max-width: 1180px;
width: 100%;
margin-bottom: 20px;
}
.eMNjQO {
max-width: 100%;
width: 100%;
}
.kPUfcv {
width: 100%;
margin-top: 2em;
}
.Trade__Preview-ftIHSO.iDqRrV,
.Trade__Preview-ftIHSO.kkSOOR.Flex__Flex-fVJVYW.iDqRrV,
.iDqRrV {
flex-direction: column;
}
.PriceChart__ChartAxis-hnvXTZ.kSqzO.Flex__Flex-fVJVYW.iDqRrV,
.PriceChart__ChartAxis-hnvXTZ.kSqzOS.Flex__Flex-fVJVYW.iDqRrV{
display: inline;
}
.TotalsRow__Row-QBNEe.bPDeSX.Flex__Flex-fVJVYW.iDqRrV .gcCxss {
margin-bottom: 5px;
}
.TotalsRow__Row-QBNEe.bPDeSX.Flex__Flex-fVJVYW.iDqRrV .dokghr {
margin-bottom: 10px;
padding-left: 0;
}
.Navbar__LinkContent-fFVkWH.jqhZyV.Flex__Flex-fVJVYW.iDqRrV,
.Navbar__LinkContent-fFVkWH.gUAdiw.Flex__Flex-fVJVYW.iDqRrV {
flex-direction: initial !important;
}
.ConfirmDiagram__Wrapper-jnqmHk.djoBUG.Flex__Flex-fVJVYW.cFkyRu {
flex-direction: row;
}
.TradeSection__Wrapper-jIpuvx.bskbTZ .SelectList__Wrapper-hHZYYo {
margin-bottom: 1em;
}
.TradeSection__Wrapper-jIpuvx.bskbTZ .fNPzZN {
width: 100%;
border: 14px solid #DAE1E9;
border-radius: 0;
}
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
    padding: 0px;
    margin: 0px;
    width: 100%;
}

ul {
    list-style: none;
}
.list_empty li {
    float: left;
    padding: 0px;
    margin: 0px 20px 35px 0px;
}

.messages {
    background-color: #DFFBE4;
    border: #A9ECB4 1px solid;
    color: #1EA133;
}
.errors, .messages {
    margin-bottom: 20px;
    padding: 10px 10px 5px 10px;
}
.messages li {
    border: 0 solid #FFFFFF;
    padding-bottom: 5px;
}
.but_user {
    padding: 10px 22px 10px 22px;
    margin: 0px 0px 0px 0px;
    background: #f4ba2f;
    color: #fff;
    border-top: none;
    border-left: none;
    border-right: none;
    font-weight: 500;
    cursor: pointer;
    border-radius: 4px;
}

.errors {
    background-color: #FFDDDD;
    border: #F1BDBD 1px solid;
    color: #BD6767;
	position: relative;
	right: 0px !important;
}

.errors, .messages {
    margin-bottom: 20px;
    padding: 10px 10px 5px 10px;
}
</style>

<?php 

?>

<meta name="viewport" content="width=device-width, initial-scale=1.0" data-react-helmet="true">
</head>

<body >
<div id="root">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Toasts__Container-kTLjCb jeFCaz"></div>
<div class="Layout__Container-jkalbK gCVQUv Flex__Flex-fVJVYW bHipRv">
<div class="LayoutDesktop__AppWrapper-cPGAqn WhXLX Flex__Flex-fVJVYW bHipRv">
<? include 'includes/topheader.php'; ?>
<div class="LayoutDesktop__ContentContainer-cdKOaO cpwUZB Flex__Flex-fVJVYW bHipRv">


<? include 'includes/menubar.php'; ?>





<div class="LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">






<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">





<div class="Trade__TradeView-eeHZtW jkvMB Flex__Flex-fVJVYW iDqRrV">
<div class="Trade__TradeFormContainer-lhJJLd hNLWXE Flex__Flex-fVJVYW iDqRrV">




<div class="LayoutDesktop__Content-flhQBc cHdqpn Flex__Flex-fVJVYW gkSoIH">

<? Messages::display(); ?>
		<? Errors::display(); ?>

<? if($c_currency != 45){ echo $content['content'];  }?>


<div class="filters">
	    	<ul class="list_empty">
	    		<li style="float: none;max-width: 300px;">
	    			<label for="c_currency"><?= Lang::string('currency') ?></label>
	    			<select id="c_currency" class="form-control" style="    margin-top: 1em;height: 40px;">
	    			<? 
					foreach ($CFG->currencies as $key => $currency1) {
						if (is_numeric($key) || $currency1['is_crypto'] != 'Y')
							continue;
						
						echo '<option value="'.$currency1['id'].'" '.($currency1['id'] == $c_currency ? 'selected="selected"' : '').'>'.$currency1['currency'].'</option>';
					}
					?>
	    			</select>
	    		</li>
				<? if($c_currency != 45 || ($c_currency == 45 && empty($bitcoin_addresses)) ) { ?>
                    <li style="float: none;"><a href="bitcoin-addresses.php?action=add&c_currency=<?= $c_currency ?>&uniq=<?= $_SESSION["btc_uniq"] ?>" class="but_user"><i class="fa fa-plus fa-lg"></i> <?= Lang::string('bitcoin-addresses-add') ?></a></li>
                <? } ?>

			</ul>
		</div>

<div class="Flex__Flex-fVJVYW gsOGkq" style="border: 1px solid #DAE1E9;">

<div class="Dashboard__DashPanel-hIpZDh fEfrBY Panel__Container-hCUKEb gmOPIV" style="border:none; width:100%; margin-bottom:0px;">
<div class="Flex__Flex-fVJVYW bHipRv">
    <div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
        <div class="Flex__Flex-fVJVYW iDqRrV">


            <h4 class="Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">
            <?= $page_title ?>
            </h4>
        </div>
        <div class="WidgetHeader__Actions-bDbtim jQqaGc">
            <div class="Flex__Flex-fVJVYW iDqRrV">

            
            </div>
        </div>
    </div>
    <div class="Flex__Flex-fVJVYW iJJJTg">
         <div class="table-otr">
          <table id="transactions_list">
            <tr>
			<th><?= Lang::string('currency') ?></th>
						<th><?= Lang::string('bitcoin-addresses-date') ?></th>
						<th><?= Lang::string('bitcoin-addresses-address') ?></th>
			</tr>
			<? 
					if ($bitcoin_addresses) {
						foreach ($bitcoin_addresses as $address) {
					?>
					<tr>
						<td><?= $CFG->currencies[$address['c_currency']]['currency'] ?></td>
						<td><input type="hidden" class="localdate" value="<?= (strtotime($address['date']) + $CFG->timezone_offset) ?>" /></td>
						<td><?= $address['address'] ?></td>
					</tr>
					<?
						}
					}
					else {
						echo '<tr><td colspan="3"><div class="" style=" text-align:  center;
						"><img src="images/no-results.gif" style="width: 300px;height: auto;" ></div></td></tr>';
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
</div>
<div class="Footer__Wrapper-jgZZNA kmYTnN Flex__Flex-fVJVYW ghkoKS">
<div class="Footer__Content-kfdTYL klDPGy Flex__Flex-fVJVYW hQXxaf">
<div class="Footer__Links-jOwZdP bsspIM Flex__Flex-fVJVYW iJJJTg">
<a class="Footer__Link-hmaedR dVuYMi" href="/home">
<span>Home</span>
</a>
<a class="Footer__Link-hmaedR dVuYMi" href="/careers">
<span>Careers</span>
</a>
<a class="Footer__Link-hmaedR dVuYMi" href="/legal/user_agreement">
<span>Legal &amp; Privacy</span>
</a>
</div>
<div class="Footer__Right-iFQtJS cGVJJy Flex__Flex-fVJVYW reCYb">
<div class="Footer__Copyright-eTGlms gSHJhw">2018 <?= $CFG->exchange_name; ?></div>
<!-- <div class="Footer__SelectWrapper-fEXIsO kxbyEA">
<select class="Select__SelectWrapper-fXiYlv THIro" name="locale">
    <option value="de">English</option>
    <option value="en">English</option>
    <option value="en-US">English - United States</option>
    <option value="es">Español</option>
    <option value="es-mx">Español - Méjico</option>
    <option value="fr">Français</option>
    <option value="id">bahasa Indonesia</option>
    <option value="it">Italiano</option>
    <option value="nl">Nederlands</option>
    <option value="pt">Português</option>
    <option value="pt-br">Português - Brazil</option>
</select>
</div> -->
<div class="Footer__NeedHelp-kNHURG gkzfZl" href="">
<button class="Button__Container-hQftQV guwUit">
    <div class="Button__Content-eaBvLU iOGmBb Flex__Flex-fVJVYW iDqRrV">
        <span>Need Help?</span>
    </div>
</button>
</div>
</div>
</div>
</div>
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
<script type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.time.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.crosshairs.js"></script>
<script type="text/javascript" src="js/flot/jquery.flot.candle.js"></script>









