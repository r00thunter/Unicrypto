<!doctype html>
<html>

<head>
<title>Profile <?= $CFG->exchange_name; ?></title>

<meta property="viewport" name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/new-style.css" rel="stylesheet" />
<link href="css/dashboard.css" rel="stylesheet" />
<link href="css/profile.css" rel="stylesheet" />
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
<style>
.messages{
    top: 7em;
}
.form_button{
    text-align: right ;
}
.form_button input{
    color: white ;
    background-color: #f4ba2f ;
    border-radius: 4px;
    border: 1px solid #f4ba2f;
    background-image: none ;

}
.form_button input:hover, .form_button input:active, .form_button input:focus{
    color: white ;
    background-color: #f4ba2f;
    -webkit-transition: background-color ease 0.25s;
    transition: background-color ease 0.25s;

}

</style>
</head>

<body class="app signed-in static_application index" data-controller-name="static_application" data-action-name="index" data-view-name="Coinbase.Views.StaticApplication.Index" data-account-id="">
<?php include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
    Link::redirect('userprofile');
elseif (User::$awaiting_token)
    Link::redirect('verify_token');
elseif (!User::isLoggedIn())
    Link::redirect('login');

$token1 = (!empty($_REQUEST['token'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['token']) : false;
$authcode1 = (!empty($_REQUEST['authcode'])) ? $_REQUEST['authcode'] : false;
$email_auth = false;
$match = false;
$request_2fa = false;
$too_few_chars = false;
$expired = false;
$no_token = false;
$same_currency = false;

API::add('User','getInfo',array($_SESSION['session_id']));
API::add('Currencies','getMain');
$query = API::send();
$main = $query['Currencies']['getMain']['results'][0];
if ($authcode1) {
    API::add('User','getSettingsChangeRequest',array(urlencode($authcode1)));
    $query = API::send();
// echo "<pre>"; print_r($query['User']['getSettingsChangeRequest']['results'][0]); exit;
    if ($query['User']['getSettingsChangeRequest']['results'][0]) {
        $_REQUEST = unserialize(base64_decode($query['User']['getSettingsChangeRequest']['results'][0]));
        // echo "This is in authcode" ;
        // echo "<pre>" ;

        // print_r($_REQUEST) ;
        // exit ;
        unset($_REQUEST['submitted']);
        $email_auth = true;
    }
    else
        Errors::add(Lang::string('settings-request-expired'));
}
if (empty($_REQUEST['settings']['pass'])) {
    unset($_REQUEST['settings']['pass']);
    unset($_REQUEST['settings']['pass2']);
    unset($_REQUEST['verify_fields']['pass']);
    unset($_REQUEST['verify_fields']['pass2']);
}
else {
    $_REQUEST['verify_fields']['pass'] = 'password';
    $_REQUEST['verify_fields']['pass2'] = 'password';
}
if (!empty($_REQUEST['settings'])) {
    // echo "String <pre>" ;
    // print_r($_REQUEST) ;
   
    if (!$email_auth && (empty($_SESSION["settings_uniq"]) || $_SESSION["settings_uniq"] != $_REQUEST['settings']['uniq']))
        $expired = true;
    
    if (!empty($_REQUEST['settings']['pass'])) {
        $match = preg_match_all($CFG->pass_regex,$_REQUEST['settings']['pass'],$matches);
        $too_few_chars = (mb_strlen($_REQUEST['settings']['pass'],'utf-8') < $CFG->pass_min_chars);
    }
    
    if (!empty($_REQUEST['settings']['pass'])) {
        $_REQUEST['settings']['pass'] = preg_replace($CFG->pass_regex, "",$_REQUEST['settings']['pass']);
        $_REQUEST['settings']['pass2'] = preg_replace($CFG->pass_regex, "",$_REQUEST['settings']['pass2']);
    }
    
    // if ($_REQUEST['settings']['default_currency'] == $_REQUEST['settings']['default_c_currency']) {
    //     $same_currency = true;
    //     $_REQUEST['settings']['default_currency'] = false;
    // }
    
    $_REQUEST['settings']['first_name'] = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u", "",$_REQUEST['settings']['first_name']);
    $_REQUEST['settings']['last_name'] = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u", "",$_REQUEST['settings']['last_name']);
    //$_REQUEST['settings']['country'] = preg_replace("/[^0-9]/", "",$_REQUEST['settings']['country']);
    $_REQUEST['settings']['email'] = preg_replace("/[^0-9a-zA-Z@\.\!#\$%\&\*+_\~\?\-]/", "",$_REQUEST['settings']['email']);
  //  $_REQUEST['settings']['chat_handle'] = preg_replace("/[^\pL a-zA-Z0-9@\s\._-]/u", "",$_REQUEST['settings']['chat_handle']);
}

$personal = new Form('settings',false,false,'form1','site_users');
if (!empty($query['User']['getInfo']['results'][0])){
    $personal->get($query['User']['getInfo']['results'][0]);
}

if (!$personal->info['email'])
    unset($personal->info['email']);

$personal->verify();

if ($expired)
    $personal->errors[] = 'Page expired.';
if ($match)
    $personal->errors[] = htmlentities(str_replace('[characters]',implode(',',array_unique($matches[0])),Lang::string('login-pass-chars-error')));
if ($too_few_chars) 
    $personal->errors[] = Lang::string('login-password-error');
// if ($same_currency)
//     $personal->errors[] = Lang::string('same-currency-error');


if (!empty($_REQUEST['submitted']) && empty($_REQUEST['settings'])) {
    if (!$email_auth && (empty($_SESSION["settings_uniq"]) || $_SESSION["settings_uniq"] != $_REQUEST['uniq']))
        Errors::add('Page expired.');
}

if (!empty($_REQUEST['submitted']) && !$token1 && !is_array($personal->errors) && !is_array(Errors::$errors)) {
    if (!empty($_REQUEST['request_2fa'])) {
        if (!($token1 > 0)) {
            $no_token = true;
            $request_2fa = true;
            Errors::add(Lang::string('security-no-token'));
        }
    }
    
    if (User::$info['verified_authy'] == 'Y' || User::$info['verified_google'] == 'Y') {
        if ($_REQUEST['send_sms'] || User::$info['using_sms'] == 'Y') {
            if (User::sendSMS()) {
                $sent_sms = true;
                Messages::add(Lang::string('withdraw-sms-sent'));
            }
        }
        $request_2fa = true;
    }
    else {
        API::add('User','settingsEmail2fa',array($_REQUEST));
        $query = API::send();
        
        $_SESSION["settings_uniq"] = md5(uniqid(mt_rand(),true));
        Link::redirect('userprofile?notice=email');
    }
}

if (!empty($_REQUEST['settings']) && is_array($personal->errors)) {
    $errors = array();
    foreach ($personal->errors as $key => $error) {
        if (stristr($error,'login-required-error')) {
            $errors[] = Lang::string('settings-'.str_replace('_','-',$key)).' '.Lang::string('login-required-error');
        }
        elseif (strstr($error,'-')) {
            $errors[] = Lang::string($error);
        }
        else {
            $errors[] = $error;
        }
    }
    Errors::$errors = $errors;
    $request_2fa = false;
}
elseif (!empty($_REQUEST['settings']) && !is_array($personal->errors)) {
    if (empty($no_token) && !$request_2fa) {
        API::settingsChangeId($authcode1);
        API::token($token1);
        // echo "<pre>"; print_r($personal->info); exit;
        API::add('User','updatePersonalInfo',array($personal->info));
        $query = API::send();

        if (!empty($query['error'])) {
            if ($query['error'] == 'security-com-error')
                Errors::add(Lang::string('security-com-error'));
            if ($query['error'] == 'authy-errors')
                Errors::merge($query['authy_errors']);
            
            if ($query['error'] == 'request-expired')
                Errors::add(Lang::string('settings-request-expired'));
            
            if ($query['error'] == 'security-incorrect-token')
                Errors::add(Lang::string('security-incorrect-token'));
        }
        
        if (!is_array(Errors::$errors)) {
            $_SESSION["settings_uniq"] = md5(uniqid(mt_rand(),true));
            Link::redirect('userprofile?message=settings-personal-message');
        }
        else
            $request_2fa = true;
    }
}

if (!empty($_REQUEST['prefs'])) {
    if (!$email_auth && (empty($_SESSION["settings_uniq"]) || $_SESSION["settings_uniq"] != $_REQUEST['uniq']))
        Errors::add('Page expired.');
    elseif (!$no_token && !$request_2fa) {
        API::settingsChangeId($authcode1);
        API::token($token1);
        API::add('User','updateSettings',array($confirm_withdrawal_2fa_btc1,$confirm_withdrawal_email_btc1,$confirm_withdrawal_2fa_bank1,$confirm_withdrawal_email_bank1,$notify_deposit_btc1,$notify_deposit_bank1,$notify_login1,$notify_withdraw_btc1,$notify_withdraw_bank1));
        $query = API::send();
            
        if (!empty($query['error'])) {
            if ($query['error'] == 'security-com-error')
                Errors::add(Lang::string('security-com-error'));
                
            if ($query['error'] == 'authy-errors')
                Errors::merge($query['authy_errors']);
                
            if ($query['error'] == 'request-expired')
                Errors::add(Lang::string('settings-request-expired'));
            
            if ($query['error'] == 'security-incorrect-token')
                Errors::add(Lang::string('security-incorrect-token'));
        }   
        if (!is_array(Errors::$errors)) {
            $_SESSION["settings_uniq"] = md5(uniqid(mt_rand(),true));
            Link::redirect('settings?message=settings-settings-message');
        }
        else
            $request_2fa = true;
    }
}

if (!empty($_REQUEST['message'])) {
    if ($_REQUEST['message'] == 'settings-personal-message')
        Messages::add(Lang::string('settings-personal-message'));
    elseif ($_REQUEST['message'] == 'settings-settings-message')
        Messages::add(Lang::string('settings-settings-message'));
    elseif ($_REQUEST['message'] == 'settings-account-deactivated')
        Messages::add(Lang::string('settings-account-deactivated'));
    elseif ($_REQUEST['message'] == 'settings-account-reactivated')
        Messages::add(Lang::string('settings-account-reactivated'));
    elseif ($_REQUEST['message'] == 'settings-account-locked')
        Messages::add(Lang::string('settings-account-locked'));
    elseif ($_REQUEST['message'] == 'settings-account-unlocked')
        Messages::add(Lang::string('settings-account-unlocked'));
}

if (!empty($_REQUEST['notice']) && $_REQUEST['notice'] == 'email')
    $notice = Lang::string('settings-change-notice');

$cur_sel = array();
if ($CFG->currencies) {
    foreach ($CFG->currencies as $key => $currency) {
        if (is_numeric($key))
            continue;
        
        $cur_sel[$key] = $currency;
    }
}

$cur_sel1 = array();
if ($CFG->currencies) {
    foreach ($CFG->currencies as $key => $currency) {
        if (is_numeric($key) || $currency['is_crypto'] != 'Y')
            continue;

        $cur_sel1[$key] = $currency;
    }
}

$_SESSION["settings_uniq"] = md5(uniqid(mt_rand(),true));

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
        <h1>Profile</h1>
    </div>
</div>
    <div class="LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">
        <div class="LayoutDesktop__Content-flhQBc bRMwEm Flex__Flex-fVJVYW gkSoIH">
            <div class="Dashboard__FadeFlex-bFoDXs cYFmKg Flex__Flex-fVJVYW iDqRrV">
                <div class="Flex__Flex-fVJVYW bHipRv">
                    <div></div>
                    <div class="Dashboard__Panels-getBDx fJxaut Flex__Flex-fVJVYW iDqRrV">
                        <div class="Flex__Flex-fVJVYW bHipRv">
                            <div class="Flex__Flex-fVJVYW gsOGkq">

                               <div class="Dashboard__ChartContainer-bKDMTA kjRPPr Flex__Flex-fVJVYW iDqRrV" style="height: auto;">
                                    <div class="Flex__Flex-fVJVYW gsOGkq" style="width: 100%;border-right: none;">
                                        <div id="page" class="jdmxYg" style="width: 100%;">
                                     <!--  <style>
                                          .message-box-wrap{
                                              position: relative !important;
                                          }
                                          </style> -->
                                           <? 
            Errors::display(); 
            Messages::display();
            ?>
            <?= (!empty($notice)) ? '<div class="notice"><div class="message-box-wrap">'.$notice.'</div></div>' : '' ?>
            <div class="row profile-image-errors" style="display:none;">
                <div>
                    <div class="alert"></div>
                </div>
            </div>
                                        <div class="row">
                                       
                                            <ul id="account_tabs" class="nav nav-tabs">
                                                <li <? if ($CFG->self == 'userprofile') { ?> class="active" <?php } ?>>
                                                    <a href="userprofile">Profile</a>
                                                </li>
                                                <li <? if ($CFG->self == 'bank-accounts') { ?> class="active" <?php } ?>>
                                                    <a href="bank-accounts">Bank</a>
                                                </li>
                                                <li <? if ($CFG->self == 'usersecurity') { ?> class="active" <?php } ?>>
                                                    <a href="usersecurity">Security</a>
                                                </li>
                                                <li>
                                                    <a href="userapi" <? if ($CFG->self == 'userapi') { ?> class="active" <?php } ?>>API</a>
                                                </li>

                                            </ul>
            <legend>Personal Details</legend>
        <div class="profile-otr">
            <?
                $personal->passwordInput('pass',Lang::string('settings-pass'));
                $personal->passwordInput('pass2',Lang::string('settings-pass-confirm'),false,false,false,false,false,false,'pass');
                $personal->textInput('first_name',Lang::string('settings-first-name'));
                $personal->textInput('last_name',Lang::string('settings-last-name'));
                // $personal->selectInput('country',Lang::string('settings-country'),false,false,$countries,false,array('name'));
                $personal->textInput('email',Lang::string('settings-email'),'email');
          //      $personal->textInput('chat_handle',Lang::string('chat-handle'));
             //   $personal->selectInput('default_c_currency',Lang::string('default-c-currency'),0,$main['crypto'],$cur_sel1,false,array('currency'));
           //     $personal->selectInput('default_currency',Lang::string('default-currency'),0,$main['fiat'],$cur_sel,false,array('currency'));
                $personal->HTML('<div class="form_button"><input type="submit" name="submit" value="'.Lang::string('settings-save-info').'" class="but_user btn" /></div><input type="hidden" name="submitted" value="1" />');
                $personal->hiddenInput('uniq',1,$_SESSION["settings_uniq"]);
                $personal->display();
                ?>
                </div>
                                        </div>
                                    </div>
                                </div>
                                    </div>
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
$(document).ready(function(){
$(".Header__DropdownButton-dItiAm").click(function(){
$(".DropdownMenu__Wrapper-ieiZya.kwMMmE").toggleClass("show-menu");
});
});
</script>

</body>

</html>