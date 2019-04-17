@extends('layouts.auth')

@section('content')
<style>
    .register-card img.logo {
    height: auto;
    width: 40%;
}
img.logo1 {
    width: 20px;
    margin: 0px 0px -41px 110px;
}
</style>
<div class="register-container">
        <div class="container">
            <div class="register-card">
                <img src="{{ asset('images/star.png') }}"" class="logo1">
                <img src="{{ asset('images/logo1.png') }}"" class="logo">
                <!-- <img src="{{ asset('user/img/logo.png') }}" class="logo"> -->
                <h6 class="text-center"><strong>Sign Up</strong></h6>
                <form method="POST" action="{{ route('register') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="form-group col-md-6 col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-user"></i></span>
                            <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" placeholder="Your first name" required autofocus>

                            @if ($errors->has('first_name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-6 col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-user"></i></span>
                            <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" placeholder="Your last name" required>

                            @if ($errors->has('last_name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-phone"></i></span>
                           <input id="phone" type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" placeholder="Phone Number" required>

                            @if ($errors->has('phone'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-envelope"></i></span>
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }} @if(Request::has('email')) {{Request::get('email')}} @endif" placeholder="Your email" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-key"></i></span>
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-key"></i></span>
                            <input id="password-confirm" type="password" placeholder="Confirm Password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-circle"></i></span>
                            <input id="referral_code" type="text" class="form-control{{ $errors->has('referral_code') ? ' is-invalid' : '' }}" value="@if(Request::has('refid')) {{ Request::get('refid') }} @endif" name="referral_code" placeholder="Referral Code">

                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" style="margin: 0 auto;" class="btn btn-primary">Sign Up</button>
                    </div>
                    <div class="col-md-12">
                        <p class="note">Already Registered? <a href="{{route('login')}}">Login</a></p>
                    </div>
                </div>
            </form>
            </div>
            <div class="copyrights">
                <p>&copy; {{date('Y')}} Prutus Exchange All Rights Reserved</p>
            </div>
        </div>
    </div>
@endsection
