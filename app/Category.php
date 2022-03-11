<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Category';

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
     * Combines Category and sub-category
     *
     * @param int $business_id
     * @return array
     */
    public static function catAndSubCategories($business_id, $enable_petro_module = 0)
    {
        $categories_query = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->orderBy('name', 'asc');
        if (!empty($enable_petro_module)) {
            $categories = $categories_query->get()->toArray();
        } else {
            $categories =  $categories_query->notPetro()->get()->toArray();
        }

        if (empty($categories)) {
            return [];
        }

        $sub_categories = Category::where('business_id', $business_id)
            ->where('parent_id', '!=', 0)
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
        $sub_cat_by_parent = [];

        if (!empty($sub_categories)) {
            foreach ($sub_categories as $sub_category) {
                if (empty($sub_cat_by_parent[$sub_category['parent_id']])) {
                    $sub_cat_by_parent[$sub_category['parent_id']] = [];
                }

                $sub_cat_by_parent[$sub_category['parent_id']][] = $sub_category;
            }
        }

        foreach ($categories as $key => $value) {
            if (!empty($sub_cat_by_parent[$value['id']])) {
                $categories[$key]['sub_categories'] = $sub_cat_by_parent[$value['id']];
            }
        }

        return $categories;
    }

    public static function forDropdown($business_id, $enable_petro_module = 0)
    {
        $categories_query = Category::where('business_id', $business_id)
            ->where('parent_id', 0);

        $categories  =  $categories_query->select(DB::raw('IF(short_code IS NOT NULL, CONCAT(name, "-", short_code), name) as name'), 'id');

        if (!empty($enable_petro_module)) {
            $categories = $categories_query->get();
        } else {
            $categories =  $categories_query->notPetro()->get();
        }

        $dropdown =  $categories->pluck('name', 'id');

        return $dropdown;
    }
    public static function subCategoryforDropdown($business_id, $enable_petro_module = 0)
    {
        $categories_query = Category::where('business_id', $business_id)
            ->where('parent_id', '!=', 0)
            ->select(DB::raw('IF(short_code IS NOT NULL, CONCAT(name, "-", short_code), name) as name'), 'id');

        if (!empty($enable_petro_module)) {
            $categories = $categories_query->get();
        } else {
            $categories =  $categories_query->notPetro()->get();
        }
        $dropdown =  $categories->pluck('name', 'id');

        return $dropdown;
    }
    public static function subCategory($business_id, $enable_petro_module = 0)
    {
        $categories_query = Category::leftjoin('categories as parent_cat', 'categories.parent_id', 'parent_cat.id')->where('categories.business_id', $business_id)
            ->where('categories.parent_id', '!=', 0)
            ->select('categories.*');

        if (!empty($enable_petro_module)) {
            $categories = $categories_query->get();
        } else {
            $categories =  $categories_query->where('parent_cat.name', '!=', 'Fuel')->get();
        }

        return $categories;
    }

    public static function subCategoryOnlyFuel($business_id)
    {
        $categories_query = Category::leftjoin('categories as parent_cat', 'categories.parent_id', 'parent_cat.id')->where('categories.business_id', $business_id)
            ->where('categories.parent_id', '!=', 0)
            ->select('categories.*');

        $categories =  $categories_query->where('parent_cat.name', 'Fuel')->get();


        return $categories;
    }

    /**
     * Scope a query to fuel category if petro enable or not
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotPetro($query)
    {
        return $query->where('categories.name', '!=', 'Fuel');
    }
}
