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
.step2,.step3,.step4,.step5,.step6,.step7,.step8,.step9,.step10,.step11,.step12,.step13,.step14,.step15,.step16,.step17,.step18,.step19 {
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
#prevBtn2,#prevBtn3,#prevBtn4,#prevBtn5,#prevBtn6,#prevBtn7,#prevBtn8,#prevBtn9,#prevBtn10,#prevBtn11,#prevBtn12,#prevBtn13,#prevBtn14,#prevBtn15,#prevBtn16,#prevBtn17,#prevBtn18,#prevBtn19 {
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
#nextBtn1,#nextBtn2,#nextBtn3,#nextBtn4,#nextBtn5,#nextBtn6,#nextBtn7,#nextBtn8,#nextBtn9,#nextBtn10,#nextBtn11,#nextBtn12,#nextBtn13,#nextBtn14,#nextBtn15,#nextBtn16,#nextBtn17,#nextBtn18 {
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
            <br><br><br><h4 class="page-title">Crypto Wallet</h4><br>
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
     <form class="modal-form" method="POST" action="{{ route('crypto.wallet.content.store') }}" >
      @else
    <form class="modal-form" method="POST" id="orders" action="{{ route('crypto.wallet.content.edit') }}" enctype="multipart/form-data">
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
            <input type="hidden" name="page_content_id25" value="{{$page_content[25]->id}}">
            <input type="hidden" name="page_content_id26" value="{{$page_content[26]->id}}">
            <input type="hidden" name="page_content_id27" value="{{$page_content[27]->id}}">
            <input type="hidden" name="page_content_id28" value="{{$page_content[28]->id}}">
            <input type="hidden" name="page_content_id29" value="{{$page_content[29]->id}}">
            <input type="hidden" name="page_content_id30" value="{{$page_content[30]->id}}">
            <input type="hidden" name="page_content_id31" value="{{$page_content[31]->id}}">
            <input type="hidden" name="page_content_id32" value="{{$page_content[32]->id}}">
            <input type="hidden" name="page_content_id33" value="{{$page_content[33]->id}}">
            <input type="hidden" name="page_content_id34" value="{{$page_content[34]->id}}">
            <input type="hidden" name="page_content_id35" value="{{$page_content[35]->id}}">
            <input type="hidden" name="page_content_id36" value="{{$page_content[36]->id}}">
            @endif
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
                            if (isset($pge_content->$symbol->crypto_wallet_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Heading ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_heading_key"  value="@if($pges_content){{$pges_content->crypto_wallet_heading_key}} @else Crypto Wallet{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_heading_key" required value="crypto_wallet_heading_key">
                  
                  
                </div>
                <div class="col-md-6">
                    <h6> Sub Heading</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[1]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_sub_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Sub Heading ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_sub_heading_key"  value="@if($pges_content){{$pges_content->crypto_wallet_sub_heading_key}} @else Send / Receive Cryptocurrencies and also View Cryptocurrency Balances on the Exchange{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_sub_heading_key" required value="crypto_wallet_sub_heading_key">

                                    
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
                       <h6> Content in list</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[2]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_li_content1_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content in list ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_li_content1_key"  value="@if($pges_content){{$pges_content->crypto_wallet_li_content1_key}} @else If you are here for the first time, generate a cryptocurrency address for each cryptocurrency.{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_li_content1_key" required value="crypto_wallet_li_content1_key" >
                </div>


                <div class="col-md-6">
                    <h6>Content in list2</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[3]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_li_content2_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content in list2 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_li_content2_key"  value="@if($pges_content){{$pges_content->crypto_wallet_li_content2_key}} @else Click on Manage Crypto addresses to create/manage the addresses.{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="crypto_wallet_li_content2_key" required value="crypto_wallet_li_content2_key">
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
                       <h6> Content in list3</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[4]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_li_content3_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content in list3 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_li_content3_key"  value="@if($pges_content){{$pges_content->crypto_wallet_li_content3_key}} @else To Send Cryptocurrencies to other wallets, paste the recipients address in the Send to Address box{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_li_content3_key" required value="crypto_wallet_li_content3_key" >
                </div>


                <div class="col-md-6">
                    <h6>Content in list4</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[5]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_li_content4_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content in list4 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_li_content4_key"  value="@if($pges_content){{$pges_content->crypto_wallet_li_content4_key}} @else Receive Cryptos to your wallet by sharing the addresses displayed below{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="crypto_wallet_li_content4_key" required value="crypto_wallet_li_content4_key">
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
                       <h6> Send Cryptos</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[6]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_crypto_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send Cryptos ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_crypto_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_crypto_key}} @else Send Cryptos{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_crypto_key" required value="crypto_wallet_send_crypto_key" >
                </div>


                <div class="col-md-6">
                    <h6>Send Crypto Modal </h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[7]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_modal_content_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send Crypto Modal ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_modal_content_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_modal_content_key}} @else To send any supported Cryptocurrency{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="crypto_wallet_send_modal_content_key" required value="crypto_wallet_send_modal_content_key">
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
                       <h6> Send Crypto Modal Content Step1</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[8]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_modal_content_li1_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send Crypto Modal Content Step1t ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_modal_content_li1_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_modal_content_li1_key}} @else Step 1: Select the Cryptocurrency you'd like to send.(Wait for the page to reflect and display the balance of the selected currency){{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_modal_content_li1_key" required value="crypto_wallet_send_modal_content_li1_key" >
                </div>


                <div class="col-md-6">
                    <h6>Send Crypto Modal Content Step2</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[9]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_modal_content_li2_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send Crypto Modal Content Step2 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_modal_content_li2_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_modal_content_li2_key}} @else Step 2: Paste the Recipient's Cryptocurrency Wallet address in the Send to Address field.{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="crypto_wallet_send_modal_content_li2_key" required value="crypto_wallet_send_modal_content_li2_key">
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
                    <h6>Send Crypto Modal Content Step3</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[10]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_modal_content_li3_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send Crypto Modal Content Step3 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_modal_content_li3_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_modal_content_li3_key}} @else Step 3: Enter the number of cryptos to send and click send.{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="crypto_wallet_send_modal_content_li3_key" required value="crypto_wallet_send_modal_content_li3_key">
                </div>

                <div class="col-md-6">
                       <h6> Send Crypto Modal Content Notes</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[11]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_modal_content_li4_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send Crypto Modal Content Notes ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_modal_content_li4_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_modal_content_li4_key}} @else Note: A percentage of blockchain fee is taken by the network to process the transaction.{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_modal_content_li4_key" required value="crypto_wallet_send_modal_content_li4_key" >
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
                       <h6> Available</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[12]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_available_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Available ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_available_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_available_key}} @else Available{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_available_key" required value="crypto_wallet_send_available_key" >
                </div>

              

                <div class="col-md-6">
                       <h6> Withdraw Currency</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[13]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_withdraw_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Withdraw Currency ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_withdraw_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_withdraw_key}} @else Withdraw Currency{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_withdraw_key" required value="crypto_wallet_send_withdraw_key" >
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
                       <h6> Send to Address</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[14]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_to_address_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send to Address ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_to_address_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_to_address_key}} @else Send to Address{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_to_address_key" required value="crypto_wallet_send_to_address_key" >
                </div>

                <div class="col-md-6">
                       <h6> Amount to Send</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[15]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount to Send ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_amount_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_amount_key}} @else Amount to Send{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_amount_key" required value="crypto_wallet_send_amount_key" >
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
                       <h6> Blockchain Fee</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[16]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_blockchain_fee_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Blockchain Fee ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_blockchain_fee_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_blockchain_fee_key}} @else Blockchain Fee{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_blockchain_fee_key" required value="crypto_wallet_send_blockchain_fee_key" >
                </div>

                    <div class="col-md-6">
                       <h6> to Receive</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[17]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_receive_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">to Receive ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_receive_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_receive_key}} @else to Receive{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_receive_key" required value="crypto_wallet_send_receive_key" >
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
                       <h6> Send Crypto</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[18]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_send_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send Crypto ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_send_button_key"  value="@if($pges_content){{$pges_content->crypto_wallet_send_button_key}} @else Send Crypto{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_send_button_key" required value="crypto_wallet_send_button_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Receive Cryptos</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[19]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_receive_crypto_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Receive Cryptos ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_receive_crypto_key"  value="@if($pges_content){{$pges_content->crypto_wallet_receive_crypto_key}} @else Receive Cryptos{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_receive_crypto_key" required value="crypto_wallet_receive_crypto_key" >
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
                       <h6> Receive Cryptos Modal Step1</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[20]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_receive_modal_content_li1_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Receive Cryptos Modal Step1 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_receive_modal_content_li1_key"  value="@if($pges_content){{$pges_content->crypto_wallet_receive_modal_content_li1_key}} @else Step 1: Select BTC or LTC or ZEC from the drop down to display the addresses{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_receive_modal_content_li1_key" required value="crypto_wallet_receive_modal_content_li1_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Receive Cryptos Modal Step2</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[21]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_receive_modal_content_li2_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Receive Cryptos Modal Step2 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_receive_modal_content_li2_key"  value="@if($pges_content){{$pges_content->crypto_wallet_receive_modal_content_li2_key}} @else Step 2: Copy the address displayed in the Send to This Address and share it with the sender.{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_receive_modal_content_li2_key" required value="crypto_wallet_receive_modal_content_li2_key" >
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
                       <h6> Receive Cryptos Modal Notes</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[22]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_receive_modal_content_li3_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Receive Cryptos Modal Notes ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_receive_modal_content_li3_key"  value="@if($pges_content){{$pges_content->crypto_wallet_receive_modal_content_li3_key}} @else Note: If the QR code isn't getting displayed or appears broken, it means that you haven't created an address. You would have to create crypto addresses for each cryptocurrency separately.{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_receive_modal_content_li3_key" required value="crypto_wallet_receive_modal_content_li3_key" >
                </div>

                <div class="col-md-6">
                       <h6> Select Currency</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[23]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_receive_select_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Select Currency ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_receive_select_key"  value="@if($pges_content){{$pges_content->crypto_wallet_receive_select_key}} @else Select Currency{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_receive_select_key" required value="crypto_wallet_receive_select_key" >
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
                       <h6> Send to This Address</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[24]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_receive_send_address_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Send to This Address ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_receive_send_address_key"  value="@if($pges_content){{$pges_content->crypto_wallet_receive_send_address_key}} @else Send to This Address{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_receive_send_address_key" required value="crypto_wallet_receive_send_address_key" >
                </div>

                <div class="col-md-6">
                       <h6> Manage Crypto Address</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[25]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_receive_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Manage Crypto Address ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_receive_button_key"  value="@if($pges_content){{$pges_content->crypto_wallet_receive_button_key}} @else Manage Crypto Address{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_receive_button_key" required value="crypto_wallet_receive_button_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn13">Previous</button>
                        <button type="button" id="nextBtn13">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step14">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Wallet</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[26]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_currency_wallet_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Wallet ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_currency_wallet_key"  value="@if($pges_content){{$pges_content->crypto_wallet_currency_wallet_key}} @else Wallet{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_currency_wallet_key" required value="crypto_wallet_currency_wallet_key" >
                </div>

              

                <div class="col-md-6">
                       <h6> Recent Deposits</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[27]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_deposit_table_head_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Recent Deposits ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_deposit_table_head_key"  value="@if($pges_content){{$pges_content->crypto_wallet_deposit_table_head_key}} @else Recent Deposits{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_deposit_table_head_key" required value="crypto_wallet_deposit_table_head_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn14">Previous</button>
                        <button type="button" id="nextBtn14">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step15">
          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Recent Withdrawals</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[28]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_withdrawl_table_head_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Recent Withdrawals ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_withdrawl_table_head_key"  value="@if($pges_content){{$pges_content->crypto_wallet_withdrawl_table_head_key}} @else Recent Withdrawals{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_withdrawl_table_head_key" required value="crypto_wallet_withdrawl_table_head_key" >
                </div>

                <div class="col-md-6">
                       <h6> Deposit Table ID</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[29]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_recent_table_id_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Deposit Table ID ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_recent_table_id_key"  value="@if($pges_content){{$pges_content->crypto_wallet_recent_table_id_key}} @else ID{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_recent_table_id_key" required value="crypto_wallet_recent_table_id_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn15">Previous</button>
                        <button type="button" id="nextBtn15">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step16">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">


                <div class="col-md-6">
                       <h6> Date and Time</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[30]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_recent_table_date_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Date and Time ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_recent_table_date_key"  value="@if($pges_content){{$pges_content->crypto_wallet_recent_table_date_key}} @else Date and Time{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_recent_table_date_key" required value="crypto_wallet_recent_table_date_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Description</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[31]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_recent_table_description_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Description ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_recent_table_description_key"  value="@if($pges_content){{$pges_content->crypto_wallet_recent_table_description_key}} @else Description{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_recent_table_description_key" required value="crypto_wallet_recent_table_description_key" >
                </div>


              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn16">Previous</button>
                        <button type="button" id="nextBtn16">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step17">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">


                <div class="col-md-6">
                       <h6> Amount</h6>
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[32]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_recent_table_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_recent_table_amount_key"  value="@if($pges_content){{$pges_content->crypto_wallet_recent_table_amount_key}} @else Amount{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_recent_table_amount_key" required value="crypto_wallet_recent_table_amount_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Net Amount</h6>
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[33]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_recent_table_net_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Net Amount ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_recent_table_net_amount_key"  value="@if($pges_content){{$pges_content->crypto_wallet_recent_table_net_amount_key}} @else Net Amount{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_recent_table_net_amount_key" required value="crypto_wallet_recent_table_net_amount_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn17">Previous</button>
                        <button type="button" id="nextBtn17">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>


        <fieldset class="step18">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">



                <div class="col-md-6">
                       <h6> Status</h6>
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[34]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_recent_table_status_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Status ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_recent_table_status_key"  value="@if($pges_content){{$pges_content->crypto_wallet_recent_table_status_key}} @else Status{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_recent_table_status_key" required value="crypto_wallet_recent_table_status_key" >
                </div>

                    <div class="col-md-6">
                       <h6> No Deposite Found</h6>
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[35]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_deposit_table_empty_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">No Deposite Found ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_deposit_table_empty_key"  value="@if($pges_content){{$pges_content->crypto_wallet_deposit_table_empty_key}} @else No Deposite Found{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_deposit_table_empty_key" required value="crypto_wallet_deposit_table_empty_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn18">Previous</button>
                        <button type="button" id="nextBtn18">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>


        <fieldset class="step19">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">

                <div class="col-md-6">
                       <h6> No Withdraw Found</h6>
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[36]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->crypto_wallet_withdraw_table_empty_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">No Withdraw Found ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="crypto_wallet_{{$languagevlue->language_symbol}}_withdraw_table_empty_key"  value="@if($pges_content){{$pges_content->crypto_wallet_withdraw_table_empty_key}} @else No Withdraw Found{{$title++}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="crypto_wallet_withdraw_table_empty_key" required value="crypto_wallet_withdraw_table_empty_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn19">Previous</button>
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
$("#nextBtn13").click(function(){
    $('.step14').css('display','block');
    $('.step13').css('display','none');
})
$("#nextBtn14").click(function(){
    $('.step15').css('display','block');
    $('.step14').css('display','none');
})
$("#nextBtn15").click(function(){
    $('.step16').css('display','block');
    $('.step15').css('display','none');
})
$("#nextBtn16").click(function(){
    $('.step17').css('display','block');
    $('.step16').css('display','none');
})
$("#nextBtn17").click(function(){
    $('.step18').css('display','block');
    $('.step17').css('display','none');
})
$("#nextBtn18").click(function(){
    $('.step19').css('display','block');
    $('.step18').css('display','none');
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
$("#prevBtn14").click(function(){
    $('.step13').css('display','block');
    $('.step14').css('display','none');
})
$("#prevBtn15").click(function(){
    $('.step14').css('display','block');
    $('.step15').css('display','none');
})
$("#prevBtn16").click(function(){
    $('.step15').css('display','block');
    $('.step16').css('display','none');
})
$("#prevBtn17").click(function(){
    $('.step16').css('display','block');
    $('.step17').css('display','none');
})
$("#prevBtn18").click(function(){
    $('.step17').css('display','block');
    $('.step18').css('display','none');
})
$("#prevBtn19").click(function(){
    $('.step18').css('display','block');
    $('.step19').css('display','none');
})
</script>

@endsection
