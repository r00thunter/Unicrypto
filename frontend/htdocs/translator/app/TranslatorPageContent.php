<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class TranslatorPageContent extends Model
{
    protected $table = 'trans_page_value';
    protected $primaryKey = 'id';
    protected $fillable = [
           'page_content_key', 'en_page_content','fn_page_content','sp_page_content','ab_page_content','gn_page_content','page_content_status', 'page_content','created_at', 'updated_at'
    ];

    // public static function getName($key){
    // 	$data = DB::table('trans_page_value')->where('page_content_key',$key)->first();
    // 	return $data;
    // 	//->en_page_heading;
    // }
}
     			