@extends('admin.layout.dashboard')

@section('content')
<style>
    .modal-content {
        background-color: #fff0;
    }
    .close {
        color: #fffcfc;
    }
    .modal-body{
        padding-left: 0px;
    }
    .modal-header {
        border-bottom: unset;
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
            <!-- Column -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-two">
                            <header>
                                <div class="avatar">
                                    <img src="https://bitexchange.cash/kyc/storage/app/{{ $user->avatar }}" alt="{{ $user->first_name }}" />
                                </div>
                            </header>

                            <h3>{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <div class="desc">
                               {{ $user->email }}
                            </div>
                            <div class="contacts">
                                <a href="{{ $user->twitter_profile }}"><i class="fa fa-twitter"></i></a>
                                <a href="{{ $user->linkedin_profile }}"><i class="fa fa-linkedin"></i></a>
                                <a href="mailto:{{ $user->email }}"><i class="fa fa-envelope"></i></a>
                                <div class="clear"></div>
                            </div>
                            <div style="text-align: center;">
                                @if($user->approval == 'PENDING')
                                <a href="{{ route('admin.approval', [ 'id' => $user->id, 'approval' => 'APPROVED' ]) }}" class="btn btn-success">Approve</a>
                                <a href="{{ route('admin.approval', [ 'id' => $user->id, 'approval' => 'DECLINED' ]) }}" class="btn btn-danger">Decline</a>
                                @elseif($user->approval == 'APPROVED')
                                <a href="{{ route('admin.approval', [ 'id' => $user->id, 'approval' => 'DECLINED' ]) }}" class="btn btn-danger">Decline</a>
                                @else
                                <a href="{{ route('admin.approval', [ 'id' => $user->id, 'approval' => 'APPROVED' ]) }}" class="btn btn-success">Approve</a>
                                @endif
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-12">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs profile-tab" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile_info" role="tab">Profile Information</a> </li>
                        <!-- <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#security" role="tab">Security</a> </li> -->
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#kyc" role="tab">KYC / AML Verification</a> </li>
                        <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#contact" role="tab">Contact Preference</a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile_info" role="tabpanel">
                            <div class="card-body">

                                <br>
                                <br>

                                <div class="row">
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>First Name</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->first_name }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>Last Name</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->last_name }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>Phone</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->phone }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>Email</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->email }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6"> <strong>Country</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->country }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6"> <strong>City</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->city }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6"> <strong>Address</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->address }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6"> <strong>Postal Code</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->postal_code }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6"> <strong>Joined at</strong>
                                        <br>
                                        <p class="text-muted">{{ date('d M Y', strtotime($user->created_at)) }}</p>
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                     <!-- <strong>Referral Link</strong> -->
                                        <br>
                                        <!-- <p class="text-muted"><a href="{{ route('ref') }}?refid={{ $user->referral_code }}">{{ route('ref') }}?refid={{ $user->referral_code }}</a></p> -->
                                    </div>
                                    <div class="col-md-6 col-xs-6"> <strong>Twitter Profile</strong>
                                        <br>
                                        <p class="text-muted"><a href="{{ $user->twitter_profile }}">{{ $user->twitter_profile }}</a></p>
                                    </div>
                                    <div class="col-md-6 col-xs-6"> <strong>Linkedin Profile</strong>
                                        <br>
                                        <p class="text-muted"><a href="{{ $user->linkedin_profile }}">{{ $user->linkedin_profile }}</a></p>
                                    </div>

                                </div>
                                <hr>

                            </div>
                        </div>
                        <!--second tab-->
                        <div class="tab-pane" id="security" role="tabpanel">
                            <div class="card-body">
                                <br> <br>
                                <div class="row">
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>Enabled 2 Factor Authentication</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->preference->E2F? 'YES' : 'NO' }}</p>
                                    </div>

                                    <div class="col-md-6 col-xs-6 b-r"> <strong>Enabled SMS Authentication</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->preference->ESMS? 'YES' : 'NO' }}</p>
                                    </div>

                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="tab-pane" id="kyc" role="tabpanel">
                            <div class="card-body">
                                <br> <br>
                                <div class="row">
                                    <div class="col-md-12 col-xs-6 b-r"> <strong>ID Proof Type</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->preference->id_proof_type }}</p>
                                    </div>

                                    <div class="col-md-12 col-xs-6 b-r"> <strong>ID Proof</strong>
                                        <br>
                                        <a  data-toggle="modal" data-target="#id_proof_modal" id="id_proof">
                                        <img src="https://bitexchange.cash/kyc/storage/app/{{ $user->preference->id_proof }}" class="thumbnail" height="200">
                                        </a>
                                    </div>

                                    <div class="col-md-12 col-xs-6 b-r"> <strong>Address Proof Type</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->preference->address_proof_type }}</p>
                                    </div>

                                    <div class="col-md-12 col-xs-6 b-r"> <strong>Address Proof</strong>
                                        <br>
                                        <a  data-toggle="modal" data-target="#address_proof_modal" id="address_proof">
                                        <img src="https://bitexchange.cash/kyc/storage/app/{{ $user->preference->address_proof }}" class="thumbnail" height="200">
                                        </a>
                                    </div>

                                    <div class="col-md-12 col-xs-6 b-r"> <strong>ID Card</strong>
                                        <br>
                                        <a data-toggle="modal" data-target="#id_card_modal" id="id_card">
                                        <img src="https://bitexchange.cash/kyc/storage/app/{{ $user->preference->id_card }}" class="thumbnail" height="200">
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>

                         <div class="tab-pane" id="contact" role="tabpanel">
                            <div class="card-body">
                                <br> <br>
                                <div class="row">
                                    <div class="col-md-6 col-xs-6 b-r"> <strong>SMS</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->preference->contact_email? 'YES' : 'NO' }}</p>
                                    </div>

                                    <div class="col-md-6 col-xs-6 b-r"> <strong>EMAIL</strong>
                                        <br>
                                        <p class="text-muted">{{ $user->preference->contact_email? 'YES' : 'NO' }}</p>
                                    </div>

                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
        </div>

        <!-- End PAge Content -->
    </div>
    <!-- End Container fluid  -->
