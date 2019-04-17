<?php 
namespace App\Repositories;

use App\Repositories\Repository;
use App\TranslatorPage;
use Illuminate\Http\Request;
use DB;

class PageRepository extends Repository
{

    /**
     * constructor for this class
     *
     * @param Page $trans_page
     * @retuen object
     */
    public function __construct(TranslatorPage $trans_page){

        $this->trans_page = $trans_page;
    }

    /**
     *  create a new page request
     *
     * @param Request $request
     * @retuen object
     */
    public function createPage($request)
    {
        $input = (array)$request;
        $page = $this->trans_page->create($input);
        return $page;
    }

    /**
     * getting page requests
     *
     * @param Request $request
     * @retuen page to user
     */
    public function selectPage()
    {
        $page = $this->trans_page->get();
        return $page;
    }

    /**
     *  Edit page request
     *
     * @param Request $request
     * @retuen object
     */
    public function editPage($request)
    {
        $input = (array)$request;
        
        $page = $this->trans_page->find($input['id'])->update($input);    
        return $page;
    }

    /**
     *  Delete page request
     *
     * @param Request $request
     * @retuen object
     */
    public function deletePage($request)
    {
        $input = (array)$request;
        
        $page = $this->trans_page->find($input['id'])->delete();    
        return $page;
    }

   
}