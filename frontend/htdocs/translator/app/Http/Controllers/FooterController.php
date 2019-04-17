<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\FooterRepository;
use App\Repositories\LanguageRepository;
use Validator;
use Auth;

class FooterController extends BaseController
{
	protected $trans_footer;

	public function __construct(FooterRepository $footerrepo,LanguageRepository $languagerepo)
    {
        $this->footerrepo = $footerrepo;
        $this->languagerepo = $languagerepo;      
    }
   // View Language Main page and List All Language
    public function index()
    {
    	if (Auth::check()) {
    		
    			$footers = $this->footerrepo->selectFooter();
                $language = $this->languagerepo->selectLanguage();
                 //print_r($footer);exit();
        		return view('footer.home', compact('footers','language'));
    		
        }else{
        	return redirect('/login');
        }
    }

    // Add New Language 
    public function createFooter(Request $request)
    {
    	
    		$request = $request->all();
             if (count($request) == 9) {
                $request['status'] = 1;
            }else{
                $request['status'] = 0;
            }
            $request['menu_key'] = 'footer_'.$request['menu_key'].'_key';
            // echo $request['menu_en'];exit;
        	$footer = $this->footerrepo->createFooter($request);
        	// print_r($language);exit;
        	if ($footer) {
        		return back()->with('success','footer added successfully');
        	}else{
        		return back()->with('error','footer Not added');
        	}
    }

    // Edit Language 
    public function editFooter(Request $request)
    {
    	// dd($request);exit;
    		$request = $request->all();
            // echo count($request);exit;
             if (count($request) == 9) {
                $request['status'] = 1;
            }else{
                $request['status'] = 0;
            }
            
        	$footer    = $this->footerrepo->editFooter($request);
        	// print_r($language);exit;
        	if ($footer) {
        		return back()->with('success','footer Updated successfully');
        	}else{
        		return back()->with('error','footer not Updated');
        	}
    }

    // Delate Language 
    public function deleteFooter(Request $request)
    {
    	
    		$request = $request->all();
        	$footer = $this->footerrepo->deleteFooter($request);
        	// print_r($language);exit;
        	if ($footer) {
        		return back()->with('success','footer Deleteed successfully');
        	}else{
        		return back()->with('error','footer not Deleted Updated');
        	}
    }
    
}
