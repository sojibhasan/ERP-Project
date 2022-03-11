<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseLine extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Purchase Line'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }

    public function variations()
    {
        return $this->belongsTo(\App\Variation::class, 'variation_id');
    }

    /**
     * Set the quantity.
     *
     * @param  string  $value
     * @return float $value
     */
    public function getQuantityAttribute($value)
    {
        return (float)$value;
    }

    /**
     * Get the unit associated with the purchase line.
     */
    public function sub_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'sub_unit_id');
    }

    /**
     * Give the quantity remaining for a particular
     * purchase line.
     *
     * @return float $value
     */
    public function getQuantityRemainingAttribute()
    {
        return (float)($this->quantity - $this->quantity_used);
    }

    /**
     * Give the sum of quantity sold, adjusted, returned.
     *
     * @return float $value
     */
    public function getQuantityUsedAttribute()
    {
        return (float)($this->quantity_sold + $this->quantity_adjusted + $this->quantity_returned + $this->mfg_quantity_used);
    }
}
