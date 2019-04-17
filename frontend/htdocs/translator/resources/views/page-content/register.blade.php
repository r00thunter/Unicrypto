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
            <br><br><br><h4 class="page-title">Register</h4>
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
                <form class="modal-form" method="POST" id="orders" action="{{ route('register.content.store') }}" enctype="multipart/form-data">
            @else
                <form class="modal-form" method="POST" id="orders" action="{{ route('register.content.edit') }}" enctype="multipart/form-data">
            <input type="hidden" name="page_content_id" value="{{$page_content[0]->id}}">
            <input type="hidden" name="page_content_id1" value="{{$page_content[1]->id}}">
            <input type="hidden" name="page_content_id2" value="{{$page_content[2]->id}}">
            <input type="hidden" name="page_content_id3" value="{{$page_content[3]->id}}">
            <input type="hidden" name="page_content_id4" value="{{$page_content[4]->id}}">
            <input type="hidden" name="page_content_id5" value="{{$page_content[5]->id}}">
            <input type="hidden" name="page_content_id6" value="{{$page_content[6]->id}}">
            <input type="hidden" name="page_content_id7" value="{{$page_content[7]->id}}">
            <input type="hidden" name="page_content_id8" value="{{$page_content[8]->id}}">
            <input type="hidden" name="page_content_id9" value="{{$page_content[9]->id}}">
            <input type="hidden" name="page_content_id10" value="{{$page_content[10]->id}}">
            <input type="hidden" name="page_content_id11" value="{{$page_content[11]->id}}">
            <input type="hidden" name="page_content_id12" value="{{$page_content[12]->id}}">
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
                            if (isset($pge_content->$symbol->register_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Heading({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_heading_key"  value="@if($pges_content){{$pges_content->register_heading_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Heading" name="register_heading_key" required value="register_heading_key">
                </div>
                <div class="col-md-6">
                       <h6> Sub-Heading</h6>

                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[1]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_sub_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Sub Heading ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_sub_heading_key"  value="@if($pges_content){{$pges_content->register_sub_heading_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Sub Heading" name="register_sub_heading_key" required value="register_sub_heading_key">
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
                    <div class="col-md-6">
                      <h6> Button-content</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[2]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Button content ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_button_key"  value="@if($pges_content){{$pges_content->register_button_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Button Content" name="register_button_key" required value="register_button_key">
                </div>
                    <div class="col-md-6">
                      <h6>First Name</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[3]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_firstname_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">First Name({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_firstname_key"  value="@if($pges_content){{$pges_content->register_firstname_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="register_firstname_key" required value="register_firstname_key">
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
                      <h6> Last Name</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[4]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_lastname_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name"> Last Name ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_lastname_key"  value="@if($pges_content){{$pges_content->register_lastname_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                  <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="register_lastname_key" required value="register_lastname_key">
                </div>
                    <div class="col-md-6">
                      <h6> Email Id</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[5]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_email_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Email Id ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_email_key"  value="@if($pges_content){{$pges_content->register_email_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Email" name="register_email_key" required value="register_email_key">
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
                    <h6> Phone</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[6]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_phone_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Phone ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_phone_key"  value="@if($pges_content){{$pges_content->register_phone_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="register_phone_key" required value="register_phone_key">
                </div>
                    <div class="col-md-6">
                      <h6>Accept</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[7]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_accept_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Accept ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_accept_key"  value="@if($pges_content){{$pges_content->register_accept_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="register_accept_key" required value="register_accept_key">
                </div>
              </div>
               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn4">Previous</button>
                <button type="button" id="nextBtn4">Next</button>
             </div>
          </div>
      </div>
            </div>
          </div>
           </fieldset>
       
        <fieldset class="step5">
       
        <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>Terms and Condition</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[8]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_terms_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Terms and Condition ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_terms_key"  value="@if($pges_content){{$pges_content->register_terms_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Referal" name="register_terms_key" required value="register_terms_key">
                </div>

                    <div class="col-md-6">
                      <h6>Use of this Site</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[9]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_use_site_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Use of this Site ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_use_site_key"  value="@if($pges_content){{$pges_content->register_use_site_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="register_use_site_key" required value="register_use_site_key">
                </div>
                </div>
               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn5">Previous</button>
                <button type="button" id="nextBtn5">Next</button>
             </div>
          </div>
      </div>
              </div>
            </div>

          </fieldset>
       
        <fieldset class="step6">
       
        <div class="tab">
                   
                <div class="form-group">
                  <div class="row">

                <div class="col-md-6">
                     <h6>Account already Register</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[10]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_already_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Account already Register ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_already_key"  value="@if($pges_content){{$pges_content->register_already_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="register_already_key" required value="register_already_key">
                </div>
                    <div class="col-md-6">
                      <h6>Login</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[11]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_login_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Login ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_login_key"  value="@if($pges_content){{$pges_content->register_login_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="register_login_key" required value="register_login_key">
                </div>
                </div>
               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn6">Previous</button>
                <button type="button" id="nextBtn6">Next</button>
             </div>
          </div>
      </div>
              </div>
            </div>

          </fieldset>
       
        <fieldset class="step7">
       
        <div class="tab">
                   
                <div class="form-group">
                  <div class="row">

                    <div class="col-md-6">
                      <h6>Referal</h6>
                     <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[12]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->register_referal_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Referal ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="register_{{$languagevlue->language_symbol}}_referal_key"  value="@if($pges_content){{$pges_content->register_referal_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="register_referal_key" required value="register_referal_key">
                </div>
                </div>
               <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn7">Previous</button>
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
$("#nextBtn4").click(function(){
    $('.step5').css('display','block');
    $('.step4').css('display','none');
})
$("#nextBtn5").click(function(){
    $('.step6').css('display','block');
    $('.step5').css('display','none');
})
$("#nextBtn6").click(function(){
    $('.step7').css('display','block');
    $('.step6').css('display','none');
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
$("#prevBtn5").click(function(){
    $('.step4').css('display','block');
    $('.step5').css('display','none');
})
$("#prevBtn6").click(function(){
    $('.step5').css('display','block');
    $('.step6').css('display','none');
})
$("#prevBtn7").click(function(){
    $('.step6').css('display','block');
    $('.step7').css('display','none');
})
</script>

@endsection
