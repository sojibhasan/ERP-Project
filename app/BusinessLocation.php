<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BusinessLocation extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Business Location'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of locations for a business
     *
     * @param int $business_id
     * @param boolean $show_all = false
     * @param array $receipt_printer_type_attribute =
     *
     * @return array
     */
    public static function forDropdown($business_id, $show_all = false, $receipt_printer_type_attribute = false, $append_id = true)
    {
        $query = BusinessLocation::where('business_id', $business_id)->Active();

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            if(!auth()->user()->is_customer){
                $query->whereIn('id', $permitted_locations);
            }
        }

        if ($append_id) {
            $query->select(
                DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"),
                'id',
                'receipt_printer_type',
                'selling_price_group_id',
                'default_payment_accounts'
            );
        }

        $result = $query->get();

        $locations = $result->pluck('name', 'id');

        if ($show_all) {
            $locations->prepend(__('report.all_locations'), '');
        }

        if ($receipt_printer_type_attribute) {
            $attributes = collect($result)->mapWithKeys(function ($item) {
                return [$item->id => [
                            'data-receipt_printer_type' => $item->receipt_printer_type,
                            'data-default_price_group' => $item->selling_price_group_id,
                            'data-default_payment_accounts' => $item->default_payment_accounts
                        ]
                    ];
            })->all();

            return ['locations' => $locations, 'attributes' => $attributes];
        } else {
            return $locations;
        }
    }

    public function price_group()
    {
        return $this->belongsTo(\App\SellingPriceGroup::class, 'selling_price_group_id');
    }

    /**
     * Scope a query to only include active location.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }


    
    public function stores()
    {
        return $this->hasMany(\App\Store::class, 'location_id');
    }


    public static function getDefaultAccountIdForMethod($method_name, $location_id)
    {
        $business_id = request()->session()->get('business.id');
        $account_id = null;
        $defualt_accounts = BusinessLocation::where('business_id', $business_id)->where('id',  $location_id)->first();
        if (!empty($defualt_accounts)) {
            $default_payment_accounts = (array) json_decode($defualt_accounts->default_payment_accounts);

            $account_id = $default_payment_accounts[$method_name]->account;
        }

        return $account_id;
    }



}
