<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class AccountGroup extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Account Group';

    protected $guarded = ['id'];


    static function getGroupByName($name, $get_only_id = false)
    {
        $business_id = request()->session()->get('user.business_id');
        $group = AccountGroup::where('business_id', $business_id)->where('name', $name)->first();
        if($get_only_id){
            if(!empty($group)){
                $group = $group->id;
            }else{
                $group = 0;
            }
        }
        return $group;
    }
    static function getAccountByGroupId($id, $include_main = false)
    {
        $business_id = request()->session()->get('user.business_id');
        $accounts = Account::where('business_id', $business_id)->where('asset_type', $id);
        if ($include_main) {
            $accounts = $accounts->get();
        } else {
            $accounts = $accounts->where('is_main_account', 0)->get();
        }

        return $accounts;
    }

    static function getAccountGroupByAccountId($account_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $account_group = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)->where('accounts.id', $account_id)->select('account_groups.*')->first();

        return $account_group;
    }
}
