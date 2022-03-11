<?php



namespace App\Http\Controllers;



use App\BusinessLocation;

use App\Contact;

use App\CustomerReference;

use App\CustomerStatement;

use App\CustomerStatementDetail;

use App\CustomerStatementSetting;

use App\Transaction;

use App\TransactionPayment;

use Illuminate\Http\Request;

use Carbon\Carbon;

use Yajra\DataTables\Facades\DataTables;

use App\Utils\ModuleUtil;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

use App\Utils\Util;

use App\Utils\BusinessUtil;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Modules\Superadmin\Entities\HelpExplanation;



class CustomerStatementController extends Controller

{

    /**

     * All Utils instance.

     *

     */

    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $commonUtil;

    protected $businessUtil;



    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct(BusinessUtil $businessUtil, Util $commonUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil)

    {

        $this->transactionUtil = $transactionUtil;

        $this->productUtil = $productUtil;

        $this->moduleUtil = $moduleUtil;

        $this->commonUtil = $commonUtil;

        $this->businessUtil = $businessUtil;

    }



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    
    {
        

        $business_id = request()->session()->get('business.id');



        $default_start = new Carbon('first day of this month');

        $default_end = new Carbon('last day of this month');



        $start_date = !empty($request->get('start_date')) ? date('Y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');

        $end_date = !empty($request->get('end_date')) ? date('Y-m-d', strtotime($request->get('end_date'))) :  $default_end->format('Y-m-d');



        $edit_customer_statement =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'edit_customer_statement');

        //Return the details in ajax call

        if ($request->ajax()) {

            $query = Transaction::leftjoin('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')

                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

                ->leftjoin('products as p', function ($join) {

                    $join->on('tsl.product_id', 'p.id');

                })

                ->leftjoin('variations', 'p.id', 'variations.product_id')

                ->leftjoin('units', 'p.unit_id', '=', 'units.id')

                ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')

                ->leftjoin('variation_store_details as vsd', 'variations.id', '=', 'vsd.variation_id')

                ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')

                ->where('p.business_id', $business_id)

                ->whereIn('transactions.type', ['sell', 'opening_balance'])

                ->whereIn('transactions.payment_status', ['due', 'partial']);



            $permitted_locations = auth()->user()->permitted_locations();

            $location_filter = '';



            if ($permitted_locations != 'all') {

                $query->whereIn('vld.location_id', $permitted_locations);



                $locations_imploded = implode(', ', $permitted_locations);

                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";

            }



            if (!empty($request->input('location_id'))) {

                $location_id = $request->input('location_id');



                $query->where('vld.location_id', $location_id);



                $location_filter .= "AND transactions.location_id=$location_id";



                //If filter by location then hide products not available in that location

                $query->join('product_locations as pls', 'pls.product_id', '=', 'p.id')

                    ->where(function ($q) use ($location_id) {

                        $q->where('pls.location_id', $location_id);

                    });

            }



            if (!empty($start_date) && !empty($end_date)) {

                $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);

            }



            if (!empty($request->input('customer_id'))) {

                $query->where('transactions.contact_id', $request->input('customer_id'));

            }



            $type = request()->get('type', null);

            if (!empty($type)) {

                $query->where('p.type', $type);

            }

            $query->orderBy('transactions.transaction_date', 'desc');

            //TODO::Check if result is correct after changing LEFT JOIN to INNER JOIN

            $pl_query_string = $this->productUtil->get_pl_quantity_sum_string('pl');

