<?php

namespace Modules\Fleet\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Fleet\Entities\Driver;
use Modules\Fleet\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;

class DriverController extends Controller
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
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $drivers = Driver::leftjoin('users', 'drivers.created_by', 'users.id')
                ->where('drivers.business_id', $business_id)
                ->select([
                    'drivers.*',
                    'users.username as created_by',
                ]);

            if (!empty(request()->employee_no)) {
                $drivers->where('employee_no', request()->employee_no);
            }
            if (!empty(request()->driver_name)) {
                $drivers->where('driver_name', request()->driver_name);
            }
            if (!empty(request()->nic_number)) {
                $drivers->where('nic_number', request()->nic_number);
            }
            if (!empty(request()->user_id)) {
                $drivers->where('created_by', request()->user_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $drivers->whereDate('joined_date', '>=', request()->start_date);
                $drivers->whereDate('joined_date', '<=', request()->end_date);
            }
            return DataTables::of($drivers)
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
                        if (auth()->user()->can('fleet.drivers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\DriverController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.drivers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\DriverController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        $html .= '<li class="divider"></li>';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\DriverController@show', [$row->id]) . '?tab=ledger" class=""><i class="fa fa-anchor"></i> ' . __("lang_v1.ledger") . '</a></li>';

                        return $html;
                    }
                )
                ->editColumn('joined_date', '{{@format_date($joined_date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $prefix_type = 'employee_no';
        //Generate reference number
        $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);
        //Generate reference number
        $employee_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

        return view('fleet::settings.drivers.create')->with(compact(
            'employee_no'
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
            $data = $request->except('_token');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;

            //update emploeyee count
            $this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);

            Driver::create($data);

            $output = [
                'success' => true,
                'tab' => 'drivers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'drivers',
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
        $driver_dropdown = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $view_type = request()->tab;
        $driver = Driver::find($id);

        return view('fleet::settings.drivers.show')->with(compact(
            'driver_dropdown',
            'view_type',
            'driver'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $driver = Driver::find($id);

        return view('fleet::settings.drivers.edit')->with(compact(
            'driver'
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
            $data = $request->except('_token', '_method');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);

            Driver::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'drivers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'drivers',
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
            Driver::where('id', $id)->delete();

            $route_operations = RouteOperation::where('driver_id', $id)->get();
            foreach ($route_operations as $route_operation) {
                Transaction::where('id', $route_operation->transaction_id)->delete();
                TransactionPayment::where('transaction_id', $route_operation->transaction_id)->delete();
            }
            RouteOperation::where('helper_id', $id)->delete();

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
