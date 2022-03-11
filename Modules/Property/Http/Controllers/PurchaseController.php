<?php

namespace Modules\Property\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Account;
use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\ContactLedger;
use App\ContactGroup;
use App\Product;
use App\PurchaseLine;
use App\Store;
use App\TaxRate;
use App\Transaction;
use App\Unit;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\TankPurchaseLine;
use Modules\Property\Entities\Property;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'account_id' => ''
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('property.purchase.view') && !auth()->user()->can('property.purchase.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = Property::leftJoin('transactions', 'properties.transaction_id', '=', 'transactions.id')
                ->leftJoin('units', 'properties.unit_id', '=', 'units.id')
                ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
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
                ->where('transactions.type', 'property_purchase')
                ->select(
                    'properties.name as property_name',
                    'properties.extent',
                    'properties.status as property_status',
                    'units.actual_name',
                    'transactions.id',
                    'transactions.deed_no',
                    'transactions.document',
                    'transactions.transaction_date',
                    'transactions.ref_no',
                    'transactions.invoice_no',
                    'contacts.name as supplier_name',
                    'transactions.status',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'BS.name as location_name',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'TP.method',
                    'TP.account_id',
                    DB::raw('SUM(TP.amount) as amount_paid'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                )->groupBy('properties.id');

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
            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $purchases->where('transactions.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $purchases->whereIn('transactions.payment_status', ['due', 'partial'])
                    ->whereNotNull('transactions.pay_term_number')
                    ->whereNotNull('transactions.pay_term_type')
                    ->whereRaw("IF(transactions.pay_term_type='days', DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number DAY) < CURDATE(), DATE_ADD(transactions.transaction_date, INTERVAL transactions.pay_term_number MONTH) < CURDATE())");
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

                    if (auth()->user()->can("property.purchase.edit")) {
                        $html .= '<li><a href="' . action('\Modules\Property\Http\Controllers\PurchaseController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                    }

                    $document_name = !empty(explode("_", $row->document, 2)[1]) ? explode("_", $row->document, 2)[1] : $row->document;
                    $html .= '<li><a href="' . asset('uploads/documents/' . $row->document) . '" download="' . $document_name . '"><i class="fa fa-download" aria-hidden="true"></i>' . __("purchase.download_document") . '</a></li>';
                    if (isFileImage($document_name)) {
                        $html .= '<li><a href="#" data-href="' . asset('uploads/documents/' . $row->document) . '" class="view_uploaded_document"><i class="fa fa-picture-o" aria-hidden="true"></i>' . __("lang_v1.view_document") . '</a></li>';
                    }


                    if (auth()->user()->can("send_notification")) {
                        if ($row->status == 'ordered') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "new_order"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.new_order_notification") . '</a></li>';
                        } elseif ($row->status == 'received') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "items_received"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_received_notification") . '</a></li>';
                        } elseif ($row->status == 'pending') {
                            $html .= '<li><a href="#" data-href="' . action('NotificationController@getTemplate', ["transaction_id" => $row->id, "template_for" => "items_pending"]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-envelope" aria-hidden="true"></i> ' . __("lang_v1.item_pending_notification") . '</a></li>';
                        }
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->addColumn('purchase_no', function ($row) {
                    return $row->id;
                })
                ->removeColumn('id')
                ->editColumn('ref_no', function ($row) {
                    return !empty($row->return_exists) ? $row->invoice_no . ' <small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned') . '"><i class="fa fa-undo"></i></small>' : $row->ref_no;
                })
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn('property_status', '{{ucfirst($property_status)}}')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->addColumn('pay_terms', '{{$pay_term_number}} {{ucfirst($pay_term_type)}}')
                ->editColumn(
                    'status',
                    '<a href="#" @if(auth()->user()->can("property.purchase.edit")) class="update_status no-print" data-purchase_id="{{$id}}" data-status="{{$status}}" @endif><span class="label @transaction_status($status) status-label" data-status-name="{{__(\'lang_v1.\' . $status)}}" data-orig-value="{{$status}}">{{__(\'lang_v1.\' . $status)}}
                        </span></a>'
                )
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                    }
                )

                ->addColumn('payment_due', function ($row) {
                    $due = $row->final_total - $row->amount_paid;
                    $due_html = '<strong>' . __('lang_v1.purchase') . ':</strong> <span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';

                    if (!empty($row->return_exists)) {
                        $return_due = $row->amount_return - $row->return_paid;
                        $due_html .= '<br><strong>' . __('lang_v1.purchase_return') . ':</strong> <a href="' . action("TransactionPaymentController@show", [$row->return_transaction_id]) . '" class="view_purchase_return_payment_modal no-print"><span class="display_currency purchase_return" data-currency_symbol="true" data-orig-value="' . $return_due . '">' . $return_due . '</span></a><span class="display_currency print_section" data-currency_symbol="true">' . $return_due . '</span>';
                    }
                    return $due_html;
                })
                ->addColumn('payment_method', function ($row) {
                    $html = '';
                    if ($row->method == 'bank_transfer') {
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                            $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';
                        }
                    } else {
                        $html .= ucfirst($row->method);
                    }

                    return $html;
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("purchase.view")) {
                            return  action('PurchaseController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns(['final_total', 'action', 'payment_due', 'payment_status', 'status', 'ref_no', 'payment_method'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        
        $units = Unit::getPropertyUnitDropdown($business_id, false, false, 'show_in_add_project_unit');
        
        $orderStatuses = ['outright_purchase' => __('property::lang.outright_purchase'), 'mortgaged' => __('property::lang.mortgaged')];
        $statuses = Property::statusesDropdown();

        $taxes = TaxRate::where('business_id', $business_id)
            ->get();


        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = ContactGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types(null, false, true);
        // no need thease methods in purchase page
        unset($payment_types['card']);
        unset($payment_types['credit_sale']);
        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);
        $type = 'supplier'; //contact type /used in quick add contact
        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('pos_create_data')->first();
        if (!empty($temp_data)) {
            $temp_data = json_decode($temp_data->pos_create_data); //name by mistake it is purchase
        }
        if (!request()->session()->get('business.popup_load_save_data')) {
            $temp_data = [];
        }


        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $is_petro_enable =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');

        $purchase_no = $this->businessUtil->getFormNumber('property_purchase');

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');
            
        $unitquery = Unit::where('business_id', $business_id)->where('is_property', 1)->first();    
        if(!empty($unitquery))
        {
            $unitid = $unitquery->id;
        }
        else
        {
            $unitid = '';
        }
        //echo $unitid;die;
        //print_r($units);die;


        return view('property::purchase.index')
            ->with(compact(
                'business_locations',
                'suppliers',
                'units',
                'statuses',
                'orderStatuses',
                'purchase_no',
                'is_petro_enable',
                'tanks',
                'contact_id',
                'type',
                'temp_data',
                'taxes',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'accounts',
                'bank_group_accounts',
                'unitid'
            ));
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
        if (!auth()->user()->can('property.purchase.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            DB::table('temp_data')->where('business_id', $business_id)->update(['pos_create_data' => '']);

            //Check if subscribed or not
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
            }
            $transaction_data = $request->only(['invoice_no', 'deed_date', 'deed_no',  'status', 'contact_id', 'location_id',  'final_total', 'exchange_rate', 'pay_term_number', 'pay_term_type']);

            $exchange_rate = $transaction_data['exchange_rate'];

            $request->validate([
                'status' => 'required',
                'contact_id' => 'required',
                'location_id' => 'required',
                'final_total' => 'required',
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $user_id = $request->session()->get('user.id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            //Update business exchange rate.
            Business::update_business($business_id, ['p_exchange_rate' => ($transaction_data['exchange_rate'])]);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;


            $transaction_data['tax_amount'] = 0;
            $transaction_data['shipping_charges'] = 0;
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;

            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'property_purchase';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['store_id'] = null;
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['deed_date']);
            $transaction_data['deed_date'] = $this->productUtil->uf_date($transaction_data['deed_date']);

            //upload document
            $transaction_data['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');

            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('purchase');
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber('purchase', $ref_count);
            }

            $transaction = Transaction::create($transaction_data);

            $property_data = [
                'business_id' => $business_id,
                'location_id' => $transaction_data['location_id'],
                'name' => $request->property_name,
                'supplier_id' => $transaction_data['contact_id'],
                'status' => 'open',
                'extent' => $request->property_extent,
                'unit_id' => $request->unit_id,
                'transaction_id' => $transaction->id,
                'added_by' => Auth::user()->id,
            ];
            Property::create($property_data);


            $payments = $request->input('payment');

            //Add Purchase payments
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);

            //update payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_add_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('property/purchases')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('property.purchase.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('PurchaseController@index'));
        }


        $purchase = Transaction::where('transactions.business_id', $business_id)
            ->join('properties', 'transactions.id', 'properties.transaction_id')
            ->where('transactions.id', $id)
            ->select(
                'properties.extent',
                'properties.name',
                'properties.unit_id',
                'properties.status',
                'transactions.status as purchase_status',
                'transactions.id',
                'transactions.final_total',
                'transactions.pay_term_number',
                'transactions.pay_term_type',
                'transactions.contact_id',
                'transactions.deed_no',
                'transactions.deed_date',
                'transactions.invoice_no',
                'transactions.location_id',

            )
            ->with(
                'contact',
                'location'
            )
            ->first();
        $payment_lines = $this->transactionUtil->getPaymentDetails($id);
        //If no payment lines found then add dummy payment line.
        if (empty($payment_lines)) {
            $payment_lines[] = $this->dummyPaymentLine;
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $units = Unit::getPropertyUnitDropdown($business_id, false, false, 'show_in_add_project_unit');
        $orderStatuses = ['outright_purchase' => __('property::lang.outright_purchase'), 'mortgaged' => __('property::lang.mortgaged')];
        $statuses = Property::statusesDropdown();

        $taxes = TaxRate::where('business_id', $business_id)
            ->get();


        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $customer_groups = ContactGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types();
        // no need thease methods in purchase page
        unset($payment_types['card']);
        unset($payment_types['credit_sale']);
        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);
        $type = 'supplier'; //contact type /used in quick add contact


        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $is_petro_enable =  $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');

        $purchase_no = $this->businessUtil->getFormNumber('purchase');

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');


        return view('property::purchase.edit')
            ->with(compact(
                'purchase',
                'payment_lines',
                'business_locations',
                'suppliers',
                'units',
                'statuses',
                'orderStatuses',
                'purchase_no',
                'is_petro_enable',
                'tanks',
                'contact_id',
                'type',
                'taxes',
                'currency_details',
                'default_purchase_status',
                'customer_groups',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'accounts',
                'bank_group_accounts'

            ));
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
        if (!auth()->user()->can('property.purchase.edit')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $transaction = Transaction::findOrFail($id);

            //Validate document size
            $request->validate([
                'document' => 'file|max:' . (config('constants.document_size_limit') / 1000)
            ]);

            $transaction = Transaction::findOrFail($id);
            $before_status = $transaction->status;

            $business_id = request()->session()->get('user.business_id');
            $enable_product_editing = $request->session()->get('business.enable_editing_product_from_purchase');

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            $update_data = $request->only(['invoice_no', 'deed_date', 'deed_no',  'status', 'contact_id', 'location_id',  'final_total', 'exchange_rate', 'pay_term_number', 'pay_term_type']);

            $exchange_rate = $update_data['exchange_rate'];


            $update_data['transaction_date'] = $this->productUtil->uf_date($update_data['deed_date'], false);
            $update_data['deed_date'] = $this->productUtil->uf_date($update_data['deed_date'], false);

            //unformat input values
            $update_data['total_before_tax'] = $this->productUtil->num_uf($update_data['final_total'], $currency_details) * $exchange_rate;



            $update_data['tax_amount'] = 0;
            $update_data['shipping_charges'] = 0;
            $update_data['final_total'] = $this->productUtil->num_uf($update_data['final_total'], $currency_details) * $exchange_rate;
            //unformat input values ends

            //upload document
            $document_name = $this->transactionUtil->uploadFile($request, 'document', 'documents');
            if (!empty($document_name)) {
                $update_data['document'] = $document_name;
            }

            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            $property_data = [
                'location_id' => $update_data['location_id'],
                'name' => $request->property_name,
                'supplier_id' => $update_data['contact_id'],
                'status' => 'open',
                'extent' => $request->property_extent,
                'unit_id' => $request->unit_id
            ];
            Property::where('transaction_id', $id)->update($property_data);


            //Add Purchase payments
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $request->input('payment'));

            //Update transaction payment status
            $this->transactionUtil->updatePaymentStatus($transaction->id);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_update_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return back()->with('status', $output);
        }

        return redirect('property/purchases')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('property.purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                $transaction = Transaction::where('id', $id)
                    ->where('business_id', $business_id)
                    ->with(['purchase_lines'])
                    ->first();

                if ($transaction->payment_status == 'paid' || $transaction->payment_status == 'partial') {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.unable_to_delete_purchase_as_payment_exist')
                    ];
                    return $output;
                }

                DB::beginTransaction();

                Property::where('transaction_id', $id)->delete();
                $transaction_id = $transaction->id;
                //Delete Transaction
                $transaction->delete();
                Transaction::withTrashed()->where('id', $transaction->id)->update(['deleted_by' => Auth::user()->id]);
                //Delete account transactions
                AccountTransaction::where('transaction_id', $transaction_id)->delete();
                ContactLedger::where('transaction_id', $transaction->id)->delete();

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.purchase_delete_success')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

    /**
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = Contact::where('business_id', $business_id)->where('active', 1);

            $selected_contacts = User::isSelectedContacts($user_id);
            if ($selected_contacts) {
                $query->join('user_contact_access AS uca', 'contacts.id', 'uca.contact_id')
                    ->where('uca.user_id', $user_id);
            }
            $suppliers = $query->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('supplier_business_name', 'like', '%' . $term . '%')
                    ->orWhere('contacts.contact_id', 'like', '%' . $term . '%');
            })
                ->select('contacts.id', 'name as text', 'supplier_business_name as business_name', 'contact_id', 'contacts.pay_term_type', 'contacts.pay_term_number')
                ->onlySuppliers()
                ->get();
            return json_encode($suppliers);
        }
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = TaxRate::where('business_id', $business_id)
                ->pluck('name', 'id');
            $purchase = Transaction::where('business_id', $business_id)
                ->where('id', $id)
                ->with(
                    'contact',
                    'purchase_lines',
                    'purchase_lines.product',
                    'purchase_lines.variations',
                    'purchase_lines.variations.product_variation',
                    'location',
                    'payment_lines'
                )
                ->first();
            $payment_methods = $this->productUtil->payment_types();

            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods'))->render();
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Update purchase status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        if (!auth()->user()->can('property.purchase.edit')) {
            abort(403, 'Unauthorized action.');
        }
        //Check if the transaction can be edited or not.
        $edit_days = request()->session()->get('business.transaction_edit_days');
        if (!$this->transactionUtil->canBeEdited($request->input('purchase_id'), $edit_days)) {
            return [
                'success' => 0,
                'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])
            ];
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = Transaction::where('business_id', $business_id)
                ->where('type', 'purchase')
                ->with(['purchase_lines'])
                ->findOrFail($request->input('purchase_id'));

            $before_status = $transaction->status;


            $update_data['status'] = $request->input('status');


            DB::beginTransaction();

            //update transaction
            $transaction->update($update_data);

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            foreach ($transaction->purchase_lines as $purchase_line) {
                $this->productUtil->updateProductStock($before_status, $transaction, $purchase_line->product_id, $purchase_line->variation_id, $purchase_line->quantity, $purchase_line->quantity, $currency_details);
            }

            //Update mapping of purchase & Sell.
            $this->transactionUtil->adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, null);

            //Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_update_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }


    /**
     * Returns the HTML row for a payment in Purchase
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getPaymentRow(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $location_id = $request->input('location_id');
        $row_index = $request->input('row_index');
        $removable = true;
        $payment_types = $this->productUtil->payment_types($location_id);

        $payment_line = $this->dummyPaymentLine;

        //Accounts
        $accounts = [];
        if ($this->moduleUtil->isModuleEnabled('account')) {
            $accounts = Account::forDropdown($business_id, true, false);
        }

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');

        return view('purchase.partials.payment_row')
            ->with(compact('payment_types', 'row_index', 'removable', 'payment_line', 'accounts', 'bank_group_accounts'));
    }
}
