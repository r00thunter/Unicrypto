<!doctype html>
<html>

<head>
<title>Profile - <?= $CFG->exchange_name; ?></title>

<meta property="viewport" name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/dashboard.css" rel="stylesheet" />
<link href="css/api_new.css" rel="stylesheet" />
<!-- <link rel="stylesheet" href="css/style.css?v=20160204" type="text/css" /> bla bla bla -->
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$("div").click(function() {
window.location = $(this).find("a").attr("href");
return false;
});
</script>

<meta name="viewport" content="width=device-width, initial-scale=1.0" data-react-helmet="true">
</head>

<body class="app signed-in static_application index" data-controller-name="static_application" data-action-name="index" data-view-name="Coinbase.Views.StaticApplication.Index" data-account-id="">
<?php include '../lib/common.php';

if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
    Link::redirect('userprofile');
elseif (User::$awaiting_token)
    Link::redirect('verify-token');
elseif (!User::isLoggedIn())
    Link::redirect('login');

//     if(empty(User::$ekyc_data) || User::$ekyc_data[0]->status != 'accepted')
// {
//     Link::redirect('ekyc.php');
// }

API::add('User','getAvailable');
API::add('Transactions','get24hData',array(28,13));
API::add('Transactions','get24hData',array(42,13));
$query = API::send();
$currencies = Settings::sessionCurrency();
$user_available = $query['User']['getAvailable']['results'][0];
$transactions_24hrs_btc_inr = $query['Transactions']['get24hData']['results'][0] ;
$transactions_24hrs_ltc_inr = $query['Transactions']['get24hData']['results'][1] ;
$c_currency_info = $CFG->currencies[$currencies['c_currency']];
// echo "<pre>"; print_r($user_available); exit;

?>
<div id="root">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Toasts__Container-kTLjCb jeFCaz"></div>
<div class="Layout__Container-jkalbK gCVQUv Flex__Flex-fVJVYW bHipRv">
<div class="LayoutDesktop__AppWrapper-cPGAqn WhXLX Flex__Flex-fVJVYW bHipRv">
    
    <? include 'includes/topheader.php'; ?>

    <div class="LayoutDesktop__ContentContainer-cdKOaO cpwUZB Flex__Flex-fVJVYW bHipRv">
        
    <? include 'includes/menubar.php'; ?>
    <div class="banner">
        <div class="container content">
            <h1>API</h1>
        </div>
    </div>
    <div class="LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">
        <div class="LayoutDesktop__Content-flhQBc bRMwEm Flex__Flex-fVJVYW gkSoIH">
            <div class="Dashboard__FadeFlex-bFoDXs cYFmKg Flex__Flex-fVJVYW iDqRrV">
                <div class="Flex__Flex-fVJVYW bHipRv">
                    <div></div>
                    <div class="Dashboard__Panels-getBDx fJxaut Flex__Flex-fVJVYW iDqRrV">
                        <div class="Flex__Flex-fVJVYW bHipRv">
                            <div class="Flex__Flex-fVJVYW gsOGkq">

                               <div class="Dashboard__ChartContainer-bKDMTA kjRPPr Flex__Flex-fVJVYW iDqRrV" style="height: auto;">
                                    <div class="Flex__Flex-fVJVYW gsOGkq" style="border: 1px solid #DAE1E9;width: 100%;border-right: none;">
                                        <div id="page" class="jdmxYg">

                                        <div class="row">
                                            <ul id="account_tabs" class="nav nav-tabs">
                                                <li <? if ($CFG->self == 'userprofile') { ?> class="active" <?php } ?>>
                                                    <a href="userprofile">Profile</a>
                                                </li>
                                                <li <? if ($CFG->self == 'bank-accounts') { ?> class="active" <?php } ?>>
                                                    <a href="bank-accounts">Bank</a>
                                                </li>
                                                <li <? if ($CFG->self == 'usersecurity') { ?> class="active" <?php } ?>>
                                                    <a href="usersecurity">Security</a>
                                                </li>
                                                <li <? if ($CFG->self == 'userapi') { ?> class="active" <?php } ?>>
                                                    <a href="userapi">API</a>
                                                </li>

                                            </ul>
                                            
                                            <form class="form-horizontal" id="edit_user_profile" action="" accept-charset="UTF-8" method="post">
                                                <input name="utf8" type="hidden" value="✓">
                                                <input type="hidden" name="_method" value="patch">
                                                <input type="hidden" name="authenticity_token" value="mGHlXTZiYgv8ls7dnvfFCKf75nXFlPFggqPTQFuperG6xbJ6rpQodM7cs70xE3SZlyQONQpjLMFO3b3RQErtFA==">
                                                <legend>API </legend>
                                                <p>You must enable 2FA on your account to proceed.</p>
                                                <a href="usersecurity" class="btn-theme btn">Setup 2FA</a>
                                                <div class="row profile-image-errors" style="display:none;">
                                                    <div>
                                                        <div class="alert"></div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

        
        <!-- Footer Section Starts Here -->
        <? include 'includes/footer.php'; ?>
        <!-- Footer Section Ends Here -->
        <div class="Backdrop__LayoutBackdrop-eRYGPr cdNVJh"></div>
    </div>
</div>
</div>
</div>
<div></div>
</div>
</div>

<script>
$(document).ready(function(){
$(".Header__DropdownButton-dItiAm").click(function(){
$(".DropdownMenu__Wrapper-ieiZya.kwMMmE").toggleClass("show-menu");
});
});
</script>

</body>

</html>