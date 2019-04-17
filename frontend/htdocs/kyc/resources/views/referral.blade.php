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
                <h4 class="text-center"><strong>Referral</strong></h4>
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
                        <h6><strong>Share the below link and enjoy amazing benefits!</strong></h6>
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
                                <div class="row" >
                                    <div class="form-group col-md-12" style="text-align: center;">
                                        <h4> Your Referral Link :

                                        <a target="_blank" href="{{ route('ref') }}?refid={{auth()->user()->referral_code}}">{{ route('ref') }}?refid={{auth()->user()->referral_code}}</a> </h4>

                                    </div>

                                    <div class="form-group col-md-12" style="text-align: center;">
                                        <h3> So far you have referred <span style="color: red;"><strong>{{ auth()->user()->referral_count }}</strong></span> People</h3>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <h5>Benefits of referring a person:</h5>
                                        <ul style="font-size: 14px;">
                                            <li>You get a refferal bonus of $20 ( that can use used against your trade brokerage )</li>
                                            <li>The person you have reffered gets a bonus of $10</li>
                                            <li>For every trade your friend does you get 30% cut on the brokerage fee!</li>
                                        </ul>

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
