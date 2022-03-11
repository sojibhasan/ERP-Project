<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\HR\Entities\SalaryComponent;
use Yajra\DataTables\Facades\DataTables;

class SalaryComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $salary_components = SalaryComponent::where('salary_components.business_id', $business_id)
                ->select([
                    'salary_components.*'
                ]);

            $salary_components->groupBy('salary_components.id');

            return DataTables::of($salary_components)
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

                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\SalaryComponentController@edit', [$row->id]) . '" data-container=".salary_component_model" class="btn-modal salary_component_eidt"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\HR\Http\Controllers\SalaryComponentController@destroy', [$row->id]) . '" class="delete-salary_component"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('type', function ($row) {
                    if ($row->type == '1') {
                        return __('hr::lang.earning');
                    } else if ($row->type == '2') {
                        return __('hr::lang.deduction');
                    } else {
                        return '';
                    }
                })
                ->editColumn('total_payable', function ($row) {
                    if ($row->total_payable == '1') {
                        return __('hr::lang.yes');
                    } else {
                        return __('hr::lang.no');
                    }
                })
                ->editColumn('cost_company', function ($row) {
                    if ($row->cost_company == '1') {
                        return __('hr::lang.yes');
                    } else {
                        return __('hr::lang.no');
                    }
                })
                ->editColumn('value_type', function ($row) {
                    if ($row->value_type == '1') {
                        return __('hr::lang.amount');
                    } else if ($row->value_type == '2') {
                        return __('hr::lang.percentage');
                    } else {
                        return '';
                    }
                })
                ->editColumn('component_amount', '{{@number_format($component_amount)}}')
                ->editColumn('statutory_fund', function ($row) {
                    if ($row->cost_company == '1') {
                        return __('hr::lang.yes');
                    } else {
                        return __('hr::lang.no');
                    }
                })

                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('hr::settings.salary_component.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $statutory_fund = SalaryComponent::where('business_id', $business_id)->where('statutory_fund', 1)->get();
        return view('hr::settings.salary_component.create')->with(compact('statutory_fund'));
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
            $input['statutory_payment'] = !empty($request->statutory_payment) ? json_encode($request->statutory_payment) : '';
            $input['total_payable'] = !empty($request->total_payable) ? 1 : 0;
            $input['cost_company'] = !empty($request->cost_company) ? 1 : 0;

            SalaryComponent::create($input);

            $output = [
                'success' => true,
                'tab' => 'salary_component',
                'msg' => __('hr::lang.salary_component_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'salary_component',
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
        $business_id = request()->session()->get('business.id');
        $salary_component = SalaryComponent::findOrFail($id);
        $statutory_fund = SalaryComponent::where('business_id', $business_id)->where('statutory_fund', 1)->get();

        return view('hr::settings.salary_component.edit')->with(compact('salary_component', 'statutory_fund'));
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
            $input['statutory_payment'] = !empty($request->statutory_payment) ? json_encode($request->statutory_payment) : '';
            $input['total_payable'] = !empty($request->total_payable) ? 1 : 0;
            $input['cost_company'] = !empty($request->cost_company) ? 1 : 0;
            SalaryComponent::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'salary_component',
                'msg' => __('hr::lang.salary_component_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'salary_component',
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
            SalaryComponent::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('hr::lang.salary_component_delete_success')
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
