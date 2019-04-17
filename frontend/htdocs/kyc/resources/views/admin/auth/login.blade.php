@extends('admin.layout.auth')

@section('content')
<style type="text/css">
    img.logo {
    height: auto;
    width: 53%;
}
img.logo1{
    width: 25px;
    margin: 0px 0px 8px 52px;
}
/*body.fix-header.fix-sidebar {
    background-color: #1C1B19 !important;
    color: #fff;
    outline: none;
}
.login-form {
    background-color: #313131 !important;
    border: 1px solid #313131 !important;
}
.login-content.card {
    background-color: #313131 !important;
    border: 1px solid #313131 !important;
}
.login-form label {
    color: #ffffff;
    text-transform: uppercase;
}*/
button.btn.btn-flat.m-b-30.m-t-30 {
    border: 2px solid #c2ab6f !important;
    background: transparent !important;
    line-height: 36px;
    /* height: 40px; */
    border-radius: 30px;
    color: #c2ab6f !important;
    width: 100% !important;
}
.login-form h4 {
    color: #ffffff;
    text-align: center;
    margin-bottom: 50px;
}
.card {
    
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.58) !important;
}
</style>
<div id="main-wrapper">
    <div class="unix-login">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="login-content card">
                        <div class="login-form">
                            <p style="text-align: center;">
                                <img src="{{ asset('images/logo1.png') }}" class="logo">
                            </p>
                            <h4>
                                KYC Admin Login
                            </h4>
                            @if (session('status'))
                            <style>.alert.alert-success {color: white;background-color: #f11f1f;border-color: #f11f1f;}</style>
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                            @endif
                            <form action="{{ route('admin.login') }}" class="form-horizontal" method="POST" role="form">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">
                                        E-Mail Address
                                    </label>
                                    <input autofocus="" class="form-control" id="email" name="email" type="email" value="{{ old('email') }}">
                                        @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>
                                                {{ $errors->first('email') }}
                                            </strong>
                                        </span>
                                        @endif
                                    </input>
                                </div>
                                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password">
                                        Password
                                    </label>
                                    <input class="form-control" id="password" name="password" type="password">
                                    <!--<span class="input-group-addon"><i class="fa fa-eye-slash" onclick="showpassword(this);"></i></span> -->
                                        @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>
                                                {{ $errors->first('password') }}
                                            </strong>
                                        </span>
                                        @endif
                                    </input>
                                </div>
                                <button class="btn btn-flat m-b-30 m-t-30" type="submit">
                                    Sign in
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        function showpassword(x1) {
              
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
                x1.classList.toggle("fa fa-eye");
            } else {
                x.type = "password";
                x1.classList.toggle('fa-eye-slash');
            }
        }
    </script>
@endsection
