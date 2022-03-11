<?php

namespace Modules\Member\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\GramasevaVasama;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class GramasevaVasamaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $gramaseva_vasama = GramasevaVasama::where('business_id', $business_id)
                ->select([
                    'gramaseva_vasamas.*'
                ]);

            return DataTables::of($gramaseva_vasama)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\GramasevaVasamaController@edit\',[$id])}}" data-container=".gramaseva_vasama_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                  <!--  <button data-href="{{action(\'\Modules\Member\Http\Controllers\GramasevaVasamaController@destroy\',[$id])}}" class="btn btn-xs btn-danger note_group_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button> -->
                   
                    '
                )
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
        return view('member::settings.gramaseva_vasama.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        try {
            $data = $request->except('_token');
            $data['business_id'] = $business_id;
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d');
            $gramaseva_vasama = GramasevaVasama::create($data);

            //Create a new permission related to the created gramaseva_vasama
            Permission::create(['name' => 'gramaseva_vasama.' . $gramaseva_vasama->id]);

            $output = [
                'success' => true,
                'msg' => __('member::lang.gramaseva_vasama_create_success')
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
        return view('member::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $gramaseva_vasama = GramasevaVasama::findOrFail($id);
        return view('member::settings.gramaseva_vasama.edit')->with(compact('gramaseva_vasama'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d');

            GramasevaVasama::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'msg' => __('member::lang.gramaseva_vasama_update_success')
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
    public function destroy($id)
    {
        try {
            GramasevaVasama::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('member::lang.gramaseva_vasama_delete_success')
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
