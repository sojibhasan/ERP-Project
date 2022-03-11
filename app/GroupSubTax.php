<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class GroupSubTax extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Group Sub Tax'; 

    public function tax_rate()
    {
        return $this->belongsTo(\App\TaxRate::class, 'group_tax_id');
    }
}
