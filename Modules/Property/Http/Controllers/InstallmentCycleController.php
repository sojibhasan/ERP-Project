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
use Modules\Property\Entities\InstallmentCycle;
use Yajra\DataTables\Facades\DataTables;

class InstallmentCycleController extends Controller
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

            $peroperty_taxes = InstallmentCycle::leftjoin('users', 'installment_cycles.created_by', 'users.id')
                ->where('installment_cycles.business_id', $business_id)
                ->select([
                    'installment_cycles.*',
                    'users.username as created_by',
                ]);

            return DataTables::of($peroperty_taxes)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Property\Http\Controllers\InstallmentCycleController@edit\', [$id])}}" data-container=".view_modal" class="btn btn-xs btn-modal btn-primary edit_installment_cycle_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                     &nbsp;
                    <button data-href="{{action(\'\Modules\Property\Http\Controllers\InstallmentCycleController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_installment_cycle_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->editColumn('cycle_date', '@if(!empty($cycle_date)){{@format_date($cycle_date)}}@endif ')
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

        return view('property::setting.installment_cycle.create')
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
            $input = $request->only(['name']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

            $tax = InstallmentCycle::create($input);
            $output = [
                'success' => true,
                'tab' => 'installment_cycle',
                'msg' => __("property::lang.installment_cycle_added_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'tab' => 'installment_cycle',
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
            $installment_cycle = InstallmentCycle::where('business_id', $business_id)->find($id);

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

            return view('property::setting.installment_cycle.edit')
                ->with(compact('installment_cycle', 'business_locations'));
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
                $input = $request->only(['name']);
                $input['business_id'] = $request->session()->get('user.business_id');
                $input['created_by'] = $request->session()->get('user.id');
                $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

                InstallmentCycle::where('id', $id)->update($input);

                $output = [
                    'success' => true,
                    'tab' => 'installment_cycle',
                    'msg' => __("property::lang.installment_cycle_updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'tab' => 'installment_cycle',
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
                InstallmentCycle::findOrFail($id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("property::lang.installment_cycle_deleted_success")
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
}
