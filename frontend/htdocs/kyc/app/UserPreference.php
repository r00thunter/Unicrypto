<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'E2F', 'ESMS', 'id_proof_type', 'id_proof', 'address_proof_type', 'address_proof', 'id_card', 'contact_sms', 'contact_email',
    ];
}
