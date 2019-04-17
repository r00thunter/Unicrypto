<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include '../lib/common.php';
        
    // if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
    //     Link::redirect('settings.php');
    // elseif (User::$awaiting_token)
    //     Link::redirect('verify-token.php');
    // elseif (!User::isLoggedIn())
    //     Link::redirect('login.php');
        
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
    <?php
        $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='terms and condition'");
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
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
        <header>
            <div class="banner row">
                <div class="container content">
                    <h1><?php echo isset($pgcont['terms_condition_heading_key']) ? $pgcont['terms_condition_heading_key'] : 'Terms and Conditions of Use'; ?></h1>
                </div>
            </div>
        </header>
       <div class="page-container">
            <div class="container">
                <div class="cms-outer">
                   <?php echo isset($pgcont['terms_condition_content_key']) ? $pgcont['terms_condition_content_key'] : ''; ?>
                   <div style="a">
                        <h5 class="m-b-1em">Terms Of Service</h5>
                    <p>The <?= $CFG->exchange_name; ?> trading platform is currently in beta (testing phase). By using this Site, and further by registering to use our Service, you "You” are agreeing to accept and comply with the terms and conditions of use stated below ("Terms of Use"). You should read the entire Terms of Use carefully before you use this web site ("Site") or any of the services of this Site.</p>
                    <p>As used herein, "<?= $CFG->exchange_name; ?>" refers to the company, including without limitation thereby, its owners, directors, investors, employees or other related parties. Depending upon context, "<?= $CFG->exchange_name; ?>" may also refer to the services, products, Site, content or other materials (collectively, "Materials") provided by <?= $CFG->exchange_name; ?>.</p>
                    <p>The Service operated by <?= $CFG->exchange_name; ?> allows buyers ("Buyers") and sellers ("Sellers"), to buy and sell the Digital currency known as "Bitcoin" or any other digital currency that <?= $CFG->exchange_name; ?>'s platform allows to trade through its platform.</p>
                    <p>The Service operated by <?= $CFG->exchange_name; ?> also allows all registered users of the Service ("Users") to:</p>
                    <p>Depending on Your country of residence, you may not be able to use all the functions of the Site. It is your responsibility to follow those rules and laws in your country of residence and/or country from which you access this Site and Services. As long as you agree to and actually comply with these Terms of Service, <?= $CFG->exchange_name; ?> grants you a personal, non-exclusive, non-transferable and limited rights to enter and use the Site and the Service.</p>
                    <p><b>IF YOU DO NOT ACCEPT THE TERMS OF USE AND CONDITIONS OUTLINED IN THIS AGREEMENT, DO NOT ACCESS THIS SITE AND DO NOT USE THIS SERVICE.</b></p>
                    <p>By opening an account to use the Service ("Account"), you expressly represent and warrant:</p>
                    <p>&nbsp; 1. You have accepted these Terms; and</p>
                    <p>&nbsp; 2. You are at least 18 years of age and have the full capacity to accept these Terms and enter into a transaction involving Bitcoin or any digital currency.</p>
                    <h5 class="m-b-1em">Risks</h5>
                    <p>The trading of goods and products, real or virtual, as well as virtual currencies involves significant risk. Prices can and do fluctuate on any given day. Due to such price fluctuations, you may increase or lose value in your assets at any given moment. Any currency - virtual or not - may be subject to large swings in value and may even become worthless. There is an inherent risk that losses will occur as a result of buying, selling or trading anything on through the trading platform.</p>
                    <p>Bitcoin trading also has special risks not generally shared with official currencies or goods or commodities in a market. Unlike most currencies, which are backed by governments or other legal entities, or commodities such as gold or silver, Bitcoin is a unique kind of currency, backed by technology and trust. There is no central bank that can take corrective measure to protect the value of Bitcoin in a crisis or issue more currency, or reverse transactions.</p>
                    <p>Instead, Bitcoin is an as-yet autonomous and largely unregulated worldwide system of currency traded between firms and individuals. Traders put their trust in a digital, decentralised and partially anonymous system that relies on peer-to-peer networking and cryptography to maintain its integrity.</p>
                    <p>There may be additional risks that we have not foreseen or identified in our Terms of Use.</p>
                    <p>You should carefully assess whether your financial situation and tolerance for risk is suitable for buying, selling or trading Bitcoin.</p>
                    <p>We have partnered with Crypto Capital Corp to manage all of the KYC/AML and Fiat transactions on our behalf. Please refer to their site at cryptocapital.co for more information on the services they provide.</p>
                    <h5 class="m-b-1em">Limited Right of Use</h5>
                    <p><b>Maintaining Your Account</b></p>
                    <p>This Site is for your personal or commercial use only. We are vigilant in maintaining the security of our Site and the Service. By registering with us, you agree to provide <?= $CFG->exchange_name; ?> with current, accurate, and complete information as needed to maintain your account and to keep such information updated. You further agree that you will not use any Account other than your own, or access the Account of any other User at any time, or assist others in obtaining unauthorised access.</p>
                    <p>You are also responsible for maintaining the confidentiality of Your Account information, including your password, safeguarding your own <?= $CFG->exchange_name; ?> account, and for all activity including Transactions that are posted to Your Account. If there is suspicious activity related to your Account please inform us right away so that we may, but are not obligated to take steps to secure your account. You are obligated to comply with these security requests, or accept termination of Your Account. You are required to notify <?= $CFG->exchange_name; ?> immediately of any unauthorised use of Your Account or password, or any other breach of security by email addressed to support@1ex.trade. Any User who violates these rules may be terminated, and thereafter held liable for losses incurred by <?= $CFG->exchange_name; ?> or any user of the Site.</p>
                    <p>Finally, You agree that You will not use the Service to perform criminal activity of any sort, including but not limited to, money laundering, illegal gambling operations, terrorist financing, or malicious hacking.</p>
                    <p><b>Termination</b></p>
                    <p>You may terminate this agreement with <?= $CFG->exchange_name; ?>, and close your Account at any time, following settlement of any pending transactions.</p>
                    <p>You also agree that <?= $CFG->exchange_name; ?> may, by giving notice, in its sole discretion terminate Your access to the Site and to Your Account, including without limitation, our right to: limit, suspend or terminate the service and Users' Accounts, prohibit access to the Site and its content, services and tools, delay or remove hosted content, and take technical and legal steps to keep Users off the Site if we think that they are creating issues or a possible legal liabilities, infringing the intellectual property rights of third parties, or acting inconsistently with the letter of these Terms. Additionally, we may, in appropriate circumstances and at our discretion, suspend or terminate Accounts of Users for any reason, including without limitation: (1) attempts to gain unauthorised access to the Site or another User's account or providing assistance to others' attempting to do so, (2) overcoming software security features limiting use of or protecting any content, (3) usage of the Service to perform illegal activities such as money laundering, illegal gambling operations, financing terrorism, or other criminal activities, (4) violations of these Terms of Use, (5) failure to pay or fraudulent payment for Transactions, (6) unexpected operational difficulties, or (7) upon the request of law enforcement or other government agencies, if deemed to be legitimate and compelling by <?= $CFG->exchange_name; ?>, acting in its sole discretion.</p>
                    <p>The suspension of an Account shall not affect the payment of the commissions due for past Transactions. Upon termination, Users shall communicate a valid bank account to allow for the transfer of any currencies credited to their account. Said bank account shall be held by the User. The funds may be transferred to a valid Crypto Capital Corp. bank account only after conversion into a currency. <?= $CFG->exchange_name; ?> shall transfer the currencies as soon as possible following the User's request in the time frames specified by <?= $CFG->exchange_name; ?>.</p>
                    <p>All services are provided without warranty of any kind, either express or implied. We do not represent that this Site will be available 100% of the time to meet your needs. We will strive to provide You with the Service as soon as possible but there are no guarantees that access will not be interrupted, or that there will be no delays, failures, errors, omissions or loss of transmitted information.</p>
                    <p>We will use reasonable endeavours to ensure that the Site can normally be accessed by You in accordance with these Terms of Use. We may suspend use of the Site for maintenance and will make reasonable efforts to give you notice. You acknowledge that this may not be possible in an emergency.</p>
                    <h5 class="m-b-1em">APIs and Widgets</h5>
                    <p>We may provide access to certain parties to access specific data and information through our API (Application Programming Interface) or through widgets. We also may provide widgets for Your use to put our data on your Site. You are free to use these.</p>
                    <h5 class="m-b-1em">External Web Sites</h5>
                    <p><?= $CFG->exchange_name; ?> makes no representations whatsoever about any other Site which you may access through this Site. The Site may provide links or other forms of reference to other websites ("External Web Sites") or Resources over which we do not have control ("External Web Sites"). In such case you acknowledge that <?= $CFG->exchange_name; ?> is providing these links or references to you only as a convenience. <?= $CFG->exchange_name; ?> is not responsible for the availability of, and content provided on, third party Sites. You are requested to review the policies posted by other Sites regarding privacy and other topics before use. <?= $CFG->exchange_name; ?> is not responsible for third party content accessible through the Site, including opinions, advice, statements, prices, activities and advertisements, and you shall bear all risks associated with the use of such content. It is up to you to take precautions to ensure that whatever you select for your use is free of such items as viruses, worms, Trojan horses and other items of a destructive nature.</p>
                    <h5 class="m-b-1em">Financial Advice</h5>
                    <p>We do not provide any investment advice in connection with the Services contemplated by these Terms of Use. We may provide information on the price, range, volatility of Bitcoin and events that have affected the price of Bitcoin but this is not considered investment advice and should not be construed as such. Any decision to purchase or sell Bitcoin is Your decision and We will not be liable for any loss suffered.</p>
                    <h5 class="m-b-1em">Financial Regulation</h5>
                    <p>Our business model, and our Service, consists of facilitating the buying, selling and trading of Bitcoin and their use to purchase goods in an unregulated, international open payment system.</p>
                    <h5 class="m-b-1em">Email</h5>
                    <p>Email messages sent over the Internet are not secure and <?= $CFG->exchange_name; ?> is not responsible for any damages incurred by the result of sending email messages over the Internet. We suggest sending email in encrypted formats; you are welcome to send PGP encrypted emails to us. The instructions and keys to do so are available upon request.</p>
                    <h5 class="m-b-1em">Jurisdiction</h5>
                    <p>The Terms of Use shall be governed and construed in accordance with the Republic of Panama.</p>
                    <h5 class="m-b-1em">Limitation of Liability</h5>
                    <p>To the extent permitted by law, <?= $CFG->exchange_name; ?> will not be held liable for any damages, loss of profit, loss of revenue, loss of business, loss of opportunity, loss of data, indirect or consequential loss unless the loss suffered arising from negligence or wilful deceit or fraud. Nothing in these terms excludes or limits the liability of either party for fraud, death or personal injury caused by its negligence, breach of terms implied by operation of law, or any other liability which may not by law be limited or excluded.</p>
                    <p>To the full extent permitted by applicable law, You hereby agree to indemnify <?= $CFG->exchange_name; ?>, and its partners against any action, liability, cost, claim, loss, damage, proceeding or expense suffered or incurred if direct or not directly arising from your use of <?= $CFG->exchange_name; ?> Sites, Your use of the Service, or from your violation of these Terms of Use.</p>
                    <h5 class="m-b-1em">Miscellaneous</h5>
                    <p>If We are unable to perform the Services outlined in the Terms of Use due to factors beyond our control including but not limited to an event of Force Majeure, change of law or change in sanctions policy we will not have any liability to You with respect to the Services provided under this agreement and for a time period coincident with the event.</p>
                    <h5 class="m-b-1em">Modification of Terms</h5>
                    <p><?= $CFG->exchange_name; ?> reserves the right to change, add or remove portions of these Terms, at any time, in an exercise of its sole discretion. You will be notified of any changes in advance through your Account. Upon such notification, it is your responsibility to review the amended Terms. Your continued use of the Site following the posting of a notice of changes to the Terms signifies that you accept and agree to the changes, and that all subsequent transactions by you will be subject to the amended Terms.</p>
                    <h5 class="m-b-1em">Definitions</h5>
                    <p>Bitcoin: means the Peer-to-Peer digital currency further described at http://bitcoin.org.</p>
                    <p>Commission: refers to the fee which is payable to <?= $CFG->exchange_name; ?> on each Transaction, such as a <?= $CFG->exchange_name; ?> Purchase Transaction.</p>
                    <p>Buyer(s): means User(s) that are submitting an offer to buy Bitcoin or other cryptocurrencies through the Service.</p>
                    <p>Seller(s): means User(s) that are submitting an offer to sell Bitcoin or other cryptocurrencies through the Service.</p>
                    <p>User(s): means Buyers and Sellers as well as any holder of an Account.</p>
                    <p>Service(s): means the technological platform, functional rules and market managed by <?= $CFG->exchange_name; ?> to permit Sellers and Buyers to perform purchase and sale transactions of Bitcoin.</p>
                    <p>Price: means "price per coin" for which Users are willing to purchase or sell Bitcoin or other cryptocurrencies, using the Service in a Bitcoin Purchase Transaction. The Price may be expressed in any of the currencies deposited by Users in their account and supported by the Service. See our Site for a full list of currencies.</p>
                    <p>Transaction: means (i) the agreement between the Buyer and the Seller to exchange Bitcoin through the Service for currencies at a commonly agreed rate ("Bitcoin Purchase Transaction"), (ii) the conversion of currencies into Bitcoin deposited by Users on their account ("Conversion Transaction"), (iii) the transfer of Bitcoin among Users ("Bitcoin Transfer Transaction"), (iv) the transfer of currencies among Users ("Currency Transfer Transaction") and (v) the purchase of ancillary products ("Purchase Transactions"). <?= $CFG->exchange_name; ?> may not offer all of these types of transactions at this time or in all places.</p>
                    <p>Transaction Price: means the total price paid by the Buyer in respect of each Transaction performed through the Service.</p>
                    <h5 class="m-b-1em">Information we collect</h5>
                    <p>If you create an account or use <?= $CFG->exchange_name; ?> services and you wish to transact withdraw any fiat funds you will need to open an account with Crypto Capital. Crypto Capital Corp may collect the following types of information:</p>
                    <ul>
                        <li>Contact information - your name, address, phone, email and other required information.</li>
                        <li>Financial information - the full bank account numbers and/or credit card numbers that you link to your <?= $CFG->exchange_name; ?> account or give us when you use paid <?= $CFG->exchange_name; ?> services.</li>
                        <li>When you use <?= $CFG->exchange_name; ?> services, we collect information about your transactions and your other activities on our website and we may collect information about your computer or other access device for fraud prevention purposes.</li>
                        <li>Finally, we may collect additional information from or about you in other ways such as contacts with our customer support team. </li>
                    </ul>
                    <h5 class="m-b-1em">How we use cookies </h5>
                    <p>When you access our website or content or use our application or <?= $CFG->exchange_name; ?> services, we or companies we work with may place small data files called cookies or pixel tags on your computer or other device. We use these technologies to:</p>
                    <ul>
                        <li>Recognise you as a <?= $CFG->exchange_name; ?> customer;</li>
                        <li>Customise <?= $CFG->exchange_name; ?> services and content;</li>
                        <li>Measure promotional effectiveness; and</li>
                        <li>Collect information about your computer or other access device to mitigate risk, help prevent fraud and promote trust and safety.</li>
                        <li>We use both session and persistent cookies when you access our website or content. Session cookies expire and no longer have any effect when you log out of your account. Persistent cookies remain on your browser until you erase them or they expire.</li>
                        <li>We also use Local Shared Objects, commonly referred to as “Flash cookies,” to help ensure that your account security is not compromised, to spot irregularities in behaviour to help prevent fraud and to support our sites and services.</li>
                        <li>We encode our cookies so that only we can interpret the information stored in them. You are free to decline our cookies if your browser or browser add-on permits, but doing so may interfere with your use of our website. The help section of most browsers or browser add-ons provides instructions on blocking, deleting or disabling cookies.</li>
                        <li>You may encounter <?= $CFG->exchange_name; ?> cookies or pixel tags on websites that we do not control. For example, if you view a web page created by a third party or use an application developed by a third party, there may be a cookie or pixel tag placed by the web page or application. Likewise, these third parties may place cookies or pixel tags that are not subject to our control and the <?= $CFG->exchange_name; ?> Privacy Policy does not cover their use.</li>
                    </ul>
                    <h5 class="m-b-1em">How we use the personal information we collect</h5>
                    <p>Our primary purpose in collecting personal information is to provide you with a secure, smooth, efficient, and customised experience. We may use your personal information to:</p>
                    <ul>
                        <li>Provide <?= $CFG->exchange_name; ?> services and customer support you request;</li>
                        <li>Process transactions and send notices about your transactions;</li>
                        <li>Resolve disputes, collect fees, and troubleshoot problems;</li>
                        <li>Prevent potentially prohibited or illegal activities;</li>
                        <li>Customise, measure, and improve <?= $CFG->exchange_name; ?> services and the content and layout of our website and applications;</li>
                        <li>Deliver targeted marketing, service update notices, and promotional offers based on your communication preferences; and</li>
                        <li>Compare information for accuracy and verify it with third parties.</li>
                    </ul>
                    <h5 class="m-b-1em">Marketing</h5>
                    <p>We will not sell or provide your information to third parties for their marketing purposes without your explicit consent. We may combine your information with information we collect from other companies and use it to improve and personalise <?= $CFG->exchange_name; ?> services, content and advertising.</p>
                    <h5 class="m-b-1em">How we share information with other parties</h5>
                    <p>We may share your information with:</p>
                    <ul>
                        <li>Service providers under contract who help with parts of our business operations such as fraud prevention, bill collection, marketing and technology services. Our contracts dictate that these service providers only use your information in connection with the services they perform for us and not for their own benefit.</li>
                        <li>Financial institutions with which we partner with.</li>
                        <li>Companies that we plan to merge with or be acquired by. (Should such a combination occur, we will require that the new combined entity follow this privacy policy with respect to your personal information. You will receive prior notice of any change in applicable policy.)</li>
                        <li>Other third parties with your consent or direction to do so.</li>
                        <li><?= $CFG->exchange_name; ?> will not sell or rent any of your personal information to third parties for their marketing purposes and only shares your personal information with third parties as described in this policy.</li>
                        <li>If you establish a <?= $CFG->exchange_name; ?> account indirectly on a third party website or via a third party application, any information that you enter on that website or application (and not directly on a <?= $CFG->exchange_name; ?> website) will be shared with the owner of the third party website or application and your information may be subject to their privacy policies.</li>
                        <li>We will notify you of material changes to this policy by updating the last updated date at the top of this page. It is recommended to visit this page frequently to check for changes.</li>
                    </ul>
                    <h5 class="m-b-1em">How you can access or change your information</h5>
                    <p>
                        You can review and edit your personal information at any time by logging in to your account and clicking Settings or My Account tab. If you deactivate your <?= $CFG->exchange_name; ?> account, we will mark your account in our database as "Deactivate" but will keep your account information in our database. This is necessary in order to deter fraud, by ensuring that persons who try to commit fraud will not be able to avoid detection simply by closing their account and opening a new account. However, if you close your account, your identifiable information will not be used by us for any further purposes, nor sold or shared with third parties, except as necessary to prevent fraud as required by law or in accordance with this Privacy Policy.
                    </p>
                    <h5 class="m-b-1em">How you can contact us about privacy questions</h5>
                    <p>If you have questions or concerns regarding this policy, you should contact us on our support page or by writing to us at <a href="mailto:contact@bitexchange.systems">contact@bitexchange.systems</a></p>
                   </div>
                </div>
            </div>
       </div>
        <?php include "includes/sonance_footer.php"; ?>
        <script type="text/javascript" src="js/ops.js?v=20160210"></script>
</html>