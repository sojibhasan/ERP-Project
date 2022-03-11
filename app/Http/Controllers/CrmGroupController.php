<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CrmGroup;
use Yajra\DataTables\Facades\DataTables;

class CrmGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        if (!auth()->user()->can('crm.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

         $crm_group =CrmGroup::where('business_id', $business_id)->select('name', 'id');

            return Datatables::of($crm_group)
                ->addColumn(
                    'action',
                    '@can("customer.update")
                            <button data-href="{{action(\'CrmGroupController@edit\', [$id])}}" class="btn btn-xs btn-primary crm_group_edit_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        @endcan

                        @can("customer.delete")
                            <button data-href="{{action(\'CrmGroupController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_crm_group_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                        @endcan'
                )

                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('crm_groups.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('crm.create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('crm_groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('crm.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['created_by'] = $request->session()->get('user.id');

            $customer_group = CrmGroup::create($input);
            $output = ['success' => true,
                            'data' => $customer_group,
                            'msg' => __("lang_v1.success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
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
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('crm.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $crm_group = CrmGroup::where('business_id', $business_id)->find($id);

            return view('crm_groups.edit')
                ->with(compact('crm_group'));
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
        if (!auth()->user()->can('crm.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['name']);
                $business_id = $request->session()->get('user.business_id');


                $customer_group = CrmGroup::where('business_id', $business_id)->findOrFail($id);
                $customer_group->name = $input['name'];
                $customer_group->save();

                $output = ['success' => true,
                            'msg' => __("lang_v1.success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
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
        if (!auth()->user()->can('crm.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $cg = CrmGroup::where('business_id', $business_id)->findOrFail($id);
                $cg->delete();

                $output = ['success' => true,
                            'msg' => __("lang_v1.success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}
