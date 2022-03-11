<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OtherIncome extends Model
{
    protected $fillable = [];

    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Other Sales'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

     /**
    * Get the settlement that belongs to the settlement.
    */
    public function settlements()
    {
        return $this->belongsTo('\Modules\Petro\Entities\Settlement', 'settlement_no', 'id');
    }
}
