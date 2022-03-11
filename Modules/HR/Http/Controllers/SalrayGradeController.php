<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\SalaryGrade;
use Yajra\DataTables\Facades\DataTables;

class SalrayGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $salary_grades = SalaryGrade::where('salary_grades.business_id', $business_id)
                ->select([
                    'salary_grades.*'
                ]);

            $salary_grades->groupBy('salary_grades.id');

            return DataTables::of($salary_grades)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\SalrayGradeController@edit', [$row->id]) . '" data-container=".salary_grade_model" class="btn-modal salary_grade_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\SalrayGradeController@destroy', [$row->id]) . '" class="delete-salary_grade"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::settings.salary_grade.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('hr::settings.salary_grade.create');
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
            SalaryGrade::create($input);

            $output = [
                'success' => true,
                'tab' => 'salary_grade',
                'msg' => __('hr::lang.salary_grade_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'salary_grade',
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
        $salary_grade = SalaryGrade::findOrFail($id);
        return view('hr::settings.salary_grade.edit')->with(compact('salary_grade'));
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
            SalaryGrade::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'salary_grade',
                'msg' => __('hr::lang.salary_grade_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'salary_grade',
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
            SalaryGrade::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr::lang.salary_grade_delete_success')
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
