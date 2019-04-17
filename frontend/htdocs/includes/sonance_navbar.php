<style type="text/css">
    .dropdown-menu
{
left : initial;
right:0;
}
</style>
<?php
// echo basename($_SERVER['REQUEST_URI']);
$menu_sql=mysqli_query($conn_l, "select menu_key, menu_".$_SESSION[LANG]." from trans_menu where status=1");
while($menurow=mysqli_fetch_array($menu_sql))
{
    $menus[$menurow['menu_key']]=$menurow['menu_'.$_SESSION[LANG]];
}
?>
<nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="index">
                <img src="images/star.png" alt="img" class="logo-star">
                <img src="images/logo1.png" alt="img" class="main-logo" style="filter: invert(100%);" />
                <!-- <img class="logo" src="sonance/img/logo.png" alt=""> -->
            </a>
            <?php if (User::isLoggedIn()): ?>

                <?php 
                API::add('TrezorWallet','getInfo',1);//API::add('TrazorWallets','get');
                $query = API::send();
                $trezor = $query['TrezorWallet']['getInfo']['results'][0];
                $trezor_status = $trezor['status']; 
                ?>
            <div class="collapse navbar-collapse justify-content-md-center" id="navbarToggler">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="balances"><?php echo isset($menus['menu_dashboard']) ? $menus['menu_dashboard'] : 'Dashboard'; ?></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo isset($menus['menu_fund']) ? $menus['menu_fund'] : 'Funds'; ?></a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="deposit">
                                <?php echo isset($menus['menu_deposit']) ? $menus['menu_deposit'] : 'Deposits'; ?>
                            </a>
                            <a class="dropdown-item" href="withdraw">
                                <?php echo isset($menus['menu_withdrawals']) ? $menus['menu_withdrawals'] : 'Withdrawals'; ?>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo isset($menus['menu_order-table']) ? $menus['menu_order-table'] : 'Orders'; ?></a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="openorders?c_currency=28&currency=27">
                                <?php echo isset($menus['menu_oper_order']) ? $menus['menu_oper_order'] : 'Your Open Orders'; ?>
                            </a>
                           
                            <a class="dropdown-item" href="tradehistory?c_currency=28&currency=27">
                                <?php echo isset($menus['menu_trade']) ? $menus['menu_trade'] : 'Trade History'; ?>
                            </a>
                             <a class="dropdown-item" href="orderhistory?c_currency=28&currency=27">
                                <?php echo isset($menus['menu_order-table']) ? $menus['menu_order-table'] : 'Order Table'; ?>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo isset($menus['menu_trade']) ? $menus['menu_trade'] : 'Trade'; ?></a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="userbuy?trade=BTC-USD&c_currency=28&currency=27">
                                <?php echo isset($menus['menu_simple-trade']) ? $menus['menu_simple-trade'] : 'Simple Trade'; ?>
                            </a>
                            <a class="dropdown-item" href="advanced-trade?trade=BTC-USD&c_currency=28&currency=27">
                                <?php echo isset($menus['menu_advanced-trade']) ? $menus['menu_advanced-trade'] : 'Advanced Trade'; ?>
                            </a>
                        </div>
                    </li>
                    
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="userbuy?trade=BTC-USD">Simple Trade</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="cryptoaddress"><?php echo isset($menus['menu_crypto-address']) ? $menus['menu_crypto-address'] : 'Crypto Address'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cryptowallet"><?php echo isset($menus['menu_crypto-wallet']) ? $menus['menu_crypto-wallet'] : 'Crypto Wallet'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="deposit"><?php echo isset($menus['menu_fiat-wallet']) ? $menus['menu_fiat-wallet'] : 'Fiat Wallet'; ?></a>
                    </li>
                                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i></a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="myprofile">
                                <strong><?php echo isset($menus['menu_account']) ? $menus['menu_account'] : 'Account'; ?></strong><br>
                                <span><?= User::$info['first_name']?></span>
                            </a>
                            <a class="dropdown-item" href="mysecurity">
                                <?php echo isset($menus['menu_security']) ? $menus['menu_security'] : 'Security'; ?>
                            </a>
                            <a class="dropdown-item" href="logout.php?log_out=1&uniq=<?= $_SESSION["logout_uniq"] ?>">
                                <?php echo isset($menus['menu_logout']) ? $menus['menu_logout'] : 'Logout'; ?>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-language"></i></a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            $result=mysqli_query($conn_l, "select * from trans_lang where language_status=1");
                            while($row=mysqli_fetch_array($result))
                            {
                            ?>
                            <a class="dropdown-item" href="#" onclick="javascript:set_language('<?php echo $row['language_symbol']; ?>');"><?php echo $row['language_name']; ?></a>
                            <?php 
                            } 
                            ?>
                        </div>
                    </li>
                </ul>
            </div>
            <? else: ?>
            <div class="collapse navbar-collapse justify-content-md-center" id="navbarToggler">
                <ul class="navbar-nav ml-auto">  
                    <li class="nav-item">
                        <a class="nav-link" href="#"><?php echo isset($menus['menu_support']) ? $menus['menu_support'] : 'Support'; ?></a>
                    </li>              
                    <li class="nav-item">
                        <a class="nav-link" href="login"><?php echo isset($menus['menu_login']) ? $menus['menu_login'] : 'Login'; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register"><?php echo isset($menus['menu_register']) ? $menus['menu_register'] : 'Register'; ?></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-language"></i></a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            $result=mysqli_query($conn_l, "select * from trans_lang where language_status=1");
                            while($row=mysqli_fetch_array($result))
                            {
                            ?>
                            <a class="dropdown-item" href="#" onclick="javascript:set_language('<?php echo $row['language_symbol']; ?>');"><?php echo $row['language_name']; ?></a>
                            <?php 
                            } 
                            ?>
                        </div>
                    </li>
                </ul>
            </div>
            <? endif; ?>
        </div>
    </nav>

<script>
window.onload = function() {
    
    //if the current page is simple trade page 
    if(window.location.pathname.search("/userbuy") != -1) {
        
        document.querySelector('title').innerHTML = '<?=$CFG->exchange_name?> | Simple Trade'
    } 
    // if the current page any other page hading heading container
    else  {
        document.querySelector('title').innerHTML = '<?=$CFG->exchange_name?> | '+document.querySelector('.banner > .container > h1').innerText
    }
    
}
</script>