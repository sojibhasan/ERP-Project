<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;

class PumpOperatorAssignment extends Model
{
     /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
