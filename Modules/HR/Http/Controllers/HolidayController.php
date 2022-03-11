<?php

namespace Modules\HR\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\Holiday;
use Yajra\DataTables\Facades\DataTables;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $holidays = Holiday::where('holidays.business_id', $business_id)
                ->select([
                    'holidays.*'
                ]);

            $holidays->groupBy('holidays.id');

            return DataTables::of($holidays)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\HolidayController@edit', [$row->id]) . '" data-container=".holiday_model" class="btn-modal holiday_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\HolidayController@destroy', [$row->id]) . '" class="delete-holiday"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('start_date', '{{@format_date($start_date)}}')
                ->editColumn('end_date', '{{@format_date($end_date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::settings.holiday.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hr::settings.holiday.create');
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
            $input['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $input['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            Holiday::create($input);

            $output = [
                'success' => true,
                'tab' => 'holidays',
                'msg' => __('hr::lang.holiday_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'holidays',
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
        $holiday = Holiday::findOrFail($id);
        return view('hr::settings.holiday.edit')->with(compact('holiday'));
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
            $input['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
            $input['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            Holiday::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'holidays',
                'msg' => __('hr::lang.holiday_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'holidays',
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
            Holiday::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr::lang.holiday_delete_success')
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
