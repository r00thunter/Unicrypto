<?php
include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
	Link::redirect('userprofile.php');
elseif (User::$awaiting_token)
	Link::redirect('verify-token.php');
elseif (!User::isLoggedIn())
	Link::redirect('login.php');

$referer = substr($_SERVER['HTTP_REFERER'],strrpos($_SERVER['HTTP_REFERER'],'/')+1);
if ($referer == 'login.php' || $referer == 'verify-token.php' || $referer == 'first-login.php') {
	if (!empty(User::$info['default_currency']))
		$_SESSION['currency'] = User::$info['default_currency'];
	if (!empty(User::$info['default_currency']))
		$_SESSION['c_currency'] = User::$info['default_c_currency'];

	API::add('User','notifyLogin');
}
else if ($referer == 'first_login.php')
	API::add('User','disableNeverLoggedIn');

API::add('User','getOnHold');
API::add('User','getAvailable');
API::add('User','getVolume');
API::add('FeeSchedule','getRecord',array(User::$info['fee_schedule']));
API::add('Stats','getBTCTraded',array($_SESSION['c_currency']));
API::add('Currencies','getMain');
$query = API::send();

$currencies = $CFG->currencies;
$on_hold = $query['User']['getOnHold']['results'][0];
$available = $query['User']['getAvailable']['results'][0];
$volume = $query['User']['getVolume']['results'][0];
$fee_bracket = $query['FeeSchedule']['getRecord']['results'][0];
$total_btc_volume = $query['Stats']['getBTCTraded']['results'][0][0]['total_btc_traded'];
$main = $query['Currencies']['getMain']['results'][0];

if (!empty($_REQUEST['message'])) {
	if ($_REQUEST['message'] == 'settings-personal-message')
		Messages::add(Lang::string('settings-personal-message'));
}

$page_title = Lang::string('account');
include 'includes/head.php';
?>
<div class="page_title">
	<div class="container">
		<div class="title"><h1><?= $page_title ?></h1></div>
        <div class="pagenation">&nbsp;<a href="index.php"><?= Lang::string('home') ?></a> <i>/</i> <a href="account.php"><?= $page_title ?></a></div>
	</div>
