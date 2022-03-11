<?php

namespace Modules\Visitor\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Visitor extends Authenticatable
{
    use Notifiable;
    // use HasRoles;

    protected $guard = 'visitor';

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $date = ['date_and_time', 'visited_date', 'created_at', 'updated_at'];

}
