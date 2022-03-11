<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class TankPurchaseLine extends Model
{
    protected $fillable = [];

    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Tank Purchase Line';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * Get the sell line that belongs to the tank.
    */
    public function fuel_tanks()
    {
        return $this->belongsTo('\Modules\Petro\Entities\FuelTank');
    }
}
