<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PageRepository;
use Validator;
use Auth;

class PageController extends BaseController
{
	protected $trans_lang;

	public function __construct(PageRepository $pagesrepo)
    {
        $this->pagerepo = $pagesrepo;
      
    }
   // View Page and List All Page
    public function index()
    {
    	if (Auth::check()) {
    		
    			$pages = $this->pagerepo->selectPage();
        		return view('page.home', compact('pages'));
    		
        }else{
        	return redirect('/login');
        }
    }

    // Add New Page 
    public function createPage(Request $request)
    {
    	
    		$request = $request->all();
            if (count($request) == 3) {
                $request['page_status'] = 1;
            }else{
                $request['page_status'] = 0;
            }
        	$pages = $this->pagerepo->createPage($request);
        	// print_r($language);exit;
        	if ($pages) {
        		return back()->with('success','New Page added successfully');
        	}else{
        		return back()->with('error','Page Not added');
        	}
    }

    // Edit Page 
    public function editPage(Request $request)
    {
    	
    		$request = $request->all();
            if (count($request) == 4) {
                $request['page_status'] = 1;
            }else{
                $request['page_status'] = 0;
            }
        	$pages = $this->pagerepo->editPage($request);
        	// print_r($language);exit;
        	if ($pages) {
        		return back()->with('success','Page Updated successfully');
        	}else{
        		return back()->with('error','Page not Updated');
        	}
    }

    // Delete Page 
    public function deletePage(Request $request)
    {
    	
    		$request = $request->all();
        	$pages = $this->pagerepo->deletePage($request);
        	// print_r($language);exit;
        	if ($pages) {
        		return back()->with('success','Page Deleted successfully');
        	}else{
        		return back()->with('error','Page Deleted successfully');
        	}
    }



    // Page Content Adding
    public function createPage1($id)
    {
        echo $id;exit;
            $request = $request->all();
            $pages = $this->pagerepo->createPage($request);
            // print_r($language);exit;
            if (count($request) == 3) {
                $request['page_status'] = 1;
            }else{
                $request['page_status'] = 0;
            }

            if ($pages) {
                return back()->with('success','New Page added successfully');
            }else{
                return back()->with('error','Page Not added');
            }
    }
    
}
