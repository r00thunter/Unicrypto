<?php 
namespace App\Repositories;

use App\Repositories\Repository;
use App\TranslatorMenu;
use Illuminate\Http\Request;
use DB;

class MenuRepository extends Repository
{

    /**
     * constructor for this class
     *
     * @param Language $trans_lang
     * @retuen object
     */
    public function __construct(TranslatorMenu $trans_menu){

        $this->trans_menu = $trans_menu;
    }

    /**
     *  create a new language request
     *
     * @param Request $request
     * @retuen object
     */
    public function createMenu($request)
    {
        $input = (array)$request;
        $menu = $this->trans_menu->create($input);
        return $menu;
    }

    /**
     * getting language requests
     *
     * @param Request $request
     * @retuen language to user
     */
    public function selectMenu()
    {
        $menu = $this->trans_menu->get();
        return $menu;
    }

    /**
     *  Edit language request
     *
     * @param Request $request
     * @retuen object
     */
    public function editMenu($request)
    {
        $input = (array)$request;
        
        $menu = $this->trans_menu->find($input['id'])->update($input);    
        return $menu;
    }

    /**
     *  Delete language request
     *
     * @param Request $request
     * @retuen object
     */
    public function deleteMenu($request)
    {
        $input = (array)$request;
        
        $menu = $this->trans_menu->find($input['id'])->delete();    
        return $menu;
    }

   
}