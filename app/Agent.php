<?php

namespace App;

use App\Notifications\AgentResetPasswordToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Agent extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AgentResetPasswordToken($token));
    }
    public function media()
    {
        return $this->morphOne(\App\Media::class, 'model');
    }
}
