<? $_SESSION["logout_uniq"] = md5(uniqid(mt_rand(),true));?>
<div class="Header__Wrapper-cwuouQ fWgtDL Flex__Flex-fVJVYW kSAvah" id="header">
<style>
.dyDdjC{
	height: auto !important;
	padding:0;
}
.dyDdjC a{
	display: inline-block;
	width: 100%;
	padding: 16px;
}
.Header__Content-dOLsDz a{
	border-left: 1px solid rgba(255, 255, 255, 0.1);
}
.Header__Content-dOLsDz a:first-child{
	border-left: none !important;
}
.Header__Content-dOLsDz a p{
	display:  inline-block;color:  #fff;text-decoration:  none;font-size: 16px;margin: 7px 20px;
}
.Header__Content-dOLsDz a p.logo-caption{
	display:  inline-block;color:  #fff;text-decoration:  none;font-size: 20px;margin: 7px 20px;
}
.dropdown {
    position: relative;
    display: inline-block;
}
.dropdown p{
	margin: 0;
}
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #fff;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 99;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {color: #f4ba2f;}

.dropdown:hover .dropdown-content {
    display: block;
}
.active-header{
    border-bottom: 2px solid #f4bb2e;
}
</style>
<div class="Header__Content-dOLsDz htCQOP Flex__Flex-fVJVYW bggzhW" style="justify-content: flex-start;">
<a href="<?php echo $CFG->baseurl; ?>">
<p class="logo-caption" style="color: #eeb62f;font-weight: 600;"><?= $CFG->exchange_name; ?></p>
</a>
<a href="dashboard">
<p style="">Dashboard</p>
</a>
<a href="userbuy?trade=BTC-USD">
<p style="">Simple Trade</p>
</a>
<a href="userexchange">
<p style="">Transactions</p>
</a>
<a href="userwallet">
<p style="">Crypto Wallet</p>
</a>
<a href="deposit">
<p style="">USD Wallet</p>
</a>

<a href="bitcoin-addresses" style="border-right: 1px solid rgba(255, 255, 255, 0.1);">
<p style="">Crypto Address</p>
</a>
<span style="position: relative;top: -4px;display: inline-block;padding-left: 15px;">
<div class="dropdown">
  <p class="dropbtn" style="color: #fff;font-size: 16px;"><?= User::$info['first_name'] ?>
  <svg style="width:24px;height:24px;position: relative;top: 6px;" viewBox="0 0 24 24">
    <path fill="#fff" d="M7,10L12,15L17,10H7Z" />
</svg></p>
  <div class="dropdown-content">
    <a href="userprofile"><i class="fa fa-user"></i> <?= Lang::string('settings') ?></a>
    <a href="logout.php?log_out=1&uniq=<?= $_SESSION["logout_uniq"] ?>"><i class="fa fa-unlock"></i> <?= Lang::string('log-out') ?></a>
  </div>
</div>
</span>
<!-- <div class="Header__Dropdown-kRsXac imiCeQ" role="button">
<div class="Header__DropdownButton-dItiAm cUBSWS Flex__Flex-fVJVYW gpfewV">
<img class="Avatar__AvatarImage-bFLlyY gzmQji" src="images/avatar.jpeg" size="32" role="presentation">
<div class="Header__Username-bPaLgO jMrxtq"><?= User::$info['first_name'] ?></div>
<svg xmlns="http://www.w3.org/2000/svg" width="9" height="6" viewBox="0 0 9 6" class="Header__DropdownArrow-cjWXoe fQHoSi">
<path d="M4.5 5.5a.5.5 0 0 1-.35-.14L0 1.4.7.5l3.8 3.62L8.3.5l.7.9-4.15 3.96a.5.5 0 0 1-.35.14z"></path>
</svg>
</div>
<div class="DropdownMenu__Wrapper-ieiZya kwMMmE">
<div class="DropdownMenu__Dropdown-kuxaaY fMiZdp Flex__Flex-fVJVYW gkSoIH">

<div class="DropdownMenu__Separator-cnqUyC jBmRma"></div>
<div class="DropdownMenu__DropdownLink-kJecXv dyDdjC Flex__Flex-fVJVYW hBrjIA">
<div class="DropdownMenu__Title-bLKAie ihpAbH">
	<a href="userprofile"><?= Lang::string('settings') ?></a>
	<a href="userprofile"><?= Lang::string('settings') ?></a>
