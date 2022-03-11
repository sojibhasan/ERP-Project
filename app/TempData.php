<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempData extends Model
{
    protected $table = 'temp_data';

     /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];
}
