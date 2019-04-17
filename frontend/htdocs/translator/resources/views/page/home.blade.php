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
.nav-min-width {
    height: 1000px !important;
}

</style>
<div class="row page-row">
    <div class="justify-content-center page">
        <div class="col-md-12 page-content-padding">
            <!-- <button type="button" class="btn btn-primary page-add-btn-modal" data-toggle="modal" data-target="#add">
                <i class="fa fa-plus" aria-hidden="true"></i>Add
            </button> -->
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
            <h2 class="page-title">Page</h2>
            
            <div class="card">
                <div class="card-header">
                    <ul>
                        <li>Display the headings and subheadings on different sections of the page in different languages.</li>
                        <li>All your pages are listed here. Click on the edit button under<strong>'Page Content'</strong> column to view and edit the language strings for each language.
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <table class="table table-striped table-bordered" id="example">
                        <thead>
                            <tr class="text-center">
                                <th>S.No</th>
                                <th>Page Name</th>
                                <th>Page Content</th>
                                <th>Page Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($pages)
                            <!-- {{$s_no=1}} -->
                            @foreach($pages as $page)
                            <tr class="text-center">
                                <td>{{$s_no}}</td>
                                <td>{{$page->page_name}}</td>
                                <td>
                                    <a href="{{url('/')}}/page-content/{{$page->id}}">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td>@if($page->page_status == 1)Active @else InActive @endif</td>
                                <td>
                                    <i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="modal" data-target="#edit{{$page->id}}"></i>
                                    <i class="fa fa-trash-o" aria-hidden="true" data-toggle="modal" data-target="#delete{{$page->id}}"></i>
                                    
                                </td>
                            </tr>
                            <!-- {{$s_no++}} -->
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="text-center">No Page to Show</td>
                            </tr>
                            @endif
                        </tbody>
                        <thead>
                            <tr class="text-center">
                                <th>S.No</th>
                                <th>Page Name</th>
                                <th>Page Content</th>
                                <th>Page Status</th>
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
        <h4 class="modal-title">Add New Page</h4>

        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

     <form class="modal-form" method="POST" action="{{ route('page.add') }}" >
      <!-- Modal body -->
      <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="form-group">
                    <label for="name">Page Name:</label>
                    <input type="text" class="form-control" id="page_name" placeholder="Enter Your Page" name="page_name" required>
                </div>
                <div class="form-group">
                    <label for="status">Page Status:</label>
                    <input id="toggle-event1" name="page_status" checked  type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
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

<!-- Edit Page Modal -->
@foreach($pages as $page)
<div class="modal" id="edit{{$page->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Page</h4>
                
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form class="modal-form" method="POST" action="{{ route('page.edit') }}" >
      <!-- Modal body -->
      <div class="modal-body">
            
               <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$page->id}}">
                <div class="form-group">
                    <label for="email">Page Name:</label>
                    <input type="text" class="form-control" id="page_name" placeholder="Enter Your Page" name="page_name" value="{{$page->page_name}}" required>
                </div>
                <div class="form-group">
                    <label for="email">Page Status:</label>
                    
                    <input id="toggle-event" name="page_status" type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger" @if($page->page_status == 1) checked @else @endif>
                   

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

<!-- Delete Page Modal -->

@foreach($pages as $page)
<div class="modal" id="delete{{$page->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

         <!-- Modal body -->
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">You want to delete {{$page->page_name}} Page</h4>
        </div>
        <div class="modal-footer">
            <form class="modal-form" method="POST" action="{{ route('page.delete') }}" >
           <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$page->id}}">
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
            $('#page_status').val('1');
      }else{
            $('#page_status').val('0');
      }
    })

    $('#toggle-event1').change(function() {
      
      if ($(this).prop('checked')) {
            $('#page_status1').val('1');
      }else{
            $('#page_status1').val('0');
      }
    })
  })
</script>
@endsection
