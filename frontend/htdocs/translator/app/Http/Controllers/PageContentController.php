<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PageContentRepository;
use App\Repositories\LanguageRepository;
use Validator;
use Auth;

class PageContentController extends BaseController
{
    protected $trans_PageContent;

    public function __construct(PageContentRepository $pagecontrepo,LanguageRepository $languagerepo)
    {
        $this->pagecontrepo = $pagecontrepo;
        $this->languagerepo = $languagerepo; 
      
    }
    
    public function showPageContent($id)
    {
      $page_id = $id;
      $page_content = $this->pagecontrepo->pageContent($page_id); 
      $page_content_view = $this->pagecontrepo->pageContentView($page_id); 
      $page_content_count = count($page_content);
      $language = $this->languagerepo->selectLanguageActive();
      $language_count = count($language);
      // echo "<br><br>";
      // print_r($page_content[6]->page_content);echo "<br><br>";
      // print_r($page_content_view);
        // print_r($page_content_count);
        // exit;
      return view($page_content_view, compact('page_content','page_id','page_content_count','language','language_count'));
    }

    // Login

     public function loginContentStore(Request $request)
    {
            
            $pagecont = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_heading_content_key ,$request->login_en_heading,$request->login_fn_heading,$request->login_sp_heading,$request->login_ab_heading,$request->login_gn_heading,1);
               
