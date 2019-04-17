@extends('layouts.auth')

@section('content')
@include('layouts.nav')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<style>
    
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
    font-size: 30px;
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
}
p.success-p {
    font-size: 20px;
    font-weight: 900;
    color: #71c696;
}
</style>
</style>
<div class="page-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="pro card">
                    <div class="card-header">
                        <h6><strong>KYC Success</strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="form-box">
                            
      <p class="f5"><div><i class="fa fa-check"></i></div></p>
      <p class="success-p"> Success </p>
      <p class="success-pp">Thanks for registering with LCCX. We will get back to you shortly for verification.</p>
      
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
        location.href = "https://q8bit.com";
    }, 5000);
});
</script>
@include('layouts.footer')
@endsection
