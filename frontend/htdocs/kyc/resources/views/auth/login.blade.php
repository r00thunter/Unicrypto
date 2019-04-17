@extends('layouts.auth')

@section('content')
<style>
.svg-inline--fa {margin-right: 10px !important;}
.register-card img.logo {
    height: auto;
    width: 40%;
}
img.logo1{
    width: 20px;
    margin: 0px 0px -41px 110px;
}
/*body.register-page {
    background-color: #1C1B19 !important;
    color: #fff;
    outline: none;
}
.register-card {
    background-color: #313131 !important;
    border: 1px solid #313131 !important;
}
button.btn.btn-primary {
    border: 2px solid #c2ab6f !important;
    background: transparent !important;
    line-height: 36px;*/
    /* height: 40px; */
/*    border-radius: 30px;
    color: #c2ab6f !important;
    width: 100% !important;
}
button.btn.btn-primary:hover {
    background: transparent;
}*/
/*Change text in autofill textbox*/
input:-webkit-autofill {
    -webkit-text-fill-color: #313131 !important;
}

</style>
<div class="register-container">
        <div class="container">
            <div class="register-card">
                <img src="{{ asset('images/logo1.png') }}" class="logo">
                <h6 class="text-center"><strong>{{ __('Login') }}</strong></h6>

        @if (session('status'))
        <style>.alert.alert-success {color: white;background-color: #f11f1f;border-color: #f11f1f;}</style>
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif
                <form method="POST" action="{{ route('frontend.login') }}">

                    <div class="row">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                       <div class="form-group col-md-12">
                        <div class="">
<!--                             <span class="input-group-addon"><i class="fas fa-envelope"></i></span> -->
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder=" Enter your regitered email ID" required autofocus>
                        @if ($errors->has('email'))
                        <style>span.invalid-feedback {font-size: 13px;}.input-group {margin-top: 5px;}</style>
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif                            
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="">
                            <!-- <span class="input-group-addon"><i class="fas fa-key"></i></span> -->
                            <input id="password" type="password" placeholder="Your Password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                            <!-- <span class="input-group-addon"><i class="fa fa-eye-slash" onclick="showpassword(this);"></i></span> -->
                            @if ($errors->has('password'))
                        <style>span.invalid-feedback {font-size: 13px;}.input-group {margin-top: 5px;}</style>
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" style="margin: 0 auto;">Login</button>
                    </div>
                    
                </div>
            </div>
                </form>

            <div class="copyrights">
                <p>&copy; {{date('Y')}} BitExchange All Rights Reserved</p>
            </div>
        </div>
    </div>
    <script type="text/javascript">
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
@endsection
