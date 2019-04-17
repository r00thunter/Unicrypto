<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Bitexchange Language Conversion</title>

  <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="public/image/favicon.png">
    <link rel="apple-touch-icon" sizes="60x60" href="public/image/favicon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="public/image/favicon.png">
    <link rel="apple-touch-icon" sizes="76x76" href="public/image/favicon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="public/image/favicon.png">
    <link rel="apple-touch-icon" sizes="120x120" href="public/image/favicon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="public/image/favicon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="public/image/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="public/image/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="public/image/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="public/image/favicon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="public/image/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="public/image/favicon.png">
    <link rel="manifest" href="sonance/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="sonance/img/favicon/ms-icon-144x144.png">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

    <!-- Styles -->
    <link href="{{URL::asset('public/css/app.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/common.php') }}" rel="stylesheet"> -->
    @include('layouts.common')
    <!-- <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet"> -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <!-- font-awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Data Table  -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
   
<!-- Scripts -->
    <script src="{{URL::asset('public/js/app.js') }}" type="text/javascript"></script>
    <!-- <script src="{{ asset('js/bootstrap.min.js') }}" defer></script> -->
    <!-- <script src="{{ asset('js/jquery.min.js') }}" defer></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
    <!-- <script src="{{ asset('js/popper.min.js') }}" defer></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" type="text/javascript"></script>

    <!-- Data Table  -->
    <script src="https://code.jquery.com/jquery-3.3.1.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
    <script src="{{URL::asset('public/js/common.js') }}" type="text/javascript"></script>

    <!-- Fonts -->
<!--     <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css"> -->

</head>
<body>
    <div id="app">
        @include('layouts.navbar')
        
        <main class="">
        @include('layouts.sidenavbar')
            @yield('content')
        </main>
    </div>
</body>
<script>
    
$('.nav-link').click(function(){
    $('.nav-link').removeClass('nav-active');
    $(this).addClass('nav-active');
});

$('a#navbarDropdown').click(function(){
    $('div#navbarSupportedContent .dropdown-menu.dropdown-menu-right').addClass('show');
});
$('.navbar').click(function(){
    $('div#navbarSupportedContent .dropdown-menu.dropdown-menu-right').removeClass('show');
});
$('main').click(function(){
    $('div#navbarSupportedContent .dropdown-menu.dropdown-menu-right').removeClass('show');
});


$(document).ready(function() {
    $('#example').DataTable();
} );
</script>
</html>
