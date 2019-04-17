<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\MediaRepository;
use Validator;
use Auth;
use App\TranslatorMedia;

class MediaController extends BaseController
{
	protected $trans_media;

	public function __construct(MediaRepository $mediarepo)
    {
        $this->mediarepo = $mediarepo;
      
    }
   // View Language Main page and List All Language
    public function index()
    {
    	if (Auth::check()) {
    		
    			$media = $this->mediarepo->selectMedia();
                 //print_r($footer);exit();
        		return view('media.home', compact('media'));
    		
        }else{
        	return redirect('/login');
        }
    }

    // Add New Language 


      public function createMedia(Request $request)
    {

            $image = $this->base_image_upload_add($request,'media_image');
            // print_r($image);exit;
            $request = $request->all();
            $request['media_image'] = $image;
            //print_r($request);exit;
            $media = $this->mediarepo->createMedia($request);
            //$media = $this->mediarepo->createMedia($request);
            
            if ($media) {
                return back()->with('success','Icon added successfully');
            }else{
                return back()->with('error','Icon Not added');
            }
    }


    public function editMedia(Request $request)
    {        

           

            if($request->media_image)
            {                
                    $image = $this->base_image_upload_update($request,'media_image');
            }
            else{
                    $image=$request->media_old;   
            }

    		$request = $request->all();
            $request['media_image'] = $image;

            
            //print_r($request);exit;
            $media = $this->mediarepo->editMedia($request);
        	//$media = $this->mediarepo->createMedia($request);
        	
        	if ($media) {
        		return back()->with('success','Icon updated successfully');
        	}else{
        		return back()->with('error','Icon Not updated');
        	}
    }

    // Edit Language 
   

    // Delate Language 
    public function deleteMedia(Request $request)
    {
    	
    		$request = $request->all();
        	$media = $this->mediarepo->deleteMedia($request);
        	// print_r($language);exit;
        	if ($media) {
        		return back()->with('success','Icon Deleteed successfully');
        	}else{
        		return back()->with('error','Icon not Deleted Updated');
        	}
    }
    
}