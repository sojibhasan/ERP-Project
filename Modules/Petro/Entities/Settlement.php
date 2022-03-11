<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Settlement extends Model
{
    protected $fillable = [];

    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Pumps';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'work_shift' => 'array'
    ];

    /**
     * Get the meter_sales that belongs to the settlement.
     */
    public function meter_sales()
    {
        return $this->hasMany('\Modules\Petro\Entities\MeterSale', 'settlement_no', 'id');
    }
    /**
     * Get the meter_sales that belongs to the settlement.
     */
    public function other_sales()
    {
        return $this->hasMany('\Modules\Petro\Entities\OtherSale', 'settlement_no', 'id');
    }
    /**
     * Get the other_income that belongs to the settlement.
     */
    public function other_incomes()
    {
        return $this->hasMany('\Modules\Petro\Entities\OtherIncome', 'settlement_no', 'id');
    }
    /**
     * Get the customer_payments that belongs to the settlement.
     */
    public function customer_payments()
    {
        return $this->hasMany('\Modules\Petro\Entities\CustomerPayment', 'settlement_no', 'id');
    }
    /**
     * Get the card payments that belongs to the settlement.
     */
    public function card_payments()
    {
        return $this->hasMany('\Modules\Petro\Entities\SettlementCardPayment', 'settlement_no', 'id');
    }
    /**
     * Get the cash payments that belongs to the settlement.
     */
    public function cash_payments()
    {
        return $this->hasMany('\Modules\Petro\Entities\SettlementCashPayment', 'settlement_no', 'id');
    }
    /**
     * Get the cheques payments that belongs to the settlement.
     */
    public function cheque_payments()
    {
        return $this->hasMany('\Modules\Petro\Entities\SettlementChequePayment', 'settlement_no', 'id');
    }
    /**
     * Get the credit sale payments that belongs to the settlement.
     */
    public function credit_sale_payments()
    {
        return $this->hasMany('\Modules\Petro\Entities\SettlementCreditSalePayment', 'settlement_no', 'id');
    }
    /**
     * Get the excess payments that belongs to the settlement.
     */
    public function excess_payments()
    {
        return $this->hasMany('\Modules\Petro\Entities\SettlementExcessPayment', 'settlement_no', 'id');
    }
    /**
     * Get the expense payments that belongs to the settlement.
     */
    public function expense_payments()
    {
        return $this->hasMany('\Modules\Petro\Entities\SettlementExpensePayment', 'settlement_no', 'id');
    }
    /**
     * Get the shortage payments that belongs to the settlement.
     */
    public function shortage_payments()
    {
        return $this->hasMany('\Modules\Petro\Entities\SettlementShortagePayment', 'settlement_no', 'id');
    }
}
