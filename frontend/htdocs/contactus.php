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
        <header>
            <div class="banner row">
                <div class="container content">
                    <h1>Contact</h1>
                </div>
            </div>
        </header>
       <div class="page-container">
            <div class="container">
                <div class="cms-outer">
                    <p class="m-b-1em">For general inquiries, you may contact us by filling out the following form. For support with using the site,<br/> please visit our <a href="https://bitexchange.cash/login.php" target="_blank">support portal.</a></p>
                   <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="info-table-outer">
                                <p><b>For General Inquiries</b></p>
                                <form>
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label>First Name</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                 <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control">
                                </div>
                                 <div class="form-group">
                                    <label>Company</label>
                                    <input type="text" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Subject</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Country</label>
                                    <select class="form-control">
                                        <option>1</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea rows="2" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn">Submit</button>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                             <div class="info-table-outer">
                                <h4 class="m-b-1em">BitExchange</h4>
                                <p class="m-b-0"><b>Office Hours:</b></p>
                                <p>Monday - Friday, 10:00 - 21:00 IST</p>
                                <p class="m-b-0"><b>E-Mail:</b></p>
                                <p>General :<a href="mailto:contact@bitexchange.systems">contact@bitexchange.systems</a></p>
                             </div>
                        </div>
                   </div>
                </div>
            </div>
       </div>
        <?php include "includes/sonance_footer.php"; ?>
        <script type="text/javascript" src="js/ops.js?v=20160210"></script>
</html>