<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Store extends Model
{
    use LogsActivity;
    
    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Store'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * Get the Store dropdown.
    *
    * @return string
    */
     public static function forDropdown($business_id, $enable_petro_module = 0)
    {
        $stores = Store::where('business_id', $business_id)->get();

        $dropdown =  $stores->pluck('name', 'id');

        return $dropdown;
    }

    public function business_locations()
    {
        return $this->belongsTo(\App\BusinessLocation::class);
    }
    
    public function variation_qty()
    {
        return $this->belongsToMany(\App\Variation::class, 'variation_store_details', 'store_id', 'product_variation_id');
    }
}
