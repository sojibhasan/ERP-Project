<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;

class UnloadStock extends Model
{
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
