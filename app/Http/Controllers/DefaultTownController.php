<?php

namespace App\Http\Controllers;

use App\Business;
use Illuminate\Http\Request;
use App\Districts;
use App\Towns;
use App\DefaultAccountType;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DefaultTownController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $default_towns = Towns::leftJoin('users AS u', 'towns.created_by', '=', 'u.id')
                ->leftJoin('districts', 'towns.district_id', '=', 'districts.id')
                // ->where('districts.business_id', $business_id)
                ->select([
                    'towns.name', 'towns.id', 'districts.name AS district'
                ]);

            $default_towns->groupBy('towns.id');

            return DataTables::of($default_towns)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'DefaultTownController@edit\',[$id])}}" data-container=".default_towns_model" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'DefaultTownController@destroy\',[$id])}}" class="btn btn-xs btn-danger delete_town"><i class="fa fa-trash "></i> @lang("account.delete")</button>
                              '
                )
                ->editColumn('name', function ($row) {
                    if ($row->is_closed == 1) {
                        return $row->name . ' <small class="label pull-right bg-red no-print">' . __("account.closed") . '</small><span class="print_section">(' . __("account.closed") . ')</span>';
                    } else {
                        return $row->name;
                    }
                })
                ->editColumn('district', function ($row) {
                    if ($row->is_closed == 1) {
                        return $row->district . ' <small class="label pull-right bg-red no-print">' . __("account.closed") . '</small><span class="print_section">(' . __("account.closed") . ')</span>';
                    } else {
                        return $row->district;
                    }
                })

                ->removeColumn('business_id')
                ->removeColumn('created_by')
                ->removeColumn('id')
                ->rawColumns(['name', 'action', 'district'])
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
        $business_id = session()->get('user.business_id');
        $districts = Districts::get();



        $asset_type_ids = json_encode(DefaultAccountType::getAccountTypeIdOfType('Assets', $business_id));

        return view('default_town.create')
            ->with(compact('districts', 'asset_type_ids'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'district_id']);
                $business_id = $request->business_id;
                if(empty($business_id)){
                    $business_id = $request->session()->get('user.business_id');
                }
                $user_id = $request->session()->get('user.id');
                $input['business_id'] = $business_id;
                $input['date'] = date('Y-m-d');
                $input['created_by'] = $user_id;
                Towns::create($input);



                $output = [
                    'success' => true,
                    'msg' => __("visitors.town_created_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

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
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $districts = Districts::get();
            $town = Towns::find($id);

            return view('default_town.edit')
                ->with(compact('districts', 'town'));
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
        if (request()->ajax()) {
            try {
                $user_id = $request->session()->get('user.id');
                $input = $request->only(['name', 'district_id']);

                $business_id = request()->session()->get('user.business_id');
                $towns = Towns::findOrFail($id);
                $towns->name = $input['name'];
                $towns->district_id = $input['district_id'];
                $towns->save();

                $output = [
                    'success' => true,
                    'msg' => __("visitors.town_edit_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
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
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                Towns::where('id', $id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("lang_v1.deleted_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
