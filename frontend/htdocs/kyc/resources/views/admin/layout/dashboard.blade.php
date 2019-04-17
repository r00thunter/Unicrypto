<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <meta content="" name="description">
        <meta content="" name="author">
        <!-- Favicon icon -->
        <title>
        KYC Admin Login
        </title>
        <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
        <!-- Bootstrap Core CSS -->
        <link href="{{ asset('css/lib/bootstrap/bootstrap.min.css') }}" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="{{ asset('css/lightbox.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/helper.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:** -->
        <!--[if lt IE 9]>
        <script src="https:**oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https:**oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <style type="text/css">
         .btn-primary{
                    background: #f4ba2f !important;
                    color: #fff;
            }
            .dt-buttons .dt-button {
                 background: #f4ba2f !important;
            }

            img.logo {
                height: auto;
                width: 53%;
            }
            .page-wrapper {
                background-color: #1C1B19;
                color: #fff;
                outline: none;
            }
         /*   .left-sidebar {
    background: #313131;
}
.sidebar-nav {
    background: #313131;
    padding: 0;
}
.sidebar-nav>ul>li.active>a {
    color: #ac9452 !important;
    font-weight: 500;
    border-left: 3px solid #313131;
}
.sidebar-nav>ul>li>a.active {
    font-weight: 400;
    background: #313131;
    color: #1976d2 !important;
}
.sidebar-nav>ul>li.active>a i {
    color: #ac9452 !important;
}
th, td {
    color: white !important;
    text-align: center;
}
.table-bordered {
    border: 1px solid #9e9e9e !important;
}
.card {
    background: #313131 none repeat scroll 0 0 !important;
}
.h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6.h1 {
    color: #f9f9f9;
}
.header .top-navbar .navbar-header {
    line-height: 45px;
    text-align: center;
    background: #313131;
}
.header {
    position: relative;
    z-index: 50;
    background: #313131;
    box-shadow: 1px 0 5px rgba(0, 0, 0, 0.1);
}
.text-primary {
    color: #fdfdfd!important;
}
.page-titles {
    background: #313131;
}
.page-wrapper {
    background-color: #1C1B19;
    color: #fff;
    outline: none;
}*/

        </style>

    </head>
    <body class="fix-header fix-sidebar">
        <!-- Preloader - style you can find in spinners.css -->
        <div class="preloader">
            <svg class="circular" viewbox="25 25 50 50">
                <circle class="path" cx="50" cy="50" fill="none" r="20" stroke-miterlimit="10" stroke-width="2">
                </circle>
            </svg>
        </div>

        <!-- Main wrapper  -->
    <div id="main-wrapper">
        <!-- header header  -->
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- Logo -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{ route('admin.home') }}">
                        <!-- Logo icon -->
                        <img src="{{ asset('images/logo1.png') }}" class="logo">
                        <!--End Logo icon -->
                    </a>
                </div>
                <!-- End Logo -->
            </nav>
        </div>
        <!-- End header header -->
        <!-- Left Sidebar  -->
        <div class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">

                        <li class="nav-label">Details</li>
                        <li> <a  href="{{ route('admin.home') }}"><i class="fa fa-user"></i><span class="hide-menu">Users</span></a></li>

                        <!-- <li class="nav-label">Account</li> -->
                         <li> <a  href="{{ route('admin.change.password') }}"><i class="fa fa-key"></i><span class="hide-menu">Change Password</span></a></li>
                        <li> <a href="{{ url('/admin/logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="fa fa-circle"></i><span class="hide-menu">Logout</span></a></li>
                        <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: none;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </div>
        <!-- End Left Sidebar  -->
        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Dashboard</h3>
                </div>
            </div>
            <!-- End Bread crumb -->

            @yield('content')

            </div>
            <!-- End Page wrapper  -->
        </div>
        <!-- End Wrapper -->
        <!-- All Jquery -->
        <script src="{{ asset('js/lib/jquery/jquery.min.js') }}">
        </script>
        <!-- Bootstrap tether Core JavaScript -->
        <script src="{{ asset('js/lib/bootstrap/js/popper.min.js') }}">
        </script>
        <script src="{{ asset('js/lib/bootstrap/js/bootstrap.min.js') }}">
        </script>
        <script src="{{ asset('js/lightbox.min.js') }}">
        </script>
        <!-- slimscrollbar scrollbar JavaScript -->
        <script src="{{ asset('js/jquery.slimscroll.js') }}">
        </script>
        <!--Menu sidebar -->
        <script src="{{ asset('js/sidebarmenu.js') }}">
        </script>
        <!--stickey kit -->
        <script src="{{ asset('js/lib/sticky-kit-master/dist/sticky-kit.min.js') }}">
        </script>

        <!--Custom JavaScript -->
        <script src="{{ asset('js/custom.min.js') }}">
        </script>

        <script src="{{ asset('js/lib/datatables/datatables.min.js') }}">
        </script>
        <script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js') }}">
        </script>
        <script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js') }}">
        </script>
        <script src="{{ asset('js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js') }}">
        </script>
        <script src="{{ asset('js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js') }}">
        </script>
        <script src="{{ asset('js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js') }}">
        </script>
        <script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js') }}">
        </script>
        <script src="{{ asset('js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js') }}">
        </script>
        <script src="{{ asset('js/lib/datatables/datatables-init.js') }}">
        </script>

        <script type="text/javascript">

        </script>
    </body>
</html>
