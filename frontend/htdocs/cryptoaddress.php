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

        if ($_REQUEST['action'] == 'add' && ($c_currency == 28 || $c_currency == 42 || $c_currency == 45 || $c_currency == 46 || $c_currency == 47)) {
            

            if ($c_currency == 28) {
                    $url = "http://$btc_ip/api/address";
            }elseif ($c_currency == 42) {
                    $url = "http://$ltc_ip/api/address";
            }elseif ($c_currency == 45) {
                    $url = "http://$eth_ip/api";
            }elseif ($c_currency == 46) {
                    $url = "http://$xrp_ip/api";
            }elseif ($c_currency == 47) {
                    $url = "http://$xlm_ip/api";
            }
          // $url = "http://18.219.243.233:9000/";
            // print_r($url);
            // die();
          $ch = curl_init($url);

          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          
          # Return response instead of printing.
          curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
          
          # Send request.
          $result = curl_exec($ch);
          
          curl_close($ch);
          
          if ($is_error) {
              unset($is_error);          
          }

          $result = json_decode($result);
          // $result_password = 0;
          // $result_address_hex = '0';
          // print_r($result);
          // echo "<br><br>hello<br>";
          // print_r($result);
          // echo $result->status;
          // echo "<br><br>";
          // print_r($result->body->response->data->address);
          // echo $result->body->address->address;
          // echo "<br><br>hello1<br>";
          // echo $result->body->address->address;
           // print_r(array($c_currency,$result_address,$result_password,$result_address_hex));
          // die();
          if ($result->status == "success") {
                // echo "<br><br>".$result->body;
                if ($c_currency == 46) {
                    $result_address = $result->body->address->stellarAddress;
                    $address_key = $result->body->address->stellarSeed;
                    API::add('BitcoinAddresses','getNewMethode',array($c_currency,$result_address,$address_key));
                }elseif ($c_currency == 45) {
                    $result_address = $result->body->address;
                    $address_key = $result->body->password;
                    API::add('BitcoinAddresses','getNewMethode',array($c_currency,$result_address,$address_key));
                }elseif ($c_currency == 47) {
                    $result_address = $result->body->address->address;
                    $address_key = $result->body->address->secret;
                    API::add('BitcoinAddresses','getNewMethode',array($c_currency,$result_address,$address_key));
                }else{
                    $result_address = $result->body->response->data->address;
                    API::add('BitcoinAddresses','getNewMethode',array($c_currency,$result_address));
                }
                
                API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
                $query = API::send();
                // print_r($query['BitcoinAddresses']['getNewMethode']['results'][0]);exit;
                $bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
                Messages::add(Lang::string('bitcoin-addresses-added'));
          }else{
                    // API::add('BitcoinAddresses','getNew',array($c_currency));
                    API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
                    $query = API::send();
                    $bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
        
                    Errors::add('Sorry address can not created.');


          }
          // die();


        }else{

             API::add('BitcoinAddresses','getNew',array($c_currency));
        API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
        $query = API::send();
        $bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
        
        Messages::add(Lang::string('bitcoin-addresses-added'));
        // if($c_currency==45)
        // {
        // Link::redirect('cryptoaddress.php?c_currency='.$c_currency);
        // }

        }



       
    }
}
$_SESSION["btc_uniq"] = md5(uniqid(mt_rand(),true));
include "includes/sonance_header.php"; 
        ?>
    <style>
        .custom-select {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 2px;
            height: 28px !important;
        }
        .messages,.errors {
            list-style-type: none;
            background: #DFFBE4;
            padding: 15px;
            border-radius: 3px;
            position: relative;
            font-size: 14px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            z-index: 999;
            max-width: 400px;
            margin: 1em 0 0 auto;
        }
        .errors {
            background: #fdbdc3;
        }
    </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
         <?php
        $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='crypto address'");
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
        <header>
            <div class="banner row">
                <div class="container content">
                    <h1><?php echo isset($pgcont['crypto_address_heading_key']) ? $pgcont['crypto_address_heading_key'] : 'Crypto Addresses'; ?></h1>
                </div>
            </div>
        </header>
        <div class="page-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                    <? Messages::display(); ?>
                    <? Errors::display(); ?>
                   

                    <br><? if($c_currency != 45){ echo isset($pgcont['crypto_address_heading_key']) ? $pgcont['crypto_address_heading_key'] : $content['content'];  }?>
                    <div class="form-group">
                        <select id="c_currency" class="form-control" style="    margin-top: 1em;height: 40px;">
                            <option value="">--Select Currency--</option>
                        <? 
                        foreach ($CFG->currencies as $key => $currency1) {
                            if (is_numeric($key) || $currency1['is_crypto'] != 'Y')
                                continue;
                            
                            echo '<option value="'.$currency1['id'].'" '.($currency1['id'] == $c_currency ? 'selected="selected"' : '').'>'.$currency1['currency'].'</option>';
                        }
                        ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <? if($c_currency != 45 || ($c_currency == 45 && empty($bitcoin_addresses)) ) { ?>
                    <a class="btn btn-primary cust-btn" href="cryptoaddress.php?action=add&c_currency=<?= $c_currency ?>&uniq=<?= $_SESSION["btc_uniq"] ?>" class="but_user" > <?php echo isset($pgcont['crypto_address_button_key']) ? $pgcont['crypto_address_button_key'] : Lang::string('bitcoin-addresses-add'); ?></a>
                    <? } ?>
                    </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <div class="info-table-outer">
                            <input type="hidden" id="refresh_transactions" value="1" />
                            <input type="hidden" id="page" value="<?= $page1 ?>" />
                            <table id="info-data-table " class="table row-border info-data-table table-hover balance-table" cellspacing="0 " width="100% ">
                                <thead>
                                    <tr>
                                    <th><?php echo isset($pgcont['crypto_address_table_head_currency_key']) ? $pgcont['crypto_address_table_head_currency_key'] : Lang::string('currency'); ?></th>
                                    <th><?php echo isset($pgcont['crypto_address_table_head_date_key']) ? $pgcont['crypto_address_table_head_date_key'] : Lang::string('bitcoin-addresses-date'); ?></th>
                                    <th><?php echo isset($pgcont['crypto_address_table_head_address_key']) ? $pgcont['crypto_address_table_head_address_key'] : Lang::string('bitcoin-addresses-address'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <? 
                    if ($bitcoin_addresses) {
                        foreach ($bitcoin_addresses as $address) {
                    ?>
                    <tr>
                        <td><?= $CFG->currencies[$address['c_currency']]['currency'] ?></td>
                        <td><input type="hidden" class="localdate" value="<?= (strtotime($address['date']) + $CFG->timezone_offset) ?>" /></td>
                        <td><?= $address['address'] ?></td>
                    </tr>
                    <?
                        }
                    }
                    else {
                        echo '<tr><td colspan="3" style="padding:0;"><div class="" style=" text-align:center;    background: #f4f6f8;
                        "><img src="images/no-results.gif" style="width: 300px;height: auto;float:none;" ></div></td></tr>';
                    }
                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "includes/sonance_footer.php"; ?>
    
        <script type="text/javascript" src="js/ops.js?v=20160210"></script>
</html>