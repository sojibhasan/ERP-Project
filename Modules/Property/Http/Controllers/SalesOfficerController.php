<?php

namespace Modules\Property\Http\Controllers;

use App\BusinessLocation;
use App\User;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Property\Entities\SalesOfficer;
use Yajra\DataTables\Facades\DataTables;

class SalesOfficerController extends Controller
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
        if (!auth()->user()->can('property.settings.sales_officer')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $sales_officers = SalesOfficer::leftjoin('users as so', 'sales_officers.officer_id', 'so.id')->leftjoin('users', 'sales_officers.created_by', 'users.id')
                ->where('sales_officers.business_id', $business_id)
                ->select([
                    'sales_officers.*',
                    'so.first_name',
                    'so.last_name',
                    'so.username',
                    'users.username as created_by',
                ]);

            return DataTables::of($sales_officers)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'\Modules\Property\Http\Controllers\SalesOfficerController@edit\', [$id])}}" data-container=".view_modal" class="btn btn-xs btn-modal btn-primary edit_sales_officer_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                     &nbsp;
                    <button data-href="{{action(\'\Modules\Property\Http\Controllers\SalesOfficerController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_sales_officer_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    '
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->addColumn('name', '{{$first_name}} {{$last_name}}')
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
        if (!auth()->user()->can('property.settings.sales_officer')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $users = User::where('business_id', $business_id)->pluck('username', 'id');

        $quick_add = false;
        if (!empty(request()->input('quick_add'))) {
            $quick_add = true;
        }

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        return view('property::setting.sales_officer.create')
            ->with(compact('quick_add', 'business_locations', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('property.settings.sales_officer')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['officer_id']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');
            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

            $tax = SalesOfficer::create($input);
            $output = [
                'success' => true,
                'tab' => 'sales-officer',
                'msg' => __("property::lang.sales_officer_added_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'tab' => 'sales-officer',
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
        if (!auth()->user()->can('property.settings.sales_officer')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $sales_officer = SalesOfficer::where('business_id', $business_id)->find($id);


            $users = User::where('business_id', $business_id)->pluck('username', 'id');

            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

            return view('property::setting.sales_officer.edit')
                ->with(compact('sales_officer', 'business_locations', 'users'));
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
        if (!auth()->user()->can('property.settings.sales_officer')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['officer_id']);
                $input['created_by'] = $request->session()->get('user.id');
                $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');

                SalesOfficer::where('id', $id)->update($input);

                $output = [
                    'success' => true,
                    'tab' => 'sales-officer',
                    'msg' => __("property::lang.sales_officer_updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'tab' => 'sales-officer',
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
        if (!auth()->user()->can('property.settings.sales_officer')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                SalesOfficer::findOrFail($id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("property::lang.sales_officer_deleted_success")
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
