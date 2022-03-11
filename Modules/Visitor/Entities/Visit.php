<?php

namespace Modules\Visitor\Entities;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
