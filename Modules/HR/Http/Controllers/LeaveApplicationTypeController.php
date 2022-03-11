<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\LeaveApplicationType;
use Yajra\DataTables\Facades\DataTables;

class LeaveApplicationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $leave_application_types = LeaveApplicationType::where('leave_application_types.business_id', $business_id)
                ->select([
                    'leave_application_types.*'
                ]);

            $leave_application_types->groupBy('leave_application_types.id');

            return DataTables::of($leave_application_types)
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
                        if ($row->leave_type != 'Short Leave' &&  $row->leave_type != 'Half Day') {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\LeaveApplicationTypeController@edit', [$row->id]) . '" data-container=".leave_application_type_model" class="btn-modal leave_application_type_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                            $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\LeaveApplicationTypeController@destroy', [$row->id]) . '" class="delete-leave_application_type"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::settings.leave_application_type.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hr::settings.leave_application_type.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');
            $input = $request->except('_token');
            $input['business_id'] = $business_id;
            LeaveApplicationType::create($input);

            $output = [
                'success' => true,
                'tab' => 'leave_application_type',
                'msg' => __('hr::lang.leave_application_type_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'leave_application_type',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('hr::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $leave_application_type = LeaveApplicationType::findOrFail($id);
        return view('hr::settings.leave_application_type.edit')->with(compact('leave_application_type'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->except('_token', '_method');
            LeaveApplicationType::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'leave_application_type',
                'msg' => __('hr::lang.leave_application_type_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'leave_application_type',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            LeaveApplicationType::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr::lang.leave_application_type_delete_success')
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
