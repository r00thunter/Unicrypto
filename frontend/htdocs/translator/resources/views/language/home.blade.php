@extends('layouts.app')

@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap2-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap2-toggle.min.js"></script>
<style>
    i.fa.fa-pencil-square-o {
    color: #28a745;
    font-weight: 600;
}
i.fa.fa-trash-o {
    color: #dc3545;
    font-weight: 600;
}
.toggle.btn {
    min-width: 59px;
    min-height: 34px;
}
span.toggle-handle.btn.btn-default {
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}
.modal-body h4.modal-title {
    font-size: 1rem;
}
nav.navbar.navbar-light.navbar-laravel {
    position: fixed;
    max-width: 100%;
    width: 100%;
    z-index: 9999;
}
.bg-light.nav-min-width {
    position: fixed !important;
    margin-top: 55px;
}

</style>
<div class="row page-row">
    <div class="justify-content-center page">
        <div class="col-md-12 page-content-padding">
          <br><br>
            <button type="button" class="btn btn-primary page-add-btn-modal" data-toggle="modal" data-target="#add">
                <i class="fa fa-plus" aria-hidden="true"></i>Add a New Language
            </button>
            @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session()->get('success') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="alert alert-error alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session()->get('error') }}
            </div>
        @endif
            <h2 class="page-title">Available Languages</h2>
            
            <div class="card">
                <div class="card-header">
                    <ul>
                        <li>Make your site truly multi-lingual, display your exchange in multiple languages so your users from different countries can view it in a language they understand.</li>
                        <li>The languages available on your exchange are listed below. You can edit the language name, symbol, make it unavailable on the exchange or delete them.</li>
                        <li>To Add a new language, Click on the <strong>+Add a New Language</strong> button at the right corner, then you can go to pages, footer, menu sections and enter the text for the newly added languages.
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <table class="table table-striped table-bordered" id="example">
                        <thead>
                            <tr class="text-center">
                                <th>S.No</th>
                                <th>Language</th>
                                <th>Language Symbol</th>
                                <th>Language Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($languages)
                            <!-- {{$s_no=1}} -->
                            @foreach($languages as $language)
                            <tr class="text-center">
                                <td>{{$s_no}}</td>
                                <td>{{$language->language_name}}</td>
                                <td>{{$language->language_symbol}}</td>
                                <td>@if($language->language_status == 1)Active @else InActive @endif</td>
                                <td>
                                    <i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="modal" data-target="#edit{{$language->id}}"></i>
                                    <i class="fa fa-trash-o" aria-hidden="true" data-toggle="modal" data-target="#delete{{$language->id}}"></i>
                                    
                                </td>
                            </tr>
                            <!-- {{$s_no++}} -->
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="text-center">No Language to Show</td>
                            </tr>
                            @endif
                        </tbody>
                        <thead>
                            <tr class="text-center">
                                <th>S.No</th>
                                <th>Languages</th>
                                <th>Language Symbol</th>
                                <th>Language Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="add">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add New Language</h4>

        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form class="modal-form" method="POST" action="{{ route('language.add') }}" >
      <div class="modal-body">
            
               <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="form-group">
                    <label for="name">Name of the Language (English, French.. etc):</label>
                    <input type="text" class="form-control" id="language_name" placeholder="Enter Your Language" name="language_name" required>
                </div>
                <div class="form-group">
                    <label for="symbol">Symbol to denote the language (En, Fr,..etc):</label>
                    <input type="text" class="form-control" id="language_symbol" placeholder="Enter Your Language Symbol" name="language_symbol" required>
                </div>
                <div class="form-group">
                    <label for="status">Make the language available:</label>
                    <input id="toggle-event1" name="language_status" checked  type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                    <!-- <input type="hidden" name="language_status" id="language_status1" value="1"> -->
                </div>
                
            
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
</form>
    </div>
  </div>
</div>

<!-- Edit Language Modal -->
@foreach($languages as $language)
<div class="modal" id="edit{{$language->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Language</h4>
                
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form class="modal-form" method="POST" action="{{ route('language.edit') }}" >
      <!-- Modal body -->
      <div class="modal-body">
            
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$language->id}}">
                <div class="form-group">
                    <label for="email">Language Name:</label>
                    <input type="text" class="form-control" id="language_name" placeholder="Enter Your Language" name="language_name" value="{{$language->language_name}}" required>
                </div>
                <div class="form-group">
                    <label for="email">Language Symbol:</label>
                    <input type="text" class="form-control" id="language_symbol" placeholder="Enter Your Language Symbol" name="language_symbol" value="{{$language->language_symbol}}" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Language Status:</label>
                    
                    <input id="toggle-event" name="language_status" type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger" @if($language->language_status == 1) checked @else @endif>
                    <!-- <input type="hidden" name="language_status" id="language_status" value="1"> -->
                    

                </div>
                
              
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>                
    </form>

    </div>
  </div>
</div>
@endforeach

<!-- Delete Language Modal -->

@foreach($languages as $language)
<div class="modal" id="delete{{$language->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

         <!-- Modal body -->
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">You want to delete {{$language->language_name}} Language</h4>
        </div>
        <div class="modal-footer">
            <form class="modal-form" method="POST" action="{{ route('language.delete') }}" >
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$language->id}}">
                <button type="submit" class="btn btn-primary">Yes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
             </form>
        </div>
    </div>
  </div>
</div>
@endforeach


<script>
  $(function() {
    $('#toggle-event').change(function() {
      
      if ($(this).prop('checked')) {
            $('#language_status').val('1');
      }else{
            $('#language_status').val('0');
      }
    })

    $('#toggle-event1').change(function() {
      
      if ($(this).prop('checked')) {
            $('#language_status1').val('1');
      }else{
            $('#language_status1').val('0');
      }
    })
  })
</script>
@endsection
