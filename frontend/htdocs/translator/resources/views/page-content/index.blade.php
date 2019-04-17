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
            <br><br><br><h4 class="page-title">Home</h4><br>
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
     <form class="modal-form" method="POST" action="{{ route('home.content.store') }}" >
      @else
                <form class="modal-form" method="POST" id="orders" action="{{ route('home.content.edit') }}" enctype="multipart/form-data">
            <input type="hidden" name="page_content_id" value="{{$page_content[0]->id}}">
            <input type="hidden" name="page_content_id1" value="{{$page_content[1]->id}}">
            <input type="hidden" name="page_content_id2" value="{{$page_content[2]->id}}">
            <input type="hidden" name="page_content_id3" value="{{$page_content[3]->id}}">
            <input type="hidden" name="page_content_id4" value="{{$page_content[4]->id}}">
            <input type="hidden" name="page_content_id5" value="{{$page_content[5]->id}}">
            <input type="hidden" name="page_content_id6" value="{{$page_content[6]->id}}">
            @endif
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="page_id" value="{{$page_id}}">
                 

            <fieldset class="step1">
                 <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>Heading</h6>
                       
                        <input type="hidden" name="id" value="2">
                         
                  
                    <!-- {{$title = 0}} -->
                    @foreach($language as $languagevlue)
                  
                        <?php
                            $pge_content = json_decode($page_content[0]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->home_heading_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Title({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="home_{{$languagevlue->language_symbol}}_heading_key"  value="@if($pges_content){{$pges_content->home_heading_key}}@endif">
                  
                        <!-- {{$title++}} -->
                    @endforeach
                        
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="home_heading_key"  value="home_heading_key">
                  
                  
                </div>
                <div class="col-md-6">
                    <h6> Graph Button Go</h6>
 
                     <!-- {{$graph_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[1]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->home_graph_go_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Graph Button Go({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="home_{{$languagevlue->language_symbol}}_graph_go_key"  value="@if($pges_content){{$pges_content->home_graph_go_key}}@endif">
                        
                        <!-- {{$graph_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="home_graph_go_key"  value="home_graph_go_key">

                                    
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
                       <h6> Table tab Head</h6>
                    

                      <!-- {{$table_tab_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[2]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            // echo isset($pge_content->$symbol);echo "<br>";
                            $pges_content = '';
                            if (isset($pge_content->$symbol->home_table_head_markets_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Table tab Head({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="home_{{$languagevlue->language_symbol}}_table_head_markets_key"  value="@if($pges_content){{$pges_content->home_table_head_markets_key}}@endif">
                        
                        <!-- {{$table_tab_title++}} -->
                    @endforeach


                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="home_table_head_markets_key"  value="home_table_head_markets_key" >
                </div>


                <div class="col-md-6">
                    <h6>Market Table th pair</h6>
                    

                     <!-- {{$market_table_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[3]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->home_table_th_pair_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Market Table th pair({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="home_{{$languagevlue->language_symbol}}_table_th_pair_key"  value="@if($pges_content){{$pges_content->home_table_th_pair_key}}@endif">
                        
                        <!-- {{$market_table_title++}} -->
                    @endforeach

                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="home_table_th_pair_key"  value="home_table_th_pair_key">
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
                       <h6> Market Table th price</h6>
                    
                      <!-- {{$market_table_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[4]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->home_table_th_last_price_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Market Table th price ({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="home_{{$languagevlue->language_symbol}}_table_th_last_price_key"  value="@if($pges_content){{$pges_content->home_table_th_last_price_key}}@endif">
                        
                        <!-- {{$market_table_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="home_table_th_last_price_key"  value="home_table_th_last_price_key" >
                </div>


                <div class="col-md-6">
                    <h6>Market Table th 24h Changes</h6>
                    
                      <!-- {{$market_table_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[5]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->home_table_th_24hchanges_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Market Table th 24h Changes({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="home_{{$languagevlue->language_symbol}}_table_th_24hchanges_key"  value="@if($pges_content){{$pges_content->home_table_th_24hchanges_key}}@endif">
                        
                        <!-- {{$market_table_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="home_table_th_24hchanges_key"  value="home_table_th_24hchanges_key">
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
                       <h6> Market Table th 24h Volume</h6>
                    
                      <!-- {{$market_table_title = 0}} -->
                    @foreach($language as $languagevlue)
                        
                        <?php
                            $pge_content = json_decode($page_content[6]->page_content);
                            $symbol = (string) $languagevlue->language_symbol;
                            $pges_content = '';
                            if (isset($pge_content->$symbol->home_table_th_24hvolume_key)) {
                                // print_r($pge_content->$symbol);
                                $pges_content = $pge_content->$symbol;
                            }
                            
                            //print_r($pge_content->$symbol);
                        ?>
                        <label for="name">Market Table th 24h Volume({{$languagevlue->language_symbol}})</label>
                        <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="home_{{$languagevlue->language_symbol}}_table_th_24hvolume_key"  value="@if($pges_content){{$pges_content->home_table_th_24hvolume_key}}@endif">
                        
                        <!-- {{$market_table_title++}} -->
                    @endforeach
                    <input type="hidden" class="form-control"   id="s" placeholder="Enter Your Page" name="home_table_th_24hvolume_key"  value="home_table_th_24hvolume_key" >
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
