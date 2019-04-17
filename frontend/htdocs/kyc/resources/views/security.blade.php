@extends('layouts.auth')

@section('content')

@include('layouts.nav')
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

        <form method="POST" action="{{ route('security.update') }}" enctype="multipart/form-data">
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
                            <h6><strong>Two-Factor Authentication (2FA)</strong></h6>
                        </div>
                        <div class="card-body">
                            <p>We highly recommend you to activate Two-Factor Authentication, which will provide maximum protection to your financial assets!</p>
                            <p>On/Off
                                <label class="switch">
                                    <input type="checkbox" name="E2F" @if(auth()->user()->preference->E2F) checked="" @endif>
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
                        <h6><strong>SMS Authentication</strong></h6>
                    </div>
                    <div class="card-body">
                        <p>is the most advanced security system, which adds an extra protection to your account, preventing unauthorized access.</p>
                        <p>On/Off
                            <label class="switch">
                                <input type="checkbox" name="ESMS" @if(auth()->user()->preference->ESMS) checked="" @endif>
                                <span class="slider round"></span>
                            </label>
                        </p>
                        <div style="float: right;"><button class="btn btn-yellow">Submit</button></div>
                    </div>
                    </div>
                </div>
            </div>
            </div>

        </form>

    </div>
</div>
@include('layouts.footer')
@endsection
