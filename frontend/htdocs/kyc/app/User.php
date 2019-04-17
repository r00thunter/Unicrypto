<?php

namespace App;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'avatar', 'phone', 'country', 'city', 'address', 'postal_code', 'twitter_profile', 'linkedin_profile', 'referral_code', 'referral_count', 'approval', 'completion',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    const PROFILE_RULES = [
        'first_name'  => 'required|string|max:255',
        'last_name'   => 'required|string|max:255',
        'phone'       => 'required',
        'email'       => 'required|string|email|max:255',
        'country'     => 'required',
        'city'        => 'required',
        'address'     => 'required',
        'postal_code' => 'required',
    ];

    const KYC_RULES = [
        'id_proof_type'      => 'required',
        'id_proof'           => 'required|image',
        'address_proof_type' => 'required',
        'address_proof'      => 'required|image',
        'id_card'            => 'required|image',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * user preferences.
     */
    public function preference()
    {
        return $this->hasOne('App\UserPreference');
    }
}
