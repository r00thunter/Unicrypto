<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

define('BASE_URL', 'http://13.58.12.71/translator/');
class BaseController extends Controller{

     public function generate_random_string()    
     {        
        return rand(11111111,99999999);    
     }
	    public function base_image_upload_add($request,$key)        
        {     
                      
            $media = $request->file($key)->getClientOriginalName();          
            $ext = $request->file($key)->getClientOriginalExtension();         
            $media = self::generate_random_string().'.'.$ext;                 
            $request->file($key)->move('public/image/',$media);                
            return "public/image/" . $media;
           
        }
        public function base_image_upload_update($request,$key)        
        {     
                      
            $media = $request->file($key)->getClientOriginalName();          
            $ext = $request->file($key)->getClientOriginalExtension();         
            $media = self::generate_random_string().'.'.$ext;                 
            $request->file($key)->move('public/image/',$media);                
            return "public/image/" . $media;
                
        }

   
}

?>