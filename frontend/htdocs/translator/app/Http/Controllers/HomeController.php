<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TranslatorLanguage;
use App\Repositories\LanguageRepository;
use DB;

class HomeController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TranslatorLanguage $trans_lang,LanguageRepository $languagerepo)
    {
        $this->middleware('auth');
        $this->trans_lang = $trans_lang;
         $this->languagerepo = $languagerepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $all_languages = $this->trans_lang->get();
        // $active_languages = $this->trans_lang->WHERE('language_status','1')->get();
        // print_r($active_language);exit;
        // return view('home',compact('all_languages','active_languages'));
        $languages = $this->languagerepo->selectLanguage();
                return view('language.home', compact('languages'));
    }
    
}
