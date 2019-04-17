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
                    <h1>Google 2-Factor Authenticator Verification</h1>
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
                                <legend><?= Lang::string('security-enter-token') ?></legend>
                                <div class="text"><?= $content['content'] ?></div>
                                <div class="span9 marginmobile" style="padding-bottom: 0;">
                                    <form id="enable_tfa" action="verify-token.php" method="POST">
                                        <input type="hidden" name="step" value="1" />
                                        <input type="hidden" name="email_auth" value="<?= !empty($_REQUEST['email_auth']) ?>" />
                                        <input type="hidden" name="authcode" value="<?= urlencode($authcode1) ?>" />
                                        <div class="control-group user-email">
                                            <!-- <span class="control-label formtexts" style="text-align: left;width: 190px;float: left;">
                                                <label class="formlabel" for="user_email" style="font-weight: 600;margin-bottom: 0;line-height: 1.9em;color: #5a5f6d;"><?= Lang::string('security-token') ?></label>
                                                </span> -->
                                            <div class="controls">
                                                <input type="text" name="token" id="token" value="<?= $token1 ?>" class="col-md-12" style="margin-bottom:5px;" placeholder="Enter your token here"/>
                                                <div>
                                                    <input type="submit" name="submit" style="margin-bottom:1em;float: right;" value="<?= Lang::string('security-validate') ?>" class="btn trigger-challenge-2fa" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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