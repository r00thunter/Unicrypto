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
include "includes/sonance_header.php"; 
$_SESSION["btc_uniq"] = md5(uniqid(mt_rand(),true));
        ?>
    <style>
        footer{
            margin-top: 0;
        }
    </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
     <?php
        $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='about us'");
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
                      <h1><?php echo isset($pgcont['about_us_heading_key']) ? $pgcont['about_us_heading_key'] : 'About'; ?></h1>
                </div>
            </div>
        </header>
       <div class="page-container">
            <div class="container">
                <div class="cms-outer">
                    <?php echo isset($pgcont['about_us_content_key']) ? $pgcont['about_us_content_key'] : ' <h5 class="m-b-1em">A Unique Exchange With a Unique Background</h5>
                    <p><b><?= $CFG->exchange_name; ?></b> is a unique cryptocurrency exchange created with the goal of making cryptocurrency trading easier and more reliable. We have partnered with cryptocurrency pioneers <a href="https://cryptocapital.co/" target="_blank" rel="nofollow">Crypto Capital Corp</a>, using their banking platform to provide users the convenience of 30 different fiat currencies on a single account, helping to foster a closer integration between crypto and fiat currencies.</p>
                    <p>We constantly improve in order to answer the needs of our users, tighten security and provide features that will create a new standard for cryptocurrency exchange platforms.</p>
                    <p>We strive to provide our users with an exchange platform environment that is both safe and secure, as well as fair and ethical. Ensuring a trusted and reliable trading environment is a top priority for <?= $CFG->exchange_name; ?>. For this reason, we have instituted circuit-breaker measures to protect average users against excessive volatility and manipulation of the market.</p>
                    <p>The measures instituted are the following:</p>
                    <p>
                        1.We won"t allow placing buy-limit orders at 35% or more below the market price.<br/>
                        2.We won"t allow placing buy-limit orders at a price below that of the user"s sell-stop orders.<br/>
                        3.We won"t allow placing sell-stop orders at a price above that of the user"s buy-limit orders.
                    </p>
                    <p>These measures will help ensure that the market functions in an equitable way for all users.</p>
                    <p><a href="register.php">Continue to the registration page ></a></p>'; ?>
                </div>
            </div>
       </div>
        <?php include "includes/sonance_footer.php"; ?>
        <script type="text/javascript" src="js/ops.js?v=20160210"></script>
</html>