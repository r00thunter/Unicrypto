<?php 
include '../lib/common.php';
    if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
        Link::redirect('myprofile');
    elseif (User::$awaiting_token)
        Link::redirect('verify_token');
    elseif (!User::isLoggedIn())
        Link::redirect('login');
    
        require_once ("cfg.php");
    $token1 = (!empty($_REQUEST['token'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['token']) : false;
    $authcode1 = (!empty($_REQUEST['authcode'])) ? $_REQUEST['authcode'] : false;
    $email_auth = false;
    $match = false;
    $request_2fa = false;
    $too_few_chars = false;
    $expired = false;
    $no_token = false;
    $same_currency = false;

    // CHECKING REFErral status 
    // $ch = curl_init("http://167.99.204.119/api/get-settings.php"); 
    // curl_setopt($ch, CURLOPT_HEADER, 0);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // $output = curl_exec($ch);      
    // curl_close($ch);
    // $ref_response = json_decode($output);
    // if ($ref_response->is_referral == 1) {
    //     $GLOBALS['REFERRAL'] = true;
    //     $GLOBALS['REFERRAL_BASE_URL'] = "http://167.99.204.119/api/";
    //     //$GLOBALS['REFERRAL_BASE_URL'] = $ref_response->base_url;
    // }else{
    //    $GLOBALS['REFERRAL'] = false; 
    // }
    
    API::add('User','getInfo',array($_SESSION['session_id']));
    API::add('Currencies','getMain');
    $query = API::send();

    $userInfo = $query['User']['getInfo']['results'][0];

    $main = $query['Currencies']['getMain']['results'][0];
    if ($authcode1) {
        API::add('User','getSettingsChangeRequest',array(urlencode($authcode1)));
        $query = API::send();
    // echo "<pre>"; print_r($query['User']['getSettingsChangeRequest']['results'][0]); exit;
        if ($query['User']['getSettingsChangeRequest']['results'][0]) {
            $_REQUEST = unserialize(base64_decode($query['User']['getSettingsChangeRequest']['results'][0]));
            // echo "This is in authcode" ;
            /* echo "<pre>" ;
            print_r($_REQUEST) ;
            exit ; */
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
        unset($_REQUEST['verify_fields']['pass1']);
    }
    else {
        $_REQUEST['verify_fields']['pass'] = 'password';
        $_REQUEST['verify_fields']['pass2'] = 'password';
        $_REQUEST['verify_fields']['pass1'] = 'current_password';
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
    //echo "<pre>";var_dump($_REQUEST);die();
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
          //  print_r($personal->info);
            API::add('User','settingsEmail2fa',array($_REQUEST,$personal->info));
            $query = API::send();
           
            if($query['User']['settingsEmail2fa']['results']['0'] === 'incorrect-current-password'){
                
                $query['error'] = 'incorrect-current-password';               
            }

           if (!empty($query['error'])) {
             if ($query['error'] == 'incorrect-current-password')
                    Errors::add('Current password is incorrect');
            }

            if ($query['error'] != 'incorrect-current-password'){

            $_SESSION["settings_uniq"] = md5(uniqid(mt_rand(),true));
            Link::redirect('myprofile?notice=email');
            }
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
                Link::redirect('myprofile?message=settings-personal-message');
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


    /* history section starts here */
    $page1 = (!empty($_REQUEST['page'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['page']) : false;
    $bypass = !empty($_REQUEST['bypass']);

    API::add('History','get',array(1,$page1));
    $query = API::send();
    $total = $query['History']['get']['results'][0];

    API::add('History','get',array(false,$page1,30));
    $query = API::send();

    $history = $query['History']['get']['results'][0];
    $pagination = Content::pagination('myprofile.php',$page1,$total,30,5,false);

    $page_title = Lang::string('history');
    /* history section endss here */

    // start of referral 
        if ($REFERRAL == true) {

            $user_email = $userInfo['email'];
            $url = $base_ip."get-user-bonus.php";

            $fields = array(
                'user_id' => urlencode($user_email)
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
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //execute post
            $result = curl_exec($ch);
            $response = json_decode($result);
            //close connection
            curl_close($ch);
            $referral_code = $response->data->referral_code;
            $bonous_point = $response->data->bonous_point;

            //
            $his_url = $base_ip."get-usage-history.php";
            $ch = curl_init();
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //execute post
            $result = curl_exec($ch);
            $response = json_decode($result);
            //var_dump($response);
            //close connection
            curl_close($ch);
        }
        
    // end of referral
    
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
            font-size: 13px;
        }
        .profile-content h6{
            font-size: 13px;
        }
    </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
        <?php
            $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='profile'");
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
        <header>
            <div class="banner row">
                <div class="container content">
                    <h1><?php echo isset($pgcont['profile_heading_key']) ? $pgcont['profile_heading_key'] : 'User Profile Settings'; ?></h1>
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
                    <?php if ($REFERRAL == true) { ?>
                    <div class="col-lg-7 col-sm-12">
                    <?php }else{ ?>
                    <div class="col-sm-12">
                    <?php } ?>
                        <div class="pro card">
                            <div class="card-header">
                                <h6><strong><?php echo isset($pgcont['profile_personal_details_key']) ? $pgcont['profile_personal_details_key'] : 'Personal Details'; ?></strong></h6>
                            </div>
                                <div class="card-body">
                                    <div>
                                        <!-- <div class="profile-img" style="background-image: url(sonance/img/user.png);"></div> -->
                                        <div class="profile-content" style="padding-left: 0;">
                                            <h6><strong><?php echo isset($pgcont['profile_personal_details_name_key']) ? $pgcont['profile_personal_details_name_key'] : 'Name :'; ?></strong> <?=$userInfo['first_name'].' '.$userInfo['last_name'] ?></h6>
                                            <h6><strong><?php echo isset($pgcont['profile_personal_details_email_key']) ? $pgcont['profile_personal_details_email_key'] : 'Email :'; ?> </strong><?=$userInfo['email'] ?></h6>
                                            <h6><strong><?php echo isset($pgcont['profile_personal_details_phone_key']) ? $pgcont['profile_personal_details_phone_key'] : 'Phone :'; ?> </strong><?=$userInfo['email'] ?></strong> <?=$userInfo['phone'] ?></h6>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>

                    <?php if ($REFERRAL == true) { ?>

                    <div class="col-lg-5 col-sm-12">
                        <div class="pro card">
                        <div class="card-header">
                            <h6><strong><?php echo isset($pgcont['profile_referal_point_key']) ? $pgcont['profile_referal_point_key'] : 'Referral point'; ?></strong></h6>
                        </div>
                        <input type="text" style="display: none;" name="ref_code" id="referral_code" value="<? echo $referral_code; ?>">
                        <div class="card-body">
                            <h6>
                                <strong><?php echo isset($pgcont['profile_referal_code_key']) ? $pgcont['profile_referal_code_key'] : 'Referral code:'; ?></strong>&nbsp;<? echo $referral_code; ?> 
                            </h6>
                            <h6>
                                <strong><?php echo isset($pgcont['profile_available_points_key']) ? $pgcont['profile_available_points_key'] : 'Available Points:'; ?></strong>&nbsp;<? echo $bonous_point; ?> Points 
                            </h6>
                            <p><a href="#referaltrans" data-toggle="modal"><u>View transactions</u></a></p>

                            <p>1 <?php echo isset($pgcont['profile_referal_point_key']) ? $pgcont['profile_referal_point_key'] : 'Referral point'; ?>  = $ <?php echo 1 / $ref_response->one_point_value; ?></p>
                            <p>1 <?php echo isset($pgcont['profile_referal_point_key']) ? $pgcont['profile_referal_point_key'] : 'Referral point'; ?> = BTC <?php echo 1 / $ref_response->BTC; ?></p>
                            <p>1 <?php echo isset($pgcont['profile_referal_point_key']) ? $pgcont['profile_referal_point_key'] : 'Referral point'; ?> = LTC <?php echo 1 / $ref_response->LTC; ?></p>
                            <p>1 <?php echo isset($pgcont['profile_referal_point_key']) ? $pgcont['profile_referal_point_key'] : 'Referral point'; ?> = BCH <?php echo 1 / $ref_response->BCH; ?></p>
                            <p>1 <?php echo isset($pgcont['profile_referal_point_key']) ? $pgcont['profile_referal_point_key'] : 'Referral point'; ?> = ZEC <?php echo 1 / $ref_response->ZEC; ?></p>
                            <p>1 <?php echo isset($pgcont['profile_referal_point_key']) ? $pgcont['profile_referal_point_key'] : 'Referral point'; ?> = ETH <?php echo 1 / $ref_response->ETH; ?></p>
                        </div>
                        </div>
                    </div>

                    <?php } ?> 
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="pro card">
                            <div class="card-header">
                                <h6><strong><?php echo isset($pgcont['profile_personal_details_key']) ? $pgcont['profile_personal_details_key'] : 'Personal Details'; ?></strong></h6>
                            </div>
                            <div class="card-body">
                                <?php 
                                $personal_btn = isset($pgcont['profile_personal_details_button_key']) ? $pgcont['profile_personal_details_button_key'] : Lang::string('settings-save-info');
                                     $personal->passwordInput('pass1',isset($pgcont['profile_personal_details_current_pass_key']) ? $pgcont['profile_personal_details_current_pass_key'] : 'Current Password', false, false, false, "form-control");
                                    $personal->passwordInput('pass',isset($pgcont['profile_personal_details_change_pass_key']) ? $pgcont['profile_personal_details_change_pass_key'] : Lang::string('settings-pass'), false, false, false, 'form-control');
                                    $personal->passwordInput('pass2',isset($pgcont['profile_personal_details_confirm_pass_key']) ? $pgcont['profile_personal_details_confirm_pass_key'] : Lang::string('settings-pass-confirm'),false,false,false,'form-control',false,false,'pass');
                                    $personal->textInput('first_name',isset($pgcont['profile_personal_details_first_name_key']) ? $pgcont['profile_personal_details_first_name_key'] : Lang::string('settings-first-name'), false, false, false, false, 'form-control');
                                    $personal->textInput('last_name',isset($pgcont['profile_personal_details_last_name_key']) ? $pgcont['profile_personal_details_last_name_key'] : Lang::string('settings-last-name'), false, false, false, false, 'form-control');
                                    $personal->textInput('phone',isset($pgcont['profile_personal_details_phone_key']) ? $pgcont['profile_personal_details_phone_key'] : Lang::string('settings-Phone'),false, false, false, false, 'form-control');
                                    $personal->textInput('email',isset($pgcont['profile_personal_details_email_key']) ? $pgcont['profile_personal_details_email_key'] : Lang::string('settings-email'),'email', false, false, false, 'form-control', 'disabled');

                                    $personal->HTML('<div class="form_button"><input type="submit" name="submit" value="'.$personal_btn.'" class="but_user btn" /></div><input type="hidden" name="submitted" value="1" />');
                                    $personal->hiddenInput('uniq',1,$_SESSION["settings_uniq"]);
                                    $personal->display();
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <h6><strong><?php echo isset($pgcont['profile_personal_details_key']) ? $pgcont['profile_personal_details_key'] : 'Account Activities'; ?></strong></h6>
                        <div class="last-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php echo isset($pgcont['profile_table_date_key']) ? $pgcont['profile_table_date_key'] : Lang::string('transactions-time'); ?></th>
                                        <th><?php echo isset($pgcont['profile_table_type_key']) ? $pgcont['profile_table_type_key'] : Lang::string('transactions-type'); ?></th>
                                        <th><?php echo isset($pgcont['profile_table_ip_address_key']) ? $pgcont['profile_table_ip_address_key'] : Lang::string('history-ip'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($history): ?>
                                        <?php foreach($history as $item): ?>
                                        <tr>
                                            <th><?=$item['date']?></th>
                                            <td><?= $item['type'].(($item['request_currency']) ? ' ('.$item['request_currency'].')' : false) ?></td>
                                            <td><?= (($item['ip']) ? $item['ip'] : 'N/A') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3">No records</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div><? echo $pagination; ?>
                    </div>
                </div>
            </div>
        </div>
        <!--modal-1-->
<div class="modal fade" id="referaltrans" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 700px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['profile_referal_transaction_key']) ? $pgcont['profile_referal_transaction_key'] : 'View transactions'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-top: 0;">
        <table id="info-data-table" class="table row-border table-hover balance-table table-border dataTable no-footer" cellspacing="0 " width="100% ">
            <thead>
                <tr>
                    <th><?php echo isset($pgcont['profile_referal_table_transaction_id_key']) ? $pgcont['profile_referal_table_transaction_id_key'] : 'Transaction ID'; ?></th>
                    <th><?php echo isset($pgcont['profile_referal_table_transaction_points_key']) ? $pgcont['profile_referal_table_transaction_points_key'] : 'Points used'; ?></th>
                    <th><?php echo isset($pgcont['profile_referal_table_transaction_amount_key']) ? $pgcont['profile_referal_table_transaction_amount_key'] : 'Amount'; ?></th>
                    <th><?php echo isset($pgcont['profile_referal_table_transaction_date_key']) ? $pgcont['profile_referal_table_transaction_date_key'] : 'Date & Time'; ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
        <?php include "includes/sonance_footer.php"; ?>

        <script type="text/javascript">

            function copy_referral() {
                var copyText = document.getElementById("referral_code");
                  copyText.select();
                  document.execCommand("Copy");
                  alert("Copied Referral code : " + copyText.value);
            }

            function copyToClipboard(element) {
              var $temp = $("<input>");
              $("body").append($temp);
              $temp.val($(element).text()).select();
              document.execCommand("copy");
              $temp.remove();
            }
            
            function showpasswordsettings_pass2(x1) {
              
            var x = document.getElementById("settings_pass2");
            if (x.type === "password") {
                x.type = "text";
                x1.classList.toggle("fa-eye");
            } else {
                x.type = "password";
                x1.classList.toggle('fa-eye-slash');
            }
        }
            function showpasswordsettings_pass(x1) {
              
            var x = document.getElementById("settings_pass");
            if (x.type === "password") {
                x.type = "text";
                x1.classList.toggle("fa-eye");
            } else {
                x.type = "password";
                x1.classList.toggle('fa-eye-slash');
            }
        }
            function showpasswordsettings_pass1(x1) {
              
            var x = document.getElementById("settings_pass1");
            if (x.type === "password") {
                x.type = "text";
                x1.classList.toggle('fa-eye');
            } else {
                x.type = "password";
                x1.classList.toggle('fa-eye-slash');
            }
        }
        </script>
    </body>
</html>