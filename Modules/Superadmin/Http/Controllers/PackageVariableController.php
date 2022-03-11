<?php

namespace Modules\Superadmin\Http\Controllers;

use Illuminate\Http\Request;
use App\PackageVariable;
use Yajra\DataTables\DataTables;
use Illuminate\Routing\Controller;

class PackageVariableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $package_variables = PackageVariable::where('is_company_variable', 0);



            return Datatables::of($package_variables)

                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                    data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\PackageVariableController@edit', [$row->id]) . '" class="btn-modal" data-container=".edit_modal"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                    $html .= '<li><a href="#" data-href="' . action('\Modules\Superadmin\Http\Controllers\PackageVariableController@destroy', [$row->id]) . '" class="btn-modal delete_variable"><i class="fa fa-trash" aria-hidden="true"></i>' . __("messages.delete") . '</a></li>';


                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('variable_options', function ($row) {
                    $all_variable_options = ['Number of Branches', 'Number of Users', 'Number of Products', 'Number of Periods', 'Number of Customers', 'Monthly Total Sales', 'No of Family Members', 'No of Vehicles'];
                    return $all_variable_options[$row->variable_options];
                })
                ->editColumn('increase_decrease', function ($row) {
                    $all_increase_decrease = ['Increase', 'Decrease'];
                    return $all_increase_decrease[$row->increase_decrease];
                })
                ->editColumn('variable_type', function ($row) {
                    $all_variable_type =  ['Fixed', 'Percentage'];
                    return $all_variable_type[$row->variable_type];
                })
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
        try {
            $package_variables = array(
                'variable_options' => $request->variable_options,
                'variable_code' => $request->variable_code,
                'option_value' => $request->option_value,
                'increase_decrease' => $request->increase_decrease,
                'variable_type' => $request->variable_type,
                'price_value' => $request->price_value
            );

            PackageVariable::create($package_variables);


            $output = [
                'success' => 1,
                'msg' => __('superadmin::lang.package_variable_add'),
            ];

            return $output;
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
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
        $package_variable = PackageVariable::find($id);

        return view('superadmin::superadmin_settings.partials.package_variables_edit')->with(compact('package_variable'));
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
       
        try {
            $package_variables = array(
                'variable_options' => $request->variable_options,
                'variable_code' => $request->variable_code,
                'option_value' => $request->option_value,
                'increase_decrease' => $request->increase_decrease,
                'variable_type' => $request->variable_type,
                'price_value' => $request->price_value
            );

            PackageVariable::where('id', $id)->update($package_variables);

            $output = [
                'success' => 1,
                'msg' => __('superadmin::lang.package_variable_update'),
            ];

            return $output;
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

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
        try {
            PackageVariable::where('id', $id)->delete();

            $output = [
                'success' => 1,
                'msg' => __('superadmin::lang.package_variable_delete'),
            ];

            return $output;
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];

            return $output;
        }
    }
}
