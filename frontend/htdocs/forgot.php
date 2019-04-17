<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <title></title>
    <meta name="author" content="">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="canonical" href="">
    <meta name="theme-color" content="#310f72">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="sonance/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="sonance/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="sonance/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="sonance/img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="sonance/img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="sonance/img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="sonance/img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="sonance/img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="sonance/img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="sonance/img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="sonance/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="sonance/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="sonance/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="sonance/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="sonance/img/favicon/ms-icon-144x144.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Default Style CSS -->
    <link rel="stylesheet" type="text/css" href="sonance/css/default.css">
    <link rel="stylesheet" type="text/css" href="sonance/css/responsive.css">
    <!-- Global site tag (gtag.js) - AdWords: 1045328140 --> <script async src="https://www.googletagmanager.com/gtag/js?id=AW-1045328140"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-1045328140'); </script>
</head>
<?php
// error_reporting(E_ALL); 
// ini_set('display_errors', 'On');
include '../lib/common.php';

$page_title = Lang::string('login-forgot');
$email1 = (!empty($_REQUEST['forgot']['email'])) ? preg_replace("/[^0-9a-zA-Z@\.\!#\$%\&\*+_\~\?\-]/", "",$_REQUEST['forgot']['email']) : false;
$captcha_error = false;

if (!empty($_REQUEST['forgot']) && $email1 && $_SESSION["forgot_uniq"] == $_REQUEST['uniq']) {
    if (empty($CFG->google_recaptch_api_key) || empty($CFG->google_recaptch_api_secret)) {
        include_once 'securimage/securimage.php';
        $securimage = new Securimage();
        $captcha_error = (empty($_REQUEST['forgot']['captcha']) || !$securimage->check($_REQUEST['forgot']['captcha']));
    }
    else {
        $captcha = new Form('captcha');
        $captcha->reCaptchaCheck(1);
        if (!empty($captcha->errors) && is_array($captcha->errors)) {
            $captcha_error = true;
            Errors::add($captcha->errors['recaptcha']);
        }
    }
    
    if (!$captcha_error) {
        API::add('User','resetUser',array($email1));
        $query = API::send();

        Messages::$messages = array();
        Messages::add(Lang::string('login-password-sent-message'));
    }
    else {
        // echo "capcha-error"; exit;
        Errors::add(Lang::string('login-capcha-error'));
    }
}

$_SESSION["forgot_uniq"] = md5(uniqid(mt_rand(),true));
?>
<script src='https://www.google.com/recaptcha/api.js<?= ((!empty($CFG->language) && $CFG->language != 'en') ? '?hl='.($CFG->language == 'zh' ? 'zh-CN' : $CFG->language) : '') ?>'></script>
<style>
.message-box-wrap
{
position: static !important;
    width: 100%;
    margin: 10px 0;
    padding: 10px !important;
    background-color: #DFFBE4 !important;
    border: #A9ECB4 1px solid;
    color: #1EA133;
    box-shadow: none !important;
    border-radius: 3px;
    }
.messages {
position: relative !important;
font-size: 14px !important;
z-index: 999 !important;

}
.rc-anchor-normal{
    width: 99% !important;
}
.g-recaptcha iframe, .g-recaptcha div{
   margin: auto !important;
}
.log-reg{
    padding-left: 30%;
}

</style>
<?php include "includes/sonance_header.php"; ?>
<?php include "includes/sonance_navbar.php"; ?>
<?php
$page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='forgot password'");
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
     
?>
<body class="register-page">
    <div class="register-container">
        <div class="container">
            <div class="register-card">
                <!-- <img src="sonance/img/logo.png" class="logo"> -->
                <!-- <h3 class="text-center m_b_20"><?= $CFG->exchange_name; ?></h3> -->
                <div class="text-center logo-otr">
                    <img src="images/star.png" alt="img" class="logo-star">
                    <img src="images/logo1.png" alt="img" class="main-logo" />
                </div>
                <h6 class="text-center"><strong><?php echo isset($pgcont['forgot_pass_heading_key']) ? $pgcont['forgot_pass_heading_key'] : 'Reset Password'; ?></strong></h6>
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
                <form method="POST" action="forgot.php" name="forgot">  
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control"  name="forgot[email]" value="<?= $email1 ?>" placeholder="<?php echo isset($pgcont['forgot_pass_placeholder_key']) ? $pgcont['forgot_pass_placeholder_key'] : 'Enter your email..'; ?>" >
                    </div>
                </div>
                <? 
                if (empty($CFG->google_recaptch_api_key) || empty($CFG->google_recaptch_api_secret)) { ?>
                <div>
                    <div><?= Lang::string('settings-capcha') ?></div> 
                    <img class="captcha_image" src="securimage/securimage_show.php" />
                </div>
                <div class="loginform_inputs">
                    <div class="input_contain">
                        <i class="fa fa-arrow-circle-o-up"></i>
                        <input type="text" class="login" name="forgot[captcha]" value="" />
                    </div>
                </div>
                <? } else { ?>
                <div style="margin-bottom:10px;">
                    <div class="g-recaptcha" data-sitekey="<?= $CFG->google_recaptch_api_key ?>"></div>
                </div>
                <? } ?>
                <input type="hidden" name="uniq" value="<?= $_SESSION["forgot_uniq"] ?>" />
                <input type="submit" name="submit" value="<?php echo isset($pgcont['forgot_pass_button_key']) ? $pgcont['forgot_pass_button_key'] : Lang::string('login-forgot-send-new'); ?>" class="btn btn-primary" />
                <!-- <a href="login.html" class="btn btn-primary">Reset</a> -->
                <p class="note"><span><?php echo isset($pgcont['forgot_pass_account_key']) ? $pgcont['forgot_pass_account_key'] : 'Don\'t have an account?'; ?> <a href="register"><?php echo isset($pgcont['forgot_pass_register_key']) ? $pgcont['forgot_pass_register_key'] : 'Register'; ?> </a></span><span class="log-reg"><?php echo isset($pgcont['login_already_key']) ? $pgcont['login_already_key'] : 'Already Registered?'; ?> <a href="login">  <?php echo isset($pgcont['login_heading_key']) ? $pgcont['login_heading_key'] : 'Login'; ?></a></span></p>
            </div>
            <div class="copyrights">
                <p>&copy; 2018 <?= $CFG->exchange_name; ?>. All Rights Reserved</p>
            </div>
        </div>
    </div>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    </script>
    </script>
    <!-- Custom Scripts -->
    <script type="text/javascript" src="sonance/js/script.js"></script>
        <script type="text/javascript" src="js/script11.js"></script>
</body>

</html>