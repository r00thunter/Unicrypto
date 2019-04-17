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
// error_reporting(E_ERROR | E_WARNING | E_PARSE);
// ini_set('display_errors', 1);
include '../lib/common.php';
require_once ("cfg.php");

// CHECKING REFErral status 
    // $ch = curl_init("http://18.220.172.39/api/get-settings.php"); 
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);      
    curl_close($ch);
    $ref_response = json_decode($output);
    if ($ref_response->is_referral == 1) {
        $GLOBALS['REFERRAL'] = true;
        // $GLOBALS['REFERRAL_BASE_URL'] = "http://18.220.172.39/api/";
        //$GLOBALS['REFERRAL_BASE_URL'] = $ref_response->base_url;
    }else{
       $GLOBALS['REFERRAL'] = false; 
    }
 $GLOBALS['REFERRAL'] = false; 
    // end of checking referral status
    
$_REQUEST['register']['country'] = (!empty($_REQUEST['register']['country'])) ? preg_replace("/[^0-9]/", "", $_REQUEST['register']['country']) : false;
$_REQUEST['register']['email'] = (!empty($_REQUEST['register']['email'])) ? preg_replace("/[^0-9a-zA-Z@\.\!#\$%\&\*+_\~\?\-]/", "", $_REQUEST['register']['email']) : false;

if (empty($CFG->google_recaptch_api_key) || empty($CFG->google_recaptch_api_secret))
    $_REQUEST['is_caco'] = (!empty($_REQUEST['form_name']) && empty($_REQUEST['is_caco'])) ? array('register' => 1) : (!empty($_REQUEST['is_caco']) ? $_REQUEST['is_caco'] : false);

if (empty($_REQUEST['form_name']))
    unset($_REQUEST['register']);

$register = new Form('register', false, false, 'form3');
unset($register->info['uniq']);
$register->verify();
$register->reCaptchaCheck();

if (!empty($_REQUEST['register']) && !$register->info['terms'])
    $register->errors[] = Lang::string('settings-terms-error');

if (!empty($_REQUEST['register']) && $CFG->register_status == 'suspended')
    $register->errors[] = Lang::string('register-disabled');

if (!empty($_REQUEST['register']) && (is_array($register->errors))) {
    $errors = array();

    if ($register->errors) {
        foreach ($register->errors as $key => $error) {
            if (stristr($error, 'login-required-error')) {
                $errors[] = Lang::string('settings-' . str_replace('_', '-', $key)) . ' ' . Lang::string('login-required-error');
            } elseif (strstr($error, '-')) {
                $errors[] = Lang::string($error);
            } else {
                $errors[] = $error;
            }
        }
    }

    Errors::$errors = $errors;
} elseif (!empty($_REQUEST['register']) && !is_array($register->errors)) {

    // API::add('User','getAlleKYC');
    // $query = API::send();
    // $email = array_search($_REQUEST['register']['email'], array_column($query['User']['getAlleKYC']['results'][0], 'email'));
    // if(!empty($email)){
    //     $errors[] = 'Email already exist.';
    //     Errors::$errors = $errors;
    // }
    // else{
            
    API::add('User', 'registerNew', array($register->info));
    // echo "INFO = ";
    // print_r($register->info) ;
    $query = API::send();
    $_SESSION["register_uniq"] = md5(uniqid(mt_rand(), true));
    $r_referral_code = $_REQUEST['referral_code'];
        if ($REFERRAL == true && $r_referral_code) {

            $referral_code = $r_referral_code;

            $url = $base_ip."check-referral-code.php?referral_code=".$referral_code;
            
            $ch = curl_init($url); 
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);      
            curl_close($ch); 
            $data = json_decode($response);

            if ($data->status == false) {
                $errors = array();
                $errors[] = 'Invalid referral code';
                Errors::$errors = $errors;
            }else{
                
                $url = $base_ip."add-user.php";

                $username = $register->info['first_name'] ." ". $register->info['last_name'];
                $fields = array(
                    'user_id' => urlencode($register->info['email']),
                    'username' => urlencode($username),
                    'referral_id' => urlencode($referral_code)
                );

                //url-ify the data for the POST
                foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                rtrim($fields_string, '&');

               //open connection
                $ch = curl_init();

                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST, count($fields));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                //execute post
                $result = curl_exec($ch);

                //close connection
                curl_close($ch);

                $response = json_decode($result);

                if ($response->status == false) {
                    $errors = array();
                    $errors[] = $response->message;
                    Errors::$errors = $errors;
                }
                else{
                    Link::redirect($CFG->baseurl . 'login?message=registered');
                }
                
            }
            //var_dump($response); exit;
        }else{
            include('/var/www/html/frontend/htdocs/exchange-prelaunch.php');
            Link::redirect($CFG->baseurl . 'login?message=registered');
        }
        
    //}
}

API::add('User', 'getCountries');
$query = API::send();

$page_title = Lang::string('home-register');

