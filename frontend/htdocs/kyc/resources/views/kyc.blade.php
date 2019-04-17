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
                <h4 class="text-center"><strong>KYC/AML Verification</strong></h4>
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

        <form method="POST" action="{{ route('kyc.update') }}" enctype="multipart/form-data">
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
                <div class="col-md-2 col-sm-3 col-xs-12">
                </div>
                <div class="col-md-8 col-sm-6 col-xs-12">
                    <div class="pro card">
                        <div class="card-header">
                            <h6><strong>KYC / AML Verification</strong></h6>
                        </div>
                        <div class="card-body">
                            <div class="form-box">
                                <form>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="row">
                                        <div class="form-group col-md-12" style="color: #212529; padding: 10px; border-radius: 10px;" >
                                            <h5><strong>ID Proof*</strong></h5>
                                            <select name="id_proof_type" required="" class="form-control">
                                                <option @if(auth()->user()->preference->id_proof_type == 'PASSPORT') selected @endif value="PASSPORT">Passport</option>
                                                <option @if(auth()->user()->preference->id_proof_type == 'RA') selected @endif value="RA">Rental Agreeent</option>
                                                <option @if(auth()->user()->preference->id_proof_type == 'SSC') selected @endif value="SSC">Social Security Card</option>
                                                <option @if(auth()->user()->preference->address_proof_type == 'DL') selected @endif value="DL">Drivers License</option>
                                                <option @if(auth()->user()->preference->id_proof_type == 'OTHER') selected @endif value="OTHER">Others</option>
                                            </select>
                                            @if(auth()->user()->preference->id_proof != '')
                                            <br>
                                            <img src="https://bitexchange.cash/kyc/storage/app/{{auth()->user()->preference->id_proof}}">
                                            @endif
                                            <input type="file" name="id_proof" required class="form-control" accept=".png,.jpg,.jpeg">
                                        </div>
                                        <div class="form-group col-md-12" style="color: #212529; padding: 10px; border-radius: 10px;">
                                            <h5><strong>Address Proof*</strong></h5>
                                            <select name="address_proof_type" required class="form-control">
                                                <option @if(auth()->user()->preference->id_proof_type == 'PASSPORT') selected @endif value="PASSPORT">Passport</option>
                                                <option @if(auth()->user()->preference->id_proof_type == 'RA') selected @endif value="RA">Rental Agreeent</option>
                                                <option @if(auth()->user()->preference->id_proof_type == 'SSC') selected @endif value="SSC">Social Security Card</option>
                                                <option @if(auth()->user()->preference->address_proof_type == 'DL') selected @endif value="DL">Drivers License</option>
                                                <option @if(auth()->user()->preference->id_proof_type == 'OTHER') selected @endif value="OTHER">Others</option>
                                            </select>
                                            @if(auth()->user()->preference->address_proof != '')
                                            <br>
                                            <img src="https://bitexchange.cash/kyc/storage/app/{{auth()->user()->preference->address_proof}}">
                                            @endif
                                            <input name="address_proof" required type="file" class="form-control" accept=".png,.jpg,.jpeg">
                                        </div>
                                        <div class="form-group col-md-12" style="color: #212529 padding: 10px; border-radius: 10px;">
                                            <h5><strong>Photo Holding your ID Card*</strong></h5>
                                            <h6><strong>Note : Please make sure that you upload a High Resolution image. Your face should also be visible clear and the details in the ID proof should also be very easily readable.</strong></h6>
                                            @if(auth()->user()->preference->id_card != '')
                                            <br>
                                            <img src="https://bitexchange.cash/kyc/storage/app/{{auth()->user()->preference->id_card}}">
                                            @else
                                            <img src="https://i.imgur.com/9IBKwcG.png?1">
                                            @endif
                                            <br>
                                            <br>
                                            <input type="file" name="id_card" required="" class="form-control" accept=".png,.jpg,.jpeg">
                                        </div>

                                        <div class="col-md-12 text-right">
                                            <button type="submit"  class="btn btn-yellow">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-3 col-xs-12">
                </div>
            </div>

         </form>

        </div>
</div>
@include('layouts.footer')
@endsection
