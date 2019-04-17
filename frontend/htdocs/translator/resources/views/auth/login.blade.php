@extends('layouts.app')

@section('content')
<style>
    body {
    background-color: #f4f4f4;
}
.login-page .card-body {
    padding-top: 80px;
    padding-bottom: 100px;
}

</style>
<div class="container">
    <div class="row justify-content-center login-page">
        <div class="col-md-8">
            <div class="card">
                

                <div class="card-body">
                    <p class="logo-img">
                        <img src="{{ asset('public/image/star.png') }}" class="logo-star" alt="star">
                        <img src="{{ asset('public/image/logo.png') }}" class="main-logo" alt="Bitexchange">
                    </p>
                    <p class="login_p">Login</p>
                    <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                       <input type="hidden" name="_token" value="{{csrf_token()}}">

                        <div class="form-group row">
                            <label for="email" class="col-sm-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="admin@bitexchange.cash" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required value="12345678">

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div> -->

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary login-btn">
                                    {{ __('Login') }}
                                </button>

                                <!-- <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
