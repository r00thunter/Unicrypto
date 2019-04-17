<?php
    // error_reporting(E_ALL); 
    // ini_set('display_errors', 'On');
    include '../lib/common.php';
    
    if (User::isLoggedIn())
        Link::redirect('myprofile.php');
    elseif (!User::$awaiting_token)
        Link::redirect('login.php');
    
    $token1 = (!empty($_REQUEST['token'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['token']) : false;
    $dont_ask1 = !empty($_REQUEST['dont_ask']);
    $authcode1 = (!empty($_REQUEST['authcode'])) ? urldecode($_REQUEST['authcode']) : false;
    
    if (!empty($_REQUEST['step']) && $_REQUEST['step'] == 1) {
        if (!($token1 > 0))
            Errors::add(Lang::string('security-no-token'));
        
        if (!is_array(Errors::$errors)) {
            $verify = User::verifyToken($token1,$dont_ask1);
            if ($verify) {
                if (!empty($_REQUEST['email_auth']))
                    Link::redirect('balances.php?authcode='.urlencode($_REQUEST['authcode']));
                else
                    Link::redirect('balances.php');
                exit;
            }
        }
    }
    
    API::add('Content','getRecord',array('security-token-login'));
    $query = API::send();
    
    $content = $query['Content']['getRecord']['results'][0];
    $page_title = Lang::string('verify-token');
    
    ?>
<!DOCTYPE html>
<html lang="en">
    <?php include "includes/sonance_header.php"; ?>
    <style>
        .errors
        {
        background: #ff000029;
        color: red;
        position: relative;
        width: 100%;
        right: 0;
        margin-top:20px;
        }
        .messages
        {
        background: #00800038;
        color: green;
        position: relative;
        width: 100%;
        right: 0;
        margin-top: 20px;
        }
        .form-control
        {
        height : 32px;
        }
    </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
        <header>
            <div class="banner row">
                <div class="container content">
                    <h1>Resetting Google 2-Factor Authenticator</h1>
                </div>
            </div>
        </header>
        <div class="page-container">
            <div class="container">
                <?php 
                    Errors::display(); 
                    Messages::display();
                    ?>
                <?php if(!empty($notice)): ?>
                <div class="notice">
                    <div class="message-box-wrap alert alert-info"><?=$notice?></div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="pro card">
                            <!-- <div class="card-header">
                                <h6><strong>Google 2-Factor Authenticator Verification</strong></h6>
                                </div> -->
                            <div class="card-body">
                                <legend>Resetting 2FA</legend>
                                <!-- <div class="text"><?= $content['content'] ?></div> -->
                                <div class="span9 marginmobile" style="padding-bottom: 0;">
                                        
                                        <p>If you have lost your phone and need to reset two-factor authentication, the process is different dependingauthentication method you chose for your account:</p>

                                        <p>1.) For Authy, you need to visit their own <a href="https://www.authy.com/phones/reset/" target="_blank">reset page</a>. When you fill out the required information, you will receive reset instructions at the email you regwhen you installed the app on your cellphone.</p>
                                        <p>2.) For Google Authenticator, you will need to use the secret code that you wrote down when you set up two-factor authentication. Enter the app and manuallyaccount using that secret code.</p>
                                        <p>3.) If you use SMS, the reset is handled using Authy as well. You can reset your SMS verification on their <a href="https://www.authy.com/phones/reset/" target="_blank">reset page</a>.</p>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "includes/sonance_footer.php"; ?>
    </body>
</html>