<?php

namespace Modules\Ezyboat\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Ezyboat\Entities\IncomeSetting;
use Modules\Ezyboat\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;

class IncomeSettingController extends Controller
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

            $income_settings = IncomeSetting::leftjoin('users', 'income_settings.created_by', 'users.id')
                ->where('income_settings.business_id', $business_id)
                ->select([
                    'income_settings.*',
                    'users.username as created_by',
                ]);

            if (!empty(request()->employee_no)) {
                $income_settings->where('employee_no', request()->employee_no);
            }
            if (!empty(request()->income_name)) {
                $income_settings->where('income_name', request()->income_name);
            }
            if (!empty(request()->nic_number)) {
                $income_settings->where('nic_number', request()->nic_number);
            }
            if (!empty(request()->user_id)) {
                $income_settings->where('created_by', request()->user_id);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $income_settings->whereDate('income_settings.date', '>=', request()->start_date);
                $income_settings->whereDate('income_settings.date', '<=', request()->end_date);
            }
            return DataTables::of($income_settings)
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
                        if (auth()->user()->can('fleet.income_settings.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Ezyboat\Http\Controllers\IncomeSettingController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }

                        if (auth()->user()->can('fleet.income_settings.delete')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Ezyboat\Http\Controllers\IncomeSettingController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        $html .= '<li class="divider"></li>';
                        $status = '';
                        if($row->status == 1){
                            $status = 'Disable';
                        }
                        if($row->status == 0){
                            $status = 'Enable';
                        }
                        $html .= '<li><a data-href="' . action('\Modules\Ezyboat\Http\Controllers\IncomeSettingController@toggleStatus', [$row->id]) . '" class="toggle-status"><i class="fa fa-info"></i> ' . $status  . '</a></li>';
                        return $html;
                    }
                )
                ->editColumn('owner_income', '{{@num_format($owner_income)}}')
                ->editColumn('crew_income', '{{@num_format($crew_income)}}')
                ->editColumn('deduct_expense_for_income', '{{ucfirst($deduct_expense_for_income)}}')
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

        return view('ezyboat::settings.income_settings.create')->with(compact(
            'business_id'
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
            $data['business_id'] = $business_id;
            $data['created_by'] = Auth::user()->id;
            $data['date'] = Carbon::now();
            $data['owner_income'] = $this->transactionUtil->num_uf($data['owner_income']);
            $data['crew_income'] = $this->transactionUtil->num_uf($data['crew_income']);
            $data['status'] = 1;

            //update emploeyee count
            $this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);


            IncomeSetting::create($data);

            $output = [
                'success' => true,
                'tab' => 'income_settings',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'income_settings',
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
        $income_setting_dropdown = IncomeSetting::where('business_id', $business_id)->pluck('income_name', 'id');
        $view_type = request()->tab;
        $income_setting = IncomeSetting::find($id);

        return view('ezyboat::settings.income_settings.show')->with(compact(
            'income_setting_dropdown',
            'view_type',
            'income_setting'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $income_setting = IncomeSetting::find($id);

        return view('ezyboat::settings.income_settings.edit')->with(compact(
            'income_setting'
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
            $data['owner_income'] = $this->transactionUtil->num_uf($data['owner_income']);
            $data['crew_income'] = $this->transactionUtil->num_uf($data['crew_income']);

            IncomeSetting::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'income_settings',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'income_settings',
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

            IncomeSetting::where('id', $id)->delete();

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


    public function toggleStatus($id)
    {
        try {
            $setting = IncomeSetting::find($id);

            $setting->status = !$setting->status;
            $setting->save();

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
