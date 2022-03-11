<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Variation extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    protected static $logName = 'Variation';

    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'combo_variations' => 'array',
        'default_multiple_unit_price' => 'array',
    ];

    public function product_variation()
    {
        return $this->belongsTo(\App\ProductVariation::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }

    /**
     * Get the sell lines associated with the variation.
     */
    public function sell_lines()
    {
        return $this->hasMany(\App\TransactionSellLine::class);
    }

    /**
     * Get the location wise details of the the variation.
     */
    public function variation_location_details()
    {
        return $this->hasMany(\App\VariationLocationDetails::class);
    }

    /**
     * Get Selling price group prices.
     */
    public function group_prices()
    {
        return $this->hasMany(\App\VariationGroupPrice::class, 'variation_id');
    }

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function getFullNameAttribute()
    {
        $name = $this->product->name;
        if ($this->product->type == 'variable') {
            $name .= ' - ' . $this->product_variation->name . ' - ' . $this->name;
        }
        $name .= ' (' . $this->sub_sku . ')';

        return $name;
    }
    public static function getVariationDropdown($business_id, $category_id = null, $sub_category_id = null, $variation_id = null)
    {
        $q = Product::leftJoin(
            'variations',
            'products.id',
            '=',
            'variations.product_id'
        )
            ->active()
            ->where('business_id', $business_id)
            ->whereNull('variations.deleted_at')
            ->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'variations.id as variation_id',
                'variations.name as variation',
                'variations.sub_sku as sub_sku'
            )->whereIn('products.type', ['variable', 'variable_only_in_sale'])
            ->groupBy('variation_id');

        if (!empty($category_id)) {
            $q->where('products.category_id', $category_id);
        }
        if (!empty($sub_category_id)) {
            $q->where('products.sub_category_id', $sub_category_id);
        }
        if (!empty($variation_id)) {
            $vari = Variation::where('id', $variation_id)->first();
            if (!empty($vari)) {
                $q->where('products.id', $vari->product_id);
            }
            $q->where('variations.id', '!=', $variation_id);
        }

        $products = $q->get();

        $products_array = [];
        foreach ($products as $product) {
            $products_array[$product->product_id]['name'] = $product->name;
            $products_array[$product->product_id]['sku'] = $product->sub_sku;
            $products_array[$product->product_id]['type'] = $product->type;
            $products_array[$product->product_id]['variations'][]
                = [
                    'variation_id' => $product->variation_id,
                    'variation_name' => $product->variation,
                    'sub_sku' => $product->sub_sku
                ];
        }

        $result = [];
        $i = 1;
        $no_of_records = $products->count();
        if (!empty($products_array)) {
            foreach ($products_array as $key => $value) {
                if ($no_of_records > 1 && $value['type'] != 'single') {
                    $result[$key] = $value['name'] . ' - ' . $value['sku'];
                }
                $name = $value['name'];
                foreach ($value['variations'] as $variation) {
                    $text = $name;
                    if ($value['type'] == 'variable' || $value['type'] == 'variable_only_in_sale') {
                        if ($variation['variation_name'] != 'DUMMY') {
                            $text = $text . ' (' . $variation['variation_name'] . ')';
                        }
                    }
                    $i++;
                    $result[$variation['variation_id']] = $text . ' - ' . $variation['sub_sku'];
                }
                $i++;
            }
        }

        return $result;
    }
}
