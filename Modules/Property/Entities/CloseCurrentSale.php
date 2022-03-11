<?php

namespace Modules\Property\Entities;

use Illuminate\Database\Eloquent\Model;

class CloseCurrentSale extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'reason_id' => 'array'
    ];

    public function property_finalize()
    {
        return $this->belongsTo(PropertyFinalize::class);
    }
}
