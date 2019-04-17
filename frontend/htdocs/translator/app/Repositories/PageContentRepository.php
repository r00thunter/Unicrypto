<?php 
namespace App\Repositories;

use App\Repositories\Repository;
use App\TranslatorPageContent;
use Illuminate\Http\Request;
use DB;

class PageContentRepository extends Repository
{

    /**
     * constructor for this class
     *
     * @param Language $trans_lang
     * @retuen object
     */
    public function __construct(TranslatorPageContent $trans_PageContent){

        $this->trans_PageContent = $trans_PageContent;
    }

        public function pageContentAdd($page_id,$page_content_key,$en_page_content,$fn_page_content,$sp_page_content,$ab_page_content,$gn_page_content,$page_content_status)        
        {     
          
          $page_content_key1 = $page_content_key;
          // echo $page_content_key1.'<br>';exit;
           $input = array();
           $input['page_id'] = $page_id;
           $input['page_content_key'] = $page_content_key1;
           $input['en_page_content'] = $en_page_content;
           $input['fn_page_content'] = $fn_page_content;
           $input['sp_page_content'] = $sp_page_content;
           $input['ab_page_content'] = $ab_page_content;
           $input['gn_page_content'] = $gn_page_content;
           $input['page_content_status'] = $page_content_status;

            $PageContent = $this->trans_PageContent->insert($input);
            return $PageContent;
         }  
           public function pageContentUpdate($page_id,$page_content_key,$en_page_content,$fn_page_content,$sp_page_content,$ab_page_content,$gn_page_content,$page_content,$page_content_status)        
        {  
              // echo $page_id;exit;
           $input = array();
           // $input['page_id'] = $page_id;
           $input['page_content_key'] = $page_content_key;
           $input['en_page_content'] = $en_page_content;
           $input['fn_page_content'] = $fn_page_content;
           $input['sp_page_content'] = $sp_page_content;
           $input['ab_page_content'] = $ab_page_content;
           $input['gn_page_content'] = $gn_page_content;
           $input['page_content'] = json_encode($page_content);
           $input['page_content_status'] = $page_content_status;
        
            $PageContent = $this->trans_PageContent->find($page_id)->update($input);
            // echo "string";exit;
            return $PageContent;
         }  
           public function pageContentUpdatenew($page_id,$page_content,$page_content_status)        
        {  
              // echo $page_id;exit;
           $input = array();
           $input['page_id'] = $page_id;
           // $input['page_content_key'] = $page_content_key;
           // $input['en_page_content'] = $en_page_content;
           // $input['fn_page_content'] = $fn_page_content;
           // $input['sp_page_content'] = $sp_page_content;
           // $input['ab_page_content'] = $ab_page_content;
           // $input['gn_page_content'] = $gn_page_content;
           $input['page_content'] = json_encode($page_content);
           $input['page_content_status'] = $page_content_status;
        
            $PageContent = $this->trans_PageContent->find($page_id)->update($input);
            // echo "string";exit;
            return $PageContent;
         }  
         public function pageContent($id){
           $PageContent  = $this->trans_PageContent->where('page_id',$id)->get();
           return $PageContent;
         } 

         public function pageContentView($id){
          
           switch ($id) {
             case 1:
               return 'page-content.index';
               break;
               case '2':
               return 'page-content.balance';
               break;
               case '3':
               return 'page-content.login';
               break;
               case 4:
               return 'page-content.register';
               break;
               case '5':
               return 'page-content.forgot-password';
               break;
               case '6':
               return 'page-content.open-orders';
               break;
               case '7':
               return 'page-content.trade-history';
               break;
               case '8':
               return 'page-content.deposit';
               break;
               case '9':
               return 'page-content.withdraw';
               break;
               case '10':
               return 'page-content.bank-account';
               break;
               case '11':
               return 'page-content.crypto-address';
               break;
               case '12':
               return 'page-content.order-history';
               break;
               case '13':
               return 'page-content.simple-trade';
               break;
               case '14':
               return 'page-content.crypto-wallet';
               break;
               case '16':
               return 'page-content.profile-setting';
               break;
               case '17':
               return 'page-content.profile';
               break;
               case '18':
               return 'page-content.about-us';
               break;
               case '19':
               return 'page-content.how-it-works';
               break;
               case '20':
               return 'page-content.terms';
               break;
               case '21':
               return 'page-content.api';
               break;
             default:
               # code...
               break;
           }

         } 
 

   
}