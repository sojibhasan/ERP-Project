<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class InvoiceScheme extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Invoice Scheme'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Returns list of invoice schemes in array format
     */
    public static function forDropdown($business_id)
    {
        $dropdown = InvoiceScheme::where('business_id', $business_id)
                                ->pluck('name', 'id');

        return $dropdown;
    }

    /**
     * Retrieves the default invoice scheme
     */
    public static function getDefault($business_id)
    {
        $default = InvoiceScheme::where('business_id', $business_id)
                                ->where('is_default', 1)
                                ->first();
        return $default;
    }
}
