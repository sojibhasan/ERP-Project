<?php

namespace Modules\Ran\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Ran\Entities\GoldGrade;
use Yajra\DataTables\Facades\DataTables;

class GoldGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $gold_grades = GoldGrade::where('gold_grades.business_id', $business_id)
            ->leftjoin('users', 'gold_grades.created_by', 'users.id')
                ->select([
                    'gold_grades.*',
                    'users.username'
                ]);

            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $gold_grades->whereDate('date_and_time', '>=', request()->start_date);
                $gold_grades->whereDate('date_and_time', '<=', request()->end_date);
            }

            return DataTables::of($gold_grades)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\GoldGradeController@edit', [$row->id]) . '" data-container=".gold_grade_model" class="btn-modal gold_grade_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        // $html .= '<li><a href="#" data-href="' . action('\Modules\Ran\Http\Controllers\GoldGradeController@destroy', [$row->id]) . '" class="delete-holiday"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('date_and_time', '{{@format_date($date_and_time)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('ran::gold_grade.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('ran::gold_grade.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            $data = array(
                'business_id' => $business_id,
                'date_and_time' => !empty($request->date_and_time) ? Carbon::parse($request->date_and_time)->format('Y-m-d H:i:s') : Carbon::now(),
                'grade_name' => $request->grade_name,
                'gold_purity' => $request->gold_purity,
                'created_by' => Auth::user()->id,
            );

            GoldGrade::create($data);
            $output = [
                'success' => true,
                'msg' => __('ran::lang.gold_grade_create_success')
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
     * @return Response
     */
    public function show()
    {
        return view('ran::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $gold_grade = GoldGrade::findOrFail($id);

        return view('ran::gold_grade.edit')->with(compact('gold_grade'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        try {
            $data = array(
                'grade_name' => $request->grade_name,
                'gold_purity' => $request->gold_purity
            );

            GoldGrade::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('ran::lang.gold_grade_update_success')
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
     * @return Response
     */
    public function destroy()
    {
    }
}
