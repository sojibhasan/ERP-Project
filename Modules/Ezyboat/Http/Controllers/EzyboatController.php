<?php

namespace Modules\Ezyboat\Http\Controllers;

use App\Account;
use App\AccountType;
use App\BusinessLocation;
use App\ExpenseCategory;
use App\Transaction;
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
use Illuminate\Support\Facades\Session;
use Modules\Ezyboat\Entities\Fleet;
use Modules\Ezyboat\Entities\Route;
use Yajra\DataTables\Facades\DataTables;

class EzyboatController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
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
            $fleets = Fleet::leftjoin('transactions AS t', 'fleets.id', '=', 't.fleet_id')
                ->leftjoin('business_locations', 'fleets.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 't.id', 'transaction_payments.transaction_id')
                ->leftjoin('users', 'fleets.created_by', 'users.id')
                ->where('fleets.business_id', $business_id)
                ->select([
                    'fleets.*',
                    'business_locations.name as location_name',
                    DB::raw("SUM(IF(t.type = 'route_operation', final_total, 0)) as income"),
                    DB::raw("SUM(IF(t.type = 'route_operation', transaction_payments.amount, 0)) as total_received"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                ]);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $fleets->whereDate('fleets.date', '>=', request()->start_date);
                $fleets->whereDate('fleets.date', '<=', request()->end_date);
            }
            if (!empty(request()->location_id)) {
                $fleets->where('fleets.location_id', request()->location_id);
            }
            if (!empty(request()->vehicle_number)) {
                $fleets->where('fleets.vehicle_number', request()->vehicle_number);
            }
            if (!empty(request()->vehicle_type)) {
                $fleets->where('fleets.vehicle_type', request()->vehicle_type);
            }
            if (!empty(request()->vehicle_brand)) {
                $fleets->where('fleets.vehicle_brand', request()->vehicle_brand);
            }
            if (!empty(request()->vehicle_model)) {
                $fleets->where('fleets.vehicle_model', request()->vehicle_model);
            }
            $fleets->groupBy('fleets.id');

            return DataTables::of($fleets)
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ezyboat\Http\Controllers\EzyboatController@edit', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ezyboat\Http\Controllers\EzyboatController@destroy', [$row->id]) . '" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '<li class="divider"></li>';

                        $html .= '<li><a href="' . action('\Modules\Ezyboat\Http\Controllers\EzyboatController@show', [$row->id]) . '?tab=info" class="" ><i class="fa fa-info-circle"></i> ' . __("fleet::lang.info") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Ezyboat\Http\Controllers\EzyboatController@show', [$row->id]) . '?tab=ledger" class="" ><i class="fa fa-anchor"></i> ' . __("fleet::lang.ledger") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Ezyboat\Http\Controllers\EzyboatController@show', [$row->id]) . '?tab=income" class="" ><i class="fa fa-money"></i> ' . __("fleet::lang.income") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Ezyboat\Http\Controllers\EzyboatController@show', [$row->id]) . '?tab=expenses" class="" ><i class="fa fa-minus"></i> ' . __("fleet::lang.expenses") . '</a></li>';


                        return $html;
                    }
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->addColumn('income', function ($row) {
                    $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $row->income . '">' . $row->income . '</span>';

                    return $html;
                })
                ->addColumn('payment_received', function ($row) {
                    $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $row->total_received . '">' . $row->total_received . '</span>';

                    return $html;
                })
                ->addColumn('payment_due', function ($row) {
                    $payment_due = $row->income - $row->total_received;
                    $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $payment_due . '">' . $payment_due . '</span>';

                    return $html;
                })
                ->editColumn('opening_balance', function ($row) {
                    $paid_opening_balance = !empty($row->opening_balance_paid) ? $row->opening_balance_paid : 0;
                    $opening_balance = !empty($row->opening_balance) ? $row->opening_balance : 0;
                    $balance_value = $opening_balance - ($paid_opening_balance);
                    $html = '<span class="display_currency ob" data-currency_symbol="true" data-orig-value="' . $balance_value . '">' . $balance_value . '</span>';

                    return $html;
                })

                ->removeColumn('id')
                ->rawColumns(['action', 'payment_due', 'opening_balance', 'income', 'payment_received'])
                ->make(true);
        }


        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $fleets = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets->pluck('vehicle_number', 'vehicle_number');
        $vehicle_types = $fleets->pluck('vehicle_type', 'vehicle_type');
        $vehicle_brands = $fleets->pluck('vehicle_brand', 'vehicle_brand');
        $vehicle_models = $fleets->pluck('vehicle_model', 'vehicle_model');

        $access_account = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id);
        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');

        return view('ezyboat::fleet.index')->with(compact(
            'business_locations',
            'vehicle_numbers',
            'vehicle_types',
            'vehicle_brands',
            'access_account',
            'income_accounts',
            'vehicle_models'
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
        $code_for_vehicle =  Fleet::where('business_id', $business_id)->count() + 1;

        $access_account = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');

        return view('ezyboat::fleet.create')->with(compact(
            'business_locations',
            'access_account',
            'income_accounts',
            'code_for_vehicle'
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
            $business_id = request()->session()->get('business.id');
            $inputs = $request->except('_token', 'opening_balance');
            $inputs['date'] = $this->transactionUtil->uf_date($inputs['date']);
            $inputs['business_id'] = $business_id;
            $inputs['created_by'] = Auth::user()->id;
            DB::beginTransaction();
            $fleet = Fleet::create($inputs);

            if (!empty($request->opening_balance)) {
                $transaction = Transaction::create(
                    [
                        'type' => 'opening_balance',
                        'fleet_id' => $fleet->id,
                        'status' => 'received',
                        'business_id' => $business_id,
                        'transaction_date' => $inputs['date'],
                        'total_before_tax' => $request->opening_balance,
                        'location_id' => $request->location_id,
                        'final_total' => $request->opening_balance,
                        'payment_status' => 'due',
                        'created_by' => Auth::user()->id
                    ]
                );
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('ezyboat::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
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
        $business_id = request()->session()->get('business.id');

        $fleet = Fleet::leftjoin('business_locations', 'fleets.location_id', 'business_locations.id')
            ->where('fleets.id', $id)
            ->select('fleets.*', 'business_locations.name as location_name')
            ->first();
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $view_type = request()->tab;
        $fleet_dropdown = Fleet::where('business_id', $business_id)->pluck('code_for_vehicle', 'id');
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        $payment_types = $this->commonUtil->payment_types();

        return view('ezyboat::fleet.show')->with(compact(
            'fleet',
            'view_type',
            'routes',
            'fleet_dropdown',
            'payment_types',
            'expense_categories'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $fleet = Fleet::find($id);
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $access_account = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');

        return view('ezyboat::fleet.edit')->with(compact(
            'business_locations',
            'access_account',
            'income_accounts',
            'fleet'
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
            $inputs = $request->except('_token', '_method');
            $inputs['date'] = $this->transactionUtil->uf_date($inputs['date']);

            Fleet::where('id', $id)->update($inputs);

            $output = [
                'success' => true,
                'msg' => __('ezyboat::lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
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
        try {
            Fleet::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('ezyboat::lang.success')
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

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getLedger($id)
    {
        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            $fleets = Fleet::leftjoin('transactions AS t', 'fleets.id', '=', 't.fleet_id')
                ->leftjoin('business_locations', 'fleets.location_id', 'business_locations.id')
                ->leftjoin('route_operations', 't.id', 'route_operations.transaction_id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('transaction_payments', 't.id', 'transaction_payments.transaction_id')
                ->leftjoin('users', 'fleets.created_by', 'users.id')
                ->where('t.type', 'route_operation')
                ->where('t.fleet_id', $id)
                ->where('fleets.business_id', $business_id)
                ->select([
                    't.transaction_date',
                    't.final_total',
                    't.fleet_id',
                    't.invoice_no',
                    'routes.destination',
                    'routes.distance',
                    'transaction_payments.method',
                    'business_locations.name as location_name',
                    DB::raw("SUM(IF(t.type = 'route_operation', transaction_payments.amount, 0)) as payment_received")
                ])->groupBy('t.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $fleets->whereDate('t.transaction_date', '>=', request()->start_date);
                $fleets->whereDate('t.transaction_date', '<=', request()->end_date);
            }
        
            $fleets->groupBy('fleets.id');
            Session::put('balance', 0);
            return DataTables::of($fleets)

                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->addColumn('final_total', function ($row) {
                    $html = '<span class="display_currency" data-currency_symbol="false" data-orig-value="' . $row->final_total . '">' . $row->final_total . '</span>';

                    return $html;
                })
                ->addColumn('payment_received', function ($row) {
                    $html = '<span class="display_currency" data-currency_symbol="false" data-orig-value="' . $row->payment_received . '">' . $row->payment_received . '</span>';

                    return $html;
                })
                ->addColumn('balance', function ($row) {
                    $balance = Session::get('balance');
                    $balance = $balance +  $row->final_total - $row->payment_received;
                    Session::put('balance', $balance);

                    $html = '<span class="display_currency" data-currency_symbol="false" data-orig-value="' . $balance . '">' . $balance . '</span>';

                    return $html;
                })
                ->addColumn('description', function ($row) {
                    $fleet = Fleet::find($row->fleet_id);
                    $details = '<b>'.__('ezyboat::lang.route_operation_no').':</b>'. $row->invoice_no . '<br>';
                    if(!empty($fleet)){
                        $details .= '<b>'.__('ezyboat::lang.vehicle_no').':</b>'. $fleet->vehicle_number;
                    }

                    return $details;
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

                ->removeColumn('id')
                ->rawColumns(['action', 'final_total', 'addColumn', 'payment_received', 'method', 'balance', 'description'])
                ->make(true);
        }
    }
}
