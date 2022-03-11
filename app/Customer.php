<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\CustomerResetPasswordToken;

class Customer extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $guard = 'customer';

    protected $fillable = [
        'business_id', 'first_name', 'last_name', 'email', 'password', 'nic_number', 'username', 'mobile', 'contact_number', 'landline', 'geo_location', 'address', 'town', 'district', 'is_company_customer'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = ['give_away_gifts' => 'array'];

    public function permitted_locations()
    {
        $user = $this;
        return 'all';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPasswordToken($token));
    }
    public function media()
    {
        return $this->morphOne(\App\Media::class, 'model');
    }
}
