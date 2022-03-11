<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CrmGroup extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'CRM Groups'; 

    protected $fillable = ['name', 'business_id', 'created_by'];

    
    public static function forDropdown($business_id, $prepend_none = true, $prepend_all = false)
    {
        $all_cg = CrmGroup::where('business_id', $business_id);
        $all_cg = $all_cg->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $all_cg = $all_cg->prepend(__("lang_v1.none"), '');
        }

        //Prepend none
        if ($prepend_all) {
            $all_cg = $all_cg->prepend(__("report.all"), '');
        }
        
        return $all_cg;
    }

}
