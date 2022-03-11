<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\MemberResetPasswordToken;
use Illuminate\Database\Eloquent\Model;

class Member extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $guard = 'member';

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
        $this->notify(new MemberResetPasswordToken($token));
    }

    /**
     * Get all of the contacts's notes & documents.
     */
    public function documentsAndnote()
    {
        return $this->morphMany('App\DocumentAndNote', 'notable');
    }
}
