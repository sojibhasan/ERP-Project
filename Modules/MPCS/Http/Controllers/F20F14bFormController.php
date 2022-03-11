<?php

namespace Modules\MPCS\Http\Controllers;

use App\Business;
use App\BusinessLocation;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;

class F20F14bFormController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $util;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->util = $util;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $setting = MpcsFormSetting::where('business_id', $business_id)->first();
        if (!empty($setting)) {
            $F20_form_sn = !empty($setting->F20_form_sn) ? $setting->F20_form_sn : 1;
        } else {
            $F20_form_sn = 1;
        }
        if (!empty($setting)) {
            $F14_from_no  = !empty($setting->F14_form_sn) ? $setting->F14_form_sn : 1;
        } else {
            $F14_from_no  = 1;
        }

        $business_locations = BusinessLocation::forDropdown($business_id);


        return view('mpcs::forms.F20andF14b.index')->with(compact(
            'F20_form_sn',
            'F14_from_no',
            'setting',
            'business_locations'
        ));
    }

    /**
     * Show the form for getFrom20 
     * @return Response
     */
    public function getFrom14B(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $query = Transaction::leftjoin('settlement_credit_sale_payments', 'transactions.credit_sale_id', 'settlement_credit_sale_payments.id')
                ->leftjoin('products', 'settlement_credit_sale_payments.product_id', 'products.id')
                ->leftjoin('contacts', 'settlement_credit_sale_payments.customer_id', 'contacts.id')
                ->leftjoin('business', 'transactions.business_id', 'business.id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->where('settlement_credit_sale_payments.business_id', $business_id)
                ->whereDate('transactions.transaction_date', '>=', $request->start_date)
                ->whereDate('transactions.transaction_date', '<=', $request->end_date)
                ->select(
                    'transactions.transaction_date',
                    'transactions.final_total',
                    'products.name as description',
                    'settlement_credit_sale_payments.qty as balance_qty',
                    'settlement_credit_sale_payments.price as unit_price',
                    'transactions.ref_no',
                    'transactions.invoice_no',
                    'contacts.name as customer',
                    'settlement_credit_sale_payments.order_number as order_no',
                    'settlement_credit_sale_payments.customer_reference as customer_reference',
                    'business.name as comapany',
                    'business_locations.mobile as tel',
                );
            if(!empty($request->location_id)){
                $query->where('transactions.location_id', $request->location_id);
            }
            $credit_sales = $query->get();
            return view('mpcs::forms.F20andF14b.partials.14b_form')->with(compact('credit_sales'));
        }
    }
    /**
     * Show the form for getFrom20 
     * @return Response
     */
    public function getFrom20()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->join(
                    'business_locations AS BS',
                    'transactions.location_id',
                    '=',
                    'BS.id'
                )
                ->leftJoin(
                    'transaction_payments AS TP',
                    'transactions.id',
                    '=',
                    'TP.transaction_id'
                )
                ->leftJoin(
                    'transactions AS PR',
                    'transactions.id',
                    '=',
                    'PR.return_parent_id'
                )
                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('variations', 'products.id', 'variations.product_id')
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->select(
                    'transactions.id',
                    'transactions.ref_no as reference_no',
                    'transaction_sell_lines.quantity as sold_qty',
                    'transaction_sell_lines.unit_price as unit_price',
                    'transactions.final_total as total_purchase_price',
                    'transactions.is_credit_sale',
                    'BS.name as location',
                    'products.sku as sku',
                    'products.name as product',
                    'products.id as product_id',
                    'TP.method as payment_method',
                    'variations.default_sell_price',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'PR.id as return_transaction_id',
                    DB::raw('SUM(TP.amount) as amount_paid'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=PR.id ) as return_paid'),
                    DB::raw('COUNT(PR.id) as return_exists'),
                    DB::raw('COALESCE(PR.final_total, 0) as amount_return'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                )
                ->groupBy('transactions.id');

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $purchases->whereIn('transactions.location_id', $permitted_locations);
            }

            if (!empty(request()->location_id)) {
                $purchases->where('transactions.location_id', request()->location_id);
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases->whereDate('transactions.transaction_date', '>=', $start)
                    ->whereDate('transactions.transaction_date', '<=', $end);
            }

            $business_id = session()->get('user.business_id');
            $business_details = Business::find($business_id);

            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('total_amount', function ($row) use ($business_details) {
                    $total_amount = $row->sold_qty * $row->unit_price;
                    $html = '';
                    if ($row->payment_method == 'cash' || $row->payment_method == 'card' || $row->payment_method == 'cheque') {
                         $html .='<span class="display_currency cash_sale" data-orig-value="' . $total_amount . '" data-currency_symbol = "false">' . $this->productUtil->num_f($total_amount, false, $business_details, false) . '</span>';
                    }
                    if ($row->is_credit_sale == 1) {
                         $html .='<span class="display_currency credit_sale" data-orig-value="' . $total_amount . '" data-currency_symbol = "false">' . $this->productUtil->num_f($total_amount, false, $business_details, false) . '</span>';
                    }
                    return $html;
                })
                ->addColumn('unit_price', function ($row) use ($business_details) {
                    return $this->productUtil->num_f($row->unit_price, false, $business_details, false);
                })
                ->addColumn('sold_qty', function ($row) use ($business_details) {
                    return $this->productUtil->num_f($row->sold_qty, false, $business_details, true);
                })
                ->removeColumn('id')

                ->rawColumns(['total_amount', 'unit_price'])
                ->make(true);
        }
    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('mpcs::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('mpcs::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('mpcs::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
