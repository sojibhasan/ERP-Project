<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Transaction';
    protected $dates = ['transaction_date'];


    //Transaction types = ['purchase','sell','expense','stock_adjustment','sell_transfer','purchase_transfer','opening_stock','sell_return','opening_balance','purchase_return', 'payroll']

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function purchase_lines()
    {
        return $this->hasMany(\App\PurchaseLine::class);
    }

    public function sell_lines()
    {
        return $this->hasMany(\App\TransactionSellLine::class);
    }

    public function route_operation()
    {
        return $this->hasOne(\Modules\Fleet\Entities\RouteOperation::class);
    }

    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }

    public function payment_lines()
    {
        return $this->hasMany(\App\TransactionPayment::class);
    }

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    public function tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax_id');
    }

    public function stock_adjustment_lines()
    {
        return $this->hasMany(\App\StockAdjustmentLine::class);
    }

    public function sales_person()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function return_parent()
    {
        return $this->hasOne(\App\Transaction::class, 'return_parent_id');
    }

    public function table()
    {
        return $this->belongsTo(\App\Restaurant\ResTable::class, 'res_table_id');
    }

    public function service_staff()
    {
        return $this->belongsTo(\App\User::class, 'res_waiter_id');
    }

    public function recurring_invoices()
    {
        return $this->hasMany(\App\Transaction::class, 'recur_parent_id');
    }

    public function recurring_parent()
    {
        return $this->hasOne(\App\Transaction::class, 'id', 'recur_parent_id');
    }

    public function price_group()
    {
        return $this->belongsTo(\App\SellingPriceGroup::class, 'selling_price_group_id');
    }

    public function types_of_service()
    {
        return $this->belongsTo(\App\TypesOfService::class, 'types_of_service_id');
    }
       public function transection_product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id','id');
    }

    /**
     * Retrieves documents path if exists
     */
    public function getDocumentPathAttribute()
    {
        $path = !empty($this->document) ? asset('/uploads/documents/' . $this->document) : null;

        return $path;
    }

    /**
     * Removes timestamp from document name
     */
    public function getDocumentNameAttribute()
    {
        $document_name = !empty(explode("_", $this->document, 2)[1]) ? explode("_", $this->document, 2)[1] : $this->document;
        return $document_name;
    }

    public function subscription_invoices()
    {
        return $this->hasMany(\App\Transaction::class, 'recur_parent_id');
    }


    public static function categorySaleQuentityByDate($business_id, $location_id, $category_id, $enable_petro_module = 0, $date)
    {
        $sell_details = Product::leftjoin('transaction_sell_lines', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->where('products.category_id', $category_id)
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->whereDay('transaction_date', '=', $date->format('d'))
            ->whereMonth('transaction_date', '=', $date->format('m'))
            ->whereYear('transaction_date', '=', $date->format('Y'))
            ->select(
                DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as qty')
            )
            ->groupBy('products.category_id');

        if (!empty($location_id)) {
            $sell_details->where('transactions.location_id', $location_id);
        }

        return ($sell_details->first()) ? $sell_details->first()->qty : 0;
    }


    public static function subCategorySaleQuentityByDate($business_id, $location_id, $category_id, $subcategory_id, $enable_petro_module = 0, $date)
    {
        $sell_details = Product::leftjoin('transaction_sell_lines', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('transactions', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->where('products.sub_category_id', $subcategory_id)
            ->orWhere('products.category_id', $category_id)
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->whereDay('transaction_date', '=', $date->format('d'))
            ->whereMonth('transaction_date', '=', $date->format('m'))
            ->whereYear('transaction_date', '=', $date->format('Y'))
            ->select(
                DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as qty')
            )

            ->groupBy('products.sub_category_id');

        if (!empty($location_id)) {

            $sell_details->where('transactions.location_id', $location_id);
        }

        return ($sell_details->first()) ? $sell_details->first()->qty : 0;
    }


    /**
     * Shipping address custom method
     */
    public function shipping_address($array = false)
    {
        $addresses = !empty($this->order_addresses) ? json_decode($this->order_addresses, true) : [];

        $shipping_address = [];

        if (!empty($addresses['shipping_address'])) {
            if (!empty($addresses['shipping_address']['shipping_name'])) {
                $shipping_address['name'] = $addresses['shipping_address']['shipping_name'];
            }
            if (!empty($addresses['shipping_address']['company'])) {
                $shipping_address['company'] = $addresses['shipping_address']['company'];
            }
            if (!empty($addresses['shipping_address']['shipping_address_line_1'])) {
                $shipping_address['address_line_1'] = $addresses['shipping_address']['shipping_address_line_1'];
            }
            if (!empty($addresses['shipping_address']['shipping_address_line_2'])) {
                $shipping_address['address_line_2'] = $addresses['shipping_address']['shipping_address_line_2'];
            }
            if (!empty($addresses['shipping_address']['shipping_city'])) {
                $shipping_address['city'] = $addresses['shipping_address']['shipping_city'];
            }
            if (!empty($addresses['shipping_address']['shipping_state'])) {
                $shipping_address['state'] = $addresses['shipping_address']['shipping_state'];
            }
            if (!empty($addresses['shipping_address']['shipping_country'])) {
                $shipping_address['country'] = $addresses['shipping_address']['shipping_country'];
            }
            if (!empty($addresses['shipping_address']['shipping_zip_code'])) {
                $shipping_address['zipcode'] = $addresses['shipping_address']['shipping_zip_code'];
            }
        }

        if ($array) {
            return $shipping_address;
        } else {
            return implode(', ', $shipping_address);
        }
    }

    /**
     * billing address custom method
     */
    public function billing_address($array = false)
    {
        $addresses = !empty($this->order_addresses) ? json_decode($this->order_addresses, true) : [];

        $billing_address = [];

        if (!empty($addresses['billing_address'])) {
            if (!empty($addresses['billing_address']['billing_name'])) {
                $billing_address['name'] = $addresses['billing_address']['billing_name'];
            }
            if (!empty($addresses['billing_address']['company'])) {
                $billing_address['company'] = $addresses['billing_address']['company'];
            }
            if (!empty($addresses['billing_address']['billing_address_line_1'])) {
                $billing_address['address_line_1'] = $addresses['billing_address']['billing_address_line_1'];
            }
            if (!empty($addresses['billing_address']['billing_address_line_2'])) {
                $billing_address['address_line_2'] = $addresses['billing_address']['billing_address_line_2'];
            }
            if (!empty($addresses['billing_address']['billing_city'])) {
                $billing_address['city'] = $addresses['billing_address']['billing_city'];
            }
            if (!empty($addresses['billing_address']['billing_state'])) {
                $billing_address['state'] = $addresses['billing_address']['billing_state'];
            }
            if (!empty($addresses['billing_address']['billing_country'])) {
                $billing_address['country'] = $addresses['billing_address']['billing_country'];
            }
            if (!empty($addresses['billing_address']['billing_zip_code'])) {
                $billing_address['zipcode'] = $addresses['billing_address']['billing_zip_code'];
            }
        }

        if ($array) {
            return $billing_address;
        } else {
            return implode(', ', $billing_address);
        }
    }

    public function cash_register_payments()
    {
        return $this->hasMany(\App\CashRegisterTransaction::class);
    }

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function transaction_for()
    {
        return $this->belongsTo(\App\User::class, 'expense_for');
    }
    
     public function transaction_store()
    {
        return $this->belongsTo(\App\Store::class , 'store_id','id');
    }

    public static function transactionTypes()
    {
        return  [
            'sell' => __('sale.sale'),
            'purchase' => __('lang_v1.purchase'),
            'sell_return' => __('lang_v1.sell_return'),
            'purchase_return' =>  __('lang_v1.purchase_return'),
            'opening_balance' => __('lang_v1.opening_balance'),
            'payment' => __('lang_v1.payment'),
            // 'settlement' => __('lang_v1.settlement')
        ];
    }

    public static function getPaymentStatus($transaction)
    {
        $payment_status = $transaction->payment_status;

        if (in_array($payment_status, ['partial', 'due']) && !empty($transaction->pay_term_number) && !empty($transaction->pay_term_type)) {
            $transaction_date = \Carbon::parse($transaction->transaction_date);
            $due_date = $transaction->pay_term_type == 'days' ? $transaction_date->addDays($transaction->pay_term_number) : $transaction_date->addMonths($transaction->pay_term_number);
            $now = \Carbon::now();
            if ($now->gt($due_date)) {
                $payment_status = $payment_status == 'due' ? 'overdue' : 'partial-overdue';
            }
        }

        if($transaction->price_later){
            $payment_status = 'price-later';
        }

        return $payment_status;
    }

    /**
     * Due date custom attribute
     */
    public function getDueDateAttribute()
    {
        $due_date = null;
        if (!empty($this->pay_term_type) && !empty($this->pay_term_number)) {
            $transaction_date = \Carbon::parse($this->transaction_date);
            $due_date = $this->pay_term_type == 'days' ? $transaction_date->addDays($this->pay_term_number) : $transaction_date->addMonths($this->pay_term_number);
        }

        return $due_date;
    }
    /**
     * invoice number dropdown
     */
    public static function invoiveNumberDropDown($type = 'sell', $prepend_none = false, $prepend_all = false, $prepend_please_select = false)
    {
        $business_id = request()->session()->get('user.business_id');
        $invoie_numbers = Transaction::where('transactions.business_id', $business_id)->where('type', $type)->pluck('invoice_no', 'invoice_no');

        //Prepend none
        if ($prepend_none) {
            $invoie_numbers = $invoie_numbers->prepend(__('lang_v1.none'), '');
        }
        if ($prepend_all) {
            $invoie_numbers = $invoie_numbers->prepend(__('lang_v1.all'), '');
        }
        if ($prepend_please_select) {
            $invoie_numbers = $invoie_numbers->prepend(__('lang_v1.please_select'), '');
        }

        return $invoie_numbers;
    }
    /**
     * Payment Ref number dropdown
     */
    public static function paymentRefNumberDropDown($type = 'sell', $prepend_none = false, $prepend_all = false, $prepend_please_select = false)
    {
        $business_id = request()->session()->get('user.business_id');
        $payment_ref_numbers = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->where('transactions.business_id', $business_id)->where('type', $type)->whereNotNull('transaction_payments.payment_ref_no')->pluck('transaction_payments.payment_ref_no', 'transaction_payments.payment_ref_no');

        //Prepend none
        if ($prepend_none) {
            $payment_ref_numbers = $payment_ref_numbers->prepend(__('lang_v1.none'), '');
        }

        if ($prepend_all) {
            $payment_ref_numbers = $payment_ref_numbers->prepend(__('lang_v1.all'), '');
        }
        if ($prepend_please_select) {
            $payment_ref_numbers = $payment_ref_numbers->prepend(__('lang_v1.please_select'), '');
        }

        return $payment_ref_numbers;
    }
    /**
     * Cheque number dropdown
     */
    public static function chequeNumberDropDown($type = 'sell', $prepend_none = false, $prepend_all = false, $prepend_please_select = false)
    {
        $business_id = request()->session()->get('user.business_id');
        $payment_ref_numbers = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->where('transactions.business_id', $business_id)->where('type', $type)->whereNotNull('transaction_payments.cheque_number')->pluck('transaction_payments.cheque_number', 'transaction_payments.cheque_number');

        //Prepend none
        if ($prepend_none) {
            $payment_ref_numbers = $payment_ref_numbers->prepend(__('lang_v1.none'), '');
        }

        if ($prepend_all) {
            $payment_ref_numbers = $payment_ref_numbers->prepend(__('lang_v1.all'), '');
        }
        if ($prepend_please_select) {
            $payment_ref_numbers = $payment_ref_numbers->prepend(__('lang_v1.please_select'), '');
        }

        return $payment_ref_numbers;
    }

}
