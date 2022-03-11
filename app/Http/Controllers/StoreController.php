<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;
use App\BusinessLocation;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $stores = Store::where('stores.business_id', $business_id)
                ->join('business_locations', 'stores.location_id', 'business_locations.id')
                ->select('stores.*', 'business_locations.location_id as location_id', 'business_locations.name as location_name');

            //Add condition for location,used in sales representative expense report & list of expense
            if (request()->has('location_id')) {
                $location_id = request()->get('location_id');
                if (!empty($location_id)) {
                    $stores->where('stores.location_id', $location_id);
                }
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $stores->whereIn('location_id', $permitted_locations);
            }


            return Datatables::of($stores)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button data-href="{{action(\'StoreController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_store_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        <a data-href="{{action(\'StoreController@destroy\', [$id])}}" class="delete_store"><i class="glyphicon glyphicon-trash" style="color:brown;"></i> @lang("messages.delete")</a>
                    </div>'
                )
                ->editColumn(
                    'status',
                    function ($row) {
                        $html = '';
                        if ($row->status == 1) {
                            $html = 'Active';
                        } else {
                            $html = 'Not Active';
                        }
                        return $html;
                    }

                )
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $locations = DB::table('business_locations')->where('business_id', $business_id)->get();
        return view('stores.index')
            ->with(compact('locations', 'business_locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('store.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('stores.create')
            ->with(compact('business_locations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('store.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['location_id', 'name', 'contact_number', 'address']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['status'] = !empty($request->input('status')) ? $request->input('status') : 0;

            $store = Store::create($input);
            $output = [
                'success' => true,
                'data' => $store,
                'msg' => __("store.added_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with('status', $output);
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
        if (!auth()->user()->can('store.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $store = Store::where('business_id', $business_id)->find($id);

            $business_locations = BusinessLocation::forDropdown($business_id, true);

            return view('stores.edit')
                ->with(compact('store', 'business_locations'));
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
        if (!auth()->user()->can('store.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['location_id', 'name', 'contact_number', 'address']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $input['status'] = !empty($request->input('status')) ? $request->input('status') : 0;

            $store = Store::where('id', $id)->update($input);
            $output = [
                'success' => true,
                'data' => $store,
                'msg' => __("store.update_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('store.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $store = Store::where('business_id', $business_id)->where('id', $id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("store.delete_success")
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

    public function locationHasStoreCount($location_id){
        $business_id = request()->user()->business_id;

        $count = Store::where('business_id', $business_id)->where('location_id', $location_id)->count();

        return ['count' => $count];
    }
}
