<?php

namespace Modules\Ezyboat\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\BusinessLocation;
use App\Contact;
use App\Product;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Ezyboat\Entities\BoatTrip;
use Modules\Ezyboat\Entities\Crew;
use Modules\Ezyboat\Entities\Driver;
use Modules\Ezyboat\Entities\Fleet;
use Modules\Ezyboat\Entities\Helper;
use Modules\Ezyboat\Entities\IncomeSetting;
use Modules\Ezyboat\Entities\Route;
use Modules\Ezyboat\Entities\RouteOperation;
use Modules\Ezyboat\Entities\RouteProduct;
use Yajra\DataTables\Facades\DataTables;

class BoatOperationController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil =  $businessUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'account_id' => ''
        ];
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'ezyboat_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('drivers', 'route_operations.driver_id', 'drivers.id')
                ->leftjoin('helpers', 'route_operations.helper_id', 'helpers.id')
                ->leftjoin('route_products', 'route_operations.product_id', 'route_products.id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('transactions.type', 'route_operation')
                ->where('route_operations.business_id', $business_id)
                ->select([
                    'route_operations.*',
                    'drivers.driver_name',
                    'helpers.helper_name',
                    'routes.route_name',
                    'fleets.vehicle_number',
                    'contacts.name as contact_name',
                    'route_products.name as product_name',
                    'transactions.id as t_id',
                    'transactions.payment_status',
                    'transaction_payments.method',
                    'transaction_payments.account_id',
                    'business_locations.name as location_name',
                ])->groupBy('route_operations.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->location_id)) {
                $route_operations->where('route_operations.location_id', request()->location_id);
            }
            if (!empty(request()->contact_id)) {
                $route_operations->where('route_operations.contact_id', request()->contact_id);
            }
            if (!empty(request()->route_id)) {
                $route_operations->where('route_operations.route_id', request()->route_id);
            }
            if (!empty(request()->vehicle_no)) {
                $route_operations->where('fleets.vehicle_number', request()->vehicle_no);
            }
            if (!empty(request()->driver_id)) {
                $route_operations->where('route_operations.driver_id', request()->driver_id);
            }
            if (!empty(request()->helper_id)) {
                $route_operations->where('route_operations.helper_id', request()->helper_id);
            }
            if (!empty(request()->payment_status)) {
                $route_operations->where('transactions.payment_status', request()->payment_status);
            }
            if (!empty(request()->payment_method)) {
                $route_operations->where('transaction_payments.method', request()->payment_method);
            }

            return DataTables::of($route_operations)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Ezyboat\Http\Controllers\BoatOperationController@edit', [$row->t_id]) . '" class=""><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ezyboat\Http\Controllers\BoatOperationController@destroy', [$row->t_id]) . '" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

                        if ($row->payment_status != 'paid') {
                            $html .= '<li class="divider"></li>';
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->t_id]) . '" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';
                        }
                        return $html;
                    }
                )
                ->editColumn('date_of_operation', '{{@format_date($date_of_operation)}}')
                ->editColumn('amount', '{{@num_format($amount)}}')
                ->editColumn('driver_incentive', '{{@num_format($driver_incentive)}}')
                ->editColumn('helper_incentive', '{{@num_format($helper_incentive)}}')
                ->editColumn('distance', '{{@num_format($distance)}}')
                ->editColumn('payment_status', function ($row) {
                    $payment_status = Transaction::getPaymentStatus($row);
                    return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                })
                ->editColumn('method', function ($row) {
                    $html = '';
                    if ($row->payment_status == 'due') {
                        return '';
                    }
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
                ->editColumn('qty', '{{@format_quantity($qty)}}')

                ->removeColumn('id')
                ->rawColumns(['action', 'payment_status', 'method'])
                ->make(true);
        }


        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $fleets_query = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        $vehicle_types = $fleets_query->pluck('vehicle_type', 'vehicle_type');
        $vehicle_brands = $fleets_query->pluck('vehicle_brand', 'vehicle_brand');
        $vehicle_models = $fleets_query->pluck('vehicle_model', 'vehicle_model');
        $contacts = Contact::where('business_id', $business_id)->pluck('name', 'id');
        $products = RouteProduct::where('business_id', $business_id)->pluck('name', 'id');
        $drivers = Crew::where('business_id', $business_id)->pluck('crew_name', 'id');
        $helpers = IncomeSetting::where('business_id', $business_id)->pluck('income_name', 'id');
        $routes = BoatTrip::where('business_id', $business_id)->pluck('trip_name', 'id');
        $fleets = Fleet::where('business_id', $business_id)->pluck('code_for_vehicle', 'id');
        $payment_status = ['partial' => 'Partial', 'due' => 'Due', 'paid' => 'Paid'];
        $payment_methods = $this->productUtil->payment_types(null, false);

        return view('ezyboat::boat_operations.index')->with(compact(
            'business_locations',
            'vehicle_numbers',
            'contacts',
            'products',
            'drivers',
            'helpers',
            'routes',
            'fleets',
            'payment_status',
            'payment_methods'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $products = RouteProduct::where('business_id', $business_id)->pluck('name', 'id');
        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id');
        $payment_line = $this->dummyPaymentLine;
        $payment_types =  $this->productUtil->payment_types(null, false, false, false, true);

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');

       
        $invoice_number = $this->commonUtil->getRouteOperationInvoiceNumber($business_id);

        return view('ezyboat::boat_operations.create')->with(compact(
            'payment_line',
            'payment_types',
            'bank_group_accounts',
            'business_locations',
            'customers',
            'invoice_number',
            'routes',
            'products',
            'fleets',
            'drivers',
            'helpers'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $user_id = $request->session()->get('user.id');

            $date_of_operation = $this->commonUtil->uf_date($request->date_of_operation);
            $data = [
                'date_of_operation' => $date_of_operation,
                'business_id' => $business_id,
                'location_id' => $request->location_id,
                'contact_id' => $request->contact_id,
                'route_id' => $request->route_id,
                'fleet_id' => $request->fleet_id,
                'invoice_no' => $request->invoice_no,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'driver_id' => $request->driver_id,
                'helper_id' => $request->helper_id,
                'order_number' => $request->order_number,
                'order_date' => !empty($request->order_date) ?  $this->commonUtil->uf_date($request->order_date) : null,
                'distance' => $this->commonUtil->num_uf($request->distance),
                'amount' => $this->commonUtil->num_uf($request->amount),
                'driver_incentive' => $this->commonUtil->num_uf($request->driver_incentive),
                'helper_incentive' => $this->commonUtil->num_uf($request->helper_incentive),
                'created_by' => $user_id
            ];

            DB::beginTransaction();
            $route_operation = RouteOperation::create($data);


            $transaction_data = $request->only(['invoice_no', 'ref_no', 'status', 'fleet_id', 'contact_id', 'total_before_tax', 'location_id', 'discount_type', 'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type']);
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'route_operation';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['store_id'] = $request->input('store_id');
            $transaction_data['transaction_date'] = $date_of_operation;
            $transaction = Transaction::create($transaction_data);

            $route_operation->transaction_id = $transaction->id;
            $route_operation->save();

            $payments = $request->payment;
            //add payment for transaction
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->to('/fleet-management/boat-operation')->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('ezyboat::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($transaction_id)
    {
        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $products = RouteProduct::where('business_id', $business_id)->pluck('name', 'id');
        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id');
        $payment_line = $this->dummyPaymentLine;
        $payment_types =  $this->productUtil->payment_types();

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');

        $transaction = Transaction::where('transactions.id', $transaction_id)
            ->with('payment_lines', 'route_operation')
            ->first();

        return view('ezyboat::boat_operations.edit')->with(compact(
            'payment_line',
            'payment_types',
            'transaction',
            'bank_group_accounts',
            'business_locations',
            'customers',
            'routes',
            'products',
            'fleets',
            'drivers',
            'helpers'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $transaction_id)
    {
        try {
            $date_of_operation = $this->commonUtil->uf_date($request->date_of_operation);
            $data = [
                'date_of_operation' => $date_of_operation,
                'location_id' => $request->location_id,
                'contact_id' => $request->contact_id,
                'route_id' => $request->route_id,
                'fleet_id' => $request->fleet_id,
                'invoice_no' => $request->invoice_no,
                'order_number' => $request->order_number,
                'order_date' => !empty($request->order_date) ?  $this->commonUtil->uf_date($request->order_date) : null,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'driver_id' => $request->driver_id,
                'helper_id' => $request->helper_id,
                'distance' => $this->commonUtil->num_uf($request->distance),
                'amount' => $this->commonUtil->num_uf($request->amount),
                'driver_incentive' => $this->commonUtil->num_uf($request->driver_incentive),
                'helper_incentive' => $this->commonUtil->num_uf($request->helper_incentive)
            ];

            DB::beginTransaction();
            $route_operation = RouteOperation::where('transaction_id', $transaction_id)->update($data);


            $transaction_data = $request->only(['invoice_no', 'ref_no', 'status', 'contact_id', 'total_before_tax', 'location_id', 'discount_type', 'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type']);
            $transaction_data['transaction_date'] = $date_of_operation;
            Transaction::where('id', $transaction_id)->update($transaction_data);

            $payments = $request->payment;

            $transaction = Transaction::find($transaction_id);
            //add payment for transaction
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);


            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->to('/fleet-management/boat-operation')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {

            Transaction::where('id', $id)->delete();
            RouteOperation::where('transaction_id', $id)->delete();
            TransactionPayment::where('transaction_id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

}
