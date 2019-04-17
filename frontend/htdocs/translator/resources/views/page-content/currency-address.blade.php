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
            <h4 class="page-title">Currency Address</h4>
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
                  
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6>Heading</h6>
                      <input type="hidden" name="id" value="$order">
                    <label for="name">Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_en_heading" required value="{{\App\TranslatorPageContent::getName('currency_heading_content_key')->en_page_content}}">
                <label for="name">Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_fn_heading" required value="{{\App\TranslatorPageContent::getName('currency_heading_content_key')->fn_page_content}}">
 <label for="name">Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_sp_heading" required value="{{\App\TranslatorPageContent::getName('currency_heading_content_key')->sp_page_content}}">
 <label for="name">Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ab_heading" required value="{{\App\TranslatorPageContent::getName('currency_heading_content_key')->ab_page_content}}">
<label for="name">Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ge_heading" required value="{{\App\TranslatorPageContent::getName('currency_heading_content_key')->ge_page_content}}">

                    <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="currency_address_heading_content_key" required value="currency_heading_content_key">
              
                </div>
                <div class="col-md-6">
                           <h6> Body-Heading</h6>
      <label for="name">Sub-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_en_body_heading" required value="{{\App\TranslatorPageContent::getName('currency_body_content_key')->en_page_content}}">
      <label for="name">Sub-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_fn_body_heading" required value="{{\App\TranslatorPageContent::getName('currency_body_content_key')->fn_page_content}}">
      <label for="name">Sub-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_sp_body_heading" required value="{{\App\TranslatorPageContent::getName('currency_body_content_key')->sp_page_content}}">
      <label for="name">Sub-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ab_body_heading" required value="{{\App\TranslatorPageContent::getName('currency_body_content_key')->ab_page_content}}">
      <label for="name">Sub-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ge_body_heading" required value="{{\App\TranslatorPageContent::getName('currency_body_content_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="currency_address_body_heading_content_key" required value="currency_body_content_key">
                </div>
              </div>
            </div>
          </div>
           
        
                 
         <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6> Currency</h6>
                    <label for="name">Currency-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_en_currency" required value="{{\App\TranslatorPageContent::getName('currency_content_key')->en_page_content}}">

                     <label for="name">Currency-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_fn_currency" required value="{{\App\TranslatorPageContent::getName('currency_content_key')->fn_page_content}}">


                    <label for="name">Currency-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_sp_currency" required value="{{\App\TranslatorPageContent::getName('currency_content_key')->sp_page_content}}">

                    <label for="name">Currency-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ab_currency" required value="{{\App\TranslatorPageContent::getName('currency_content_key')->ab_page_content}}">
<label for="name">Currency-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ge_currency" required value="{{\App\TranslatorPageContent::getName('currency_content_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="currency_address_currency_content_key" required value="currency_content_key">
                </div>
                <div class="col-md-6">
                   <h6> Button-content</h6>
 <label for="name">Content-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_en_button" required value="{{\App\TranslatorPageContent::getName('currency_address_content_key')->en_page_content}}">
 <label for="name">Content-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_fn_button" required value="{{\App\TranslatorPageContent::getName('currency_address_content_key')->fn_page_content}}">
 <label for="name">Content-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_sp_button" required value="{{\App\TranslatorPageContent::getName('currency_address_content_key')->sp_page_content}}">
 <label for="name">Content-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ab_button" required value="{{\App\TranslatorPageContent::getName('currency_address_content_key')->ab_page_content}}">
<label for="name">Content-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ge_button" required value="{{\App\TranslatorPageContent::getName('currency_address_content_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="currency_address_button_content_key" required value="currency_address_content_key">
                </div>
              </div>
            </div>
            </div>
         <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6> table-Heading</h6>
                    <label for="name">table-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_en_table" required value="{{\App\TranslatorPageContent::getName('currency_table_key')->en_page_content}}">
                <label for="name">Table-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_fn_table" required value="{{\App\TranslatorPageContent::getName('currency_table_key')->fn_page_content}}">
<label for="name">Table-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_sp_table" required value="{{\App\TranslatorPageContent::getName('currency_table_key')->sp_page_content}}">
<label for="name">Table-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ab_table" required value="{{\App\TranslatorPageContent::getName('currency_table_key')->ab_page_content}}">
<label for="name">Table-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ge_table" required value="{{\App\TranslatorPageContent::getName('currency_table_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="currency_address_table_content_key" required value="currency_table_key">
                </div>
                <div class="col-md-6">
                       <h6> Currency-Table</h6>
  <label for="name">Currency-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_en_table_currency" required value="{{\App\TranslatorPageContent::getName('currency1_table_key')->en_page_content}}">
  <label for="name">Currency-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_fn_table_currency" required value="{{\App\TranslatorPageContent::getName('currency1_table_key')->fn_page_content}}">
<label for="name">Currency-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_sp_table_currency" required
                     value="{{\App\TranslatorPageContent::getName('currency1_table_key')->sp_page_content}}">
<label for="name">Currency-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ab_table_currency" required value="{{\App\TranslatorPageContent::getName('currency1_table_key')->ab_page_content}}">
<label for="name">Currency-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ge_table_currency" required value="{{\App\TranslatorPageContent::getName('currency1_table_key')->ge_page_content}}">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="currency_address_table_currency_heading_content_key" required value="currency1_table_key">
                </div>
              </div>
            </div>
          </div>
            
         
        <div class="tab">
                   
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-6">
                      <h6> Datetime-Table</h6>
                    <label for="name">Datetime-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_en_datetime" required value="{{\App\TranslatorPageContent::getName('currency_datetime_key')->en_page_content}}">
               <label for="name">Datetime-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_fn_datetime" required value="{{\App\TranslatorPageContent::getName('currency_datetime_key')->fn_page_content}}"">
 <label for="name">Datetime-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_sp_datetime" required value="{{\App\TranslatorPageContent::getName('currency_datetime_key')->ab_page_content}}"">
<label for="name">Datetime-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ab_datetime" required value="{{\App\TranslatorPageContent::getName('currency_datetime_key')->ab_page_content}}"">
<label for="name">Datetime-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ge_datetime" required value="{{\App\TranslatorPageContent::getName('currency_datetime_key')->ge_page_content}}"">
<input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="currency_address_datetime_content_key" required value="currency_datetime_key">
                </div>
                <div class="col-md-6">
                     <h6> Address-Table</h6>
<label for="name">Address-Title({{$language[0]->language_symbol}})</label>
               <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_en_address" required value="{{\App\TranslatorPageContent::getName('currency_address_key')->en_page_content}}">
<label for="name">Address-Title({{$language[1]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_fn_address" required value="{{\App\TranslatorPageContent::getName('currency_address_key')->fn_page_content}}">
<label for="name">Address-Title({{$language[2]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_sp_address" required value="{{\App\TranslatorPageContent::getName('currency_address_key')->sp_page_content}}">
                
<label for="name">Address-Title({{$language[3]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ab_address" required value="{{\App\TranslatorPageContent::getName('currency_address_key')->ab_page_content}}">
  <label for="name">Address-Title({{$language[4]->language_symbol}})</label>
                    <input type="text" class="form-control" id="menu_name" placeholder="Enter Your Page" name="currency_address_ge_address" required value="{{\App\TranslatorPageContent::getName('currency_address_key')->ge_page_content}}">
 <input type="hidden" class="form-control" id="s" placeholder="Enter Your Page" name="currency_address_address_content_key" required value="currency_address_key">
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
