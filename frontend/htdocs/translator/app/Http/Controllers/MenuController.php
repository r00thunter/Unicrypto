<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\MenuRepository;
use App\Repositories\LanguageRepository;
use Validator;
use Auth;

class MenuController extends BaseController
{
	protected $trans_menu;

	public function __construct(MenuRepository $menurepo,LanguageRepository $languagerepo)
    {
        $this->menurepo = $menurepo;
        $this->languagerepo = $languagerepo; 
      
    }
   // View Language Main page and List All Language
    public function index()
    {
    	if (Auth::check()) {
    		
    			$menu = $this->menurepo->selectmenu();
                $language = $this->languagerepo->selectLanguage();
                //print_r($menu);exit();
        		return view('menu.home', compact('menu','language'));
    		
        }else{
        	return redirect('/login');
        }
    }

    // Add New Language 
    public function createMenu(Request $request)
    {
    	
    		$request = $request->all();
            if (count($request) == 8) {
                $request['status'] = 1;
            }else{
                $request['status'] = 0;
            }
            $request['menu_key'] = 'menu_'.$request['menu_key'].'_key';
        	$menu = $this->menurepo->createMenu($request);
        	// print_r($language);exit;
        	if ($menu) {
        		return back()->with('success','Menu added successfully');
        	}else{
        		return back()->with('error','Menu Not added');
        	}
    }

    // Edit Language 
    public function editMenu(Request $request)
    {
    	
    		$request = $request->all();
            if (count($request) == 9) {
                $request['status'] = 1;
            }else{
                $request['status'] = 0;
            }
        	$menu    = $this->menurepo->editMenu($request);
        	// print_r($language);exit;
        	if ($menu) {
        		return back()->with('success','Menu Updated successfully');
        	}else{
        		return back()->with('error','Menu not Updated');
        	}
    }

    // Delate Language 
    public function deleteMenu(Request $request)
    {
    	
    		$request = $request->all();
        	$menu = $this->menurepo->deleteMenu($request);
        	// print_r($language);exit;
        	if ($menu) {
        		return back()->with('success','Menu Deleteed successfully');
        	}else{
        		return back()->with('error','Menu not Deleted Updated');
        	}
    }
    
}
