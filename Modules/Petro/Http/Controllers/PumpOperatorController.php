<?php

namespace Modules\Petro\Http\Controllers;

use App\Account;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use App\Notifications\CustomerNotification;
use App\Product;
use App\System;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Petro\Entities\PumpOperator;
use App\Utils\BusinessUtil;
use App\Utils\Util;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperatorPayment;
use Spatie\Permission\Models\Role;

class PumpOperatorController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        
        $business_id =  Auth::user()->business_id;

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            if (request()->ajax()) {
                $query = PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->leftjoin('settlements', 'pump_operators.id', 'settlements.pump_operator_id')
                    ->where('pump_operators.business_id', $business_id)
                    ->select([
                        'pump_operators.*',
                        'settlements.settlement_no as st_no',
                        'pump_operators.id as pump_operator_id',
                        'business_locations.name as location_name',
                    ])->groupBy('pump_operators.id');

                if (!empty(request()->location_id)) {
                    $query->where('pump_operators.location_id', request()->location_id);
                }
                if (!empty(request()->pump_operator)) {
                    $query->where('pump_operators.id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('settlements.settlement_no', request()->settlement_no);
                }
                if (!empty(request()->status)) {
                    if (request()->status == 'active') {
                        $query->where('pump_operators.active', 1);
                    } else {
                        $query->where('pump_operators.active', 0);
                    }
                }
                if (!empty(request()->type)) {
                }

                $start_date = request()->start_date;
                $end_date = request()->end_date;
                $business_details = Business::find($business_id);


                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $business_id = session()->get('user.business_id');
                            $pay_excess_commission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pay_excess_commission');
                            $recover_shortage = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'recover_shortage');
                            $pump_operator_ledger = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_ledger');

                            $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">
                    
                            <li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . '"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>
                            <li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@edit', [$row->id]) . '" class="edit_contact_button"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                            if (auth()->user()->can('pum_operator.active_inactive')) {
                                $html .= '<li class="divider"></li>';
                                if (!$row->active) {
                                    $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@toggleActivate', [$row->id]) . '" class="toggle_active_button"><i class="fa fa-check"></i> ' . __("lang_v1.activate") . '</a></li>';
                                } else {
                                    $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@toggleActivate', [$row->id]) . '" class="toggle_active_button"><i class="fa fa-times"></i> ' . __("lang_v1.deactivate") . '</a></li>';
                                }
                            }

                            $html .= '<li class="divider"></li>';
                            if ($pay_excess_commission) {
                                $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\ExcessComissionController@create', ['pump_operator_id' => $row->id]) . '" class="edit_contact_button"> ' . __("petro::lang.pay_excess_and_commission") . '</a></li>';
                            }
                            if ($recover_shortage) {
                                $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\RecoverShortageController@create', ['pump_operator_id' => $row->id]) . '" class="edit_contact_button"> ' . __("petro::lang.recover_shortages") . '</a></li>';
                            }
                            $html .= '<li class="divider"></li>
                            <li>
                                <a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . "?view=contact_info" . '">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    ' . __("contact.contact_info", ["contact" => __("contact.contact")]) . '
                                </a>
                            </li>
                            <li>';

                            if ($pump_operator_ledger) {
                                $html .= '  <a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . "?view=ledger" . '">
                                    <i class="fa fa-anchor" aria-hidden="true"></i>
                                    ' . __("lang_v1.ledger") . '
                                </a>
                            </li>';
                            }

                            $html .= '<li>
                                <a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . "?view=documents_and_notes" . '">
                                    <i class="fa fa-paperclip" aria-hidden="true"></i>
                                     ' . __("lang_v1.documents_and_notes") . '
                                </a>
                            </li>
                       
                        </ul></div>';

                            return $html;
                        }
                    )
                    ->addColumn(
                        'current_status',
                        function ($row) use ($start_date, $end_date, $business_details) {
                            $ledger_details = $this->__getLedgerDetails($row->pump_operator_id, $start_date, $end_date);
                            $balance = $ledger_details['balance_due'];
                            $color_class = '';
                            if($balance > 0 ){
                                $color_class = 'text-red';
                            }
                            return  '<span class="display_currency current_status '.$color_class.'" data-orig-value="' . $balance . '" data-currency_symbol = false>' . $this->productUtil->num_f($balance, false, $business_details, false) . '</span>';
                        }
                    )
                    ->addColumn(
                        'pump_no',
                        ''
                    )
                    ->addColumn(
                        'settlement_no',
                        ''
                    )
                    ->addColumn(
                        'sold_fuel_qty',
                        function ($row) use ($business_details, $start_date, $end_date) {
                            $qty =    PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                                ->leftjoin('transactions', 'pump_operators.id', 'transactions.pump_operator_id')
                                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                                ->leftjoin('categories', 'products.category_id', 'categories.id')
                                ->where('transactions.type', 'sell')
                                ->where('categories.name', 'Fuel')
                                ->where('transactions.transaction_date', '>=', $start_date)
                                ->where('transactions.transaction_date', '<=', $end_date)
                                ->where('pump_operators.id', $row->pump_operator_id)
                                ->select([
                                    'pump_operators.*',
                                    'business_locations.name as location_name',
                                    DB::raw('SUM(transaction_sell_lines.quantity) as sold_fuel_qty')
                                ])->first();

                            if (empty($qty->sold_fuel_qty)) {
                                return $this->productUtil->num_f(0, false, $business_details, true);
                            }
                            return  '<span class="display_currency sold_fuel_qty" data-orig-value="' . $qty->sold_fuel_qty . '" data-currency_symbol = true>' . $this->productUtil->num_f($qty->sold_fuel_qty, false, $business_details, true) . '</span>';
                        }
                    )
                    ->addColumn(
                        'sale_amount_fuel',
                        function ($row) use ($business_details, $start_date, $end_date) {
                            $amount =    PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                                ->leftjoin('transactions', 'pump_operators.id', 'transactions.pump_operator_id')
                                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                                ->leftjoin('categories', 'products.category_id', 'categories.id')
                                ->where('transactions.type', 'sell')
                                ->where('categories.name', 'Fuel')
                                ->where('transactions.transaction_date', '>=', $start_date)
                                ->where('transactions.transaction_date', '<=', $end_date)
                                ->where('pump_operators.id', $row->pump_operator_id)
                                ->select([
                                    'pump_operators.*',
                                    'business_locations.name as location_name',
                                    DB::raw('SUM(transaction_sell_lines.quantity * unit_price) as sale_amount_fuel')
                                ])->first();
                            return  '<span class="display_currency sale_amount_fuel" data-orig-value="' . $amount->sale_amount_fuel . '" data-currency_symbol = true>' . $this->productUtil->num_f($amount->sale_amount_fuel, false, $business_details, false) . '</span>';
                        }
                    )
                    ->editColumn(
                        'excess_amount',
                        function ($row) use ($business_details, $start_date, $end_date) {
                            $total_excess = $this->transactionUtil->getPumpOperatorExcessOrShortageByDate($row->pump_operator_id, 'excess', $start_date, $end_date);
                            return  '<span class="display_currency excess_amount" data-orig-value="' .  $total_excess . '" data-currency_symbol = true>' . $this->productUtil->num_f($total_excess, false, $business_details, false) . '</span>';
                        }
                    )
                    ->editColumn(
                        'short_amount',
                        function ($row) use ($business_details, $start_date, $end_date) {
                            $total_shortage = $this->transactionUtil->getPumpOperatorExcessOrShortageByDate($row->pump_operator_id, 'shortage', $start_date, $end_date);

                            return  '<span class="display_currency short_amount" data-orig-value="' . $total_shortage . '" data-currency_symbol = true>' . $this->productUtil->num_f($total_shortage, false, $business_details, false) . '</span>';
                        }
                    )
                    ->editColumn(
                        'commission_rate',
                        function ($row) use ($business_details) {
                            return  '<span class="display_currency commission_ap" data-orig-value="' . $row->commission_ap . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->commission_ap, false, $business_details, false) . '</span>';
                        }
                    )
                    ->addColumn(
                        'commission_amount',
                        function ($row) use ($business_details, $start_date, $end_date) {
                            $amount =    PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                                ->leftjoin('transactions', 'pump_operators.id', 'transactions.pump_operator_id')
                                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                                ->leftjoin('categories', 'products.category_id', 'categories.id')
                                ->where('transactions.type', 'sell')
                                ->where('categories.name', 'Fuel')
                                ->where('transactions.transaction_date', '>=', $start_date)
                                ->where('transactions.transaction_date', '<=', $end_date)
                                ->where('pump_operators.id', $row->pump_operator_id)
                                ->select([
                                    'pump_operators.*',
                                    'business_locations.name as location_name',
                                    DB::raw('SUM(transaction_sell_lines.quantity * unit_price) as sale_amount_fuel'),
                                    DB::raw('SUM(transaction_sell_lines.quantity) as sale_qty_fuel')
                                ])->first();
                            if ($row->commission_type == 'fixed') {
                                return  '<span class="display_currency commission_amount" data-orig-value="' . $amount->sale_qty_fuel *  $row->commission_ap . '" data-currency_symbol = true>' . $this->productUtil->num_f($amount->sale_qty_fuel *  $row->commission_ap, false, $business_details, true) . '</span>';
                            }
                            if ($row->commission_type == 'percentage') {
                                return  '<span class="display_currency commission_amount" data-orig-value="' . ($amount->sale_amount_fuel * $row->commission_ap) / 100 . '" data-currency_symbol = true>' . $this->productUtil->num_f(($amount->sale_amount_fuel * $row->commission_ap) / 100, false, $business_details, true) . '</span>';
                            }
                            return  '<span class="display_currency commission_amount" data-orig-value="0" data-currency_symbol = true>' . $this->productUtil->num_f(0, false, $business_details, false) . '</span>';
                        }
                    )

                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action', 'sold_fuel_qty', 'sale_amount_fuel', 'excess_amount', 'short_amount', 'commission_rate', 'commission_amount', 'current_status'])
                    ->make(true);
            }
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        // $pumps = Pump::leftjoin('pump_operator_assignments', function ($join) {
        //     $join->on('pumps.id', 'pump_operator_assignments.pump_id')->whereDate('date_and_time', date('Y-m-d'));
        // })->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
        //     ->where('pumps.business_id', $business_id)
        //     ->select('pumps.*', 'pump_operator_assignments.pump_operator_id', 'pump_operators.name as pumper_name')
        //     ->orderBy('pumps.id')
        //     ->get();
            $pumps = collect(DB::select("SELECT pumps.*,pump_operator_assignments.status as status, pump_operator_assignments.pump_operator_id, pump_operators.name AS pumper_name FROM pumps LEFT JOIN pump_operator_assignments ON pumps.id = pump_operator_assignments.pump_id AND DATE(date_and_time) = '2022-01-07' AND ( pump_operator_assignments.pump_id, pump_operator_assignments.created_at ) IN( SELECT pump_id, MAX(created_at) FROM pump_operator_assignments WHERE DATE(date_and_time) = '2022-01-07' GROUP BY pump_id ) LEFT JOIN pump_operators ON pump_operator_assignments.pump_operator_id = pump_operators.id WHERE pumps.business_id = 2 ORDER BY pumps.id ASC"));
        // return $pumps;
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        $payment_types = $this->productUtil->payment_types($default_location);
        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')->where('products.business_id', $business_id)->where('categories.name', 'Fuel')->pluck('products.name', 'products.id');
        $settlement_nos = [];

        $day_entries_query = PumperDayEntry::leftjoin('pump_operators', 'pumper_day_entries.pump_operator_id', 'pump_operators.id')
            ->leftjoin('pumps', 'pumper_day_entries.pump_id', 'pumps.id')
            ->whereDate('pumper_day_entries.date', date('Y-m-d'))
            ->where('pumper_day_entries.business_id', $business_id)
            ->select('pump_operators.name', 'pumper_day_entries.*', 'pumps.pump_name');

        $day_entries = $day_entries_query->get();

        $today_pumps = implode(', ', $day_entries->pluck('pump_name')->toArray());

        $payments = PumpOperatorPayment::leftjoin('pump_operators', 'pump_operator_payments.pump_operator_id', 'pump_operators.id')
            ->whereDate('date_and_time', date('Y-m-d'))->select(
                DB::raw('SUM(IF(payment_type="cash", payment_amount, 0)) as cash'),
                DB::raw('SUM(IF(payment_type="card", payment_amount, 0)) as card'),
                DB::raw('SUM(IF(payment_type="cheque", payment_amount, 0)) as cheque'),
                DB::raw('SUM(IF(payment_type="credit", payment_amount, 0)) as credit'),
                DB::raw('SUM(payment_amount) as total')
            );
        $payments = $payments->first();

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        return view('petro::pump_operators.index')->with(compact(
            'business_locations',
            'pump_operators',
            'settlement_nos',
            'message',
            'payment_types',
            'pumps',
            'day_entries',
            'today_pumps',
            'tanks',
            'products',
            'payments'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);

        $commission_type_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'commission_type');
        $pump_operator_dashboard = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard');

        return view('petro::pump_operators.create')->with(compact('locations', 'commission_type_permission', 'pump_operator_dashboard'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'location_id' => 'required',
            'email' => 'required|unique:users',
            'cnic' => 'required',
            'dob' => 'required',
            'commission_type' => 'required',
            'mobile' => 'required',
            'username' => 'required|unique:users'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }
        $business_id = request()->session()->get('business.id');

        try {
            //Check if subscribed or not, then check for users quota
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            } elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
            }

            $data = array(
                'business_id' => $business_id,
                'name' => $request->name,
                'address' => $request->address,
                'location_id' => $request->location_id,
                'cnic' => $request->cnic,
                'dob' => Carbon::parse($request->dob)->format('Y-m-d'),
                'commission_type' => $request->commission_type,
                'commission_ap' => !empty($request->commission_ap) ? $request->commission_ap : 0.00,
                'mobile' => $request->mobile,
                'landline' => $request->landline,
                'status' => 1
            );

            if (!empty($request->input('opening_balance'))) {
                if ($request->input('opening_balance_type') == 'shortage') {
                    $data['short_amount'] = $request->input('opening_balance');
                }
                if ($request->input('opening_balance_type') == 'excess') {
                    $data['excess_amount'] = $request->input('opening_balance');
                }
            }

            DB::beginTransaction();
            $pump_operator = PumpOperator::create($data);


            $this->createUser($request, $pump_operator);

            //Add opening balance
            if (!empty($request->input('opening_balance'))) {
                $this->transactionUtil->createOpeningBalanceTransactionForPumpOperator($business_id, $pump_operator->id, $request->input('opening_balance'), $request->input('opening_balance_type'), $request->location_id);
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_operator_add_success')
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

    public function createUser($request, $pump_operator)
    {
        $business_id = request()->session()->get('business.id');
        $pump_operator_data = array(
            'business_id' => $business_id,
            'surname' => '',
            'first_name' => $request->name,
            'last_name' => '',
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'contact_number' => $request->mobile,
            'address' => $request->address,
            'is_pump_operator' => 1,
            'pump_operator_id' => $pump_operator->id,
            'pump_operator_passcode' => $request->password
        );

        $user = User::create($pump_operator_data);
        $role = Role::where('name', 'Pump Operator#' . $business_id)->first();

        if (empty($role)) {
            $role = Role::create([
                'name' => 'Pump Operator#' . $business_id,
                'business_id' => $business_id,
                'is_service_staff' => 0
            ]);
            $role->givePermissionTo('pump_operator.dashboard');
        }
        $user->assignRole($role->name);

        return true;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id =  Auth::user()->business_id;
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $pump_operator = PumpOperator::findOrFail($id);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'contact_info';
        }

        $pump_operator_ledger_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_ledger');

        return view('petro::pump_operators.show')
            ->with(compact('pump_operator', 'pump_operators', 'business_locations', 'view_type', 'pump_operator_ledger_permission'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);

        $pump_operator = PumpOperator::findOrFail($id);
        $user = User::where('business_id', $business_id)->where('pump_operator_id', $id)->first();
        $pump_operator_dashboard = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard');

        return view('petro::pump_operators.edit')->with(compact('locations', 'pump_operator', 'user', 'pump_operator_dashboard'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $data = array(
                'business_id' => $business_id,
                'name' => $request->name,
                'address' => $request->address,
                'location_id' => $request->location_id,
                'commission_type' => $request->commission_type,
                'commission_ap' => $request->commission_ap,
                'mobile' => $request->mobile,
                'landline' => $request->landline,
                'status' => 1
            );

            PumpOperator::where('id', $id)->update($data);
            $pump_operator = PumpOperator::findOrFail($id);

            $user = User::where('pump_operator_id', $id)->where('business_id', $business_id)->first();
            if (empty($user)) {
                $this->createUser($request, $pump_operator);
            } else {
                $user->email = $request->email;
                if (!empty($request->password)) {
                    $user->password = Hash::make($request->password);
                    $user->pump_operator_passcode = $request->password;
                }
                $user->save();
            }

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_operator_update_success')
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
     * @return Response
     */
    public function destroy()
    {
    }
    /**
     * Import Operators
     * @return Response
     */
    public function importPumps()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('petro::pump_operators.import_operators')->with(compact('business_locations'));
    }
    /**
     * Import Operators saves
     * @return Response
     */
    public function saveImport(Request $request)
    {
        $notAllowed = $this->productUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }
        $business_id = request()->session()->get('business.id');
        $location_id =   $request->location_id;
        $type =   $request->commission_type;

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('pumps_csv')) {
                $file = $request->file('pumps_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);

                $row_no = 0;
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {

                    $pump_operator = [];
                    $pump_operator['business_id'] = $business_id;
                    $pump_operator['location_id'] = $location_id;
                    $pump_operator['commission_type'] = $type;

                    //Check if any column is missing
                    if (count($value) < 5) {
                        $is_valid =  false;
                        $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                        break;
                    }

                    $name = strtolower(trim($value[0]));
                    if ($name) {
                        $pump_operator['name'] = $name;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for pump operator name in row no. $row_no";
                        break;
                    }

                    $address = strtolower(trim($value[1]));
                    if ($address) {
                        $pump_operator['address'] = $address;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for address in row no. $row_no";
                        break;
                    }

                    $mobile = strtolower(trim($value[2]));
                    if ($mobile) {
                        $pump_operator['mobile'] = $mobile;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for mobile in row no. $row_no";
                        break;
                    }

                    $landline = strtolower(trim($value[3]));
                    if ($landline) {
                        $pump_operator['landline'] = $landline;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for landline in row no. $row_no";
                        break;
                    }

                    $dob = strtolower(trim($value[4]));
                    if ($dob) {
                        $pump_operator['dob'] = Carbon::parse($dob)->format('Y-m-d');
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for date of birth in row no. $row_no";
                        break;
                    }

                    $cnic = strtolower(trim($value[5]));
                    if ($cnic) {
                        $pump_operator['cnic'] = $cnic;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for national identity number in row no. $row_no";
                        break;
                    }


                    if (!$is_valid) {
                        throw new \Exception($error_msg);
                    }

                    $pump_operator['status'] = 1;

                    PumpOperator::create($pump_operator);

                    $row_no++;
                }
                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_operator_import_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];

            return redirect()->back()->with('notification', $output);
        }

        return redirect('/petro/pump-operators')->with('status', $output);
    }

    /**
     * Shows ledger for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getLedger()
    {
        $business_id =  Auth::user()->business_id;
        $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
            ->where('account_types.name', 'like', '%Assets%')
            ->where('accounts.business_id', $business_id)
            ->pluck('accounts.id')->toArray();
        $pump_operator_id = request()->input('pump_operator_id');

        $start_date = request()->start_date;
        $end_date =  request()->end_date;

        $pump_operator = PumpOperator::find($pump_operator_id);
        $business_details = $this->businessUtil->getDetails($pump_operator->business_id);
        $location_details = BusinessLocation::where('business_id', $pump_operator->business_id)->first();
        $opening_balance = Transaction::where('pump_operator_id', $pump_operator_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');

        $ledger_details = $this->__getLedgerDetails($pump_operator_id, $start_date, $end_date);

        $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
            ->leftjoin('transaction_payments', 'account_transactions.transaction_payment_id', 'transaction_payments.id')
            ->where('pump_operator_id', $pump_operator_id)
            ->where('account_transactions.sub_type', 'ledger_show')
            ->whereIn('transactions.sub_type', ['excess', 'shortage'])
            ->select(
                'account_transactions.*',
                'account_transactions.type as acc_transaction_type',
                'business_locations.name as location_name',
                'transactions.ref_no',
                'transactions.invoice_no',
                'transactions.sub_type',
                'transactions.transaction_date',
                'transactions.payment_status',
                'transaction_payments.method as payment_method',
                'transaction_payments.payment_ref_no',
                'transaction_payments.id as tp_id',
                'transaction_payments.paid_on',
                'transactions.type as transaction_type',
                DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
            );

        if (!empty($start_date)  && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        $ledger_transactions = $query->groupBy('account_transactions.id')->orderBy('account_transactions.id', 'asc')->withTrashed()->get();

        $payment_types = $this->transactionUtil->payment_types();

        if (request()->input('action') == 'pdf') {
            $for_pdf = true;
            $html = view('petro::pump_operators.ledger')
                ->with(compact('ledger_details', 'pump_operator', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'payment_types'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }
        if (request()->input('action') == 'print') {
            $for_pdf = true;
            return view('petro::pump_operators.ledger')
                ->with(compact('ledger_details', 'pump_operator', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'payment_types'))->render();
        }

        return view('petro::pump_operators.ledger')
            ->with(compact('ledger_details', 'pump_operator', 'opening_balance', 'ledger_transactions', 'business_details', 'location_details', 'payment_types'));
    }

    /**
     * Function to get ledger details
     *
     */
    private function __getLedgerDetails($pump_operator_id, $start_date, $end_date)
    {
        $business_id =  Auth::user()->business_id;
        $contact = PumpOperator::where('id', $pump_operator_id)->first();
        //Get transaction totals between dates

        $pump_op_query = Transaction::where('business_id', $business_id)
            ->where('type', 'settlement')
            ->whereIn('sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', ABS(final_total), 0)) as excess"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', final_total, 0)) as shortage")
            )->first();

        $total_paid_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->where('type', 'settlement')
            ->whereIn('sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transaction_payments.paid_on', '>=', $start_date)
            ->whereDate('transaction_payments.paid_on', '<=', $end_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', ABS(transaction_payments.amount), 0)) as excess_paid"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', transaction_payments.amount, 0)) as shortage_recovered")
            )->first();

        $total_short  = $pump_op_query->shortage + $total_paid_query->excess_paid;
        $total_recovered_excess  = $pump_op_query->excess + $total_paid_query->shortage_recovered;

        // $beginning_balance = $this->getBeginningBalance($pump_operator_id, $start_date);
        $beginning_balance = 0;

        $balance_due = $beginning_balance + $total_short - $total_recovered_excess;


        $output = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_short' => $total_short,
            'total_recovered_excess' => $total_recovered_excess,
            'beginning_balance' => $beginning_balance,
            'balance_due' => $balance_due
        ];

        return $output;
    }

    private function getBeginningBalance($pump_operator_id, $start_date)
    {
        $business_id =  Auth::user()->business_id;
        $pump_op_query = Transaction::where('business_id', $business_id)
            ->where('transactions.type', 'settlement')
            ->whereIn('transactions.sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '<', $start_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', final_total, 0)) as excess"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', final_total, 0)) as shortage")
            )->first();

        $total_paid_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'settlement')
            ->whereIn('transactions.sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transaction_payments.paid_on', '<', $start_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', transaction_payments.amount, 0)) as excess_paid"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', transaction_payments.amount, 0)) as shortage_recovered")
            )->first();

        $total_short  = $pump_op_query->shortage + $total_paid_query->excess_paid;
        $total_recovered_excess  = $pump_op_query->excess + $total_paid_query->shortage_recovered;

        $balance_due = $total_short - $total_recovered_excess;

        return $balance_due;
    }

    /**
     * Query to get transaction totals for a customer
     *
     */
    private function __transactionQuery($pump_operator_id, $start, $end = null)
    {
        $business_id =  Auth::user()->business_id;
        $transaction_type_keys = array_keys(Transaction::transactionTypes());

        $query = Transaction::where('transactions.pump_operator_id', $pump_operator_id)
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
    private function __paymentQuery($pump_operator_id, $start, $end = null)
    {
        $business_id =  Auth::user()->business_id;

        $query = TransactionPayment::join(
            'transactions as t',
            'transaction_payments.transaction_id',
            '=',
            't.id'
        )
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->where('t.pump_operator_id', $pump_operator_id)
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

    /**
     * Function to send ledger notification
     *
     */
    public function sendLedger(Request $request)
    {
        $notAllowed = $this->notificationUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $data = $request->only(['to_email', 'subject', 'email_body', 'cc', 'bcc']);
            $emails_array = array_map('trim', explode(',', $data['to_email']));

            $pump_operator_id = $request->input('pump_operator_id');
            $business_id = request()->session()->get('business.id');

            $start_date = request()->input('start_date');
            $end_date =  request()->input('end_date');

            $pump_operator = PumpOperator::find($pump_operator_id);

            $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
                ->where('account_types.name', 'like', '%Assets%')
                ->where('accounts.business_id', $business_id)
                ->pluck('accounts.id')->toArray();

            $ledger_details = $this->__getLedgerDetails($pump_operator_id, $start_date, $end_date);

            $business_details = $this->businessUtil->getDetails($pump_operator->business_id);
            $location_details = BusinessLocation::where('business_id', $pump_operator->business_id)->first();
            $opening_balance = Transaction::where('pump_operator_id', $pump_operator_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');


            $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                ->where('transactions.type', 'sell')
                ->orWhere('transactions.type', 'opening_balance')
                ->where('pump_operator_id', $pump_operator_id)
                ->select(
                    'account_transactions.*',
                    'account_transactions.type as acc_transaction_type',
                    'business_locations.name as location_name',
                    'transactions.ref_no',
                    'transactions.transaction_date',
                    'transactions.payment_status',
                    'transaction_payments.method as payment_method',
                    'transactions.type as transaction_type',
                    DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
                );

            if (!empty($start_date)  && !empty($end_date)) {
                $query->whereDate(
                    'transactions.transaction_date',
                    '>=',
                    $start_date
                )->whereDate('transactions.transaction_date', '<=', $end_date)->get();
            }
            $ledger_transactions = $query->get();

            $orig_data = [
                'email_body' => $data['email_body'],
                'subject' => $data['subject']
            ];

            $tag_replaced_data = $this->notificationUtil->replaceTags($business_id, $orig_data, null, $contact);
            $data['email_body'] = $tag_replaced_data['email_body'];
            $data['subject'] = $tag_replaced_data['subject'];

            //replace balance_due
            $data['email_body'] = str_replace('{balance_due}', $this->notificationUtil->num_f($ledger_details['balance_due']), $data['email_body']);

            $data['email_settings'] = request()->session()->get('business.email_settings');


            $for_pdf = true;
            $html = view('petro::pump_operators.ledger')
                ->with(compact('ledger_details', 'pump_operator', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);

            $file = config('constants.mpdf_temp_path') . '/' . time() . '_ledger.pdf';
            $mpdf->Output($file, 'F');

            $data['attachment'] =  $file;
            $data['attachment_name'] =  'ledger.pdf';
            \Notification::route('mail', $emails_array)
                ->notify(new CustomerNotification($data));

            if (file_exists($file)) {
                unlink($file);
            }

            $output = ['success' => 1, 'msg' => __('lang_v1.notification_sent_successfully')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()
            ];
        }

        return $output;
    }


    public function getReport()
    {
        $business_id =  Auth::user()->business_id;

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            if (request()->ajax()) {
                $query = PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->where('pump_operators.business_id', $business_id)
                    ->select([
                        'pump_operators.*',
                        'business_locations.name as location_name',
                    ]);

                if (!empty(request()->location_id)) {
                }
                if (!empty(request()->pump_operator)) {
                }
                if (!empty(request()->settlement_no)) {
                }
                if (!empty(request()->type)) {
                }
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                }

                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'pump_no',
                        ''
                    )
                    ->addColumn(
                        'settlement_no',
                        ''
                    )
                    ->addColumn(
                        'pumped_fuel_ltrs',
                        ''
                    )
                    ->addColumn(
                        'amount',
                        ''
                    )
                    ->addColumn(
                        'commission_rate',
                        '{{$commission_type}}'
                    )
                    ->addColumn(
                        'commission_amount',
                        '{{$commission_ap}}'
                    )

                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action'])
                    ->make(true);
            }
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $settlement_nos = [];

        return view('petro::pump_operators.pump_operator_report')->with(compact('business_locations', 'pump_operators', 'settlement_nos'));
    }


    /**
     * Display a excess and shortage of the resource.
     * @return Response
     */
    public function getPumperExcessShortagePayments()
    {
        $business_id =  Auth::user()->business_id;

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            $payment_types = $this->productUtil->payment_types();
            if (request()->ajax()) {
                $query = PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->leftjoin('transactions', 'pump_operators.id', 'transactions.pump_operator_id')
                    ->leftjoin('transaction_payments', function ($join) {
                        $join->on('transactions.id', 'transaction_payments.transaction_id')->whereNull('transaction_payments.deleted_at');
                    })
                    ->where('pump_operators.business_id', $business_id)
                    ->where('transactions.type', 'settlement')
                    ->whereIn('transactions.sub_type', ['shortage', 'excess'])
                    ->select([
                        'pump_operators.*',
                        'transactions.id as t_id',
                        'transactions.type',
                        'transactions.sub_type',
                        'transactions.final_total',
                        'transactions.transaction_date',
                        'pump_operators.id as pump_operator_id',
                        'business_locations.name as location_name',
                        'transaction_payments.amount',
                        'transaction_payments.method',
                        'transaction_payments.id as tp_id',
                        'transaction_payments.paid_on'
                    ])->groupBy('transaction_payments.id');

                if (!empty(request()->location_id)) {
                    $query->where('transactions.location_id', request()->location_id);
                }
                if (!empty(request()->pump_operator)) {
                    $query->where('transactions.pump_operator_id', request()->pump_operator);
                }

                if (!empty(request()->type)) {
                    $query->where('transactions.sub_type', request()->type);
                }
                if (!empty(request()->payment_type)) {
                    $query->where('transaction_payments.method', request()->payment_type);
                }
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('transaction_payments.paid_on', '>=', request()->start_date);
                    $query->whereDate('transaction_payments.paid_on', '<=', request()->end_date);
                }
                $business_id = session()->get('user.business_id');
                $business_details = Business::find($business_id);


                $fuel_tanks = Datatables::of($query)
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

                            if (!empty($row->tp_id)) {
                                $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->t_id]) . '" class="view_payment_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';

                                if ($row->sub_type == 'shortage') {
                                    $html .= '<li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\RecoverShortageController@edit", [$row->tp_id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>
                                    <li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\RecoverShortageController@destroy", [$row->tp_id]) . '" class="delete_payment" ><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</a></li>';
                                }
                                if ($row->sub_type == 'excess') {
                                    $html .= '<li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\ExcessComissionController@edit", [$row->tp_id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>
                                    <li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\ExcessComissionController@destroy", [$row->tp_id]) . '" class="delete_payment" ><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</a></li>';
                                }
                            }

                            $html .= '</ul></div>';

                            return $html;
                        }
                    )
                    ->editColumn(
                        'paid_on',
                        '{{@format_date($paid_on)}}'
                    )
                    ->editColumn(
                        'excess_amount',
                        function ($row) use ($business_details) {
                            if ($row->sub_type == 'excess') {
                                return  '<span class="display_currency excess_amount" data-orig-value="' . $row->final_total . '" data-currency_symbol = true>' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                            }
                            return $this->productUtil->num_f(0, false, $business_details, false);
                        }
                    )
                    ->editColumn(
                        'short_amount',
                        function ($row) use ($business_details) {
                            if ($row->sub_type == 'shortage') {
                                return  '<span class="display_currency short_amount" data-orig-value="' . $row->final_total . '" data-currency_symbol = true>' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                            }
                            return $this->productUtil->num_f(0, false, $business_details, false);
                        }
                    )
                    ->addColumn('shortage_recover', function ($row) use ($business_details) {
                        if ($row->sub_type == 'shortage') {
                            return $this->productUtil->num_f($row->amount, false, $business_details, false);
                        }
                        return $this->productUtil->num_f(0, false, $business_details, false);
                    })
                    ->addColumn('excess_paid', function ($row) use ($business_details, $payment_types) {
                        $method = '';
                        if (!empty($row->method)) {
                            $method = $payment_types[$row->method];
                        }
                        if ($row->sub_type == 'excess') {
                            return $this->productUtil->num_f($row->amount, false, $business_details, false) . ' ' . $method;
                        }
                        return $this->productUtil->num_f(0, false, $business_details, false);
                    })

                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action', 'sold_fuel_qty', 'sale_amount_fuel', 'excess_amount', 'short_amount', 'commission_rate', 'commission_amount'])
                    ->make(true);
            }
        }
    }

    public function toggleActivate($id)
    {
        try {
            $pump_operator = PumpOperator::findOrFail($id);
            $pump_operator->active = !$pump_operator->active;
            $pump_operator->save();

            $output = [
                'success' => true,
                'msg' => __('lang_V1.success')
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


    public function dashboard()
    {
        $business_id = Auth::user()->business_id;
        $pump_operator_id = Auth::user()->pump_operator_id;

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $fuel_tanks = FuelTank::where('business_id', $business_id)->get();

        $general_message = '';

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard')) {
            if (System::getProperty('general_message_pump_operator_dashbaord_checkbox') == 1) {
                $font_size = System::getProperty('customer_supplier_security_deposit_current_liability_font_size');
                $color = System::getProperty('customer_supplier_security_deposit_current_liability_color');
                $msg = System::getProperty('customer_supplier_security_deposit_current_liability_message');
                $general_message = '<p style="font-size: ' . $font_size . ';color: ' . $color . ' ">' . $msg . '</p>';
            }
        }

        $today_close_pump_count = PumpOperatorAssignment::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->whereDate('date_and_time', date('Y-m-d'))->where('status', 'open')->count();
        $is_all_closed = PumperDayEntry::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->whereDate('date', date('Y-m-d'))->count();

        $can_close_shift = true;
        if ($today_close_pump_count == 0) {
            $can_close_shift = true;
        }

        if (empty(session()->get('pump_operator_main_system'))) {
            $layout = 'pumper';
        } else {
            $layout = 'app';
        }

        return view('petro::pump_operators.dashboard')->with(compact(
            'general_message',
            'fuel_tanks',
            'layout',
            'can_close_shift',
            'date_filters'
        ));
    }
    public function getDashboardData(Request $request)
    {
        $pump_operator_id =  $request->pump_operator_id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        $start_date =  $request->start;
        $end_date =  $request->end;

        $data = [
            'total_liter_sold' => 0,
            'total_income_earned' => 0,
            'total_short' => 0,
            'total_excess' => 0,
        ];
        $sold_fuel_query =    Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('transactions.type', 'sell')
            ->where('categories.name', 'Fuel')
            ->where('transactions.pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select([
                DB::raw('SUM(transaction_sell_lines.quantity) as sold_fuel_qty'),
                DB::raw('SUM(transaction_sell_lines.quantity * unit_price) as sale_amount_fuel')
            ])->first();

        if (!empty($sold_fuel_query->sold_fuel_qty)) {
            $data['total_liter_sold'] = $sold_fuel_query->sold_fuel_qty;
        }

        if ($pump_operator->commission_type == 'fixed') {
            $data['total_income_earned'] =  $sold_fuel_query->sold_fuel_qty *  $pump_operator->commission_ap;
        }
        if ($pump_operator->commission_type == 'percentage') {
            $data['total_income_earned'] =  ($sold_fuel_query->sale_amount_fuel * $pump_operator->commission_ap) / 100;
        }

        $short_amount_query =    Transaction::where('transactions.type', 'settlement')
            ->where('transactions.sub_type', 'shortage')
            ->where('transactions.pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select([
                DB::raw('SUM(transactions.final_total) as short_amount')
            ])->first();
        $excess_amount_query =    Transaction::where('transactions.type', 'settlement')
            ->where('transactions.sub_type', 'excess')
            ->where('transactions.pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select([
                DB::raw('SUM(transactions.final_total) as excess_amount')
            ])->first();

        $data['total_short'] = $short_amount_query->short_amount;
        $data['total_excess'] = $excess_amount_query->excess_amount;



        return $data;
    }

    /**
     * check user name exist or not
     * @return Renderable
     */
    public function checUsername(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (empty($user)) {
            return ['success' => 1, 'msg' => 'ok'];
        } else {
            return ['success' => 0, 'msg' => __('lang_v1.username_already_exist')];
        }
    }

    /**
     * check user name exist or not
     * @return Renderable
     */
    public function checPasscode(Request $request)
    {
        $user = User::where('pump_operator_passcode', $request->passcode)->first();

        if (empty($user)) {
            return ['success' => 1, 'msg' => 'ok'];
        } else {
            return ['success' => 0, 'msg' => __('lang_v1.passcode_already_exist')];
        }
    }

    /**
     * check user name exist or not
     * @return Renderable
     */
    public function setMainSystemSession(Request $request)
    {
        $request->session()->put('pump_operator_main_system', true);

        return redirect()->to('petro/pump-operators/dashboard');
    }
}