$_SESSION["register_uniq"] = md5(uniqid(mt_rand(), true));
?>
<style type="text/css">
    .errors li {
    border: 0 solid #FFFFFF;
    padding-bottom: 5px;
}
.errors {
    background-color: #FFDDDD;
    border: #F1BDBD 1px solid;
    color: #BD6767;
}.errors, .messages {
    margin-bottom: 20px;
    padding: 10px 10px 5px 10px;
    position: static;
    padding: 0;
    margin: 0 20px 0;
    background: transparent;
    border: none;
    box-shadow: none;
}ul {
    list-style: none;

}
.g-recaptcha iframe, .g-recaptcha div{
   margin: auto !important;
}
</style>
<script src='https://www.google.com/recaptcha/api.js<?= ((!empty($CFG->language) && $CFG->language != 'en') ? '?hl=' . ($CFG->language == 'zh' ? 'zh-CN' : $CFG->language) : '') ?>'></script>
<?php include "includes/sonance_header.php"; ?>
<?php include "includes/sonance_navbar.php"; ?>
<?php
$page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='register'");
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
                <h6 class="text-center"><strong><?php echo isset($pgcont['register_heading_key']) ? $pgcont['register_heading_key'] : 'Register'; ?></strong></h6>
            <?
            $currencies_list = array();
            if ($CFG->currencies) {
                foreach ($CFG->currencies as $key => $currency) {
                    if (is_numeric($key))
                        continue;

                    $currencies_list[$key] = $currency;
                }
            }

            $currencies_list1 = array();
            if ($CFG->currencies) {
                foreach ($CFG->currencies as $key => $currency) {
                    if (is_numeric($key) || $currency['is_crypto'] != 'Y')
                        continue;

                    $currencies_list1[$key] = $currency;
                }
            }

            Errors::display();
            Messages::display();

   
            $register->textInput('first_name', isset($pgcont['register_firstname_key']) ? $pgcont['register_firstname_key'] : Lang::string('settings-first-name') , 'first_name', false, false, false, 'form-control');
            $register->textInput('last_name', isset($pgcont['register_lastname_key']) ? $pgcont['register_lastname_key'] : Lang::string('settings-last-name'), false, false, false, false, 'form-control');
            $register->textInput('email', isset($pgcont['register_email_key']) ? $pgcont['register_email_key'] : Lang::string('settings-email'), 'email', false, false, false, 'form-control');
            $register->textInput('phone', isset($pgcont['register_phone_key']) ? $pgcont['register_phone_key'] : Lang::string('settings-phone'), 'phone', false, false, false, 'form-control');
            if ($REFERRAL == true) {
                $register->HTML('<label for="referral_code">'.isset($pgcont['register_referal_key']) ? $pgcont['register_referal_key'] : "Referral Code".'  </label><input type="text" name="referral_code" value="" id="register_referral_code" class="form-control"><br>');
                //$register->textInput('referral_code', 'Referral Code', 'phone', false, false, false, 'form-control');
            }
            // $register->textInput('pan_no', Lang::string('settings-pan-number'), 'pan_no', false, false, false, 'form-control');
            // $register->selectInput('default_c_currency', Lang::string('default-c-currency'), 1, false, $currencies_list1, false, array('currency'), false, false, 'form-control');
            // $register->selectInput('default_currency', Lang::string('default-currency'), 1, false, $currencies_list, false, array('currency'), false, false, 'form-control');
            $register->checkBox('terms', (isset($pgcont['register_accept_key']) ? $pgcont['register_accept_key'] : Lang::string('settings-terms-accept')).' '.(isset($pgcont['register_terms_key']) ? '<a href="terms.php">'.$pgcont['register_terms_key'].'</a>' : '').' '.(isset($pgcont['register_use_site_key']) ? $pgcont['register_use_site_key'] : ''), false, false, false, false, false, false);
            $register->captcha(Lang::string('settings-capcha'));
            $register->HTML('<input type="hidden" name="default-currency" value="27">');
            $register->HTML('<input type="hidden" name="default-c-currency" value="28">');
            $register->HTML('<div class="form-group"><input type="submit" name="submit" value="' . (isset($pgcont['register_button_key']) ? $pgcont['register_button_key'] : Lang::string('home-register')) . '" class="btn btn-primary" /></div>');
            $register->hiddenInput('uniq', 1, $_SESSION["register_uniq"]);
            $register->display();

            ?>
            <style>
                input[name="register[terms]"] {
                    width: 15px;
                    height: 15px;
                }
                input[name="register[terms]"] + label {
                    vertical-align: middle;
                    margin-left: 5px;
                }
                
            </style>
                <!-- <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-user"></i></span>
                        <input class="form-control" type="text" placeholder="First Name">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-user"></i></span>
                        <input class="form-control" type="text" placeholder="Last Name">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                        <input class="form-control" type="email" placeholder="Email ID">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fas fa-user"></i></span>
                        <input class="form-control" type="text" placeholder="Contact Number">
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">I agree the terms and conditions.</label>
                </div>
                <a href="#" class="btn btn-primary">Register</a> -->
                 <!-- <a href="#" class="btn btn-primary">Register</a>  -->
                <p class="note"><?php echo isset($pgcont['register_already_key']) ? $pgcont['register_already_key'] : 'Already Registered?'; ?> <a href="login"><?php echo isset($pgcont['  ']) ? $pgcont['register_login_key'] : 'Login'; ?></a></p>
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