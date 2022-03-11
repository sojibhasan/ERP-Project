<?php

namespace Modules\Property\Http\Controllers;

use App\BusinessLocation;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\BlockCloseReason;
use Yajra\DataTables\Facades\DataTables;

class BlockCloseReasonController extends Controller
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
        if (!auth()->user()->can('property.settings.block_close_reason')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $peroperty_taxes = BlockCloseReason::leftjoin('users', 'block_close_reasons.created_by', 'users.id')
                ->where('block_close_reasons.business_id', $business_id)
                ->select([
                    'block_close_reasons.*',
                    'users.username as created_by',
                ]);

            return DataTables::of($peroperty_taxes)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Property\Http\Controllers\BlockCloseReasonController@edit\', [$id])}}" data-container=".view_modal" class="btn btn-xs btn-modal btn-primary edit_block_close_reason_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                     &nbsp;
                    <button data-href="{{action(\'\Modules\Property\Http\Controllers\BlockCloseReasonController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_block_close_reason_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
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
        if (!auth()->user()->can('property.settings.block_close_reason')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        return view('property::setting.block_close_reason.create')
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
        if (!auth()->user()->can('property.settings.block_close_reason')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['reason']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

            $tax = BlockCloseReason::create($input);
            $output = [
                'success' => true,
                'tab' => 'block-close-reason',
                'msg' => __("property::lang.block_close_reason_added_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'tab' => 'block-close-reason',
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
        if (!auth()->user()->can('property.settings.block_close_reason')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $block_close_reason = BlockCloseReason::where('business_id', $business_id)->find($id);

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

            return view('property::setting.block_close_reason.edit')
                ->with(compact('block_close_reason', 'business_locations'));
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
        if (!auth()->user()->can('property.settings.block_close_reason')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['reason']);
                $input['created_by'] = $request->session()->get('user.id');
                $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

                BlockCloseReason::where('id', $id)->update($input);

                $output = [
                    'success' => true,
                    'tab' => 'block-close-reason',
                    'msg' => __("property::lang.block_close_reason_updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'tab' => 'block-close-reason',
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
        if (!auth()->user()->can('property.settings.block_close_reason')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                BlockCloseReason::findOrFail($id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("property::lang.block_close_reason_deleted_success")
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
