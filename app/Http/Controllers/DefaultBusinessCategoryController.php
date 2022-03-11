<?php

namespace App\Http\Controllers;

use App\DefaultBusinessCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DefaultBusinessCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = DefaultBusinessCategory::select('*');
            $customer_reference = Datatables::of($query)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
        
                <li><a class=" btn-modal" data-href="{{action(\'DefaultBusinessCategoryController@edit\', $id)}}"
                data-container=".edit_modal">
                <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a> </li>
                <li><a href="{{action(\'DefaultBusinessCategoryController@destroy\', [$id])}}" class="delete_busiess_cat_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                </ul></div>'
                )
                ->removeColumn('id');


            return $customer_reference->rawColumns(['action'])
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
        return view('superadmin::superadmin_settings.partials.add_default_business_category_modal');
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
            $data = array(
                'category_name' => $request->category_name
            );

            DefaultBusinessCategory::create($data);

            $output = [
                'success' => 1,
                'msg' => __("superadmin::lang.business_category_add_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
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
        $business_category = DefaultBusinessCategory::findOrFail($id);
        return view('superadmin::superadmin_settings.partials.add__default_business_category_modal')->with(compact('business_category'));
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
            $data = array(
                'category_name' => $request->category_name
            );

            DefaultBusinessCategory::where('id', $id)->update($data);

            $output = [
                'success' => 1,
                'msg' => __("superadmin::lang.business_category_update_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
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
            DefaultBusinessCategory::where('id', $id)->delete();

            $output = [
                'success' => 1,
                'msg' => __("superadmin::lang.business_category_delete_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
    }
}
