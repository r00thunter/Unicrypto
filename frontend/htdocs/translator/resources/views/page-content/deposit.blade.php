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
.step2,.step3,.step4,.step5,.step6,.step7,.step8,.step9,.step10,.step11,.step12,.step13 {
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
#prevBtn2,#prevBtn3,#prevBtn4,#prevBtn5,#prevBtn6,#prevBtn7,#prevBtn8,#prevBtn9,#prevBtn10,#prevBtn11,#prevBtn12,#prevBtn13 {
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
#nextBtn1,#nextBtn2,#nextBtn3,#nextBtn4,#nextBtn5,#nextBtn6,#nextBtn7,#nextBtn8,#nextBtn9,#nextBtn10,#nextBtn11,#nextBtn12 {
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
  <br>
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
            <br><br><br><h4 class="page-title">Deposit</h4><br>
             <div class="card-header">
                    <ul>
                        <li>Add the respective text in multiple languages for all the available elements of the page.</li>
                        <li>Different languages are denoted by the Symbol (ES, FR ) mentioned in the languages section.</li>
                        <li>To add a new language, go to the language section.
                        </li>
                    </ul>
                </div>
                <br>
            
      <!-- Modal body -->
      @if($page_content_count<1)
     <form class="modal-form" method="POST" action="{{ route('deposit.content.store') }}" >
      @else
                <form class="modal-form" method="POST" id="orders" action="{{ route('deposit.content.edit') }}" enctype="multipart/form-data">
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
            <input type="hidden" name="page_content_id13" value="{{$page_content[13]->id}}">
            <input type="hidden" name="page_content_id14" value="{{$page_content[14]->id}}">
            <input type="hidden" name="page_content_id15" value="{{$page_content[15]->id}}">
            <input type="hidden" name="page_content_id16" value="{{$page_content[16]->id}}">
            <input type="hidden" name="page_content_id17" value="{{$page_content[17]->id}}">
            <input type="hidden" name="page_content_id18" value="{{$page_content[18]->id}}">
            <input type="hidden" name="page_content_id19" value="{{$page_content[19]->id}}">
            <input type="hidden" name="page_content_id20" value="{{$page_content[20]->id}}">
            <input type="hidden" name="page_content_id21" value="{{$page_content[21]->id}}">
            <input type="hidden" name="page_content_id22" value="{{$page_content[22]->id}}">
            <input type="hidden" name="page_content_id23" value="{{$page_content[23]->id}}">
            <input type="hidden" name="page_content_id24" value="{{$page_content[24]->id}}">
            @endif
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="page_id" value="{{$page_id}}">
                 

            <fieldset class="step1">
                 <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>Heading</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[0]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Heading ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_heading_key"  value="@if($pges_content){{$pges_content->deposit_heading_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_heading_key" required value="deposit_heading_key">
                  
                  
                </div>
                <div class="col-md-6">
                    <h6> Sub-Heading</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[1]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_sub_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Sub Heading ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_sub_heading_key"  value="@if($pges_content){{$pges_content->deposit_sub_heading_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_sub_heading_key" required value="deposit_sub_heading_key">

                                    
                </div>
              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                      <!-- <button type="button" id="prevBtn1">Previous</button> -->
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
                       <h6> Header Button</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[2]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_heading_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Header Button ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_heading_button_key"  value="@if($pges_content){{$pges_content->deposit_heading_button_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_heading_button_key" required value="deposit_heading_button_key" >
                </div>


                <div class="col-md-6">
                    <h6>Your Fiat Currency Transactions</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[3]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_fiat_currency_trans_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Your Fiat Currency Transactions ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_fiat_currency_trans_key"  value="@if($pges_content){{$pges_content->deposit_fiat_currency_trans_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="deposit_fiat_currency_trans_key" required value="deposit_fiat_currency_trans_key">
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
                       <h6> Fiat Currency Modal Head</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[4]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_fiat_currency_trans_modal_head_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Fiat Currency Modal Head ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_fiat_currency_trans_modal_head_key"  value="@if($pges_content){{$pges_content->deposit_fiat_currency_trans_modal_head_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_fiat_currency_trans_modal_head_key" required value="deposit_fiat_currency_trans_modal_head_key" >
                </div>


                <div class="col-md-6">
                    <h6>Modal Hear you can</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[5]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_fiat_currency_trans_modal_here_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Modal Hear you can ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_fiat_currency_trans_modal_here_key"  value="@if($pges_content){{$pges_content->deposit_fiat_currency_trans_modal_here_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="deposit_fiat_currency_trans_modal_here_key" required value="deposit_fiat_currency_trans_modal_here_key">
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
                       <h6> Modal content li1</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[6]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_fiat_currency_trans_modal_li1_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Modal content li1 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_fiat_currency_trans_modal_li1_key"  value="@if($pges_content){{$pges_content->deposit_fiat_currency_trans_modal_li1_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_fiat_currency_trans_modal_li1_key" required value="deposit_fiat_currency_trans_modal_li1_key" >
                </div>


                <div class="col-md-6">
                    <h6>Modal content li2</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[7]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_fiat_currency_trans_modal_li2_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Modal content li2 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_fiat_currency_trans_modal_li2_key"  value="@if($pges_content){{$pges_content->deposit_fiat_currency_trans_modal_li2_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="deposit_fiat_currency_trans_modal_li2_key" required value="deposit_fiat_currency_trans_modal_li2_key">
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
                       <h6> Modal content li3</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[8]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_fiat_currency_trans_modal_li3_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Modal content li3 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_fiat_currency_trans_modal_li3_key"  value="@if($pges_content){{$pges_content->deposit_fiat_currency_trans_modal_li3_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_fiat_currency_trans_modal_li3_key" required value="deposit_fiat_currency_trans_modal_li3_key" >
                </div>


                <div class="col-md-6">
                    <h6>Transaction Id</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[9]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_transaction_id_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Transaction Id ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_transaction_id_key"  value="@if($pges_content){{$pges_content->deposit_transaction_id_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="deposit_transaction_id_key" required value="deposit_transaction_id_key">
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
                    <h6>Bank Name</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[10]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_bank_name_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Bank Name ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_bank_name_key"  value="@if($pges_content){{$pges_content->deposit_bank_name_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="deposit_bank_name_key" required value="deposit_bank_name_key">
                </div>

                <div class="col-md-6">
                       <h6> Amount</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[11]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_amount_key"  value="@if($pges_content){{$pges_content->deposit_amount_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_amount_key" required value="deposit_amount_key" >
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
                       <h6> Add</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[12]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_add_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Add ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_add_key"  value="@if($pges_content){{$pges_content->deposit_add_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_add_key" required value="deposit_add_key" >
                </div>

              

                <div class="col-md-6">
                       <h6> Fiat Wallet</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[13]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_fiat_wallet_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Fiat Wallet ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_fiat_wallet_key"  value="@if($pges_content){{$pges_content->deposit_fiat_wallet_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_fiat_wallet_key" required value="deposit_fiat_wallet_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn7">Previous</button>
                        <button type="button" id="nextBtn7">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step8">
          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Wallet</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[14]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_wallet_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Wallet ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_wallet_key"  value="@if($pges_content){{$pges_content->deposit_wallet_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_wallet_key" required value="deposit_wallet_key" >
                </div>

                <div class="col-md-6">
                       <h6> History</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[15]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_table_history_head_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">History ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_table_history_head_key"  value="@if($pges_content){{$pges_content->deposit_table_history_head_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_table_history_head_key" required value="deposit_table_history_head_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn8">Previous</button>
                        <button type="button" id="nextBtn8">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step9">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Modal Head Deposits</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[16]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_table_history_modal_head_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Modal Head Deposits ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_table_history_modal_head_key"  value="@if($pges_content){{$pges_content->deposit_table_history_modal_head_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_table_history_modal_head_key" required value="deposit_table_history_modal_head_key" >
                </div>

                <div class="col-md-6">
                       <h6> Modal Head Content</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[17]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_table_history_modal_content_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Modal Head Content ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_table_history_modal_content_key"  value="@if($pges_content){{$pges_content->deposit_table_history_modal_content_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_table_history_modal_content_key" required value="deposit_table_history_modal_content_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn9">Previous</button>
                        <button type="button" id="nextBtn9">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step10">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Table Head Date</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[18]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_table_history_table_th_date_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Table Head Date ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_table_history_table_th_date_key"  value="@if($pges_content){{$pges_content->deposit_table_history_table_th_date_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_table_history_table_th_date_key" required value="deposit_table_history_table_th_date_key" >
                </div>

                <div class="col-md-6">
                       <h6> Table Head Type</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[19]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_table_history_table_th_type_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Table Head Type ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_table_history_table_th_type_key"  value="@if($pges_content){{$pges_content->deposit_table_history_table_th_type_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_table_history_table_th_type_key" required value="deposit_table_history_table_th_type_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn10">Previous</button>
                        <button type="button" id="nextBtn10">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>


        <fieldset class="step11">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Table Head Coin</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[20]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_table_history_table_th_coin_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Table Head Coin ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_table_history_table_th_coin_key"  value="@if($pges_content){{$pges_content->deposit_table_history_table_th_coin_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_table_history_table_th_coin_key" required value="deposit_table_history_table_th_coin_key" >
                </div>

                <div class="col-md-6">
                       <h6> Table Head Amount</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[21]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_table_history_table_th_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Table Head Amount ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_table_history_table_th_amount_key"  value="@if($pges_content){{$pges_content->deposit_table_history_table_th_amount_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_table_history_table_th_amount_key" required value="deposit_table_history_table_th_amount_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn11">Previous</button>
                        <button type="button" id="nextBtn11">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step12">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Table Head Status</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[22]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_table_history_table_th_status_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Table Head Status ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_table_history_table_th_status_key"  value="@if($pges_content){{$pges_content->deposit_table_history_table_th_status_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_table_history_table_th_status_key" required value="deposit_table_history_table_th_status_key" >
                </div>

                <div class="col-md-6">
                       <h6> Table Head Manage Accounts</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[23]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_button_manage_account_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Table Head Manage Accounts ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_button_manage_account_key"  value="@if($pges_content){{$pges_content->deposit_button_manage_account_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                                
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_button_manage_account_key" required value="deposit_button_manage_account_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn12">Previous</button>
                        <button type="button" id="nextBtn12">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step13">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                   
                <div class="col-md-6">
                       <h6> Button Withdraw</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[24]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->deposit_button_withdraw_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Button Withdraw({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="deposit_{{$languagevlue->language_symbol}}_button_withdraw_key"  value="@if($pges_content){{$pges_content->deposit_button_withdraw_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="deposit_button_withdraw_key" required value="deposit_button_withdraw_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn13">Previous</button>
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
$("#nextBtn7").click(function(){
    $('.step8').css('display','block');
    $('.step7').css('display','none');
})
$("#nextBtn8").click(function(){
    $('.step9').css('display','block');
    $('.step8').css('display','none');
})
$("#nextBtn9").click(function(){
    $('.step10').css('display','block');
    $('.step9').css('display','none');
})
$("#nextBtn10").click(function(){
    $('.step11').css('display','block');
    $('.step10').css('display','none');
})
$("#nextBtn11").click(function(){
    $('.step12').css('display','block');
    $('.step11').css('display','none');
})
$("#nextBtn12").click(function(){
    $('.step13').css('display','block');
    $('.step12').css('display','none');
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
$("#prevBtn8").click(function(){
    $('.step7').css('display','block');
    $('.step8').css('display','none');
})
$("#prevBtn9").click(function(){
    $('.step8').css('display','block');
    $('.step9').css('display','none');
})
$("#prevBtn10").click(function(){
    $('.step9').css('display','block');
    $('.step10').css('display','none');
})
$("#prevBtn11").click(function(){
    $('.step10').css('display','block');
    $('.step11').css('display','none');
})
$("#prevBtn12").click(function(){
    $('.step11').css('display','block');
    $('.step12').css('display','none');
})
$("#prevBtn13").click(function(){
    $('.step12').css('display','block');
    $('.step13').css('display','none');
})
</script>

@endsection
