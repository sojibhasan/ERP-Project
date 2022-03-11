<?php

namespace Modules\Member\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;

class Suggestion extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Suggestion';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected static function getStateOfUrgenciesArray()
    {
        return [
            'normal' => 'Normal',
            'medium' => 'Medium',
            'high' => 'High',
        ];
    }
    protected static function getSolutionGivenArray()
    {
        return [
            'solved' => 'Solved',
            'pending' => 'Pending',
            'rejected' => 'Rejected',
        ];
    }

    protected static function getStatusArray()
    {
        return [
            'closed' => 'Closed',
            'waiting_for_reponse' => 'Waiting for response',
            'assigned_to' => 'Assigned to',
        ];
    }

    protected static function checkMemberorNot()
    {
        if (Auth::guard('web')->check()) {
            return false;
        } else {
            return true;
        }
    }
}
