<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StockTransferRequest extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Stock Adjustment Requests';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

      /**
     * Return list of categories associated with the group_tax
     *
     * @return object
     */
    public function products()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }
}
