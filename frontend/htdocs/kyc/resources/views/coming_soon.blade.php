<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>BitExchange - KYC</title>
    <meta name="title" content="BitExchange - KYC">
    <meta name="description" content="BitExchange - KYC">
    <meta name="keywords" content="BitExchange - KYC">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-96x96.png') }}" />

    <!-- FONT -->
    <link href='//fonts.googleapis.com/css?family=Poppins:200,300,600,700' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700i" rel="stylesheet">

    <!-- ICON FONTS -->
    <link rel="stylesheet" href="{{ asset('web/css/font-awesome.css') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('web/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('web/css/style.css') }}">
    <style>
.brand img.logo-star {
    position: relative;
    top: -35px;
}
.brand .main-logo {
    margin-left: 20px;
}
        
    </style>
</head>

<body>
<div class="loader_bg"><div class="loader"></div></div>
    <div id="particles"></div>
    <header>
        <div class="brand">
            <img src="{{asset('images/logo1.png') }}" class="img-responsive main-logo" alt="Bitexchange Exchange">
            <img class="logo-star" src="{{asset('images/star.png') }}" alt="Bitexchage Exchange Logo">
        </div>
        <div class="menu xs-black">
            <span class="black"><a href="{{ route('login') }}">Login</a></span>&nbsp;&nbsp;&nbsp;&nbsp;
            <span class="black"><a href="{{ route('register') }}">Register</a></span>&nbsp;&nbsp;&nbsp;&nbsp;
            <!-- <span class="menu-icon black">Register</span> -->
        </div>
    </header>
    <div class="wrapper text-center left_content">
        <div class="left bg_no">
            <div class="one">
                <div class="color_black">
                    <div class="top">
                        <div class="cogs">
                            <i class="cog cog-lg fa fa-cog"></i>
                            <i class="cog cog-counter cog-md fa fa-cog"></i>
                            <i class="cog cog-sm fa fa-cog"></i>
                        </div>
                        <h4 class="font_semibold">Our Exchange is</h4>
                        <h1 class="title"><span class="serif big_2">Getting Launched Soon</span></h1>
                        <h1 id='text' class="title"></h1>
                    </div>
                    <div class="space_5"></div>
                    <div class="countdown">
                        <ul class="color_black" id="countdown"></ul>
                    </div>
                    <div class="space_10"></div>
                    <div class="subscribe">
                        <form class="subscribe_form" action="subscribe.php" method="POST">
                            <div class="form-group">
                                <div class="controls">
                                    <label for="mail-subscribe" class="serif">Register NOW for special benefits!</label>
                                    <br>
                                    <a  class="submit btn" href="{{ route('login') }}" style="float:none">
                                        Log Me In.
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="space_10"></div>

                    <footer class="one">
                        Copyright © 2018 Prutus Cryptocurrency Exchange
                    </footer>
                </div>
            </div>
            <div class="info hide">
                <div class="space_10"></div>
                <div class="contact">
                    <h2 class="title">Register NOW for special benefits! </h2>
                    <br>
                    <form class="fadeInUpBig anim" method="get" action="{{ route('register') }}">
                        <div class="messages"></div>
                        <div class="controls">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input id="form_subject" required="" type="text" name="email" class="form-control"
                                               placeholder="Your email">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <input type="submit" class="btn btn-send" value="Submit">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="space_10"></div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="bg_image img_10"></div>
        </div>
    </div>

<!-- JS -->
<script type="text/javascript" src="{{ asset('web/js/jquery-3.1.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('web/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('web/js/validator.js') }}"></script>
<script type="text/javascript" src="{{ asset('web/js/contact.js') }}"></script>
<script type="text/javascript" src="{{ asset('web/js/jquery.particleground.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('web/js/countdown.js') }}"></script>
<script type="text/javascript" src="{{ asset('web/js/script.js') }}"></script>
    <script>
        $('#particles').particleground({
            dotColor: '#dddddd',
            lineColor: '#dddddd',
            density: 15000
        });
    </script>
</body>
</html>
