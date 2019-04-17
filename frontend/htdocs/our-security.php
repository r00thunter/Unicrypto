<!doctype html>
<html>

<head>
<title>Profile</title>

<meta property="viewport" name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/new-style.css" rel="stylesheet" />
<link href="css/dashboard.css" rel="stylesheet" />
<link href="css/profile.css" rel="stylesheet" />
<!-- <link rel="stylesheet" href="css/style.css?v=20160204" type="text/css" /> -->
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$("div").click(function() {
window.location = $(this).find("a").attr("href");
return false;
});
</script>

<meta name="viewport" content="width=device-width, initial-scale=1.0" data-react-helmet="true">
<?php
include '../lib/common.php';

API::add('Content','getRecord',array('our-security'));
$query = API::send();

$content = $query['Content']['getRecord']['results'][0];
$page_title = $content['title'];


?>
<!-- <div class="page_title">
	<div class="container">
		<div class="title"><h1><?= $page_title ?></h1></div>
        <div class="pagenation">&nbsp;<a href="<?= Lang::url('index.php') ?>"><?= Lang::string('home') ?></a> <i>/</i> <a href="<?= Lang::url('our-security.php') ?>"><?= Lang::string('our-security') ?></a></div>
	</div>
</div>
<div class="container">
	<div class="content_right">
    <div class="text1"><?= $content['content'] ?></div>
    </div>
	<div class="clearfix mar_top8"></div>
</div> -->
<div id="root">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Flex__Flex-fVJVYW iJJJTg">
<div class="Toasts__Container-kTLjCb jeFCaz"></div>
<div class="Layout__Container-jkalbK gCVQUv Flex__Flex-fVJVYW bHipRv">
<div class="LayoutDesktop__AppWrapper-cPGAqn WhXLX Flex__Flex-fVJVYW bHipRv">
    
    <? include 'includes/topheader.php'; ?>

    <div class="LayoutDesktop__ContentContainer-cdKOaO cpwUZB Flex__Flex-fVJVYW bHipRv">
        
    <? include 'includes/menubar.php'; ?>

    <div class="LayoutDesktop__Wrapper-ksSvka fWIqmZ Flex__Flex-fVJVYW cpsCBW">
        <div class="LayoutDesktop__Content-flhQBc bRMwEm Flex__Flex-fVJVYW gkSoIH">
            <div class="Dashboard__FadeFlex-bFoDXs cYFmKg Flex__Flex-fVJVYW iDqRrV">
                <div class="Flex__Flex-fVJVYW bHipRv">
                    <div></div>
                    <div class="Dashboard__Panels-getBDx fJxaut Flex__Flex-fVJVYW iDqRrV">
                        <div class="Flex__Flex-fVJVYW bHipRv">
                            <div class="Flex__Flex-fVJVYW gsOGkq">

                               <div class="Dashboard__ChartContainer-bKDMTA kjRPPr Flex__Flex-fVJVYW iDqRrV" style="height: auto;">
								   
                                    <div class="Flex__Flex-fVJVYW gsOGkq" style="width: 100%;border-right: none;">
                                        <div id="page" class="jdmxYg" style="width: 100%;">
											<div class="row">
											<div class="WidgetHeader__Wrapper-lkOFAm gkEpki Flex__Flex-fVJVYW bggzhW">
    <div class="Flex__Flex-fVJVYW iDqRrV">
        <div class="Flex__Flex-fVJVYW iDqRrV">
            <div class="PriceChart__PriceHeading-iIpDul gaOoIW Flex__Flex-fVJVYW jGNjWx">

                <div class="Flex__Flex-fVJVYW reCYb">
                    <h4 class="PriceChart__HeadingTitle-bZuIYw eopEKS Heading__StyledHeading-sALAQ hwfHDH" style="font-size: 18px;">Our Security</h4>

                </div>
            </div>

        </div>
    </div>

</div>
										<?= $content['content'] ?>
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
        <?php include "includes/footer.php"; ?>
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