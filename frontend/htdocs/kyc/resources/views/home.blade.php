@extends('layouts.auth')

@section('content')

@include('layouts.nav')
<style>
  /*  .navbar {
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
}*/
/*body {
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
    padding: 5px 35px;
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
                <h4 class="text-center"><strong>Account Settings</strong></h4>
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
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="pro card">
                    <div class="card-header">
                        <h6><strong>Profile information</strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="form-box">
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

                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="">First Name*</label>
                                        <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ auth()->user()->first_name }}" required autofocus>

                                        @if ($errors->has('first_name'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('first_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Last Name*</label>
                                        <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ auth()->user()->last_name }}" required>

                                        @if ($errors->has('last_name'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('last_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Email*</label>
                                         <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" readonly name="email" value="{{ auth()->user()->email }}" required>

                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Contact Number*</label>
                                         <input id="phone" type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ auth()->user()->phone }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>

                                        @if ($errors->has('phone'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Country*</label>
                                        <input type="text" id="country" class="form-control{{ $errors->has('country') ? ' is-invalid' : '' }}" name="country" required value="{{ auth()->user()->country }}">
                                            
                                        </select>

                                        @if ($errors->has('country'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('country') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">City*</label>
                                        <input class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" id="city" type="text" class="form-control" name="city" value="{{ auth()->user()->city }}" required>

                                        @if ($errors->has('city'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('city') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="">Address*</label>
                                        <textarea class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" id="address" type="text" class="form-control" name="address" required>{{ auth()->user()->address }}</textarea>

                                        @if ($errors->has('address'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('address') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Postal Code*</label>
                                        <input class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}" id="postal_code" type="text" value="{{ auth()->user()->postal_code }}" class="form-control" name="postal_code" required>

                                        @if ($errors->has('postal_code'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('postal_code') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                     <div class="form-group col-md-6">
                                        <label for="">Avatar*</label>
                                        @if(auth()->user()->avatar != '')
                                        <img src="https://bitexchange.cash/kyc/storage/app/{{ auth()->user()->avatar }}" height="50">
                                        @else
                                        <input class="form-control{{ $errors->has('avatar') ? ' is-invalid' : '' }}" id="avatar" type="file" class="form-control" name="avatar" required accept=".jpg,.jpeg,.png">
                                        @endif

                                        @if ($errors->has('avatar'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('avatar') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Twitter Profile URL</label>
                                        <input class="form-control{{ $errors->has('twitter_profile') ? ' is-invalid' : '' }}" id="twitter_profile" type="url" class="form-control" value="{{ auth()->user()->twitter_profile }}" name="twitter_profile">

                                        @if ($errors->has('twitter_profile'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('twitter_profile') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Linkedin Profile URL</label>
                                        <input class="form-control{{ $errors->has('linkedin_profile') ? ' is-invalid' : '' }}" id="linkedin_profile" type="url" class="form-control" value="{{ auth()->user()->linkedin_profile }}" name="linkedin_profile">

                                        @if ($errors->has('linkedin_profile'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('linkedin_profile') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <button type="submit" class="btn btn-yellow">Save Info</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection
