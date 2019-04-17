@extends('layouts.app')

@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap2-toggle.min.css" rel="stylesheet">

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap2-toggle.min.js">
  
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
/* Hide all steps by default: */
.step2,.step3,.step4 {
  display: none;
}

.tab {
    position: relative;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.25rem;
    padding: 20px;
}
button:focus {
    outline: 1px dotted;
    outline: 0px auto -webkit-focus-ring-color !important;
}
#prevBtn2,#prevBtn3,#prevBtn4 {
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
#nextBtn1,#nextBtn2,#nextBtn3 {
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
        <br><br><br>
            <h4 class="page-title">Forgot Password</h4>
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
             <form class="modal-form" method="POST" id="orders" action="{{ route('forgot.pass.content.store') }}" enctype="multipart/form-data">
            @else
                <form class="modal-form" method="POST" id="orders" action="{{ route('forgot.pass.content.edit') }}" enctype="multipart/form-data">
            <input type="hidden" name="page_content_id" value="{{$page_content[0]->id}}">
            <input type="hidden" name="page_content_id1" value="{{$page_content[1]->id}}">
            <input type="hidden" name="page_content_id2" value="{{$page_content[2]->id}}">
            <input type="hidden" name="page_content_id3" value="{{$page_content[3]->id}}">
            <input type="hidden" name="page_content_id4" value="{{$page_content[4]->id}}">
            <input type="hidden" name="page_content_id5" value="{{$page_content[5]->id}}">
            <input type="hidden" name="page_content_id6" value="{{$page_content[6]->id}}">
            @endif
      <!-- Modal body -->
                       <input type="hidden" name="_token" value="{{csrf_token()}}">
               
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
                            if (isset($pge_content->$symbol->forgot_pass_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Heading({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="forgot_{{$languagevlue->language_symbol}}_pass_heading_key"  value="@if($pges_content){{$pges_content->forgot_pass_heading_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="forgot_pass_heading_key" required value="forgot_pass_heading_key">
                </div>
                <div class="col-md-6">
                        <h6> Email</h6>
                    
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[1]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->forgot_pass_placeholder_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Email ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="forgot_{{$languagevlue->language_symbol}}_pass_placeholder_key"  value="@if($pges_content){{$pges_content->forgot_pass_placeholder_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="forgot_pass_placeholder_key" required value="forgot_pass_placeholder_key">
                </div>
              </div>

               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <!-- <button type="button" id="prevBtn3">Previous</button> -->
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
                    <div class="col-md-6">
                      <h6> Button-content</h6>
                    
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[2]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->forgot_pass_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Button Content({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="forgot_{{$languagevlue->language_symbol}}_pass_button_key"  value="@if($pges_content){{$pges_content->forgot_pass_button_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="forgot_pass_button_key" required value="forgot_pass_button_key">
                </div>
                <div class="col-md-6">
                    
                   <h6>Dont Have Account</h6>
                    
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[3]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->forgot_pass_account_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Dont Have Account({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="forgot_{{$languagevlue->language_symbol}}_pass_account_key"  value="@if($pges_content){{$pges_content->forgot_pass_account_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="forgot_pass_account_key" required value="forgot_pass_account_key"> 
                </div>
              </div>

               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn2">Previous</button>
                <button type="button" id="nextBtn2">Next</button>
             </div>
          </div>
      </div>
            </div>
          </div>
            </fieldset>

            <fieldset class="step3">
        
         <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6> Register</h6>
                    
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[4]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->forgot_pass_register_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Register ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="forgot_{{$languagevlue->language_symbol}}_pass_register_key"  value="@if($pges_content){{$pges_content->forgot_pass_register_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="forgot_pass_register_key" required value="forgot_pass_register_key">
                </div>
                <div class="col-md-6">
                  <h6>Account already Register</h6>
                    
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[5]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->login_already_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Account already Register ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="login_{{$languagevlue->language_symbol}}_already_key"  value="@if($pges_content){{$pges_content->login_already_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="login_already_key" required value="login_already_key">
                </div>
              </div>

               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn3">Previous</button>
                <button type="button" id="nextBtn3">Next</button>
             </div>
          </div>
      </div>
            </div>
          </div>
            </fieldset>

            <fieldset class="step4">
        <div class="tab">
               
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>login</h6>
                    
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[6]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->login_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Login ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="login_{{$languagevlue->language_symbol}}_heading_key"  value="@if($pges_content){{$pges_content->login_heading_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="login_heading_key" required value="login_heading_key">
                  
   
                </div>
               
              </div>

               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn4">Previous</button>
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



<script>
  $("#nextBtn1").click(function(){
    $('.step2').css('display','block');
    $('.step1').css('display','none');
})
$("#nextBtn2").click(function(){
    $('.step3').css('display','block');
    $('.step2').css('display','none');
})
$("#nextBtn3").click(function(){
    $('.step4').css('display','block');
    $('.step3').css('display','none');
})
$("#prevBtn2").click(function(){
    $('.step1').css('display','block');
    $('.step2').css('display','none');
})
$("#prevBtn3").click(function(){
    $('.step2').css('display','block');
    $('.step3').css('display','none');
})
$("#prevBtn4").click(function(){
    $('.step3').css('display','block');
    $('.step4').css('display','none');
})
</script>

@endsection