<!-- id proofModal -->
  <div class="modal fade" id="id_proof_modal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body" style="width: 700px;">
          <button type="button" class="close" data-dismiss="modal" id="modal_close">&times;</button>
          <br><br>
          <img src="https://bitexchange.cash/kyc/storage/app/{{ $user->preference->id_proof }}" style="width: 700px;">
        </div>
      </div>
      
    </div>
  </div>



  <div class="modal fade" id="address_proof_modal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body" style="width: 500px;">
          <button type="button" class="close" data-dismiss="modal" id="address_modal_close">&times;</button>
          <br><br>
          <img src="https://bitexchange.cash/kyc/storage/app/{{ $user->preference->address_proof }}" style="width: 500px;">
        </div>
      </div>
      
    </div>
  </div>



  <div class="modal fade" id="id_card_modal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body" style="width: 700px;">
          <button type="button" class="close" data-dismiss="modal" id="id_modal_close">&times;</button>
          <br><br>
          <img src="https://bitexchange.cash/kyc/storage/app/{{ $user->preference->id_card }}" style="width: 700px;">
        </div>
      </div>
      
    </div>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script>
      $('#modal_close').click(function() {
        
          // $('.modal-backdrop').removeClass('show');
          $('.modal-open').css('overflow','scroll');
          $('.modal-backdrop').css('display','none');
          $('#id_proof_modal').css('display','none');
      })

      $('#address_modal_close').click(function() {
        
          // $('.modal-backdrop').removeClass('show');
          $('.modal-open').css('overflow','scroll');          
          $('.modal-backdrop').css('display','none');
          $('#address_proof_modal').css('display','none');
      })

      $('#id_modal_close').click(function() {
        
          // $('.modal-backdrop').removeClass('show');
          $('.modal-open').css('overflow','scroll');
          $('.modal-backdrop').css('display','none');
          $('#id_card_modal').css('display','none');
      })
      $('#id_proof').click(function() {
        
          // $('.modal-backdrop').addClass('show');
          $('.modal-backdrop').css('display','block');
          $('#id_proof_modal').css('display','block');
      })
      $('#address_proof').click(function() {
        
          // $('.modal-backdrop').addClass('show');
          $('.modal-backdrop').css('display','block');
          $('#address_proof_modal').css('display','block');
      })
      $('#id_card').click(function() {
        
          // $('.modal-backdrop').addClass('show');
          $('.modal-backdrop').css('display','block');
          $('#id_card_modal').css('display','block');
      })
  </script>
@endsection
