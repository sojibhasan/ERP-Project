<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\HR\Entities\WorkShift;
use Yajra\DataTables\Facades\DataTables;

class WorkShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $job_categories = WorkShift::where('business_id', $business_id);

            return DataTables::of($job_categories)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <a class="btn-modal eye_modal  btn-xs btn-primary" 
                        data-href="{{action(\'\Modules\HR\Http\Controllers\WorkShiftController@edit\', [$id])}}" 
                        data-container=".workshift_edit_modal">
                        <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit") </a>
                        &nbsp;
                        <a data-href="{{action(\'\Modules\HR\Http\Controllers\WorkShiftController@destroy\', [$id])}}" class="delete_workshift  btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a>
                    </div>'
                )
                ->removeColumn('id')


                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::settings.work_shift.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_name' => 'required|string',
            'shift_form' => 'required|string',
            'shift_to' => 'required|string'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $data = array(
                'business_id' => $business_id,
                'shift_name' => $request->input('shift_name'),
                'shift_form' => $request->input('shift_form'),
                'shift_to' => $request->input('shift_to')
            );

            WorkShift::insert($data);

            $output = [
                'success' => 1,
                'tab' => 'working_shift',
                'msg' => __('hr.workshift_add_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'tab' => 'working_shift',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
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
        $workshift = WorkShift::where('id', $id)->first();
        return view('hr::settings.work_shift.edit')->with(compact('workshift'));
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
        $validator = Validator::make($request->all(), [
            'shift_name' => 'required|string',
            'shift_form' => 'required|string',
            'shift_to' => 'required|string'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $data = array(
                'business_id' => $business_id,
                'shift_name' => $request->input('shift_name'),
                'shift_form' => $request->input('shift_form'),
                'shift_to' => $request->input('shift_to')
            );

            WorkShift::where('id', $id)->update($data);

            $output = [
                'success' => 1,
                'tab' => 'working_shift',
                'msg' => __('hr.workshift_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'tab' => 'working_shift',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            WorkShift::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr.workshift_delete_success')
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }
}
