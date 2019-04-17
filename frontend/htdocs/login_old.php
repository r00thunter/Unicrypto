<?php
// error_reporting(E_ALL); 
// ini_set('display_errors', 'On');
include '../lib/common.php';
if (User::isLoggedIn())
	Link::redirect('dashboard');
$page_title = Lang::string('home-login');
// $user1 = (!empty($_REQUEST['login']['user'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['login']['user']) : false;
$user1 = $_REQUEST['login']['user'];
$pass1 = (!empty($_REQUEST['login']['pass'])) ? preg_replace($CFG->pass_regex, "",$_REQUEST['login']['pass']) : false;

if (!empty($_REQUEST['submitted'])) {
	if (empty($user1)) {
		Errors::add(Lang::string('login-user-empty-error'));
	}

	if (empty($pass1)) {
		Errors::add(Lang::string('login-password-empty-error'));
	}
	
	// if (!empty($_REQUEST['submitted']) && (empty($_SESSION["register_uniq"]) || $_SESSION["register_uniq"] != $_REQUEST['uniq']))
	// 	Errors::add('Page expired.');
	
	if (!empty(User::$attempts) && User::$attempts > 3 && !empty($CFG->google_recaptch_api_key) && !empty($CFG->google_recaptch_api_secret)) {
		$captcha = new Form('captcha');
		$captcha->reCaptchaCheck(1);
		if (!empty($captcha->errors) && is_array($captcha->errors)) {
			Errors::add($captcha->errors['recaptcha']);
		}
	}
	
	if (!is_array(Errors::$errors)) {
		$login = User::logIn($user1,$pass1);
		if ($login && empty($login['error'])) {
			if (!empty($login['message']) && $login['message'] == 'awaiting-token') {
			    $_SESSION["register_uniq"] = md5(uniqid(mt_rand(),true));
				Link::redirect('verify-token');
			}
			elseif (!empty($login['message']) && $login['message'] == 'logged-in' && $login['no_logins'] == 'Y') {
			    $_SESSION["register_uniq"] = md5(uniqid(mt_rand(),true));
				Link::redirect('change-password');
			}
			elseif (!empty($login['message']) && $login['message'] == 'logged-in') {
 			    $_SESSION["register_uniq"] = md5(uniqid(mt_rand(),true));
				Link::redirect('dashboard');
			}
		}
		elseif (!$login || !empty($login['error'])) {
			Errors::add(Lang::string('login-invalid-login-error'));
		}
	}
}

if (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'registered')
	Messages::add(Lang::string('register-success'));

$_SESSION["register_uniq"] = md5(uniqid(mt_rand(),true));
// $user1 = 54235837 ;
// $pass1 = "bitExch@nge3623" ;
// include 'includes/head.php';
?>
<style>
.message-box-wrap
{
position: static !important;
    width: 100%;
    margin: 0;
    padding: 5 !important;
    background-color: #DFFBE4 !important;
    border: #A9ECB4 1px solid;
    color: #1EA133;
    box-shadow: none !important;
    }
    .messages {
    list-style-type: none !important;;
    padding: 15px 15px 0 !important;;
    border-radius: 3px !important;;
    position: relative !important;;
    font-size: 14px !important;;
    z-index: 999 !important;;
    background-color: transparent !important;
    box-shadow: none !important;
    right:0 !important;

}
</style>
<link href="css/new-style.css" rel="stylesheet" />
<!-- Favicon --> 
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-touch-icon1.png">
    <link rel="icon" type="image/png" href="images/favicon/CK Logo Small_Blue_sq_32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="images/favicon/CK Logo Small_Blue_sq_16.png" sizes="16x16">
<div class="new-login-otr">
	<div class="login-inner">
	<div class="header-bar">
		<a href=""><p style="display:  inline-block;color:  #fff;text-decoration:  none;font-size: 35px;margin: 7px 20px;">Bitexchange</p>
</a>
	<ul>
		<li><a href="login">Login</a></li>
		<li><a href="register">Sign up</a></li>
		<li><a href="forgot">Forgot password?</a></li>
	</ul>
	</div>
	<div class="header-content">
		<div class="header-content-inner">
			<h2>Login</h2>
			<? 
			if (count(Errors::$errors) > 0) {
				echo '<span style="display: inline-block;margin: 0 20px;font-size: 14px;color: red;">'.Errors::$errors[0].'</span>';
			}
			
			if (count(Messages::$messages) > 0) {
				echo '
			<div class="messages" id="div4">
				<div class="message-box-wrap">
					'.Messages::$messages[0].'
				</div>
			</div>';
			}
			?>
            <form method="POST" action="login.php" name="login">
			<div class="loginform">	
			<div class="loginform_inputs">		
			<div class="form-group">
				<input type="email" class="form-control" name="login[user]" value="<?= htmlspecialchars($user1) ?>" placeholder="Enter your email.." >
			</div>
			<div class="form-group">
				<input type="password" class="form-control" name="login[pass]" value="<?= htmlspecialchars($pass1) ?>" placeholder="Enter your Password..">
			</div>
			</div>
			<? if (!empty(User::$attempts) && User::$attempts > 2 && !empty($CFG->google_recaptch_api_key) && !empty($CFG->google_recaptch_api_secret)) { ?>
		    	<div style="margin-bottom:10px;">
		    		<div class="g-recaptcha" data-sitekey="<?= $CFG->google_recaptch_api_key ?>"></div>
		    	</div>
		    	<? } ?>
			<div class="form-group">
				<input type="hidden" name="submitted" value="1" />
	    		<input type="hidden" name="uniq" value="<?= $_SESSION["register_uniq"] ?>" />
				<button type="submit" class="kZBVvC"><?= Lang::string('home-login') ?></button>
			</div>
			</form>
		</div>
		</div>
	</div>
	</div>
</div>
