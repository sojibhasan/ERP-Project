<?php

namespace Modules\HR\Entities;

use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\EmployeeResetPasswordToken;

class Employee extends Authenticatable
{
    use LogsActivity;
    use Notifiable;
    use HasRoles;

    protected $guard = 'employee';

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function permitted_locations()
    {
        $user = $this;
        return 'all';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new EmployeeResetPasswordToken($token));
    }

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Employee';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