            $pagecont1 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_sub_heading_content_key,$request->login_en_sub_heading,$request->login_fn_sub_heading,$request->login_sp_sub_heading,$request->login_ab_sub_heading,$request->login_gn_sub_heading,1);
              
            $pagecont2 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_content_key,$request->login_en_content,$request->login_fn_content,$request->login_sp_content,$request->login_ab_content,$request->login_gn_content,1);
              
             $pagecont3 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_password_content_key,$request->login_en_password,$request->login_fn_password,$request->login_sp_password,$request->login_ab_password,$request->login_gn_password,1);
              
              $pagecont4 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_account_content_key,$request->login_en_account,$request->login_fn_account,$request->login_sp_account,$request->login_ab_account,$request->login_gn_account,1);
                
               $pagecont5 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_register_content_key,$request->login_en_register,$request->login_fn_register,$request->login_sp_register,$request->login_ab_register,$request->login_gn_register,1);

              $pagecont6 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_email_placeholder_key,$request->login_en_email_placeholder,$request->login_fn_email_placeholder,$request->login_sp_email_placeholder,$request->login_ab_email_placeholder,$request->login_gn_email_placeholder,1);

              $pagecont7 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_pass_placeholder_key,$request->login_en_pass_placeholder,$request->login_fn_pass_placeholder,$request->login_sp_pass_placeholder,$request->login_ab_pass_placeholder,$request->login_gn_pass_placeholder,1);
              
               return back()->with('success','Contane Created Successfully');
    }

     public function loginContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->login_heading_content_key ,$request->login_en_heading,$request->login_fn_heading,$request->login_sp_heading,$request->login_ab_heading,$request->login_gn_heading,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->login_sub_heading_content_key,$request->login_en_sub_heading,$request->login_fn_sub_heading,$request->login_sp_sub_heading,$request->login_ab_sub_heading,$request->login_gn_sub_heading,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->login_content_key,$request->login_en_content,$request->login_fn_content,$request->login_sp_content,$request->login_ab_content,$request->login_gn_content,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->login_password_content_key,$request->login_en_password,$request->login_fn_password,$request->login_sp_password,$request->login_ab_password,$request->login_gn_password,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->login_account_content_key,$request->login_en_account,$request->login_fn_account,$request->login_sp_account,$request->login_ab_account,$request->login_gn_account,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->login_register_content_key,$request->login_en_register,$request->login_fn_register,$request->login_sp_register,$request->login_sb_register,$request->login_gn_register,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->login_email_placeholder_key,$request->login_en_email_placeholder,$request->login_fn_email_placeholder,$request->login_sp_email_placeholder,$request->login_ab_email_placeholder,$request->login_gn_email_placeholder,1);

           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->login_pass_placeholder_key,$request->login_en_pass_placeholder,$request->login_fn_pass_placeholder,$request->login_sp_pass_placeholder,$request->login_ab_pass_placeholder,$request->login_gn_pass_placeholder,1);

      $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();
              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();
          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_value11 = 'login_'.$language->language_symbol.'_heading_key';
              $page_content_value21 = 'login_'.$language->language_symbol.'_sub_heading_key';
              $page_content_value31 = 'login_'.$language->language_symbol.'_button_key';
              $page_content_value41 = 'login_'.$language->language_symbol.'_password_key';
              $page_content_value51 = 'login_'.$language->language_symbol.'_account_key';
              $page_content_value61 = 'login_'.$language->language_symbol.'_register_content_key';
              $page_content_value71 = 'login_'.$language->language_symbol.'_email_placeholder_key';
              $page_content_value81 = 'login_'.$language->language_symbol.'_pass_placeholder_key';
              $symbol = $language->language_symbol;
              if (isset($request->$page_content_value11)) {
                   $page_content_value1['login_heading_key'] = $request->$page_content_value11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value21)) {
                   $page_content_value2['login_sub_heading_key'] = $request->$page_content_value21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value31)) {
                   $page_content_value3['login_button_key'] = $request->$page_content_value31;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value41)) {
                   $page_content_value4['login_password_key'] = $request->$page_content_value41;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value51)) {
                   $page_content_value5['login_account_key'] = $request->$page_content_value51;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value61)) {
                   $page_content_value6['login_register_content_key'] = $request->$page_content_value61;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value71)) {
                   $page_content_value7['login_email_placeholder_key'] = $request->$page_content_value71;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value81)) {
                   $page_content_value8['login_pass_placeholder_key'] = $request->$page_content_value81;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;
              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);
          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
            return back()->with('success','Updated Successfully');
            
    }

    // Register



     public function registerContentStore(Request $request)
    {

            
            $pagecont = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_heading_content_key ,$request->login_en_heading,$request->login_fn_heading,$request->login_sp_heading,$request->login_ab_heading,$request->login_gn_heading,1);
               
            $pagecont1 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_sub_heading_content_key,$request->login_en_sub_heading,$request->login_fn_sub_heading,$request->login_sp_sub_heading,$request->login_ab_sub_heading,$request->login_gn_sub_heading,1);
              
            $pagecont2 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_content_key,$request->login_en_content,$request->login_fn_content,$request->login_sp_content,$request->login_ab_content,$request->login_gn_content,1);
              
             $pagecont3 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_password_content_key,$request->login_en_password,$request->login_fn_password,$request->login_sp_password,$request->login_ab_password,$request->login_gn_password,1);
              
              $pagecont4 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_account_content_key,$request->login_en_account,$request->login_fn_account,$request->login_sp_account,$request->login_ab_account,$request->login_gn_account,1);
                
               $pagecont5 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_register_content_key,$request->login_en_register,$request->login_fn_register,$request->login_sp_register,$request->login_ab_register,$request->login_gn_register,1);

              $pagecont6 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_email_placeholder_key,$request->login_en_email_placeholder,$request->login_fn_email_placeholder,$request->login_sp_email_placeholder,$request->login_ab_email_placeholder,$request->login_gn_email_placeholder,1);

              $pagecont7 = $this->pagecontrepo->pageContentAdd($request->page_id,$request->login_pass_placeholder_key,$request->login_en_pass_placeholder,$request->login_fn_pass_placeholder,$request->login_sp_pass_placeholder,$request->login_ab_pass_placeholder,$request->login_gn_pass_placeholder,1);
              
               return back()->with('success','Contane Created Successfully');
    }
    
     public function registerContentEdit(Request $request)
    {
      // print_r($request);exit;
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->register_heading_key ,$request->register_en_heading,$request->register_fn_heading,$request->register_sp_heading,$request->register_ab_heading,$request->register_gn_heading,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->register_sub_heading_key,$request->register_en_sub_heading,$request->register_fn_sub_heading,$request->register_sp_sub_heading,$request->register_ab_sub_heading,$request->register_gn_sub_heading,1);
                  
           //    $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->register_button_key,$request->register_en_button,$request->register_fn_button,$request->register_sp_button,$request->register_ab_button,$request->register_gn_button,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->register_firstname_key,$request->register_en_name,$request->register_fn_name,$request->register_sp_name,$request->register_ab_name,$request->register_gn_name,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->register_lastname_key,$request->register_en_last_name,$request->register_fn_last_name,$request->register_sp_last_name,$request->register_ab_last_name,$request->register_gn_last_name,1);
             
           //    $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->register_email_key,$request->register_en_email,$request->register_fn_email,$request->register_sp_email,$request->register_ab_email,$request->register_gn_email,1);
                  
           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->register_phone_key,$request->register_en_phone,$request->register_fn_phone,$request->register_sp_phone,$request->register_ab_phone,$request->register_gn_phone,1);

           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->register_accept_key,$request->register_en_accept,$request->register_fn_accept,$request->register_sp_accept,$request->register_ab_accept,$request->register_gn_accept,1);

           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->register_terms_key,$request->register_en_term,$request->register_fn_term,$request->register_sp_term,$request->register_ab_term,$request->register_gn_term,1);

           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->register_use_site_key,$request->register_en_use_site_key,$request->register_fn_use_site_key,$request->register_sp_use_site_key,$request->register_ab_use_site_key,$request->register_gn_use_site_key,1);
                  
           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->register_already_key,$request->register_en_account,$request->register_fn_account,$request->register_sp_account,$request->register_ab_account,$request->register_gn_account,1);

           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->register_login_key,$request->register_en_login,$request->register_fn_login,$request->register_sp_login,$request->register_ab_login,$request->register_gn_login,1);

           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->register_referal_key,$request->register_en_referal,$request->register_fn_referal,$request->register_sp_referal,$request->register_ab_referal,$request->register_gn_referal,1);

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();
              $page_content_value_all_9 = array();$page_content_value_all_10 = array();
              $page_content_value_all_11 = array();$page_content_value_all_12 = array();
              $page_content_value_all_13 = array();

              $page_content_values1 = array();$page_content_values2 = array();$page_content_values3 = array();
              $page_content_values4 = array();$page_content_values5 = array();$page_content_values6 = array();
              $page_content_values7 = array();$page_content_values8 = array();$page_content_values9 = array();
              $page_content_values10 = array();$page_content_values11 = array();$page_content_values12 = array();$page_content_values13 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();
              $page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();
          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_values1 = 'register_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'register_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'register_'.$language->language_symbol.'_button_key';
              $page_content_values4 = 'register_'.$language->language_symbol.'_firstname_key';
              $page_content_values5 = 'register_'.$language->language_symbol.'_lastname_key';
              $page_content_values6 = 'register_'.$language->language_symbol.'_email_key';
              $page_content_values7 = 'register_'.$language->language_symbol.'_phone_key';
              $page_content_values8 = 'register_'.$language->language_symbol.'_accept_key';
              $page_content_values9 = 'register_'.$language->language_symbol.'_terms_key';
              $page_content_values10 = 'register_'.$language->language_symbol.'_use_site_key';
              $page_content_values11 = 'register_'.$language->language_symbol.'_already_key';
              $page_content_values12 = 'register_'.$language->language_symbol.'_login_key';
              $page_content_values13 = 'register_'.$language->language_symbol.'_referal_key';
              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['register_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['register_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['register_button_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['register_firstname_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['register_lastname_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['register_email_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['register_phone_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values8)) {
                   $page_content_value8['register_accept_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['register_terms_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['register_use_site_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['register_already_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['register_login_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['register_referal_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;
              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);
          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);
          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);

            return back()->with('success','Updated Successfully');
            
    }

    //  Forgot Password



     public function forgotContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->forgot_pass_heading_key ,$request->forgot_pass_en_heading_key,$request->forgot_pass_fn_heading_key,$request->forgot_pass_sp_heading_key,$request->forgot_pass_ab_heading_key,$request->forgot_pass_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->forgot_pass_placeholder_key,$request->forgot_pass_en_placeholder_key,$request->forgot_pass_fn_placeholder_key,$request->forgot_pass_sp_placeholder_key,$request->forgot_pass_ab_placeholder_key,$request->forgot_pass_gn_placeholder_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->forgot_pass_button_key,$request->forgot_pass_en_button_key,$request->forgot_pass_fn_button_key,$request->forgot_pass_sp_button_key,$request->forgot_pass_ab_button_key,$request->forgot_pass_gn_button_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->forgot_pass_account_key,$request->forgot_pass_en_account_key,$request->forgot_pass_fn_account_key,$request->forgot_pass_sp_account_key,$request->forgot_pass_ab_account_key,$request->forgot_pass_gn_account_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->forgot_pass_register_key,$request->forgot_pass_en_register_key,$request->forgot_pass_fn_register_key,$request->forgot_pass_sp_register_key,$request->forgot_pass_ab_register_key,$request->forgot_pass_gn_register_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->login_already_key,$request->login_en_already_key,$request->login_fn_already_key,$request->login_sp_already_key,$request->login_ab_already_key,$request->login_gn_already_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->login_heading_key,$request->login_en_heading_key,$request->login_fn_heading_key,$request->login_sp_heading_key,$request->login_ab_heading_key,$request->login_gn_heading_key,1);

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();
              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();
          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_value11 = 'forgot_'.$language->language_symbol.'_pass_heading_key';
              $page_content_value21 = 'forgot_'.$language->language_symbol.'_pass_placeholder_key';
              $page_content_value31 = 'forgot_'.$language->language_symbol.'_pass_button_key';
              $page_content_value41 = 'forgot_'.$language->language_symbol.'_pass_account_key';
              $page_content_value51 = 'forgot_'.$language->language_symbol.'_pass_register_key';
              $page_content_value61 = 'login_'.$language->language_symbol.'_already_key';
              $page_content_value71 = 'login_'.$language->language_symbol.'_heading_key';
              $symbol = $language->language_symbol;
              if (isset($request->$page_content_value11)) {
                   $page_content_value1['forgot_pass_heading_key'] = $request->$page_content_value11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value21)) {
                   $page_content_value2['forgot_pass_placeholder_key'] = $request->$page_content_value21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value31)) {
                   $page_content_value3['forgot_pass_button_key'] = $request->$page_content_value31;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value41)) {
                   $page_content_value4['forgot_pass_account_key'] = $request->$page_content_value41;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value51)) {
                   $page_content_value5['forgot_pass_register_key'] = $request->$page_content_value51;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value61)) {
                   $page_content_value6['login_already_key'] = $request->$page_content_value61;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value71)) {
                   $page_content_value7['login_heading_key'] = $request->$page_content_value71;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);



            return back()->with('success','Updated Successfully');
            
    }

    //  Open Orders



     public function openorderContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->open_order_heading_key ,$request->open_order_en_heading_key,$request->open_order_fn_heading_key,$request->open_order_sp_heading_key,$request->open_order_ab_heading_key,$request->open_order_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->open_order_sub_heading_key,$request->open_order_en_sub_heading_key,$request->open_order_fn_sub_heading_key,$request->open_order_sp_sub_heading_key,$request->open_order_ab_sub_heading_key,$request->open_order_gn_sub_heading_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->open_order_currency_pair_key,$request->open_order_en_currency_pair_key,$request->open_order_fn_currency_pair_key,$request->open_order_sp_currency_pair_key,$request->open_order_ab_currency_pair_key,$request->open_order_gn_currency_pair_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->open_order_currency_pair_modal_head_key,$request->open_order_en_currency_pair_modal_head_key,$request->open_order_fn_currency_pair_modal_head_key,$request->open_order_sp_currency_pair_modal_head_key,$request->open_order_ab_currency_pair_modal_head_key,$request->open_order_gn_currency_pair_modal_head_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->open_order_currency_pair_modal_content_key,$request->open_order_en_currency_pair_modal_content_key,$request->open_order_fn_currency_pair_modal_content_key,$request->open_order_sp_currency_pair_modal_content_key,$request->open_order_ab_currency_pair_modal_content_key,$request->open_order_gn_currency_pair_modal_content_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->open_order_currency_pair_modal_amount_key,$request->open_order_en_currency_pair_modal_amount_key,$request->open_order_fn_currency_pair_modal_amount_key,$request->open_order_sp_currency_pair_modal_amount_key,$request->open_order_ab_currency_pair_modal_amount_key,$request->open_order_gn_currency_pair_modal_amount_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->open_order_currency_pair_modal_value_key,$request->open_order_en_currency_pair_modal_value_key,$request->open_order_fn_currency_pair_modal_value_key,$request->open_order_sp_currency_pair_modal_value_key,$request->open_order_ab_currency_pair_modal_value_key,$request->open_order_gn_currency_pair_modal_value_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->open_order_currency_pair_modal_price_key,$request->open_order_en_currency_pair_modal_price_key,$request->open_order_fn_currency_pair_modal_price_key,$request->open_order_sp_currency_pair_modal_price_key,$request->open_order_ab_currency_pair_modal_price_key,$request->open_order_gn_currency_pair_modal_price_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->open_order_currency_pair_modal_fee_key,$request->open_order_en_currency_pair_modal_fee_key,$request->open_order_fn_currency_pair_modal_fee_key,$request->open_order_sp_currency_pair_modal_fee_key,$request->open_order_ab_currency_pair_modal_fee_key,$request->open_order_gn_currency_pair_modal_fee_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->open_order_buy_order_key,$request->open_order_en_buy_order_key,$request->open_order_fn_buy_order_key,$request->open_order_sp_buy_order_key,$request->open_order_ab_buy_order_key,$request->open_order_gn_buy_order_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->open_order_sell_order_key,$request->open_order_en_sell_order_key,$request->open_order_fn_sell_order_key,$request->open_order_sp_sell_order_key,$request->open_order_ab_sell_order_key,$request->open_order_gn_sell_order_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->open_order_table_head_type_key,$request->open_order_en_table_head_type_key,$request->open_order_fn_table_head_type_key,$request->open_order_sp_table_head_type_key,$request->open_order_ab_table_head_type_key,$request->open_order_gn_table_head_type_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->open_order_table_head_date_key,$request->open_order_en_table_head_date_key,$request->open_order_fn_table_head_date_key,$request->open_order_sp_table_head_date_key,$request->open_order_ab_table_head_date_key,$request->open_order_gn_table_head_date_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->open_order_table_head_price_key,$request->open_order_en_table_head_price_key,$request->open_order_fn_table_head_price_key,$request->open_order_sp_table_head_price_key,$request->open_order_ab_table_head_price_key,$request->open_order_gn_table_head_price_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->open_order_table_head_amount_key,$request->open_order_en_table_head_amount_key,$request->open_order_fn_table_head_amount_key,$request->open_order_sp_table_head_amount_key,$request->open_order_ab_table_head_amount_key,$request->open_order_gn_table_head_amount_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->open_order_table_head_value_key,$request->open_order_en_table_head_value_key,$request->open_order_fn_table_head_value_key,$request->open_order_sp_table_head_value_key,$request->open_order_ab_table_head_value_key,$request->open_order_gn_table_head_value_key,1);


           //    $pagecont16 = $this->pagecontrepo->pageContentUpdate($request->page_content_id16,$request->open_order_table_head_action_key,$request->open_order_en_table_head_action_key,$request->open_order_fn_table_head_action_key,$request->open_order_sp_table_head_action_key,$request->open_order_ab_table_head_action_key,$request->open_order_gn_table_head_action_key,1);


           //    $pagecont17 = $this->pagecontrepo->pageContentUpdate($request->page_content_id17,$request->open_order_table_buy_no_value_key,$request->open_order_en_table_buy_no_value_key,$request->open_order_fn_table_buy_no_value_key,$request->open_order_sp_table_buy_no_value_key,$request->open_order_ab_table_buy_no_value_key,$request->open_order_gn_table_buy_no_value_key,1);


           //    $pagecont18 = $this->pagecontrepo->pageContentUpdate($request->page_content_id18,$request->open_order_table_sell_no_value_key,$request->open_order_en_table_sell_no_value_key,$request->open_order_fn_table_sell_no_value_key,$request->open_order_sp_table_sell_no_value_key,$request->open_order_ab_table_sell_no_value_key,$request->open_order_gn_table_sell_no_value_key,1);

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();$page_content_value_all_9 = array();$page_content_value_all_10 = array();$page_content_value_all_11 = array();$page_content_value_all_12 = array();$page_content_value_all_13 = array();$page_content_value_all_14 = array();$page_content_value_all_15 = array();$page_content_value_all_16 = array();$page_content_value_all_17 = array();$page_content_value_all_18 = array();$page_content_value_all_19 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();$page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();$page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();$page_content_value19 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_values1 = 'open_order_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'open_order_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'open_order_'.$language->language_symbol.'_currency_pair_key';
              $page_content_values4 = 'open_order_'.$language->language_symbol.'_currency_pair_modal_head_key';
              $page_content_values5 = 'open_order_'.$language->language_symbol.'_currency_pair_modal_content_key';
              $page_content_values6 = 'open_order_'.$language->language_symbol.'_currency_pair_modal_amount_key';
              $page_content_values7 = 'open_order_'.$language->language_symbol.'_currency_pair_modal_value_key';
              $page_content_values8 = 'open_order_'.$language->language_symbol.'_currency_pair_modal_price_key';
              $page_content_values9 = 'open_order_'.$language->language_symbol.'_currency_pair_modal_fee_key';
              $page_content_values10 = 'open_order_'.$language->language_symbol.'_buy_order_key';
              $page_content_values11 = 'open_order_'.$language->language_symbol.'_sell_order_key';
              $page_content_values12 = 'open_order_'.$language->language_symbol.'_table_head_type_key';
              $page_content_values13 = 'open_order_'.$language->language_symbol.'_table_head_date_key';
              $page_content_values14 = 'open_order_'.$language->language_symbol.'_table_head_price_key';
              $page_content_values15 = 'open_order_'.$language->language_symbol.'_table_head_amount_key';
              $page_content_values16 = 'open_order_'.$language->language_symbol.'_table_head_value_key';
              $page_content_values17 = 'open_order_'.$language->language_symbol.'_table_head_action_key';
              $page_content_values18 = 'open_order_'.$language->language_symbol.'_table_buy_no_value_key';
              $page_content_values19 = 'open_order_'.$language->language_symbol.'_table_sell_no_value_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['open_order_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['open_order_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['open_order_currency_pair_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['open_order_currency_pair_modal_head_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['open_order_currency_pair_modal_content_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['open_order_currency_pair_modal_amount_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['open_order_currency_pair_modal_value_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['open_order_currency_pair_modal_price_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['open_order_currency_pair_modal_fee_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['open_order_buy_order_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['open_order_sell_order_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['open_order_table_head_type_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['open_order_table_head_date_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['open_order_table_head_price_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['open_order_table_head_amount_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['open_order_table_head_value_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['open_order_table_head_action_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['open_order_table_buy_no_value_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values19)) {
                   $page_content_value19['open_order_table_sell_no_value_key'] = $request->$page_content_values19;
                   // echo $page_content_value1."string1<br>";
              }

              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              $page_content_value_all_19[$language->language_symbol] = $page_content_value19;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);
          $pagecont19 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id18,$page_content_value_all_19,1);


            return back()->with('success','Updated Successfully');
            
    } 


    //  Trade History

     public function tradehistoryContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->trade_history_heading_key ,$request->trade_history_en_heading_key,$request->trade_history_fn_heading_key,$request->trade_history_sp_heading_key,$request->trade_history_ab_heading_key,$request->trade_history_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->trade_history_sub_heading_key,$request->trade_history_en_sub_heading_key,$request->trade_history_fn_sub_heading_key,$request->trade_history_sp_sub_heading_key,$request->trade_history_ab_sub_heading_key,$request->trade_history_gn_sub_heading_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->trade_history_currency_pair_key,$request->trade_history_en_currency_pair_key,$request->trade_history_fn_currency_pair_key,$request->trade_history_sp_currency_pair_key,$request->trade_history_ab_currency_pair_key,$request->trade_history_gn_currency_pair_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->trade_history_currency_pair_modal_head_key,$request->trade_history_en_currency_pair_modal_head_key,$request->trade_history_fn_currency_pair_modal_head_key,$request->trade_history_sp_currency_pair_modal_head_key,$request->trade_history_ab_currency_pair_modal_head_key,$request->trade_history_gn_currency_pair_modal_head_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->trade_history_currency_pair_modal_content_key,$request->trade_history_en_currency_pair_modal_content_key,$request->trade_history_fn_currency_pair_modal_content_key,$request->trade_history_sp_currency_pair_modal_content_key,$request->trade_history_ab_currency_pair_modal_content_key,$request->trade_history_gn_currency_pair_modal_content_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->trade_history_currency_pair_modal_amount_key,$request->trade_history_en_currency_pair_modal_amount_key,$request->trade_history_fn_currency_pair_modal_amount_key,$request->trade_history_sp_currency_pair_modal_amount_key,$request->trade_history_ab_currency_pair_modal_amount_key,$request->trade_history_gn_currency_pair_modal_amount_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->trade_history_currency_pair_modal_value_key,$request->trade_history_en_currency_pair_modal_value_key,$request->trade_history_fn_currency_pair_modal_value_key,$request->trade_history_sp_currency_pair_modal_value_key,$request->trade_history_ab_currency_pair_modal_value_key,$request->trade_history_gn_currency_pair_modal_value_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->trade_history_currency_pair_modal_price_key,$request->trade_history_en_currency_pair_modal_price_key,$request->trade_history_fn_currency_pair_modal_price_key,$request->trade_history_sp_currency_pair_modal_price_key,$request->trade_history_ab_currency_pair_modal_price_key,$request->trade_history_gn_currency_pair_modal_price_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->trade_history_currency_pair_modal_fee_key,$request->trade_history_en_currency_pair_modal_fee_key,$request->trade_history_fn_currency_pair_modal_fee_key,$request->trade_history_sp_currency_pair_modal_fee_key,$request->trade_history_ab_currency_pair_modal_fee_key,$request->trade_history_gn_currency_pair_modal_fee_key,1);

           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->trade_history_table_head_type_key,$request->trade_history_en_table_head_type_key,$request->trade_history_fn_table_head_type_key,$request->trade_history_sp_table_head_type_key,$request->trade_history_ab_table_head_type_key,$request->trade_history_gn_table_head_type_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->trade_history_table_head_date_key,$request->trade_history_en_table_head_date_key,$request->trade_history_fn_table_head_date_key,$request->trade_history_sp_table_head_date_key,$request->trade_history_ab_table_head_date_key,$request->trade_history_gn_table_head_date_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->trade_history_table_head_amount_key,$request->trade_history_en_table_head_amount_key,$request->trade_history_fn_table_head_amount_key,$request->trade_history_sp_table_head_amount_key,$request->trade_history_ab_table_head_amount_key,$request->trade_history_gn_table_head_amount_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->trade_history_table_head_value_key,$request->trade_history_en_table_head_value_key,$request->trade_history_fn_table_head_value_key,$request->trade_history_sp_table_head_value_key,$request->trade_history_ab_table_head_value_key,$request->trade_history_gn_table_head_value_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->trade_history_table_head_price_key,$request->trade_history_en_table_head_price_key,$request->trade_history_fn_table_head_price_key,$request->trade_history_sp_table_head_price_key,$request->trade_history_ab_table_head_price_key,$request->trade_history_gn_table_head_price_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->trade_history_table_head_fee_key,$request->trade_history_en_table_head_fee_key,$request->trade_history_fn_table_head_fee_key,$request->trade_history_sp_table_head_fee_key,$request->trade_history_ab_table_head_fee_key,$request->trade_history_gn_table_head_fee_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->trade_history_table_no_value_key,$request->trade_history_en_table_no_value_key,$request->trade_history_fn_table_no_value_key,$request->trade_history_sp_table_no_value_key,$request->trade_history_ab_table_no_value_key,$request->trade_history_gn_table_no_value_key,1);
      $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();$page_content_value_all_9 = array();$page_content_value_all_10 = array();$page_content_value_all_11 = array();$page_content_value_all_12 = array();$page_content_value_all_13 = array();$page_content_value_all_14 = array();$page_content_value_all_15 = array();$page_content_value_all_16 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();$page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();$page_content_value16 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_values1 = 'trade_history_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'trade_history_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'trade_history_'.$language->language_symbol.'_currency_pair_key';
              $page_content_values4 = 'trade_history_'.$language->language_symbol.'_currency_pair_modal_head_key';
              $page_content_values5 = 'trade_history_'.$language->language_symbol.'_currency_pair_modal_content_key';
              $page_content_values6 = 'trade_history_'.$language->language_symbol.'_currency_pair_modal_amount_key';
              $page_content_values7 = 'trade_history_'.$language->language_symbol.'_currency_pair_modal_value_key';


              $page_content_values8 = 'trade_history_'.$language->language_symbol.'_currency_pair_modal_price_key';
              $page_content_values9 = 'trade_history_'.$language->language_symbol.'_currency_pair_modal_fee_key';
              $page_content_values10 = 'trade_history_'.$language->language_symbol.'_table_head_type_key';
              $page_content_values11 = 'trade_history_'.$language->language_symbol.'_table_head_date_key';
              $page_content_values12 = 'trade_history_'.$language->language_symbol.'_table_head_amount_key';
              $page_content_values13 = 'trade_history_'.$language->language_symbol.'_table_head_value_key';
              $page_content_values14 = 'trade_history_'.$language->language_symbol.'_table_head_price_key';
              $page_content_values15 = 'trade_history_'.$language->language_symbol.'_table_head_fee_key';
              $page_content_values16 = 'trade_history_'.$language->language_symbol.'_table_no_value_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['trade_history_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['trade_history_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['trade_history_currency_pair_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['trade_history_currency_pair_modal_head_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['trade_history_currency_pair_modal_content_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['trade_history_currency_pair_modal_amount_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['trade_history_currency_pair_modal_value_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['trade_history_currency_pair_modal_price_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['trade_history_currency_pair_modal_fee_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['trade_history_table_head_type_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['trade_history_table_head_date_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['trade_history_table_head_amount_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['trade_history_table_head_value_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['trade_history_table_head_price_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['trade_history_table_head_fee_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['trade_history_table_no_value_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }

              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);


            return back()->with('success','Updated Successfully');
            
    }

    //  Order History



     public function orderhistoryContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->order_history_heading_key ,$request->order_history_en_heading_key,$request->order_history_fn_heading_key,$request->order_history_sp_heading_key,$request->order_history_ab_heading_key,$request->order_history_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->order_history_sub_heading_key,$request->order_history_en_sub_heading_key,$request->order_history_fn_sub_heading_key,$request->order_history_sp_sub_heading_key,$request->order_history_ab_sub_heading_key,$request->order_history_gn_sub_heading_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->order_history_currency_pair_key,$request->order_history_en_currency_pair_key,$request->order_history_fn_currency_pair_key,$request->order_history_sp_currency_pair_key,$request->order_history_ab_currency_pair_key,$request->order_history_gn_currency_pair_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->order_history_currency_pair_modal_head_key,$request->order_history_en_currency_pair_modal_head_key,$request->order_history_fn_currency_pair_modal_head_key,$request->order_history_sp_currency_pair_modal_head_key,$request->order_history_ab_currency_pair_modal_head_key,$request->order_history_gn_currency_pair_modal_head_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->order_history_currency_pair_modal_content_key,$request->order_history_en_currency_pair_modal_content_key,$request->order_history_fn_currency_pair_modal_content_key,$request->order_history_sp_currency_pair_modal_content_key,$request->order_history_ab_currency_pair_modal_content_key,$request->order_history_gn_currency_pair_modal_content_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->order_history_currency_pair_modal_amount_key,$request->order_history_en_currency_pair_modal_amount_key,$request->order_history_fn_currency_pair_modal_amount_key,$request->order_history_sp_currency_pair_modal_amount_key,$request->order_history_ab_currency_pair_modal_amount_key,$request->order_history_gn_currency_pair_modal_amount_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->order_history_currency_pair_modal_value_key,$request->order_history_en_currency_pair_modal_value_key,$request->order_history_fn_currency_pair_modal_value_key,$request->order_history_sp_currency_pair_modal_value_key,$request->order_history_ab_currency_pair_modal_value_key,$request->order_history_gn_currency_pair_modal_value_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->order_history_currency_pair_modal_price_key,$request->order_history_en_currency_pair_modal_price_key,$request->order_history_fn_currency_pair_modal_price_key,$request->order_history_sp_currency_pair_modal_price_key,$request->order_history_ab_currency_pair_modal_price_key,$request->order_history_gn_currency_pair_modal_price_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->order_history_currency_pair_modal_fee_key,$request->order_history_en_currency_pair_modal_fee_key,$request->order_history_fn_currency_pair_modal_fee_key,$request->order_history_sp_currency_pair_modal_fee_key,$request->order_history_ab_currency_pair_modal_fee_key,$request->order_history_gn_currency_pair_modal_fee_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->order_history_buy_order_key,$request->order_history_en_buy_order_key,$request->order_history_fn_buy_order_key,$request->order_history_sp_buy_order_key,$request->order_history_ab_buy_order_key,$request->order_history_gn_buy_order_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->order_history_sell_order_key,$request->order_history_en_sell_order_key,$request->order_history_fn_sell_order_key,$request->order_history_sp_sell_order_key,$request->order_history_ab_sell_order_key,$request->order_history_gn_sell_order_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->order_history_table_head_type_key,$request->order_history_en_table_head_type_key,$request->order_history_fn_table_head_type_key,$request->order_history_sp_table_head_type_key,$request->order_history_ab_table_head_type_key,$request->order_history_gn_table_head_type_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->order_history_table_head_date_key,$request->order_history_en_table_head_date_key,$request->order_history_fn_table_head_date_key,$request->order_history_sp_table_head_date_key,$request->order_history_ab_table_head_date_key,$request->order_history_gn_table_head_date_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->order_history_table_head_price_key,$request->order_history_en_table_head_price_key,$request->order_history_fn_table_head_price_key,$request->order_history_sp_table_head_price_key,$request->order_history_ab_table_head_price_key,$request->order_history_gn_table_head_price_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->order_history_table_head_amount_key,$request->order_history_en_table_head_amount_key,$request->order_history_fn_table_head_amount_key,$request->order_history_sp_table_head_amount_key,$request->order_history_ab_table_head_amount_key,$request->order_history_gn_table_head_amount_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->order_history_table_head_value_key,$request->order_history_en_table_head_value_key,$request->order_history_fn_table_head_value_key,$request->order_history_sp_table_head_value_key,$request->order_history_ab_table_head_value_key,$request->order_history_gn_table_head_value_key,1);


           //    $pagecont16 = $this->pagecontrepo->pageContentUpdate($request->page_content_id16,$request->order_history_table_buy_no_value_key,$request->order_history_en_table_buy_no_value_key,$request->order_history_fn_table_buy_no_value_key,$request->order_history_sp_table_buy_no_value_key,$request->order_history_ab_table_buy_no_value_key,$request->order_history_gn_table_buy_no_value_key,1);


           //    $pagecont17 = $this->pagecontrepo->pageContentUpdate($request->page_content_id17,$request->order_history_table_sell_no_value_key,$request->order_history_en_table_sell_no_value_key,$request->order_history_fn_table_sell_no_value_key,$request->order_history_sp_table_sell_no_value_key,$request->order_history_ab_table_sell_no_value_key,$request->order_history_gn_table_sell_no_value_key,1);

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();
              $page_content_value_all_9 = array();$page_content_value_all_10 = array();
              $page_content_value_all_11 = array();$page_content_value_all_12 = array();
              $page_content_value_all_13 = array();$page_content_value_all_14 = array();
              $page_content_value_all_15 = array();$page_content_value_all_16 = array();
              $page_content_value_all_17 = array();$page_content_value_all_18 = array();
              $page_content_value_all_19 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();
              $page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();
              $page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();
              $page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();
              $page_content_value19 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

                           $page_content_values1 = 'order_history_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'order_history_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'order_history_'.$language->language_symbol.'_currency_pair_key';
              $page_content_values4 = 'order_history_'.$language->language_symbol.'_currency_pair_modal_head_key';
              $page_content_values5 = 'order_history_'.$language->language_symbol.'_currency_pair_modal_content_key';
              $page_content_values6 = 'order_history_'.$language->language_symbol.'_currency_pair_modal_amount_key';
              $page_content_values7 = 'order_history_'.$language->language_symbol.'_currency_pair_modal_value_key';
              $page_content_values8 = 'order_history_'.$language->language_symbol.'_currency_pair_modal_price_key';
              $page_content_values9 = 'order_history_'.$language->language_symbol.'_currency_pair_modal_fee_key';
              $page_content_values10 = 'order_history_'.$language->language_symbol.'_buy_order_key';
              $page_content_values11 = 'order_history_'.$language->language_symbol.'_sell_order_key';
              $page_content_values12 = 'order_history_'.$language->language_symbol.'_table_head_type_key';
              $page_content_values13 = 'order_history_'.$language->language_symbol.'_table_head_date_key';
              $page_content_values14 = 'order_history_'.$language->language_symbol.'_table_head_price_key';
              $page_content_values15 = 'order_history_'.$language->language_symbol.'_table_head_amount_key';
              $page_content_values16 = 'order_history_'.$language->language_symbol.'_table_head_value_key';
              $page_content_values17 = 'order_history_'.$language->language_symbol.'_table_buy_no_value_key';
              $page_content_values18 = 'order_history_'.$language->language_symbol.'_table_sell_no_value_key';


              $symbol = $language->language_symbol;
              // print_r($request->$page_content_values1);exit();
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['order_history_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['order_history_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['order_history_currency_pair_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['order_history_currency_pair_modal_head_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['order_history_currency_pair_modal_content_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['order_history_currency_pair_modal_amount_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['order_history_currency_pair_modal_value_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['order_history_currency_pair_modal_price_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['order_history_currency_pair_modal_fee_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['order_history_buy_order_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['order_history_sell_order_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['order_history_table_head_type_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['order_history_table_head_date_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['order_history_table_head_price_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['order_history_table_head_amount_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['order_history_table_head_value_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['order_history_table_buy_no_value_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['order_history_table_sell_no_value_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }

              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);

            return back()->with('success','Updated Successfully');
            
    } 

    //  Crypto Address



     public function cryptoaddressContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->crypto_address_heading_key ,$request->crypto_address_en_heading_key,$request->crypto_address_fn_heading_key,$request->crypto_address_sp_heading_key,$request->crypto_address_ab_heading_key,$request->crypto_address_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->crypto_address_contect_key,$request->crypto_address_en_contect_key,$request->crypto_address_fn_contect_key,$request->crypto_address_sp_contect_key,$request->crypto_address_ab_contect_key,$request->crypto_address_gn_contect_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->crypto_address_button_key,$request->crypto_address_en_button_key,$request->crypto_address_fn_button_key,$request->crypto_address_sp_button_key,$request->crypto_address_ab_button_key,$request->crypto_address_gn_button_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->crypto_address_create_address_wrong_key,$request->crypto_address_en_create_address_wrong_key,$request->crypto_address_fn_create_address_wrong_key,$request->crypto_address_sp_create_address_wrong_key,$request->crypto_address_ab_create_address_wrong_key,$request->crypto_address_gn_create_address_wrong_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->crypto_address_create_address_wrong_success_key,$request->crypto_address_en_create_address_wrong_success_key,$request->crypto_address_fn_create_address_wrong_success_key,$request->crypto_address_sp_create_address_wrong_success_key,$request->crypto_address_ab_create_address_wrong_success_key,$request->crypto_address_gn_create_address_wrong_success_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->crypto_address_table_head_currency_key,$request->crypto_address_en_table_head_currency_key,$request->crypto_address_fn_table_head_currency_key,$request->crypto_address_sp_table_head_currency_key,$request->crypto_address_ab_table_head_currency_key,$request->crypto_address_gn_table_head_currency_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->crypto_address_table_head_date_key,$request->crypto_address_en_table_head_date_key,$request->crypto_address_fn_table_head_date_key,$request->crypto_address_sp_table_head_date_key,$request->crypto_address_ab_table_head_date_key,$request->crypto_address_gn_table_head_date_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->crypto_address_table_head_address_key,$request->crypto_address_en_table_head_address_key,$request->crypto_address_fn_table_head_address_key,$request->crypto_address_sp_table_head_address_key,$request->crypto_address_ab_table_head_address_key,$request->crypto_address_gn_table_head_address_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->crypto_address_table_no_address_key,$request->crypto_address_en_table_no_address_key,$request->crypto_address_fn_table_no_address_key,$request->crypto_address_sp_table_no_address_key,$request->crypto_address_ab_table_no_address_key,$request->crypto_address_gn_table_no_address_key,1);

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();
              $page_content_value_all_9 = array();
              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();
          foreach ($languages as $key => $language) {
            // if ($title > 4) {

                           $page_content_value11 = 'crypto_address_'.$language->language_symbol.'_heading_key';
              $page_content_value21 = 'crypto_address_'.$language->language_symbol.'_contect_key';
              $page_content_value31 = 'crypto_address_'.$language->language_symbol.'_button_key';
              $page_content_value41 = 'crypto_address_'.$language->language_symbol.'_create_address_wrong_key';
              $page_content_value51 = 'crypto_address_'.$language->language_symbol.'_create_address_wrong_success_key';
              $page_content_value61 = 'crypto_address_'.$language->language_symbol.'_table_head_currency_key';
              $page_content_value71 = 'crypto_address_'.$language->language_symbol.'_table_head_date_key';
              $page_content_value81 = 'crypto_address_'.$language->language_symbol.'_table_head_address_key';
              $page_content_value91 = 'crypto_address_'.$language->language_symbol.'_table_no_address_key';
              $symbol = $language->language_symbol;
              if (isset($request->$page_content_value11)) {
                   $page_content_value1['crypto_address_heading_key'] = $request->$page_content_value11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value21)) {
                   $page_content_value2['crypto_address_contect_key'] = $request->$page_content_value21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value31)) {
                   $page_content_value3['crypto_address_button_key'] = $request->$page_content_value31;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value41)) {
                   $page_content_value4['crypto_address_create_address_wrong_key'] = $request->$page_content_value41;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value51)) {
                   $page_content_value5['crypto_address_create_address_wrong_success_key'] = $request->$page_content_value51;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value61)) {
                   $page_content_value6['crypto_address_table_head_currency_key'] = $request->$page_content_value61;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value71)) {
                   $page_content_value7['crypto_address_table_head_date_key'] = $request->$page_content_value71;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value81)) {
                   $page_content_value8['crypto_address_table_head_address_key'] = $request->$page_content_value81;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value91)) {
                   $page_content_value9['crypto_address_table_no_address_key'] = $request->$page_content_value91;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;
              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);
          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          


            return back()->with('success','Updated Successfully');            
    } 

    //  Deposit



     public function depositContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->deposit_heading_key ,$request->deposit_en_heading_key,$request->deposit_fn_heading_key,$request->deposit_sp_heading_key,$request->deposit_ab_heading_key,$request->deposit_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->deposit_sub_heading_key,$request->deposit_en_sub_heading_key,$request->deposit_fn_sub_heading_key,$request->deposit_sp_sub_heading_key,$request->deposit_ab_sub_heading_key,$request->deposit_gn_sub_heading_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->deposit_heading_button_key,$request->deposit_en_heading_button_key,$request->deposit_fn_heading_button_key,$request->deposit_sp_heading_button_key,$request->deposit_ab_heading_button_key,$request->deposit_gn_heading_button_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->deposit_fiat_currency_trans_key,$request->deposit_en_fiat_currency_trans_key,$request->deposit_fn_fiat_currency_trans_key,$request->deposit_sp_fiat_currency_trans_key,$request->deposit_ab_fiat_currency_trans_key,$request->deposit_gn_fiat_currency_trans_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->deposit_fiat_currency_trans_modal_head_key,$request->deposit_en_fiat_currency_trans_modal_head_key,$request->deposit_fn_fiat_currency_trans_modal_head_key,$request->deposit_sp_fiat_currency_trans_modal_head_key,$request->deposit_ab_fiat_currency_trans_modal_head_key,$request->deposit_gn_fiat_currency_trans_modal_head_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->deposit_fiat_currency_trans_modal_here_key,$request->deposit_en_fiat_currency_trans_modal_here_key,$request->deposit_fn_fiat_currency_trans_modal_here_key,$request->deposit_sp_fiat_currency_trans_modal_here_key,$request->deposit_ab_fiat_currency_trans_modal_here_key,$request->deposit_gn_fiat_currency_trans_modal_here_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->deposit_fiat_currency_trans_modal_li1_key,$request->deposit_en_fiat_currency_trans_modal_li1_key,$request->deposit_fn_fiat_currency_trans_modal_li1_key,$request->deposit_sp_fiat_currency_trans_modal_li1_key,$request->deposit_ab_fiat_currency_trans_modal_li1_key,$request->deposit_gn_fiat_currency_trans_modal_li1_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->deposit_fiat_currency_trans_modal_li2_key,$request->deposit_en_fiat_currency_trans_modal_li2_key,$request->deposit_fn_fiat_currency_trans_modal_li2_key,$request->deposit_sp_fiat_currency_trans_modal_li2_key,$request->deposit_ab_fiat_currency_trans_modal_li2_key,$request->deposit_gn_fiat_currency_trans_modal_li2_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->deposit_fiat_currency_trans_modal_li3_key,$request->deposit_en_fiat_currency_trans_modal_li3_key,$request->deposit_fn_fiat_currency_trans_modal_li3_key,$request->deposit_sp_fiat_currency_trans_modal_li3_key,$request->deposit_ab_fiat_currency_trans_modal_li3_key,$request->deposit_gn_fiat_currency_trans_modal_li3_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->deposit_transaction_id_key,$request->deposit_en_transaction_id_key,$request->deposit_fn_transaction_id_key,$request->deposit_sp_transaction_id_key,$request->deposit_ab_transaction_id_key,$request->deposit_gn_transaction_id_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->deposit_bank_name_key,$request->deposit_en_bank_name_key,$request->deposit_fn_bank_name_key,$request->deposit_sp_bank_name_key,$request->deposit_ab_bank_name_key,$request->deposit_gn_bank_name_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->deposit_amount_key,$request->deposit_en_amount_key,$request->deposit_fn_amount_key,$request->deposit_sp_amount_key,$request->deposit_ab_amount_key,$request->deposit_gn_amount_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->deposit_add_key,$request->deposit_en_add_key,$request->deposit_fn_add_key,$request->deposit_sp_add_key,$request->deposit_ab_add_key,$request->deposit_gn_add_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->deposit_fiat_wallet_key,$request->deposit_en_fiat_wallet_key,$request->deposit_fn_fiat_wallet_key,$request->deposit_sp_fiat_wallet_key,$request->deposit_ab_fiat_wallet_key,$request->deposit_gn_fiat_wallet_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->deposit_wallet_key,$request->deposit_en_wallet_key,$request->deposit_fn_wallet_key,$request->deposit_sp_wallet_key,$request->deposit_ab_wallet_key,$request->deposit_gn_wallet_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->deposit_table_history_head_key,$request->deposit_en_table_history_head_key,$request->deposit_fn_table_history_head_key,$request->deposit_sp_table_history_head_key,$request->deposit_ab_table_history_head_key,$request->deposit_gn_table_history_head_key,1);


           //    $pagecont16 = $this->pagecontrepo->pageContentUpdate($request->page_content_id16,$request->deposit_table_history_modal_head_key,$request->deposit_en_table_history_modal_head_key,$request->deposit_fn_table_history_modal_head_key,$request->deposit_sp_table_history_modal_head_key,$request->deposit_ab_table_history_modal_head_key,$request->deposit_gn_table_history_modal_head_key,1);


           //    $pagecont17 = $this->pagecontrepo->pageContentUpdate($request->page_content_id17,$request->deposit_table_history_modal_content_key,$request->deposit_en_table_history_modal_content_key,$request->deposit_fn_table_history_modal_content_key,$request->deposit_sp_table_history_modal_content_key,$request->deposit_ab_table_history_modal_content_key,$request->deposit_gn_table_history_modal_content_key,1);


           //    $pagecont18 = $this->pagecontrepo->pageContentUpdate($request->page_content_id18,$request->deposit_table_history_table_th_date_key,$request->deposit_en_table_history_table_th_date_key,$request->deposit_fn_table_history_table_th_date_key,$request->deposit_sp_table_history_table_th_date_key,$request->deposit_ab_table_history_table_th_date_key,$request->deposit_gn_table_history_table_th_date_key,1);

           //    $pagecont19 = $this->pagecontrepo->pageContentUpdate($request->page_content_id19,$request->deposit_table_history_table_th_type_key,$request->deposit_en_table_history_table_th_type_key,$request->deposit_fn_table_history_table_th_type_key,$request->deposit_sp_table_history_table_th_type_key,$request->deposit_ab_table_history_table_th_type_key,$request->deposit_gn_table_history_table_th_type_key,1);

           //    $pagecont20 = $this->pagecontrepo->pageContentUpdate($request->page_content_id20,$request->deposit_table_history_table_th_coin_key,$request->deposit_en_table_history_table_th_coin_key,$request->deposit_fn_table_history_table_th_coin_key,$request->deposit_sp_table_history_table_th_coin_key,$request->deposit_ab_table_history_table_th_coin_key,$request->deposit_gn_table_history_table_th_coin_key,1);

           //    $pagecont21 = $this->pagecontrepo->pageContentUpdate($request->page_content_id21,$request->deposit_table_history_table_th_amount_key,$request->deposit_en_table_history_table_th_amount_key,$request->deposit_fn_table_history_table_th_amount_key,$request->deposit_sp_table_history_table_th_amount_key,$request->deposit_ab_table_history_table_th_amount_key,$request->deposit_gn_table_history_table_th_amount_key,1); 

           //    $pagecont22 = $this->pagecontrepo->pageContentUpdate($request->page_content_id22,$request->deposit_table_history_table_th_status_key,$request->deposit_en_table_history_table_th_status_key,$request->deposit_fn_table_history_table_th_status_key,$request->deposit_sp_table_history_table_th_status_key,$request->deposit_ab_table_history_table_th_status_key,$request->deposit_gn_table_history_table_th_status_key,1);

           //    $pagecont23 = $this->pagecontrepo->pageContentUpdate($request->page_content_id23,$request->deposit_button_manage_account_key,$request->deposit_en_button_manage_account_key,$request->deposit_fn_button_manage_account_key,$request->deposit_sp_button_manage_account_key,$request->deposit_ab_button_manage_account_key,$request->deposit_gn_button_manage_account_key,1); 

           //    $pagecont24 = $this->pagecontrepo->pageContentUpdate($request->page_content_id24,$request->deposit_button_withdraw_key,$request->deposit_en_button_withdraw_key,$request->deposit_fn_button_withdraw_key,$request->deposit_sp_button_withdraw_key,$request->deposit_ab_button_withdraw_key,$request->deposit_gn_button_withdraw_key,1);

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();$page_content_value_all_9 = array();$page_content_value_all_10 = array();$page_content_value_all_11 = array();$page_content_value_all_12 = array();$page_content_value_all_13 = array();$page_content_value_all_14 = array();$page_content_value_all_15 = array();$page_content_value_all_16 = array();$page_content_value_all_17 = array();$page_content_value_all_18 = array();$page_content_value_all_19 = array();$page_content_value_all_20 = array();$page_content_value_all_21 = array();$page_content_value_all_22 = array();$page_content_value_all_23 = array();$page_content_value_all_24 = array();$page_content_value_all_25 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();$page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();$page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();$page_content_value19 = array();$page_content_value20 = array();$page_content_value21 = array();$page_content_value22 = array();$page_content_value23 = array();$page_content_value24 = array();
              $page_content_value25 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_values1 = 'deposit_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'deposit_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'deposit_'.$language->language_symbol.'_heading_button_key';
              $page_content_values4 = 'deposit_'.$language->language_symbol.'_fiat_currency_trans_key';
              $page_content_values5 = 'deposit_'.$language->language_symbol.'_fiat_currency_trans_modal_head_key';
              $page_content_values6 = 'deposit_'.$language->language_symbol.'_fiat_currency_trans_modal_here_key';
              $page_content_values7 = 'deposit_'.$language->language_symbol.'_fiat_currency_trans_modal_li1_key';
              $page_content_values8 = 'deposit_'.$language->language_symbol.'_fiat_currency_trans_modal_li2_key';
              $page_content_values9 = 'deposit_'.$language->language_symbol.'_fiat_currency_trans_modal_li3_key';
              $page_content_values10 = 'deposit_'.$language->language_symbol.'_transaction_id_key';
              $page_content_values11 = 'deposit_'.$language->language_symbol.'_bank_name_key';
              $page_content_values12 = 'deposit_'.$language->language_symbol.'_amount_key';
              $page_content_values13 = 'deposit_'.$language->language_symbol.'_add_key';
              $page_content_values14 = 'deposit_'.$language->language_symbol.'_fiat_wallet_key';
              $page_content_values15 = 'deposit_'.$language->language_symbol.'_wallet_key';
              $page_content_values16 = 'deposit_'.$language->language_symbol.'_table_history_head_key';
              $page_content_values17 = 'deposit_'.$language->language_symbol.'_table_history_modal_head_key';
              $page_content_values18 = 'deposit_'.$language->language_symbol.'_table_history_modal_content_key';
              $page_content_values19 = 'deposit_'.$language->language_symbol.'_table_history_table_th_date_key';
              $page_content_values20 = 'deposit_'.$language->language_symbol.'_table_history_table_th_type_key';
              $page_content_values21 = 'deposit_'.$language->language_symbol.'_table_history_table_th_coin_key';
              $page_content_values22 = 'deposit_'.$language->language_symbol.'_table_history_table_th_amount_key';
              $page_content_values23 = 'deposit_'.$language->language_symbol.'_table_history_table_th_status_key';
              $page_content_values24 = 'deposit_'.$language->language_symbol.'_button_manage_account_key';
              $page_content_values25 = 'deposit_'.$language->language_symbol.'_button_withdraw_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['deposit_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['deposit_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['deposit_heading_button_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['deposit_fiat_currency_trans_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['deposit_fiat_currency_trans_modal_head_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['deposit_fiat_currency_trans_modal_here_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['deposit_fiat_currency_trans_modal_li1_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['deposit_fiat_currency_trans_modal_li2_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['deposit_fiat_currency_trans_modal_li3_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['deposit_transaction_id_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['deposit_bank_name_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['deposit_amount_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['deposit_add_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['deposit_fiat_wallet_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['deposit_wallet_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['deposit_table_history_head_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['deposit_table_history_modal_head_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['deposit_table_history_modal_content_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values19)) {
                   $page_content_value19['deposit_table_history_table_th_date_key'] = $request->$page_content_values19;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values20)) {
                   $page_content_value20['deposit_table_history_table_th_type_key'] = $request->$page_content_values20;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values21)) {
                   $page_content_value21['deposit_table_history_table_th_coin_key'] = $request->$page_content_values21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values22)) {
                   $page_content_value22['deposit_table_history_table_th_amount_key'] = $request->$page_content_values22;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values23)) {
                   $page_content_value23['deposit_table_history_table_th_status_key'] = $request->$page_content_values23;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values24)) {
                   $page_content_value24['deposit_button_manage_account_key'] = $request->$page_content_values24;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values25)) {
                   $page_content_value25['deposit_button_withdraw_key'] = $request->$page_content_values25;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              $page_content_value_all_19[$language->language_symbol] = $page_content_value19;
              $page_content_value_all_20[$language->language_symbol] = $page_content_value20;
              $page_content_value_all_21[$language->language_symbol] = $page_content_value21;
              $page_content_value_all_22[$language->language_symbol] = $page_content_value22;
              $page_content_value_all_23[$language->language_symbol] = $page_content_value23;
              $page_content_value_all_24[$language->language_symbol] = $page_content_value24;
              $page_content_value_all_25[$language->language_symbol] = $page_content_value25;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);
          $pagecont19 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id18,$page_content_value_all_19,1);
          $pagecont20 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id19,$page_content_value_all_20,1);
          $pagecont21 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id20,$page_content_value_all_21,1);
          $pagecont22 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id21,$page_content_value_all_22,1);
          $pagecont23 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id22,$page_content_value_all_23,1);
          $pagecont24 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id23,$page_content_value_all_24,1);
          $pagecont25 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id24,$page_content_value_all_25,1);


            return back()->with('success','Updated Successfully');
            
    } 
    //  My Profile



     public function profileContentEdit(Request $request)
    {
      // echo  $request->page_content_id;exit;
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->profile_heading_key,$request->profile_en_heading_key,$request->profile_fn_heading_key,$request->profile_sp_heading_key,$request->profile_ab_heading_key,$request->profile_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->profile_personal_details_key,$request->profile_en_personal_details_key,$request->profile_fn_personal_details_key,$request->profile_sp_personal_details_key,$request->profile_ab_personal_details_key,$request->profile_gn_personal_details_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->profile_personal_details_name_key,$request->profile_en_personal_details_name_key,$request->profile_fn_personal_details_name_key,$request->profile_sp_personal_details_name_key,$request->profile_ab_personal_details_name_key,$request->profile_gn_personal_details_name_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->profile_personal_details_email_key,$request->profile_en_personal_details_email_key,$request->profile_fn_personal_details_email_key,$request->profile_sp_personal_details_email_key,$request->profile_ab_personal_details_email_key,$request->profile_gn_personal_details_email_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->profile_personal_details_phone_key,$request->profile_en_personal_details_phone_key,$request->profile_fn_personal_details_phone_key,$request->profile_sp_personal_details_phone_key,$request->profile_ab_personal_details_phone_key,$request->profile_gn_personal_details_phone_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->profile_personal_details_current_pass_key,$request->profile_en_personal_details_current_pass_key,$request->profile_fn_personal_details_current_pass_key,$request->profile_sp_personal_details_current_pass_key,$request->profile_ab_personal_details_current_pass_key,$request->profile_gn_personal_details_current_pass_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->profile_personal_details_change_pass_key,$request->profile_en_personal_details_change_pass_key,$request->profile_fn_personal_details_change_pass_key,$request->profile_sp_personal_details_change_pass_key,$request->profile_ab_personal_details_change_pass_key,$request->profile_gn_personal_details_change_pass_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->profile_personal_details_confirm_pass_key,$request->profile_en_personal_details_confirm_pass_key,$request->profile_fn_personal_details_confirm_pass_key,$request->profile_sp_personal_details_confirm_pass_key,$request->profile_ab_personal_details_confirm_pass_key,$request->profile_gn_personal_details_confirm_pass_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->profile_personal_details_first_name_key,$request->profile_en_personal_details_first_name_key,$request->profile_fn_personal_details_first_name_key,$request->profile_sp_personal_details_first_name_key,$request->profile_ab_personal_details_first_name_key,$request->profile_gn_personal_details_first_name_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->profile_personal_details_last_name_key,$request->profile_en_personal_details_last_name_key,$request->profile_fn_personal_details_last_name_key,$request->profile_sp_personal_details_last_name_key,$request->profile_ab_personal_details_last_name_key,$request->profile_gn_personal_details_last_name_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->profile_personal_details_button_key,$request->profile_en_personal_details_button_key,$request->profile_fn_personal_details_button_key,$request->profile_sp_personal_details_button_key,$request->profile_ab_personal_details_button_key,$request->profile_gn_personal_details_button_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->profile_account_activities_key,$request->profile_en_account_activities_key,$request->profile_fn_account_activities_key,$request->profile_sp_account_activities_key,$request->profile_ab_account_activities_key,$request->profile_gn_account_activities_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->profile_table_date_key,$request->profile_en_table_date_key,$request->profile_fn_table_date_key,$request->profile_sp_table_date_key,$request->profile_ab_table_date_key,$request->profile_gn_table_date_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->profile_table_type_key,$request->profile_en_table_type_key,$request->profile_fn_table_type_key,$request->profile_sp_table_type_key,$request->profile_ab_table_type_key,$request->profile_gn_table_type_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->profile_table_ip_address_key,$request->profile_en_table_ip_address_key,$request->profile_fn_table_ip_address_key,$request->profile_sp_table_ip_address_key,$request->profile_ab_table_ip_address_key,$request->profile_gn_table_ip_address_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->profile_referal_point_key,$request->profile_en_referal_point_key,$request->profile_fn_referal_point_key,$request->profile_sp_referal_point_key,$request->profile_ab_referal_point_key,$request->profile_gn_referal_point_key,1);


           //    $pagecont16 = $this->pagecontrepo->pageContentUpdate($request->page_content_id16,$request->profile_referal_code_key,$request->profile_en_referal_code_key,$request->profile_fn_referal_code_key,$request->profile_sp_referal_code_key,$request->profile_ab_referal_code_key,$request->profile_gn_referal_code_key,1);


           //    $pagecont17 = $this->pagecontrepo->pageContentUpdate($request->page_content_id17,$request->profile_available_points_key,$request->profile_en_available_points_key,$request->profile_fn_available_points_key,$request->profile_sp_available_points_key,$request->profile_ab_available_points_key,$request->profile_gn_available_points_key,1);


           //    $pagecont18 = $this->pagecontrepo->pageContentUpdate($request->page_content_id18,$request->profile_referal_transaction_key,$request->profile_en_referal_transaction_key,$request->profile_fn_referal_transaction_key,$request->profile_sp_referal_transaction_key,$request->profile_ab_referal_transaction_key,$request->profile_gn_referal_transaction_key,1);

           //    $pagecont19 = $this->pagecontrepo->pageContentUpdate($request->page_content_id19,$request->profile_referal_table_transaction_id_key,$request->profile_en_referal_table_transaction_id_key,$request->profile_fn_referal_table_transaction_id_key,$request->profile_sp_referal_table_transaction_id_key,$request->profile_ab_referal_table_transaction_id_key,$request->profile_gn_referal_table_transaction_id_key,1);

           //    $pagecont20 = $this->pagecontrepo->pageContentUpdate($request->page_content_id20,$request->profile_referal_table_transaction_points_key,$request->profile_en_referal_table_transaction_points_key,$request->profile_fn_referal_table_transaction_points_key,$request->profile_sp_referal_table_transaction_points_key,$request->profile_ab_referal_table_transaction_points_key,$request->profile_gn_referal_table_transaction_points_key,1);

           //    $pagecont21 = $this->pagecontrepo->pageContentUpdate($request->page_content_id21,$request->profile_referal_table_transaction_amount_key,$request->profile_en_referal_table_transaction_amount_key,$request->profile_fn_referal_table_transaction_amount_key,$request->profile_sp_referal_table_transaction_amount_key,$request->profile_ab_referal_table_transaction_amount_key,$request->profile_gn_referal_table_transaction_amount_key,1); 

           //    $pagecont22 = $this->pagecontrepo->pageContentUpdate($request->page_content_id22,$request->profile_referal_table_transaction_date_key,$request->profile_en_referal_table_transaction_date_key,$request->profile_fn_referal_table_transaction_date_key,$request->profile_sp_referal_table_transaction_date_key,$request->profile_ab_referal_table_transaction_date_key,$request->profile_gn_referal_table_transaction_date_key,1);

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();$page_content_value_all_9 = array();$page_content_value_all_10 = array();$page_content_value_all_11 = array();$page_content_value_all_12 = array();$page_content_value_all_13 = array();$page_content_value_all_14 = array();$page_content_value_all_15 = array();$page_content_value_all_16 = array();$page_content_value_all_17 = array();$page_content_value_all_18 = array();$page_content_value_all_19 = array();$page_content_value_all_20 = array();$page_content_value_all_21 = array();$page_content_value_all_22 = array();$page_content_value_all_23 = array();$page_content_value_all_24 = array();$page_content_value_all_25 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();$page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();$page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();$page_content_value19 = array();$page_content_value20 = array();$page_content_value21 = array();$page_content_value22 = array();$page_content_value23 = array();$page_content_value24 = array();
              $page_content_value25 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

                            $page_content_values1 = 'profile_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'profile_'.$language->language_symbol.'_personal_details_key';
              $page_content_values3 = 'profile_'.$language->language_symbol.'_personal_details_name_key';
              $page_content_values4 = 'profile_'.$language->language_symbol.'_personal_details_email_key';
              $page_content_values5 = 'profile_'.$language->language_symbol.'_personal_details_phone_key';
              $page_content_values6 = 'profile_'.$language->language_symbol.'_personal_details_current_pass_key';
              $page_content_values7 = 'profile_'.$language->language_symbol.'_personal_details_change_pass_key';


              $page_content_values8 = 'profile_'.$language->language_symbol.'_personal_details_confirm_pass_key';
              $page_content_values9 = 'profile_'.$language->language_symbol.'_personal_details_first_name_key';
              $page_content_values10 = 'profile_'.$language->language_symbol.'_personal_details_last_name_key';
              $page_content_values11 = 'profile_'.$language->language_symbol.'_personal_details_button_key';
              $page_content_values12 = 'profile_'.$language->language_symbol.'_account_activities_key';
              $page_content_values13 = 'profile_'.$language->language_symbol.'_table_date_key';
              $page_content_values14 = 'profile_'.$language->language_symbol.'_table_type_key';
              $page_content_values15 = 'profile_'.$language->language_symbol.'_table_ip_address_key';
              $page_content_values16 = 'profile_'.$language->language_symbol.'_referal_point_key';
              $page_content_values17 = 'profile_'.$language->language_symbol.'_referal_code_key';
              $page_content_values18 = 'profile_'.$language->language_symbol.'_available_points_key';
              $page_content_values19 = 'profile_'.$language->language_symbol.'_referal_transaction_key';
              $page_content_values20 = 'profile_'.$language->language_symbol.'_referal_table_transaction_id_key';
              $page_content_values21 = 'profile_'.$language->language_symbol.'_referal_table_transaction_points_key';
              $page_content_values22 = 'profile_'.$language->language_symbol.'_referal_table_transaction_amount_key';
              $page_content_values23 = 'profile_'.$language->language_symbol.'_referal_table_transaction_date_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['profile_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['profile_personal_details_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['profile_personal_details_name_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['profile_personal_details_email_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['profile_personal_details_phone_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['profile_personal_details_current_pass_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['profile_personal_details_change_pass_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['profile_personal_details_confirm_pass_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['profile_personal_details_first_name_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['profile_personal_details_last_name_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['profile_personal_details_button_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['profile_account_activities_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['profile_table_date_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['profile_table_type_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['profile_table_ip_address_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['profile_referal_point_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['profile_referal_code_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['profile_available_points_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values19)) {
                   $page_content_value19['profile_referal_transaction_key'] = $request->$page_content_values19;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values20)) {
                   $page_content_value20['profile_referal_table_transaction_id_key'] = $request->$page_content_values20;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values21)) {
                   $page_content_value21['profile_referal_table_transaction_points_key'] = $request->$page_content_values21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values22)) {
                   $page_content_value22['profile_referal_table_transaction_amount_key'] = $request->$page_content_values22;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values23)) {
                   $page_content_value23['profile_referal_table_transaction_date_key'] = $request->$page_content_values23;
                   // echo $page_content_value1."string1<br>";
              }

              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              $page_content_value_all_19[$language->language_symbol] = $page_content_value19;
              $page_content_value_all_20[$language->language_symbol] = $page_content_value20;
              $page_content_value_all_21[$language->language_symbol] = $page_content_value21;
              $page_content_value_all_22[$language->language_symbol] = $page_content_value22;
              $page_content_value_all_23[$language->language_symbol] = $page_content_value23;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);
          $pagecont19 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id18,$page_content_value_all_19,1);
          $pagecont20 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id19,$page_content_value_all_20,1);
          $pagecont21 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id20,$page_content_value_all_21,1);
          $pagecont22 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id21,$page_content_value_all_22,1);
          $pagecont23 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id22,$page_content_value_all_23,1);

             
            return back()->with('success','Updated Successfully');
            
    } 
    //  Security



     public function securityContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->security_heading_key ,$request->security_en_heading_key,$request->security_fn_heading_key,$request->security_sp_heading_key,$request->security_ab_heading_key,$request->security_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->security_notification,$request->security_en_notification,$request->security_fn_notification,$request->security_sp_notification,$request->security_ab_notification,$request->security_gn_notification,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->security_confirm_crypto_wiyhdrawl_key,$request->security_en_confirm_crypto_wiyhdrawl_key,$request->security_fn_confirm_crypto_wiyhdrawl_key,$request->security_sp_confirm_crypto_wiyhdrawl_key,$request->security_ab_confirm_crypto_wiyhdrawl_key,$request->security_gn_confirm_crypto_wiyhdrawl_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->security_confirm_bank_withdrawl_key,$request->security_en_confirm_bank_withdrawl_key,$request->security_fn_confirm_bank_withdrawl_key,$request->security_sp_confirm_bank_withdrawl_key,$request->security_ab_confirm_bank_withdrawl_key,$request->security_gn_confirm_bank_withdrawl_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->security_notify_crypto_deposit_key,$request->security_en_notify_crypto_deposit_key,$request->security_fn_notify_crypto_deposit_key,$request->security_sp_notify_crypto_deposit_key,$request->security_ab_notify_crypto_deposit_key,$request->security_gn_notify_crypto_deposit_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->security_notify_bank_deposit_key,$request->security_en_notify_bank_deposit_key,$request->security_fn_notify_bank_deposit_key,$request->security_sp_notify_bank_deposit_key,$request->security_ab_notify_bank_deposit_key,$request->security_gn_notify_bank_deposit_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->security_notify_crypto_withdrawal_key,$request->security_en_notify_crypto_withdrawal_key,$request->security_fn_notify_crypto_withdrawal_key,$request->security_sp_notify_crypto_withdrawal_key,$request->security_ab_notify_crypto_withdrawal_key,$request->security_gn_notify_crypto_withdrawal_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->security_notify_bank_withdrawal_key,$request->security_en_notify_bank_withdrawal_key,$request->security_fn_notify_bank_withdrawal_key,$request->security_sp_notify_bank_withdrawal_key,$request->security_ab_notify_bank_withdrawal_key,$request->security_gn_notify_bank_withdrawal_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->security_notify_logs_account_key,$request->security_en_notify_logs_account_key,$request->security_fn_notify_logs_account_key,$request->security_sp_notify_logs_account_key,$request->security_ab_notify_logs_account_key,$request->security_gn_notify_logs_account_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->security_notify_button_key,$request->security_en_notify_button_key,$request->security_fn_notify_button_key,$request->security_sp_notify_button_key,$request->security_ab_notify_button_key,$request->security_gn_notify_button_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->security_google_auth_key,$request->security_en_google_auth_key,$request->security_fn_google_auth_key,$request->security_sp_google_auth_key,$request->security_ab_google_auth_key,$request->security_gn_google_auth_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->security_enable_auth_key,$request->security_en_enable_auth_key,$request->security_fn_enable_auth_key,$request->security_sp_enable_auth_key,$request->security_ab_enable_auth_key,$request->security_gn_enable_auth_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->security_2fa_methode_key,$request->security_en_2fa_methode_key,$request->security_fn_2fa_methode_key,$request->security_sp_2fa_methode_key,$request->security_ab_2fa_methode_key,$request->security_gn_2fa_methode_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->security_select_google_auth_key,$request->security_en_select_google_auth_key,$request->security_fn_select_google_auth_key,$request->security_sp_select_google_auth_key,$request->security_ab_select_google_auth_key,$request->security_gn_select_google_auth_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->security_enable_2fa_key,$request->security_en_enable_2fa_key,$request->security_fn_enable_2fa_key,$request->security_sp_enable_2fa_key,$request->security_ab_enable_2fa_key,$request->security_gn_enable_2fa_key,1);

            $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();$page_content_value_all_9 = array();$page_content_value_all_10 = array();$page_content_value_all_11 = array();$page_content_value_all_12 = array();$page_content_value_all_13 = array();$page_content_value_all_14 = array();$page_content_value_all_15 = array();$page_content_value_all_16 = array();$page_content_value_all_17 = array();$page_content_value_all_18 = array();$page_content_value_all_19 = array();$page_content_value_all_20 = array();$page_content_value_all_21 = array();$page_content_value_all_22 = array();$page_content_value_all_23 = array();$page_content_value_all_24 = array();$page_content_value_all_25 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();$page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();$page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();$page_content_value19 = array();$page_content_value20 = array();$page_content_value21 = array();$page_content_value22 = array();$page_content_value23 = array();$page_content_value24 = array();
              $page_content_value25 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

                            $page_content_values1 = 'security_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'security_'.$language->language_symbol.'_notification';
              $page_content_values3 = 'security_'.$language->language_symbol.'_confirm_crypto_wiyhdrawl_key';
              $page_content_values4 = 'security_'.$language->language_symbol.'_confirm_bank_withdrawl_key';
              $page_content_values5 = 'security_'.$language->language_symbol.'_notify_crypto_deposit_key';
              $page_content_values6 = 'security_'.$language->language_symbol.'_notify_bank_deposit_key';
              $page_content_values7 = 'security_'.$language->language_symbol.'_notify_crypto_withdrawal_key';
              $page_content_values8 = 'security_'.$language->language_symbol.'_notify_bank_withdrawal_key';
              $page_content_values9 = 'security_'.$language->language_symbol.'_notify_logs_account_key';
              $page_content_values10 = 'security_'.$language->language_symbol.'_notify_button_key';
              $page_content_values11 = 'security_'.$language->language_symbol.'_google_auth_key';
              $page_content_values12 = 'security_'.$language->language_symbol.'_enable_auth_key';
              $page_content_values13 = 'security_'.$language->language_symbol.'_2fa_methode_key';
              $page_content_values14 = 'security_'.$language->language_symbol.'_select_google_auth_key';
              $page_content_values15 = 'security_'.$language->language_symbol.'_enable_2fa_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['security_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['security_notification'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['security_confirm_crypto_wiyhdrawl_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['security_confirm_bank_withdrawl_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['security_notify_crypto_deposit_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['security_notify_bank_deposit_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['security_notify_crypto_withdrawal_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['security_notify_bank_withdrawal_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['security_notify_logs_account_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['security_notify_button_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['security_google_auth_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['security_enable_auth_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['security_2fa_methode_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['security_select_google_auth_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['security_enable_2fa_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);

             
            return back()->with('success','Updated Successfully');
            
    } 

    //  Balance



     public function balanceContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->balance_heading_key ,$request->balance_en_heading_key,$request->balance_fn_heading_key,$request->balance_sp_heading_key,$request->balance_ab_heading_key,$request->balance_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->balance_sub_heading_key,$request->balance_en_sub_heading_key,$request->balance_fn_sub_heading_key,$request->balance_sp_sub_heading_key,$request->balance_ab_sub_heading_key,$request->balance_gn_sub_heading_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->balance_total_crypto_key,$request->balance_en_total_crypto_key,$request->balance_fn_total_crypto_key,$request->balance_sp_total_crypto_key,$request->balance_ab_total_crypto_key,$request->balance_gn_total_crypto_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->balance_total_usd_key,$request->balance_en_total_usd_key,$request->balance_fn_total_usd_key,$request->balance_sp_total_usd_key,$request->balance_ab_total_usd_key,$request->balance_gn_total_usd_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->balance_total_cryp_usd_key,$request->balance_en_total_cryp_usd_key,$request->balance_fn_total_cryp_usd_key,$request->balance_sp_total_cryp_usd_key,$request->balance_ab_total_cryp_usd_key,$request->balance_gn_total_cryp_usd_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->balance_market_rate_key,$request->balance_en_market_rate_key,$request->balance_fn_market_rate_key,$request->balance_sp_market_rate_key,$request->balance_ab_market_rate_key,$request->balance_gn_market_rate_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->balance_market_rate_modal_key,$request->balance_en_market_rate_modal_key,$request->balance_fn_market_rate_modal_key,$request->balance_sp_market_rate_modal_key,$request->balance_ab_market_rate_modal_key,$request->balance_gn_market_rate_modal_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->balance_fee_level_key,$request->balance_en_fee_level_key,$request->balance_fn_fee_level_key,$request->balance_sp_fee_level_key,$request->balance_ab_fee_level_key,$request->balance_gn_fee_level_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->balance_commision_maker_key,$request->balance_en_commision_maker_key,$request->balance_fn_commision_maker_key,$request->balance_sp_commision_maker_key,$request->balance_ab_commision_maker_key,$request->balance_gn_commision_maker_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->balance_commision_taker_key,$request->balance_en_commision_taker_key,$request->balance_fn_commision_taker_key,$request->balance_sp_commision_taker_key,$request->balance_ab_commision_taker_key,$request->balance_gn_commision_taker_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->balance_30_days_volume_key,$request->balance_en_30_days_volume_key,$request->balance_fn_30_days_volume_key,$request->balance_sp_30_days_volume_key,$request->balance_ab_30_days_volume_key,$request->balance_gn_30_days_volume_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->balance_available_balance_key,$request->balance_en_available_balance_key,$request->balance_fn_available_balance_key,$request->balance_sp_available_balance_key,$request->balance_ab_available_balance_key,$request->balance_gn_available_balance_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->balance_available_balance_table_coin_key,$request->balance_en_available_balance_table_coin_key,$request->balance_fn_available_balance_table_coin_key,$request->balance_sp_available_balance_table_coin_key,$request->balance_ab_available_balance_table_coin_key,$request->balance_gn_available_balance_table_coin_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->balance_available_balance_table_name_key,$request->balance_en_available_balance_table_name_key,$request->balance_fn_available_balance_table_name_key,$request->balance_sp_available_balance_table_name_key,$request->balance_ab_available_balance_table_name_key,$request->balance_gn_available_balance_table_name_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->balance_available_balance_table_balance_key,$request->balance_en_available_balance_table_balance_key,$request->balance_fn_available_balance_table_balance_key,$request->balance_sp_available_balance_table_balance_key,$request->balance_ab_available_balance_table_balance_key,$request->balance_gn_available_balance_table_balance_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->balance_available_balance_table_estimate_key,$request->balance_en_available_balance_table_estimate_key,$request->balance_fn_available_balance_table_estimate_key,$request->balance_sp_available_balance_table_estimate_key,$request->balance_ab_available_balance_table_estimate_key,$request->balance_gn_available_balance_table_estimate_key,1);


           //    $pagecont16 = $this->pagecontrepo->pageContentUpdate($request->page_content_id16,$request->balance_available_balance_table_td_deposite_key,$request->balance_en_available_balance_table_td_deposite_key,$request->balance_fn_available_balance_table_td_deposite_key,$request->balance_sp_available_balance_table_td_deposite_key,$request->balance_ab_available_balance_table_td_deposite_key,$request->balance_gn_available_balance_table_td_deposite_key,1);


           //    $pagecont17 = $this->pagecontrepo->pageContentUpdate($request->page_content_id17,$request->balance_available_balance_table_td_withdraw_key,$request->balance_en_available_balance_table_td_withdraw_key,$request->balance_fn_available_balance_table_td_withdraw_key,$request->balance_sp_available_balance_table_td_withdraw_key,$request->balance_ab_available_balance_table_td_withdraw_key,$request->balance_gn_available_balance_table_td_withdraw_key,1);


           //    $pagecont18 = $this->pagecontrepo->pageContentUpdate($request->page_content_id18,$request->balance_available_balance_table_td_trade_key,$request->balance_en_available_balance_table_td_trade_key,$request->balance_fn_available_balance_table_td_trade_key,$request->balance_sp_available_balance_table_td_trade_key,$request->balance_ab_available_balance_table_td_trade_key,$request->balance_gn_available_balance_table_td_trade_key,1);

           //    $pagecont19 = $this->pagecontrepo->pageContentUpdate($request->page_content_id19,$request->balance_available_balance_modal_key,$request->balance_en_available_balance_modal_key,$request->balance_fn_available_balance_modal_key,$request->balance_sp_available_balance_modal_key,$request->balance_ab_available_balance_modal_key,$request->balance_gn_available_balance_modal_key,1);

           //    $pagecont20 = $this->pagecontrepo->pageContentUpdate($request->page_content_id20,$request->balance_on_hold_key,$request->balance_en_on_hold_key,$request->balance_fn_on_hold_key,$request->balance_sp_on_hold_key,$request->balance_ab_on_hold_key,$request->balance_gn_on_hold_key,1);

           //    $pagecont21 = $this->pagecontrepo->pageContentUpdate($request->page_content_id21,$request->balance_on_hold_table_open_key,$request->balance_en_on_hold_table_open_key,$request->balance_fn_on_hold_table_open_key,$request->balance_sp_on_hold_table_open_key,$request->balance_ab_on_hold_table_open_key,$request->balance_gn_on_hold_table_open_key,1); 

           //    $pagecont22 = $this->pagecontrepo->pageContentUpdate($request->page_content_id22,$request->balance_on_hold_table_withdrawl_key,$request->balance_en_on_hold_table_withdrawl_key,$request->balance_fn_on_hold_table_withdrawl_key,$request->balance_sp_on_hold_table_withdrawl_key,$request->balance_ab_on_hold_table_withdrawl_key,$request->balance_gn_on_hold_table_withdrawl_key,1);

           //    $pagecont23 = $this->pagecontrepo->pageContentUpdate($request->page_content_id23,$request->balance_heading_button_buy_sell_key,$request->balance_en_heading_button_buy_sell_key,$request->balance_fn_heading_button_buy_sell_key,$request->balance_sp_heading_button_buy_sell_key,$request->balance_ab_heading_button_buy_sell_key,$request->balance_gn_heading_button_buy_sell_key,1); 

           //    $pagecont24 = $this->pagecontrepo->pageContentUpdate($request->page_content_id24,$request->balance_heading_button_deposit_fiat_key,$request->balance_en_heading_button_deposit_fiat_key,$request->balance_fn_heading_button_deposit_fiat_key,$request->balance_sp_heading_button_deposit_fiat_key,$request->balance_ab_heading_button_deposit_fiat_key,$request->balance_gn_heading_button_deposit_fiat_key,1);
      $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();$page_content_value_all_9 = array();$page_content_value_all_10 = array();$page_content_value_all_11 = array();$page_content_value_all_12 = array();$page_content_value_all_13 = array();$page_content_value_all_14 = array();$page_content_value_all_15 = array();$page_content_value_all_16 = array();$page_content_value_all_17 = array();$page_content_value_all_18 = array();$page_content_value_all_19 = array();$page_content_value_all_20 = array();$page_content_value_all_21 = array();$page_content_value_all_22 = array();$page_content_value_all_23 = array();$page_content_value_all_24 = array();$page_content_value_all_25 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();$page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();$page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();$page_content_value19 = array();$page_content_value20 = array();$page_content_value21 = array();$page_content_value22 = array();$page_content_value23 = array();$page_content_value24 = array();
              $page_content_value25 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_values1 = 'balance_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'balance_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'balance_'.$language->language_symbol.'_total_crypto_key';
              $page_content_values4 = 'balance_'.$language->language_symbol.'_total_usd_key';
              $page_content_values5 = 'balance_'.$language->language_symbol.'_total_cryp_usd_key';
              $page_content_values6 = 'balance_'.$language->language_symbol.'_market_rate_key';
              $page_content_values7 = 'balance_'.$language->language_symbol.'_market_rate_modal_key';


              $page_content_values8 = 'balance_'.$language->language_symbol.'_fee_level_key';
              $page_content_values9 = 'balance_'.$language->language_symbol.'_commision_maker_key';
              $page_content_values10 = 'balance_'.$language->language_symbol.'_commision_taker_key';
              $page_content_values11 = 'balance_'.$language->language_symbol.'_30_days_volume_key';
              $page_content_values12 = 'balance_'.$language->language_symbol.'_available_balance_key';
              $page_content_values13 = 'balance_'.$language->language_symbol.'_available_balance_table_coin_key';
              $page_content_values14 = 'balance_'.$language->language_symbol.'_available_balance_table_name_key';
              $page_content_values15 = 'balance_'.$language->language_symbol.'_available_balance_table_balance_key';
              $page_content_values16 = 'balance_'.$language->language_symbol.'_available_balance_table_estimate_key';
              $page_content_values17 = 'balance_'.$language->language_symbol.'_available_balance_table_td_deposite_key';
              $page_content_values18 = 'balance_'.$language->language_symbol.'_available_balance_table_td_withdraw_key';
              $page_content_values19 = 'balance_'.$language->language_symbol.'_available_balance_table_td_trade_key';
              $page_content_values20 = 'balance_'.$language->language_symbol.'_available_balance_modal_key';
              $page_content_values21 = 'balance_'.$language->language_symbol.'_on_hold_key';
              $page_content_values22 = 'balance_'.$language->language_symbol.'_on_hold_table_open_key';
              $page_content_values23 = 'balance_'.$language->language_symbol.'_on_hold_table_withdrawl_key';
              $page_content_values24 = 'balance_'.$language->language_symbol.'_heading_button_buy_sell_key';
              $page_content_values25 = 'balance_'.$language->language_symbol.'_heading_button_deposit_fiat_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['balance_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['balance_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['balance_total_crypto_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['balance_total_usd_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['balance_total_cryp_usd_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['balance_market_rate_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['balance_market_rate_modal_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['balance_fee_level_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['balance_commision_maker_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['balance_commision_taker_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['balance_30_days_volume_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['balance_available_balance_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['balance_available_balance_table_coin_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['balance_available_balance_table_name_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['balance_available_balance_table_balance_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['balance_available_balance_table_estimate_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['balance_available_balance_table_td_deposite_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['balance_available_balance_table_td_withdraw_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values19)) {
                   $page_content_value19['balance_available_balance_table_td_trade_key'] = $request->$page_content_values19;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values20)) {
                   $page_content_value20['balance_available_balance_modal_key'] = $request->$page_content_values20;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values21)) {
                   $page_content_value21['balance_on_hold_key'] = $request->$page_content_values21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values22)) {
                   $page_content_value22['balance_on_hold_table_open_key'] = $request->$page_content_values22;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values23)) {
                   $page_content_value23['balance_on_hold_table_withdrawl_key'] = $request->$page_content_values23;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values24)) {
                   $page_content_value24['balance_heading_button_buy_sell_key'] = $request->$page_content_values24;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values25)) {
                   $page_content_value25['balance_heading_button_deposit_fiat_key'] = $request->$page_content_values25;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              $page_content_value_all_19[$language->language_symbol] = $page_content_value19;
              $page_content_value_all_20[$language->language_symbol] = $page_content_value20;
              $page_content_value_all_21[$language->language_symbol] = $page_content_value21;
              $page_content_value_all_22[$language->language_symbol] = $page_content_value22;
              $page_content_value_all_23[$language->language_symbol] = $page_content_value23;
              $page_content_value_all_24[$language->language_symbol] = $page_content_value24;
              $page_content_value_all_25[$language->language_symbol] = $page_content_value25;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);
          $pagecont19 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id18,$page_content_value_all_19,1);
          $pagecont20 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id19,$page_content_value_all_20,1);
          $pagecont21 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id20,$page_content_value_all_21,1);
          $pagecont22 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id21,$page_content_value_all_22,1);
          $pagecont23 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id22,$page_content_value_all_23,1);
          $pagecont24 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id23,$page_content_value_all_24,1);
          $pagecont25 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id24,$page_content_value_all_25,1);

            return back()->with('success','Updated Successfully');
            
    } 

    //  Balance



     public function cryptowalletContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->crypto_wallet_heading_key ,$request->crypto_wallet_en_heading_key,$request->crypto_wallet_fn_heading_key,$request->crypto_wallet_sp_heading_key,$request->crypto_wallet_ab_heading_key,$request->crypto_wallet_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->crypto_wallet_sub_heading_key,$request->crypto_wallet_en_sub_heading_key,$request->crypto_wallet_fn_sub_heading_key,$request->crypto_wallet_sp_sub_heading_key,$request->crypto_wallet_ab_sub_heading_key,$request->crypto_wallet_gn_sub_heading_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->crypto_wallet_li_content1_key,$request->crypto_wallet_en_li_content1_key,$request->crypto_wallet_fn_li_content1_key,$request->crypto_wallet_sp_li_content1_key,$request->crypto_wallet_ab_li_content1_key,$request->crypto_wallet_gn_li_content1_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->crypto_wallet_li_content2_key,$request->crypto_wallet_en_li_content2_key,$request->crypto_wallet_fn_li_content2_key,$request->crypto_wallet_sp_li_content2_key,$request->crypto_wallet_ab_li_content2_key,$request->crypto_wallet_gn_li_content2_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->crypto_wallet_li_content3_key,$request->crypto_wallet_en_li_content3_key,$request->crypto_wallet_fn_li_content3_key,$request->crypto_wallet_sp_li_content3_key,$request->crypto_wallet_ab_li_content3_key,$request->crypto_wallet_gn_li_content3_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->crypto_wallet_li_content4_key,$request->crypto_wallet_en_li_content4_key,$request->crypto_wallet_fn_li_content4_key,$request->crypto_wallet_sp_li_content4_key,$request->crypto_wallet_ab_li_content4_key,$request->crypto_wallet_gn_li_content4_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->crypto_wallet_send_crypto_key,$request->crypto_wallet_en_send_crypto_key,$request->crypto_wallet_fn_send_crypto_key,$request->crypto_wallet_sp_send_crypto_key,$request->crypto_wallet_ab_send_crypto_key,$request->crypto_wallet_gn_send_crypto_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->crypto_wallet_send_modal_content_key,$request->crypto_wallet_en_send_modal_content_key,$request->crypto_wallet_fn_send_modal_content_key,$request->crypto_wallet_sp_send_modal_content_key,$request->crypto_wallet_ab_send_modal_content_key,$request->crypto_wallet_gn_send_modal_content_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->crypto_wallet_send_modal_content_li1_key,$request->crypto_wallet_en_send_modal_content_li1_key,$request->crypto_wallet_fn_send_modal_content_li1_key,$request->crypto_wallet_sp_send_modal_content_li1_key,$request->crypto_wallet_ab_send_modal_content_li1_key,$request->crypto_wallet_gn_send_modal_content_li1_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->crypto_wallet_send_modal_content_li2_key,$request->crypto_wallet_en_send_modal_content_li2_key,$request->crypto_wallet_fn_send_modal_content_li2_key,$request->crypto_wallet_sp_send_modal_content_li2_key,$request->crypto_wallet_ab_send_modal_content_li2_key,$request->crypto_wallet_gn_send_modal_content_li2_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->crypto_wallet_send_modal_content_li3_key,$request->crypto_wallet_en_send_modal_content_li3_key,$request->crypto_wallet_fn_send_modal_content_li3_key,$request->crypto_wallet_sp_send_modal_content_li3_key,$request->crypto_wallet_ab_send_modal_content_li3_key,$request->crypto_wallet_gn_send_modal_content_li3_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->crypto_wallet_send_modal_content_li4_key,$request->crypto_wallet_en_send_modal_content_li4_key,$request->crypto_wallet_fn_send_modal_content_li4_key,$request->crypto_wallet_sp_send_modal_content_li4_key,$request->crypto_wallet_ab_send_modal_content_li4_key,$request->crypto_wallet_gn_send_modal_content_li4_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->crypto_wallet_send_available_key,$request->crypto_wallet_en_send_available_key,$request->crypto_wallet_fn_send_available_key,$request->crypto_wallet_sp_send_available_key,$request->crypto_wallet_ab_send_available_key,$request->crypto_wallet_gn_send_available_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->crypto_wallet_send_withdraw_key,$request->crypto_wallet_en_send_withdraw_key,$request->crypto_wallet_fn_send_withdraw_key,$request->crypto_wallet_sp_send_withdraw_key,$request->crypto_wallet_ab_send_withdraw_key,$request->crypto_wallet_gn_send_withdraw_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->crypto_wallet_send_to_address_key,$request->crypto_wallet_en_send_to_address_key,$request->crypto_wallet_fn_send_to_address_key,$request->crypto_wallet_sp_send_to_address_key,$request->crypto_wallet_ab_send_to_address_key,$request->crypto_wallet_gn_send_to_address_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->crypto_wallet_send_amount_key,$request->crypto_wallet_en_send_amount_key,$request->crypto_wallet_fn_send_amount_key,$request->crypto_wallet_sp_send_amount_key,$request->crypto_wallet_ab_send_amount_key,$request->crypto_wallet_gn_send_amount_key,1);


           //    $pagecont16 = $this->pagecontrepo->pageContentUpdate($request->page_content_id16,$request->crypto_wallet_send_blockchain_fee_key,$request->crypto_wallet_en_send_blockchain_fee_key,$request->crypto_wallet_fn_send_blockchain_fee_key,$request->crypto_wallet_sp_send_blockchain_fee_key,$request->crypto_wallet_ab_send_blockchain_fee_key,$request->crypto_wallet_gn_send_blockchain_fee_key,1);


           //    $pagecont17 = $this->pagecontrepo->pageContentUpdate($request->page_content_id17,$request->crypto_wallet_send_receive_key,$request->crypto_wallet_en_send_receive_key,$request->crypto_wallet_fn_send_receive_key,$request->crypto_wallet_sp_send_receive_key,$request->crypto_wallet_ab_send_receive_key,$request->crypto_wallet_gn_send_receive_key,1);


           //    $pagecont18 = $this->pagecontrepo->pageContentUpdate($request->page_content_id18,$request->crypto_wallet_send_button_key,$request->crypto_wallet_en_send_button_key,$request->crypto_wallet_fn_send_button_key,$request->crypto_wallet_sp_send_button_key,$request->crypto_wallet_ab_send_button_key,$request->crypto_wallet_gn_send_button_key,1);

           //    $pagecont19 = $this->pagecontrepo->pageContentUpdate($request->page_content_id19,$request->crypto_wallet_receive_crypto_key,$request->crypto_wallet_en_receive_crypto_key,$request->crypto_wallet_fn_receive_crypto_key,$request->crypto_wallet_sp_receive_crypto_key,$request->crypto_wallet_ab_receive_crypto_key,$request->crypto_wallet_gn_receive_crypto_key,1);

           //    $pagecont20 = $this->pagecontrepo->pageContentUpdate($request->page_content_id20,$request->crypto_wallet_receive_modal_content_li1_key,$request->crypto_wallet_en_receive_modal_content_li1_key,$request->crypto_wallet_fn_receive_modal_content_li1_key,$request->crypto_wallet_sp_receive_modal_content_li1_key,$request->crypto_wallet_ab_receive_modal_content_li1_key,$request->crypto_wallet_gn_receive_modal_content_li1_key,1);

           //    $pagecont21 = $this->pagecontrepo->pageContentUpdate($request->page_content_id21,$request->crypto_wallet_receive_modal_content_li2_key,$request->crypto_wallet_en_receive_modal_content_li2_key,$request->crypto_wallet_fn_receive_modal_content_li2_key,$request->crypto_wallet_sp_receive_modal_content_li2_key,$request->crypto_wallet_ab_receive_modal_content_li2_key,$request->crypto_wallet_gn_receive_modal_content_li2_key,1); 

           //    $pagecont22 = $this->pagecontrepo->pageContentUpdate($request->page_content_id22,$request->crypto_wallet_receive_modal_content_li3_key,$request->crypto_wallet_en_receive_modal_content_li3_key,$request->crypto_wallet_fn_receive_modal_content_li3_key,$request->crypto_wallet_sp_receive_modal_content_li3_key,$request->crypto_wallet_ab_receive_modal_content_li3_key,$request->crypto_wallet_gn_receive_modal_content_li3_key,1);

           //    $pagecont23 = $this->pagecontrepo->pageContentUpdate($request->page_content_id23,$request->crypto_wallet_receive_select_key,$request->crypto_wallet_en_receive_select_key,$request->crypto_wallet_fn_receive_select_key,$request->crypto_wallet_sp_receive_select_key,$request->crypto_wallet_ab_receive_select_key,$request->crypto_wallet_gn_receive_select_key,1); 

           //    $pagecont24 = $this->pagecontrepo->pageContentUpdate($request->page_content_id24,$request->crypto_wallet_receive_send_address_key,$request->crypto_wallet_en_receive_send_address_key,$request->crypto_wallet_fn_receive_send_address_key,$request->crypto_wallet_sp_receive_send_address_key,$request->crypto_wallet_ab_receive_send_address_key,$request->crypto_wallet_gn_receive_send_address_key,1);
              
           //    $pagecont25 = $this->pagecontrepo->pageContentUpdate($request->page_content_id25,$request->crypto_wallet_receive_button_key,$request->crypto_wallet_en_receive_button_key,$request->crypto_wallet_fn_receive_button_key,$request->crypto_wallet_sp_receive_button_key,$request->crypto_wallet_ab_receive_button_key,$request->crypto_wallet_gn_receive_button_key,1);


           //    $pagecont26 = $this->pagecontrepo->pageContentUpdate($request->page_content_id26,$request->crypto_wallet_currency_wallet_key,$request->crypto_wallet_en_currency_wallet_key,$request->crypto_wallet_fn_currency_wallet_key,$request->crypto_wallet_sp_currency_wallet_key,$request->crypto_wallet_ab_currency_wallet_key,$request->crypto_wallet_gn_currency_wallet_key,1);


           //    $pagecont27 = $this->pagecontrepo->pageContentUpdate($request->page_content_id27,$request->crypto_wallet_deposit_table_head_key,$request->crypto_wallet_en_deposit_table_head_key,$request->crypto_wallet_fn_deposit_table_head_key,$request->crypto_wallet_sp_deposit_table_head_key,$request->crypto_wallet_ab_deposit_table_head_key,$request->crypto_wallet_gn_deposit_table_head_key,1);


           //    $pagecont28 = $this->pagecontrepo->pageContentUpdate($request->page_content_id28,$request->crypto_wallet_withdrawl_table_head_key,$request->crypto_wallet_en_withdrawl_table_head_key,$request->crypto_wallet_fn_withdrawl_table_head_key,$request->crypto_wallet_sp_withdrawl_table_head_key,$request->crypto_wallet_ab_withdrawl_table_head_key,$request->crypto_wallet_gn_withdrawl_table_head_key,1);


           //    $pagecont29 = $this->pagecontrepo->pageContentUpdate($request->page_content_id29,$request->crypto_wallet_recent_table_id_key,$request->crypto_wallet_en_recent_table_id_key,$request->crypto_wallet_fn_recent_table_id_key,$request->crypto_wallet_sp_recent_table_id_key,$request->crypto_wallet_ab_recent_table_id_key,$request->crypto_wallet_gn_recent_table_id_key,1);


           //    $pagecont30 = $this->pagecontrepo->pageContentUpdate($request->page_content_id30,$request->crypto_wallet_recent_table_date_key,$request->crypto_wallet_en_recent_table_date_key,$request->crypto_wallet_fn_recent_table_date_key,$request->crypto_wallet_sp_recent_table_date_key,$request->crypto_wallet_ab_recent_table_date_key,$request->crypto_wallet_gn_recent_table_date_key,1);


           //    $pagecont31 = $this->pagecontrepo->pageContentUpdate($request->page_content_id31,$request->crypto_wallet_recent_table_description_key,$request->crypto_wallet_en_recent_table_description_key,$request->crypto_wallet_fn_recent_table_description_key,$request->crypto_wallet_sp_recent_table_description_key,$request->crypto_wallet_ab_recent_table_description_key,$request->crypto_wallet_gn_recent_table_description_key,1);


           //    $pagecont32 = $this->pagecontrepo->pageContentUpdate($request->page_content_id32,$request->crypto_wallet_recent_table_amount_key,$request->crypto_wallet_en_recent_table_amount_key,$request->crypto_wallet_fn_recent_table_amount_key,$request->crypto_wallet_sp_recent_table_amount_key,$request->crypto_wallet_ab_recent_table_amount_key,$request->crypto_wallet_gn_recent_table_amount_key,1);


           //    $pagecont33 = $this->pagecontrepo->pageContentUpdate($request->page_content_id33,$request->crypto_wallet_recent_table_net_amount_key,$request->crypto_wallet_en_recent_table_net_amount_key,$request->crypto_wallet_fn_recent_table_net_amount_key,$request->crypto_wallet_sp_recent_table_net_amount_key,$request->crypto_wallet_ab_recent_table_net_amount_key,$request->crypto_wallet_gn_recent_table_net_amount_key,1);

           //    $pagecont34 = $this->pagecontrepo->pageContentUpdate($request->page_content_id34,$request->crypto_wallet_recent_table_status_key,$request->crypto_wallet_en_recent_table_status_key,$request->crypto_wallet_fn_recent_table_status_key,$request->crypto_wallet_sp_recent_table_status_key,$request->crypto_wallet_ab_recent_table_status_key,$request->crypto_wallet_gn_recent_table_status_key,1);

           //    $pagecont35 = $this->pagecontrepo->pageContentUpdate($request->page_content_id35,$request->crypto_wallet_deposit_table_empty_key,$request->crypto_wallet_en_deposit_table_empty_key,$request->crypto_wallet_fn_deposit_table_empty_key,$request->crypto_wallet_sp_deposit_table_empty_key,$request->crypto_wallet_ab_deposit_table_empty_key,$request->crypto_wallet_gn_deposit_table_empty_key,1);

           //    $pagecont36 = $this->pagecontrepo->pageContentUpdate($request->page_content_id36,$request->crypto_wallet_withdraw_table_empty_key,$request->crypto_wallet_en_withdraw_table_empty_key,$request->crypto_wallet_fn_withdraw_table_empty_key,$request->crypto_wallet_sp_withdraw_table_empty_key,$request->crypto_wallet_ab_withdraw_table_empty_key,$request->crypto_wallet_gn_withdraw_table_empty_key,1); 

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();
              $page_content_value_all_9 = array();$page_content_value_all_10 = array();
              $page_content_value_all_11 = array();$page_content_value_all_12 = array();
              $page_content_value_all_13 = array();$page_content_value_all_14 = array();
              $page_content_value_all_15 = array();$page_content_value_all_16 = array();
              $page_content_value_all_17 = array();$page_content_value_all_18 = array();
              $page_content_value_all_19 = array();$page_content_value_all_20 = array();
              $page_content_value_all_21 = array();$page_content_value_all_22 = array();
              $page_content_value_all_23 = array();$page_content_value_all_24 = array();
              $page_content_value_all_25 = array();$page_content_value_all_26 = array();
              $page_content_value_all_27 = array();$page_content_value_all_28 = array();
              $page_content_value_all_29 = array();$page_content_value_all_30 = array();
              $page_content_value_all_31 = array();$page_content_value_all_32 = array();
              $page_content_value_all_33 = array();$page_content_value_all_34 = array();
              $page_content_value_all_35 = array();$page_content_value_all_36 = array();
              $page_content_value_all_37 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();
              $page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();
              $page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();
              $page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();
              $page_content_value19 = array();$page_content_value20 = array();$page_content_value21 = array();
              $page_content_value22 = array();$page_content_value23 = array();$page_content_value24 = array();
              $page_content_value25 = array();$page_content_value_26 = array();$page_content_value_27 = array();
              $page_content_value_28 = array();$page_content_value_29 = array();$page_content_value_30 = array();
              $page_content_value_31 = array();$page_content_value_32 = array();$page_content_value_33 = array();
              $page_content_value_34 = array();$page_content_value_35 = array();$page_content_value_36 = array();
              $page_content_value_37 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

                            $page_content_values1 = 'crypto_wallet_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'crypto_wallet_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'crypto_wallet_'.$language->language_symbol.'_li_content1_key';
              $page_content_values4 = 'crypto_wallet_'.$language->language_symbol.'_li_content2_key';
              $page_content_values5 = 'crypto_wallet_'.$language->language_symbol.'_li_content3_key';
              $page_content_values6 = 'crypto_wallet_'.$language->language_symbol.'_li_content4_key';
              $page_content_values7 = 'crypto_wallet_'.$language->language_symbol.'_send_crypto_key';
              $page_content_values8 = 'crypto_wallet_'.$language->language_symbol.'_send_modal_content_key';
              $page_content_values9 = 'crypto_wallet_'.$language->language_symbol.'_send_modal_content_li1_key';
              $page_content_values10 = 'crypto_wallet_'.$language->language_symbol.'_send_modal_content_li2_key';
              $page_content_values11 = 'crypto_wallet_'.$language->language_symbol.'_send_modal_content_li3_key';
              $page_content_values12 = 'crypto_wallet_'.$language->language_symbol.'_send_modal_content_li4_key';
              $page_content_values13 = 'crypto_wallet_'.$language->language_symbol.'_send_available_key';
              $page_content_values14 = 'crypto_wallet_'.$language->language_symbol.'_send_withdraw_key';
              $page_content_values15 = 'crypto_wallet_'.$language->language_symbol.'_send_to_address_key';
              $page_content_values16 = 'crypto_wallet_'.$language->language_symbol.'_send_amount_key';
              $page_content_values17 = 'crypto_wallet_'.$language->language_symbol.'_send_blockchain_fee_key';
              $page_content_values18 = 'crypto_wallet_'.$language->language_symbol.'_send_receive_key';
              $page_content_values19 = 'crypto_wallet_'.$language->language_symbol.'_send_button_key';
              $page_content_values20 = 'crypto_wallet_'.$language->language_symbol.'_receive_crypto_key';
              $page_content_values21 = 'crypto_wallet_'.$language->language_symbol.'_receive_modal_content_li1_key';
              $page_content_values22 = 'crypto_wallet_'.$language->language_symbol.'_receive_modal_content_li2_key';
              $page_content_values23 = 'crypto_wallet_'.$language->language_symbol.'_receive_modal_content_li3_key';
              $page_content_values24 = 'crypto_wallet_'.$language->language_symbol.'_receive_select_key';
              $page_content_values25 = 'crypto_wallet_'.$language->language_symbol.'_receive_send_address_key';
              $page_content_values26 = 'crypto_wallet_'.$language->language_symbol.'_receive_button_key';
              $page_content_values27 = 'crypto_wallet_'.$language->language_symbol.'_currency_wallet_key';
              $page_content_values28 = 'crypto_wallet_'.$language->language_symbol.'_deposit_table_head_key';
              $page_content_values29 = 'crypto_wallet_'.$language->language_symbol.'_withdrawl_table_head_key';
              $page_content_values30 = 'crypto_wallet_'.$language->language_symbol.'_recent_table_id_key';
              $page_content_values31 = 'crypto_wallet_'.$language->language_symbol.'_recent_table_date_key';
              $page_content_values32 = 'crypto_wallet_'.$language->language_symbol.'_recent_table_description_key';
              $page_content_values33 = 'crypto_wallet_'.$language->language_symbol.'_recent_table_amount_key';
              $page_content_values34 = 'crypto_wallet_'.$language->language_symbol.'_recent_table_net_amount_key';
              $page_content_values35 = 'crypto_wallet_'.$language->language_symbol.'_recent_table_status_key';
              $page_content_values36 = 'crypto_wallet_'.$language->language_symbol.'_deposit_table_empty_key';
              $page_content_values37 = 'crypto_wallet_'.$language->language_symbol.'_withdraw_table_empty_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['crypto_wallet_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['crypto_wallet_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['crypto_wallet_li_content1_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['crypto_wallet_li_content2_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['crypto_wallet_li_content3_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['crypto_wallet_li_content4_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['crypto_wallet_send_crypto_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['crypto_wallet_send_modal_content_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['crypto_wallet_send_modal_content_li1_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['crypto_wallet_send_modal_content_li2_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['crypto_wallet_send_modal_content_li3_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['crypto_wallet_send_modal_content_li4_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['crypto_wallet_send_available_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['crypto_wallet_send_withdraw_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['crypto_wallet_send_amount_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['crypto_wallet_send_blockchain_fee_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['crypto_wallet_send_receive_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['crypto_wallet_send_button_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values19)) {
                   $page_content_value19['crypto_wallet_receive_crypto_key'] = $request->$page_content_values19;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values20)) {
                   $page_content_value20['crypto_wallet_receive_modal_content_li1_key'] = $request->$page_content_values20;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values21)) {
                   $page_content_value21['crypto_wallet_receive_modal_content_li2_key'] = $request->$page_content_values21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values22)) {
                   $page_content_value22['crypto_wallet_receive_modal_content_li3_key'] = $request->$page_content_values22;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values23)) {
                   $page_content_value23['crypto_wallet_receive_select_key'] = $request->$page_content_values23;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values24)) {
                   $page_content_value24['crypto_wallet_heading_button_buy_sell_key'] = $request->$page_content_values24;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values25)) {
                   $page_content_value25['crypto_wallet_receive_send_address_key'] = $request->$page_content_values25;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values26)) {
                   $page_content_value26['crypto_wallet_receive_button_key'] = $request->$page_content_values26;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values27)) {
                   $page_content_value27['crypto_wallet_currency_wallet_key'] = $request->$page_content_values27;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values28)) {
                   $page_content_value28['crypto_wallet_deposit_table_head_key'] = $request->$page_content_values28;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values29)) {
                   $page_content_value29['crypto_wallet_withdrawl_table_head_key'] = $request->$page_content_values29;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values30)) {
                   $page_content_value30['crypto_wallet_recent_table_id_key'] = $request->$page_content_values30;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values31)) {
                   $page_content_value31['crypto_wallet_recent_table_date_key'] = $request->$page_content_values31;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values32)) {
                   $page_content_value32['crypto_wallet_recent_table_description_key'] = $request->$page_content_values32;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values33)) {
                   $page_content_value33['crypto_wallet_recent_table_amount_key'] = $request->$page_content_values33;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values34)) {
                   $page_content_value34['crypto_wallet_recent_table_net_amount_key'] = $request->$page_content_values34;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values35)) {
                   $page_content_value35['crypto_wallet_recent_table_status_key'] = $request->$page_content_values35;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values36)) {
                   $page_content_value36['crypto_wallet_deposit_table_empty_key'] = $request->$page_content_values36;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values37)) {
                   $page_content_value37['crypto_wallet_withdraw_table_empty_key'] = $request->$page_content_values37;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              $page_content_value_all_19[$language->language_symbol] = $page_content_value19;
              $page_content_value_all_20[$language->language_symbol] = $page_content_value20;
              $page_content_value_all_21[$language->language_symbol] = $page_content_value21;
              $page_content_value_all_22[$language->language_symbol] = $page_content_value22;
              $page_content_value_all_23[$language->language_symbol] = $page_content_value23;
              $page_content_value_all_24[$language->language_symbol] = $page_content_value24;
              $page_content_value_all_25[$language->language_symbol] = $page_content_value25;
              $page_content_value_all_26[$language->language_symbol] = $page_content_value26;
              $page_content_value_all_27[$language->language_symbol] = $page_content_value27;
              $page_content_value_all_28[$language->language_symbol] = $page_content_value28;
              $page_content_value_all_29[$language->language_symbol] = $page_content_value29;
              $page_content_value_all_30[$language->language_symbol] = $page_content_value30;
              $page_content_value_all_31[$language->language_symbol] = $page_content_value31;
              $page_content_value_all_32[$language->language_symbol] = $page_content_value32;
              $page_content_value_all_33[$language->language_symbol] = $page_content_value33;
              $page_content_value_all_34[$language->language_symbol] = $page_content_value34;
              $page_content_value_all_35[$language->language_symbol] = $page_content_value35;
              $page_content_value_all_36[$language->language_symbol] = $page_content_value36;
              $page_content_value_all_37[$language->language_symbol] = $page_content_value37;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);
          $pagecont19 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id18,$page_content_value_all_19,1);
          $pagecont20 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id19,$page_content_value_all_20,1);
          $pagecont21 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id20,$page_content_value_all_21,1);
          $pagecont22 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id21,$page_content_value_all_22,1);
          $pagecont23 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id22,$page_content_value_all_23,1);
          $pagecont24 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id23,$page_content_value_all_24,1);
          $pagecont25 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id24,$page_content_value_all_25,1);
          $pagecont26 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id25,$page_content_value_all_26,1);
          $pagecont27 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id23,$page_content_value_all_27,1);
          $pagecont28 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id27,$page_content_value_all_28,1);
          $pagecont29 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id28,$page_content_value_all_29,1);
          $pagecont30 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id29,$page_content_value_all_30,1);
          $pagecont31 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id30,$page_content_value_all_31,1);
          $pagecont32 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id31,$page_content_value_all_32,1);
          $pagecont33 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id32,$page_content_value_all_33,1);
          $pagecont34 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id33,$page_content_value_all_34,1);
          $pagecont35 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id34,$page_content_value_all_35,1);
          $pagecont36 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id35,$page_content_value_all_36,1);
          $pagecont37 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id36,$page_content_value_all_37,1);

            return back()->with('success','Updated Successfully');
            
    } 

    //  Bank Account



     public function bankaccountContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->manage_bank_heading_key ,$request->manage_bank_en_heading_key,$request->manage_bank_fn_heading_key,$request->manage_bank_sp_heading_key,$request->manage_bank_ab_heading_key,$request->manage_bank_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->manage_bank_sub_heading_key,$request->manage_bank_en_sub_heading_key,$request->manage_bank_fn_sub_heading_key,$request->manage_bank_sp_sub_heading_key,$request->manage_bank_ab_sub_heading_key,$request->manage_bank_gn_sub_heading_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->manage_bank_add_bank_account_key,$request->manage_bank_en_add_bank_account_key,$request->manage_bank_fn_add_bank_account_key,$request->manage_bank_sp_add_bank_account_key,$request->manage_bank_ab_add_bank_account_key,$request->manage_bank_gn_add_bank_account_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->manage_bank_add_bank_account_modal_head_key,$request->manage_bank_en_add_bank_account_modal_head_key,$request->manage_bank_fn_add_bank_account_modal_head_key,$request->manage_bank_sp_add_bank_account_modal_head_key,$request->manage_bank_ab_add_bank_account_modal_head_key,$request->manage_bank_gn_add_bank_account_modal_head_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->manage_bank_account_holder_key,$request->manage_bank_en_account_holder_key,$request->manage_bank_fn_account_holder_key,$request->manage_bank_sp_account_holder_key,$request->manage_bank_ab_account_holder_key,$request->manage_bank_gn_account_holder_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->manage_bank_account_number_key,$request->manage_bank_en_account_number_key,$request->manage_bank_fn_account_number_key,$request->manage_bank_sp_account_number_key,$request->manage_bank_ab_account_number_key,$request->manage_bank_gn_account_number_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->manage_bank_account_name_key,$request->manage_bank_en_account_name_key,$request->manage_bank_fn_account_name_key,$request->manage_bank_sp_account_name_key,$request->manage_bank_ab_account_name_key,$request->manage_bank_gn_account_name_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->manage_bank_ifsc_key,$request->manage_bank_en_ifsc_key,$request->manage_bank_fn_ifsc_key,$request->manage_bank_sp_ifsc_key,$request->manage_bank_ab_ifsc_key,$request->manage_bank_gn_ifsc_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->manage_bank_description_key,$request->manage_bank_en_description_key,$request->manage_bank_fn_description_key,$request->manage_bank_sp_description_key,$request->manage_bank_ab_description_key,$request->manage_bank_gn_description_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->manage_bank_currency_key,$request->manage_bank_en_currency_key,$request->manage_bank_fn_currency_key,$request->manage_bank_sp_currency_key,$request->manage_bank_ab_currency_key,$request->manage_bank_gn_currency_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->manage_bank_button_add_key,$request->manage_bank_en_button_add_key,$request->manage_bank_fn_button_add_key,$request->manage_bank_sp_button_add_key,$request->manage_bank_ab_button_add_key,$request->manage_bank_gn_button_add_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->manage_bank_fiat_wallet_key,$request->manage_bank_en_fiat_wallet_key,$request->manage_bank_fn_fiat_wallet_key,$request->manage_bank_sp_fiat_wallet_key,$request->manage_bank_ab_fiat_wallet_key,$request->manage_bank_gn_fiat_wallet_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->manage_bank_wallet_key,$request->manage_bank_en_wallet_key,$request->manage_bank_fn_wallet_key,$request->manage_bank_sp_wallet_key,$request->manage_bank_ab_wallet_key,$request->manage_bank_gn_wallet_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->manage_bank_button_deposit_key,$request->manage_bank_en_button_deposit_key,$request->manage_bank_fn_button_deposit_key,$request->manage_bank_sp_button_deposit_key,$request->manage_bank_ab_button_deposit_key,$request->manage_bank_gn_button_deposit_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->manage_bank_button_withdraw_key,$request->manage_bank_en_button_withdraw_key,$request->manage_bank_fn_button_withdraw_key,$request->manage_bank_sp_button_withdraw_key,$request->manage_bank_ab_button_withdraw_key,$request->manage_bank_gn_button_withdraw_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->manage_bank_table_head_key,$request->manage_bank_en_table_head_key,$request->manage_bank_fn_table_head_key,$request->manage_bank_sp_table_head_key,$request->manage_bank_ab_table_head_key,$request->manage_bank_gn_table_head_key,1);


           //    $pagecont16 = $this->pagecontrepo->pageContentUpdate($request->page_content_id16,$request->manage_bank_table_modal_head_key,$request->manage_bank_en_table_modal_head_key,$request->manage_bank_fn_table_modal_head_key,$request->manage_bank_sp_table_modal_head_key,$request->manage_bank_ab_table_modal_head_key,$request->manage_bank_gn_table_modal_head_key,1);


           //    $pagecont17 = $this->pagecontrepo->pageContentUpdate($request->page_content_id17,$request->manage_bank_table_modal_content_key,$request->manage_bank_en_table_modal_content_key,$request->manage_bank_fn_table_modal_content_key,$request->manage_bank_sp_table_modal_content_key,$request->manage_bank_ab_table_modal_content_key,$request->manage_bank_gn_table_modal_content_key,1);


           //    $pagecont18 = $this->pagecontrepo->pageContentUpdate($request->page_content_id18,$request->manage_bank_table_th_account_key,$request->manage_bank_en_table_th_account_key,$request->manage_bank_fn_table_th_account_key,$request->manage_bank_sp_table_th_account_key,$request->manage_bank_ab_table_th_account_key,$request->manage_bank_gn_table_th_account_key,1);

           //    $pagecont19 = $this->pagecontrepo->pageContentUpdate($request->page_content_id19,$request->manage_bank_table_th_currency_key,$request->manage_bank_en_table_th_currency_key,$request->manage_bank_fn_table_th_currency_key,$request->manage_bank_sp_table_th_currency_key,$request->manage_bank_ab_table_th_currency_key,$request->manage_bank_gn_table_th_currency_key,1);

           //    $pagecont20 = $this->pagecontrepo->pageContentUpdate($request->page_content_id20,$request->manage_bank_table_th_description_key,$request->manage_bank_en_table_th_description_key,$request->manage_bank_fn_table_th_description_key,$request->manage_bank_sp_table_th_description_key,$request->manage_bank_ab_table_th_description_key,$request->manage_bank_gn_table_th_description_key,1);

           //    $pagecont21 = $this->pagecontrepo->pageContentUpdate($request->page_content_id21,$request->manage_bank_add_bank_account_modal_content_key,$request->manage_bank_en_add_bank_account_modal_content_key,$request->manage_bank_fn_add_bank_account_modal_content_key,$request->manage_bank_sp_add_bank_account_modal_content_key,$request->manage_bank_ab_add_bank_account_modal_content_key,$request->manage_bank_gn_add_bank_account_modal_content_key,1); 

           //    $pagecont22 = $this->pagecontrepo->pageContentUpdate($request->page_content_id22,$request->manage_bank_table_th_action_key,$request->manage_bank_en_table_th_action_key,$request->manage_bank_fn_table_th_action_key,$request->manage_bank_sp_table_th_action_key,$request->manage_bank_ab_table_th_action_key,$request->manage_bank_gn_table_th_action_key,1);

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();$page_content_value_all_9 = array();$page_content_value_all_10 = array();$page_content_value_all_11 = array();$page_content_value_all_12 = array();$page_content_value_all_13 = array();$page_content_value_all_14 = array();$page_content_value_all_15 = array();$page_content_value_all_16 = array();$page_content_value_all_17 = array();$page_content_value_all_18 = array();$page_content_value_all_19 = array();$page_content_value_all_20 = array();$page_content_value_all_21 = array();$page_content_value_all_22 = array();$page_content_value_all_23 = array();$page_content_value_all_24 = array();$page_content_value_all_25 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();$page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();$page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();$page_content_value19 = array();$page_content_value20 = array();$page_content_value21 = array();$page_content_value22 = array();$page_content_value23 = array();$page_content_value24 = array();
              $page_content_value25 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_values1 = 'manage_bank_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'manage_bank_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'manage_bank_'.$language->language_symbol.'_add_bank_account_key';
              $page_content_values4 = 'manage_bank_'.$language->language_symbol.'_add_bank_account_modal_head_key';
              $page_content_values5 = 'manage_bank_'.$language->language_symbol.'_account_holder_key';
              $page_content_values6 = 'manage_bank_'.$language->language_symbol.'_account_number_key';
              $page_content_values7 = 'manage_bank_'.$language->language_symbol.'_account_name_key';


              $page_content_values8 = 'manage_bank_'.$language->language_symbol.'_ifsc_key';
              $page_content_values9 = 'manage_bank_'.$language->language_symbol.'_description_key';
              $page_content_values10 = 'manage_bank_'.$language->language_symbol.'_currency_key';
              $page_content_values11 = 'manage_bank_'.$language->language_symbol.'_button_add_key';
              $page_content_values12 = 'manage_bank_'.$language->language_symbol.'_fiat_wallet_key';
              $page_content_values13 = 'manage_bank_'.$language->language_symbol.'_wallet_key';
              $page_content_values14 = 'manage_bank_'.$language->language_symbol.'_button_deposit_key';
              $page_content_values15 = 'manage_bank_'.$language->language_symbol.'_button_withdraw_key';
              $page_content_values16 = 'manage_bank_'.$language->language_symbol.'_table_head_key';
              $page_content_values17 = 'manage_bank_'.$language->language_symbol.'_table_modal_head_key';
              $page_content_values18 = 'manage_bank_'.$language->language_symbol.'_table_modal_content_key';
              $page_content_values19 = 'manage_bank_'.$language->language_symbol.'_table_th_account_key';
              $page_content_values20 = 'manage_bank_'.$language->language_symbol.'_table_th_currency_key';
              $page_content_values21 = 'manage_bank_'.$language->language_symbol.'_table_th_description_key';
              $page_content_values22 = 'manage_bank_'.$language->language_symbol.'_add_bank_account_modal_content_key';
              $page_content_values23 = 'manage_bank_'.$language->language_symbol.'_table_th_action_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['manage_bank_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['manage_bank_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['manage_bank_add_bank_account_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['manage_bank_add_bank_account_modal_head_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['manage_bank_account_holder_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['manage_bank_account_number_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['manage_bank_account_name_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['manage_bank_fee_level_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['manage_bank_description_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['manage_bank_currency_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['manage_bank_button_add_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['manage_bank_fiat_wallet_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['manage_bank_wallet_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['manage_bank_button_deposit_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['manage_bank_button_withdraw_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['manage_bank_table_head_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['manage_bank_table_modal_head_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['manage_bank_table_modal_content_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values19)) {
                   $page_content_value19['manage_bank_table_th_account_key'] = $request->$page_content_values19;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values20)) {
                   $page_content_value20['manage_bank_table_th_currency_key'] = $request->$page_content_values20;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values21)) {
                   $page_content_value21['manage_bank_table_th_description_key'] = $request->$page_content_values21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values22)) {
                   $page_content_value22['manage_bank_add_bank_account_modal_content_key'] = $request->$page_content_values22;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values23)) {
                   $page_content_value23['manage_bank_table_th_action_key'] = $request->$page_content_values23;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              $page_content_value_all_19[$language->language_symbol] = $page_content_value19;
              $page_content_value_all_20[$language->language_symbol] = $page_content_value20;
              $page_content_value_all_21[$language->language_symbol] = $page_content_value21;
              $page_content_value_all_22[$language->language_symbol] = $page_content_value22;
              $page_content_value_all_23[$language->language_symbol] = $page_content_value23;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);
          $pagecont19 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id18,$page_content_value_all_19,1);
          $pagecont20 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id19,$page_content_value_all_20,1);
          $pagecont21 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id20,$page_content_value_all_21,1);
          $pagecont22 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id21,$page_content_value_all_22,1);
          $pagecont23 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id22,$page_content_value_all_23,1);

            return back()->with('success','Updated Successfully');
            
    } 

    //  Home or Index



     public function homeContentEdit(Request $request)
    {
          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();
              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();
          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_value11 = 'home_'.$language->language_symbol.'_heading_key';
              $page_content_value21 = 'home_'.$language->language_symbol.'_graph_go_key';
              $page_content_value31 = 'home_'.$language->language_symbol.'_table_head_markets_key';
              $page_content_value41 = 'home_'.$language->language_symbol.'_table_th_pair_key';
              $page_content_value51 = 'home_'.$language->language_symbol.'_table_th_last_price_key';
              $page_content_value61 = 'home_'.$language->language_symbol.'_table_th_24hchanges_key';
              $page_content_value71 = 'home_'.$language->language_symbol.'_table_th_24hvolume_key';
              $symbol = $language->language_symbol;
              if (isset($request->$page_content_value11)) {
                   $page_content_value1['home_heading_key'] = $request->$page_content_value11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value21)) {
                   $page_content_value2['home_graph_go_key'] = $request->$page_content_value21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value31)) {
                   $page_content_value3['home_table_head_markets_key'] = $request->$page_content_value31;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value41)) {
                   $page_content_value4['home_table_th_pair_key'] = $request->$page_content_value41;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value51)) {
                   $page_content_value5['home_table_th_last_price_key'] = $request->$page_content_value51;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value61)) {
                   $page_content_value6['home_table_th_24hchanges_key'] = $request->$page_content_value61;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_value71)) {
                   $page_content_value7['home_table_th_24hvolume_key'] = $request->$page_content_value71;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);
              // exit;


            return back()->with('success','Updated Successfully');
            
    } 

    // About Us

     public function aboutusContentEdit(Request $request){

      // print_r($request->page_content_id);exit;
       $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->about_us_content_key ,$request->about_us_en_content,$request->about_us_fn_content,$request->about_us_sp_content,$request->about_us_ab_content,$request->about_us_gn_content,1);
              
            $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->about_us_heading_key,$request->about_us_en_heading,$request->about_us_fn_heading,$request->about_us_sp_heading,$request->about_us_ab_heading,$request->about_us_gn_heading,1);


            return back()->with('success','Updated Successfully');
     }

    // How it works

     public function howitContentEdit(Request $request){

      // print_r($request->page_content_id);exit;
       // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->how_it_works_content_key_key ,$request->how_it_works_en_content_key,$request->how_it_works_fn_content_key,$request->how_it_works_sp_content_key,$request->how_it_works_ab_content_key,$request->how_it_works_gn_content_key,1);
              
       //      $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->how_it_works_heading_key,$request->how_it_works_en_heading_key,$request->how_it_works_fn_heading_key,$request->how_it_works_sp_heading_key,$request->how_it_works_ab_heading_key,$request->how_it_works_gn_heading_key,1);

      $languages = $this->languagerepo->selectLanguageActive();
      $title = 0;
      $page_content_value_all_1 = array();$page_content_value_all_2 = array();
      $page_content_value1 = array();$page_content_value2 = array();

      foreach ($languages as $key => $language) {

          $page_content_values1 = 'how_it_works_'.$language->language_symbol.'_heading_key';
          $page_content_values2 = 'how_it_works_'.$language->language_symbol.'_content_key';
          $symbol = $language->language_symbol;
          if (isset($request->$page_content_values1)) {
                $page_content_value1['how_it_works_heading_key'] = $request->$page_content_values1;
          }
          if (isset($request->$page_content_values2)) {
                $page_content_value2['how_it_works_content_key'] = $request->$page_content_values2;
          }
          $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
          $page_content_value_all_2[$language->language_symbol] = $page_content_value2;

          

      }

          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);

            return back()->with('success','Updated Successfully');
     }

    // Terms and Condition

     public function termsContentEdit(Request $request){

      // print_r($request->page_content_id);exit;
       // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->terms_condition_heading_key ,$request->terms_condition_en_heading_key,$request->terms_condition_fn_heading_key,$request->terms_condition_sp_heading_key,$request->terms_condition_ab_heading_key,$request->terms_condition_gn_heading_key,1);
              
       //      $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->terms_condition_content_key,$request->terms_condition_en_content_key,$request->terms_condition_en_content_key,$request->terms_condition_sp_content_key,$request->terms_condition_ab_content_key,$request->terms_condition_gn_content_key,1);

       $languages = $this->languagerepo->selectLanguageActive();
      $title = 0;
      $page_content_value_all_1 = array();$page_content_value_all_2 = array();
      $page_content_value1 = array();$page_content_value2 = array();

      foreach ($languages as $key => $language) {

          $page_content_values1 = 'terms_condition_'.$language->language_symbol.'_heading_key';
          $page_content_values2 = 'terms_condition_'.$language->language_symbol.'_content_key';
          $symbol = $language->language_symbol;
          if (isset($request->$page_content_values1)) {
                $page_content_value1['terms_condition_heading_key'] = $request->$page_content_values1;
          }
          if (isset($request->$page_content_values2)) {
                $page_content_value2['terms_condition_content_key'] = $request->$page_content_values2;
          }
          $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
          $page_content_value_all_2[$language->language_symbol] = $page_content_value2;

          

      }

          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);


            return back()->with('success','Updated Successfully');
     }

    // Api

     public function apiContentEdit(Request $request){

      // print_r($request->page_content_id);exit;
       // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->api_heading_key ,$request->api_en_heading,$request->api_fn_heading,$request->api_sp_heading,$request->api_ab_heading,$request->api_gn_heading,1);
              
       //      $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->api_content_key,$request->api_en_content,$request->api_fn_content,$request->api_sp_content,$request->api_ab_content,$request->api_gn_content,1);

       $languages = $this->languagerepo->selectLanguageActive();
      $title = 0;
      $page_content_value_all_1 = array();$page_content_value_all_2 = array();
      $page_content_value1 = array();$page_content_value2 = array();

      foreach ($languages as $key => $language) {

          $page_content_values1 = 'api_'.$language->language_symbol.'_heading_key';
          $page_content_values2 = 'api_'.$language->language_symbol.'_content_key';
          $symbol = $language->language_symbol;
          if (isset($request->$page_content_values1)) {
                $page_content_value1['api_heading_key'] = $request->$page_content_values1;
          }
          if (isset($request->$page_content_values2)) {
                $page_content_value2['api_content_key'] = $request->$page_content_values2;
          }
          $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
          $page_content_value_all_2[$language->language_symbol] = $page_content_value2;

          

      }

          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);


            return back()->with('success','Updated Successfully');
     }

    //  Withdraw



     public function withdrawContentEdit(Request $request)
    {
           // $pagecont = $this->pagecontrepo->pageContentUpdate($request->page_content_id,$request->withdraw_heading_key ,$request->withdraw_en_heading_key,$request->withdraw_fn_heading_key,$request->withdraw_sp_heading_key,$request->withdraw_ab_heading_key,$request->withdraw_gn_heading_key,1);
              
           //  $pagecont1 = $this->pagecontrepo->pageContentUpdate($request->page_content_id1,$request->withdraw_sub_heading_key,$request->withdraw_en_sub_heading_key,$request->withdraw_fn_sub_heading_key,$request->withdraw_sp_sub_heading_key,$request->withdraw_ab_sub_heading_key,$request->withdraw_gn_sub_heading_key,1);
              
           //  $pagecont2 = $this->pagecontrepo->pageContentUpdate($request->page_content_id2,$request->withdraw_heading_button_key,$request->withdraw_en_heading_button_key,$request->withdraw_fn_heading_button_key,$request->withdraw_sp_heading_button_key,$request->withdraw_ab_heading_button_key,$request->withdraw_gn_heading_button_key,1);
              
           //  $pagecont3 = $this->pagecontrepo->pageContentUpdate($request->page_content_id3,$request->withdraw_fiat_currency_key,$request->withdraw_en_fiat_currency_key,$request->withdraw_fn_fiat_currency_key,$request->withdraw_sp_fiat_currency_key,$request->withdraw_ab_fiat_currency_key,$request->withdraw_gn_fiat_currency_key,1);
              
           //  $pagecont4 = $this->pagecontrepo->pageContentUpdate($request->page_content_id4,$request->withdraw_fiat_currency_trans_modal_head_key,$request->withdraw_en_fiat_currency_trans_modal_head_key,$request->withdraw_fn_fiat_currency_trans_modal_head_key,$request->withdraw_sp_fiat_currency_trans_modal_head_key,$request->withdraw_ab_fiat_currency_trans_modal_head_key,$request->withdraw_gn_fiat_currency_trans_modal_head_key,1);
              
           //  $pagecont5 = $this->pagecontrepo->pageContentUpdate($request->page_content_id5,$request->withdraw_fiat_currency_trans_modal_content_key,$request->withdraw_en_fiat_currency_trans_modal_content_key,$request->withdraw_fn_fiat_currency_trans_modal_content_key,$request->withdraw_sp_fiat_currency_trans_modal_content_key,$request->withdraw_ab_fiat_currency_trans_modal_content_key,$request->withdraw_gn_fiat_currency_trans_modal_content_key,1);

           //    $pagecont6 = $this->pagecontrepo->pageContentUpdate($request->page_content_id6,$request->withdraw_form_available_key,$request->withdraw_en_form_available_key,$request->withdraw_fn_form_available_key,$request->withdraw_sp_form_available_key,$request->withdraw_ab_form_available_key,$request->withdraw_gn_form_available_key,1);


           //    $pagecont7 = $this->pagecontrepo->pageContentUpdate($request->page_content_id7,$request->withdraw_bank_account_key,$request->withdraw_en_bank_account_key,$request->withdraw_fn_bank_account_key,$request->withdraw_sp_bank_account_key,$request->withdraw_ab_bank_account_key,$request->withdraw_gn_bank_account_key,1);


           //    $pagecont8 = $this->pagecontrepo->pageContentUpdate($request->page_content_id8,$request->withdraw_amount_key,$request->withdraw_en_amount_key,$request->withdraw_fn_amount_key,$request->withdraw_sp_amount_key,$request->withdraw_ab_amount_key,$request->withdraw_gn_amount_key,1);


           //    $pagecont9 = $this->pagecontrepo->pageContentUpdate($request->page_content_id9,$request->withdraw_fee_key,$request->withdraw_en_fee_key,$request->withdraw_fn_fee_key,$request->withdraw_sp_fee_key,$request->withdraw_ab_fee_key,$request->withdraw_gn_fee_key,1);


           //    $pagecont10 = $this->pagecontrepo->pageContentUpdate($request->page_content_id10,$request->withdraw_receive_key,$request->withdraw_en_receive_key,$request->withdraw_fn_receive_key,$request->withdraw_sp_receive_key,$request->withdraw_ab_receive_key,$request->withdraw_gn_receive_key,1);


           //    $pagecont11 = $this->pagecontrepo->pageContentUpdate($request->page_content_id11,$request->withdraw_add_key,$request->withdraw_en_add_key,$request->withdraw_fn_add_key,$request->withdraw_sp_add_key,$request->withdraw_ab_add_key,$request->withdraw_gn_add_key,1);


           //    $pagecont12 = $this->pagecontrepo->pageContentUpdate($request->page_content_id12,$request->withdraw_fiat_wallet_key,$request->withdraw_en_fiat_wallet_key,$request->withdraw_fn_fiat_wallet_key,$request->withdraw_sp_fiat_wallet_key,$request->withdraw_ab_fiat_wallet_key,$request->withdraw_gn_fiat_wallet_key,1);


           //    $pagecont13 = $this->pagecontrepo->pageContentUpdate($request->page_content_id13,$request->withdraw_wallet_key,$request->withdraw_en_wallet_key,$request->withdraw_fn_wallet_key,$request->withdraw_sp_wallet_key,$request->withdraw_ab_wallet_key,$request->withdraw_gn_wallet_key,1);


           //    $pagecont14 = $this->pagecontrepo->pageContentUpdate($request->page_content_id14,$request->withdraw_table_history_head_key,$request->withdraw_en_table_history_head_key,$request->withdraw_fn_table_history_head_key,$request->withdraw_sp_table_history_head_key,$request->withdraw_ab_table_history_head_key,$request->withdraw_gn_table_history_head_key,1);


           //    $pagecont15 = $this->pagecontrepo->pageContentUpdate($request->page_content_id15,$request->withdraw_table_history_modal_head_key,$request->withdraw_en_table_history_modal_head_key,$request->withdraw_fn_table_history_modal_head_key,$request->withdraw_sp_table_history_modal_head_key,$request->withdraw_ab_table_history_modal_head_key,$request->withdraw_gn_table_history_modal_head_key,1);


           //    $pagecont16 = $this->pagecontrepo->pageContentUpdate($request->page_content_id16,$request->withdraw_table_history_modal_content_key,$request->withdraw_en_table_history_modal_content_key,$request->withdraw_fn_table_history_modal_content_key,$request->withdraw_sp_table_history_modal_content_key,$request->withdraw_ab_table_history_modal_content_key,$request->withdraw_gn_table_history_modal_content_key,1);


           //    $pagecont17 = $this->pagecontrepo->pageContentUpdate($request->page_content_id17,$request->withdraw_table_history_table_th_date_key,$request->withdraw_en_table_history_table_th_date_key,$request->withdraw_fn_table_history_table_th_date_key,$request->withdraw_sp_table_history_table_th_date_key,$request->withdraw_ab_table_history_table_th_date_key,$request->withdraw_gn_table_history_table_th_date_key,1);


           //    $pagecont18 = $this->pagecontrepo->pageContentUpdate($request->page_content_id18,$request->withdraw_table_history_table_th_amount_key,$request->withdraw_en_table_history_table_th_amount_key,$request->withdraw_fn_table_history_table_th_amount_key,$request->withdraw_sp_table_history_table_th_amount_key,$request->withdraw_ab_table_history_table_th_amount_key,$request->withdraw_gn_table_history_table_th_amount_key,1);

           //    $pagecont19 = $this->pagecontrepo->pageContentUpdate($request->page_content_id19,$request->withdraw_table_history_table_th_status_key,$request->withdraw_en_table_history_table_th_status_key,$request->withdraw_fn_table_history_table_th_status_key,$request->withdraw_sp_table_history_table_th_status_key,$request->withdraw_ab_table_history_table_th_status_key,$request->withdraw_gn_table_history_table_th_status_key,1);

           //    $pagecont20 = $this->pagecontrepo->pageContentUpdate($request->page_content_id20,$request->withdraw_heading_wallet_button_key,$request->withdraw_en_heading_wallet_button_key,$request->withdraw_fn_heading_wallet_button_key,$request->withdraw_sp_heading_wallet_button_key,$request->withdraw_ab_heading_wallet_button_key,$request->withdraw_gn_heading_wallet_button_key,1);

           //    $pagecont21 = $this->pagecontrepo->pageContentUpdate($request->page_content_id21,$request->withdraw_table_history_no_record_key,$request->withdraw_en_table_history_no_record_key,$request->withdraw_fn_table_history_no_record_key,$request->withdraw_sp_table_history_no_record_key,$request->withdraw_ab_table_history_no_record_key,$request->withdraw_gn_table_history_no_record_key,1); 

          $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();$page_content_value_all_9 = array();$page_content_value_all_10 = array();$page_content_value_all_11 = array();$page_content_value_all_12 = array();$page_content_value_all_13 = array();$page_content_value_all_14 = array();$page_content_value_all_15 = array();$page_content_value_all_16 = array();$page_content_value_all_17 = array();$page_content_value_all_18 = array();$page_content_value_all_19 = array();$page_content_value_all_20 = array();$page_content_value_all_21 = array();$page_content_value_all_22 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();$page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();$page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();$page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();$page_content_value19 = array();$page_content_value20 = array();$page_content_value21 = array();$page_content_value22 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

              $page_content_values1 = 'withdraw_'.$language->language_symbol.'_heading_key';
              $page_content_values2 = 'withdraw_'.$language->language_symbol.'_sub_heading_key';
              $page_content_values3 = 'withdraw_'.$language->language_symbol.'_heading_button_key';
              $page_content_values4 = 'withdraw_'.$language->language_symbol.'_fiat_currency_key';
              $page_content_values5 = 'withdraw_'.$language->language_symbol.'_fiat_currency_trans_modal_head_key';
              $page_content_values6 = 'withdraw_'.$language->language_symbol.'_fiat_currency_trans_modal_content_key';
              $page_content_values7 = 'withdraw_'.$language->language_symbol.'_form_available_key';


              $page_content_values8 = 'withdraw_'.$language->language_symbol.'_bank_account_key';
              $page_content_values9 = 'withdraw_'.$language->language_symbol.'_amount_key';
              $page_content_values10 = 'withdraw_'.$language->language_symbol.'_fee_key';
              $page_content_values11 = 'withdraw_'.$language->language_symbol.'_receive_key';
              $page_content_values12 = 'withdraw_'.$language->language_symbol.'_add_key';
              $page_content_values13 = 'withdraw_'.$language->language_symbol.'_fiat_wallet_key';
              $page_content_values14 = 'withdraw_'.$language->language_symbol.'_wallet_key';
              $page_content_values15 = 'withdraw_'.$language->language_symbol.'_table_history_head_key';
              $page_content_values16 = 'withdraw_'.$language->language_symbol.'_table_history_modal_head_key';
              $page_content_values17 = 'withdraw_'.$language->language_symbol.'_table_history_modal_content_key';
              $page_content_values18 = 'withdraw_'.$language->language_symbol.'_table_history_table_th_date_key';
              $page_content_values19 = 'withdraw_'.$language->language_symbol.'_table_history_table_th_amount_key';
              $page_content_values20 = 'withdraw_'.$language->language_symbol.'_table_history_table_th_status_key';
              $page_content_values21 = 'withdraw_'.$language->language_symbol.'_heading_wallet_button_key';
              $page_content_values22 = 'withdraw_'.$language->language_symbol.'_table_history_no_record_key';


              $symbol = $language->language_symbol;
              // print_r($page_content_value1['withdraw_heading_key'] = $request->$page_content_values1);exit;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['withdraw_heading_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";exit();
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['withdraw_sub_heading_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['withdraw_heading_button_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['withdraw_fiat_currency_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['withdraw_fiat_currency_trans_modal_head_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['withdraw_fiat_currency_trans_modal_content_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['withdraw_form_available_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['withdraw_fee_level_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['withdraw_amount_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['withdraw_fee_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['withdraw_receive_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['withdraw_add_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['withdraw_fiat_wallet_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['withdraw_wallet_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['withdraw_table_history_head_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['withdraw_table_history_modal_head_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['withdraw_table_history_modal_content_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['withdraw_table_history_table_th_date_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values19)) {
                   $page_content_value19['withdraw_table_history_table_th_amount_key'] = $request->$page_content_values19;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values20)) {
                   $page_content_value20['withdraw_table_history_table_th_status_key'] = $request->$page_content_values20;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values21)) {
                   $page_content_value21['withdraw_heading_wallet_button_key'] = $request->$page_content_values21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values22)) {
                   $page_content_value22['withdraw_table_history_no_record_key'] = $request->$page_content_values22;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              $page_content_value_all_19[$language->language_symbol] = $page_content_value19;
              $page_content_value_all_20[$language->language_symbol] = $page_content_value20;
              $page_content_value_all_21[$language->language_symbol] = $page_content_value21;
              $page_content_value_all_22[$language->language_symbol] = $page_content_value22;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);
          $pagecont19 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id18,$page_content_value_all_19,1);
          $pagecont20 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id19,$page_content_value_all_20,1);
          $pagecont21 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id20,$page_content_value_all_21,1);
          $pagecont22 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id21,$page_content_value_all_22,1);


            return back()->with('success','Updated Successfully');
            
    } 



    //  Simple Trade



     public function simpletradeContentEdit(Request $request)
    {
           $languages = $this->languagerepo->selectLanguageActive();
          $title = 0;
              
              $page_content_value_all_1 = array();$page_content_value_all_2 = array();
              $page_content_value_all_3 = array();$page_content_value_all_4 = array();
              $page_content_value_all_5 = array();$page_content_value_all_6 = array();
              $page_content_value_all_7 = array();$page_content_value_all_8 = array();
              $page_content_value_all_9 = array();$page_content_value_all_10 = array();
              $page_content_value_all_11 = array();$page_content_value_all_12 = array();
              $page_content_value_all_13 = array();$page_content_value_all_14 = array();
              $page_content_value_all_15 = array();$page_content_value_all_16 = array();
              $page_content_value_all_17 = array();$page_content_value_all_18 = array();
              $page_content_value_all_19 = array();$page_content_value_all_20 = array();
              $page_content_value_all_21 = array();$page_content_value_all_22 = array();

              $page_content_value_all_23 = array();$page_content_value_all_24 = array();
              $page_content_value_all_25 = array();$page_content_value_all_26 = array();
              $page_content_value_all_27 = array();$page_content_value_all_28 = array();
              $page_content_value_all_29 = array();$page_content_value_all_30 = array();
              $page_content_value_all_31 = array();$page_content_value_all_32 = array();
              $page_content_value_all_33 = array();$page_content_value_all_34 = array();
              $page_content_value_all_35 = array();$page_content_value_all_36 = array();
              $page_content_value_all_37 = array();$page_content_value_all_38 = array();
              $page_content_value_all_39 = array();$page_content_value_all_40 = array();
              $page_content_value_all_41 = array();$page_content_value_all_42 = array();
              $page_content_value_all_43 = array();$page_content_value_all_44 = array();
              $page_content_value_all_45 = array();$page_content_value_all_46 = array();
              $page_content_value_all_47 = array();$page_content_value_all_48 = array();
              $page_content_value_all_49 = array();$page_content_value_all_50 = array();
              $page_content_value_all_51 = array();$page_content_value_all_52 = array();
              $page_content_value_all_53 = array();$page_content_value_all_54 = array();
              $page_content_value_all_55 = array();$page_content_value_all_56 = array();
              $page_content_value_all_57 = array();$page_content_value_all_58 = array();
              $page_content_value_all_59 = array();$page_content_value_all_60 = array();
              $page_content_value_all_61 = array();$page_content_value_all_62 = array();
              $page_content_value_all_63 = array();$page_content_value_all_64 = array();
              $page_content_value_all_65 = array();

              $page_content_value1 = array();$page_content_value2 = array();$page_content_value3 = array();
              $page_content_value4 = array();$page_content_value5 = array();$page_content_value6 = array();
              $page_content_value7 = array();$page_content_value8 = array();$page_content_value9 = array();
              $page_content_value10 = array();$page_content_value11 = array();$page_content_value12 = array();
              $page_content_value13 = array();$page_content_value14 = array();$page_content_value15 = array();
              $page_content_value16 = array();$page_content_value17 = array();$page_content_value18 = array();
              $page_content_value19 = array();$page_content_value20 = array();$page_content_value21 = array();
              $page_content_value22 = array();
              $page_content_value_23 = array();$page_content_value_24 = array();
              $page_content_value_25 = array();$page_content_value_26 = array();
              $page_content_value_27 = array();$page_content_value_28 = array();
              $page_content_value_29 = array();$page_content_value_30 = array();
              $page_content_value_31 = array();$page_content_value_32 = array();
              $page_content_value_33 = array();$page_content_value_34 = array();
              $page_content_value_35 = array();$page_content_value_36 = array();
              $page_content_value_37 = array();$page_content_value_38 = array();
              $page_content_value_39 = array();$page_content_value_40 = array();
              $page_content_value_41 = array();$page_content_value_42 = array();
              $page_content_value_43 = array();$page_content_value_44 = array();
              $page_content_value_45 = array();$page_content_value_46 = array();
              $page_content_value_47 = array();$page_content_value_48 = array();
              $page_content_value_49 = array();$page_content_value_50 = array();
              $page_content_value_51 = array();$page_content_value_52 = array();
              $page_content_value_53 = array();$page_content_value_54 = array();
              $page_content_value_55 = array();$page_content_value_56 = array();
              $page_content_value_57 = array();$page_content_value_58 = array();
              $page_content_value_59 = array();$page_content_value_60 = array();
              $page_content_value_61 = array();$page_content_value_62 = array();
              $page_content_value_63 = array();$page_content_value_64 = array();
              $page_content_value_65 = array();

          foreach ($languages as $key => $language) {
            // if ($title > 4) {

                           


              $page_content_values1 = 'simple_trade_'.$language->language_symbol.'last_price_key';
              $page_content_values2 = 'simple_trade_'.$language->language_symbol.'_24change_key';
              $page_content_values3 = 'simple_trade_'.$language->language_symbol.'_24volume_key';
              $page_content_values4 = 'simple_trade_'.$language->language_symbol.'_li_content1_key';
              $page_content_values5 = 'simple_trade_'.$language->language_symbol.'_li_content2_key';
              $page_content_values6 = 'simple_trade_'.$language->language_symbol.'_li_content3_key';
              $page_content_values7 = 'simple_trade_'.$language->language_symbol.'_li_content4_key';
              $page_content_values8 = 'simple_trade_'.$language->language_symbol.'_li_content4_deposite_key';
              $page_content_values9 = 'simple_trade_'.$language->language_symbol.'_open_market_key';
              $page_content_values10 = 'simple_trade_'.$language->language_symbol.'_open_market_content_key';
              $page_content_values11 = 'simple_trade_'.$language->language_symbol.'_open_market_tab_buy_key';
              $page_content_values12 = 'simple_trade_'.$language->language_symbol.'_open_market_tab_sell_key';
              $page_content_values13 = 'simple_trade_'.$language->language_symbol.'_open_market_table_price_key';
              $page_content_values14 = 'simple_trade_'.$language->language_symbol.'_open_market_table_amount_key';
              $page_content_values15 = 'simple_trade_'.$language->language_symbol.'_open_market_table_total_key';
              $page_content_values16 = 'simple_trade_'.$language->language_symbol.'_currency_pair_key';
              $page_content_values17 = 'simple_trade_'.$language->language_symbol.'_currency_pair_modal_content_key';
              $page_content_values18 = 'simple_trade_'.$language->language_symbol.'_currency_pair_modal_li1_key';
              $page_content_values19 = 'simple_trade_'.$language->language_symbol.'_currency_pair_modal_li2_key';
              $page_content_values20 = 'simple_trade_'.$language->language_symbol.'_currency_pair_modal_li3_key';
              $page_content_values21 = 'simple_trade_'.$language->language_symbol.'_currency_pair_modal_li4_key';
              $page_content_values22 = 'simple_trade_'.$language->language_symbol.'_currency_pair_table_pair_key';
              $page_content_values23 = 'simple_trade_'.$language->language_symbol.'_currency_pair_table_price_key';
              $page_content_values24 = 'simple_trade_'.$language->language_symbol.'_currency_pair_table_change_key';
              $page_content_values25 = 'simple_trade_'.$language->language_symbol.'_buy_sell_key';

              $page_content_values26 = 'simple_trade_'.$language->language_symbol.'_buy_sell_modal_content_key';
              $page_content_values27 = 'simple_trade_'.$language->language_symbol.'_buy_sell_modal_li1_key';
              $page_content_values28 = 'simple_trade_'.$language->language_symbol.'_buy_sell_modal_li2_key';
              $page_content_values29 = 'simple_trade_'.$language->language_symbol.'_buy_sell_modal_li3_key';
              $page_content_values30 = 'simple_trade_'.$language->language_symbol.'_buy_sell_modal_li4_key';
              $page_content_values31 = 'simple_trade_'.$language->language_symbol.'_buy_sell_modal_li5_key';
              $page_content_values32 = 'simple_trade_'.$language->language_symbol.'_buy_crypto_key';
              $page_content_values33 = 'simple_trade_'.$language->language_symbol.'_available_key';
              $page_content_values34 = 'simple_trade_'.$language->language_symbol.'_buy_amount_key';
              $page_content_values35 = 'simple_trade_'.$language->language_symbol.'_buy_market_key';
              $page_content_values36 = 'simple_trade_'.$language->language_symbol.'_limit_key';
              $page_content_values37 = 'simple_trade_'.$language->language_symbol.'_stop_key';
              $page_content_values38 = 'simple_trade_'.$language->language_symbol.'_limit_price_key';
              $page_content_values39 = 'simple_trade_'.$language->language_symbol.'_subtotal_key';
              $page_content_values40 = 'simple_trade_'.$language->language_symbol.'_fee_key';
              $page_content_values41 = 'simple_trade_'.$language->language_symbol.'_approx_key';
              $page_content_values42 = 'simple_trade_'.$language->language_symbol.'_to_spend_key';
              $page_content_values43 = 'simple_trade_'.$language->language_symbol.'_buy_button_key';
              $page_content_values44 = 'simple_trade_'.$language->language_symbol.'_confirm_key';
              $page_content_values45 = 'simple_trade_'.$language->language_symbol.'_buy_confirm_amount_key';
              $page_content_values46 = 'simple_trade_'.$language->language_symbol.'_buy_confirm_button_key';
              $page_content_values47 = 'simple_trade_'.$language->language_symbol.'_buy_confirm_button_back_key';
              $page_content_values48 = 'simple_trade_'.$language->language_symbol.'_buy_confirm_button_content_key';
              $page_content_values49 = 'simple_trade_'.$language->language_symbol.'_sell_crypto_key';
              $page_content_values50 = 'simple_trade_'.$language->language_symbol.'_sell_amount_key';
              $page_content_values51 = 'simple_trade_'.$language->language_symbol.'_sell_market_key';
              $page_content_values52 = 'simple_trade_'.$language->language_symbol.'_sell_button_key';
              $page_content_values53 = 'simple_trade_'.$language->language_symbol.'_sell_confirm_amount_key';
              $page_content_values54 = 'simple_trade_'.$language->language_symbol.'_history_key';
              $page_content_values55 = 'simple_trade_'.$language->language_symbol.'_history_table_pair_key';
              $page_content_values56 = 'simple_trade_'.$language->language_symbol.'_history_table_price_key';
              $page_content_values57 = 'simple_trade_'.$language->language_symbol.'_history_table_amount_key';
              $page_content_values58 = 'simple_trade_'.$language->language_symbol.'_open_market_sell_no_data_key';
              $page_content_values59 = 'simple_trade_'.$language->language_symbol.'_open_market_buy_no_data_key';
              $page_content_values60 = 'simple_trade_'.$language->language_symbol.'_referral_bonus_key';
              $page_content_values61 = 'simple_trade_'.$language->language_symbol.'_history_table_no_data_key';
              $page_content_values62 = 'simple_trade_'.$language->language_symbol.'_to_receive_key';
              $page_content_values63 = 'simple_trade_'.$language->language_symbol.'_sell_confirm_button_key';
              $page_content_values64 = 'simple_trade_'.$language->language_symbol.'_sell_confirm_button_key';
              $page_content_values65 = 'simple_trade_'.$language->language_symbol.'_currency_use_key';


              $symbol = $language->language_symbol;
              if (isset($request->$page_content_values1)) {
                   $page_content_value1['simple_tradelast_price_key'] = $request->$page_content_values1;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values2)) {
                   $page_content_value2['simple_trade_24change_key'] = $request->$page_content_values2;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values3)) {
                   $page_content_value3['simple_trade_24volume_key'] = $request->$page_content_values3;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values4)) {
                   $page_content_value4['simple_trade_li_content1_key'] = $request->$page_content_values4;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values5)) {
                   $page_content_value5['simple_trade_li_content2_key'] = $request->$page_content_values5;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values6)) {
                   $page_content_value6['simple_trade_li_content3_key'] = $request->$page_content_values6;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values7)) {
                   $page_content_value7['simple_trade_li_content4_key'] = $request->$page_content_values7;
                   // echo $page_content_value1."string1<br>";
              }


              if (isset($request->$page_content_values8)) {
                   $page_content_value8['simple_trade_li_content4_deposite_key'] = $request->$page_content_values8;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values9)) {
                   $page_content_value9['simple_trade_open_market_key'] = $request->$page_content_values9;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values10)) {
                   $page_content_value10['simple_trade_open_market_content_key'] = $request->$page_content_values10;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values11)) {
                   $page_content_value11['simple_trade_open_market_tab_buy_key'] = $request->$page_content_values11;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values12)) {
                   $page_content_value12['simple_trade_open_market_tab_sell_key'] = $request->$page_content_values12;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values13)) {
                   $page_content_value13['simple_trade_open_market_table_price_key'] = $request->$page_content_values13;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values14)) {
                   $page_content_value14['simple_trade_open_market_table_amount_key'] = $request->$page_content_values14;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values15)) {
                   $page_content_value15['simple_trade_open_market_table_total_key'] = $request->$page_content_values15;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values16)) {
                   $page_content_value16['simple_trade_currency_pair_key'] = $request->$page_content_values16;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values17)) {
                   $page_content_value17['simple_trade_currency_pair_modal_content_key'] = $request->$page_content_values17;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values18)) {
                   $page_content_value18['simple_trade_currency_pair_modal_li1_key'] = $request->$page_content_values18;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values19)) {
                   $page_content_value19['simple_trade_currency_pair_modal_li2_key'] = $request->$page_content_values19;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values20)) {
                   $page_content_value20['simple_trade_currency_pair_modal_li3_key'] = $request->$page_content_values20;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values21)) {
                   $page_content_value21['simple_trade_currency_pair_modal_li4_key'] = $request->$page_content_values21;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values22)) {
                   $page_content_value22['simple_trade_currency_pair_table_pair_key'] = $request->$page_content_values22;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values23)) {
                   $page_content_value23['simple_trade_currency_pair_table_price_key'] = $request->$page_content_values23;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values24)) {
                   $page_content_value24['simple_trade_currency_pair_table_change_key'] = $request->$page_content_values24;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values25)) {
                   $page_content_value25['simple_trade_buy_sell_key'] = $request->$page_content_values25;
                   // echo $page_content_value1."string1<br>";
              }



              if (isset($request->$page_content_values26)) {
                   $page_content_value26['simple_trade_buy_sell_modal_content_key'] = $request->$page_content_values26;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values27)) {
                   $page_content_value27['simple_trade_buy_sell_modal_li1_key'] = $request->$page_content_values27;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values28)) {
                   $page_content_value28['simple_trade_buy_sell_modal_li2_key'] = $request->$page_content_values28;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values29)) {
                   $page_content_value29['simple_trade_buy_sell_modal_li3_key'] = $request->$page_content_values29;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values30)) {
                   $page_content_value30['simple_trade_buy_sell_modal_li4_key'] = $request->$page_content_values30;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values31)) {
                   $page_content_value31['simple_trade_buy_sell_modal_li5_key'] = $request->$page_content_values31;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values32)) {
                   $page_content_value32['simple_trade_buy_crypto_key'] = $request->$page_content_values32;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values33)) {
                   $page_content_value33['simple_trade_available_key'] = $request->$page_content_values33;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values34)) {
                   $page_content_value34['simple_trade_buy_amount_key'] = $request->$page_content_values34;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values35)) {
                   $page_content_value35['simple_trade_buy_market_key'] = $request->$page_content_values35;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values36)) {
                   $page_content_value36['simple_trade_limit_key'] = $request->$page_content_values36;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values37)) {
                   $page_content_value37['simple_trade_stop_key'] = $request->$page_content_values37;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values38)) {
                   $page_content_value38['simple_trade_limit_price_key'] = $request->$page_content_values38;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values39)) {
                   $page_content_value39['simple_trade_subtotal_key'] = $request->$page_content_values39;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values40)) {
                   $page_content_value40['simple_trade_fee_key'] = $request->$page_content_values40;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values41)) {
                   $page_content_value41['simple_trade_approx_key'] = $request->$page_content_values41;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values42)) {
                   $page_content_value42['simple_trade_to_spend_key'] = $request->$page_content_values42;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values43)) {
                   $page_content_value43['simple_trade_buy_button_key'] = $request->$page_content_values43;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values44)) {
                   $page_content_value44['simple_trade_confirm_key'] = $request->$page_content_values44;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values45)) {
                   $page_content_value45['simple_trade_buy_confirm_amount_key'] = $request->$page_content_values45;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values46)) {
                   $page_content_value46['simple_trade_buy_confirm_button_key'] = $request->$page_content_values46;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values47)) {
                   $page_content_value47['simple_trade_buy_confirm_button_back_key'] = $request->$page_content_values47;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values48)) {
                   $page_content_value48['simple_trade_buy_confirm_button_content_key'] = $request->$page_content_values48;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values49)) {
                   $page_content_value49['simple_trade_sell_crypto_key'] = $request->$page_content_values49;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values50)) {
                   $page_content_value50['simple_trade_sell_amount_key'] = $request->$page_content_values50;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values51)) {
                   $page_content_value51['simple_trade_sell_market_key'] = $request->$page_content_values51;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values52)) {
                   $page_content_value52['simple_trade_sell_button_key'] = $request->$page_content_values52;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values53)) {
                   $page_content_value53['simple_trade_sell_confirm_amount_key'] = $request->$page_content_values53;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values54)) {
                   $page_content_value54['simple_trade_history_key'] = $request->$page_content_values54;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values55)) {
                   $page_content_value55['simple_trade_history_table_pair_key'] = $request->$page_content_values55;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values56)) {
                   $page_content_value56['simple_trade_history_table_price_key'] = $request->$page_content_values56;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values57)) {
                   $page_content_value57['simple_trade_history_table_amount_key'] = $request->$page_content_values57;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values58)) {
                   $page_content_value58['simple_trade_open_market_sell_no_data_key'] = $request->$page_content_values58;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values59)) {
                   $page_content_value59['simple_trade_open_market_buy_no_data_key'] = $request->$page_content_values59;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values60)) {
                   $page_content_value60['simple_trade_referral_bonus_key'] = $request->$page_content_values60;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values61)) {
                   $page_content_value61['simple_trade_history_table_no_data_key'] = $request->$page_content_values61;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values62)) {
                   $page_content_value62['simple_trade_to_receive_key'] = $request->$page_content_values62;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values63)) {
                   $page_content_value63['simple_trade_sell_confirm_button_key'] = $request->$page_content_values63;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values64)) {
                   $page_content_value64['simple_trade_sell_confirm_button_key'] = $request->$page_content_values64;
                   // echo $page_content_value1."string1<br>";
              }
              if (isset($request->$page_content_values65)) {
                   $page_content_value65['simple_trade_currency_use_key'] = $request->$page_content_values65;
                   // echo $page_content_value1."string1<br>";
              }
              $page_content_value_all_1[$language->language_symbol] = $page_content_value1;
              $page_content_value_all_2[$language->language_symbol] = $page_content_value2;
              $page_content_value_all_3[$language->language_symbol] = $page_content_value3;
              $page_content_value_all_4[$language->language_symbol] = $page_content_value4;
              $page_content_value_all_5[$language->language_symbol] = $page_content_value5;
              $page_content_value_all_6[$language->language_symbol] = $page_content_value6;
              $page_content_value_all_7[$language->language_symbol] = $page_content_value7;


              $page_content_value_all_8[$language->language_symbol] = $page_content_value8;
              $page_content_value_all_9[$language->language_symbol] = $page_content_value9;
              $page_content_value_all_10[$language->language_symbol] = $page_content_value10;
              $page_content_value_all_11[$language->language_symbol] = $page_content_value11;
              $page_content_value_all_12[$language->language_symbol] = $page_content_value12;
              $page_content_value_all_13[$language->language_symbol] = $page_content_value13;
              $page_content_value_all_14[$language->language_symbol] = $page_content_value14;
              $page_content_value_all_15[$language->language_symbol] = $page_content_value15;
              $page_content_value_all_16[$language->language_symbol] = $page_content_value16;
              $page_content_value_all_17[$language->language_symbol] = $page_content_value17;
              $page_content_value_all_18[$language->language_symbol] = $page_content_value18;
              $page_content_value_all_19[$language->language_symbol] = $page_content_value19;
              $page_content_value_all_20[$language->language_symbol] = $page_content_value20;
              $page_content_value_all_21[$language->language_symbol] = $page_content_value21;
              $page_content_value_all_22[$language->language_symbol] = $page_content_value22;
              $page_content_value_all_23[$language->language_symbol] = $page_content_value23;
              $page_content_value_all_24[$language->language_symbol] = $page_content_value24;
              $page_content_value_all_25[$language->language_symbol] = $page_content_value25;

              $page_content_value_all_26[$language->language_symbol] = $page_content_value26;
              $page_content_value_all_27[$language->language_symbol] = $page_content_value27;
              $page_content_value_all_28[$language->language_symbol] = $page_content_value28;
              $page_content_value_all_29[$language->language_symbol] = $page_content_value29;
              $page_content_value_all_30[$language->language_symbol] = $page_content_value30;
              $page_content_value_all_31[$language->language_symbol] = $page_content_value31;
              $page_content_value_all_32[$language->language_symbol] = $page_content_value32;
              $page_content_value_all_33[$language->language_symbol] = $page_content_value33;
              $page_content_value_all_34[$language->language_symbol] = $page_content_value34;
              $page_content_value_all_35[$language->language_symbol] = $page_content_value35;
              $page_content_value_all_36[$language->language_symbol] = $page_content_value36;
              $page_content_value_all_37[$language->language_symbol] = $page_content_value37;
              $page_content_value_all_38[$language->language_symbol] = $page_content_value38;
              $page_content_value_all_39[$language->language_symbol] = $page_content_value39;
              $page_content_value_all_40[$language->language_symbol] = $page_content_value40;
              $page_content_value_all_41[$language->language_symbol] = $page_content_value41;
              $page_content_value_all_42[$language->language_symbol] = $page_content_value42;
              $page_content_value_all_43[$language->language_symbol] = $page_content_value43;
              $page_content_value_all_44[$language->language_symbol] = $page_content_value44;
              $page_content_value_all_45[$language->language_symbol] = $page_content_value45;
              $page_content_value_all_46[$language->language_symbol] = $page_content_value46;
              $page_content_value_all_47[$language->language_symbol] = $page_content_value47;
              $page_content_value_all_48[$language->language_symbol] = $page_content_value48;
              $page_content_value_all_49[$language->language_symbol] = $page_content_value49;
              $page_content_value_all_50[$language->language_symbol] = $page_content_value50;
              $page_content_value_all_51[$language->language_symbol] = $page_content_value51;
              $page_content_value_all_52[$language->language_symbol] = $page_content_value52;
              $page_content_value_all_53[$language->language_symbol] = $page_content_value53;
              $page_content_value_all_54[$language->language_symbol] = $page_content_value54;
              $page_content_value_all_55[$language->language_symbol] = $page_content_value55;
              $page_content_value_all_56[$language->language_symbol] = $page_content_value56;
              $page_content_value_all_57[$language->language_symbol] = $page_content_value57;
              $page_content_value_all_58[$language->language_symbol] = $page_content_value58;
              $page_content_value_all_59[$language->language_symbol] = $page_content_value59;
              $page_content_value_all_60[$language->language_symbol] = $page_content_value60;
              $page_content_value_all_61[$language->language_symbol] = $page_content_value61;
              $page_content_value_all_62[$language->language_symbol] = $page_content_value62;
              $page_content_value_all_63[$language->language_symbol] = $page_content_value63;
              $page_content_value_all_64[$language->language_symbol] = $page_content_value64;
              $page_content_value_all_65[$language->language_symbol] = $page_content_value65;
              
            // }
              $title++;
          }
          // print_r($page_content_value_all_1);
          // exit;
           
          $pagecont1 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id,$page_content_value_all_1,1);
          $pagecont2 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id1,$page_content_value_all_2,1);
          $pagecont3 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id2,$page_content_value_all_3,1);
          $pagecont4 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id3,$page_content_value_all_4,1);
          $pagecont5 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id4,$page_content_value_all_5,1);
          $pagecont6 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id5,$page_content_value_all_6,1);
          $pagecont7 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id6,$page_content_value_all_7,1);

          $pagecont8 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id7,$page_content_value_all_8,1);
          $pagecont9 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id8,$page_content_value_all_9,1);
          $pagecont10 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id9,$page_content_value_all_10,1);
          $pagecont11 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id10,$page_content_value_all_11,1);

          $pagecont12 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id11,$page_content_value_all_12,1);
          $pagecont13 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id12,$page_content_value_all_13,1);
          $pagecont14 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id13,$page_content_value_all_14,1);
          $pagecont15 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id14,$page_content_value_all_15,1);
          $pagecont16 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id15,$page_content_value_all_16,1);
          $pagecont17 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id16,$page_content_value_all_17,1);
          $pagecont18 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id17,$page_content_value_all_18,1);
          $pagecont19 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id18,$page_content_value_all_19,1);
          $pagecont20 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id19,$page_content_value_all_20,1);
          $pagecont21 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id20,$page_content_value_all_21,1);
          $pagecont22 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id21,$page_content_value_all_22,1);
          $pagecont23 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id22,$page_content_value_all_23,1);
          $pagecont24 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id23,$page_content_value_all_24,1);
          $pagecont25 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id24,$page_content_value_all_25,1);


          $pagecont26 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id25,$page_content_value_all_26,1);
          $pagecont27 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id26,$page_content_value_all_27,1);
          $pagecont28 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id27,$page_content_value_all_28,1);
          $pagecont29 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id28,$page_content_value_all_29,1);
          $pagecont30 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id29,$page_content_value_all_30,1);
          $pagecont31 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id30,$page_content_value_all_31,1);
          $pagecont32 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id31,$page_content_value_all_32,1);
          $pagecont33 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id32,$page_content_value_all_33,1);
          $pagecont34 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id33,$page_content_value_all_34,1);
          $pagecont35 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id34,$page_content_value_all_35,1);
          $pagecont36 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id35,$page_content_value_all_36,1);
          $pagecont37 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id36,$page_content_value_all_37,1);
          $pagecont38 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id37,$page_content_value_all_38,1);
          $pagecont39 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id38,$page_content_value_all_39,1);
          $pagecont40 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id39,$page_content_value_all_40,1);
          $pagecont41 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id40,$page_content_value_all_41,1);
          $pagecont42 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id41,$page_content_value_all_42,1);
          $pagecont43 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id42,$page_content_value_all_43,1);
          $pagecont44 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id43,$page_content_value_all_44,1);
          $pagecont45 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id44,$page_content_value_all_45,1);
          $pagecont46 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id45,$page_content_value_all_46,1);
          $pagecont47 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id46,$page_content_value_all_47,1);
          $pagecont48 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id47,$page_content_value_all_48,1);
          $pagecont49 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id48,$page_content_value_all_49,1);
          $pagecont50 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id49,$page_content_value_all_50,1);
          $pagecont51 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id50,$page_content_value_all_51,1);
          $pagecont52 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id51,$page_content_value_all_52,1);
          $pagecont53 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id52,$page_content_value_all_53,1);
          $pagecont54 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id53,$page_content_value_all_54,1);
          $pagecont55 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id54,$page_content_value_all_55,1);
          $pagecont56 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id55,$page_content_value_all_56,1);
          $pagecont57 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id56,$page_content_value_all_57,1);
          $pagecont58 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id57,$page_content_value_all_58,1);
          $pagecont59 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id58,$page_content_value_all_59,1);
          $pagecont60 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id59,$page_content_value_all_60,1);
          $pagecont61 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id60,$page_content_value_all_61,1);
          $pagecont62 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id61,$page_content_value_all_62,1);
          $pagecont63 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id62,$page_content_value_all_63,1);
          $pagecont64 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id63,$page_content_value_all_64,1);
          // $pagecont65 = $this->pagecontrepo->pageContentUpdatenew($request->page_content_id64,$page_content_value_all_65,1);

// exit;
            return back()->with('success','Updated Successfully');
            
    } 





}