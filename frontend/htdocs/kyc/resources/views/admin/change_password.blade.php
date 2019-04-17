@extends('admin.layout.dashboard')

@section('content')
<style>
    button.btn.btn-primary {
    border: 2px solid #c2ab6f !important;
    background: transparent !important;
    line-height: 36px;
    /* height: 40px; */
    border-radius: 30px;
    color: #c2ab6f !important;
    width: 100% !important;
    /*box-shadow: 0 14px 26px -12px #c2ab6f, 0 4px 23px 0 #c2ab6f, 0 8px 10px -5px #c2ab6f;*/
}
.page-wrapper {
    background-color: #fffffe;
    color: #fff;
    outline: none;
}
</style>
<!-- Container fluid  -->
<div class="container-fluid">
    <!-- Start Page Content -->
    <div class="row">
        <!-- Column -->
        <div class="col-lg-12">
            <div class="card">
                <h2>
                    Change Password
                </h2>
                <div class="card-body">
                    <br>
                    @if (session('status'))
                    <div class="alert alert-info">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(Session::has('flash_error'))
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            {{ Session::get('flash_error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.update.password') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="1">
                        <br>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input class="form-control" autofocus="" type="password" value="" name="password">

                                    </div>

                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input class="form-control" type="password" value="" name="password_confirmation">

                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-primary" type="submit">Update Password</button>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <hr>
                            </hr>
                        </br>
                    </br>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!-- End PAge Content -->
</div>
<!-- End Container fluid  -->
@endsection
