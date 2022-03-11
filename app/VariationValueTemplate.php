<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class VariationValueTemplate extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Variation Value Template'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    /**
     * Get the variation that owns the attribute.
     */
    public function variationTemplate()
    {
        return $this->belongsTo(\App\VariationTemplate::class);
    }
}
