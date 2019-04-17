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
            <button type="button" class="btn btn-primary page-add-btn-modal" data-toggle="modal" data-target="#add">
                <i class="fa fa-plus" aria-hidden="true"></i> Add New Menu item
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
            <h2 class="page-title">Menu</h2>
            
            <div class="card">
                <div class="card-header">
                    <ul>
                        <li>Display menu options in any available language.</li>
                        <li>Add Multi-lingual text to the Menu options from this page.</li>
                        <li>To Add a new language, Click on the <strong>+Add a New Language</strong> enter the respective text in multiple languages available.
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <table class="table table-striped table-bordered" id="example">
                        <thead>
                            <tr class="text-center">
                                <th>S.No</th>
                                <th>English</th>
                                <th>French</th>
                                <th>Spanish</th>
                                <th>Arabic</th>
                                <th>German</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($menu)
                            <!-- {{$s_no=1}} -->
                            @foreach($menu as $menus)
                            <tr class="text-center">
                                <td>{{$s_no}}</td>
                                <td>{{$menus->menu_en}}</td>
                                <td>{{$menus->menu_fn}}</td>
                                <td>{{$menus->menu_sp}}</td>
                                <td>{{$menus->menu_ab}}</td>
                                <td>{{$menus->menu_gn}}</td>
                                <td>@if($menus->status == 1)Active @else InActive @endif</td>
                                
                                
                                <td>
                                    <i class="fa fa-pencil-square-o" aria-hidden="true" data-toggle="modal" data-target="#edit{{$menus->id}}"></i>
                                    <i class="fa fa-trash-o" aria-hidden="true" data-toggle="modal" data-target="#delete{{$menus->id}}"></i>
                                    
                                </td>
                            </tr>
                            <!-- {{$s_no++}} -->
                            @endforeach
                            @else
                            <tr>
                                <td colspan="5" class="text-center">No Menu to Show</td>
                            </tr>
                            @endif
                        </tbody>
                        <thead>
                            <tr class="text-center">
                                <th>S.No</th>
                                <th>English</th>
                                <th>French</th>
                                <th>Spanish</th>
                                <th>Arabic</th>
                                <th>German</th>
                                <th>Status</th>
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
        <h4 class="modal-title">Add New Menu</h4>

        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

     <form class="modal-form" method="POST" action="{{ route('menu.add') }}" >
      <!-- Modal body -->
      <div class="modal-body">
              <input type="hidden" name="_token" value="{{csrf_token()}}">

                <div class="form-group">
                    <label for="name">Menu({{$language[0]->language_symbol}}):</label>
                    <input type="text" class="form-control menu_first" id="menu_name" placeholder="Enter Menu English" name="menu_en" required>
                </div>
               <div class="form-group">
                    <label for="name">Menu({{$language[1]->language_symbol}}):</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Menu French" name="menu_fn" required>
                </div>
                   <div class="form-group">
                    <label for="name">Menu({{$language[2]->language_symbol}}):</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Menu Spanish" name="menu_sp" required>
                </div>

                   <div class="form-group">
                    <label for="name">Menu({{$language[3]->language_symbol}}):</label>

                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Menu Arabic" name="menu_ab" required>
                </div>
                   <div class="form-group">
                    <label for="name">Menu({{$language[4]->language_symbol}}):</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Menu German" name="menu_gn" required>
                </div>
                  <div class="form-group">
                    <label for="name">Menu_key:</label>
                    <input type="text" class="form-control" id="key" placeholder="Enter Menu Key" name="menu_key" required readonly="">
                </div>
                <div class="form-group">
                    <label for="status">Menu Status:</label>
                    <input id="toggle-event1" name="status" checked  type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                  
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
@foreach($menu as $edit_menu)
<div class="modal" id="edit{{$edit_menu->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Menu</h4>
                
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form class="modal-form" method="POST" action="{{ route('menu.edit') }}" >
      <!-- Modal body -->
      <div class="modal-body">
            
               <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$edit_menu->id}}">
                <div class="form-group">
                    <label for="name">Menu({{$language[0]->language_symbol}}):</label>
                    <input type="text" class="form-control" id="page_name" placeholder="Enter Menu English" name="menu_en" value="{{$edit_menu->menu_en}}" >
                </div>
               <div class="form-group">
                    <label for="name">Menu({{$language[1]->language_symbol}}):</label>
                    <input type="text" class="form-control" id="page_name" placeholder="Enter Menu French" name="menu_fn" value="{{$edit_menu->menu_fn}}">
                </div>
                   <div class="form-group">
                    <label for="name">Menu({{$language[2]->language_symbol}}):</label>
                    <input type="text" class="form-control" id="page_name" placeholder="Enter Menu Spanish" name="menu_sp" value="{{$edit_menu->menu_sp}}" >
                </div>
                   <div class="form-group">
                    <label for="name">Menu({{$language[3]->language_symbol}}):</label>
                    <input type="text" class="form-control" id="page_name" placeholder="Enter Menu Arabic" name="menu_ab" value="{{$edit_menu->menu_ab}}" >
                </div>
                   <div class="form-group">
                    <label for="name">Menu({{$language[4]->language_symbol}}):</label>
                    <input type="text" class="form-control" id="page_name" placeholder="Enter Menu German" name="menu_gn" value="{{$edit_menu->menu_gn}}" >
                </div>
                <div class="form-group">
                    <label for="name">Menu_key:</label>
                    <input type="text" class="form-control" id="page_name" placeholder="Enter Menu Key" name="menu_key" value="{{$edit_menu->menu_key}}" readonly>
                </div>
                <div class="form-group">
                    <label for="status">Menu Status:</label>
                    
                    <input id="toggle-event" name="status" type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger" @if($edit_menu->status == 1) checked @else @endif>
                    

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

@foreach($menu as $menus2)
<div class="modal" id="delete{{$menus2->id}}">
  <div class="modal-dialog">
    <div class="modal-content">

         <!-- Modal body -->
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">You want to delete {{$menus2->menu_name}} Menu</h4>
        </div>
        <div class="modal-footer">
            <form class="modal-form" method="POST" action="{{ route('menu.delete') }}" >
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$menus2->id}}">
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

       $(".menu_first").keyup(function(){
          var string = $(this).val();
          $("#key").val(string.split(' ').join('_'));
        });



    $('#toggle-event').change(function() {
      
      if ($(this).prop('checked')) {
            $('#menu_status').val('1');
      }else{
            $('#menu_status').val('0');
      }
    })

    $('#toggle-event1').change(function() {
      
      if ($(this).prop('checked')) {
            $('#menu_status1').val('1');
      }else{
            $('#menu_status1').val('0');
      }
    })
  })
</script>
@endsection
