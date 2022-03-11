<?php

namespace Modules\Member\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\GramasevaVasama;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class BalamandalayaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $balamandalaya = Balamandalaya::leftjoin('gramaseva_vasamas', 'balamandalayas.gramaseva_vasama_id', 'gramaseva_vasamas.id')
                ->where('balamandalayas.business_id', $business_id)
                ->select([
                    'balamandalayas.*',
                    'gramaseva_vasamas.gramaseva_vasama'
                ]);

            return DataTables::of($balamandalaya)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\BalamandalayaController@edit\',[$id])}}" data-container=".balamandalaya_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                  <!--  <button data-href="{{action(\'\Modules\Member\Http\Controllers\BalamandalayaController@destroy\',[$id])}}" class="btn btn-xs btn-danger note_group_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button> -->
                   
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
        $business_id = request()->session()->get('business.id');
        $gramaseva_vasamas = GramasevaVasama::where('business_id', $business_id)->pluck('gramaseva_vasama', 'id');

        return view('member::settings.balamandalaya.create')->with(compact('gramaseva_vasamas'));
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
            $balamandalaya = Balamandalaya::create($data);

            //Create a new permission related to the created balamandalaya
            Permission::create(['name' => 'balamandalaya.' . $balamandalaya->id]);

            $output = [
                'success' => true,
                'tab' => 'balamandalaya',
                'msg' => __('member::lang.balamandalaya_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'balamandalaya',
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
        $business_id = request()->session()->get('business.id');

        $balamandalaya = Balamandalaya::findOrFail($id);
        $gramaseva_vasamas = GramasevaVasama::where('business_id', $business_id)->pluck('gramaseva_vasama', 'id');

        return view('member::settings.balamandalaya.edit')->with(compact('balamandalaya', 'gramaseva_vasamas'));
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

            Balamandalaya::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'balamandalaya',
                'msg' => __('member::lang.balamandalaya_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'balamandalaya',
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
            Balamandalaya::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('member::lang.balamandalaya_delete_success')
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
