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
.step2,.step3,.step4,.step5,.step6,.step7,.step8,.step9,.step10,.step11,.step12,.step13,.step14,.step15,.step16,.step17,.step18,.step19,.step20,.step21,.step22,.step23,.step24,.step25,.step26,.step27,.step28,.step29,.step30,.step31,.step32,.step33 {
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
#prevBtn2,#prevBtn3,#prevBtn4,#prevBtn5,#prevBtn6,#prevBtn7,#prevBtn8,#prevBtn9,#prevBtn10,#prevBtn11,#prevBtn12,#prevBtn13,#prevBtn14,#prevBtn15,#prevBtn16,#prevBtn17,#prevBtn18,#prevBtn19,#prevBtn20,#prevBtn21,#prevBtn22,#prevBtn23,#prevBtn24,#prevBtn25,#prevBtn26,#prevBtn27,#prevBtn28,#prevBtn29,#prevBtn30,#prevBtn31,#prevBtn32,#prevBtn33 {
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
#nextBtn1,#nextBtn2,#nextBtn3,#nextBtn4,#nextBtn5,#nextBtn6,#nextBtn7,#nextBtn8,#nextBtn9,#nextBtn10,#nextBtn11,#nextBtn12,#nextBtn13,#nextBtn14,#nextBtn15,#nextBtn16,#nextBtn17,#nextBtn18,#nextBtn19,#nextBtn20,#nextBtn21,#nextBtn22,#nextBtn23,#nextBtn24,#nextBtn25,#nextBtn26,#nextBtn27,#nextBtn28,#nextBtn29,#nextBtn30,#nextBtn31,#nextBtn32,#nextBtn33 {
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
            <br><br><br><h4 class="page-title">Simple Trade</h4><br>
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
     <form class="modal-form" method="POST" action="{{ route('simple.trade.content.store') }}" >
      @else
                <form class="modal-form" method="POST" id="orders" action="{{ route('simple.trade.content.edit') }}" enctype="multipart/form-data">
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
            <input type="hidden" name="page_content_id37" value="{{$page_content[37]->id}}">
            <input type="hidden" name="page_content_id38" value="{{$page_content[38]->id}}">
            <input type="hidden" name="page_content_id39" value="{{$page_content[39]->id}}">
            <input type="hidden" name="page_content_id40" value="{{$page_content[40]->id}}">
            <input type="hidden" name="page_content_id41" value="{{$page_content[41]->id}}">
            <input type="hidden" name="page_content_id42" value="{{$page_content[42]->id}}">
            <input type="hidden" name="page_content_id43" value="{{$page_content[43]->id}}">
            <input type="hidden" name="page_content_id44" value="{{$page_content[44]->id}}">
            <input type="hidden" name="page_content_id45" value="{{$page_content[45]->id}}">
            <input type="hidden" name="page_content_id46" value="{{$page_content[46]->id}}">
            <input type="hidden" name="page_content_id47" value="{{$page_content[47]->id}}">
            <input type="hidden" name="page_content_id48" value="{{$page_content[48]->id}}">
            <input type="hidden" name="page_content_id49" value="{{$page_content[49]->id}}">
            <input type="hidden" name="page_content_id50" value="{{$page_content[50]->id}}">
            <input type="hidden" name="page_content_id51" value="{{$page_content[51]->id}}">
            <input type="hidden" name="page_content_id52" value="{{$page_content[52]->id}}">
            <input type="hidden" name="page_content_id53" value="{{$page_content[53]->id}}">
            <input type="hidden" name="page_content_id54" value="{{$page_content[54]->id}}">
            <input type="hidden" name="page_content_id55" value="{{$page_content[55]->id}}">
            <input type="hidden" name="page_content_id56" value="{{$page_content[56]->id}}">
            <input type="hidden" name="page_content_id57" value="{{$page_content[57]->id}}">
            <input type="hidden" name="page_content_id58" value="{{$page_content[58]->id}}">
            <input type="hidden" name="page_content_id59" value="{{$page_content[59]->id}}">
            <input type="hidden" name="page_content_id60" value="{{$page_content[60]->id}}">
            <input type="hidden" name="page_content_id61" value="{{$page_content[61]->id}}">
            <input type="hidden" name="page_content_id62" value="{{$page_content[62]->id}}">
            <input type="hidden" name="page_content_id63" value="{{$page_content[63]->id}}">
            <input type="hidden" name="page_content_id64" value="{{$page_content[64]->id}}">
            @endif
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="page_id" value="{{$page_id}}">
                 

            <fieldset class="step1">
                 <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>Last Price</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[0]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_last_price_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Last Price ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_last_price_key"  value="@if($pges_content){{$pges_content->simple_trade_last_price_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_last_price_key" required value="simple_trade_last_price_key">
                  
                  
                </div>
                <div class="col-md-6">
                    <h6> 24h change</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[1]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_24change_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">24h change ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_24change_key"  value="@if($pges_content){{$pges_content->simple_trade_24change_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_24change_key" required value="simple_trade_24change_key">

                                    
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
                       <h6> 24h Volume</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[2]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_24volume_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">24h Volume ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_24volume_key"  value="@if($pges_content){{$pges_content->simple_trade_24volume_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_24volume_key" required value="simple_trade_24volume_key" >
                </div>


                <div class="col-md-6">
                    <h6>Content in list1</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[3]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_li_content1_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content in list1 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_li_content1_key"  value="@if($pges_content){{$pges_content->simple_trade_li_content1_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_li_content1_key" required value="simple_trade_li_content1_key">
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
                       <h6> Content in list2</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[4]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_li_content2_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content in list2 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_li_content2_key"  value="@if($pges_content){{$pges_content->simple_trade_li_content2_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_li_content2_key" required value="simple_trade_li_content2_key" >
                </div>


                <div class="col-md-6">
                    <h6>Content in list3</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[5]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_li_content3_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content in list3 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_li_content3_key"  value="@if($pges_content){{$pges_content->simple_trade_li_content3_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_li_content3_key" required value="simple_trade_li_content3_key">
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
                       <h6> Content in list4</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[6]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_li_content4_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Content in list4 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_li_content4_key"  value="@if($pges_content){{$pges_content->simple_trade_li_content4_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_li_content4_key" required value="simple_trade_li_content4_key" >
                </div>


                <div class="col-md-6">
                    <h6>Add a Deposit </h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[7]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_li_content4_deposite_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Add a Deposit ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_li_content4_deposite_key"  value="@if($pges_content){{$pges_content->simple_trade_li_content4_deposite_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_li_content4_deposite_key" required value="simple_trade_li_content4_deposite_key">
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
                       <h6> Open Orders on Market</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[8]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Open Orders on Market ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_open_market_key" required value="simple_trade_open_market_key" >
                </div>


                <div class="col-md-6">
                    <h6>Open Orders on Market Modal Content</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[9]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_content_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Open Orders on Market Modal Content ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_content_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_content_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_open_market_content_key" required value="simple_trade_open_market_content_key">
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
                    <h6>Buy Orders</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[10]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_tab_buy_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy Orders ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_tab_buy_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_tab_buy_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_open_market_tab_buy_key" required value="simple_trade_open_market_tab_buy_key">
                </div>

                <div class="col-md-6">
                       <h6> Sell Orders</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[11]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_tab_sell_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Sell Orders ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_tab_sell_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_tab_sell_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_open_market_tab_sell_key" required value="simple_trade_open_market_tab_sell_key" >
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
                       <h6> Price</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[12]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_table_price_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Price ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_table_price_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_table_price_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_open_market_table_price_key" required value="simple_trade_open_market_table_price_key" >
                </div>

              

                <div class="col-md-6">
                       <h6> Amount</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[13]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_table_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_table_amount_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_table_amount_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_open_market_table_amount_key" required value="simple_trade_open_market_table_amount_key" >
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
                       <h6> Total</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[14]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_table_total_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Total ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_table_total_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_table_total_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_open_market_table_total_key" required value="simple_trade_open_market_table_total_key" >
                </div>

                <div class="col-md-6">
                       <h6> Currency Pairs</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[15]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Currency Pairs ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_key" required value="simple_trade_currency_pair_key" >
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
                       <h6> Currency pair modal content</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[16]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_modal_content_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Currency pair modal content ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_modal_content_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_modal_content_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_modal_content_key" required value="simple_trade_currency_pair_modal_content_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Currency pair modal li1</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[17]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_modal_li1_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Currency pair modal li1 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_modal_li1_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_modal_li1_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_modal_li1_key" required value="simple_trade_currency_pair_modal_li1_key" >
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
                       <h6> Currency pair modal li2</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[18]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_modal_li2_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Currency pair modal li2 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_modal_li2_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_modal_li2_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_modal_li2_key" required value="simple_trade_currency_pair_modal_li2_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Currency pair modal li3</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[19]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_modal_li3_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Currency pair modal li3 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_modal_li3_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_modal_li3_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_modal_li3_key" required value="simple_trade_currency_pair_modal_li3_key" >
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
                       <h6> Currency pair modal li4</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[20]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_modal_li4_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Currency pair modal li4 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_modal_li4_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_modal_li4_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_modal_li4_key" required value="simple_trade_currency_pair_modal_li4_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Pair</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[21]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_table_pair_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Pair ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_table_pair_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_table_pair_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_table_pair_key" required value="simple_trade_currency_pair_table_pair_key" >
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
                       <h6> Price</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[22]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_table_price_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Price ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_table_price_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_table_price_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_table_price_key" required value="simple_trade_currency_pair_table_price_key" >
                </div>

                <div class="col-md-6">
                       <h6> Change</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[23]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_pair_table_change_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Change ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_pair_table_change_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_pair_table_change_key}}  @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_pair_table_change_key" required value="simple_trade_currency_pair_table_change_key" >
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
                       <h6> Buy or Sell</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[24]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_sell_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy or Sell ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_sell_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_sell_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_sell_key" required value="simple_trade_buy_sell_key" >
                </div>

                <div class="col-md-6">
                       <h6> Buy or Sell modal Content</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[25]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_sell_modal_content_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy or Sell modal Content ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_sell_modal_content_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_sell_modal_content_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_sell_modal_content_key" required value="simple_trade_buy_sell_modal_content_key" >
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
                       <h6> Buy or Sell modal li1</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[26]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_sell_modal_li1_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy or Sell modal li1 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_sell_modal_li1_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_sell_modal_li1_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_sell_modal_li1_key" required value="simple_trade_buy_sell_modal_li1_key" >
                </div>

              

                <div class="col-md-6">
                       <h6> Buy or Sell modal li2</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[27]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_sell_modal_li2_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy or Sell modal li2 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_sell_modal_li2_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_sell_modal_li2_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_sell_modal_li2_key" required value="simple_trade_buy_sell_modal_li2_key" >
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
                       <h6> Buy or Sell modal li3</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[28]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_sell_modal_li3_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy or Sell modal li3 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_sell_modal_li3_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_sell_modal_li3_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_sell_modal_li3_key" required value="simple_trade_buy_sell_modal_li3_key" >
                </div>

                <div class="col-md-6">
                       <h6> Buy or Sell modal li4</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[29]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_sell_modal_li4_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy or Sell modal li4 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_sell_modal_li4_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_sell_modal_li4_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_sell_modal_li4_key" required value="simple_trade_buy_sell_modal_li4_key" >
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
                       <h6> Buy or Sell modal li5</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[30]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_sell_modal_li5_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy or Sell modal li5 ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_sell_modal_li5_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_sell_modal_li5_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_sell_modal_li5_key" required value="simple_trade_buy_sell_modal_li5_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Buy Cryptocurrency</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[31]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_crypto_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy Cryptocurrency ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_crypto_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_crypto_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_crypto_key" required value="simple_trade_buy_crypto_key" >
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
                       <h6> Available Balance</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[32]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_available_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Available Balance ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_available_key"  value="@if($pges_content){{$pges_content->simple_trade_available_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_available_key" required value="simple_trade_available_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Amount to Buy</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[33]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount to Buy ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_amount_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_amount_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_amount_key" required value="simple_trade_buy_amount_key" >
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
                       <h6> Currency to use</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[64]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_currency_use_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Currency to use ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_currency_use_key"  value="@if($pges_content){{$pges_content->simple_trade_currency_use_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_currency_use_key" required value="simple_trade_currency_use_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Buy at market price</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[34]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_market_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy at market price ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_market_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_market_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_market_key" required value="simple_trade_buy_market_key" >
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
                       <h6> Limit Order</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[35]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_limit_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Limit Order ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_limit_key"  value="@if($pges_content){{$pges_content->simple_trade_limit_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_limit_key" required value="simple_trade_limit_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Confirm Sale </h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[62]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_sell_confirm_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Confirm Sale ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_sell_confirm_button_key"  value="@if($pges_content){{$pges_content->simple_trade_sell_confirm_button_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_sell_confirm_button_key" required value="simple_trade_sell_confirm_button_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn19">Previous</button>
                      <button type="button" id="nextBtn19">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step20">
                 <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>Stop Order</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[36]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_stop_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Stop Order ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_stop_key"  value="@if($pges_content){{$pges_content->simple_trade_stop_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_stop_key" required value="simple_trade_stop_key">
                  
                  
                </div>
                <div class="col-md-6">
                    <h6> Limit Price</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[37]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_limit_price_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Limit Price ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_limit_price_key"  value="@if($pges_content){{$pges_content->simple_trade_limit_price_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_limit_price_key" required value="simple_trade_limit_price_key">

                                    
                </div>
              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                      <button type="button" id="prevBtn20">Previous</button>
                      <button type="button" id="nextBtn20">Next</button>
                    </div>
                  </div>
                </div>

            </div>
          </div>
        </fieldset>

        <fieldset class="step21">        
         <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Subtotal</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[38]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_subtotal_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Subtotal ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_subtotal_key"  value="@if($pges_content){{$pges_content->simple_trade_subtotal_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_subtotal_key" required value="simple_trade_subtotal_key" >
                </div>


                <div class="col-md-6">
                    <h6>Fee</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[39]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_fee_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Fee ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_fee_key"  value="@if($pges_content){{$pges_content->simple_trade_fee_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_fee_key" required value="simple_trade_fee_key">
                </div>
              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn21">Previous</button>
                        <button type="button" id="nextBtn21">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </fieldset>

        <fieldset class="step22">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Approx</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[40]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_approx_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Approx ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_approx_key"  value="@if($pges_content){{$pges_content->simple_trade_approx_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_approx_key" required value="simple_trade_approx_key" >
                </div>


                <div class="col-md-6">
                    <h6>to spend</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[41]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_to_spend_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">to spend ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_to_spend_key"  value="@if($pges_content){{$pges_content->simple_trade_to_spend_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_to_spend_key" required value="simple_trade_to_spend_key">
                </div>
              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn22">Previous</button>
                        <button type="button" id="nextBtn22">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>
        </fieldset>

        <fieldset class="step23">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Buy</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[42]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Buy ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_button_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_button_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_button_key" required value="simple_trade_buy_button_key" >
                </div>


                <div class="col-md-6">
                    <h6>Confirm Transaction Details </h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[43]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_confirm_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Confirm Transaction Details ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_confirm_key"  value="@if($pges_content){{$pges_content->simple_trade_confirm_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_confirm_key" required value="simple_trade_confirm_key">
                </div>
              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn23">Previous</button>
                        <button type="button" id="nextBtn23">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step24">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Amount to Buy</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[44]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_confirm_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount to Buy ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_confirm_amount_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_confirm_amount_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_confirm_amount_key" required value="simple_trade_buy_confirm_amount_key" >
                </div>


                <div class="col-md-6">
                    <h6>By clicking CONFIRM button an order request will be created.</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[45]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_confirm_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">By clicking CONFIRM button an order request will be created. ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_confirm_button_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_confirm_button_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_buy_confirm_button_key" required value="simple_trade_buy_confirm_button_key">
                </div>
              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn24">Previous</button>
                        <button type="button" id="nextBtn24">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step25">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                    <h6>Back</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[46]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_confirm_button_back_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Back ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_confirm_button_back_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_confirm_button_back_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="simple_trade_buy_confirm_button_back_key" required value="simple_trade_buy_confirm_button_back_key">
                </div>

                <div class="col-md-6">
                       <h6> Conform Button</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[47]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_buy_confirm_button_content_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Conform button ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_buy_confirm_button_content_key"  value="@if($pges_content){{$pges_content->simple_trade_buy_confirm_button_content_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        

                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_buy_confirm_button_content_key" required value="simple_trade_buy_confirm_button_content_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn25">Previous</button>
                        <button type="button" id="nextBtn25">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step26">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Sell Cryptocurrency</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[48]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_sell_crypto_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Sell Cryptocurrency ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_sell_crypto_key"  value="@if($pges_content){{$pges_content->simple_trade_sell_crypto_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_sell_crypto_key" required value="simple_trade_sell_crypto_key" >
                </div>

              

                <div class="col-md-6">
                       <h6> Amount to Sell</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[49]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_sell_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount to Sell ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_sell_amount_key"  value="@if($pges_content){{$pges_content->simple_trade_sell_amount_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_sell_amount_key" required value="simple_trade_sell_amount_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn26">Previous</button>
                        <button type="button" id="nextBtn26">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step27">
          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Sell at market price</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[50]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_sell_market_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Sell at market price ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_sell_market_key"  value="@if($pges_content){{$pges_content->simple_trade_sell_market_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_sell_market_key" required value="simple_trade_sell_market_key" >
                </div>

                <div class="col-md-6">
                       <h6> Sell</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[51]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_sell_button_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Sell ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_sell_button_key"  value="@if($pges_content){{$pges_content->simple_trade_sell_button_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_sell_button_key" required value="simple_trade_sell_button_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn27">Previous</button>
                        <button type="button" id="nextBtn27">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step28">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">


                <div class="col-md-6">
                       <h6> Amount to Sell</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[52]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_sell_confirm_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount to Sell ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_sell_confirm_amount_key"  value="@if($pges_content){{$pges_content->simple_trade_sell_confirm_amount_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_sell_confirm_amount_key" required value="simple_trade_sell_confirm_amount_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Trade History</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[53]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_history_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Trade History ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_history_key"  value="@if($pges_content){{$pges_content->simple_trade_history_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_history_key" required value="simple_trade_history_key" >
                </div>


              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn28">Previous</button>
                        <button type="button" id="nextBtn28">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

        <fieldset class="step29">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">


                <div class="col-md-6">
                       <h6> Trade History table head Pair</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[54]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_history_table_pair_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Pair ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_history_table_pair_key"  value="@if($pges_content){{$pges_content->simple_trade_history_table_pair_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_history_table_pair_key" required value="simple_trade_history_table_pair_key" >
                </div>

                    <div class="col-md-6">
                       <h6> Trade History table head Price</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[55]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_history_table_price_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Price ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_history_table_price_key"  value="@if($pges_content){{$pges_content->simple_trade_history_table_price_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_history_table_price_key" required value="simple_trade_history_table_price_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn29">Previous</button>
                        <button type="button" id="nextBtn29">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>


        <fieldset class="step30">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">



                <div class="col-md-6">
                       <h6> Trade History table head Amount</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[56]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_history_table_amount_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Amount ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_history_table_amount_key"  value="@if($pges_content){{$pges_content->simple_trade_history_table_amount_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_history_table_amount_key" required value="simple_trade_history_table_amount_key" >
                </div>

                    <div class="col-md-6">
                       <h6> No Sell Orders</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[57]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_sell_no_data_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">No Sell Orders ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_sell_no_data_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_sell_no_data_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_open_market_sell_no_data_key" required value="simple_trade_open_market_sell_no_data_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn30">Previous</button>
                        <button type="button" id="nextBtn30">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>


        <fieldset class="step31">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">

                <div class="col-md-6">
                       <h6> No Buy Orders</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[58]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_open_market_buy_no_data_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">No Buy Orders ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_open_market_buy_no_data_key"  value="@if($pges_content){{$pges_content->simple_trade_open_market_buy_no_data_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_open_market_buy_no_data_key" required value="simple_trade_open_market_buy_no_data_key" >
                </div>

                <div class="col-md-6">
                       <h6> Use your Referral Bonus</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[59]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_referral_bonus_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Use your Referral Bonus ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_referral_bonus_key"  value="@if($pges_content){{$pges_content->simple_trade_referral_bonus_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_referral_bonus_key" required value="simple_trade_referral_bonus_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn31">Previous</button>
                        <button type="button" id="nextBtn31">Next</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>


        <fieldset class="step32">

          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">

                <div class="col-md-6">
                       <h6> No Trade Orders</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[60]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_history_table_no_data_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">No Trade Orders ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_history_table_no_data_key"  value="@if($pges_content){{$pges_content->simple_trade_history_table_no_data_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_history_table_no_data_key" required value="simple_trade_history_table_no_data_key" >
                </div>

                <div class="col-md-6">
                       <h6> to Receive</h6>
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[61]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->simple_trade_to_receive_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">to Receive ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="simple_trade_{{$languagevlue->language_symbol}}_to_receive_key"  value="@if($pges_content){{$pges_content->simple_trade_to_receive_key}} @endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="simple_trade_to_receive_key" required value="simple_trade_to_receive_key" >
                </div>

              </div>
                <div class="modal-footer">
                  <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn32">Previous</button>
                        <button type="submit" id="btnsubmit">Submit</button>
                    </div>
                  </div>
                </div>
            </div>
          </div>

        </fieldset>

      <br><br>




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
$("#nextBtn19").click(function(){
    $('.step20').css('display','block');
    $('.step19').css('display','none');
})
$("#nextBtn20").click(function(){
    $('.step21').css('display','block');
    $('.step20').css('display','none');
})
$("#nextBtn21").click(function(){
    $('.step22').css('display','block');
    $('.step21').css('display','none');
})
$("#nextBtn22").click(function(){
    $('.step23').css('display','block');
    $('.step22').css('display','none');
})
$("#nextBtn23").click(function(){
    $('.step24').css('display','block');
    $('.step23').css('display','none');
})
$("#nextBtn24").click(function(){
    $('.step25').css('display','block');
    $('.step24').css('display','none');
})
$("#nextBtn25").click(function(){
    $('.step26').css('display','block');
    $('.step25').css('display','none');
})
$("#nextBtn26").click(function(){
    $('.step27').css('display','block');
    $('.step26').css('display','none');
})
$("#nextBtn27").click(function(){
    $('.step28').css('display','block');
    $('.step27').css('display','none');
})
$("#nextBtn28").click(function(){
    $('.step29').css('display','block');
    $('.step28').css('display','none');
})
$("#nextBtn29").click(function(){
    $('.step30').css('display','block');
    $('.step29').css('display','none');
})
$("#nextBtn30").click(function(){
    $('.step31').css('display','block');
    $('.step30').css('display','none');
})
$("#nextBtn31").click(function(){
    $('.step32').css('display','block');
    $('.step31').css('display','none');
})
$("#nextBtn32").click(function(){
    $('.step33').css('display','block');
    $('.step32').css('display','none');
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
$("#prevBtn20").click(function(){
    $('.step19').css('display','block');
    $('.step20').css('display','none');
})
$("#prevBtn21").click(function(){
    $('.step20').css('display','block');
    $('.step21').css('display','none');
})
$("#prevBtn22").click(function(){
    $('.step21').css('display','block');
    $('.step22').css('display','none');
})
$("#prevBtn23").click(function(){
    $('.step22').css('display','block');
    $('.step23').css('display','none');
})
$("#prevBtn24").click(function(){
    $('.step23').css('display','block');
    $('.step24').css('display','none');
})
$("#prevBtn25").click(function(){
    $('.step24').css('display','block');
    $('.step25').css('display','none');
})
$("#prevBtn26").click(function(){
    $('.step25').css('display','block');
    $('.step26').css('display','none');
})
$("#prevBtn27").click(function(){
    $('.step26').css('display','block');
    $('.step27').css('display','none');
})
$("#prevBtn28").click(function(){
    $('.step27').css('display','block');
    $('.step28').css('display','none');
})
$("#prevBtn29").click(function(){
    $('.step28').css('display','block');
    $('.step29').css('display','none');
})
$("#prevBtn30").click(function(){
    $('.step29').css('display','block');
    $('.step30').css('display','none');
})
$("#prevBtn31").click(function(){
    $('.step30').css('display','block');
    $('.step31').css('display','none');
})
$("#prevBtn32").click(function(){
    $('.step31').css('display','block');
    $('.step32').css('display','none');
})
$("#prevBtn33").click(function(){
    $('.step32').css('display','block');
    $('.step33').css('display','none');
})
</script>

@endsection
