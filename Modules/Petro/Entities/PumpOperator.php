<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PumpOperator extends Model
{
    protected $fillable = [];

    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Pump Operators';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * Get all of the contacts's notes & documents.
     */
    public function documentsAndnote()
    {
        return $this->morphMany('App\DocumentAndNote', 'notable');
    }

    /**
     * Get the business that owns the user.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }
}
