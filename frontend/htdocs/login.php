<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
        <title></title>
        <meta name="author" content="">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <link rel="canonical" href="">
        <meta name="theme-color" content="#310f72">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="57x57" href="sonance/img/favicon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="sonance/img/favicon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="sonance/img/favicon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="sonance/img/favicon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="sonance/img/favicon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="sonance/img/favicon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="sonance/img/favicon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="sonance/img/favicon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="sonance/img/favicon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192" href="sonance/img/favicon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="sonance/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="sonance/img/favicon/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="sonance/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="sonance/img/favicon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="sonance/img/favicon/ms-icon-144x144.png">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <!-- Default Style CSS -->
        <link rel="stylesheet" type="text/css" href="sonance/css/default.css">
        <link rel="stylesheet" type="text/css" href="sonance/css/responsive.css">
        <!-- Global site tag (gtag.js) - AdWords: 1045328140 --> 
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-1045328140"></script>
        <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-1045328140'); </script>
    </head>
    <?php 
        include '../lib/common.php';
        if (User::isLoggedIn())
            Link::redirect('/');
        $page_title = Lang::string('home-login');
        // $user1 = (!empty($_REQUEST['login']['user'])) ? preg_replace("/[^0-9]/", "",$_REQUEST['login']['user']) : false;
        if (!empty($_REQUEST['email'])&& !empty($_REQUEST['password'])){
                $user1 = $_REQUEST['email'];
                $pass1 = $_REQUEST['password'];
            }

        if (!empty($_REQUEST['submitted'])) {

            $user1 = $_REQUEST['login']['user'];
            $pass1 = (!empty($_REQUEST['login']['pass'])) ? preg_replace($CFG->pass_regex, "",$_REQUEST['login']['pass']) : false;

            if (empty($user1)) {
                Errors::add(Lang::string('login-user-empty-error'));
            }
        
            if (empty($pass1)) {
                Errors::add(Lang::string('login-password-empty-error'));
            }
        }
            if(!empty($user1) && !empty($pass1)){
            // if (!empty($_REQUEST['submitted']) && (empty($_SESSION["register_uniq"]) || $_SESSION["register_uniq"] != $_REQUEST['uniq']))
            //  Errors::add('Page expired.');
            
            if (!empty(User::$attempts) && User::$attempts > 3 && !empty($CFG->google_recaptch_api_key) && !empty($CFG->google_recaptch_api_secret)) {
                $captcha = new Form('captcha');
                $captcha->reCaptchaCheck(1);
                if (!empty($captcha->errors) && is_array($captcha->errors)) {
                    Errors::add($captcha->errors['recaptcha']);
                }
            }
            if (!is_array(Errors::$errors)) {

       //          API::add('User','verifyapprovedlogin',array($user1));
    			// $query = API::send();

    			// if($query['User']['verifyapprovedlogin']['results']['0']!='APPROVED'){
    			// 	Link::redirect('https://www.bitexchange.cash/kyc/public/login');
    			// }

                $login = User::logIn($user1,$pass1);
                // echo "<pre>"; print_r($login); exit;
                if ($login && empty($login['error'])) {
                    if (!empty($login['message']) && $login['message'] == 'awaiting-token') {
                        $_SESSION["register_uniq"] = md5(uniqid(mt_rand(),true));
                        Link::redirect('verify-token');
                    }
                    elseif (!empty($login['message']) && $login['message'] == 'logged-in' && $login['no_logins'] == 'Y') {
                        $_SESSION["register_uniq"] = md5(uniqid(mt_rand(),true));
                        Link::redirect('change-password');
                    }
                    elseif (!empty($login['message']) && $login['message'] == 'logged-in') {
                        $_SESSION["register_uniq"] = md5(uniqid(mt_rand(),true));
                        Link::redirect('balances');
                    }
                }
                elseif (!$login || !empty($login['error'])) {
                    Errors::add(Lang::string('login-invalid-login-error'));
                }
            }
        }
        
        if (!empty($_REQUEST['message']) && $_REQUEST['message'] == 'registered')
            Messages::add(Lang::string('register-success'));
        
        $_SESSION["register_uniq"] = md5(uniqid(mt_rand(),true));
        ?>
        <style>
            .messages, .errors{
            position: relative;
            margin: 0 0 1em 3em;
        }
        .show_eye{
            padding: 0px 10px 0px 0px;
        }
        </style>
    <?php include "includes/sonance_header.php"; ?>
    <?php include "includes/sonance_navbar.php"; ?>


           <?php    
    $page_sql=mysqli_query($conn_l, "select id from trans_page where page_status=1 and page_name='login'");
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

    // $pg_cont_sql=mysqli_query($conn_l, "select page_content_key, ".$_SESSION[LANG]."_page_content from trans_page_value where page_content_status=1 and page_id='".$page_id."'");
    // while($pagecontrow=mysqli_fetch_array($pg_cont_sql))
    // {
    //     $pgcont[$pagecontrow['page_content_key']]=$pagecontrow[$_SESSION[LANG].'_page_content'];
    // }       
    ?>
    <body class="register-page">
        <div class="register-container">
            <div class="container">
                <div class="register-card">
                    <!-- <img src="sonance/img/logo.png" class="logo"> -->
                    <!-- <h3 class="text-center m_b_20"><?= $CFG->exchange_name; ?></h3> -->
                    <div class="text-center logo-otr" onclick="window.location.href='index'" style="cursor:pointer">
                        <img src="images/star.png" alt="img" class="logo-star">
                        <img src="images/logo1.png" alt="img" class="main-logo" />
                    </div>
                    <h6 class="text-center"><strong><?php echo isset($pgcont['login_heading_key']) ? $pgcont['login_heading_key'] : 'Login'; ?></strong></h6>
                    <? 
                        if (count(Errors::$errors) > 0) {
                            echo '<span style="display: inline-block;margin: 0 0 1em;font-size: 14px;width: 100%;
                        color: red;background: #f7e0e0;padding: 10px;border-radius: 3px;">'.Errors::$errors[0].'</span>';
                        }
                        
                        if (count(Messages::$messages) > 0) {
                            echo '
                        <div class="messages" id="div4">
                            <div class="message-box-wrap">
                                '.Messages::$messages[0].'
                            </div>
                        </div>';
                        }
                        ?>
                    <form method="POST" action="login" name="login">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                                <input class="form-control" type="email" name="login[user]" value="demo@bitexchange.cash" placeholder="<?php echo isset($pgcont['login_email_placeholder_key']) ? $pgcont['login_email_placeholder_key'] : 'Email'; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fas fa-key"></i>
                                </span>
                                <input class="form-control" type="password" name="login[pass]" id="password" value="12345678" placeholder="<?php echo isset($pgcont['login_pass_placeholder_key']) ? $pgcont['login_pass_placeholder_key'] : 'Password'; ?>">
                                <span class="input-group-addon show_eye"><i class="fas fa-eye-slash" onclick="showpassword(this)"></i>
                                </span>
                            </div>
                        </div>
                        <? if (!empty(User::$attempts) && User::$attempts > 2 && !empty($CFG->google_recaptch_api_key) && !empty($CFG->google_recaptch_api_secret)) { ?>
                        <div style="margin-bottom:10px;">
                            <div class="g-recaptcha" data-sitekey="<?= $CFG->google_recaptch_api_key ?>"></div>
                        </div>
                        <? } ?>
                        <input type="hidden" name="submitted" value="1" />
                        <input type="hidden" name="uniq" value="<?= $_SESSION["register_uniq"] ?>" />
                              <button type="submit" class="btn btn-primary"><?php echo isset($pgcont['login_button_key']) ? $pgcont['login_button_key'] : Lang::string('home-login'); ?></button>
                        <!-- <a href="profile.html" class="btn btn-primary">Login</a> -->
                            <p class="note"><a href="forgot"><?php echo isset($pgcont['login_password_key']) ? $pgcont['login_password_key'] : 'Forgot Password?'; ?></a> <span class="pull-right"><?php echo isset($pgcont['login_account_key']) ? $pgcont['login_account_key'] : 'Don\'t have an account?'; ?> <a href="register"><?php echo isset($pgcont['login_register_content_key']) ? $pgcont['login_register_content_key'] : 'Register'; ?></a></span></p>
                    </form>
                </div>
                <div class="copyrights">
                    <p>&copy; 2019 Unicrypto : All Rights Reserved</p>
                </div>
            </div>
        </div>
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
        </script>
        </script>
        <!-- Custom Scripts -->
        <script type="text/javascript" src="sonance/js/script.js"></script>
        <script type="text/javascript" src="js/script11.js"></script>
        <script>
            console.log('hksd');

            function showpassword(x1) {
              
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
                x1.classList.toggle("fa-eye");
            } else {
                x.type = "password";
                x1.classList.toggle('fa-eye-slash');
            }
        }
        </script>
    </body>
</html>