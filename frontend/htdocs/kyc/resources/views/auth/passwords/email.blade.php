@extends('layouts.auth')

@section('content')
<div class="register-container">
        <div class="container">
            <div class="register-card">
                <img src="{{ asset('user/img/logo.png') }}" class="logo">
                <h6 class="text-center"><strong>Forgot Password</strong></h6>
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>

                        <div class="col-md-12">
                        <p class="note">Already Registered? <a href="{{route('login')}}">Login</a></p>
                    </div>

                    </form>
                    <div class="col-md-12">
                        <br>
                            <p>In case you did not get the email, Please check your SPAM folder also.</p>
                        </div>
                </div>
            </div>
        </div>
@endsection
