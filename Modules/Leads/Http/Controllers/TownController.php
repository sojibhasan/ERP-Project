<?php

namespace Modules\Leads\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Leads\Entities\District;
use Modules\Leads\Entities\Town;
use Yajra\DataTables\Facades\DataTables;

class TownController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $towns = Town::leftjoin('districts', 'towns.district_id', 'districts.id')
                ->where('towns.business_id', $business_id)
                ->select([
                    'towns.*',
                    'districts.name as district'
                ]);

            if (!empty(request()->district)) {
                $towns->where('district_id', request()->district);
            }
            if (!empty(request()->user)) {
                $towns->where('towns.created_by', request()->user);
            }
            return DataTables::of($towns)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Leads\Http\Controllers\TownController@edit\',[$id])}}" data-container=".town_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Leads\Http\Controllers\TownController@destroy\',[$id])}}" class="btn btn-xs btn-danger town_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
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
        $districts = District::where('business_id', $business_id)->pluck('name', 'id');

        return view('leads::settings.town.create')->with(compact('districts'));
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
            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');
            $input['name'] = $request->name;
            $input['district_id'] = $request->district_id;
            $input['created_by'] = Auth::user()->id;
            $input['business_id'] = $business_id;

            Town::create($input);

            $output = [
                'success' => true,
                'tab' => 'town',
                'msg' => __('leads::lang.town_create_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'town',
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
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $districts = District::where('business_id', $business_id)->pluck('name', 'id');
        $town = Town::findOrFail($id);
        return view('leads::settings.town.edit')->with(compact('town', 'districts'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input['date'] = !empty($request->date) ? Carbon::parse($request->date)->format('Y-m-d') : date('Y-m-d');
            $input['name'] = $request->name;
            $input['district_id'] = $request->district_id;

            Town::where('id', $id)->update($input);

            $output = [
                'success' => true,
                'tab' => 'town',
                'msg' => __('leads::lang.town_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'town',
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
            Town::where('id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('leads::lang.town_delete_success')
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
