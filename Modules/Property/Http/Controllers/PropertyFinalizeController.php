<?php



namespace Modules\Property\Http\Controllers;



use App\Account;

use App\AccountTransaction;

use App\ContactLedger;

use App\Transaction;

use App\Utils\BusinessUtil;

use App\Utils\ModuleUtil;

use App\Utils\TransactionUtil;

use App\Utils\Util;

use Carbon\Carbon;

use Illuminate\Contracts\Support\Renderable;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Modules\Property\Entities\BlockCloseReason;

use Modules\Property\Entities\FinanceOption;

use Modules\Property\Entities\Installment;

use Modules\Property\Entities\InstallmentCycle;

use Modules\Property\Entities\Property;

use Modules\Property\Entities\PropertyAccountSetting;

use Modules\Property\Entities\PropertyBlock;

use Modules\Property\Entities\PropertyFinalize;

use Modules\Property\Entities\PropertySellLine;

use Yajra\DataTables\Facades\DataTables;



class PropertyFinalizeController extends Controller

{

    protected $moduleUtil;

    protected $commonUtil;

    protected $businessUtil;

    protected $transactionUtil;



    /**

     * Constructor

     *

     *

     * @return void

     */

    public function __construct(ModuleUtil $moduleUtil, Util $commonUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil)

    {

        $this->moduleUtil = $moduleUtil;

        $this->commonUtil = $commonUtil;

        $this->businessUtil = $businessUtil;

        $this->transactionUtil = $transactionUtil;

    }





    /**

     * Display a listing of the resource.

     * @return Renderable

     */

    public function index()

