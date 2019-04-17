@extends('admin.layout.dashboard')

@section('content')
<style>
table {
    font-size : 10px;
}

tbody tr td
{
    color : black;
}
tbody tr td:last-child
{
    text-align:left;
}
.dataTable > thead > tr > th[class*=sort]:after{
    display:none;
}
.dt-buttons .dt-button {
        background: #f4ba2f !important;
    border: 2px solid #ffb22b !important;
    background: transparent !important;
    line-height: 36px;
    /* height: 40px; */
    border-radius: 30px;
    color: #131313 !important;
    width: 100% !important;
}
.left-sidebar {
    background: #ffffff !important;
}
.sidebar-nav {
    background: #ffffff;
    padding: 0;
}
.sidebar-nav>ul>li>a.active {
    font-weight: 400;
    background: #ffffff !important;
    color: #1976d2 !important;
}
.page-titles {
    background: #fffdfd;
}
.header {
    position: relative;
    z-index: 50;
    background: #ffffff;
    box-shadow: 1px 0 5px rgba(0, 0, 0, 0.1);
}
.header .top-navbar .navbar-header {
    line-height: 45px;
    text-align: center;
    background: #ffffff;
}
.h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6.h1, p, li {
    color: #000000;
}
.text-primary {
    color: #101010!important;
}
.page-wrapper {
    background-color: #ffffff;
    color: #040404;
    outline: none;
}
h4.card-title {
    color: white;
}
.card {
    background: #ffffff none repeat scroll 0 0 !important;
}
th, td {
    color: black !important;
    text-align: center;
    font-size: 11px;
}
</style>
<!-- Container fluid  -->
<div class="container-fluid">
    <!-- Start Page Content -->
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"> @if(Request::has('id')) {{Request::get('name')}}'s Referrals @else Users @endif</h4>
                    <div class="table-responsive m-t-40">
                        <table id="example23" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Reffered by</th>
                                    <th>Status</th>
                                    <th>Referral Count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($Users as $user)
                                <tr>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                    @if(!empty($user->referredBy))
                                    <a href="{{ route('admin.details') }}?id={{ $user->referredBy->id }}&name={{ $user->referredBy->first_name }}">{{ $user->referredBy->first_name }}<a>
                                    @else
                                        None
                                    @endif
                                    </td>
                                    <td>{{ $user->approval }}</td>
                                    <td>{{ $user->referral_count }}</td>
                                    <td>
                                        <div class="btn-group">

                                            <div class="btn-group">
                                                <button type="button" class="btn btn-xs btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                               Action <span class="caret"></span></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        @if($user->approval == 'PENDING')
                                                        <a href="{{ route('admin.approval', [ 'id' => $user->id, 'approval' => 'APPROVED' ]) }}" class="btn btn-xs  btn-success">Approve</a>
                                                        <a href="{{ route('admin.approval', [ 'id' => $user->id, 'approval' => 'DECLINED' ]) }}" class="btn  btn-xs btn-danger">Decline</a>
                                                        @elseif($user->approval == 'APPROVED')
                                                        <a href="{{ route('admin.approval', [ 'id' => $user->id, 'approval' => 'DECLINED' ]) }}" class="btn  btn-xs btn-danger">Decline</a>
                                                        @else
                                                        <a href="{{ route('admin.approval', [ 'id' => $user->id, 'approval' => 'APPROVED' ]) }}" class="btn  btn-xs btn-success">Approve</a>
                                                        @endif
                                                    </li>
                                                    <!-- <li><a href="{{ route('admin.referrals') }}?id={{ $user->id }}&name={{ $user->first_name }}" class="btn btn-info btn-xs">Referrals</a></li> -->
                                                    <li><a class="btn btn-warning btn-xs" href="{{ route('admin.details') }}?id={{ $user->id }}&name={{ $user->first_name }}">More Details</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- End PAge Content -->
</div>
<!-- End Container fluid  -->

@endsection
