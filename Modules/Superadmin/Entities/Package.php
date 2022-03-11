<?php

namespace Modules\Superadmin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'custom_permissions' => 'array',
        'current_values' => 'array',
    ];
    
    /**
     * Scope a query to only include active packages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope a query to only include active packages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', 1);
    }

    /**
     * Returns the list of active pakages
     *
     * @return object
     */
    public static function listPackages($exlude_private = false)
    {
        $packages = Package::active()->visible()
                        ->orderby('sort_order');

        if ($exlude_private) {
            $packages->notPrivate();
        }

        return $packages->get();
    }

    /**
     * Scope a query to exclude private packages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotPrivate($query)
    {
        return $query->where('is_private', 0);
    }

    public static function getPackagePeriodInDays($package){

        if($package->interval == 'years'){
            return $package->interval_count * 365;
        }
        if($package->interval == 'months'){
            return $package->interval_count * 30;
        }
        if($package->interval == 'days'){
            return $package->interval_count * 1;
        }

    }
}
