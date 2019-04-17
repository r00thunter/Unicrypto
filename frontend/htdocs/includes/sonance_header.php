<? $_SESSION["logout_uniq"] = md5(uniqid(mt_rand(),true));?>
<head>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-118158391-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-118158391-1');
</script>

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <!-- Color Switch -->
    <link rel="stylesheet" href="sonance/css/jquery.colorpanel.css">
    <!-- Default Style CSS -->
    <link rel="stylesheet" type="text/css" href="sonance/css/default.css">
    <link rel="stylesheet" type="text/css" href="sonance/css/responsive.css">
    <!-- Color Skin -->
    <link rel="stylesheet" type="text/css" id="cpswitch" href="sonance/css/skins/default.css">
     
    <!-- Global site tag (gtag.js) - AdWords: 1045328140 --> <script async src="https://www.googletagmanager.com/gtag/js?id=AW-1045328140"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-1045328140'); </script>

    <input type="hidden" id="javascript_date_format" value="<?= Lang::string('javascript-date-format') ?>" />
    <input type="hidden" id="javascript_mon_0" value="<?= Lang::string('jan') ?>" />
    <input type="hidden" id="javascript_mon_1" value="<?= Lang::string('feb') ?>" />
    <input type="hidden" id="javascript_mon_2" value="<?= Lang::string('mar') ?>" />
    <input type="hidden" id="javascript_mon_3" value="<?= Lang::string('apr') ?>" />
    <input type="hidden" id="javascript_mon_4" value="<?= Lang::string('may') ?>" />
    <input type="hidden" id="javascript_mon_5" value="<?= Lang::string('jun') ?>" />
    <input type="hidden" id="javascript_mon_6" value="<?= Lang::string('jul') ?>" />
    <input type="hidden" id="javascript_mon_7" value="<?= Lang::string('aug') ?>" />
    <input type="hidden" id="javascript_mon_8" value="<?= Lang::string('sep') ?>" />
    <input type="hidden" id="javascript_mon_9" value="<?= Lang::string('oct') ?>" />
    <input type="hidden" id="javascript_mon_10" value="<?= Lang::string('nov') ?>" />
    <input type="hidden" id="javascript_mon_11" value="<?= Lang::string('dec') ?>" />
    <input type="hidden" id="gmt_offset" value="<?= $CFG->timezone_offset ?>" />
    <input type="hidden" id="is_logged_in" value="<?= User::isLoggedIn() ?>" />
    <input type="hidden" id="cfg_orders_edit" value="<?= Lang::string('orders-edit') ?>" />
    <input type="hidden" id="cfg_orders_delete" value="<?= Lang::string('orders-delete') ?>" />
    <input type="hidden" id="cfg_user_id" value="<?= (User::isLoggedIn()) ? User::$info['user'] : '0' ?>" />
    <input type="hidden" id="buy_errors_no_compatible" value="<?= Lang::string('buy-errors-no-compatible') ?>" />
    <input type="hidden" id="orders_converted_from" value="<?= Lang::string('orders-converted-from') ?>" />
    <input type="hidden" id="your_order" value="<?= Lang::string('home-your-order') ?>" />
    <input type="hidden" id="order-cancel-all-conf" value="<?= Lang::string('order-cancel-all-conf') ?>" />
    <input type="hidden" id="this_currency_id" value="<?= (!empty($currency_info)) ? $currency_info['id'] : 0 ?>" />
    <input type="hidden" id="chat_handle" value="<?= (User::isLoggedIn()) ? User::$info['chat_handle'] : 'not-logged-in' ?>" />
    <input type="hidden" id="chat_baseurl" value="<?= ($CFG->chat_baseurl) ? $CFG->chat_baseurl : $CFG->baseurl ?>" />
    <input type="hidden" id="cfg_thousands_separator" value="<?= (!empty($CFG->thousands_separator)) ? $CFG->thousands_separator : ',' ?>" />
    <input type="hidden" id="cfg_decimal_separator" value="<?= (!empty($CFG->decimal_separator)) ? $CFG->decimal_separator : '.' ?>" />
    <input type="hidden" id="cfg_time_24h" value="<?= (!empty($CFG->time_24h)) ? $CFG->time_24h : 'N' ?>" />
    <?= Lang::url(false,false,1); ?>
    <?= Lang::jsCurrencies(false,false,1); ?>
</head>
<?php include "includes/language.php"; ?>