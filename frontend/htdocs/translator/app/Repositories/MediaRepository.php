<?php 
namespace App\Repositories;

use App\Repositories\Repository;
use App\TranslatorMedia;
use Illuminate\Http\Request;
use DB;

class MediaRepository extends Repository
{

    /**
     * constructor for this class
     *
     * @param Language $trans_lang
     * @retuen object
     */
    public function __construct(TranslatorMedia $trans_media){

        $this->trans_media = $trans_media;
    }

    /**
     *  create a new language request
     *
     * @param Request $request
     * @retuen object
     */
    public function createMedia($request)
    {
         
             
        $input = (array)$request;
        $media = $this->trans_media->create($input);
        return $media;
    }

    /**
     * getting language requests
     *
     * @param Request $request
     * @retuen language to user
     */
    public function selectMedia()
    {
        $media = $this->trans_media->get();
        return $media;
    }

    /**
     *  Edit language request
     *
     * @param Request $request
     * @retuen object
     */
    public function editMedia($request)
    {
        $input = (array)$request;
        
        $media = $this->trans_media->find($input['id'])->update($input);    
        return $media;
    }

    /**
     *  Delete language request
     *
     * @param Request $request
     * @retuen object
     */
    public function deleteMedia($request)
    {
        $input = (array)$request;
        
        $media= $this->trans_media->find($input['id'])->delete();    
        return $media;
    }

   
}