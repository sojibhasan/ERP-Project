<?php

namespace Modules\TasksManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Notes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'members' => 'array',
        'reminder' => 'array'
    ];

    /**
     * The array of status
     *
     * @var array
     */
    protected static function getStatusArray()
    {
        return [
            'new' => 'New',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
        ];
    }

    /**
     * The array of reminders
     *
     * @var array
     */
    protected static function getReminderArray()
    {
        return [
            'one_time' => 'One Time',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }
}
