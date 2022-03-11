<?php

namespace Modules\Fleet\Entities;

use Illuminate\Database\Eloquent\Model;

class RouteOperation extends Model
{
    protected $fillable = [];

    protected $guarded  = ['id'];

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }
}
