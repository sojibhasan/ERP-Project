<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Variation_store_detail extends Model
{
    use LogsActivity;
    

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * Get the Store dropdown.
    *
    * @return string
    */
}
