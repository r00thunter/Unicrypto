<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;
use Validator;
use Auth;

class LanguageController extends BaseController
{
	protected $trans_lang;

	public function __construct(LanguageRepository $languagerepo)
    {
        $this->languagerepo = $languagerepo;
      
    }
   // View Language Main page and List All Language
    public function index()
    {
    	if (Auth::check()) {
    		
    			$languages = $this->languagerepo->selectLanguage();
        		return view('language.home', compact('languages'));
    		
        }else{
        	return redirect('/login');
        }
    }

    // Add New Language 
    public function createLanguage(Request $request)
    {
    	
    		$request = $request->all();
            if (count($request) == 5) {
                $request['language_status'] = 1;
            }else{
                $request['language_status'] = 0;
            }
        	$language = $this->languagerepo->createLanguage($request);
        	// print_r($language);exit;
        	if ($language) {
        		return back()->with('success','Language added successfully');
        	}else{
        		return back()->with('error','Language Not added');
        	}
    }

    // Edit Language 
    public function editLanguage(Request $request)
    {
    	
    		$request = $request->all();
            // echo count($request);exit;
            if (count($request) == 5) {
                $request['language_status'] = 1;
            }else{
                $request['language_status'] = 0;
            }
            // print_r($request);exit;
        	$language = $this->languagerepo->editLanguage($request);
        	// print_r($language);exit;
        	if ($language) {
        		return back()->with('success','Language Updated successfully');
        	}else{
        		return back()->with('error','Language not Updated');
        	}
    }

    // Delate Language 
    public function deleteLanguage(Request $request)
    {
    	
    		$request = $request->all();
        	$language = $this->languagerepo->deleteLanguage($request);
        	// print_r($language);exit;
        	if ($language) {
        		return back()->with('success','Language Deleteed successfully');
        	}else{
        		return back()->with('error','Language not Deleted Updated');
        	}
    }
    
}
