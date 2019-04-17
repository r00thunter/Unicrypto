<!DOCTYPE html>
<html lang="en">
<?php include '../lib/common.php';
        
    if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
        Link::redirect('settings.php');
    elseif (User::$awaiting_token)
        Link::redirect('verify-token.php');
    elseif (!User::isLoggedIn())
        Link::redirect('login.php');
        
        if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']),$CFG->currencies)))
    $_SESSION['ba_c_currency'] = $_REQUEST['c_currency'];
else if (empty($_SESSION['ba_c_currency']))
    $_SESSION['ba_c_currency'] = $_SESSION['c_currency'];


$c_currency = $_SESSION['ba_c_currency'];
API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
API::add('Content','getRecord',array('bitcoin-addresses'));
$query = API::send();

$bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
$content = $query['Content']['getRecord']['results'][0];
$page_title = Lang::string('bitcoin-addresses');

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'add' && $_SESSION["btc_uniq"] == $_REQUEST['uniq']) {
    if (strtotime($bitcoin_addresses[0]['date']) >= strtotime('-1 day'))
        Errors::add('You can only add one new '.$CFG->currencies[$c_currency]['currency'] .' address every 24 hours.');
    
    if (!is_array(Errors::$errors)) {
        API::add('BitcoinAddresses','getNew',array($c_currency));
        API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
        $query = API::send();
        $bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
        
        Messages::add(Lang::string('bitcoin-addresses-added'));
        Link::redirect('cryptoaddress.php');

    }
}
$currencies = Settings::sessionCurrency();
$currency1 = $currencies['currency'];
$c_currency1 = $currencies['c_currency'];

API::add('Content','getRecord',array('fee-schedule'));
API::add('FeeSchedule','get',array($currency1));
$query = API::send();

$content = $query['Content']['getRecord']['results'][0];
$page_title = $content['title'];
$fee_schedule = $query['FeeSchedule']['get']['results'][0];
include "includes/sonance_header.php"; 
$_SESSION["btc_uniq"] = md5(uniqid(mt_rand(),true));
        ?>
    <style>
        footer{
            margin-top: 0;
        }
        .table th{
            background: #f1f1f1;
            border: none;
        }
        table.dataTable{
            margin: 0;
            border: 1px solid #ddd;
        }
    </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
        <header>
            <div class="banner row">
                <div class="container content">
                    <h1>Fees</h1>
                </div>
            </div>
        </header>
       <div class="page-container">
            <div class="container">
                <div class="cms-outer">
                    <h5 class="m-b-1em">Fees and commissions</h5>
                    <p><b>Deposits and withdrawals:</b> Free</p>
                    <p><b>Minimum Transaction:</b> $5 USD</p>
                    <div class="info-table-outer">
                       
                    <table class="table row-border info-data-table table-hover balance-table table-border">
                <tr>
                    <th><?= Lang::string('fee-schedule-fee1') ?></th>
                    <th><?= Lang::string('fee-schedule-fee') ?></th>
                    <th>
                        <?= Lang::string('fee-schedule-volume') ?>
                        <span class="graph_options" style="margin-left:5px;">
                            <span style="margin:0;float:none;display:inline;">
                                <select id="fee_currency">
                                <? 
                                if ($CFG->currencies) {
                                    foreach ($CFG->currencies as $key => $currency) {
                                        if (is_numeric($key) || $currency['id'] == $c_currency1)
                                            continue;
                                        
                                        echo '<option '.($currency['id'] == $currency1 ? 'selected="selected"' : '' ).' value="'.$currency['id'].'">'.$currency['currency'].'</option>';
                                    }
                                }
                                ?>
                                </select>
                            </span>
                        </span>
                    </th>
                    <th><?= Lang::string('fee-schedule-flc') ?></th>
                </tr>
                <? 
                if ($fee_schedule) {
                    $last_fee1 = false;
                    $last_btc = false;
                    foreach ($fee_schedule as $fee) {
                        $symbol = ($fee['to_usd'] > 0) ? '<' : '>';
                        $from = ($fee['to_usd'] > 0) ? Stringz::currency($fee['to_usd'],0) : Stringz::currency($fee['from_usd'],0);
                ?>
                <tr>
                    <?= ($fee['fee1'] != $last_fee1) ? '<td>'.$fee['fee1'].'%</td>' : '<td class="inactive"></td>' ?>
                    <td><?= $fee['fee'] ?>%</td>
                    <td><?= $symbol.' '.$fee['fa_symbol'].$from ?></td>
                    <?= ($fee['global_btc'] != $last_btc) ? '<td>'.Stringz::currency($fee['global_btc']).' '.$CFG->currencies[$c_currency1]['currency'].'</td>' : '<td class="inactive"></td>' ?>
                </tr>
                <?
                        $last_fee1 = $fee['fee1'];
                        $last_btc = $fee['global_btc'];
                    }
                }
                ?>
            </table>
                    </div>
                </div>
            </div>
       </div>
        <?php include "includes/sonance_footer.php"; ?>
        <script type="text/javascript" src="js/ops.js?v=20160210"></script>
</html>