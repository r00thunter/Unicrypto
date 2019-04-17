<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatorMenu extends Model
{
    protected $table = 'trans_menu';
    protected $primaryKey = 'id';
      protected $fillable = [
           'menu_key', 'menu_en', 'menu_fn','menu_sp','menu_ab','menu_gn','status', 'created_at', 'updated_at'
    ];
}
