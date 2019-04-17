@extends('layouts.auth')

@section('content')
<style type="text/css">
img.logo {
    height: auto;
    width: 16%;
}
img.logo1{
    width: 25px;
    margin: 0px 0px 5px -7px;
}
.page-container {
    min-height: 85.5vh;
}
</style>
<div class="page-container">
    <div class="container">
<div class="row" style="text-align: center;">
            <div class="col-lg-12"><br><br><br><br>
                <img src="{{ asset('images/star.png') }}" class="logo1">
                <img src="{{ asset('images/logo1.png') }}" class="logo">                  
                  <h1>
                    Oops!</h1>
                <h4>
                    Something went wrong!</h4>
                
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection