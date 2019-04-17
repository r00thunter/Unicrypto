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
            
            @if(session()->has('success'))<br><br>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session()->get('success') }}
            </div>
        @endif
        @if(session()->has('error'))<br><br>
            <div class="alert alert-error alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session()->get('error') }}
            </div>
        @endif
            <br><br><br><h4 class="page-title">API</h4>
             <div class="card-header">
                    <ul>
                        <li>Add the respective text in multiple languages for all the available elements of the page.</li>
                        <li>Different languages are denoted by the Symbol (ES, FR ) mentioned in the languages section.</li>
                        <li>To add a new language, go to the language section.
                        </li>
                    </ul>
                </div>
                <br>
             <input type="hidden" class="form-control" id="page_content_count" value="{{$language_count}}">
            @if($page_content_count<1)
                <form class="modal-form" method="POST" id="orders" action="{{ route('api.content.store') }}" enctype="multipart/form-data">
            @else
                <form class="modal-form" method="POST" id="orders" action="{{ route('api.content.edit') }}" enctype="multipart/form-data">
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
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[0]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->api_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Heading ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="api_{{$languagevlue->language_symbol}}_heading_key"  value="@if($pges_content){{$pges_content->api_heading_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Heading" name="api_heading_key" required value="api_heading_key">
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
                      <h6> Page Content</h6>
                         
                  
                    <!-- {{$title = 1}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[1]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->api_content_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content ({{$languagevlue->language_symbol}})</label>
                         <textarea name="api_{{$languagevlue->language_symbol}}_content_key" class="form-control" id="content{{$title}}">@if($pges_content){{$pges_content->api_content_key}}@endif</textarea>
                        
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Heading" name="api_content_key" required value="api_content_key">

                        
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
        var page_content_count = $('#page_content_count').val();
      console.log(page_content_count);
      for(var row=1;row <= page_content_count;row++){
        console.log(row);
        CKEDITOR.replace('content'+row);
      
      }
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
