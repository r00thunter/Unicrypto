<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatorMedia extends Model
{
    protected $table = 'trans_media';
    protected $primaryKey = 'id';
      protected $fillable = [
            'media_name','media_image','media_link','created_at', 'updated_at'
               
        ];
}
