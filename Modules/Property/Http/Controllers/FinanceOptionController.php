<?php

namespace Modules\Property\Http\Controllers;

use App\BusinessLocation;
use App\Transaction;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\FinanceOption;
use Yajra\DataTables\Facades\DataTables;

class FinanceOptionController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $peroperty_taxes = FinanceOption::leftjoin('business_locations', 'finance_options.location_id', 'business_locations.id')
                ->leftjoin('users', 'finance_options.created_by', 'users.id')
                ->where('finance_options.business_id', $business_id)
                ->select([
                    'finance_options.*',
                    'business_locations.name as location_name',
                    'users.username as created_by',
                ]);

            return DataTables::of($peroperty_taxes)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Property\Http\Controllers\FinanceOptionController@edit\', [$id])}}" data-container=".view_modal" class="btn btn-xs btn-modal btn-primary edit_finance_option_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                     &nbsp;
                    <button data-href="{{action(\'\Modules\Property\Http\Controllers\FinanceOptionController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_finance_option_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->editColumn('custom_payments', '{{ucfirst($custom_payments)}}')
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        return view('property::setting.finance_options.create')
            ->with(compact('quick_add', 'business_locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['location_id', 'finance_option', 'custom_payments']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

            $tax = FinanceOption::create($input);
            $output = [
                'success' => true,
                'tab' => 'finance_option',
                'msg' => __("property::lang.finance_option_added_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'tab' => 'finance_option',
                'msg' => __("messages.something_went_wrong")
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $finance_option = FinanceOption::where('business_id', $business_id)->find($id);

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

            return view('property::setting.finance_options.edit')
                ->with(compact('finance_option', 'business_locations'));
        }
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
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['location_id', 'finance_option', 'custom_payments']);
                $input['business_id'] = $request->session()->get('user.business_id');
                $input['created_by'] = $request->session()->get('user.id');
                $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

                FinanceOption::where('id', $id)->update($input);

                $output = [
                    'success' => true,
                    'tab' => 'finance_option',
                    'msg' => __("property::lang.finance_option_updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'tab' => 'finance_option',
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('property.settings.tax')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $transactions = Transaction::where('finance_option_id', $id)->first();
                if (!empty($transactions)) {
                    $output = [
                        'success' => false,
                        'msg' => __("property::lang.transaction_exit_deleted_error")
                    ];
                    return $output;
                }
                FinanceOption::findOrFail($id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("property::lang.finance_option_deleted_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => '__("messages.something_went_wrong")'
                ];
            }

            return $output;
        }
    }

    public function getDetails($id){
        $finance_option = FinanceOption::find($id);
        $data = [
            'success' => 1,
            'finance_option' => $finance_option
        ];
        return $data;
    }
}
