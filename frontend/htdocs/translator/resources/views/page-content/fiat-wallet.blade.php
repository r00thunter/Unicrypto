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
.tab {
  display: none;
}

#prevBtn {
  background-color: #bbbbbb;
}

/* Make circles that indicate the steps of the form: */


.step.active {
  opacity: 1;
}

/* Mark the steps that are finished and valid: */
.step.finish {
  background-color: #4CAF50;
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
            <h4 class="page-title">Fiat Wallet</h4>
             <div class="card-header">
                    <ul>
                        <li>Add the respective text in multiple languages for all the available elements of the page.</li>
                        <li>Different languages are denoted by the Symbol (ES, FR ) mentioned in the languages section.</li>
                        <li>To add a new language, go to the language section.
                        </li>
                    </ul>
                </div>
                <br>
             <form class="modal-form" method="POST" id="orders" action=" " enctype="multipart/form-data">
      <!-- Modal body -->
     
               <input type="hidden" name="_token" value="{{csrf_token()}}">
                 
                 <div class="tab">
                  <h6>Heading</h6>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <input type="hidden" name="id" value="$order">
                    <label for="name">Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_heading" required value="{{\App\TranslatorPageContent::getName('fiat_heading_content_key')->en_page_content}}">
                <label for="name">Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_heading" required value="{{\App\TranslatorPageContent::getName('fiat_heading_content_key')->fn_page_content}}">
<label for="name">Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_heading" required value="{{\App\TranslatorPageContent::getName('fiat_heading_content_key')->sp_page_content}}">
<label for="name">Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_heading" required value="{{\App\TranslatorPageContent::getName('fiat_heading_content_key')->ab_page_content}}">
  <label for="name">Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_heading" required value="{{\App\TranslatorPageContent::getName('fiat_heading_content_key')->ge_page_content}}">
 <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_heading_content_key" required value="fiat_heading_content_key">
                </div>
                <div class="col-md-6">
                   <h6> Body-Heading</h6>
 <label for="name">Sub-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_body_heading" required value="{{\App\TranslatorPageContent::getName('fiat_body_heading_content_key')->en_page_content}}"> 
  <label for="name">Sub-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_body_heading" required value="{{\App\TranslatorPageContent::getName('fiat_body_heading_content_key')->fn_page_content}}">
<label for="name">Sub-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_body_heading" required value="{{\App\TranslatorPageContent::getName('fiat_body_heading_content_key')->sp_page_content}}">
 <label for="name">Sub-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_body_heading" required value="{{\App\TranslatorPageContent::getName('fiat_body_heading_content_key')->ab_page_content}}">
<label for="name">Sub-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_body_heading" required value="{{\App\TranslatorPageContent::getName('fiat_body_heading_content_key')->ge_page_content}}">
 <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_body_heading_content_key" required value="fiat_body_heading_content_key">
                  </div>
              </div>
            </div>
          </div>
            
      <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Button-content</h6>
                    <label for="name">Content-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_button" required value="{{\App\TranslatorPageContent::getName('fiat_button_content_key')->en_page_content}}">
                <label for="name">Content-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_button"  value="{{\App\TranslatorPageContent::getName('fiat_button_content_key')->fn_page_content}}">
 <label for="name">Content-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_button" required value="{{\App\TranslatorPageContent::getName('fiat_button_content_key')->sp_page_content}}">
<label for="name">Content-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_button" required value="{{\App\TranslatorPageContent::getName('fiat_button_content_key')->ab_page_content}}">
 <label for="name">Content-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_button" required value="{{\App\TranslatorPageContent::getName('fiat_button_content_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_button_content_key" required value="fiat_button_content_key">
                </div>
                <div class="col-md-6">
                      <h6>LeftSide-Heading</h6>
 <label for="name">Fait-Currency-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_content_key')->en_page_content}}">
 <label for="name">Fait-Currency-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_content_key')->fn_page_content}}">
 <label for="name">Fait-Currency-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_content_key')->sp_page_content}}">
  <label for="name">Fait-Currency-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_content_key')->ab_page_content}}">
 <label for="name">Fait-Currency-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_content_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_table_heading_content_key" required value="fiat_table_heading_content_key">
                </div>
              </div>
            </div>
          </div>
           
          <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6>Transaction-Id</h6>
                    <label for="name">Transaction-Id-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_transaction" required value="{{\App\TranslatorPageContent::getName('fiat_transaction_content_key')->en_page_content}}">
                <label for="name">Transaction-Id-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_transaction" required value="{{\App\TranslatorPageContent::getName('fiat_transaction_content_key')->fn_page_content}}">
<label for="name">Transaction-Id-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_transaction" required value="{{\App\TranslatorPageContent::getName('fiat_transaction_content_key')->sp_page_content}}">
<label for="name">Transaction-Id-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_transaction" required value="{{\App\TranslatorPageContent::getName('fiat_transaction_content_key')->ab_page_content}}">
 <label for="name">Transaction-Id-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_transaction" required value="{{\App\TranslatorPageContent::getName('fiat_transaction_content_key')->ge_page_content}}">

                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_transaction_content_key" required value="fiat_transaction_content_key">
                </div>
                <div class="col-md-6">
                     <h6>Bank Name</h6>
                       <label for="name">Bank-Name-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_bank" required value="{{\App\TranslatorPageContent::getName('fiat_bank_content_key')->en_page_content}}">
<label for="name">Bank-Name-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_bank" required value="{{\App\TranslatorPageContent::getName('fiat_bank_content_key')->fn_page_content}}">
<label for="name">Bank-Name-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_bank" required value="{{\App\TranslatorPageContent::getName('fiat_bank_content_key')->sp_page_content}}">
  <label for="name">Bank-Name-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_bank" required value="{{\App\TranslatorPageContent::getName('fiat_bank_content_key')->ab_page_content}}">
<label for="name">Bank-Name-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_bank" required value="{{\App\TranslatorPageContent::getName('fiat_bank_content_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_bank_content_key" required value="fiat_bank_content_key">
                </div>
              </div>
            </div>
          </div>
            
        <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Amount</h6>
<label for="name">Amount-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_content_key')->en_page_content}}">
 <label for="name">Amount-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_content_key')->fn_page_content}}">
 <label for="name">Amount-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_content_key')->sp_page_content}}">
<label for="name">Amount-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_content_key')->ab_page_content}}">
 <label for="name">Amount-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_content_key')->ge_page_content}}">
 <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_amount_content_key" required value="fiat_amount_content_key">
                
                </div>
                <div class="col-md-6">
                    <h6>RightSide-Heading</h6>
                    <label for="name">Fiat-Wallet-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_right_heading" required value="{{\App\TranslatorPageContent::getName('fiat_right_heading_content_key')->en_page_content}}">
                <label for="name">Fiat-Wallet-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_right_heading" required value="{{\App\TranslatorPageContent::getName('fiat_right_heading_content_key')->fn_page_content}}">
<label for="name">Fiat-Wallet-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_right_heading" required value="{{\App\TranslatorPageContent::getName('fiat_right_heading_content_key')->sp_page_content}}">
 <label for="name">Fiat-Wallet-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_right_heading" required value="{{\App\TranslatorPageContent::getName('fiat_right_heading_content_key')->ab_page_content}}">
<label for="name">Fiat-Wallet-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_right_heading" required value="{{\App\TranslatorPageContent::getName('fiat_right_heading_content_key')->ge_page_content}}">
 <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_right_heading_content_key" required value="fiat_right_heading_content_key"> 
                </div>
              </div>
            </div>
          </div>
           
     <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>USD-Wallet</h6>
   <label for="name">USD-Doller-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_usd" required value="{{\App\TranslatorPageContent::getName('fiat_usd_content_key')->en_page_content}}">
 <label for="name">USD-Doller-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_usd" required value="{{\App\TranslatorPageContent::getName('fiat_usd_content_key')->fn_page_content}}">
  <label for="name">USD-Doller-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_usd" required value="{{\App\TranslatorPageContent::getName('fiat_usd_content_key')->sp_page_content}}">
  <label for="name">USD-Doller-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_usd" required value="{{\App\TranslatorPageContent::getName('fiat_usd_content_key')->ab_page_content}}">
    <label for="name">USD-Doller-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_usd" required value="{{\App\TranslatorPageContent::getName('fiat_usd_content_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_usd_content_key" required value="fiat_usd_content_key">
                </div>
                <div class="col-md-6">
                      <h6> Button-content</h6>
                    <label for="name">Content-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_button1" required value="{{\App\TranslatorPageContent::getName('fiat_button1_key')->en_page_content}}">
               <label for="name">Content-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_button1" required value="{{\App\TranslatorPageContent::getName('fiat_button1_key')->fn_page_content}}">
 <label for="name">Content-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_button1" required value="{{\App\TranslatorPageContent::getName('fiat_button1_key')->sp_page_content}}">
<label for="name">Content-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_button1" required value="
                    {{\App\TranslatorPageContent::getName('fiat_button1_key')->ab_page_content}}">
<label for="name">Content-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_button1" required value="{{\App\TranslatorPageContent::getName('fiat_button1_key')->ge_page_content}}">
 <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_button1_content_key" required value="fiat_button1_key">
                
                
                </div>
              </div>
            </div>
          </div>
            
       
        <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                     <h6> Button-content2</h6>
                    <label for="name">Content-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_button2" required value="{{\App\TranslatorPageContent::getName('fiat_button2_key')->en_page_content}}">
                <label for="name">Content-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_button2" required value="{{\App\TranslatorPageContent::getName('fiat_button2_key')->fn_page_content}}">
<label for="name">Content-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_button2" required value="{{\App\TranslatorPageContent::getName('fiat_button2_key')->sp_page_content}}">
<label for="name">Content-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_button2" required value="{{\App\TranslatorPageContent::getName('fiat_button2_key')->ab_page_content}}">
<label for="name">Content-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_button2" required value="{{\App\TranslatorPageContent::getName('fiat_button2_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_button2_content_key" required value="fiat_button2_key">
                <div class="col-md-6">
                      <h6> table-Heading</h6>
                    <label for="name">table-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_key')->en_page_content}}">
                <label for="name">Table-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_key')->fn_page_content}}">
<label for="name">Table-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_key')->sp_page_content}}">
<label for="name">Table-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_key')->ab_page_content}}">
label for="name">Table-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_table_heading" required value="{{\App\TranslatorPageContent::getName('fiat_table_heading_key')->ge_page_content}}">

                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_table_heading_content_key" required value="fiat_table_heading_key">
                </div>
              </div>
            </div>
          </div>
            
        
            
         <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6> Type-Table</h6>
                    <label for="name">Type-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_type" required value="{{\App\TranslatorPageContent::getName('fiat_type_key')->en_page_content}}">
               <label for="name">Type-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_type" required value="{{\App\TranslatorPageContent::getName('fiat_type_key')->fn_page_content}}">
<label for="name">Type-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_type" required value="{{\App\TranslatorPageContent::getName('fiat_type_key')->sp_page_content}}">
<label for="name">Type-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_type" required value="{{\App\TranslatorPageContent::getName('fiat_type_key')->ab_page_content}}">
 <label for="name">Type-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_type" required value="{{\App\TranslatorPageContent::getName('fiat_type_key')->ge_page_content}}">
   <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_type_content_key" required value="fiat_type_key">
                </div>
                <div class="col-md-6">
                     <h6> Date-Table</h6>
                    <label for="name">Date-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_date" required value="{{\App\TranslatorPageContent::getName('fiat_date_key')->en_page_content}}">
               <label for="name">Date-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_date" required value="{{\App\TranslatorPageContent::getName('fiat_date_key')->fn_page_content}}">
<label for="name">Date-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_date" required value="{{\App\TranslatorPageContent::getName('fiat_date_key')->sp_page_content}}">
<label for="name">Date-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_date" required value="{{\App\TranslatorPageContent::getName('fiat_date_key')->ab_page_content}}">
<label for="name">Date-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_date" required value="{{\App\TranslatorPageContent::getName('fiat_date_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_date_content_key" required value="fiat_date_key">  
                </div>
              </div>
            </div>
          </div>
           
        <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                       <h6> Coin-Table</h6>
<label for="name">Coin-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_coin" required value="{{\App\TranslatorPageContent::getName('fiat_coin_key')->en_page_content}}">
<label for="name">Coin-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_coin" required value="{{\App\TranslatorPageContent::getName('fiat_coin_key')->fn_page_content}}">
<label for="name">Coin-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_coin" required value="{{\App\TranslatorPageContent::getName('fiat_coin_key')->sp_page_content}}">
 <label for="name">Coin-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_coin" required value="{{\App\TranslatorPageContent::getName('fiat_coin_key')->ab_page_content}}">
<label for="name">Coin-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_gecoin" required value="{{\App\TranslatorPageContent::getName('fiat_coin_key')->ge_page_content}}">
 <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_coin_content_key" required value="fiat_coin_key">
                </div>
                <div class="col-md-6">
                   <h6> Amount-Table</h6>
                    <label for="name">Amount-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_key')->en_page_content}}">
               <label for="name">Amount-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_key')->fn_page_content}}">
 <label for="name">Amount-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_key')->sp_page_content}}">
<label for="name">Amount-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_key')->ab_page_content}}">
<label for="name">Amount-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_amount" required value="{{\App\TranslatorPageContent::getName('fiat_amount_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_amount_content_key" required value="fiat_amount_key">
                </div>
              </div>
            </div>
          </div>
           
       
        
         
        <div class="tab">
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6> Status-Table</h6>
    <label for="name">Status-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_en_status" required value="{{\App\TranslatorPageContent::getName('fiat_status_key')->en_page_content}}">
    <label for="name">Status-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_fn_status" required value="{{\App\TranslatorPageContent::getName('fiat_status_key')->fn_page_content}}">
  <label for="name">Status-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_sp_status" required value="{{\App\TranslatorPageContent::getName('fiat_status_key')->sp_page_content}}">
 <label for="name">Status-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ab_status" required value="{{\App\TranslatorPageContent::getName('fiat_status_key')->ab_page_content}}">
<div class="col-md-6">
                    <label for="name">Status-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="fiat_ge_status" required value="{{\App\TranslatorPageContent::getName('fiat_status_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="fiat_status_content_key" required value="fiat_status_key">  
                </div>
                
              </div>
            </div>
          </div>
            
        
        
                 
                
            
      

      <!-- Modal footer -->
      <div class="modal-footer">
            <div style="overflow:auto;">
             <div style="float:right;">
                <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
             </div>
          </div>
      </div>
      <div style="text-align:center;margin-top:40px;">
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
  </div>
     </form>
            
        </div>
    </div>
</div>

<!-- The Modal -->

<!-- Edit Page Modal -->


<!-- Delete Page Modal -->



<script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the crurrent tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");


  // Exit the function if any field in the current tab is invalid:
 // if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  console.log(currentTab);
  if (currentTab >= x.length) {
    // ... the form gets submitted:
      document.getElementById("orders").submit();
    return false;
  }
  // Otherwise, display the correct tab:

  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}
</script>

@endsection
