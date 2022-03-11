<?php

namespace Modules\Member\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\ServiceArea;
use Yajra\DataTables\Facades\DataTables;

class ServiceAreasController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $service_areas = ServiceArea::where('business_id', $business_id)
                ->select([
                    'service_areas.*'
                ]);

            return DataTables::of($service_areas)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\ServiceAreasController@edit\',[$id])}}" data-container=".service_areas_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                  <!--  <button data-href="{{action(\'\Modules\Member\Http\Controllers\ServiceAreasController@destroy\',[$id])}}" class="btn btn-xs btn-danger note_group_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button> -->
                   
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
        return view('member::settings.service_areas.create');
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
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d') ;
            ServiceArea::create($data);

            $output = [
                'success' => true,
                'tab' => 'service_areas',
                'msg' => __('member::lang.service_areas_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'service_areas',
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
        $service_areas = ServiceArea::findOrFail($id);

        return view('member::settings.service_areas.edit')->with(compact('service_areas'));
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
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d') ;

            ServiceArea::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'service_areas',
                'msg' => __('member::lang.service_areas_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'service_areas',
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
            ServiceArea::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('member::lang.service_areas_delete_success')
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
