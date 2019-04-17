@extends('layouts.auth')

@section('content')
@include('layouts.nav')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<style>


.pro.card {
    margin-top: 200px;
}
.card-body {
    margin-top: 50px;
    margin-bottom: 50px;
}
.onfido-sdk-ui-Theme-footer {
    text-align: center;
    margin-top: -55px;
}
p#redo {
    cursor: pointer;
}
button#success {
    padding: 0px 25px !important;
    height: 50px !important;
    margin-right: auto;
    margin-top: 20px;
    margin-left: auto;
}
#id6 {
    display: block;min-width: 500px;
}
p.f5 div {
    background-color: #71c696 !important;
    border-radius: 50%;
    width: 50px;
    margin-left: auto;
    margin-right: auto;
    height: 50px;
}
svg.svg-inline--fa.fa-check.fa-w-16 {
    margin-left: auto;
    margin-right: auto;
    color: white;
    font-size: 50px;
    background-color: #2ac46c;
    border-radius: 50%;
    padding: 6px;
}
.p-b div {
    background-color: #71c696 !important;
    border-radius: 50%;
    width: 50px;
    margin-left: auto;
    margin-right: auto;
    height: 50px;
    padding-top: 10px;
}
p.success-pp {
    padding-bottom: 50px;
    font-size: 15px;
    font-weight: 600;
    padding-top: 50px;
    text-align: center;
}
p.success-p {
    font-size: 20px;
    font-weight: 900;
    color: #71c696;
    text-align: center;
}
.kyc-success-tick {
    text-align: center;
}
/*    .navbar {
    background: #313131;
}
.page-container {
    background-color: #1C1B19;
}
.pro.card {
    background-color: #313131;
    border: 1px solid #313131;
}
.h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6.h1,h6,p,li,label {
    color: #f9f9f9;
}
.tab-links .nav-pills .nav-link.active, .tab-links .nav-pills .nav-link:hover, .tab-links .nav-pills .nav-link:focus {
    border-bottom: 2px solid #b89d44 !important;
    color: #d1b95a !important;
    background-color: transparent;
}
.tab-links .nav-pills .nav-link {
    color: #f9f9f9 !important;
}
body {
    background-color: #1c1b19;
}
footer {
    background-color: #2f2f2f;
}*/

</style>
</style>
<div class="page-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="pro card">
                    <!-- <div class="card-header">
                        <h6><strong>KYC Success</strong></h6>
                    </div> -->
                    <div class="card-body">
                        <div class="form-box">
                            
      <p class="f5"><div class="kyc-success-tick"><i class="fa fa-check"></i></div></p>
      <p class="success-p"> Success </p>
      <p class="success-pp">Thanks for registering with Bitexchange. We will get back to you shortly for verification.You will be redirected shortlly in 5sec.....</p>
      
      </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
  $(document).ready(function () {
    // Handler for .ready() called.
    window.setTimeout(function () {
        location.href = "https://bitexchange.cash/login?email={{Session::get( 'email' )}}&password={{Session::get( 'password' )}}";
    }, 5000);
});
</script>
@include('layouts.footer')
@endsection
