<?php

namespace Modules\Property\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Property extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];

    public static function statusesDropdown(){
        return ['open' => __('property::lang.open'), 'close' => __('property::lang.close')];
    }

    public function scopeNotSold($query){
        $query->where('property_blocks.is_sold', 0);
    }

    public function scopeOnlySold($query){
        $query->where('property_blocks.is_sold', 1);
    }

    public function blocks(){
        return $this->hasMany(\Modules\Property\Entities\PropertyBlock::class, 'property_id');
    }
    
    public static function getLandAndBlockDropdown($business_id, $sold_only = false, $append_block = true){
        $query = Property::leftjoin('property_blocks', 'properties.id', 'property_blocks.property_id')->where('properties.business_id', $business_id);
        if ($sold_only) {
            $query->where('property_blocks.is_sold', 1);
        }

        if ($append_block) {
            $query->select(
                DB::raw("CONCAT(name, ' - ', COALESCE(block_number, '')) AS property_name"),
                'property_blocks.id'
                    );
        } else {
            $query->select(
                'property_blocks.id',
                DB::raw("name as property_name")
            );
        }
        
        $dp = $query->groupBy('property_blocks.id')->pluck('property_name', 'id');


        return $dp;
    }
    public static function getLandAndBlockByCustomerDropdown($customer_id, $sold_only = false, $append_block = true){
        $query = Property::leftjoin('property_blocks', 'properties.id', 'property_blocks.property_id')->where('property_blocks.customer_id', $customer_id);
        if ($sold_only) {
            $query->where('property_blocks.is_sold', 1);
        }

        if ($append_block) {
            $query->select(
                DB::raw("CONCAT(name, ' - ', COALESCE(block_number, '')) AS property_name"),
                'property_blocks.id'
                    );
        } else {
            $query->select(
                'property_blocks.id',
                DB::raw("name as property_name")
            );
        }
        
        $dp = $query->groupBy('property_blocks.id')->pluck('property_name', 'id');


        return $dp;
    }
}
