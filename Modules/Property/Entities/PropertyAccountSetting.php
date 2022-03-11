<?php

namespace Modules\Property\Entities;

use Illuminate\Database\Eloquent\Model;

class PropertyAccountSetting extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];

    public function property(){
        $this->belongsTo(\Modules\Property\Entities\Property::class);
    }
}
