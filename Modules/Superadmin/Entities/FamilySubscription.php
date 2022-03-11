<?php

namespace Modules\Superadmin\Entities;

use Illuminate\Database\Eloquent\Model;

class FamilySubscription extends Model
{
    protected $fillable = [];
      /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

     /**
     * Returns the list of packages status
     *
     * @return array
     */
    public static function package_subscription_status()
    {
        return ['approved' => trans("superadmin::lang.approved"), 'declined' => trans("superadmin::lang.declined"), 'waiting' => trans("superadmin::lang.waiting")];
    }
}
