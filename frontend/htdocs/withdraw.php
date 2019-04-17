<?php
 include '../lib/common.php';

// if(empty(User::$ekyc_data) || User::$ekyc_data[0]->status != 'accepted')
// {
//     Link::redirect('ekyc');
// }

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
    Link::redirect('userprofile');
elseif (User::$awaiting_token)
    Link::redirect('verify-token');
elseif (!User::isLoggedIn())
    Link::redirect('login');

$currencies = Settings::sessionCurrency();
API::add('Wallets','getWallet',array($currencies['c_currency']));
$query = API::send(); 

$wallet = $query['Wallets']['getWallet']['results'][0];
$c_currency_info = $CFG->currencies[$currencies['c_currency']];
$page1 = (!empty($_REQUEST['page'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['page']) : false;
$btc_address1 = (!empty($_REQUEST['btc_address'])) ?  preg_replace("/[^\da-z]/i", "",$_REQUEST['btc_address']) : false;
$btc_amount1 = (!empty($_REQUEST['btc_amount'])) ? Stringz::currencyInput($_REQUEST['btc_amount']) : 0;
$btc_total1 = ($btc_amount1 > 0) ? $btc_amount1 - $wallet['bitcoin_sending_fee'] : 0;
$account1 = (!empty($_REQUEST['account'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['account']) : false;
$fiat_amount1 = (!empty($_REQUEST['fiat_amount'])) ? Stringz::currencyInput($_REQUEST['fiat_amount']) : 0;
$fiat_total1 = ($fiat_amount1 > 0) ? $fiat_amount1 - $CFG->fiat_withdraw_fee : 0;
$token1 = (!empty($_REQUEST['token'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['token']) : false;
$authcode1 = (!empty($_REQUEST['authcode'])) ? $_REQUEST['authcode'] : false;
$request_2fa = false;
$no_token = false;

if ((!empty($_REQUEST['bitcoins']) || !empty($_REQUEST['fiat'])) && !$token1) {
    if (!empty($_REQUEST['request_2fa'])) {
        if (!($token1 > 0)) {
            $no_token = true;
            $request_2fa = true;
            Errors::add(Lang::string('security-no-token'));
        }
    }

    if ((User::$info['verified_authy'] == 'Y'|| User::$info['verified_google'] == 'Y') && ((User::$info['confirm_withdrawal_2fa_btc'] == 'Y' && $_REQUEST['bitcoins']) || (User::$info['confirm_withdrawal_2fa_bank'] == 'Y' && $_REQUEST['fiat']))) {
        if (!empty($_REQUEST['send_sms']) || User::$info['using_sms'] == 'Y') {
            if (User::sendSMS()) {
                $sent_sms = true;
                Messages::add(Lang::string('withdraw-sms-sent'));
            }
        }
        $request_2fa = true;
    }
}

if ($authcode1) {
    API::add('Requests','emailValidate',array(urlencode($authcode1)));
    $query = API::send();

    if ($query['Requests']['emailValidate']['results'][0]) {
        Link::redirect('withdraw?message=withdraw-2fa-success');
    }
    else {
        Errors::add(Lang::string('settings-request-expired'));
    }
}

API::add('Content','getRecord',array('deposit-no-bank'));
API::add('User','getAvailable');
API::add('Requests','get',array(1,false,false,1));
API::add('Requests','get',array(false,$page1,15,1));
API::add('BankAccounts','get');
if ($account1 > 0)
    API::add('BankAccounts','getRecord',array($account1));
if ($btc_address1)
    API::add('BitcoinAddresses','validateAddress',array($currencies['c_currency'],$btc_address1));

$query = API::send();
//if last withdraw is verify redirect the withdraw page
if($_REQUEST['notice'] == 'email'){
	if($query['Requests']['get']['results'][1][0]['request_status'] != 4){
            Link::redirect('withdraw');
	}
}


$user_available = $query['User']['getAvailable']['results'][0];
$bank_instructions = $query['Content']['getRecord']['results'][0];
$bank_accounts = $query['BankAccounts']['get']['results'][0];
// echo "<pre>"; print_r($bank_accounts); exit;
$total = $query['Requests']['get']['results'][0];
$requests = $query['Requests']['get']['results'][1];

$pagination = Content::pagination('withdraw.php', $page1, $total, 15, 5, false);

if ($account1 > 0) {
    $bank_account = $query['BankAccounts']['getRecord']['results'][0];
}
elseif ($bank_accounts) {
    $key = key($bank_accounts);
    $bank_account = $bank_accounts[$key];   
}

if ($bank_account) {
    $currency_info = $CFG->currencies[$bank_account['currency']];
    $currency1 = $currency_info['currency'];
}


if ($CFG->withdrawals_status == 'suspended')
    Errors::add(Lang::string('withdrawal-suspended'));

if (!empty($_REQUEST['bitcoins'])) {
    if (($btc_amount1 - $wallet['bitcoin_sending_fee']) < 0.00000001)
        Errors::add(Lang::string('withdraw-amount-zero'));
    if ($btc_amount1 > $user_available[$c_currency_info['currency']])
        Errors::add(str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('withdraw-too-much')));
    if (!$query['BitcoinAddresses']['validateAddress']['results'][0])
        Errors::add(str_replace('[c_currency]',$c_currency_info['currency'],Lang::string('withdraw-address-invalid')));
    
    if (!is_array(Errors::$errors)) {
        if (User::$info['confirm_withdrawal_email_btc'] == 'Y' && !$request_2fa && !$token1) {
            API::add('Requests','insert',array($c_currency_info['id'],$btc_amount1,$btc_address1));
            $query = API::send();
            Link::redirect('withdraw?notice=email');
        }
        elseif (!$request_2fa) {
            API::token($token1);
            API::add('Requests','insert',array($c_currency_info['id'],$btc_amount1,$btc_address1));
            $query = API::send();
            
            if ($query['error'] == 'security-com-error')
                Errors::add(Lang::string('security-com-error'));
            
            if ($query['error'] == 'authy-errors')
                Errors::merge($query['authy_errors']);
            
            if ($query['error'] == 'security-incorrect-token')
                Errors::add(Lang::string('security-incorrect-token'));
            
            if (!is_array(Errors::$errors)) {
                if ($query['Requests']['insert']['results'][0]) {
                    if ($token1 > 0)
                        Link::redirect('withdraw?message=withdraw-2fa-success');
                    else
                        Link::redirect('withdraw?message=withdraw-success');
                }   
            }
            elseif (!$no_token) {
                $request_2fa = true;
            }
        }
    }
    elseif (!$no_token) {
        $request_2fa = false;
    }
}
elseif (!empty($_REQUEST['fiat'])) {
    if (!($account1 > 0))
        Errors::add(Lang::string('withdraw-no-account'));
    if (!is_array($bank_account))
        Errors::add(Lang::string('withdraw-account-not-found'));
    if (!($fiat_amount1 > 0))
        Errors::add(Lang::string('withdraw-amount-zero'));
    if ($fiat_amount1 > 0 && $fiat_amount1 < 1)
        Errors::add(Lang::string('withdraw-amount-one'));
    if (!$bank_accounts[$bank_account['account_number']])
        Errors::add(Lang::string('withdraw-account-not-found'));
    if ($fiat_amount1 > $user_available[strtoupper($currency1)])
        Errors::add(Lang::string('withdraw-too-much'));
        
    if (!is_array(Errors::$errors)) {
        if (User::$info['confirm_withdrawal_email_bank'] == 'Y' && !$request_2fa && !$token1) {
            API::add('Requests','insert',array($bank_account['currency'],$fiat_amount1,false,$bank_account['account_number']));
            $query = API::send();
            Link::redirect('withdraw?notice=email');
        }
        elseif (!$request_2fa) {
            API::token($token1);
            API::add('Requests','insert',array($bank_account['currency'],$fiat_amount1,false,$bank_account['account_number']));
            $query = API::send();
            
            if ($query['error'] == 'security-com-error')
                Errors::add(Lang::string('security-com-error'));
                
            if ($query['error'] == 'authy-errors')
                Errors::merge($query['authy_errors']);
            
            if ($query['error'] == 'security-incorrect-token')
                Errors::add(Lang::string('security-incorrect-token'));
                
            if (!is_array(Errors::$errors)) {
                if ($query['Requests']['insert']['results'][0]) {
                    if ($token1 > 0)
                        Link::redirect('withdraw?message=withdraw-2fa-success');
                    else
                        Link::redirect('withdraw?message=withdraw-success');
                }
            }
            elseif (!$no_token) {
                $request_2fa = true;
            }
        }
    }
    elseif (!$no_token) {
        $request_2fa = false;
    }
}

if (!empty($_REQUEST['message'])) {
    if ($_REQUEST['message'] == 'withdraw-2fa-success')
        Messages::add(Lang::string('withdraw-2fa-success'));
    elseif ($_REQUEST['message'] == 'withdraw-success')
        Messages::add(Lang::string('withdraw-success'));
}

if (!empty($_REQUEST['notice']) && $_REQUEST['notice'] == 'email')
    $notice = Lang::string('withdraw-email-notice');

	$page_title = Lang::string('withdraw');
    
                
    ?>
<!DOCTYPE html>
<html lang="en">
    <style>
        .input-caption {
        position: relative;
        float: right;
        top: -28px;
        right: 6px;
        height: 28px;
        padding-top: 5px;
        }
        .custom-select
        {
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 2px;
        height: 28px !important;
        }
        label.cont
        {
            width:100%;
        }
        .pull-right 
        {
            float:right;
        }

        .current-otr p 
        {
            margin: 5px 0;
        }
        .left-side-widget .nav-link:hover,
        .left-side-widget .nav-link:focus,
        .left-side-widget .nav-link:visited,
        .left-side-widget .nav-link:active{
            color:#000 !important;
        }
    </style>
   <!DOCTYPE html>
<html lang="en">
    <?php include "includes/sonance_header.php";  ?>
    <body id="wrapper">
   <?php include "includes/sonance_navbar.php"; ?>
    <?php
    $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='withdraw'");
    while($pagerow=mysqli_fetch_array($page_sql))
    {
        $page_id=$pagerow['id'];
    }
    $pg_cont_sql=mysqli_query($conn_l, "select page_content_key, ".$_SESSION[LANG]."_page_content from trans_page_value where page_content_status=1 and page_id='".$page_id."'");
    while($pagecontrow=mysqli_fetch_array($pg_cont_sql))
    {
        $pgcont[$pagecontrow['page_content_key']]=$pagecontrow[$_SESSION[LANG].'_page_content'];
    }       
    ?>
   <header>
            <div class="banner row">
                <div class="container content">
                    <h1><?php echo isset($pgcont['withdraw_heading_key']) ? $pgcont['withdraw_heading_key'] : 'WITHDRAW'; ?></h1>
                    <p class="text-white text-center"><?php echo isset($pgcont['withdraw_sub_heading_key']) ? $pgcont['withdraw_sub_heading_key'] : 'Create a Withdrawal request, view your Withdrawal transaction history.'; ?></p>
                    <div class="text-center">
                      <a href="manageaccounts.php" class="btn" style="background: #007bff !important;"><?php echo isset($pgcont['withdraw_heading_button_key']) ? $pgcont['withdraw_heading_button_key'] : 'Manage Bank Account'; ?></a> 
                    </div>
                </div>
            </div>
        </header>
    <div class="page-container">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="pro card">
                        <div class="card-header">
                            <h6>
                                <strong><?php echo isset($pgcont['withdraw_fiat_currency_key']) ? $pgcont['withdraw_fiat_currency_key'] : 'Withdraw Fiat Currency'; ?></strong>
                                <span class="float-right">
                                    <a href="#fiatcurrency" data-toggle="modal">
                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" >
                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"/>
                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"/>
                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                        C26.355,23.457,25,26.261,25,29.158V32"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                </a>
                                </span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="deposite-otr">
                                <form id="buy_form" action="withdraw.php" method="POST">
                                    <input type="hidden" name="action" value="add">
                                     <? Errors::display(); ?>
                                    <? Messages::display(); ?>
                                    <?php if(!empty($notice)): ?>
                                    <div class="notice">
                                        <div class="message-box-wrap alert alert-info"><?=$notice?></div>
                                    </div>
                                    <?php endif; ?>          
                                    <div class="form-group">
                                        <!-- <label>Available USD</label>
                                        <p>$0</p> -->
                                            <?= str_replace('[currency]','<span class="currency_label">'.$currency_info['currency'].'</span>',(isset($pgcont['withdraw_form_available_key']) ? $pgcont['withdraw_form_available_key'] :'Available')) ?> USD</div>
                                    <div class="value"><span class="currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="user_available"><?= Stringz::currency($user_available[strtoupper($currency1)]) ?>
                                  </div>
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo isset($pgcont['withdraw_bank_account_key']) ? $pgcont['withdraw_bank_account_key'] : 'Receiving Bank Account'; ?></label>
                                           <select id="withdraw_account"  class="form-control" name="account">
                                                <?
                                                if ($bank_accounts) {
                                                    foreach ($bank_accounts as $account) {
                                                        echo '<option '.(($bank_account['id'] == $account['id']) ? 'selected="selected"' : '').' value="'.$account['id'].'">'.$account['account_number'].' - ('.$account['currency'].')</option>';
                                                    }
                                                }   
                                                ?>
                                            </select>
                                    </div>
                                    
                                    <div class="form-group">

                                        <label><?php echo isset($pgcont['withdraw_amount_key']) ? $pgcont['withdraw_amount_key'] : 'Amount to Withdraw'; ?></label>
                                        <div class="input-group mb-3">
                                      <input type="text" class="form-control" id="fiat_amount"  name="fiat_amount" value="<?= Stringz::currencyOutput($fiat_amount1) ?>" onblur="receiveusd();" aria-describedby="basic-addon2">
                                      <div class="input-group-append">

                                        <span class="input-group-text" id="basic-addon2">USD</span>
                                      </div>
                                    </div>
                                    <!-- <div class="qualify"><span class="currency_label"><?= $currency_info['currency'] ?></span></div> -->
                                   
                                    <div class="form-group">
                                        <label><?php echo isset($pgcont['withdraw_fee_key']) ? $pgcont['withdraw_fee_key'] : 'Fee'; ?></label>
                                        <div class="Flex__Flex-fVJVYW gkSoIH">
                                          <div class="form-group">
                                          <span class="currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="withdraw_fiat_fee"><?= Stringz::currencyOutput($CFG->fiat_withdraw_fee) ?>
                                          </div>
                                        <!-- <p>$1</p> -->
                                    </div>
                                    <div class="form-group">
                                        <label>USD <?php echo isset($pgcont['withdraw_receive_key']) ? $pgcont['withdraw_receive_key'] : 'to Receive'; ?></label>
                                       <!--  <p>$0.00</p> -->
                                       <p><span class="currency_char"><?= $currency_info['fa_symbol'] ?></span><span id="withdraw_fiat_total"><?= Stringz::currency($fiat_total1) ?></span></p>
                                    </div>
                                    </div>
                                     <input type="hidden" name="fiat" value="1" />
                                    <div class="form-group">
                                        <button type="submit" class="btn"><?php echo isset($pgcont['withdraw_add_key']) ? $pgcont['withdraw_add_key'] : 'Withdraw Currency'; ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="pro card">
                        <div class="card-header">
                            <h6><strong><?php echo isset($pgcont['withdraw_fiat_wallet_key']) ? $pgcont['withdraw_fiat_wallet_key'] : 'Fiat Wallet'; ?></strong>
                                 <!-- <span class="float-right">
                                    <a href="#dephistory" data-toggle="modal">
                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" >
                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"/>
                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"/>
                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                        C26.355,23.457,25,26.261,25,29.158V32"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                </a>
                                </span> -->
                            </h6>
                        </div>
                        
                        <div class="card-body">
                            <div class="media">
                          <img class="mr-3" src="images/dollar.png" alt="" width="40" height="40">
                          <div class="media-body">
                            <p class="mb-0"><b>USD <?php echo isset($pgcont['withdraw_wallet_key']) ? $pgcont['withdraw_wallet_key'] : 'Wallet'; ?></b></p>
                            <span>   $ <?= Stringz::currency($user_available[
                                          'USD'
                                        ]) ?></span>
                             <div class="">
                                <a href="deposit.php"  class="btn text-black"><?php echo isset($pgcont['withdraw_heading_wallet_button_key']) ? $pgcont['withdraw_heading_wallet_button_key'] : 'Deposit'; ?></a>
                                <a href="manageaccounts.php" class="btn" style="background: #007bff !important;"><?php echo isset($pgcont['withdraw_heading_button_key']) ? $pgcont['withdraw_heading_button_key'] : 'Manage Bank Account'; ?></a> 
                            </div>
                          </div>
                        </div>
                        </div>
                    </div>
                    <div class="pro card">
                        <div class="card-header">
                            <h6><strong><?php echo isset($pgcont['withdraw_table_history_head_key']) ? $pgcont['withdraw_table_history_head_key'] : 'History'; ?></strong>
                                 <span class="float-right">
                                    <a href="#dephistory" data-toggle="modal">
                                    <svg style="width:15px;height:15px;" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                         viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve" >
                                    <circle style="fill:#47a0dc" cx="25" cy="25" r="25"/>
                                    <line style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" x1="25" y1="37" x2="25" y2="39"/>
                                    <path style="fill:none;stroke:#FFFFFF;stroke-width:4;stroke-linecap:round;stroke-miterlimit:10;" d="M18,16
                                        c0-3.899,3.188-7.054,7.1-6.999c3.717,0.052,6.848,3.182,6.9,6.9c0.035,2.511-1.252,4.723-3.21,5.986
                                        C26.355,23.457,25,26.261,25,29.158V32"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                </a>
                                </span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="">
                     
                         <?php if($requests) {
                            ?>
                                <table class="table table-border">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?php echo isset($pgcont['withdraw_table_history_table_th_date_key']) ? $pgcont['withdraw_table_history_table_th_date_key'] : 'Date'; ?>
                                            </th>
                                            <!-- <th scope="col">Type</th> -->
                                            <th scope="col"><?php echo isset($pgcont['withdraw_table_history_table_th_amount_key']) ? $pgcont['withdraw_table_history_table_th_amount_key'] : 'Amount'; ?></th>
                                            <th scope="col"><?php echo isset($pgcont['withdraw_table_history_table_th_status_key']) ? $pgcont['withdraw_table_history_table_th_status_key'] : 'Status'; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                              <?
                    
                        foreach ($requests as $request) {

                            if($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') continue;
                            ?>
                                        <tr>
                                            <th scope="row"><?php 
                                            // $d = date_create($request['date']);
                                            // echo date_format($d, "M"); 
                                            echo date("d-m-Y",strtotime($request['date']));
                                            ?>
                                                <?php echo date_format($d, "d"); ?></th>
                                            <td><?= (($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency((($request['net_amount'] > 0) ? $request['net_amount'] : ($request['amount'] - $request['fee'])),true).' '.$request['fa_symbol'] : $request['fa_symbol'].Stringz::currency((($request['net_amount'] > 0) ? $request['net_amount'] : ($request['amount'] - $request['fee'])))) ?>
                                            </td>
                                            <td> <?= $request['status'] ?></td>
                                            <!-- <td>0.000754</td> -->
                                        </tr>

                                     <?
                                        }
                                        ?>

                                    
                                                <?/* Total amount withdrawn
                                                 (($CFG->currencies[$request['currency']]['is_crypto'] == 'Y') ? Stringz::currency($request['amount'],true).' '.$request['fa_symbol'] : $request['fa_symbol'].Stringz::currency($request['amount'])) */?>
                                        
                                    </tbody>
                                </table>
                               <?php 
                                     } else {
                                        echo '<div style="padding:20% 35%;">';
                                        echo isset($pgcont['withdraw_table_history_no_record_key']) ? $pgcont['withdraw_table_history_no_record_key'] : 'No Withdraw found';
                                        echo '</div>';
                                    }
                                    ?>
                                    <?php echo $pagination; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal-1-->
<div class="modal fade" id="fiatcurrency" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['withdraw_fiat_currency_trans_modal_head_key']) ? $pgcont['withdraw_fiat_currency_trans_modal_head_key'] : 'Withdraw Fiat Currency'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['withdraw_fiat_currency_trans_modal_content_key']) ? $pgcont['withdraw_fiat_currency_trans_modal_content_key'] : 'Here you can Place a Withdraw requests.'; ?></p>
        
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="dephistory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo isset($pgcont['withdraw_table_history_modal_head_key']) ? $pgcont['withdraw_table_history_modal_head_key'] : 'Withdrawal Transaction'; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo isset($pgcont['withdraw_table_history_modal_content_key']) ? $pgcont['withdraw_table_history_modal_content_key'] : 'View all your withdrawal transaction details.'; ?></p>      
      </div>
    </div>
  </div>
</div>
      <?php include "includes/sonance_footer.php"; ?>
<!-- <script type="text/javascript" src="js/ops.js?v=20160210"></script> -->
<!--     <script type="text/javascript">
    $(document).ready(function() {
        // $('.selectpicker').select2();
    });
    </script> -->
</body>
<script type="text/javascript">
	function receiveusd() {
	    var x = document.getElementById("fiat_amount").value;
	    var y = "<?php echo $CFG->fiat_withdraw_fee; ?>";
	    var res = 0;
	    if(x != 0){
	   		 var res = parseInt(x) - parseInt(y);
	    }
		$('#withdraw_fiat_total').html(res);
	}
</script>
</html>