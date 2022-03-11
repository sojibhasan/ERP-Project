<?php

namespace Modules\MPCS\Http\Controllers;

use App\MergedSubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\MPCS\Entities\FormOpeningValue;
use Modules\MPCS\Entities\MpcsFormSetting;
use Yajra\DataTables\Facades\DataTables;

class FormOpeningValueController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $form_opening_values = FormOpeningValue::leftjoin('users', 'form_opening_values.edited_by', 'users.id')
                ->where('form_opening_values.business_id', $business_id)
                ->select([
                    'form_name', 'date', 'form_opening_values.id',
                    'users.username as edited_by'
                ]);

            if(!empty(request()->start_date) && !empty(request()->end_date)){
                $form_opening_values->whereDate('form_opening_values.date', '>=', request()->start_date);
                $form_opening_values->whereDate('form_opening_values.date', '<=', request()->end_date);
            }
            if(!empty(request()->form_name)){
                $form_opening_values->where('form_opening_values.form_name', request()->form_name);
            }

            return DataTables::of($form_opening_values)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\MPCS\Http\Controllers\FormOpeningValueController@show', [$row->id]) . '" data-container=".form_opening_value_model" class="btn-modal holiday_eidt"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\MPCS\Http\Controllers\FormOpeningValueController@print', [$row->id]) . '" class="print_settings"><i class="fa fa-print"></i> ' . __("messages.print") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('date', '{{@format_date($date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('mpcs::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('business.id');

        $form_values = FormOpeningValue::leftjoin('users', 'form_opening_values.edited_by', 'users.id')
        ->where('form_opening_values.id', $id)
        ->select('form_opening_values.*', 'users.username as edited_by')->first();

        $months = MpcsFormSetting::getMonthArray();
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();

        return view('mpcs::forms_setting.show')->with(compact(
            'form_values',
            'merged_sub_categories',
            'months'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('mpcs::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    /**
     * print the specified resource from storage.
     * @return Response
     */
    public function print($id)
    {
        $business_id = request()->session()->get('business.id');

        $form_values = FormOpeningValue::leftjoin('users', 'form_opening_values.edited_by', 'users.id')
        ->where('form_opening_values.id', $id)
        ->select('form_opening_values.*', 'users.username as edited_by')->first();

        $months = MpcsFormSetting::getMonthArray();
        $merged_sub_categories = MergedSubCategory::where('business_id', $business_id)->get();

        return view('mpcs::forms_setting.print')->with(compact(
            'form_values',
            'merged_sub_categories',
            'months'
        ));
    }
}
