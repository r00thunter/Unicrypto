@extends('layouts.app')

@section('content')
<style>
    .container1 {
    max-width: 100%;
    padding-left: 50px;
}
.div-card {
        width: 272px;
    display: inline-block;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 10px;
    height: 100%!important;
    flex: 1 1 auto;
    padding: 1.25rem;
    margin-right: 20px;
    color: white;
}
.line11 {
    width: 281px;
    height: 24px;
    border-bottom: 1px solid green;
    -webkit-transform: translateY(18px) translateX(5px) rotate(-25deg);
    position: absolute;
    /* top: -33px; */
    left: 0px;
}
p.align-left {
    text-align: left;
    color: black;
}
p {
    margin-top: 0;
    margin-bottom: 1rem;
}
p.align-right {
    text-align: right;
}

.bg-light.nav-min-width {
    background-color: #2f3340!important;
    height: 900px;
}
</style>
<div class="container1">
    <div class="row page-row">
    <h2 class="page-title">Language Conversion Dashboard</h2>
        
        <div class="col-md-6" style="
    max-width: 100%;
">
            <div class="card">
                <div class="card-header">Available Language</div>

                <div class="card-body">
  
    <div class="div-card"><div class="line1"></div><p class="align-left">English</p><p class="align-right">English</p></div>
   <div class="div-card"><div class="line1"></div><p class="align-left">français</p><p class="align-right">French</p></div>
   <div class="div-card"><div class="line1"></div><p class="align-left">عربى</p><p class="align-right">Arabic</p></div>
   <div class="div-card"><div class="line1"></div><p class="align-left">Español</p><p class="align-right">Spanish</p></div>
    <br><br>
    <div class="div-card"><div class="line1"></div><p class="align-left">Deutsche</p><p class="align-right">German</p></div>
    <div class="div-card"><div class="line1"></div><p class="align-left">Melayu</p><p class="align-right">Malay</p></div>
                    
                </div>
            </div>
        </div>
        <br><br>
        <div class="col-md-6" style="max-width: 100%;">
            <div class="card">
                <div class="card-header">Steps to be followed :</div>

                <div class="card-body">
                   
                    <p>You can view and send tokens to people who have purchased the tokens here.</p>
                    <p>- All your Token requests are listed here. In the Status, if it shows as PENDING it means that people have paid and are waiting for you to transfer the tokens to them.</p>
                    <p>- See how much they paid and transfer the exact amount of tokens they paid for.</p>
                    <p>- Find the latest token request orders here. For semi-automatic token delivery, you will have to verify the payment and send the tokens from here. For Automatic delivery, if the contract is configured, it will show already sent.</p>
                    <p>- To send tokens to users, click on SEND TOKENS under the Action tab.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
