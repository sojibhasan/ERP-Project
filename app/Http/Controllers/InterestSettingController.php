<?php

namespace App\Http\Controllers;

use App\InterestSetting;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class InterestSettingController extends Controller
{
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $interestSettings = InterestSetting::leftJoin('accounts', 'interest_settings.account_id', 'accounts.id')
            ->leftJoin('contact_groups', 'interest_settings.contact_group_id', 'contact_groups.id')
            ->leftJoin('users', 'interest_settings.created_by', 'users.id')
            ->select(
                'interest_settings.id',
                'interest_settings.date',
                'contact_groups.name as contact_group',
                'accounts.name as account',
                'users.username'
            );

        $datatable = DataTables::of($interestSettings);

        $rawColumns = ['interest_settings.date', 'contact_group', 'account', 'users.username'];

        return $datatable->rawColumns($rawColumns)
            ->make(true);
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
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'contact_group_id' => 'required|integer',
            'account_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'tab' => 'interest-settings',
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            InterestSetting::create([
                'date' =>  $this->commonUtil->uf_date($request->date),
                'business_id' => $business_id,
                'contact_group_id' => $request->contact_group_id,
                'account_id' => $request->account_id,
                'created_by' => $request->user()->id
            ]);

            $output = [
                'success' => 1,
                'tab' => 'interest-settings',
                'msg' => __('messages.saved_successfully')
            ];
            return redirect()->back()->with('status', $output);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'tab' => 'interest-settings',
                'msg' => __("messages.something_went_wrong")
            ];
            return redirect()->back()->with('status', $output);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
