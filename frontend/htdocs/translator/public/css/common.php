body,h1,h2,h3,h4,h5,h6,p,li,label,a{
	font-family: 'Open Sans', sans-serif;
}

/*Login page start*/

.logo-star {
    width: 20px;
    position: relative;
    top: -2px;
}
.main-logo {
    width: 165px;
}
p.logo-img {
    text-align: center;
    /* padding-left: 100px; */
}
p.login_p {
    text-align: center;
    font-size: 16px;
    font-weight: 600;
}
.login-page {
    padding-top: 170px;
}
.login-btn {
    background-color: #f4ba2f !important;
    border-color: #f4ba2f !important;
    font-weight: 600 !important;
}

/*Login page End*/

/*nav bar start*/
.full-with {
    width: 100%;
}
div#navbarSupportedContent {
    display: block !important;
    float: right !important;
}
div#navbarSupportedContent .dropdown-menu.dropdown-menu-right {
    position: absolute !important;
}
@media (min-width: 768px){
div#navbarSupportedContent {
    padding-right: 150px !important;
}
.nav-menu-hide {
    display: none;
}
}
@media (max-width: 767px){
.nav-menu-hide {
    display: block;
}
div#navbarSupportedContent1{
	display: none;
}
}
.nav-min-width {    
	width: 15%;
    position: absolute !important;
    height: 100%;
}
.nav-min-width ul.navbar-nav {
    display: block;
    height: 100%;
}
.navbar-laravel {
    background-color: #2f3340;
}
.navbar-light .navbar-nav .nav-link {
    color: rgb(255, 255, 255) !important;
}
.navbar-light .navbar-nav .nav-link:focus, .navbar-light .navbar-nav .nav-link:hover {
    color: rgb(255, 248, 248) !important;
}
/*nav bar end*/