    {

        $business_id = request()->session()->get('user.business_id');



        if (request()->ajax()) {
             DB::enableQueryLog();
            $purchases = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')

                ->leftJoin('properties', 'property_sell_lines.property_id', '=', 'properties.id')

                ->leftJoin('property_blocks', 'property_sell_lines.block_id', '=', 'property_blocks.id')

                ->leftJoin('units', 'properties.unit_id', '=', 'units.id')

                ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                ->leftJoin('property_finalizes', 'property_sell_lines.id', '=', 'property_finalizes.property_sell_line_id')

                ->leftJoin('installment_cycles', 'property_finalizes.installment_cycle_id', '=', 'installment_cycles.id')

                ->leftJoin('finance_options', 'property_finalizes.finance_option_id', '=', 'finance_options.id')

                ->leftJoin(

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



                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')

                ->where('transactions.business_id', $business_id)

                ->where('transactions.type', 'property_sell')

                ->select(

                    'properties.id as property_id',

                    'properties.name as property_name',

                    'properties.extent',

                    'properties.status as property_status',

                    'property_blocks.id as block_id',

                    'property_blocks.block_number',

                    'property_blocks.block_extent',

                    'property_blocks.block_sale_price',

                    'property_blocks.block_sold_price',

                    'property_blocks.is_finalized',

                    'property_finalizes.id as finalize_id',

                    'property_finalizes.down_payment',

                    'property_finalizes.installment_amount',

                    'property_finalizes.loan_capital',

                    'property_finalizes.total_interest',

                    'property_finalizes.no_of_installment',

                    'property_finalizes.easy_payment',

                    'property_finalizes.first_installment_date',

                    'property_finalizes.is_closed',

                    'units.actual_name as unit_name',

                    'transactions.id',

                    'transactions.deed_no',

                    'transactions.document',

                    'transactions.transaction_date',

                    'transactions.ref_no',

                    'transactions.invoice_no',

                    'contacts.name as customer_name',

                    'transactions.status',

                    'transactions.payment_status',

                    'transactions.final_total',

                    'installment_cycles.name as installment_cycle',

                    'BS.name as location_name',

                    'transactions.pay_term_number',

                    'transactions.pay_term_type',

                    'TP.method',

                    'TP.account_id',

                    'property_sell_lines.id as psl_id',

                    'finance_options.finance_option',

                    'finance_options.custom_payments',

                    DB::raw('TP.amount as amount_paid'),

                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")

                );



            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {

                $purchases->whereIn('transactions.location_id', $permitted_locations);

            }



            if (!empty(request()->supplier_id)) {

                $purchases->where('contacts.id', request()->supplier_id);

            }

            if (!empty(request()->location_id)) {

                $purchases->where('transactions.location_id', request()->location_id);

            }



            if (!empty(request()->status)) {

                $purchases->where('properties.status', request()->status);

            }



            if (!empty(request()->start_date) && !empty(request()->end_date)) {

                $start = request()->start_date;

                $end =  request()->end_date;

                $purchases->whereDate('transactions.transaction_date', '>=', $start)

                    ->whereDate('transactions.transaction_date', '<=', $end);

            }

        //     echo "<pre>";
        //     print_r($purchases->toSql());
        //     die(
            
        // );
            return DataTables::of($purchases)

                ->addColumn('action', function ($row) {

                    $html = '<div class="btn-group">

                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 

                                data-toggle="dropdown" aria-expanded="false">' .

                        __("messages.actions") .

                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                </span>

                            </button>

                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (!$row->is_finalized) {

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyFinalizeController@create', ['sell_line_id' => $row->psl_id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-bandcamp" aria-hidden="true"></i>' . __("property::lang.finalize") . '</a></li>';

                    }

                    if (!$row->all_payments_completed) {

                        if (auth()->user()->can('property.add_new_sale')) {

                            $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyFinalizeController@create', ['sell_line_id' => $row->psl_id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-bandcamp" aria-hidden="true"></i>' . __("property::lang.add_new_sale") . '</a></li>';

                        }

                    }

                    if (auth()->user()->can('property.current_sale.edit') && !empty($row->finalize_id)) {

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyFinalizeController@edit', $row->finalize_id) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("property::lang.edit_current_sale") . '</a></li>';

                    }

                    if (!empty($row->finalize_id)) {

                        if (auth()->user()->can('property.current_sale.view')) {

                            $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\PropertyFinalizeController@show', $row->finalize_id) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("property::lang.view_current_sale") . '</a></li>';

                        }

                        if (!$row->is_closed) {

                            if (auth()->user()->can('property.current_sale.close.create')) {

                                $html .= '<li class="divider"></li>';

                                $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\CloseCurrentSaleController@create', ['finalize_id' => $row->finalize_id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-ban" aria-hidden="true"></i>' . __("property::lang.close_current_sale") . '</a></li>';

                            }

                        }

                        if ($row->is_closed) {

                            $html .= '<li class="divider"></li>';

                            $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\CloseCurrentSaleController@show', $row->finalize_id) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("property::lang.view_closed_sales") . '</a></li>';

                        }

                    }

                    if (!empty($row->is_closed)) {

                        if (auth()->user()->can('property.current_sale.close.edit')) {

                            $html .= '<li><a href="#" data-href="' . action('\Modules\Property\Http\Controllers\CloseCurrentSaleController@edit', $row->finalize_id) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>' . __("property::lang.edit_close_current_sale") . '</a></li>';

                        }

                    }

                    $html .= '<li class="divider"></li>';

                    $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->id]) . '" class="view_payment_modal"><i class="fa fa-money"></i> ' . __("purchase.view_payments") . '</a></li>';





                    $html .=  '</ul></div>';

                    return $html;

                })

                ->addColumn('purchase_no', function ($row) {

                    return $row->id;

                })

                ->addColumn('reservation_amount', function ($row) {

                    return 0;

                })

                ->editColumn('easy_payment', '{{ucfirst($easy_payment)}}')

                ->editColumn('no_of_installment', '{{@num_format($no_of_installment)}}')

                ->editColumn('first_installment_date', '@if(!empty($first_installment_date)){{@format_date($first_installment_date)}}@endif')

                ->editColumn('installment_cycle', function ($row) {

                    if ($row->custom_payments == 'no') {

                        return $row->installment_cycle;

                    }

                    return '';

                })

                ->editColumn(

                    'installment_amount',

                    function ($row) {

                        if ($row->custom_payments == 'no') {

                            return '<span class="display_currency installment_amount" data-currency_symbol="false" data-orig-value="' . $row->installment_amount . '">' . $row->installment_amount . '</span>';

                        }

                        return '';

                    }

                )

                ->editColumn(

                    'down_payment',

                    '<span class="display_currency down_payment" data-currency_symbol="false" data-orig-value="{{$down_payment}}">{{$down_payment}}</span>'

                )

                ->editColumn(

                    'final_total',

                    '<span class="display_currency final_total" data-currency_symbol="false" data-orig-value="{{$final_total}}">{{$final_total}}</span>'

                )

                ->editColumn(

                    'loan_capital',

                    '<span class="display_currency loan_capital" data-currency_symbol="false" data-orig-value="{{$loan_capital}}">{{$loan_capital}}</span>'

                )

                ->editColumn(

                    'total_interest',

                    '<span class="display_currency total_interest" data-currency_symbol="false" data-orig-value="{{$total_interest}}">{{$total_interest}}</span>'

                )

                ->editColumn('property_status', '{{ucfirst($property_status)}}')

                ->editColumn('block_extent', '{{@format_quantity($block_extent)}}')

                ->editColumn('block_sale_price', '{{@num_format($block_sale_price)}}')

                ->editColumn('block_sold_price', '{{@num_format($block_sold_price)}}')

                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')





                ->removeColumn('id')

                ->rawColumns([

                    'final_total',

                    'action',

                    'payment_due',

                    'payment_status',

                    'status',

                    'ref_no',

                    'down_payment',

                    'installment_amount',

                    'capital',

                    'final_total',

                    'loan_capital',

                    'total_interest',

                    'payment_method'

                ])

                ->make(true);

        }

    }



    /**

     * Show the form for creating a new resource.

     * @return Renderable

     */

    public function create()

    {

        $sell_line_id = request()->sell_line_id;

        $business_id = request()->session()->get('user.business_id');

        $property_sell = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')

            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

            ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')

            ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')

            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')

            ->where('property_sell_lines.id', $sell_line_id)

            ->select(

                'property_blocks.*',

                'properties.name as property_name',

                'business_locations.name as location_name',

                'contacts.name as customer_name',

                'transactions.transaction_date',

                'transactions.invoice_no',

                'property_sell_lines.id as property_sell_line_id',

                'transactions.id as transaction_id'

            )->first();

          $contactLedger = ContactLedger::where('payment_option_id',27)->get()->sum('amount');

       

        

        $finance_options = FinanceOption::where('business_id', $business_id)->pluck('finance_option', 'id');

        $installment_cycles = InstallmentCycle::where('business_id', $business_id)->pluck('name', 'id');



        return view('property::property_finalize.create')->with(compact(

            'property_sell',

            'finance_options',

            'installment_cycles',

            'contactLedger'

        ));

    }



    /**

     * Store a newly created resource in storage.

     * @param Request $request

     * @return Renderable

     */

    public function store(Request $request)

    {



        try {

            $business_id = request()->session()->get('user.business_id');

            $inputs = $request->except(['_token', 'installments', 'custom_payments']);



            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            if (!empty($document_name)) {

                $inputs['attachment'] = $document_name;

            }



            $inputs['business_id'] = $business_id;

            $inputs['date'] = !empty($inputs['date']) ? $this->transactionUtil->uf_date($inputs['date']) : date('Y-m-d');

            $inputs['first_installment_date'] = !empty($inputs['first_installment_date']) ?  $this->transactionUtil->uf_date($inputs['first_installment_date']) : date('Y-m-d');



            DB::beginTransaction();

            PropertyFinalize::create($inputs);



            PropertyBlock::where('id', $inputs['block_id'])->update(['is_finalized' => 1]);

            //create account transactions

            if ($inputs['easy_payment'] == 'yes') {



                $property_sell_line = PropertySellLine::find($inputs['property_sell_line_id']);

                $account_settings = PropertyAccountSetting::where('property_id',  $property_sell_line->property_id)->first();



                $transaction = Transaction::find($property_sell_line->transaction_id);



                //capital amount transactions

                $account_transaction_data = [

                    'contact_id' => !empty($transaction) ? $transaction->contact_id : null,

                    'amount' => $inputs['loan_capital'],

                    'account_id' => $account_settings->account_receivable_account_id,

                    'type' => 'debit',

                    'operation_date' =>  !empty($inputs['date']) ? $inputs['date'] : date('Y-m-d H:i:s'),

                    'created_by' => Auth::user()->id,

                    'transaction_id' => !empty($transaction) ? $transaction->id : null,

                    'transaction_sell_line_id' =>  !empty($inputs['property_sell_line_id']) ? $inputs['property_sell_line_id'] : null,

                    'income_type' => 'capital income'

                ];

                AccountTransaction::createAccountTransaction($account_transaction_data);

                ContactLedger::createContactLedger($account_transaction_data);



                $account_transaction_data['account_id'] = $account_settings->income_account_id;

                $account_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($account_transaction_data);



                //interest amount transactions

                $account_transaction_data['amount'] = $inputs['total_interest'];

                $account_transaction_data['income_type'] = 'interest income';

                $account_transaction_data['account_id'] = $account_settings->account_receivable_account_id;

                $account_transaction_data['type'] = 'debit';

                AccountTransaction::createAccountTransaction($account_transaction_data);

                ContactLedger::createContactLedger($account_transaction_data);



                $account_transaction_data['account_id'] = $account_settings->interest_income_account_id;

                $account_transaction_data['type'] = 'credit';

                AccountTransaction::createAccountTransaction($account_transaction_data);

                $installments = $request->installments;



                if(!empty($installments)){

                    foreach ($installments as $installment) {

                        $installment_data = [

                            'business_id' => $business_id,

                            'transaction_id' => !empty($transaction) ? $transaction->id : null,

                            'transaction_sell_line_id' => !empty($inputs['property_sell_line_id']) ? $inputs['property_sell_line_id'] : null,

                            'installment_no' => $installment['installment_no'],

                            'amount' => $installment['amount'],

                            'date' => $this->transactionUtil->uf_date($installment['date']),

                            'payment_status' => 'due'

                        ];

                        $installment_new = Installment::create($installment_data);

    

                        $this_date = Carbon::createFromFormat('Y-m-d', $this->transactionUtil->uf_date($installment['date']));

                        $now = Carbon::now();

                        $diff = $this_date->diffInMonths($now, false);

    

    

                        if ($diff >= 0) {

                            $account_receivable_id = Account::getAccountByAccountName('Accounts Receivable')->id;

                            $account_transaction_data = [

                                'contact_id' => !empty($transaction) ? $transaction->contact_id : null,

                                'amount' => $installment['amount'],

                                'account_id' => $account_receivable_id,

                                'type' => 'debit',

                                'operation_date' =>  !empty($installment['date']) ? $this->transactionUtil->uf_date($installment['date']) : date('Y-m-d H:i:s'),

                                'created_by' => Auth::user()->id,

                                'transaction_id' => !empty($transaction) ? $transaction->id : null,

                                'transaction_sell_line_id' =>  !empty($inputs['property_sell_line_id']) ? $inputs['property_sell_line_id'] : null,

                                'installment_id' =>  $installment_new->id

                            ];

                            AccountTransaction::createAccountTransaction($account_transaction_data);

                            $account_transaction_data['type'] = 'debit';

                            $account_transaction_data['installment_id'] = $installment_new->id;

                            ContactLedger::createContactLedger($account_transaction_data);

                        }

                    }

                }

            }



            DB::commit();

            $output = [

                'success' => true,

                'tab' => 'property_details',

                'msg' => __('property::lang.property_finalize_success')

            ];

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'tab' => 'property_details',

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

    }



    /**

     * Show the specified resource.

     * @param int $id

     * @return Renderable

     */

    public function show($id)

    {

        $property_finalize = PropertyFinalize::find($id);

        $sell_line_id = $property_finalize->property_sell_line_id;

        $business_id = request()->session()->get('user.business_id');

        $property_sell = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')

            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

            ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')

            ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')

            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')

            ->where('property_sell_lines.id', $sell_line_id)

            ->select(

                'property_blocks.*',

                'properties.name as property_name',

                'business_locations.name as location_name',

                'contacts.name as customer_name',

                'transactions.transaction_date',

                'transactions.invoice_no',

                'property_sell_lines.id as property_sell_line_id',

                'transactions.id as transaction_id'

            )



            ->first();



        $finance_options = FinanceOption::where('business_id', $business_id)->pluck('finance_option', 'id');

        $installment_cycles = InstallmentCycle::where('business_id', $business_id)->pluck('name', 'id');

        $installments = Installment::where('transaction_id', $property_finalize->transaction_id)->get();

        $finance_option = FinanceOption::find($property_finalize->finance_option_id);



        return view('property::property_finalize.show')->with(compact(

            'property_sell',

            'finance_options',

            'finance_option',

            'installment_cycles',

            'installments',

            'property_finalize'

        ));

    }



    /**

     * Show the form for editing the specified resource.

     * @param int $id

     * @return Renderable

     */

    public function edit($id)

    {

        $property_finalize = PropertyFinalize::find($id);

        $sell_line_id = $property_finalize->property_sell_line_id;

        $business_id = request()->session()->get('user.business_id');

        $property_sell = Transaction::leftjoin('property_sell_lines', 'transactions.id', 'property_sell_lines.transaction_id')

            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

            ->leftjoin('property_blocks', 'property_sell_lines.block_id', 'property_blocks.id')

            ->leftjoin('properties', 'property_sell_lines.property_id', 'properties.id')

            ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')

            ->where('property_sell_lines.id', $sell_line_id)

            ->select(

                'property_blocks.*',

                'properties.name as property_name',

                'business_locations.name as location_name',

                'contacts.name as customer_name',

                'transactions.transaction_date',

                'transactions.invoice_no',

                'property_sell_lines.id as property_sell_line_id',

                'transactions.id as transaction_id'

            )



            ->first();



        $finance_options = FinanceOption::where('business_id', $business_id)->pluck('finance_option', 'id');

        $installment_cycles = InstallmentCycle::where('business_id', $business_id)->pluck('name', 'id');

        $installments = Installment::where('transaction_id', $property_finalize->transaction_id)->get();

        $finance_option = FinanceOption::find($property_finalize->finance_option_id);



        return view('property::property_finalize.edit')->with(compact(

            'property_sell',

            'finance_options',

            'finance_option',

            'installment_cycles',

            'installments',

            'property_finalize'

        ));

    }



    /**

     * Update the specified resource in storage.

     * @param Request $request

     * @param int $id

     * @return Renderable

     */

    public function update(Request $request, $id)

    {

        try {

            $business_id = request()->session()->get('user.business_id');

            $inputs = $request->except(['_token', '_method', 'installments']);



            $document_name = $this->transactionUtil->uploadFile($request, 'attachment', 'attachment');

            if (!empty($document_name)) {

                $inputs['attachment'] = $document_name;

            }



            $inputs['business_id'] = $business_id;

            $inputs['date'] = !empty($inputs['date']) ? Carbon::createFromFormat('d/m/Y', $inputs['date'])->format('Y-m-d') : date('Y-m-d');

            $inputs['first_installment_date'] = !empty($inputs['first_installment_date']) ?  Carbon::createFromFormat('d/m/Y', $inputs['first_installment_date'])->format('Y-m-d') : date('Y-m-d');



            DB::beginTransaction();

            $property_finalize = PropertyFinalize::where('id', $id)->update($inputs);

            $property_finalize = PropertyFinalize::find($id);

            $transaction = Transaction::find($property_finalize->transaction_id);



            $installments = $request->installments;



            $installment_new_array = [];

            foreach ($installments as $installment) {

                $installment_data = [

                    'business_id' => $business_id,

                    'transaction_id' => !empty($transaction) ? $transaction->id : null,

                    'transaction_sell_line_id' => !empty($inputs['property_sell_line_id']) ? $inputs['property_sell_line_id'] : null,

                    'installment_no' => $installment['installment_no'],

                    'amount' => $installment['amount'],

                    'date' => $this->transactionUtil->uf_date($installment['date']),

                    'payment_status' => 'due'

                ];

                if (!empty($installment['installment_id'])) {

                    $installment_new = Installment::where('id', $installment['installment_id'])->update($installment_data);

                    $edited_installment_ids[] = $installment['installment_id'];

                    $this_date = Carbon::createFromFormat('Y-m-d', $this->transactionUtil->uf_date($installment['date']));

                    $now = Carbon::now();

                    $diff = $this_date->diffInMonths($now, false);





                    if ($diff >= 0) {

                        $account_receivable_id = Account::getAccountByAccountName('Accounts Receivable')->id;

                        $account_transaction_data = [

                            'amount' => $installment['amount'],

                            'account_id' => $account_receivable_id,

                            'type' => 'debit',

                            'operation_date' =>  !empty($installment['date']) ? $this->transactionUtil->uf_date($installment['date']) : date('Y-m-d H:i:s'),

                            'created_by' => Auth::user()->id,

                            'transaction_id' => !empty($transaction) ? $transaction->id : null,

                            'transaction_sell_line_id' =>  !empty($inputs['property_sell_line_id']) ? $inputs['property_sell_line_id'] : null

                        ];

                        AccountTransaction::where('installment_id', $installment['installment_id'])->update($account_transaction_data);

                        $account_transaction_data['type'] = 'debit';

                        $account_transaction_data['contact_id'] = empty($transaction) ? $transaction->contact_id : null;

                        unset($account_transaction_data['account_id']);

                        ContactLedger::where('installment_id', $installment['installment_id'])->update($account_transaction_data);

                    } else {

                        AccountTransaction::where('installment_id', $installment['installment_id'])->forcedelete();

                        ContactLedger::where('installment_id', $installment['installment_id'])->forcedelete();

                    }

                } else {

                    $installment_new_array[] = $installment_data;

                }

            }



            Installment::whereNotIn('id', $edited_installment_ids)->delete();

            AccountTransaction::where('transaction_id', $transaction->id)->whereNotIn('installment_id', $edited_installment_ids)->forcedelete();



            foreach ($installment_new_array as $installment_n) {

                $installment_new_created = Installment::create($installment_n);



                $this_date = Carbon::createFromFormat('Y-m-d', $this->transactionUtil->uf_date($installment_n['date']));

                $now = Carbon::now();

                $diff = $this_date->diffInMonths($now, false);





                if ($diff >= 0) {

                    $account_receivable_id = Account::getAccountByAccountName('Accounts Receivable')->id;

                    $account_transaction_data = [

                        'contact_id' => !empty($transaction) ? $transaction->contact_id : null,

                        'amount' => $installment_n['amount'],

                        'account_id' => $account_receivable_id,

                        'type' => 'debit',

                        'operation_date' =>  !empty($installment_n['date']) ? $this->transactionUtil->uf_date($installment_n['date']) : date('Y-m-d H:i:s'),

                        'created_by' => Auth::user()->id,

                        'transaction_id' => !empty($transaction) ? $transaction->id : null,

                        'transaction_sell_line_id' =>  !empty($inputs['property_sell_line_id']) ? $inputs['property_sell_line_id'] : null,

                        'installment_id' =>  $installment_new_created->id

                    ];

                    AccountTransaction::createAccountTransaction($account_transaction_data);

                    $account_transaction_data['type'] = 'debit';

                    $account_transaction_data['installment_id'] = $installment_new_created->id;

                    ContactLedger::createContactLedger($account_transaction_data);

                }

            }



            DB::commit();

            $output = [

                'success' => true,

                'tab' => 'property_details',

                'msg' => __('property::lang.property_finalize_success')

            ];

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'tab' => 'property_details',

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

    }



    /**

     * Remove the specified resource from storage.

     * @param int $id

     * @return Renderable

     */

    public function destroy($id)

    {

        //

    }

}

