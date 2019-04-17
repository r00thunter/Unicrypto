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
        }    </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>

         <?php
        $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='how it works'");
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
                    <h1><?php echo isset($pgcont['how_it_works_heading_key']) ? $pgcont['how_it_works_heading_key'] : 'How Does Bitcoin Work?'; ?></h1>
                </div>
            </div>
        </header>
       <div class="page-container">
            <div class="container">
                <div class="cms-outer">
                    <?php echo isset($pgcont['how_it_works_content_key']) ? $pgcont['how_it_works_content_key'] : '<h5 class="m-b-1em">How does Bitcoin work?</h5>
                    <p>This is a question that often causes confusion. Here"s a quick explanation!</p>
                    <p><b>The basics for a new user</b></p>
                    <p>As a new user, you can get started with Bitcoin without understanding the technical details. Once you have installed a Bitcoin wallet on your computer or mobile phone, it will generate your first Bitcoin address and you can create more whenever you need one. You can disclose your addresses to your friends so that they can pay you or vice versa. In fact, this is pretty similar to how email works, except that Bitcoin addresses should only be used once.</p>
                    <p><b>Balances - block chain</b></p>
                    <p>The block chain is a <b>shared public ledger</b> on which the entire Bitcoin network relies. All confirmed transactions are included in the block chain. This way, Bitcoin wallets can calculate their spendable balance and new transactions can be verified to be spending bitcoins that are actually owned by the spender. The integrity and the chronological order of the block chain are enforced with cryptography.</p>
                    <p><b>Transactions - private keys</b></p>
                    <p>A transaction is <b>a transfer of value between Bitcoin wallets</b> that gets included in the block chain. Bitcoin wallets keep a secret piece of data called a private key or seed, which is used to sign transactions, providing a mathematical proof that they have come from the owner of the wallet. The signature also prevents the transaction from being altered by anybody once it has been issued. All transactions are broadcast between users and usually begin to be confirmed by the network in the following 10 minutes, through a process called mining.</p>
                    <p><b>Processing - mining</b></p>
                    <p>Mining is a <b>distributed consensus system</b> that is used to confirm waiting transactions by including them in the block chain. It enforces a chronological order in the block chain, protects the neutrality of the network, and allows different computers to agree on the state of the system. To be confirmed, transactions must be packed in a block that fits very strict cryptographic rules that will be verified by the network. These rules prevent previous blocks from being modified because doing so would invalidate all following blocks. Mining also creates the equivalent of a competitive lottery that prevents any individual from easily adding new blocks consecutively in the block chain. This way, no individuals can control what is included in the block chain or replace parts of the block chain to roll back their own spends.</p>
                    <p><b>Going down the rabbit hole</b></p>
                    <p>This is only a very short and concise summary of the system. If you want to get into the details, you can <a href="https://bitcoin.org/bitcoin.pdf" rel="nofollow" target="_blank">read the original paper</a> that describes the system"s design, and explore the Bitcoin wiki.</p>
                    <p><b>Source:<a href="https://bitcoin.org/en/" target="_blank" rel="nofollow">"www.bitcoin.org"</a></b></p>'; ?>
                </div>
            </div>
       </div>
        <?php include "includes/sonance_footer.php"; ?>
        <script type="text/javascript" src="js/ops.js?v=20160210"></script>
        
</html>