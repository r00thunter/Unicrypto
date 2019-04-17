@extends('layouts.auth')

@section('content')

@include('layouts.nav')

<style>
   /* .navbar {
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
}
button.btn.btn-yellow {
    border: 2px solid #c2ab6f !important;
    background: transparent !important;
    line-height: 36px;
    border-radius: 30px;
    color: #c2ab6f !important;
    padding: 5px 50px;
    font-size: 17px;
}*/

</style>
<div class="page-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <br>
                <br>
                <br>
                <h4 class="text-center"><strong>Contact Preference</strong></h4>
                <br>
                <br>
                <br>
            </div>
            @include('layouts.progress')
            <br>
            <br>
            <br>
            @include('layouts.tab')
        </div>

        <form method="POST" action="{{ route('contact.update') }}" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif

         @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
        @endif

        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">

                    <div class="pro card">
                        <div class="card-header">
                            <h6><strong>Email</strong></h6>
                        </div>
                        <div class="card-body">
                            <h4>Can we contact you via. email?</h4>
                            <p>On/Off
                                <label class="switch">
                                    <input type="checkbox" name="contact_email" @if(auth()->user()->preference->contact_email) checked="" @endif>
                                    <span class="slider round"></span>
                                </label>
                            </p>
                            <div style="float: right;"><button class="btn btn-yellow">Submit</button></div>
                        </div>
                    </div>

                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">

                    <div class="pro card">
                        <div class="card-header">
                            <h6><strong>SMS</strong></h6>
                        </div>
                        <div class="card-body">
                            <h4>Can we contact you via. SMS?</h4>
                            <p>On/Off
                                <label class="switch">
                                    <input type="checkbox" name="contact_sms" @if(auth()->user()->preference->contact_sms) checked="" @endif>
                                    <span class="slider round"></span>
                                </label>
                            </p>
                            <div style="float: right;"><button class="btn btn-yellow">Submit</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        </div>
</div>
@include('layouts.footer')
@endsection