</div>
<div class="container">
	<div class="content_right">
		<div class="testimonials-4">
			<? Messages::display(); ?>
			<? 
			if (User::$info['verified_authy'] != 'Y' && User::$info['verified_google'] != 'Y') {
				echo '<div class="notice"><div class="message-box-wrap">'.Lang::string('account-security-notify').'</div></div>';
			}
			?>
			<div class="mar_top2"></div>
			<ul class="list_empty">
				<li><a href="buy-sell.php" class="but_user"><i class="fa fa-btc fa-lg"></i> <?= Lang::string('buy-sell') ?></a></li>
				<li><a href="deposit.php" class="but_user"><i class="fa fa-download fa-lg"></i> <?= Lang::string('deposit') ?></a></li>
				<li><a href="withdraw.php" class="but_user"><i class="fa fa-upload fa-lg"></i> <?= Lang::string('withdraw') ?></a></li>
			</ul>
			<div class="clear"></div>
            <div class="content">
            	<h3 class="section_label">
                    <span class="left"><i class="fa fa-check fa-2x"></i></span>
                    <span class="right"><?= Lang::string('account-balance') ?></span>
                </h3>
                <div class="clear"></div>
                <div class="balances">
                	<div class="one_half">
                		<div class="label"><?= $CFG->currencies[$main['crypto']]['currency'].' '.Lang::string('account-available') ?></div>
                		<div class="amount"><?= Stringz::currency($available[$CFG->currencies[$main['crypto']]['currency']],true) ?></div>
                	</div>
	            	<?
	            	$i = 2;
	            	foreach ($available as $currency => $balance) {
						if ($currency == $CFG->currencies[$main['crypto']]['currency'])
							continue;
						
						$last_class = ($i % 2 == 0) ? 'last' : '';
						$is_crypto = ($CFG->currencies[$currency]['is_crypto'] == 'Y');
					?>
					<div class="one_half <?= $last_class ?>">
                		<div class="label"><?= $currency.' '.Lang::string('account-available') ?>:</div>
                		<div class="amount"><?= (!$is_crypto ? $CFG->currencies[$currency]['fa_symbol'].' ' : '').Stringz::currency($balance,$is_crypto) ?></div>
                	</div>
					<?
						$i++;
					} 
	            	?>
	            	<div class="clear"></div>
            	</div>
            	<div class="clear"></div>
            </div>
            <div class="mar_top3"></div>
            <div class="clear"></div>
            <div class="content">
            	<h3 class="section_label">
                    <span class="left"><i class="fa fa-exclamation fa-2x"></i></span>
                    <span class="right"><?= Lang::string('account-on-hold') ?></span>
                </h3>
                <div class="clear"></div>
                <div class="balances">
	            	<?
	            	if ($on_hold) {
	            		foreach ($on_hold as $currency => $balance) {
	            			if ($CFG->currencies[$currency]['id'] != $main['crypto'] && (empty($balance['order']) && empty($balance['withdrawal'])))
	            				continue;
	            			
	            			$is_crypto = ($CFG->currencies[$currency]['is_crypto'] == 'Y');
					?>
					<div class="one_half">
                		<div class="label"><?= $currency.' '.Lang::string('account-on-order') ?>:</div>
                		<div class="amount"><?= ((!$is_crypto) ? $CFG->currencies[$currency]['fa_symbol'] : '').(!empty($balance['order']) ? Stringz::currency($balance['order'],$is_crypto) : '0.00') ?></div>
                	</div>
                	<div class="one_half last">
                		<div class="label"><?= $currency.' '.Lang::string('account-on-widthdrawal') ?>:</div>
                		<div class="amount"><?= ((!$is_crypto) ? $CFG->currencies[$currency]['fa_symbol'] : '').(!empty($balance['withdrawal']) ? Stringz::currency($balance['withdrawal'],$is_crypto) : '0.00') ?></div>
                	</div>
					<?
						} 
					}
					else {
						echo Lang::string('account-nothing-on-hold');
					}
	            	?>
	            	<div class="clear"></div>
            	</div>
            	<div class="clear"></div>
            </div>
            <div class="mar_top3"></div>
            <div class="content1">
	            <h3 class="section_label">
					<span class="left"><i class="fa fa-info fa-2x"></i></span>
					<span class="right"><?= Lang::string('account-fee-structure') ?></span>
				</h3>
				<div class="clear"></div>
				<div class="balances">
					<div class="one_half">
						<div class="label"><?= Lang::string('account-fee-bracket1') ?>:</div>
						<div class="amount"><?= $fee_bracket['fee1'] ?>% <a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule.php"><i class="fa fa-question-circle"></i></a></div>
	                </div>
	                <div class="one_half last">
						<div class="label"><?= Lang::string('account-fee-bracket') ?>:</div>
						<div class="amount"><?= $fee_bracket['fee'] ?>% <a title="<?= Lang::string('account-view-fee-schedule') ?>" href="fee-schedule.php"><i class="fa fa-question-circle"></i></a></div>
	                </div>
	                <div class="one_half">
	                	<div class="label"><?= str_replace('[currency]',$CFG->currencies[$main['fiat']]['currency'],Lang::string('account-30-day-vol')) ?>:</div>
	                	<div class="amount"><?= $CFG->currencies[$main['fiat']]['fa_symbol'].Stringz::currency($volume / $CFG->currencies[$main['fiat']]['usd_ask']) ?></div>
	                </div>
		            <div class="clear"></div>
	            </div>
	            <div class="clear"></div>
            </div>
            <div class="mar_top8"></div>
        </div>
	</div>
	<? include 'includes/sidebar_account.php'; ?>
</div>
<? include 'includes/foot.php'; ?>