            $products = $query->select(

                'variations.sub_sku as sku',

                'p.name as product',

                'p.type',

                'p.id as product_id',

                // 'business_locations.name as location',

                'units.short_name as unit',

                'p.enable_stock as enable_stock',

                // 'variations.sell_price_inc_tax as unit_price',
                // 'variations.default_sell_price as unit_price',
                'tsl.unit_price',

                'pv.name as product_variation',

                'variations.name as variation_name',

                'tsl.quantity  as sold_qty',

                'transactions.transaction_date  as transaction_date',

                'transactions.type as tran_type',

                'transactions.ref_no',

                'transactions.invoice_no',

                'transactions.customer_ref',

                'transactions.order_no',

                'transactions.order_date',

                'transactions.final_total',

                'transactions.id as transaction_id',

                'tsl.id as tsl_id',

                DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE

                TP.transaction_id=transactions.id) as total_paid')

            )->orderBy('tsl.id', 'asc');



            $datatable =  DataTables::of($products)
                ->editColumn('product', function ($row) {

                    $name = $row->product;

                    if ($row->type == 'variable') {

                        $name .= ' - ' . $row->product_variation . '-' . $row->variation_name;

                    }

                    return $name;

                })
                ->removeColumn('enable_stock')

                ->removeColumn('unit')

                ->removeColumn('id')

                ->addColumn('qty', function ($row) {

                    if ($row->tran_type == 'sell') {

                        return $row->sold_qty;

                    } else {

                        return '';

                    }

                })

                ->addColumn('action', function ($row) use ($edit_customer_statement) {

                    $html = '<div class="btn-group">

                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 

                                data-toggle="dropdown" aria-expanded="false">' .

                        __("messages.actions") .

                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                </span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->transaction_id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';

                    if ($edit_customer_statement) {

                        if (auth()->user()->can('edit_customer_statement')) {

                            $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@edit", [$row->transaction_id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';

                        }

                    }

                    return $html;

                })

                ->editColumn(

                    'transaction_date',

                    '{{@format_date($transaction_date)}}'

                )

                ->addColumn(

                    'due_amount',

                    '<span class="display_currency due" data-currency_symbol="true" data-orig-value="{{@num_format($final_total - $total_paid)}}">{{@num_format($final_total - $total_paid)}}</span>'

                )

                ->editColumn(

                    'final_total',

                    '{{@num_format($final_total)}}'

                )

                ->editColumn(

                    'unit_price',

                    '{{@num_format($unit_price)}}'

                )

                ->editColumn(

                    'qty',

                    '{{@format_quantity($qty)}}'

                );

            $raw_columns  = [

                'action',

                'final_total',

                'due_amount'

            ];



            return $datatable->rawColumns($raw_columns)->make(true);

        }



        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $enable_separate_customer_statement_no =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_separate_customer_statement_no');

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $statement_no = CustomerStatement::where('business_id', $business_id)->count();



        return view('customer_statement.index')->with(compact('customers', 'enable_separate_customer_statement_no', 'business_locations', 'statement_no', 'help_explanations'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        //

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $business_id = request()->session()->get('business.id');

        try {

            $default_start = new Carbon('first day of this month');

            $default_end = new Carbon('last day of this month');



            $start_date = !empty($request->get('start_date')) ? date('Y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');

            $end_date = !empty($request->get('end_date')) ? date('Y-m-d', strtotime($request->get('end_date'))) :  $default_end->format('Y-m-d');



            $query = Transaction::leftjoin('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')

                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

                ->leftjoin('products as p', function ($join) {

                    $join->on('tsl.product_id', 'p.id');

                })

                ->leftjoin('variations', 'p.id', 'variations.product_id')

                ->leftjoin('units', 'p.unit_id', '=', 'units.id')

                ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')

                ->leftjoin('variation_store_details as vsd', 'variations.id', '=', 'vsd.variation_id')

                ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')

                ->where('p.business_id', $business_id)

                ->whereIn('transactions.type', ['sell', 'opening_balance']);



            $permitted_locations = auth()->user()->permitted_locations();

            $location_filter = '';



            if ($permitted_locations != 'all') {

                $query->whereIn('vld.location_id', $permitted_locations);



                $locations_imploded = implode(', ', $permitted_locations);

                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";

            }



            if (!empty($request->input('location_id'))) {

                $location_id = $request->input('location_id');



                $query->where('vld.location_id', $location_id);



                $location_filter .= "AND transactions.location_id=$location_id";



                //If filter by location then hide products not available in that location

                $query->join('product_locations as pls', 'pls.product_id', '=', 'p.id')

                    ->where(function ($q) use ($location_id) {

                        $q->where('pls.location_id', $location_id);

                    });

            }



            if (!empty($start_date) && !empty($end_date)) {

                $query->whereBetween('transactions.transaction_date', [$start_date, $end_date]);

            }



            if (!empty($request->input('customer_id'))) {

                $query->where('transactions.contact_id', $request->input('customer_id'));

            }



            $query->orderBy('transactions.transaction_date', 'desc');



            $transactions = $query->select(

                'variations.sub_sku as sku',

                'p.name as product',

                'p.type',

                'p.id as product_id',

                'business_locations.name as location',

                'units.short_name as unit',

                'p.enable_stock as enable_stock',

                'variations.sell_price_inc_tax as unit_price',

                'pv.name as product_variation',

                'variations.name as variation_name',

                'tsl.quantity  as sold_qty',

                'transactions.transaction_date  as transaction_date',

                'transactions.type as tran_type',

                'transactions.ref_no',

                'transactions.invoice_no',

                'transactions.customer_ref',

                'transactions.order_no',

                'transactions.order_date',

                'transactions.final_total',

                'transactions.id as transaction_id',

                'tsl.id as tsl_id',

                DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE

                TP.transaction_id=transactions.id) as total_paid')

            )->get();

            DB::beginTransaction();

            $statement = CustomerStatement::create([

                'business_id' => $business_id,

                'customer_id' => $request->customer_id,

                'statement_no' => $request->statement_no,

                'print_date' => date('Y-m-d'),

                'date_from' => Carbon::parse($start_date)->format('Y-m-d'),

                'date_to' => Carbon::parse($end_date)->format('Y-m-d'),

                'added_by' => Auth::user()->id

            ]);



            foreach ($transactions as $transaction) {

                CustomerStatementDetail::create([

                    'business_id' => $business_id,

                    'statement_id' => $statement->id,

                    'date' => Carbon::parse($transaction->transaction_date)->format('Y-m-d'),

                    'location' => $transaction->location,

                    'invoice_no' => $transaction->invoice_no,

                    'customer_reference' => $transaction->customer_ref,

                    'order_no' => $transaction->order_no,

                    'order_date' => $transaction->order_date,

                    'product' => $transaction->product,

                    'unit_price' => $transaction->unit_price,

                    'qty' => $transaction->sold_qty,

                    'invoice_amount' => $transaction->final_total,

                    'due_amount' => $transaction->final_total - $transaction->total_paid,

                ]);

            }



            $default_location = BusinessLocation::where('business_id', $business_id)->first();



            $statement->location_id = !empty($transaction[0]->location_id) ? $transaction[0]->location_id : $default_location->id;



            DB::commit();

            $output = [

                'success' => 1,

                'msg' => __('messages.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $statement = CustomerStatement::findOrFail($id);

        $contact = Contact::findOrFail($statement->customer_id);

        $start_date = $statement->date_from;

        $end_date = $statement->date_to;



        $statement_details = CustomerStatementDetail::where('statement_id', $id)->get();



        $ledger_details = $this->__getLedgerDetails($statement->customer_id, $start_date, $end_date);

        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $for_pdf = 1;


        return view('customer_statement.show')->with(compact('contact', 'ledger_details', 'business_details', 'for_pdf', 'location_details', 'statement_details', 'statement'));

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $transaction = Transaction::findOrFail($id);

        $customer_references = CustomerReference::where('contact_id', $transaction->contact_id)->pluck('reference', 'reference');



        return view('customer_statement.edit')->with(compact('transaction', 'customer_references'));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        try {

            $data = array(

                'order_no' => $request->order_no,

                'order_date' => !empty($request->order_date) ? Carbon::parse($request->order_date)->format('Y-m-d') : null,

                'customer_ref' => $request->customer_ref,

            );



            Transaction::where('id', $id)->update($data);



            $output = [

                'success' => 1,

                'msg' => __('message.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        //

    }



    /**

     * print the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function rePrint($id)

    {

        $statement = CustomerStatement::findOrFail($id);

        $contact = Contact::findOrFail($statement->customer_id);

        $start_date = $statement->date_from;

        $end_date = $statement->date_to;



        $statement_details = CustomerStatementDetail::where('statement_id', $id)->get();



        $ledger_details = $this->__getLedgerDetails($statement->customer_id, $start_date, $end_date);

        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $for_pdf = 1;

        $reprint = 1;



        return view('customer_statement.print')->with(compact(

            'contact',

            'ledger_details',

            'business_details',

            'for_pdf',

            'location_details',

            'statement_details',

            'statement',

            'reprint'

        ));

    }



    public function getStatementHeader(Request $request, $statement_no)

    {

        $contact_id = $request->customer_id;

        $start_date = $request->start_date;

        $end_date = $request->end_date;

        

        $contact = Contact::findOrFail($contact_id);


        $ledger_details = $this->__getLedgerDetails($contact_id, $start_date, $end_date);

        $business_details = $this->businessUtil->getDetails($contact->business_id);

        $location_details = BusinessLocation::where('business_id', $contact->business_id)->first();

        $opening_balance = Transaction::where('contact_id', $contact_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');
    
        $for_pdf = 1;

        return view('customer_statement.partials.print_statement_header')->with(compact('contact', 'ledger_details', 'business_details', 'for_pdf', 'location_details', 'statement_no', 'opening_balance'))->render();

    }





    public function getCustomerStatementNo(Request $request)

    {

        $customer_id = $request->customer_id;



        $customer_settings = CustomerStatementSetting::where('customer_id', $customer_id)->first();


        if (!empty($customer_settings)) {

            $starting_no = $customer_settings->starting_no;

        } else {

            $starting_no = 1;

        }



        $count = CustomerStatement::where('customer_id', $customer_id)->count();



        $statement_no = $starting_no + $count;



        $header = (string) $this->getStatementHeader($request, $statement_no);


        return ['statement_no' => $statement_no, 'header' => $header];

    }



    /**

     * Function to get ledger details

     *

     */

    private function __getLedgerDetails($contact_id, $start, $end)

    {

        $contact = Contact::where('id', $contact_id)->first();

        //Get transaction totals between dates

        $transactions = $this->__transactionQuery($contact_id, $start, $end)

            ->with(['location'])->get();



        $transaction_types = Transaction::transactionTypes();



        //Get sum of totals before start date

        $previous_transaction_sums = $this->__transactionQuery($contact_id, $start)

            ->select(

                DB::raw("SUM(IF(type = 'purchase', final_total, 0)) as total_purchase"),

                DB::raw("SUM(IF(type = 'sell' AND status = 'final', final_total, 0)) as total_invoice"),

                DB::raw("SUM(IF(type = 'sell_return', final_total, 0)) as total_sell_return"),

                DB::raw("SUM(IF(type = 'purchase_return', final_total, 0)) as total_purchase_return"),

                DB::raw("SUM(IF(type = 'opening_balance', final_total, 0)) as opening_balance")

            )->first();



        $ledger = [];



        foreach ($transactions as $transaction) {

            $ledger[] = [

                'date' => $transaction->transaction_date,

                'ref_no' => in_array($transaction->type, ['sell', 'sell_return']) ? $transaction->invoice_no : $transaction->ref_no,

                'type' => $transaction_types[$transaction->type],

                'location' => $transaction->location->name,

                'payment_status' =>  __('lang_v1.' . $transaction->payment_status),

                'total' => $transaction->final_total,

                'payment_method' => '',

                'debit' => '',

                'credit' => '',

                'others' => $transaction->additional_notes

            ];

        }



        $invoice_sum = $transactions->where('type', 'sell')->sum('final_total');

        $purchase_sum = $transactions->where('type', 'purchase')->sum('final_total');

        $sell_return_sum = $transactions->where('type', 'sell_return')->sum('final_total');

        $purchase_return_sum = $transactions->where('type', 'purchase_return')->sum('final_total');



        //Get payment totals between dates

        $payments = $this->__paymentQuery($contact_id, $start, $end)

            ->select('transaction_payments.*', 'bl.name as location_name', 't.type as transaction_type', 't.ref_no', 't.invoice_no')->get();

        $paymentTypes = $this->transactionUtil->payment_types();



        //Get payment totals before start date

        $prev_payments_sum = $this->__paymentQuery($contact_id, $start)

            ->select(DB::raw("SUM(transaction_payments.amount) as total_paid"))

            ->first();



        foreach ($payments as $payment) {

            $ref_no = in_array($payment->transaction_type, ['sell', 'sell_return']) ?  $payment->invoice_no :  $payment->ref_no;

            $ledger[] = [

                'date' => $payment->paid_on,

                'ref_no' => $payment->payment_ref_no,

                'type' => $transaction_types['payment'],

                'location' => $payment->location_name,

                'payment_status' => '',

                'total' => '',

                'payment_method' => !empty($paymentTypes[$payment->method]) ? $paymentTypes[$payment->method] : '',

                'debit' => in_array($payment->transaction_type, ['purchase', 'sell_return']) ? $payment->amount : '',

                'credit' => in_array($payment->transaction_type, ['sell', 'purchase_return', 'opening_balance']) ? $payment->amount : '',

                'others' => $payment->note . '<small>' . __('account.payment_for') . ': ' . $ref_no . '</small>'

            ];



            if ($contact->type == "supplier") {

            }

        }



        $total_invoice_paid = $payments->where('transaction_type', 'sell')->sum('amount');
        $total_invoice_paid_ = $payments->where('transaction_type', 'advance_payment')->sum('amount');

        $total_purchase_paid = $payments->where('transaction_type', 'purchase')->sum('amount');



        $start_date = $this->commonUtil->format_date($start);

        $end_date = $this->commonUtil->format_date($end);



        $total_invoice = $invoice_sum - $sell_return_sum;

        $total_purchase = $purchase_sum - $purchase_return_sum;



        $total_prev_invoice = $previous_transaction_sums->total_purchase + $previous_transaction_sums->total_invoice -  $previous_transaction_sums->total_sell_return -  $previous_transaction_sums->total_purchase_return;

        $total_prev_paid = $prev_payments_sum->total_paid;



        $beginning_balance = ($previous_transaction_sums->opening_balance + $total_prev_invoice) - $prev_payments_sum->amount;



        $total_paid = $total_invoice_paid + $total_purchase_paid + $total_invoice_paid_;

        $curr_due = $total_paid - ($beginning_balance + $total_invoice + $total_purchase);



        //Sort by date

        if (!empty($ledger)) {

            usort($ledger, function ($a, $b) {

                $t1 = strtotime($a['date']);

                $t2 = strtotime($b['date']);

                return $t2 - $t1;

            });

        }



        $output = [

            'ledger' => $ledger,

            'start_date' => $start_date,

            'end_date' => $end_date,

            'total_invoice' => $total_invoice,

            'total_purchase' => $total_purchase,

            'beginning_balance' => $beginning_balance,

            'balance_due' => $curr_due,

            'total_paid' => $total_paid

        ];


        return $output;

    }





    /**

     * Query to get transaction totals for a customer

     *

     */

    private function __transactionQuery($contact_id, $start, $end = null)

    {

        $business_id = request()->session()->get('user.business_id');

        $transaction_type_keys = array_keys(Transaction::transactionTypes());



        $query = Transaction::where('transactions.contact_id', $contact_id)

            ->where('transactions.business_id', $business_id)

            ->where('status', '!=', 'draft')

            ->whereIn('type', $transaction_type_keys);



        if (!empty($start)  && !empty($end)) {

            $query->whereDate(

                'transactions.transaction_date',

                '>=',

                $start

            )

                ->whereDate('transactions.transaction_date', '<=', $end)->get();

        }



        if (!empty($start)  && empty($end)) {

            $query->whereDate('transactions.transaction_date', '<', $start);

        }



        return $query;

    }



    /**

     * Query to get payment details for a customer

     *

     */

    private function __paymentQuery($contact_id, $start, $end = null)

    {

        $business_id = request()->session()->get('user.business_id');



        $query = TransactionPayment::join(

            'transactions as t',

            'transaction_payments.transaction_id',

            '=',

            't.id'

        )

            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')

            ->where('t.contact_id', $contact_id)

            ->where('t.business_id', $business_id)

            ->where('t.status', '!=', 'draft');



        if (!empty($start)  && !empty($end)) {

            $query->whereDate('paid_on', '>=', $start)

                ->whereDate('paid_on', '<=', $end);

        }



        if (!empty($start)  && empty($end)) {

            $query->whereDate('paid_on', '<', $start);

        }



        return $query;

    }





    public function getCustomerStatementList(Request $request)

    {

        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {

            $query = CustomerStatement::leftjoin('contacts', 'customer_statements.customer_id', 'contacts.id')

                ->leftjoin('users', 'customer_statements.added_by', 'users.id')
                
                ->leftjoin('customer_statement_details', 'customer_statements.id', 'customer_statement_details.statement_id')

                ->leftjoin('business_locations', 'customer_statements.location_id', 'business_locations.id')

                ->where('customer_statements.business_id',  $business_id)

                ->select(

                   [ 'customer_statements.*',

                    'contacts.name as customer',
                    
                    'customer_statement_details.invoice_amount as amount',

                    'users.username as username',
                    
                    'business_locations.name as location']

                );



                $fuel_tanks = Datatables::of($query)

                ->addColumn('action', function ($row) {

                    $html = '<div class="btn-group">

                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 

                            data-toggle="dropdown" aria-expanded="false">' .

                        __("messages.actions") .

                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown

                            </span>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@show", [$row->id]) . '" class="btn-modal" data-container=".customer_statement_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';

                    $html .= '<li><a href="#" data-href="' . action("CustomerStatementController@rePrint", [$row->id]) . '" class="reprint_statement"><i class="fa fa-print" aria-hidden="true"></i> ' . __("contact.print") . '</a></li>';

                    return $html;

                })

                ->addColumn(

                    'amount',

                    '{{$amount}}'

                ) 

                ->removeColumn('id');





            return $fuel_tanks->rawColumns(['action', 'amount'])

                ->make(true);
        }

    }

}