</div>
</div>

<div class="DropdownMenu__Separator-cnqUyC jBmRma"></div>
<div class="DropdownMenu__DropdownLink-kJecXv dyDdjC Flex__Flex-fVJVYW hBrjIA">
<div class="DropdownMenu__Title-bLKAie ihpAbH"><a href="logout.php?log_out=1&uniq=<?= $_SESSION["logout_uniq"] ?>"><i class="fa fa-unlock"></i> <?= Lang::string('log-out') ?></a></div>
</div>
</div>
<div class="DropdownMenu__Overlay-kLnxWE iXnAHY Flex__Flex-fVJVYW hBrjIA"></div>
</div>
</div> -->
</div>

<input type="hidden" id="javascript_date_format" value="<?= Lang::string('javascript-date-format') ?>" />
<input type="hidden" id="javascript_mon_0" value="<?= Lang::string('jan') ?>" />
<input type="hidden" id="javascript_mon_1" value="<?= Lang::string('feb') ?>" />
<input type="hidden" id="javascript_mon_2" value="<?= Lang::string('mar') ?>" />
<input type="hidden" id="javascript_mon_3" value="<?= Lang::string('apr') ?>" />
<input type="hidden" id="javascript_mon_4" value="<?= Lang::string('may') ?>" />
<input type="hidden" id="javascript_mon_5" value="<?= Lang::string('jun') ?>" />
<input type="hidden" id="javascript_mon_6" value="<?= Lang::string('jul') ?>" />
<input type="hidden" id="javascript_mon_7" value="<?= Lang::string('aug') ?>" />
<input type="hidden" id="javascript_mon_8" value="<?= Lang::string('sep') ?>" />
<input type="hidden" id="javascript_mon_9" value="<?= Lang::string('oct') ?>" />
<input type="hidden" id="javascript_mon_10" value="<?= Lang::string('nov') ?>" />
<input type="hidden" id="javascript_mon_11" value="<?= Lang::string('dec') ?>" />
<input type="hidden" id="gmt_offset" value="<?= $CFG->timezone_offset ?>" />
<input type="hidden" id="is_logged_in" value="<?= User::isLoggedIn() ?>" />
<input type="hidden" id="cfg_orders_edit" value="<?= Lang::string('orders-edit') ?>" />
<input type="hidden" id="cfg_orders_delete" value="<?= Lang::string('orders-delete') ?>" />
<input type="hidden" id="cfg_user_id" value="<?= (User::isLoggedIn()) ? User::$info['user'] : '0' ?>" />
<input type="hidden" id="buy_errors_no_compatible" value="<?= Lang::string('buy-errors-no-compatible') ?>" />
<input type="hidden" id="orders_converted_from" value="<?= Lang::string('orders-converted-from') ?>" />
<input type="hidden" id="your_order" value="<?= Lang::string('home-your-order') ?>" />
<input type="hidden" id="order-cancel-all-conf" value="<?= Lang::string('order-cancel-all-conf') ?>" />
<input type="hidden" id="this_currency_id" value="<?= (!empty($currency_info)) ? $currency_info['id'] : 0 ?>" />
<input type="hidden" id="chat_handle" value="<?= (User::isLoggedIn()) ? User::$info['chat_handle'] : 'not-logged-in' ?>" />
<input type="hidden" id="chat_baseurl" value="<?= ($CFG->chat_baseurl) ? $CFG->chat_baseurl : $CFG->baseurl ?>" />
<input type="hidden" id="cfg_thousands_separator" value="<?= (!empty($CFG->thousands_separator)) ? $CFG->thousands_separator : ',' ?>" />
<input type="hidden" id="cfg_decimal_separator" value="<?= (!empty($CFG->decimal_separator)) ? $CFG->decimal_separator : '.' ?>" />
<input type="hidden" id="cfg_time_24h" value="<?= (!empty($CFG->time_24h)) ? $CFG->time_24h : 'N' ?>" />
<?= Lang::url(false,false,1); ?>
<?= Lang::jsCurrencies(false,false,1); ?>

</div>