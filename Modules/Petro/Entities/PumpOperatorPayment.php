<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;

class PumpOperatorPayment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pump_operator_payments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function pump_operator()
    {
        return $this->belongsTo('Modules\Petro\Entities\PumpOperator', 'pump_operators_id');
    }

    public static function getPaymentTypesArray(){
        return [
            'cash' => __('petro::lang.cash'),
            'cheque' => __('petro::lang.cheque'),
            'card' => __('petro::lang.card'),
            'credit' => __('petro::lang.credit'),
            'multiple_credit' => __('petro::lang.multiple_credit'),
            'shortage' => __('petro::lang.shortage'),
            'excess' => __('petro::lang.excess'),
        ];
    }
}
