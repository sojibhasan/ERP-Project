<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MeterSale extends Model
{
    protected $fillable = [];

    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Pumps'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * Get the settlement that belongs to the subscription.
    */
    public function settlements()
    {
        return $this->belongsTo('\Modules\Petro\Entities\Settlement', 'settlement_no', 'id');
    }

    /**
    * Get the products that belongs to the subscription.
    */
    public function products()
    {
        return $this->belongsTo('App\Product');
    }

}
