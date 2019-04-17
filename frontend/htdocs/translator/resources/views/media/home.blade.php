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
</style>
<div class="row page-row">
    <div class="justify-content-center page">
        <div class="col-md-12 page-content-padding">
            <button type="button" class="btn btn-primary page-add-btn-modal" data-toggle="modal" data-target="#add">
                <i class="fa fa-plus" aria-hidden="true"></i>Add
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
            <h2 class="page-title">Media</h2>
            
            <div class="card">
                <!-- <div class="card-header">Dashboard</div> -->

                <div class="card-body">
                    <table class="table table-striped table-bordered" id="example">
                        <thead>
                            <tr class="text-center">
                                <th>S.No</th>
                                <th>Media Name</th>
                                <th>Media Image</th>
                               
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($media)
                            <!-- {{$s_no=1}} -->
                            @foreach($media as $medias)
                            <tr class="text-center">
                                <td>{{$s_no}}</td>
                                <td>{{$medias->media_name}}</td>
                                <td><img src="{{$medias->media_image}}" style="width: 40px;"></td>
                                 <td>
                                    <i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="modal" data-target="#edit{{$medias->id}}"></i>
                                    <i class="fa fa-trash-o" aria-hidden="true" data-toggle="modal" data-target="#delete{{$medias->id}}"></i>
                                    
                                </td>
                            </tr>
                            <!-- {{$s_no++}} -->
                            @endforeach
                            @else
                            
                            <tr>
                                <td colspan="5" class="text-center">No Media to Show</td>
                            </tr>
                            @endif
                        </tbody>
                        <thead>
                            <tr class="text-center">
                                  <th>S.No</th>
                                <th>Media Name</th>
                                <th>Media Image</th>
                               
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
        <h4 class="modal-title">Add New Social Media</h4>

        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

     <form class="modal-form" method="POST" action="{{ route('media.add') }}" enctype="multipart/form-data">
      <!-- Modal body -->
      <div class="modal-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}">

                <div class="form-group">
                    <label for="name">Media Name:</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Media Link" name="media_name" required>
                </div>
                <div class="form-group">
                    <label for="name">Media Link:</label>
                    <input type="text" class="form-control" id="menu_link" placeholder="Enter Media Link" name="media_link">
                </div>
               <div class="form-group">
                    <label for="name">Media Image:</label>
                    <input type="file" class="form-control" id="menu_name" placeholder="Select Icon" name="media_image" required>
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
@foreach($media as $medias1)
<div class="modal" id="edit{{$medias1->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Social Media</h4>
                
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
     <form class="modal-form" method="POST" action="{{ route('media.edit') }}" enctype="multipart/form-data">
      <!-- Modal body -->
      <div class="modal-body">
            
               <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$medias1->id}}">
                 <div class="form-group">
                    <label for="name">Media Name:</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Select Media Name" name="media_name" value="{{$medias1->media_name}}" required="">
                </div>
                <div class="form-group">
                    <label for="name">Media Link:</label>
                    <input type="text" class="form-control" id="menu_link" placeholder="Select Media Name" name="media_link" value="{{$medias1->media_link}}">
                </div>
               <div class="form-group">
                    <label for="name">Media Image:</label>
                    <input type="file" class="form-control" id="menu_name" placeholder="Select  Icon" name="media_image" value="">
                    <img src="{{$medias->media_image}}" style="width: 40px;">
                    
                    <input type="hidden" name="media_old" value="{{$medias->media_image}}">

                </div>
               
                   
                
              
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>                
    </form>
   
   
    </div>
  </div>
</div>
@endforeach

<!-- Delete Page Modal -->

@foreach($media as $medias2)
<div class="modal" id="delete{{$medias2->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

         <!-- Modal body -->
         <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
             <h4 class="modal-title">You want to delete {{$medias2->media}} media</h4>
        </div>
        <div class="modal-footer">
            <form class="modal-form" method="POST" action="{{ route('media.delete') }}" >
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$medias2->id}}">
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
