<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatorPage extends Model
{
    protected $table = 'trans_page';
    protected $primaryKey = 'id';
      protected $fillable = [
           'page_name', 'page_status', 'created_at', 'updated_at'
    ];
}
