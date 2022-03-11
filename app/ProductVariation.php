<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductVariation extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Product Variation'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    public function variations()
    {
        return $this->hasMany(\App\Variation::class);
    }

    public function variation_template()
    {
        return $this->belongsTo(\App\VariationTemplate::class);
    }
}
