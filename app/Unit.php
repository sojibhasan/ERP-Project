<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Unit extends Model
{
    use LogsActivity;
    
    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Unit'; 

    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of units for a business
     *
     * @param int $business_id
     * @param boolean $show_none = true
     *
     * @return array
     */
    public static function forDropdown($business_id, $show_none = false, $only_base = true, $show_only = null)
    {
        $query = Unit::where('business_id', $business_id)->where('is_property', 0);
        if ($only_base) {
            $query->whereNull('base_unit_id');
        }
        if(!empty($show_only)){
            $query->where(function($q) use ($show_only){
                $q->whereNull('base_unit_id');
                if(!empty($show_only)){
                    $q->orWhere($show_only, 1);
                }
            });
        }
        $units = $query->select(DB::raw('CONCAT(actual_name, " (", short_name, ")") as name'), 'id')->get();
        $dropdown = $units->pluck('name', 'id');
        if ($show_none) {
            $dropdown->prepend(__('messages.please_select'), '');
        }
        
        return $dropdown;
    }

    /**
     * Return list of units for a business
     *
     * @param int $business_id
     * @param boolean $show_none = true
     *
     * @return array
     */
    public static function getPropertyUnitDropdown($business_id, $show_none = false, $only_base = true, $show_only = null)
    {
        $query = Unit::where('business_id', $business_id)->where('is_property', 1);
        if ($only_base) {
            $query->whereNull('base_unit_id');
        }

        if(!empty($show_only)){
            $query->where(function($q) use ($show_only){
                $q->whereNull('base_unit_id');
                if(!empty($show_only)){
                    $q->orWhere($show_only, 1);
                }
            });
        }
        $units = $query->select(DB::raw('CONCAT(actual_name, " (", short_name, ")") as name'), 'id')->get();
        $dropdown = $units->pluck('name', 'id');
        if ($show_none) {
            $dropdown->prepend(__('messages.please_select'), '');
        }
        
        return $dropdown;
    }

    public function sub_units()
    {
        return $this->hasMany(\App\Unit::class, 'base_unit_id');
    }

    public function base_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'base_unit_id');
    }
}
