<?php

namespace Modules\Superadmin\Entities;

use Illuminate\Database\Eloquent\Model;

class PayOnline extends Model
{
    protected $fillable = [];
    protected $guarded = ['id'];

      /**
     * Returns the list of packages status
     *
     * @return array
     */
    public static function payment_status()
    {
        return ['approved' => trans("superadmin::lang.approved"), 'declined' => trans("superadmin::lang.declined"), 'pending' => trans("superadmin::lang.pending")];
    }
}
