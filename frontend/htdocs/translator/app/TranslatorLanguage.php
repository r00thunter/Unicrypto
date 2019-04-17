<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatorLanguage extends Model
{
    protected $table = 'trans_lang';
    protected $primaryKey = 'id';
      protected $fillable = [
           'language_name', 'language_symbol', 'language_status', 'created_at', 'updated_at'
    ];
}
