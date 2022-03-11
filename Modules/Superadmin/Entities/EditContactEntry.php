<?php

namespace Modules\Superadmin\Entities;

use Illuminate\Database\Eloquent\Model;

class EditContactEntry extends Model
{
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
