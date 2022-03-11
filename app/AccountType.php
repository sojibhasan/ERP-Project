<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AccountType extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Account Type'; 
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function sub_types()
    {
        return $this->hasMany(\App\AccountType::class, 'parent_account_type_id');
    }

    public function parent_account()
    {
        return $this->belongsTo(\App\AccountType::class, 'parent_account_type_id');
    }

    public static function getAccountTypeIdOfType($type, $business_id){
        $account_type = AccountType::where('name', 'like', '%' .$type. '%')->where('business_id', $business_id)->select('id')->pluck('id')->toArray();

        return $account_type;
    }

    public static function getAccountTypeIdByName($type, $business_id, $get_only_id = false){
        $account_type = AccountType::where('name', 'like', '%' .$type. '%')->where('business_id', $business_id)->first();

        if($get_only_id){
            return !empty($account_type) ? $account_type->id : null;

        }else{
            return $account_type;
        }
    }

    public static function forDropdown($business_id, $prepend_none = false, $prepend_please_select = false)
    {
        $query = AccountType::where('business_id', $business_id);
        
        $dropdown = $query->pluck('name', 'id');
        if ($prepend_none) {
            $dropdown->prepend(__('lang_v1.none'), '');
        }
        if ($prepend_please_select) {
            $dropdown->prepend(__('lang_v1.please_select'), '');
        }

        return $dropdown;
    }


}
