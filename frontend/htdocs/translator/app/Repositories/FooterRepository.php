<?php 
namespace App\Repositories;

use App\Repositories\Repository;
use App\TranslatorFooter;
use Illuminate\Http\Request;
use DB;

class FooterRepository extends Repository
{

    /**
     * constructor for this class
     *
     * @param Language $trans_lang
     * @retuen object
     */
    public function __construct(TranslatorFooter $trans_footer){

        $this->trans_footer = $trans_footer;
    }

    /**
     *  create a new language request
     *
     * @param Request $request
     * @retuen object
     */
    public function createFooter($request)
    {
        $input = (array)$request;
        $footer = $this->trans_footer->create($input);
        return $footer;
    }

    /**
     * getting language requests
     *
     * @param Request $request
     * @retuen language to user
     */
    public function selectFooter()
    {
        $footer = $this->trans_footer->get();
        return $footer;
    }

    /**
     *  Edit language request
     *
     * @param Request $request
     * @retuen object
     */
    public function editFooter($request)
    {
        $input = (array)$request;
        // print_r($input);exit;
        $footer = $this->trans_footer->find($input['id'])->update($input);    
        return $footer;
    }

    /**
     *  Delete language request
     *
     * @param Request $request
     * @retuen object
     */
    public function deleteFooter($request)
    {
        $input = (array)$request;
        
        $footer = $this->trans_footer->find($input['id'])->delete();    
        return $footer;
    }

   
}