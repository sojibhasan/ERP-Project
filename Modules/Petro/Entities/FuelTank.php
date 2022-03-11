<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FuelTank extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Fuel Tank';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the tank has many transaction 
     */
    public function sell_lines()
    {
        return $this->hasMany('\Modules\Petro\Entities\TankSellLine', 'tank_id');
    }
    /**
     * Get the tank has many transaction 
     */
    public function purchase_lines()
    {
        return $this->hasMany('\Modules\Petro\Entities\TankPurchaseLine', 'tank_id');
    }

    public static function getFuelTankDetailsById($id)
    {
        $fuel_tank = FuelTank::leftjoin('products', 'fuel_tanks.product_id', 'products.id')
            ->leftjoin('business_locations', 'fuel_tanks.location_id', 'business_locations.id')
            ->where('fuel_tanks.id', $id)
            ->select('products.name as product_name', 'fuel_tanks.*')->first();
        return $fuel_tank;
    }
}
