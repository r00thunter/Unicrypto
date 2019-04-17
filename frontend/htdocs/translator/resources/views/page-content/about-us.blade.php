@extends('layouts.app')

@section('content')

  
</script>
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
button:focus {
    outline: 1px dotted;
    outline: 0px auto -webkit-focus-ring-color;
}
/* Hide all steps by default: */
.step2,.step3,.step4,.step5,.step6,.step7 {
  display: none;
}

#prevBtn2,#prevBtn3,#prevBtn4,#prevBtn5,#prevBtn6,#prevBtn7 {
    width: 100px;
    background: #C5C5F1;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 5px 5px;
    margin: 10px 5px;
}
#nextBtn1,#nextBtn2,#nextBtn3,#nextBtn4,#nextBtn5,#nextBtn6 {
    width: 100px;
    background: #2f3340;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 5px 5px;
    margin: 10px 5px;
}
#btnsubmit {
    width: 100px;
    background: #128e47;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 5px 5px;
    margin: 10px 5px;
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
            <br><br><h4 class="page-title">About Us</h4>
             <div class="card-header">
                    <ul>
                        <li>Add the respective text in multiple languages for all the available elements of the page.</li>
                        <li>Different languages are denoted by the Symbol (ES, FR ) mentioned in the languages section.</li>
                        <li>To add a new language, go to the language section.
                        </li>
                    </ul>
                </div>
                <br>
            @if($page_content_count<1)
                <form class="modal-form" method="POST" id="orders" action="{{ route('aboutus.content.store') }}" enctype="multipart/form-data">
            @else
                <form class="modal-form" method="POST" id="orders" action="{{ route('aboutus.content.edit') }}" enctype="multipart/form-data">
            <input type="hidden" name="page_content_id" value="{{$page_content[0]->id}}">
            <input type="hidden" name="page_content_id1" value="{{$page_content[1]->id}}">
            @endif
      <!-- Modal body -->
     
                <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" name="page_id" value="{{$page_id}}">
            <fieldset class="step1">
                 <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                    <h6>Heading</h6>
                    <label for="name">Title({{$language[0]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name"   placeholder="Enter Your Heading" name="about_us_en_heading" required value="@if($page_content[1]->en_page_content){{$page_content[1]->en_page_content}}@endif">
                    <label for="name">Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control"   id="menu_name" placeholder="Enter Your Heading" name="about_us_fn_heading" required value="@if($page_content[1]->fn_page_content){{$page_content[1]->fn_page_content}}@endif">
                    <label for="name">Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name"   placeholder="Enter Your Heading" name="about_us_sp_heading" required value="@if($page_content[1]->sp_page_content){{$page_content[1]->sp_page_content}}@endif">
                    <label for="name">Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name"   placeholder="Enter Your Heading" name="about_us_ab_heading" required value="@if($page_content[1]->ab_page_content){{$page_content[1]->ab_page_content}}@endif">
                
                    <label for="name">Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control"   id="menu_name" placeholder="Enter Your Heading" name="about_us_gn_heading" required value="@if($page_content[1]->gn_page_content){{$page_content[1]->gn_page_content}}@endif">
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Heading" name="about_us_heading_key" required value="about_us_heading_key">
                </div>
              </div>
               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                
                <button type="button" id="nextBtn1">Next</button>
             </div>
          </div>
      </div>
            </div>
          </div>
            
        </fieldset>
       <fieldset class="step2">      
        
         <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-12">
                      <h6> Button-content</h6>
                    <label for="name">Title({{$language[0]->language_symbol}})</label>
                     <textarea name="about_us_en_content" class="form-control" id="content">@if($page_content[0]->en_page_content){{$page_content[0]->en_page_content}}@endif</textarea>
                    <label for="name">Title({{$language[1]->language_symbol}})</label>
                    <textarea name="about_us_fn_content" class="form-control" id="content1">@if($page_content[0]->en_page_content){{$page_content[0]->en_page_content}}@endif</textarea>
                    <label for="name">Title({{$language[2]->language_symbol}})</label>
                     <textarea name="about_us_sp_content" class="form-control" id="content2">@if($page_content[0]->en_page_content){{$page_content[0]->en_page_content}}@endif</textarea>
                    <label for="name">Title({{$language[3]->language_symbol}})</label>
                     <textarea name="about_us_ab_content" class="form-control" id="content3">@if($page_content[0]->en_page_content){{$page_content[0]->en_page_content}}@endif</textarea>
                
                    <label for="name">Title({{$language[4]->language_symbol}})</label>
                     <textarea name="about_us_gn_content" class="form-control" id="content4">@if($page_content[0]->en_page_content){{$page_content[0]->en_page_content}}@endif</textarea>
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Heading" name="about_us_content_key" required value="about_us_content_key">

                        
                          <br>

                    </div>
                    
              </div>
               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn2">Previous</button>
                <button type="submit" id="btnsubmit">Submit</button>
             </div>
          </div>
      </div>
            </div>
          </div>
       </fieldset>     
        
     </form>
            
        </div>
    </div>
</div>

<!-- The Modal -->

<!-- Edit Page Modal -->


<!-- Delete Page Modal -->


<script src="{{ asset('public/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
<script src="{{{ asset('public/plugin/ckeditor/ckeditor.js') }}}" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
      // Replace the <textarea id="editor1"> with a CKEditor
      // instance, using default configuration.
        CKEDITOR.replace('content');
        CKEDITOR.replace('content1');
        CKEDITOR.replace('content2');
        CKEDITOR.replace('content3');
        CKEDITOR.replace('content4');
     });
     
     //Flat red color scheme for iCheck
        // $('input[type="radio"]').iCheck({
        //   radioClass: 'iradio_flat-red'
        // });
  </script>
<script>
 $("#nextBtn1").click(function(){
    $('.step2').css('display','block');
    $('.nav-min-width').css('height','2000px');
    $('.step1').css('display','none');
})
$("#prevBtn2").click(function(){
    $('.step1').css('display','block');
    $('.nav-min-width').css('height','100%');
    $('.step2').css('display','none');
})
</script>

@endsection
