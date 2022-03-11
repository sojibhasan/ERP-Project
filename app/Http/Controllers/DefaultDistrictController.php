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

class DefaultDistrictController extends Controller
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
            $default_district = Districts::leftJoin('users AS u', 'districts.created_by', '=', 'u.id')

                ->select([
                    'districts.name', 'districts.id',
                ]);

            $default_district->groupBy('districts.id');
            return DataTables::of($default_district)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'DefaultDistrictController@edit\',[$id])}}" data-container=".edit_modal" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'DefaultDistrictController@destroy\',[$id])}}" class="btn btn-xs btn-danger delete_district"><i class="fa fa-trash "></i> @lang("account.delete")</button>
                              '
                )
                ->editColumn('name', function ($row) {
                    if ($row->is_closed == 1) {
                        return $row->name . ' <small class="label pull-right bg-red no-print">' . __("account.closed") . '</small><span class="print_section">(' . __("account.closed") . ')</span>';
                    } else {
                        return $row->name;
                    }
                })

                ->removeColumn('business_id')
                ->removeColumn('created_by')
                ->removeColumn('id')
                ->rawColumns(['name', 'action'])
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
        $account_types = DefaultAccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types'])
            ->get();


        $asset_type_ids = json_encode(DefaultAccountType::getAccountTypeIdOfType('Assets', $business_id));

        return view('default_district.create')
            ->with(compact('account_types', 'asset_type_ids'));
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
                $input = $request->only(['name']);

                $business_id = $request->business_id;
                if (empty($business_id)) {
                    $business_id = $request->session()->get('user.business_id');
                }
                $user_id = $request->session()->get('user.id');
                $input['business_id'] = $business_id;
                $input['date'] = date('Y-m-d');
                $input['created_by'] = $user_id;
                Districts::create($input);

                $output = [
                    'success' => true,
                    'msg' => __("visitors.districted_created_success")
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
            $districts = Districts::find($id);

            $account_types = DefaultAccountType::where('business_id', $business_id)
                ->whereNull('parent_account_type_id')
                ->with(['sub_types'])
                ->get();


            return view('default_district.edit')
                ->with(compact('districts', 'account_types'));
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
                $input = $request->only(['name']);

                $business_id = request()->session()->get('user.business_id');
                $district = Districts::where('business_id', $business_id)
                    ->findOrFail($id);
                $district->name = $input['name'];


                $district->save();

                $output = [
                    'success' => true,
                    'msg' => __("visitors.districted_edit_success")
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

                Districts::where('id', $id)->delete();

                //delete account for other businesses

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
    public function getDistrictDropDown(Request $request)
    {
        $towns = Districts::select('name', 'id')->get();
        $options = '';
        $options .= '<option value="">Please Select</option>';
        foreach ($towns as $value) {
            $options .= '<option value="' . $value->id . '">' . $value->name . '</option>';
        }
        return $options;
    }

    public function getTownsByDistrict(Request $request)
    {
        $towns = Towns::where('district_id', $request->district)->select('name', 'id')->get();
        $options = '';
        $options .= '<option value="">Please Select</option>';
        foreach ($towns as $value) {
            $options .= '<option value="' . $value->id . '">' . $value->name . '</option>';
        }
        return $options;
    }
}
